<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductRepository
{
    private const SOLD_COUNT_SELECT = 'COALESCE((
        SELECT SUM(order_items.quantity)
        FROM order_items
        INNER JOIN orders ON orders.id = order_items.order_id
        WHERE order_items.product_id = products.id
        AND orders.payment_status = \'lunas\'
    ), 0) as sold_count';

    public function paginate(int $perPage = 8): LengthAwarePaginator
    {
        return DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.*', 'categories.name as category_name', 'categories.slug as category_slug')
            ->orderByDesc('products.created_at')
            ->paginate($perPage);
    }

    public function listActive(?int $categoryId = null): Collection
    {
        $query = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.*', 'categories.name as category_name')
            ->selectRaw(self::SOLD_COUNT_SELECT)
            ->where('products.status', 'active')
            ->where('categories.is_active', true);

        if ($categoryId) {
            $query->where('products.category_id', $categoryId);
        }

        return $query->orderBy('products.name')->get();
    }

    public function paginateActive(int $perPage, int $page, string $search = '', array $categoryIds = []): LengthAwarePaginator
    {
        $query = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.*', 'categories.name as category_name', 'categories.slug as category_slug')
            ->selectRaw(self::SOLD_COUNT_SELECT)
            ->where('products.status', 'active')
            ->where('categories.is_active', true);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('products.name', 'like', '%'.$search.'%')
                    ->orWhere('categories.name', 'like', '%'.$search.'%');
            });
        }

        if ($categoryIds !== []) {
            $query->whereIn('products.category_id', $categoryIds);
        }

        return $query
            ->orderByDesc('products.created_at')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function findBySlug(string $slug): ?object
    {
        return DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.*', 'categories.name as category_name', 'categories.slug as category_slug')
            ->selectRaw(self::SOLD_COUNT_SELECT)
            ->where('products.slug', $slug)
            ->first();
    }

    public function findById(int $id): ?object
    {
        return DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.*', 'categories.name as category_name')
            ->where('products.id', $id)
            ->first();
    }

    public function findActiveById(int $id): ?object
    {
        return DB::table('products')
            ->where('id', $id)
            ->where('status', 'active')
            ->first();
    }

    public function insert(array $data): int
    {
        return (int) DB::table('products')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));
    }

    public function update(int $id, array $data): int
    {
        return DB::table('products')
            ->where('id', $id)
            ->update(array_merge($data, ['updated_at' => now()]));
    }

    public function softDelete(int $id): int
    {
        return $this->update($id, ['status' => 'draft']);
    }
}
