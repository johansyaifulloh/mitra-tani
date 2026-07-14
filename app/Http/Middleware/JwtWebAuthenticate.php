<?php

namespace App\Http\Middleware;

use App\Support\JwtCookie;
use Closure;
use Exception;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class JwtWebAuthenticate
{
    public function handle(Request $request, Closure $next, ?string $loginRoute = null): Response
    {
        $token = $request->cookie(JwtCookie::ACCESS);

        if (! $token) {
            return $this->redirectToLogin($request, $loginRoute);
        }

        try {
            $user = JWTAuth::setToken($token)->authenticate();

            // Pemisahan area: rute toko hanya untuk pelanggan.
            // Rute admin (loginRoute = 'admin') dibiarkan, role dicek middleware role.
            if ($loginRoute !== 'admin' && $user && $user->role !== 'customer') {
                return $this->redirectToLogin($request, $loginRoute);
            }

            auth('api')->setUser($user);
        } catch (Exception $e) {
            return $this->redirectToLogin($request, $loginRoute);
        }

        return $next($request);
    }

    private function redirectToLogin(Request $request, ?string $loginRoute): Response
    {
        $route = $loginRoute === 'admin' ? 'admin.login' : 'toko.login';

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return redirect()->route($route, [
            'redirect' => $request->route()?->getName() ?? '',
        ])->with('info', 'Silakan login terlebih dahulu.');
    }
}
