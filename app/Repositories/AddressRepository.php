<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AddressRepository
{
    public function listForUser(int $userId): Collection
    {
        return DB::table('addresses')
            ->where('user_id', $userId)
            ->orderByDesc('is_default')
            ->orderBy('label')
            ->get();
    }

    public function findById(int $id, int $userId): ?object
    {
        return DB::table('addresses')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    public function insert(array $data): int
    {
        return (int) DB::table('addresses')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));
    }

    public function update(int $id, array $data): int
    {
        return DB::table('addresses')
            ->where('id', $id)
            ->update(array_merge($data, ['updated_at' => now()]));
    }

    public function delete(int $id): int
    {
        return DB::table('addresses')->where('id', $id)->delete();
    }

    public function clearDefaultForUser(int $userId): void
    {
        DB::table('addresses')
            ->where('user_id', $userId)
            ->update(['is_default' => false, 'updated_at' => now()]);
    }

    public function countForUser(int $userId): int
    {
        return (int) DB::table('addresses')
            ->where('user_id', $userId)
            ->count();
    }
}
