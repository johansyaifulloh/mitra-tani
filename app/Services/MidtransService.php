<?php

namespace App\Services;

use App\Repositories\MidtransSettingRepository;
use App\Repositories\PaymentChannelRepository;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    // Peta kode channel lokal -> kode enabled_payments Midtrans
    private const CHANNEL_MAP = [
        'qris' => 'qris',
        'bca_va' => 'bca_va',
        'bni_va' => 'bni_va',
        'bri_va' => 'bri_va',
        'mandiri_va' => 'echannel',
        'permata_va' => 'permata_va',
        'gopay' => 'gopay',
        'shopeepay' => 'shopeepay',
    ];

    public function __construct(
        private MidtransSettingRepository $midtransSettingRepository,
        private PaymentChannelRepository $paymentChannelRepository,
    ) {}

    public function createSnapToken(object $order): string
    {
        // STEP 1: Ambil kredensial aktif
        $settings = $this->midtransSettingRepository->getActive();

        if (! $settings || empty($settings->server_key)) {
            throw new Exception('Kredensial Midtrans belum dikonfigurasi.');
        }

        // STEP 2: Tentukan endpoint Snap sesuai mode
        $baseUrl = $settings->mode === 'production'
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        // STEP 3: Susun payload transaksi
        $payload = [
            'transaction_details' => [
                'order_id' => $order->code,
                'gross_amount' => (int) $order->total,
            ],
            'customer_details' => [
                'first_name' => $order->buyer_name,
                'phone' => $order->buyer_phone,
            ],
            'expiry' => [
                'unit' => 'minute',
                'duration' => (int) ($settings->expiry_duration ?? 1440),
            ],
        ];

        // STEP 3b: Batasi metode bayar sesuai channel yang diaktifkan admin.
        // Jika tidak ada channel aktif, biarkan Midtrans menampilkan semua.
        $enabledPayments = $this->resolveEnabledPayments();

        if (! empty($enabledPayments)) {
            $payload['enabled_payments'] = $enabledPayments;
        }

        // STEP 4: Panggil Snap API dengan Basic Auth (server_key:)
        try {
            $response = Http::withBasicAuth($settings->server_key, '')
                ->acceptJson()
                ->asJson()
                ->timeout(20)
                ->post($baseUrl, $payload);
        } catch (Exception $e) {
            Log::error('MidtransService::createSnapToken request failed', ['error' => $e->getMessage()]);

            throw new Exception('Gagal menghubungi Midtrans. Coba lagi.');
        }

        // STEP 5: Validasi respons
        if (! $response->successful() || ! $response->json('token')) {
            Log::error('MidtransService::createSnapToken invalid response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            $message = $response->json('error_messages.0')
                ?? 'Gagal membuat token pembayaran Midtrans.';

            throw new Exception($message);
        }

        return $response->json('token');
    }

    public function getTransactionStatus(string $orderId): ?array
    {
        // STEP 1: Ambil kredensial aktif
        $settings = $this->midtransSettingRepository->getActive();

        if (! $settings || empty($settings->server_key)) {
            return null;
        }

        // STEP 2: Tentukan endpoint Core API sesuai mode
        $baseUrl = $settings->mode === 'production'
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';

        // STEP 3: Tanya status transaksi ke Midtrans pakai server key
        try {
            $response = Http::withBasicAuth($settings->server_key, '')
                ->acceptJson()
                ->get("{$baseUrl}/v2/{$orderId}/status");
        } catch (Exception $e) {
            Log::warning('MidtransService::getTransactionStatus failed', ['error' => $e->getMessage()]);

            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        return $response->json();
    }

    private function resolveEnabledPayments(): array
    {
        $codes = $this->paymentChannelRepository->listActive()
            ->pluck('code')
            ->map(fn ($code) => self::CHANNEL_MAP[$code] ?? null)
            ->filter()
            ->unique()
            ->values()
            ->all();

        return $codes;
    }

    public function verifySignature(array $payload, string $signature): bool
    {
        $settings = $this->midtransSettingRepository->getActive();

        if (! $settings) {
            return false;
        }

        // Format resmi Midtrans:
        // sha512(order_id + status_code + gross_amount + server_key)
        $expected = hash('sha512',
            ($payload['order_id'] ?? '')
            .($payload['status_code'] ?? '')
            .($payload['gross_amount'] ?? '')
            .$settings->server_key
        );

        return hash_equals($expected, $signature);
    }
}
