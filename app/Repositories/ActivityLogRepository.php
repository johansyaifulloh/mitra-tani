<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ActivityLogRepository
{
    public function recent(int $limit = 8): Collection
    {
        return DB::table('activity_logs')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function insert(array $data): int
    {
        return (int) DB::table('activity_logs')->insertGetId(array_merge($data, [
            'created_at' => now(),
        ]));
    }
}
