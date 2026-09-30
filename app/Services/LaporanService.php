<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Support\FormatHelper;
use App\Support\SampleData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LaporanService
{
    public function __construct(
        private OrderRepository $orderRepository,
        private OrderService $orderService,
    ) {}

    public function paginate(?string $dateFrom, ?string $dateTo, int $perPage = 10): LengthAwarePaginator
    {
        return $this->paginateTransactions($dateFrom, $dateTo, $perPage);
    }

    public function paginateTransactions(?string $dateFrom, ?string $dateTo, int $perPage = 10): LengthAwarePaginator
    {
        $this->orderService->expireOverdueOrders();

        $paginator = $this->orderRepository->paginateFiltered($perPage, $dateFrom, $dateTo);

        $orderIds = collect($paginator->items())->pluck('id')->all();
        $itemsByOrder = $this->fetchItemsForOrders($orderIds);
        $proofsByOrder = $this->fetchProofsForOrders($orderIds);

        return $paginator->through(function ($row) use ($itemsByOrder, $proofsByOrder) {
            $item = FormatHelper::orderForAdmin($row);
            $item['status'] = $row->pickup_status ?? $row->payment_status;
            $item['status_label'] = SampleData::statusLabel($item['status']);
            $item['subtotal'] = (float)($row->subtotal ?? 0);
            $item['admin_fee'] = (float)($row->admin_fee ?? 0);
            $item['subtotal_rp'] = FormatHelper::rupiah($row->subtotal ?? 0);
            $item['admin_fee_rp'] = FormatHelper::rupiah($row->admin_fee ?? 0);

            $orderItems = $itemsByOrder->get($row->id, collect())->values()->all();
            $item['items'] = $orderItems;
            $item['items_summary'] = count($orderItems) > 0
                ? collect($orderItems)->map(fn ($it) => $it['product_name'].' (x'.$it['quantity'].')')->implode(', ')
                : '1 Paket Produk';

            $item['pickup_proof'] = $proofsByOrder->get($row->id);

            return $item;
        });
    }

    public function summary(?string $dateFrom, ?string $dateTo): array
    {
        $this->orderService->expireOverdueOrders();

        $allOrders = $this->orderRepository->getAllFiltered($dateFrom, $dateTo);

        $totalRevenue = 0;
        $paidCount = 0;
        $pendingCount = 0;
        $pickupDoneCount = 0;

        foreach ($allOrders as $order) {
            if ($order->payment_status === 'lunas') {
                $totalRevenue += (float) $order->total;
                $paidCount++;
            }
            if ($order->payment_status === 'menunggu_pembayaran') {
                $pendingCount++;
            }
            if (in_array($order->pickup_status ?? '', ['disetujui', 'selesai'], true)) {
                $pickupDoneCount++;
            }
        }

        return [
            'total_revenue' => $totalRevenue,
            'total_orders' => $allOrders->count(),
            'paid_count' => $paidCount,
            'pending_count' => $pendingCount,
            'pickup_done_count' => $pickupDoneCount,
        ];
    }

    public function exportData(?string $dateFrom, ?string $dateTo): array
    {
        $this->orderService->expireOverdueOrders();

        $orders = $this->orderRepository->getAllFiltered($dateFrom, $dateTo);
        $orderIds = $orders->pluck('id')->all();
        $itemsByOrder = $this->fetchItemsForOrders($orderIds);

        $transactions = $orders->map(function ($row) use ($itemsByOrder) {
            $item = FormatHelper::orderForAdmin($row);
            $item['status'] = $row->pickup_status ?? $row->payment_status;
            $item['status_label'] = SampleData::statusLabel($item['status']);
            $item['payment_status_label'] = FormatHelper::paymentStatusLabel($row->payment_status)['label'];
            $item['pickup_status_label'] = FormatHelper::pickupStatusLabel($row->pickup_status)['label'] ?: '—';
            $item['raw_date'] = date('d F Y, H:i', strtotime($row->created_at));
            $item['subtotal'] = (float) ($row->subtotal ?? 0);
            $item['admin_fee'] = (float) ($row->admin_fee ?? 0);
            $item['subtotal_rp'] = FormatHelper::rupiah($row->subtotal ?? 0);
            $item['admin_fee_rp'] = FormatHelper::rupiah($row->admin_fee ?? 0);

            $orderItems = $itemsByOrder->get($row->id, collect())->values()->all();
            $item['items'] = $orderItems;
            $item['items_summary'] = count($orderItems) > 0
                ? collect($orderItems)->map(fn ($it) => $it['product_name'].' (x'.$it['quantity'].')')->implode(', ')
                : '1 Paket Produk';

            return $item;
        });

        $summary = $this->summary($dateFrom, $dateTo);

        return [
            'transactions' => $transactions,
            'summary' => $summary,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'printed_at' => date('d F Y, H:i').' WIB',
        ];
    }

    private function fetchItemsForOrders(array $orderIds): Collection
    {
        if (empty($orderIds)) {
            return collect();
        }

        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('order_items.*', 'products.name as product_name', 'products.emoji', 'products.image_path')
            ->whereIn('order_items.order_id', $orderIds)
            ->get()
            ->groupBy('order_id')
            ->map(function ($group) {
                return $group->map(function ($it) {
                    return [
                        'id' => $it->id,
                        'product_id' => $it->product_id,
                        'product_name' => $it->product_name,
                        'emoji' => $it->emoji ?? '🌱',
                        'unit_price' => (float) $it->unit_price,
                        'unit_price_rp' => FormatHelper::rupiah($it->unit_price),
                        'quantity' => (int) $it->quantity,
                        'subtotal' => (float) $it->subtotal,
                        'subtotal_rp' => FormatHelper::rupiah($it->subtotal),
                    ];
                });
            });
    }

    private function fetchProofsForOrders(array $orderIds): Collection
    {
        if (empty($orderIds)) {
            return collect();
        }

        return DB::table('pickup_proofs')
            ->join('users', 'pickup_proofs.verified_by', '=', 'users.id')
            ->select('pickup_proofs.*', 'users.name as verified_by_name')
            ->whereIn('pickup_proofs.order_id', $orderIds)
            ->get()
            ->keyBy('order_id')
            ->map(function ($p) {
                return [
                    'photo' => FormatHelper::proofPhotoUrl($p->photo_path),
                    'note' => $p->note ?? '',
                    'verified_by' => $p->verified_by_name,
                    'verified_at' => date('d/m/Y H:i', strtotime($p->verified_at)),
                ];
            });
    }
}
