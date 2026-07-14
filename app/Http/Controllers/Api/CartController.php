<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService,
    ) {}

    public function index(): JsonResponse
    {
        $items = $this->cartService->listForUser(auth('api')->id());

        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $this->cartService->addOrUpdate(
                auth('api')->id(),
                (int) $request->product_id,
                (int) $request->quantity,
            );

            return response()->json(['message' => 'Keranjang diperbarui.']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate(['quantity' => ['required', 'integer', 'min:0']]);

        try {
            $this->cartService->updateQuantity(auth('api')->id(), $id, (int) $request->quantity);

            return response()->json(['message' => 'Keranjang diperbarui.']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->cartService->remove(auth('api')->id(), $id);

            return response()->json(['message' => 'Item dihapus.']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
