<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use App\Repositories\OrderRepository;
use App\Support\JwtCookie;
use Exception;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ProfilController extends Controller
{
    public function __construct(
        private OrderRepository $orderRepository,
    ) {}

    public function index(Request $request)
    {
        $user = null;
        $orderCounts = [
            'unpaid' => 0,
            'ready_pickup' => 0,
            'completed' => 0,
            'cancelled' => 0,
            'total' => 0,
            'active' => 0,
        ];

        $token = $request->cookie(JwtCookie::ACCESS);

        if ($token) {
            try {
                $authUser = JWTAuth::setToken($token)->authenticate();

                // Area toko hanya untuk pelanggan; admin/owner dianggap tamu.
                if ($authUser && $authUser->role === 'customer') {
                    $user = $authUser;
                    auth('api')->setUser($user);
                    $orderCounts = $this->orderRepository->getOrderCountsForUser($user->id);
                }
            } catch (Exception $e) {
                $user = null;
            }
        }

        return view('mobile.profil.index', [
            'user' => $user,
            'orderCounts' => $orderCounts,
        ]);
    }
}
