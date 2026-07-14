<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CategoryRepository
{
    public function allActive(): Collection
    {
        return DB::table('categories')
            ->where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();
    }

    public function allActiveWithProductCount(): Collection
    {
        return DB::table('categories')
            ->select(
                'categories.*',
                DB::raw('(SELECT COUNT(*) FROM products WHERE products.category_id = categories.id AND products.status = \'active\') as product_count'),
            )
            ->where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();
    }

    public function all(): Collection
    {
        return DB::table('categories')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();
    }

    public function findById(int $id): ?object
    {
        return DB::table('categories')->where('id', $id)->first();
    }

    public function findBySlug(string $slug): ?object
    {
        return DB::table('categories')->where('slug', $slug)->first();
    }

    public function insert(array $data): int
    {
        return (int) DB::table('categories')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));
    }

    public function update(int $id, array $data): int
    {
        return DB::table('categories')
            ->where('id', $id)
            ->update(array_merge($data, ['updated_at' => now()]));
    }

    public function paginateForAdmin(
        int $page,
        int $perPage,
        string $search = '',
        array $categoryIds = [],
        array $statuses = [],
    ): LengthAwarePaginator {
        $query = DB::table('categories');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('categories.name', 'like', '%'.$search.'%')
                    ->orWhere('categories.description', 'like', '%'.$search.'%');
            });
        }

        if ($categoryIds !== []) {
            $query->whereIn('categories.id', $categoryIds);
        }

        if ($statuses !== []) {
            $activeValues = collect($statuses)->map(function ($status) {
                return in_array($status, ['active', '1', 1, true], true) ? 1 : 0;
            })->unique()->values()->all();

            $query->whereIn('categories.is_active', $activeValues);
        }

        return $query
            ->orderBy('categories.display_order')
            ->orderBy('categories.name')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function countProducts(int $categoryId): int
    {
        return (int) DB::table('products')
            ->where('category_id', $categoryId)
            ->count();
    }

    public function delete(int $id): int
    {
        return DB::table('categories')->where('id', $id)->delete();
    }
}
