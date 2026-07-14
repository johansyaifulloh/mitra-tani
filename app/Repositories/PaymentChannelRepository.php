<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PaymentChannelRepository
{
    public function listAll(): Collection
    {
        return DB::table('payment_channels')
            ->orderBy('code')
            ->get();
    }

    public function listActive(): Collection
    {
        return DB::table('payment_channels')
            ->where('is_enabled', true)
            ->orderBy('code')
            ->get();
    }

    public function update(int $id, array $data): int
    {
        return DB::table('payment_channels')
            ->where('id', $id)
            ->update($data);
    }

    public function setEnabledByCodes(array $enabledCodes): void
    {
        // Aktifkan channel yang ada di daftar, nonaktifkan sisanya
        DB::table('payment_channels')->update(['is_enabled' => false]);

        if (! empty($enabledCodes)) {
            DB::table('payment_channels')
                ->whereIn('code', $enabledCodes)
                ->update(['is_enabled' => true]);
        }
    }
}
