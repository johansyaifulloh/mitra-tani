<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderItemRepository
{
    public function insert(array $data): int
    {
        return (int) DB::table('order_items')->insertGetId($data);
    }

    public function listForOrder(int $orderId): Collection
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('order_items.*', 'products.name as product_name', 'products.emoji')
            ->where('order_items.order_id', $orderId)
            ->get();
    }
}
