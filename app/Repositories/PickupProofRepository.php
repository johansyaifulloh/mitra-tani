<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class PickupProofRepository
{
    public function findByOrderId(int $orderId): ?object
    {
        return DB::table('pickup_proofs')
            ->join('users', 'pickup_proofs.verified_by', '=', 'users.id')
            ->select('pickup_proofs.*', 'users.name as verified_by_name')
            ->where('pickup_proofs.order_id', $orderId)
            ->first();
    }

    public function insert(array $data): int
    {
        return (int) DB::table('pickup_proofs')->insertGetId($data);
    }
}
