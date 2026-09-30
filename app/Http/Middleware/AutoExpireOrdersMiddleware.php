<?php

namespace App\Http\Middleware;

use App\Services\OrderService;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AutoExpireOrdersMiddleware
{
    public function __construct(
        private OrderService $orderService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Otomatis batalkan pesanan kadaluarsa di background saat website diakses
        // Dilindungi cache 30 detik agar query sangat ringan dan tidak membebani server
        try {
            $lockKey = 'auto_expire_orders_lock';
            if (! Cache::has($lockKey)) {
                Cache::put($lockKey, true, now()->addSeconds(30));
                $this->orderService->expireOverdueOrders();
            }
        } catch (Exception $e) {
            Log::warning('AutoExpireOrdersMiddleware error: ' . $e->getMessage());
        }

        return $next($request);
    }
}
