<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function __construct(
        private OrderService $orderService,
    ) {}

    public function index(Request $request)
    {
        //--------------------------------------------------
        // STEP 1
        // Ambil filter status dari query (opsional)
        //--------------------------------------------------
        $status = $request->query('status');

        //--------------------------------------------------
        // STEP 2
        // Segarkan status pesanan yang belum dibayar dari Midtrans
        //--------------------------------------------------
        $userId = auth('api')->id();
        $this->orderService->refreshPendingForUser($userId);

        //--------------------------------------------------
        // STEP 3
        // Ambil daftar transaksi milik user yang login
        //--------------------------------------------------
        $data = $this->orderService->listForUser($userId, $status);

        //--------------------------------------------------
        // STEP 3
        // Tampilkan halaman transaksi
        //--------------------------------------------------
        return view('mobile.transaksi.index', $data);
    }
}
