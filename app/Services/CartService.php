<?php

namespace App\Services;

use App\Repositories\CartRepository;
use App\Repositories\ProductRepository;
use App\Support\FormatHelper;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartService
{
    public const ADMIN_FEE = 2500;

    public function __construct(
        private CartRepository $cartRepository,
        private ProductRepository $productRepository,
    ) {}

    public function fetchForUser(int $userId): array
    {
        $rows = $this->cartRepository->listForUser($userId);

        return [
            'items' => $rows->map(fn ($row) => FormatHelper::cartItemForView($row))->all(),
            'meta' => $this->buildMeta($rows),
        ];
    }

    public function summaryForCheckout(int $userId): array
    {
        $rows = $this->cartRepository->selectedForUser($userId);

        if ($rows->isEmpty()) {
            throw new Exception('Keranjang kosong atau tidak ada item terpilih.');
        }

        $items = [];
        $subtotal = 0;

        foreach ($rows as $row) {
            $product = $this->productRepository->findActiveById($row->product_id);

            if (! $product) {
                throw new Exception("Produk {$row->product_name} tidak tersedia.");
            }

            if ($row->quantity > $product->stock) {
                throw new Exception("Stok {$row->product_name} tidak mencukupi.");
            }

            $lineSubtotal = $row->price * $row->quantity;
            $subtotal += $lineSubtotal;

            $items[] = [
                'name' => $row->product_name,
                'emoji' => $row->emoji ?? '🌱',
                'quantity' => (int) $row->quantity,
                'unit_price' => (float) $row->price,
                'unit_price_label' => FormatHelper::rupiah($row->price),
                'subtotal' => $lineSubtotal,
                'subtotal_label' => FormatHelper::rupiah($lineSubtotal),
            ];
        }

        $total = $subtotal + self::ADMIN_FEE;

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'subtotal_label' => FormatHelper::rupiah($subtotal),
            'admin_fee' => self::ADMIN_FEE,
            'admin_fee_label' => FormatHelper::rupiah(self::ADMIN_FEE),
            'total' => $total,
            'total_label' => FormatHelper::rupiah($total),
            'item_count' => collect($items)->sum('quantity'),
        ];
    }

    public function listForUser(int $userId): array
    {
        $rows = $this->cartRepository->listForUser($userId);

        return $rows->map(fn ($row) => FormatHelper::cartItemForView($row))->all();
    }

    public function selectedCount(int $userId): int
    {
        return $this->cartRepository->selectedQuantityForUser($userId);
    }

    public function addOrUpdate(int $userId, int $productId, int $quantity): void
    {
        //--------------------------------------------------
        // STEP 1
        // Ambil referensi produk dan item keranjang
        //--------------------------------------------------
        $product = $this->productRepository->findActiveById($productId);
        $existing = $this->cartRepository->findItem($userId, $productId);

        //--------------------------------------------------
        // STEP 2
        // Validasi hasil database
        //--------------------------------------------------
        if (! $product) {
            throw new Exception('Produk tidak ditemukan atau tidak aktif.');
        }

        if ($quantity < 1) {
            throw new Exception('Jumlah minimal 1.');
        }

        if ($existing) {
            $quantity = $existing->quantity + $quantity;
        }

        if ($quantity > $product->stock) {
            throw new Exception('Stok tidak mencukupi.');
        }

        //--------------------------------------------------
        // STEP 3
        // Simpan ke keranjang (transaction)
        //--------------------------------------------------
        DB::beginTransaction();

        try {
            if ($existing) {
                $this->cartRepository->update($existing->id, ['quantity' => $quantity]);
            } else {
                $this->cartRepository->insert([
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'is_selected' => true,
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('CartService::addOrUpdate failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function updateQuantity(int $userId, int $cartItemId, int $quantity): void
    {
        $item = $this->cartRepository->findById($cartItemId, $userId);

        if (! $item) {
            throw new Exception('Item keranjang tidak ditemukan.');
        }

        $product = $this->productRepository->findActiveById($item->product_id);

        if (! $product) {
            throw new Exception('Produk tidak ditemukan.');
        }

        if ($quantity < 1) {
            $this->cartRepository->delete($cartItemId);

            return;
        }

        if ($quantity > $product->stock) {
            throw new Exception('Stok tidak mencukupi.');
        }

        $this->cartRepository->update($cartItemId, ['quantity' => $quantity]);
    }

    public function toggleSelection(int $userId, int $cartItemId, bool $isSelected): void
    {
        $item = $this->cartRepository->findById($cartItemId, $userId);

        if (! $item) {
            throw new Exception('Item keranjang tidak ditemukan.');
        }

        $this->cartRepository->update($cartItemId, ['is_selected' => $isSelected]);
    }

    public function setSelectAll(int $userId, bool $isSelected): void
    {
        $this->cartRepository->setSelectAllForUser($userId, $isSelected);
    }

    public function remove(int $userId, int $cartItemId): void
    {
        $item = $this->cartRepository->findById($cartItemId, $userId);

        if (! $item) {
            throw new Exception('Item keranjang tidak ditemukan.');
        }

        $this->cartRepository->delete($cartItemId);
    }

    private function buildMeta(Collection $rows): array
    {
        $subtotal = 0;
        $count = 0;

        foreach ($rows as $row) {
            if ($row->is_selected) {
                $subtotal += $row->price * $row->quantity;
                $count += (int) $row->quantity;
            }
        }

        $total = $subtotal;

        return [
            'selected_count' => $count,
            'subtotal' => $subtotal,
            'subtotal_label' => FormatHelper::rupiah($subtotal),
            'total' => $total,
            'total_label' => FormatHelper::rupiah($total),
        ];
    }
}
