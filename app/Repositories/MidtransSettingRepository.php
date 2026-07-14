<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MidtransSettingRepository
{
    public function getActive(): ?object
    {
        return DB::table('midtrans_settings')
            ->where('is_active', true)
            ->first();
    }

    public function first(): ?object
    {
        return DB::table('midtrans_settings')
            ->orderBy('id')
            ->first();
    }

    public function update(int $id, array $data): int
    {
        return DB::table('midtrans_settings')
            ->where('id', $id)
            ->update(array_merge($data, ['updated_at' => now()]));
    }
}
