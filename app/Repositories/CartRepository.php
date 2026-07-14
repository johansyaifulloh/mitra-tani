<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CartRepository
{
    public function listForUser(int $userId): Collection
    {
        return DB::table('cart_items')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->select(
                'cart_items.*',
                'products.name as product_name',
                'products.price',
                'products.emoji',
                'products.slug as product_slug',
                'products.stock',
            )
            ->where('cart_items.user_id', $userId)
            ->orderBy('cart_items.created_at')
            ->get();
    }

    public function findItem(int $userId, int $productId): ?object
    {
        return DB::table('cart_items')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();
    }

    public function findById(int $id, int $userId): ?object
    {
        return DB::table('cart_items')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    public function insert(array $data): int
    {
        return (int) DB::table('cart_items')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));
    }

    public function update(int $id, array $data): int
    {
        return DB::table('cart_items')
            ->where('id', $id)
            ->update(array_merge($data, ['updated_at' => now()]));
    }

    public function delete(int $id): int
    {
        return DB::table('cart_items')->where('id', $id)->delete();
    }

    public function clearForUser(int $userId): int
    {
        return DB::table('cart_items')->where('user_id', $userId)->delete();
    }

    public function setSelectAllForUser(int $userId, bool $isSelected): int
    {
        return DB::table('cart_items')
            ->where('user_id', $userId)
            ->update([
                'is_selected' => $isSelected,
                'updated_at' => now(),
            ]);
    }

    public function selectedForUser(int $userId): Collection
    {
        return DB::table('cart_items')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->select('cart_items.*', 'products.name as product_name', 'products.price', 'products.emoji')
            ->where('cart_items.user_id', $userId)
            ->where('cart_items.is_selected', true)
            ->get();
    }

    public function totalQuantityForUser(int $userId): int
    {
        return (int) DB::table('cart_items')
            ->where('user_id', $userId)
            ->sum('quantity');
    }

    public function selectedQuantityForUser(int $userId): int
    {
        return (int) DB::table('cart_items')
            ->where('user_id', $userId)
            ->where('is_selected', true)
            ->sum('quantity');
    }
}
