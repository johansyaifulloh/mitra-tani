<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\RefreshTokenRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function __construct(
        private UserRepository $userRepository,
        private RefreshTokenRepository $refreshTokenRepository,
    ) {}

    public function login(string $identifier, string $password, ?array $allowedRoles = null): array
    {
        // STEP 1: Get DB reference
        $userRow = $this->userRepository->findByIdentifier($identifier);

        // STEP 2: Validate DB results
        if (! $userRow || ! Hash::check($password, $userRow->password)) {
            throw new Exception('Email/telepon atau password salah.');
        }

        if ($allowedRoles && ! in_array($userRow->role, $allowedRoles, true)) {
            throw new Exception('Akun tidak memiliki akses ke halaman ini.');
        }

        // STEP 3: Business logic — issue tokens
        return $this->issueTokens($userRow);
    }

    public function register(array $data): array
    {
        // STEP 1: Get DB reference
        $existingEmail = $data['email'] ? $this->userRepository->findByEmail($data['email']) : null;
        $existingPhone = $this->userRepository->findByPhone($data['phone']);

        // STEP 2: Validate DB results
        if ($existingEmail) {
            throw new Exception('Email sudah terdaftar.');
        }

        if ($existingPhone) {
            throw new Exception('Nomor telepon sudah terdaftar.');
        }

        // STEP 3: Business logic
        $userId = $this->userRepository->insert([
            'name' => $data['name'],
            'email' => $data['email'] ?: null,
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        // STEP 4: Get created user
        $userRow = $this->userRepository->findById($userId);

        return $this->issueTokens($userRow);
    }

    public function refresh(string $refreshToken): array
    {
        // STEP 1: Get DB reference
        $tokenHash = hash('sha256', $refreshToken);
        $stored = $this->refreshTokenRepository->findValidByHash($tokenHash);

        // STEP 2: Validate DB results
        if (! $stored) {
            throw new Exception('Refresh token tidak valid atau sudah kedaluwarsa.');
        }

        // STEP 3: Revoke old token, issue new pair
        $this->refreshTokenRepository->revoke($stored->id);
        $userRow = $this->userRepository->findById($stored->user_id);

        if (! $userRow) {
            throw new Exception('User tidak ditemukan.');
        }

        return $this->issueTokens($userRow);
    }

    public function logout(?string $refreshToken, ?string $accessToken = null, ?int $userId = null): void
    {
        try {
            if ($refreshToken) {
                $this->refreshTokenRepository->revokeByHash(hash('sha256', $refreshToken));
            }

            if ($accessToken) {
                JWTAuth::setToken($accessToken)->invalidate(true);
            }
        } catch (Exception $e) {
            Log::error('AuthService::logout failed', ['error' => $e->getMessage()]);
        }
    }

    public function me(int $userId): ?object
    {
        return $this->userRepository->findById($userId);
    }

    private function issueTokens(object $userRow): array
    {
        $user = User::query()->find($userRow->id);
        $accessToken = JWTAuth::fromUser($user);

        $plainRefresh = Str::random(64);
        $expiresAt = now()->addMinutes((int) config('jwt.refresh_ttl', 20160));

        $this->refreshTokenRepository->insert(
            $userRow->id,
            hash('sha256', $plainRefresh),
            $expiresAt->toDateTimeString(),
        );

        return [
            'access_token' => $accessToken,
            'refresh_token' => $plainRefresh,
            'token_type' => 'bearer',
            'expires_in' => (int) config('jwt.ttl', 60) * 60,
            'user' => [
                'id' => $userRow->id,
                'name' => $userRow->name,
                'email' => $userRow->email,
                'phone' => $userRow->phone,
                'role' => $userRow->role,
            ],
        ];
    }
}
