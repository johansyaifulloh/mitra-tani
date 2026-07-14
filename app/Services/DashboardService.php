<?php

namespace App\Services;

use App\Repositories\ActivityLogRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Support\FormatHelper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function __construct(
        private OrderRepository $orderRepository,
        private ProductRepository $productRepository,
        private CategoryRepository $categoryRepository,
        private ActivityLogRepository $activityLogRepository,
    ) {}

    public function stats(): array
    {
        $totalProducts = $this->productRepository->listActive()->count();
        $todayOrders = DB::table('orders')->whereDate('created_at', today())->count();
        $pendingApproval = $this->orderRepository->countByStatus('pickup_status', 'menunggu_approval');
        $monthRevenue = $this->orderRepository->sumRevenue(
            now()->startOfMonth()->toDateString(),
            now()->toDateString(),
        );

        return [
            ['label' => 'Produk Aktif', 'value' => $totalProducts, 'hint' => 'Dari database katalog', 'hint_type' => 'up', 'icon' => '📦'],
            ['label' => 'Transaksi Hari Ini', 'value' => $todayOrders, 'hint' => $this->orderRepository->paginate(1)->total().' total', 'hint_type' => 'neutral', 'icon' => '🧾'],
            ['label' => 'Menunggu Pengambilan', 'value' => $pendingApproval, 'hint' => 'Lunas · barang masih di toko', 'hint_type' => 'warn', 'icon' => '⏳'],
            ['label' => 'Pendapatan Bulan Ini', 'value' => FormatHelper::rupiah($monthRevenue), 'hint' => 'Data dari pesanan lunas', 'hint_type' => 'up', 'icon' => '💰', 'value_class' => 'green'],
        ];
    }

    public function recentTransactions(int $perPage = 5): LengthAwarePaginator
    {
        return $this->orderRepository->paginate($perPage);
    }

    public function revenueChart(int $months = 6): array
    {
        $rows = $this->orderRepository->revenueByDay($months * 30);
        $monthsMap = [];

        foreach ($rows as $row) {
            $key = date('M', strtotime($row->date));
            $monthsMap[$key] = ($monthsMap[$key] ?? 0) + (float) $row->revenue;
        }

        $defaults = ['Feb' => 0, 'Mar' => 0, 'Apr' => 0, 'Mei' => 0, 'Jun' => 0, 'Jul' => 0];

        foreach ($defaults as $month => $zero) {
            if (! isset($monthsMap[$month])) {
                $monthsMap[$month] = $zero;
            }
        }

        return collect($monthsMap)->map(function ($value, $month) {
            $jt = $value >= 1000000 ? number_format($value / 1000000, 1, ',', '.').' jt' : number_format($value / 1000, 0, ',', '.').' rb';

            return ['month' => $month, 'value' => (int) $value, 'label' => $jt];
        })->values()->all();
    }

    public function topProducts(int $limit = 5): array
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                'products.name',
                'products.emoji',
                DB::raw('SUM(order_items.quantity) as sold'),
                DB::raw('SUM(order_items.subtotal) as revenue'),
            )
            ->where('orders.payment_status', 'lunas')
            ->groupBy('products.id', 'products.name', 'products.emoji')
            ->orderByDesc('sold')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'name' => $row->name,
                'emoji' => $row->emoji ?? '🌱',
                'sold' => (int) $row->sold,
                'revenue' => (float) $row->revenue,
            ])
            ->all();
    }

    public function categoryStats(): array
    {
        $categories = $this->categoryRepository->allActive();
        $products = $this->productRepository->listActive();
        $total = max($products->count(), 1);

        return $categories->map(function ($cat) use ($products, $total) {
            $count = $products->where('category_id', $cat->id)->count();

            return [
                'name' => $cat->name,
                'emoji' => $cat->icon,
                'count' => $count,
                'percent' => (int) round(($count / $total) * 100),
            ];
        })->all();
    }

    public function statusSummary(): array
    {
        $counts = [
            'menunggu_pembayaran' => $this->orderRepository->countByStatus('payment_status', 'menunggu_pembayaran'),
            'menunggu_approval' => $this->orderRepository->countByStatus('pickup_status', 'menunggu_approval'),
            'disetujui' => $this->orderRepository->countByStatus('pickup_status', 'disetujui'),
            'selesai' => $this->orderRepository->countByStatus('pickup_status', 'selesai'),
        ];

        $total = max(array_sum($counts), 1);

        return collect($counts)->map(function ($count, $status) use ($total) {
            return [
                'status' => $status,
                'count' => $count,
                'percent' => (int) round(($count / $total) * 100),
            ];
        })->values()->all();
    }

    public function recentActivity(int $limit = 5): array
    {
        return $this->activityLogRepository->recent($limit)
            ->map(fn ($row) => [
                'icon' => '📋',
                'text' => $row->description ?? $row->action,
                'time' => date('d/m/Y H:i', strtotime($row->created_at)),
            ])
            ->all();
    }
}
