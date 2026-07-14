<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MidtransSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('midtrans_settings')->updateOrInsert(
            ['id' => 1],
            [
                'merchant_id' => config('midtrans.merchant_id', ''),
                'server_key' => config('midtrans.server_key', ''),
                'client_key' => config('midtrans.client_key', ''),
                'mode' => config('midtrans.mode', 'sandbox'),
                'notification_url' => url('/api/midtrans/notification'),
                'finish_url' => url('/toko/pembayaran?status=finish'),
                'unfinish_url' => url('/toko/pembayaran?status=unfinish'),
                'error_url' => url('/toko/pembayaran?status=error'),
                'expiry_duration' => 1440,
                'is_active' => true,
                'updated_at' => now(),
            ],
        );

        $channels = [
            ['code' => 'qris', 'name' => 'QRIS', 'channel_group' => 'qris', 'icon' => 'QR', 'color' => '#059669', 'is_enabled' => true],
            ['code' => 'bca_va', 'name' => 'BCA Virtual Account', 'channel_group' => 'va', 'icon' => 'BCA', 'color' => '#00529b', 'is_enabled' => true],
            ['code' => 'mandiri_va', 'name' => 'Mandiri Virtual Account', 'channel_group' => 'va', 'icon' => 'MDR', 'color' => '#f59e0b', 'is_enabled' => true],
            ['code' => 'bni_va', 'name' => 'BNI Virtual Account', 'channel_group' => 'va', 'icon' => 'BNI', 'color' => '#f97316', 'is_enabled' => true],
            ['code' => 'bri_va', 'name' => 'BRI Virtual Account', 'channel_group' => 'va', 'icon' => 'BRI', 'color' => '#2563eb', 'is_enabled' => false],
            ['code' => 'gopay', 'name' => 'GoPay', 'channel_group' => 'ewallet', 'icon' => 'GP', 'color' => '#00aed6', 'is_enabled' => true],
            ['code' => 'shopeepay', 'name' => 'ShopeePay', 'channel_group' => 'ewallet', 'icon' => 'SP', 'color' => '#ee4d2d', 'is_enabled' => false],
        ];

        foreach ($channels as $channel) {
            DB::table('payment_channels')->updateOrInsert(
                ['code' => $channel['code']],
                $channel,
            );
        }
    }
}
