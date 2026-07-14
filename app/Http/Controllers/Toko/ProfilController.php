<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use App\Support\JwtCookie;
use Exception;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ProfilController extends Controller
{
    public function index(Request $request)
    {
        $user = null;

        $token = $request->cookie(JwtCookie::ACCESS);

        if ($token) {
            try {
                $authUser = JWTAuth::setToken($token)->authenticate();

                // Area toko hanya untuk pelanggan; admin/owner dianggap tamu.
                if ($authUser && $authUser->role === 'customer') {
                    $user = $authUser;
                    auth('api')->setUser($user);
                }
            } catch (Exception $e) {
                $user = null;
            }
        }

        return view('mobile.profil.index', [
            'user' => $user,
        ]);
    }
}
