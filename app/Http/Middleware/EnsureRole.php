<?php

namespace App\Http\Middleware;

use App\Support\JwtCookie;
use Closure;
use Exception;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $role = auth('api')->user()?->role;

        if (! $role) {
            $token = $request->cookie(JwtCookie::ACCESS) ?? $request->bearerToken();

            if (! $token) {
                abort(401, 'Unauthenticated.');
            }

            try {
                $role = JWTAuth::setToken($token)->getPayload()->get('role');
            } catch (Exception $e) {
                abort(401, 'Unauthenticated.');
            }
        }

        if (! in_array($role, $roles, true)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden.'], 403);
            }

            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
