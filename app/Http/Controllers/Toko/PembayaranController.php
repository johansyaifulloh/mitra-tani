<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use App\Services\MidtransSettingService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private MidtransSettingService $midtransSettingService,
    ) {}

    public function show(string $order)
    {
        $detail = $this->orderService->detailForPayment($order, auth('api')->id());

        if (! $detail) {
            abort(404);
        }

        $settings = $this->midtransSettingService->getActiveSettings();

        return view('mobile.pembayaran.index', [
            'order' => $detail,
            'clientKey' => $settings->client_key ?? '',
            'isProduction' => ($settings->mode ?? 'sandbox') === 'production',
        ]);
    }

    public function snap(string $order)
    {
        $detail = $this->orderService->detailForPayment($order, auth('api')->id());

        if (! $detail) {
            abort(404);
        }

        return response()->json(['snap_token' => $detail['snap_token']]);
    }

    public function syncStatus(Request $request, string $order)
    {
        //--------------------------------------------------
        // STEP 1
        // Ambil hasil dari popup Snap (metode & status)
        //--------------------------------------------------
        $result = $request->only(['payment_type', 'transaction_status', 'transaction_id']);

        //--------------------------------------------------
        // STEP 2
        // Simpan metode terpilih ke pesanan milik user
        //--------------------------------------------------
        $this->orderService->syncFromSnapResult($order, auth('api')->id(), $result);

        return response()->json(['success' => true]);
    }
}
