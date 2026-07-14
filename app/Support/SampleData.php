<?php

namespace App\Support;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class SampleData
{
    public static function products(): array
    {
        return [
            ['name' => 'Pupuk Urea 50kg', 'cat' => 'Pupuk', 'price' => 'Rp 85.000', 'badge' => 'Baru', 'emoji' => '🌱', 'slug' => 'pupuk-urea-50kg', 'stock' => 120],
            ['name' => 'Benih Cabai F1', 'cat' => 'Benih', 'price' => 'Rp 25.000', 'badge' => null, 'emoji' => '🌾', 'slug' => 'benih-cabai-f1', 'stock' => 45],
            ['name' => 'Obat Hama Matador', 'cat' => 'Obat', 'price' => 'Rp 35.000', 'badge' => null, 'emoji' => '💊', 'slug' => 'obat-hama-matador', 'stock' => 30],
            ['name' => 'Pupuk NPK 15-15-15', 'cat' => 'Pupuk', 'price' => 'Rp 95.000', 'badge' => 'Promo', 'emoji' => '🌱', 'slug' => 'pupuk-npk', 'stock' => 80],
            ['name' => 'Sprayer 16 Liter', 'cat' => 'Alat Tani', 'price' => 'Rp 150.000', 'badge' => null, 'emoji' => '💧', 'slug' => 'sprayer-16l', 'stock' => 15],
            ['name' => 'Pupuk Organik 5kg', 'cat' => 'Pupuk', 'price' => 'Rp 45.000', 'badge' => 'Terlaris', 'emoji' => '🌿', 'slug' => 'pupuk-organik-5kg', 'stock' => 60],
            ['name' => 'Benih Tomat Hybrid', 'cat' => 'Benih', 'price' => 'Rp 18.000', 'badge' => null, 'emoji' => '🍅', 'slug' => 'benih-tomat', 'stock' => 55],
            ['name' => 'Pupuk Za 50kg', 'cat' => 'Pupuk', 'price' => 'Rp 78.000', 'badge' => null, 'emoji' => '🌱', 'slug' => 'pupuk-za', 'stock' => 90],
            ['name' => 'Fungisida Mancozeb', 'cat' => 'Obat', 'price' => 'Rp 42.000', 'badge' => null, 'emoji' => '💊', 'slug' => 'fungisida-mancozeb', 'stock' => 25],
            ['name' => 'Cangkul Stainless', 'cat' => 'Alat Tani', 'price' => 'Rp 65.000', 'badge' => null, 'emoji' => '🔧', 'slug' => 'cangkul-stainless', 'stock' => 20],
            ['name' => 'Benih Padi IR64', 'cat' => 'Benih', 'price' => 'Rp 32.000', 'badge' => 'Baru', 'emoji' => '🌾', 'slug' => 'benih-padi-ir64', 'stock' => 70],
            ['name' => 'Pupuk KCL 50kg', 'cat' => 'Pupuk', 'price' => 'Rp 88.000', 'badge' => null, 'emoji' => '🌱', 'slug' => 'pupuk-kcl', 'stock' => 40],
        ];
    }

    public static function categories(): array
    {
        return [
            ['name' => 'Pupuk', 'icon' => '🌱', 'slug' => 'pupuk', 'color' => '#059669'],
            ['name' => 'Benih', 'icon' => '🌾', 'slug' => 'benih', 'color' => '#d97706'],
            ['name' => 'Obat Tanaman', 'icon' => '💊', 'slug' => 'obat-tanaman', 'color' => '#dc2626'],
            ['name' => 'Alat Tani', 'icon' => '🔧', 'slug' => 'alat-tani', 'color' => '#2563eb'],
        ];
    }

    public static function productBadges(): array
    {
        return ['Baru', 'Promo', 'Terlaris'];
    }

    public static function transactions(): array
    {
        $raw = [
            ['id' => 'TRX-001', 'customer' => 'Andi Wijaya', 'total' => 197500, 'date' => '07/07/2026', 'payment' => 'QRIS', 'payment_status' => 'lunas', 'status' => 'menunggu_approval'],
            ['id' => 'TRX-002', 'customer' => 'Siti Rahayu', 'total' => 85000, 'date' => '07/07/2026', 'payment' => 'VA BCA', 'payment_status' => 'lunas', 'status' => 'disetujui'],
            ['id' => 'TRX-003', 'customer' => 'Budi Santoso', 'total' => 125000, 'date' => '06/07/2026', 'payment' => 'QRIS', 'payment_status' => 'menunggu_pembayaran', 'status' => 'menunggu_pembayaran'],
            ['id' => 'TRX-004', 'customer' => 'Dewi Lestari', 'total' => 45000, 'date' => '06/07/2026', 'payment' => 'VA Mandiri', 'payment_status' => 'lunas', 'status' => 'selesai'],
            ['id' => 'TRX-005', 'customer' => 'Rudi Hartono', 'total' => 95000, 'date' => '06/07/2026', 'payment' => 'QRIS', 'payment_status' => 'lunas', 'status' => 'menunggu_approval'],
            ['id' => 'TRX-006', 'customer' => 'Maya Sari', 'total' => 150000, 'date' => '05/07/2026', 'payment' => 'VA BNI', 'payment_status' => 'lunas', 'status' => 'selesai'],
            ['id' => 'TRX-007', 'customer' => 'Joko Susilo', 'total' => 35000, 'date' => '05/07/2026', 'payment' => 'QRIS', 'payment_status' => 'expired', 'status' => 'expired'],
            ['id' => 'TRX-008', 'customer' => 'Ani Wulandari', 'total' => 78000, 'date' => '05/07/2026', 'payment' => 'VA BCA', 'payment_status' => 'lunas', 'status' => 'ditolak'],
            ['id' => 'TRX-009', 'customer' => 'Hendra Kusuma', 'total' => 220000, 'date' => '04/07/2026', 'payment' => 'QRIS', 'payment_status' => 'lunas', 'status' => 'menunggu_approval'],
            ['id' => 'TRX-010', 'customer' => 'Fitri Anggraini', 'total' => 65000, 'date' => '04/07/2026', 'payment' => 'VA Mandiri', 'payment_status' => 'lunas', 'status' => 'disetujui'],
            ['id' => 'TRX-011', 'customer' => 'Agus Prasetyo', 'total' => 180000, 'date' => '04/07/2026', 'payment' => 'QRIS', 'payment_status' => 'menunggu_pembayaran', 'status' => 'menunggu_pembayaran'],
            ['id' => 'TRX-012', 'customer' => 'Rina Melati', 'total' => 42000, 'date' => '03/07/2026', 'payment' => 'VA BCA', 'payment_status' => 'lunas', 'status' => 'selesai'],
            ['id' => 'TRX-013', 'customer' => 'Bambang Wijaya', 'total' => 88000, 'date' => '03/07/2026', 'payment' => 'QRIS', 'payment_status' => 'lunas', 'status' => 'menunggu_approval'],
            ['id' => 'TRX-014', 'customer' => 'Sri Mulyani', 'total' => 32000, 'date' => '02/07/2026', 'payment' => 'VA BNI', 'payment_status' => 'lunas', 'status' => 'selesai'],
            ['id' => 'TRX-015', 'customer' => 'Eko Prasetya', 'total' => 175000, 'date' => '02/07/2026', 'payment' => 'QRIS', 'payment_status' => 'lunas', 'status' => 'disetujui'],
        ];

        return array_map(fn ($t) => array_merge(
            $t,
            self::buyerInfo($t['customer']),
            ['pickup_proof' => self::pickupProof($t['id'], $t['status'])],
        ), $raw);
    }

    public static function pickupProof(string $id, string $status): ?array
    {
        if (! in_array($status, ['disetujui', 'selesai'], true)) {
            return null;
        }

        $proofs = [
            'TRX-002' => ['verified_at' => '07/07/2026 10:32', 'verified_by' => 'Admin Mantri', 'note' => 'Barang diambil langsung di toko'],
            'TRX-004' => ['verified_at' => '06/07/2026 14:15', 'verified_by' => 'Admin Mantri', 'note' => 'Pelanggan bawa KTP, barang lengkap'],
            'TRX-006' => ['verified_at' => '05/07/2026 09:48', 'verified_by' => 'Admin Mantri', 'note' => 'Diambil oleh keluarga pembeli'],
            'TRX-010' => ['verified_at' => '04/07/2026 16:20', 'verified_by' => 'Admin Mantri', 'note' => 'Foto barang & tanda tangan pelanggan'],
            'TRX-012' => ['verified_at' => '03/07/2026 11:05', 'verified_by' => 'Admin Mantri', 'note' => 'Pengambilan di counter depan'],
            'TRX-015' => ['verified_at' => '02/07/2026 13:40', 'verified_by' => 'Admin Mantri', 'note' => 'Barang sudah dicek kelengkapan'],
        ];

        return $proofs[$id] ?? [
            'verified_at' => '—',
            'verified_by' => 'Admin',
            'note' => 'Barang sudah diambil pelanggan',
        ];
    }

    public static function buyerInfo(string $customer): array
    {
        $map = [
            'Andi Wijaya' => ['phone' => '0812-3456-7890', 'address' => 'Jl. Melati No. 12, RT 03/RW 05, Selorejo, Malang 65100'],
            'Siti Rahayu' => ['phone' => '0813-9876-5432', 'address' => 'Jl. Kenanga No. 8, Ds. Selorejo, Malang 65100'],
            'Budi Santoso' => ['phone' => '0857-1122-3344', 'address' => 'Dusun Krajan Blok C2, Selorejo, Malang 65100'],
            'Dewi Lestari' => ['phone' => '0822-5566-7788', 'address' => 'Jl. Anggrek No. 5, Selorejo, Malang 65100'],
            'Rudi Hartono' => ['phone' => '0811-2233-4455', 'address' => 'Jl. Mawar No. 21, RT 02/RW 04, Selorejo, Malang'],
            'Maya Sari' => ['phone' => '0856-7788-9900', 'address' => 'Perumahan Tani Sejahtera Blok B7, Selorejo'],
            'Hendra Kusuma' => ['phone' => '0812-6677-8899', 'address' => 'Jl. Flamboyan No. 3, Selorejo, Malang 65100'],
            'Fitri Anggraini' => ['phone' => '0838-1234-5678', 'address' => 'Dusun Sumber Rejo, Selorejo, Malang'],
            'Bambang Wijaya' => ['phone' => '0819-5544-3322', 'address' => 'Jl. Dahlia No. 15, Selorejo, Malang 65100'],
        ];

        return $map[$customer] ?? ['phone' => '0813-0000-0000', 'address' => 'Selorejo, Malang, Jawa Timur'];
    }

    public static function savedAddresses(): array
    {
        return [
            ['id' => 1, 'label' => 'Rumah', 'name' => 'Andi Wijaya', 'phone' => '0812-3456-7890', 'street' => 'Jl. Melati No. 12, RT 03/RW 05', 'district' => 'Selorejo, Malang 65100', 'default' => true],
            ['id' => 2, 'label' => 'Kebun', 'name' => 'Andi Wijaya', 'phone' => '0812-3456-7890', 'street' => 'Dusun Krajan, Blok C2', 'district' => 'Selorejo, Malang 65100', 'default' => false],
        ];
    }

    public static function findTransactionByCode(?string $code): ?array
    {
        if (! $code) {
            return null;
        }

        $normalized = strtoupper(trim(str_replace('#', '', $code)));
        if (! str_starts_with($normalized, 'TRX-')) {
            $normalized = 'TRX-'.$normalized;
        }

        return self::findTransaction($normalized);
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'lunas' => 'Lunas',
            'menunggu_approval' => 'Menunggu Pengambilan',
            'disetujui' => 'Sudah Diambil',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            'expired' => 'Kedaluwarsa',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    public static function statusClass(string $status): string
    {
        return match ($status) {
            'menunggu_pembayaran' => 'panel-badge--neutral',
            'lunas' => 'panel-badge--info',
            'menunggu_approval' => 'panel-badge--warn',
            'disetujui', 'selesai' => 'panel-badge--ok',
            'ditolak', 'expired' => 'panel-badge--danger',
            default => 'panel-badge--neutral',
        };
    }

    public static function paymentStatusLabel(string $status): string
    {
        return match ($status) {
            'lunas' => 'Lunas',
            'menunggu_pembayaran' => 'Pending',
            'expired' => 'Expired',
            default => ucfirst($status),
        };
    }

    public static function paymentStatusClass(string $status): string
    {
        return match ($status) {
            'lunas' => 'panel-badge--ok',
            'menunggu_pembayaran' => 'panel-badge--warn',
            'expired' => 'panel-badge--danger',
            default => 'panel-badge--neutral',
        };
    }

    public static function pickupStatusDescription(string $status): string
    {
        return match ($status) {
            'menunggu_pembayaran' => 'Pelanggan belum menyelesaikan pembayaran.',
            'menunggu_approval' => 'Pembayaran lunas — barang masih di toko, menunggu verifikasi pengambilan.',
            'disetujui' => 'Admin sudah verifikasi — barang sudah diambil pelanggan.',
            'selesai' => 'Transaksi selesai.',
            'ditolak' => 'Pengambilan ditolak / dibatalkan.',
            'expired' => 'Pembayaran kedaluwarsa.',
            default => '',
        };
    }

    public static function pickupFlowSteps(): array
    {
        return [
            ['step' => '1', 'title' => 'Bayar Online', 'desc' => 'Pelanggan checkout & bayar via Midtrans Snap'],
            ['step' => '2', 'title' => 'Pembayaran Lunas', 'desc' => 'Barang disiapkan, masih di toko'],
            ['step' => '3', 'title' => 'Ambil di Toko', 'desc' => 'Pelanggan datang ke toko Selorejo'],
            ['step' => '4', 'title' => 'Approval Pengambilan', 'desc' => 'Admin ambil foto kamera & verifikasi'],
        ];
    }

    public static function formatRp(int $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }

    public static function findTransaction(string $id): ?array
    {
        foreach (self::transactions() as $trx) {
            if ($trx['id'] === $id) {
                return $trx;
            }
        }

        return null;
    }

    public static function paginate(array $items, int $perPage = 10, ?string $pageName = 'page'): LengthAwarePaginator
    {
        $page = Paginator::resolveCurrentPage($pageName);
        $collection = collect($items);

        $paginator = new LengthAwarePaginator(
            $collection->forPage($page, $perPage)->values()->all(),
            $collection->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'pageName' => $pageName]
        );

        return $paginator->withQueryString();
    }

    public static function paginateProducts(int $perPage = 8): LengthAwarePaginator
    {
        return self::paginate(self::products(), $perPage);
    }

    public static function paginateTransactions(int $perPage = 8, ?callable $filter = null): LengthAwarePaginator
    {
        $items = $filter ? array_values(array_filter(self::transactions(), $filter)) : self::transactions();

        return self::paginate($items, $perPage);
    }

    public static function findProduct(string $slug): ?array
    {
        foreach (self::products() as $p) {
            if ($p['slug'] === $slug) {
                return $p;
            }
        }

        return null;
    }

    public static function dashboardStats(): array
    {
        $transactions = self::transactions();
        $today = array_filter($transactions, fn ($t) => $t['date'] === '07/07/2026');
        $pendingApproval = array_filter($transactions, fn ($t) => $t['status'] === 'menunggu_approval');
        $monthRevenue = array_sum(array_column($transactions, 'total'));

        return [
            ['label' => 'Produk Aktif', 'value' => count(self::products()), 'hint' => '↑ 3 produk baru bulan ini', 'hint_type' => 'up', 'icon' => '📦'],
            ['label' => 'Transaksi Hari Ini', 'value' => count($today), 'hint' => count($transactions).' total bulan ini', 'hint_type' => 'neutral', 'icon' => '🧾'],
            ['label' => 'Menunggu Pengambilan', 'value' => count($pendingApproval), 'hint' => 'Lunas · barang masih di toko', 'hint_type' => 'warn', 'icon' => '⏳'],
            ['label' => 'Pendapatan Bulan Ini', 'value' => self::formatRp($monthRevenue), 'hint' => '↑ 18% vs bulan lalu', 'hint_type' => 'up', 'icon' => '💰', 'value_class' => 'green'],
        ];
    }

    public static function revenueChart(): array
    {
        return [
            ['month' => 'Feb', 'value' => 8200000, 'label' => '8,2 jt'],
            ['month' => 'Mar', 'value' => 9500000, 'label' => '9,5 jt'],
            ['month' => 'Apr', 'value' => 7800000, 'label' => '7,8 jt'],
            ['month' => 'Mei', 'value' => 11200000, 'label' => '11,2 jt'],
            ['month' => 'Jun', 'value' => 10500000, 'label' => '10,5 jt'],
            ['month' => 'Jul', 'value' => 12400000, 'label' => '12,4 jt'],
        ];
    }

    public static function topProducts(): array
    {
        return [
            ['name' => 'Pupuk Urea 50kg', 'emoji' => '🌱', 'sold' => 84, 'revenue' => 7140000],
            ['name' => 'Benih Cabai F1', 'emoji' => '🌾', 'sold' => 62, 'revenue' => 1550000],
            ['name' => 'Pupuk NPK 15-15-15', 'emoji' => '🌱', 'sold' => 41, 'revenue' => 3895000],
            ['name' => 'Sprayer 16 Liter', 'emoji' => '💧', 'sold' => 18, 'revenue' => 2700000],
            ['name' => 'Pupuk Organik 5kg', 'emoji' => '🌿', 'sold' => 35, 'revenue' => 1575000],
        ];
    }

    public static function categoryStats(): array
    {
        return [
            ['name' => 'Pupuk', 'emoji' => '🌱', 'count' => 18, 'percent' => 38],
            ['name' => 'Benih', 'emoji' => '🌾', 'count' => 12, 'percent' => 25],
            ['name' => 'Obat Tanaman', 'emoji' => '💊', 'count' => 10, 'percent' => 21],
            ['name' => 'Alat Tani', 'emoji' => '🔧', 'count' => 8, 'percent' => 16],
        ];
    }

    public static function transactionStatusSummary(): array
    {
        $transactions = self::transactions();
        $counts = [
            'menunggu_pembayaran' => 0,
            'menunggu_approval' => 0,
            'disetujui' => 0,
            'selesai' => 0,
        ];

        foreach ($transactions as $t) {
            if (isset($counts[$t['status']])) {
                $counts[$t['status']]++;
            }
        }

        $total = array_sum($counts) ?: 1;

        return collect($counts)->map(fn ($count, $status) => [
            'status' => $status,
            'count' => $count,
            'percent' => round($count / $total * 100),
        ])->values()->all();
    }

    public static function recentActivity(): array
    {
        return [
            ['icon' => '✅', 'text' => 'Pengambilan #TRX-004 diverifikasi — barang sudah diambil', 'time' => '5 menit lalu'],
            ['icon' => '💳', 'text' => 'Pembayaran #TRX-002 lunas — menunggu pengambilan di toko', 'time' => '22 menit lalu'],
            ['icon' => '📦', 'text' => 'Produk Pupuk Organik 5kg ditambahkan', 'time' => '1 jam lalu'],
            ['icon' => '⏳', 'text' => '#TRX-001 lunas · barang siap diambil · perlu verifikasi admin', 'time' => '2 jam lalu'],
            ['icon' => '🛒', 'text' => 'Pesanan baru #TRX-003 dari Budi Santoso', 'time' => '3 jam lalu'],
        ];
    }

    public static function midtransSettings(): array
    {
        return [
            'server_key' => 'SB-Mid-server-xxxxxxxxxxxxxxxx',
            'client_key' => 'SB-Mid-client-xxxxxxxxxxxxxxxx',
            'mode' => 'sandbox',
            'notification_url' => url('/api/midtrans/notification'),
            'finish_url' => url('/toko/pembayaran?status=finish'),
            'unfinish_url' => url('/toko/pembayaran?status=unfinish'),
            'error_url' => url('/toko/pembayaran?status=error'),
            'expiry_duration' => 1440,
            'is_active' => true,
            'channel_groups' => [
                'qris' => ['label' => 'QRIS', 'desc' => 'Scan QR & bayar instan'],
                'va' => ['label' => 'Virtual Account', 'desc' => 'Transfer bank otomatis'],
                'ewallet' => ['label' => 'E-Wallet', 'desc' => 'GoPay, ShopeePay, dll.'],
            ],
            'channels' => [
                ['code' => 'qris', 'name' => 'QRIS', 'group' => 'qris', 'icon' => 'QR', 'color' => '#059669', 'enabled' => true],
                ['code' => 'bca_va', 'name' => 'BCA Virtual Account', 'group' => 'va', 'icon' => 'BCA', 'color' => '#00529b', 'enabled' => true],
                ['code' => 'mandiri_va', 'name' => 'Mandiri Virtual Account', 'group' => 'va', 'icon' => 'MDR', 'color' => '#f59e0b', 'enabled' => true],
                ['code' => 'bni_va', 'name' => 'BNI Virtual Account', 'group' => 'va', 'icon' => 'BNI', 'color' => '#f97316', 'enabled' => true],
                ['code' => 'bri_va', 'name' => 'BRI Virtual Account', 'group' => 'va', 'icon' => 'BRI', 'color' => '#2563eb', 'enabled' => false],
                ['code' => 'gopay', 'name' => 'GoPay', 'group' => 'ewallet', 'icon' => 'GP', 'color' => '#00aed6', 'enabled' => true],
                ['code' => 'shopeepay', 'name' => 'ShopeePay', 'group' => 'ewallet', 'icon' => 'SP', 'color' => '#ee4d2d', 'enabled' => false],
            ],
        ];
    }
}
