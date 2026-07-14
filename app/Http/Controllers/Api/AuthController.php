<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $tokens = $this->authService->register($request->validated());

            return response()->json($tokens, 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $tokens = $this->authService->login($data['identifier'], $data['password']);

            return response()->json($tokens);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    public function refresh(Request $request): JsonResponse
    {
        $refreshToken = $request->input('refresh_token') ?? $request->cookie('mt_refresh');

        if (! $refreshToken) {
            return response()->json(['message' => 'Refresh token required.'], 401);
        }

        try {
            $tokens = $this->authService->refresh($refreshToken);

            return response()->json($tokens);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout(
            $request->input('refresh_token') ?? $request->cookie('mt_refresh'),
            $request->bearerToken(),
        );

        return response()->json(['message' => 'Logged out.']);
    }

    public function me(): JsonResponse
    {
        $user = $this->authService->me(auth('api')->id());

        if (! $user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
        ]);
    }
}
