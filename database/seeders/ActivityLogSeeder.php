<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('role', 'admin')->first();

        $logs = [
            ['action' => 'verified', 'description' => 'Pengambilan #TRX-004 diverifikasi — barang sudah diambil', 'offset' => 5],
            ['action' => 'paid', 'description' => 'Pembayaran #TRX-002 lunas — menunggu pengambilan di toko', 'offset' => 22],
            ['action' => 'created', 'description' => 'Produk Pupuk Organik 5kg ditambahkan', 'offset' => 60],
            ['action' => 'paid', 'description' => '#TRX-001 lunas · barang siap diambil · perlu verifikasi admin', 'offset' => 120],
            ['action' => 'created', 'description' => 'Pesanan baru #TRX-003 dari Budi Santoso', 'offset' => 180],
        ];

        foreach ($logs as $log) {
            DB::table('activity_logs')->insert([
                'user_id' => $admin?->id,
                'action' => $log['action'],
                'subject_type' => null,
                'subject_id' => null,
                'description' => $log['description'],
                'created_at' => now()->subMinutes($log['offset']),
            ]);
        }
    }
}
