<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class UserRepository
{
    public function findById(int $id): ?object
    {
        return DB::table('users')->where('id', $id)->first();
    }

    public function findByEmail(string $email): ?object
    {
        return DB::table('users')->where('email', $email)->first();
    }

    public function findByPhone(string $phone): ?object
    {
        return DB::table('users')->where('phone', $phone)->first();
    }

    public function findByIdentifier(string $identifier): ?object
    {
        if (str_contains($identifier, '@')) {
            return $this->findByEmail($identifier);
        }

        return $this->findByPhone($identifier);
    }

    public function insert(array $data): int
    {
        return (int) DB::table('users')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));
    }
}
