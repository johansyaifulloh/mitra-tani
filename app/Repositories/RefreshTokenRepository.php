<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class RefreshTokenRepository
{
    public function findValidByHash(string $tokenHash): ?object
    {
        return DB::table('refresh_tokens')
            ->where('token_hash', $tokenHash)
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->first();
    }

    public function insert(int $userId, string $tokenHash, string $expiresAt): int
    {
        return (int) DB::table('refresh_tokens')->insertGetId([
            'user_id' => $userId,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
            'created_at' => now(),
        ]);
    }

    public function revoke(int $id): void
    {
        DB::table('refresh_tokens')
            ->where('id', $id)
            ->update(['revoked_at' => now()]);
    }

    public function revokeAllForUser(int $userId): void
    {
        DB::table('refresh_tokens')
            ->where('user_id', $userId)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);
    }

    public function revokeByHash(string $tokenHash): void
    {
        DB::table('refresh_tokens')
            ->where('token_hash', $tokenHash)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);
    }
}
