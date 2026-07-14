<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MidtransService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtransWebhookController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private MidtransService $midtransService,
    ) {}

    public function notification(Request $request): JsonResponse
    {
        // STEP 1: Ambil payload & signature (Midtrans kirim signature_key di body)
        $payload = $request->all();
        $signature = $payload['signature_key'] ?? '';

        // STEP 2: Verifikasi signature agar notifikasi benar dari Midtrans
        if (! $signature || ! $this->midtransService->verifySignature($payload, $signature)) {
            return response()->json(['message' => 'Invalid signature.'], 403);
        }

        // STEP 3: Update status pesanan sesuai notifikasi
        $this->orderService->handleNotification($payload);

        return response()->json(['message' => 'OK']);
    }
}
