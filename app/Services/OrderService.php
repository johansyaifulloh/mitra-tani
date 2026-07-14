<?php

namespace App\Services;

use App\Repositories\AddressRepository;
use App\Repositories\CartRepository;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use App\Support\FormatHelper;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository,
        private OrderItemRepository $orderItemRepository,
        private CartRepository $cartRepository,
        private AddressRepository $addressRepository,
        private ProductRepository $productRepository,
        private UserRepository $userRepository,
        private MidtransService $midtransService,
    ) {}

    public function createFromCheckout(int $userId, array $data): object
    {
        // STEP 1: Get DB reference
        $cartItems = $this->cartRepository->selectedForUser($userId);
        $address = $this->addressRepository->findById($data['address_id'], $userId);
        $user = $this->userRepository->findById($userId);

        // STEP 2: Validate DB results
        if ($cartItems->isEmpty()) {
            throw new Exception('Keranjang kosong atau tidak ada item terpilih.');
        }

        if (! $address) {
            throw new Exception('Alamat pengambilan tidak ditemukan.');
        }

        if (! $user) {
            throw new Exception('User tidak ditemukan.');
        }

        // STEP 3: Business logic
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $product = $this->productRepository->findActiveById($item->product_id);

            if (! $product) {
                throw new Exception("Produk {$item->product_name} tidak tersedia.");
            }

            if ($item->quantity > $product->stock) {
                throw new Exception("Stok {$item->product_name} tidak mencukupi.");
            }

            $subtotal += $item->price * $item->quantity;
        }

        $adminFee = CartService::ADMIN_FEE;
        $total = $subtotal + $adminFee;
        $code = $this->generateOrderCode();

        // STEP 4: DB transaction
        DB::beginTransaction();

        try {
            $orderId = $this->orderRepository->insert([
                'code' => $code,
                'user_id' => $userId,
                'address_id' => $address->id,
                'buyer_name' => $address->recipient_name,
                'buyer_phone' => $address->phone,
                'subtotal' => $subtotal,
                'admin_fee' => $adminFee,
                'total' => $total,
                'payment_status' => 'menunggu_pembayaran',
                'pickup_status' => null,
                'expired_at' => now()->addDay(),
            ]);

            foreach ($cartItems as $item) {
                $this->orderItemRepository->insert([
                    'order_id' => $orderId,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'unit_price' => $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->price * $item->quantity,
                ]);
            }

            $this->cartRepository->clearForUser($userId);

            $order = $this->orderRepository->findById($orderId);
            $snapToken = $this->midtransService->createSnapToken($order);
            $this->orderRepository->update($orderId, [
                'snap_token' => $snapToken,
                'midtrans_order_id' => $code,
            ]);

            DB::commit();

            return $this->orderRepository->findById($orderId);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('OrderService::createFromCheckout failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function findByCode(string $code): ?object
    {
        return $this->orderRepository->findByCode($code);
    }

    private function resolvePaymentMethod(array $status): ?string
    {
        $type = $status['payment_type'] ?? null;

        if (! $type) {
            return null;
        }

        // Virtual Account: bank spesifik ada di va_numbers[].bank
        if ($type === 'bank_transfer') {
            $bank = $status['va_numbers'][0]['bank'] ?? null;

            if (! $bank && ! empty($status['permata_va_number'])) {
                $bank = 'permata';
            }

            return $bank ? $bank.'_va' : 'bank_transfer';
        }

        // Mandiri Bill Payment
        if ($type === 'echannel') {
            return 'mandiri_va';
        }

        // Gerai retail: indomaret / alfamart
        if ($type === 'cstore') {
            return $status['store'] ?? 'cstore';
        }

        // qris, gopay, shopeepay, credit_card, dll
        return $type;
    }

    public function refreshPendingForUser(int $userId): void
    {
        // Cek ke Midtrans untuk tiap pesanan yang masih menunggu pembayaran,
        // agar status & metode ter-update walau webhook tidak aktif.
        $pending = $this->orderRepository->listForUser($userId, 'menunggu_pembayaran');

        foreach ($pending as $order) {
            try {
                $this->syncFromSnapResult($order->code, $userId);
            } catch (Exception $e) {
                Log::warning('OrderService::refreshPendingForUser failed', [
                    'code' => $order->code,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    public function listForUser(int $userId, ?string $status = null): array
    {
        // STEP 1: Tentukan filter status pembayaran (null = semua)
        $paymentStatus = in_array($status, ['menunggu_pembayaran', 'lunas', 'expired'], true)
            ? $status
            : null;

        // STEP 2: Ambil pesanan milik user
        $orders = $this->orderRepository->listForUser($userId, $paymentStatus);

        // STEP 3: Hitung jumlah per status untuk badge tab
        $counts = $this->orderRepository->countByPaymentStatusForUser($userId);

        // STEP 4: Format data untuk view
        $items = $orders->map(function ($order) {
            $statusInfo = FormatHelper::paymentStatusLabel($order->payment_status);
            $pickupInfo = FormatHelper::pickupStatusLabel($order->pickup_status ?? null);

            $itemCount = (int) ($order->item_count ?? 0);

            return [
                'code' => $order->code,
                'total_label' => FormatHelper::rupiah($order->total),
                'item_count' => $itemCount,
                'total_qty' => (int) ($order->total_qty ?? 0),
                'first_product_name' => $order->first_product_name ?? 'Produk',
                'first_product_emoji' => $order->first_product_emoji ?? '🌱',
                'first_product_qty' => (int) ($order->first_product_qty ?? 1),
                'more_count' => max(0, $itemCount - 1),
                'payment_status' => $order->payment_status,
                'status_label' => $statusInfo['label'],
                'status_tone' => $statusInfo['tone'],
                'pickup_status' => $order->pickup_status ?? null,
                'pickup_label' => $pickupInfo['label'],
                'pickup_tone' => $pickupInfo['tone'],
                'pickup_desc' => $pickupInfo['desc'],
                'proof_photo' => ! empty($order->proof_photo) ? asset('storage/'.$order->proof_photo) : null,
                'proof_note' => $order->proof_note ?? null,
                'proof_verifier' => $order->proof_verifier ?? null,
                'proof_verified_at' => ! empty($order->proof_verified_at) ? date('d M Y, H:i', strtotime($order->proof_verified_at)) : null,
                'payment_method' => FormatHelper::paymentMethodLabel($order->payment_method ?? null),
                'is_unpaid' => $order->payment_status === 'menunggu_pembayaran',
                'date' => date('d M Y H:i', strtotime($order->created_at)),
                'expired_at' => $order->expired_at ? date('d M Y, H:i', strtotime($order->expired_at)) : null,
                'expired_ms' => $order->expired_at ? \Carbon\Carbon::parse($order->expired_at)->getTimestamp() * 1000 : null,
                'is_expired_soon' => $order->payment_status === 'menunggu_pembayaran'
                    && $order->expired_at
                    && strtotime($order->expired_at) < strtotime('+3 hours'),
            ];
        })->all();

        return [
            'orders' => $items,
            'counts' => [
                'all' => array_sum($counts),
                'menunggu_pembayaran' => $counts['menunggu_pembayaran'] ?? 0,
                'lunas' => $counts['lunas'] ?? 0,
                'expired' => $counts['expired'] ?? 0,
            ],
            'active' => $status ?? 'all',
        ];
    }

    public function detailForPayment(string $code, int $userId): ?array
    {
        $order = $this->orderRepository->findByCode($code);

        if (! $order || $order->user_id !== $userId) {
            return null;
        }

        $items = $this->orderItemRepository->listForOrder($order->id)
            ->map(fn ($row) => [
                'name' => $row->product_name,
                'emoji' => $row->emoji ?? '🌱',
                'quantity' => (int) $row->quantity,
                'subtotal_label' => FormatHelper::rupiah($row->subtotal),
            ])
            ->all();

        return [
            'code' => $order->code,
            'total' => (float) $order->total,
            'total_label' => FormatHelper::rupiah($order->total),
            'subtotal_label' => FormatHelper::rupiah($order->subtotal),
            'admin_fee_label' => FormatHelper::rupiah($order->admin_fee),
            'payment_status' => $order->payment_status,
            'buyer_name' => $order->buyer_name,
            'buyer_phone' => $order->buyer_phone,
            'address_text' => $order->address_text ?? '—',
            'snap_token' => $order->snap_token,
            'expired_at' => $order->expired_at,
            'items' => $items,
        ];
    }

    public function syncFromSnapResult(string $code, int $userId, array $result = []): void
    {
        // STEP 1: Pastikan pesanan milik user
        $order = $this->orderRepository->findByCode($code);

        if (! $order || $order->user_id !== $userId) {
            return;
        }

        // STEP 2: Utamakan status resmi dari Midtrans (pakai server key).
        // Jika gagal (mis. offline), pakai hasil dari popup Snap sebagai cadangan.
        $status = $this->midtransService->getTransactionStatus($code) ?: $result;

        $paymentMethod = $this->resolvePaymentMethod($status);
        $transactionStatus = $status['transaction_status'] ?? null;
        $transactionId = $status['transaction_id'] ?? null;

        // STEP 3: Susun perubahan
        $update = [];

        if ($paymentMethod) {
            $update['payment_method'] = $paymentMethod;
        }

        if ($transactionId) {
            $update['midtrans_transaction_id'] = $transactionId;
        }

        if (in_array($transactionStatus, ['capture', 'settlement'], true)) {
            if ($order->payment_status !== 'lunas') {
                $update['payment_status'] = 'lunas';
                $update['pickup_status'] = 'menunggu_approval';
                $update['paid_at'] = now();
            }
        } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny'], true)) {
            $update['payment_status'] = 'expired';
        }

        if ($update) {
            $this->orderRepository->update($order->id, $update);
        }
    }

    public function handleNotification(array $payload): void
    {
        $orderCode = $payload['order_id'] ?? null;
        $status = $payload['transaction_status'] ?? null;

        if (! $orderCode) {
            return;
        }

        $order = $this->orderRepository->findByCode($orderCode);

        if (! $order) {
            return;
        }

        $update = [];

        $paymentMethod = $this->resolvePaymentMethod($payload);

        if (in_array($status, ['capture', 'settlement'], true)) {
            $update = [
                'payment_status' => 'lunas',
                'pickup_status' => 'menunggu_approval',
                'paid_at' => now(),
                'midtrans_transaction_id' => $payload['transaction_id'] ?? null,
                'payment_method' => $paymentMethod,
            ];
        } elseif ($status === 'pending') {
            // Metode sudah dipilih pelanggan di Snap, tapi belum dibayar.
            $update = [
                'payment_status' => 'menunggu_pembayaran',
                'payment_method' => $paymentMethod,
            ];
        } elseif (in_array($status, ['expire', 'cancel', 'deny'], true)) {
            $update = ['payment_status' => 'expired'];
        }

        if ($update) {
            $this->orderRepository->update($order->id, $update);
        }
    }

    private function generateOrderCode(): string
    {
        do {
            $code = 'TRX-'.str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while ($this->orderRepository->findByCode($code));

        return $code;
    }
}
