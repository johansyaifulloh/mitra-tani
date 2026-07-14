<?php

namespace App\Services;

use App\Repositories\MidtransSettingRepository;
use App\Repositories\PaymentChannelRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransSettingService
{
    public function __construct(
        private MidtransSettingRepository $midtransSettingRepository,
        private PaymentChannelRepository $paymentChannelRepository,
    ) {}

    public function getSettingsArray(): array
    {
        $settings = $this->midtransSettingRepository->first();
        $channels = $this->paymentChannelRepository->listAll();

        if (! $settings) {
            return [
                'channels' => [],
                'channel_groups' => $this->channelGroups(),
            ];
        }

        return [
            'merchant_id' => $settings->merchant_id ?? '',
            'server_key' => $settings->server_key,
            'client_key' => $settings->client_key,
            'mode' => $settings->mode,
            'notification_url' => $settings->notification_url,
            'finish_url' => $settings->finish_url,
            'unfinish_url' => $settings->unfinish_url,
            'error_url' => $settings->error_url,
            'expiry_duration' => $settings->expiry_duration,
            'is_active' => (bool) $settings->is_active,
            'channel_groups' => $this->channelGroups(),
            'channels' => $channels->map(fn ($row) => [
                'code' => $row->code,
                'name' => $row->name,
                'group' => $row->channel_group,
                'icon' => $row->icon ?? '',
                'color' => $row->color ?? '#059669',
                'enabled' => (bool) $row->is_enabled,
            ])->all(),
        ];
    }

    public function getActiveSettings(): ?object
    {
        return $this->midtransSettingRepository->getActive();
    }

    public function update(array $data): void
    {
        $settings = $this->midtransSettingRepository->first();

        if (! $settings) {
            throw new Exception('Pengaturan Midtrans tidak ditemukan.');
        }

        DB::beginTransaction();

        try {
            $this->midtransSettingRepository->update($settings->id, [
                'merchant_id' => $data['merchant_id'] ?? null,
                'server_key' => $data['server_key'],
                'client_key' => $data['client_key'],
                'mode' => $data['mode'],
                'notification_url' => $data['notification_url'],
                'finish_url' => $data['finish_url'],
                'unfinish_url' => $data['unfinish_url'],
                'error_url' => $data['error_url'],
                'expiry_duration' => $data['expiry_duration'] ?? 1440,
                'is_active' => ! empty($data['is_active']),
            ]);

            $this->paymentChannelRepository->setEnabledByCodes($data['channels'] ?? []);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('MidtransSettingService::update failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function testConnection(string $serverKey, string $mode): array
    {
        // STEP 1: Tentukan base URL sesuai mode
        $baseUrl = $mode === 'production'
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';

        // STEP 2: Panggil Core API status transaksi dummy dengan Basic Auth.
        // Server key valid -> 404 (transaksi tidak ada). Key salah -> 401.
        try {
            $response = Http::withBasicAuth($serverKey, '')
                ->acceptJson()
                ->timeout(15)
                ->get("{$baseUrl}/v2/mantri-tani-connection-test/status");
        } catch (Exception $e) {
            Log::error('MidtransSettingService::testConnection failed', ['error' => $e->getMessage()]);

            throw new Exception('Gagal menghubungi server Midtrans. Periksa koneksi internet.');
        }

        // STEP 3: Interpretasi status HTTP
        $status = $response->status();

        if ($status === 401 || $status === 403) {
            throw new Exception('Server Key tidak valid atau ditolak Midtrans (HTTP '.$status.').');
        }

        return [
            'mode' => $mode,
            'http_status' => $status,
            'message' => 'Koneksi berhasil. Server Key valid untuk mode '.ucfirst($mode).'.',
        ];
    }

    private function channelGroups(): array
    {
        return [
            'qris' => ['label' => 'QRIS', 'desc' => 'Scan QR & bayar instan'],
            'va' => ['label' => 'Virtual Account', 'desc' => 'Transfer bank otomatis'],
            'ewallet' => ['label' => 'E-Wallet', 'desc' => 'GoPay, ShopeePay, dll.'],
        ];
    }
}
