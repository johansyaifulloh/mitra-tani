<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use App\Http\Requests\Toko\CheckoutRequest;
use App\Services\AddressService;
use App\Services\CartService;
use App\Services\OrderService;
use Exception;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct(
        private AddressService $addressService,
        private CartService $cartService,
        private OrderService $orderService,
    ) {}

    public function index()
    {
        //--------------------------------------------------
        // STEP 1
        // Ambil ringkasan keranjang & alamat user
        //--------------------------------------------------
        $userId = auth('api')->id();

        try {
            $summary = $this->cartService->summaryForCheckout($userId);
        } catch (Exception $e) {
            return redirect()
                ->route('toko.keranjang.index')
                ->with('info', $e->getMessage());
        }

        $hasAddress = $this->addressService->hasAny($userId);

        //--------------------------------------------------
        // STEP 2
        // Tampilkan halaman checkout
        //--------------------------------------------------
        return view('mobile.checkout.index', [
            'addresses' => $this->addressService->listForUser($userId),
            'hasAddress' => $hasAddress,
            'summary' => $summary,
        ]);
    }

    public function store(CheckoutRequest $request)
    {
        //--------------------------------------------------
        // STEP 1
        // Validasi alamat tersedia sebelum buat pesanan
        //--------------------------------------------------
        $userId = auth('api')->id();

        if (! $this->addressService->hasAny($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat belum diisi. Silakan tambahkan alamat terlebih dahulu.',
            ], 422);
        }

        //--------------------------------------------------
        // STEP 2
        // Buat pesanan melalui service
        //--------------------------------------------------
        try {
            $order = $this->orderService->createFromCheckout(
                $userId,
                $request->validated(),
            );
        } catch (Exception $e) {
            Log::error('CheckoutController::store failed', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        //--------------------------------------------------
        // STEP 3
        // Kembalikan JSON untuk AJAX
        //--------------------------------------------------
        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat.',
            'data' => [
                'redirect_url' => route('toko.pembayaran.show', $order->code),
            ],
        ]);
    }
}
