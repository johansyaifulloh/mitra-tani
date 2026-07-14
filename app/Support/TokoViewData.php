<?php

namespace App\Support;

use App\Repositories\CartRepository;
use App\Support\JwtCookie;
use Exception;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class TokoViewData
{
    public static function resolve(?Request $request = null, ?string $redirectRoute = null): array
    {
        $request ??= request();
        $loggedIn = false;
        $userId = null;

        if ($token = $request->cookie(JwtCookie::ACCESS)) {
            try {
                $user = JWTAuth::setToken($token)->authenticate();

                // Area toko hanya untuk pelanggan; admin/owner dianggap tamu.
                if ($user && $user->role === 'customer') {
                    auth('api')->setUser($user);
                    $loggedIn = true;
                    $userId = $user->id;
                }
            } catch (Exception $e) {
                $loggedIn = false;
            }
        }

        $cartCount = 0;

        if ($userId) {
            // Badge hanya menghitung item yang tercentang (is_selected)
            $cartCount = app(CartRepository::class)->selectedQuantityForUser($userId);
        }

        $redirect = $redirectRoute ?? $request->route()?->getName() ?? 'home';

        return [
            'tokoLoggedIn' => $loggedIn,
            'tokoCartCount' => $cartCount,
            'tokoLoginUrl' => route('toko.login', ['redirect' => $redirect]),
            'tokoKeranjangUrl' => route('toko.keranjang.index'),
        ];
    }
}
