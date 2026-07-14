<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AddressService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct(
        private AddressService $addressService,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->addressService->listForUser(auth('api')->id()));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'street' => ['required', 'string'],
            'district' => ['required', 'string'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        try {
            $id = $this->addressService->create(auth('api')->id(), $data);

            return response()->json(['id' => $id, 'message' => 'Alamat disimpan.'], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'label' => ['sometimes', 'string', 'max:50'],
            'recipient_name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'string', 'max:20'],
            'street' => ['sometimes', 'string'],
            'district' => ['sometimes', 'string'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        try {
            $this->addressService->update(auth('api')->id(), $id, $data);

            return response()->json(['message' => 'Alamat diperbarui.']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->addressService->delete(auth('api')->id(), $id);

            return response()->json(['message' => 'Alamat dihapus.']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
