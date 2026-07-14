<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    public function paginate(int $perPage = 10, ?callable $filter = null): LengthAwarePaginator
    {
        $query = DB::table('orders')
            ->leftJoin('addresses', 'orders.address_id', '=', 'addresses.id')
            ->select(
                'orders.*',
                DB::raw("CONCAT(addresses.street, ', ', addresses.district) as address_text"),
            )
            ->orderByDesc('orders.created_at');

        if ($filter) {
            $query->where($filter);
        }

        return $query->paginate($perPage);
    }

    public function paginateFiltered(int $perPage, ?string $dateFrom, ?string $dateTo): LengthAwarePaginator
    {
        $query = DB::table('orders')
            ->leftJoin('addresses', 'orders.address_id', '=', 'addresses.id')
            ->select(
                'orders.*',
                DB::raw("CONCAT(addresses.street, ', ', addresses.district) as address_text"),
            )
            ->orderByDesc('orders.created_at');

        if ($dateFrom) {
            $query->whereDate('orders.created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('orders.created_at', '<=', $dateTo);
        }

        return $query->paginate($perPage);
    }

    public function listForUser(int $userId, ?string $paymentStatus = null): Collection
    {
        $query = DB::table('orders')
            ->leftJoin('addresses', 'orders.address_id', '=', 'addresses.id')
            ->leftJoin('pickup_proofs', 'orders.id', '=', 'pickup_proofs.order_id')
            ->leftJoin('users as verifier', 'pickup_proofs.verified_by', '=', 'verifier.id')
            ->select(
                'orders.*',
                DB::raw("CONCAT(addresses.street, ', ', addresses.district) as address_text"),
                'pickup_proofs.photo_path as proof_photo',
                'pickup_proofs.note as proof_note',
                'pickup_proofs.verified_at as proof_verified_at',
                'verifier.name as proof_verifier',
                DB::raw('(SELECT COUNT(*) FROM order_items WHERE order_items.order_id = orders.id) as item_count'),
                DB::raw('(SELECT SUM(quantity) FROM order_items WHERE order_items.order_id = orders.id) as total_qty'),
                DB::raw('(SELECT products.name FROM order_items JOIN products ON order_items.product_id = products.id WHERE order_items.order_id = orders.id ORDER BY order_items.id LIMIT 1) as first_product_name'),
                DB::raw('(SELECT products.emoji FROM order_items JOIN products ON order_items.product_id = products.id WHERE order_items.order_id = orders.id ORDER BY order_items.id LIMIT 1) as first_product_emoji'),
                DB::raw('(SELECT order_items.quantity FROM order_items WHERE order_items.order_id = orders.id ORDER BY order_items.id LIMIT 1) as first_product_qty'),
            )
            ->where('orders.user_id', $userId)
            ->orderByDesc('orders.created_at');

        if ($paymentStatus) {
            $query->where('orders.payment_status', $paymentStatus);
        }

        return $query->get();
    }

    public function countByPaymentStatusForUser(int $userId): array
    {
        return DB::table('orders')
            ->select('payment_status', DB::raw('COUNT(*) as total'))
            ->where('user_id', $userId)
            ->groupBy('payment_status')
            ->pluck('total', 'payment_status')
            ->all();
    }

    public function findByCode(string $code): ?object
    {
        return DB::table('orders')
            ->leftJoin('addresses', 'orders.address_id', '=', 'addresses.id')
            ->select(
                'orders.*',
                DB::raw("CONCAT(addresses.street, ', ', addresses.district) as address_text"),
            )
            ->where('orders.code', $code)
            ->first();
    }

    public function findById(int $id): ?object
    {
        return DB::table('orders')->where('id', $id)->first();
    }

    public function findByIdForUser(int $id, int $userId): ?object
    {
        return DB::table('orders')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    public function insert(array $data): int
    {
        return (int) DB::table('orders')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));
    }

    public function update(int $id, array $data): int
    {
        return DB::table('orders')
            ->where('id', $id)
            ->update(array_merge($data, ['updated_at' => now()]));
    }

    public function countByStatus(string $column, string $status): int
    {
        return DB::table('orders')->where($column, $status)->count();
    }

    public function sumRevenue(?string $dateFrom = null, ?string $dateTo = null): float
    {
        $query = DB::table('orders')->where('payment_status', 'lunas');

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return (float) ($query->sum('total') ?? 0);
    }

    public function revenueByDay(int $days = 7): Collection
    {
        return DB::table('orders')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as revenue'))
            ->where('payment_status', 'lunas')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();
    }

    public function topProducts(int $limit = 5): Collection
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as sold'))
            ->where('orders.payment_status', 'lunas')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('sold')
            ->limit($limit)
            ->get();
    }

    public function listForApproval(int $perPage = 6): LengthAwarePaginator
    {
        return DB::table('orders')
            ->leftJoin('addresses', 'orders.address_id', '=', 'addresses.id')
            ->select(
                'orders.*',
                DB::raw("CONCAT(addresses.street, ', ', addresses.district) as address_text"),
            )
            ->where('orders.payment_status', 'lunas')
            ->whereIn('orders.pickup_status', ['menunggu_approval', 'disetujui'])
            ->orderByDesc('orders.created_at')
            ->paginate($perPage);
    }
}
