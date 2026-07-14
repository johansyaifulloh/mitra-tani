<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use App\Http\Requests\Toko\StoreCartItemRequest;
use App\Http\Requests\Toko\UpdateCartItemRequest;
use App\Services\AddressService;
use App\Services\CartService;
use App\Services\ProductService;
use App\Support\JwtCookie;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class KeranjangController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private ProductService $productService,
        private AddressService $addressService,
    ) {}

    public function index(Request $request)
    {
        //--------------------------------------------------
        // STEP 1
        // Cek status login dari JWT cookie
        //--------------------------------------------------
        $loggedIn = false;
        $userId = null;

        if ($request->cookie(JwtCookie::ACCESS)) {
            try {
                $user = JWTAuth::setToken($request->cookie(JwtCookie::ACCESS))->authenticate();

                // Keranjang toko hanya untuk pelanggan.
                if ($user && $user->role === 'customer') {
                    auth('api')->setUser($user);
                    $loggedIn = true;
                    $userId = $user->id;
                }
            } catch (Exception $e) {
                // tampilan keranjang tamu
            }
        }

        if (!$loggedIn) {
            return redirect()->route('toko.login', ['redirect' => 'toko.keranjang.index']);
        }

        $hasAddress = $this->addressService->hasAny($userId);

        //--------------------------------------------------
        // STEP 2
        // Tampilkan halaman keranjang (data dimuat via AJAX)
        //--------------------------------------------------
        return view('mobile.keranjang.index', [
            'checkoutRoute' => route('toko.checkout.index'),
            'alamatUrl' => route('toko.alamat.index'),
            'loginUrl' => route('toko.login', ['redirect' => 'checkout']),
            'loggedIn' => $loggedIn,
            'hasAddress' => $hasAddress,
            'products' => $this->productService->listActive(),
        ]);
    }

    public function fetchData()
    {
        //--------------------------------------------------
        // STEP 1
        // Ambil data keranjang user dari service
        //--------------------------------------------------
        try {
            $userId = auth('api')->id();
            $payload = $this->cartService->fetchForUser($userId);
            $hasAddress = $this->addressService->hasAny($userId);

            //--------------------------------------------------
            // STEP 2
            // Kembalikan JSON untuk AJAX
            //--------------------------------------------------
            return response()->json([
                'success' => true,
                'data' => $payload['items'],
                'meta' => array_merge($payload['meta'], [
                    'has_address' => $hasAddress,
                ]),
            ]);
        } catch (Exception $e) {
            Log::error('KeranjangController::fetchData failed', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(StoreCartItemRequest $request)
    {
        //--------------------------------------------------
        // STEP 1
        // Ambil data hasil validasi request
        //--------------------------------------------------
        $data = $request->validated();

        //--------------------------------------------------
        // STEP 2
        // Tambah / update item keranjang
        //--------------------------------------------------
        try {
            $this->cartService->addOrUpdate(
                auth('api')->id(),
                (int) $data['product_id'],
                (int) $data['quantity'],
            );
        } catch (Exception $e) {
            Log::error('KeranjangController::store failed', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        //--------------------------------------------------
        // STEP 3
        // Kembalikan JSON + jumlah item terpilih (untuk badge)
        //--------------------------------------------------
        return response()->json([
            'success' => true,
            'message' => 'Produk ditambahkan ke keranjang.',
            'meta' => [
                'cart_count' => $this->cartService->selectedCount(auth('api')->id()),
            ],
        ]);
    }

    public function count()
    {
        //--------------------------------------------------
        // STEP 1
        // Kembalikan jumlah item tercentang untuk badge header
        //--------------------------------------------------
        return response()->json([
            'success' => true,
            'meta' => [
                'cart_count' => $this->cartService->selectedCount(auth('api')->id()),
            ],
        ]);
    }

    public function update(UpdateCartItemRequest $request, int $id)
    {
        try {
            if ($request->has('is_selected')) {
                $this->cartService->toggleSelection(
                    auth('api')->id(),
                    $id,
                    $request->boolean('is_selected'),
                );
            } else {
                $this->cartService->updateQuantity(
                    auth('api')->id(),
                    $id,
                    (int) $request->quantity,
                );
            }

            $payload = $this->cartService->fetchForUser(auth('api')->id());

            return response()->json([
                'success' => true,
                'message' => 'Keranjang diperbarui.',
                'data' => $payload['items'],
                'meta' => array_merge($payload['meta'], [
                    'has_address' => $this->addressService->hasAny(auth('api')->id()),
                ]),
            ]);
        } catch (Exception $e) {
            Log::error('KeranjangController::update failed', ['message' => $e->getMessage(), 'id' => $id]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->cartService->remove(auth('api')->id(), $id);

            $payload = $this->cartService->fetchForUser(auth('api')->id());

            return response()->json([
                'success' => true,
                'message' => 'Item dihapus dari keranjang.',
                'data' => $payload['items'],
                'meta' => array_merge($payload['meta'], [
                    'has_address' => $this->addressService->hasAny(auth('api')->id()),
                ]),
            ]);
        } catch (Exception $e) {
            Log::error('KeranjangController::destroy failed', ['message' => $e->getMessage(), 'id' => $id]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function selectAll(Request $request)
    {
        $request->validate(['is_selected' => ['required', 'boolean']]);

        try {
            $this->cartService->setSelectAll(auth('api')->id(), $request->boolean('is_selected'));

            $payload = $this->cartService->fetchForUser(auth('api')->id());

            return response()->json([
                'success' => true,
                'message' => 'Pilihan keranjang diperbarui.',
                'data' => $payload['items'],
                'meta' => array_merge($payload['meta'], [
                    'has_address' => $this->addressService->hasAny(auth('api')->id()),
                ]),
            ]);
        } catch (Exception $e) {
            Log::error('KeranjangController::selectAll failed', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
