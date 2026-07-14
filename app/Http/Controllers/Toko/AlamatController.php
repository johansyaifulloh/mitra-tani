<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use App\Http\Requests\Toko\StoreAddressRequest;
use App\Http\Requests\Toko\UpdateAddressRequest;
use App\Services\AddressService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AlamatController extends Controller
{
    public function __construct(
        private AddressService $addressService,
    ) {}

    public function index()
    {
        //--------------------------------------------------
        // STEP 1
        // Ambil daftar alamat user
        //--------------------------------------------------
        $userId = auth('api')->id();
        $addresses = $this->addressService->listForUser($userId);

        //--------------------------------------------------
        // STEP 2
        // Tampilkan halaman alamat
        //--------------------------------------------------
        return view('mobile.alamat.index', [
            'addresses' => $addresses,
            'hasAddress' => $addresses !== [],
        ]);
    }

    public function store(StoreAddressRequest $request)
    {
        //--------------------------------------------------
        // STEP 1
        // Ambil data hasil validasi request
        //--------------------------------------------------
        $data = $request->validated();
        $data['is_default'] = $request->boolean('is_default');

        //--------------------------------------------------
        // STEP 2
        // Simpan alamat melalui service
        //--------------------------------------------------
        try {
            $this->addressService->create(auth('api')->id(), $data);
        } catch (Exception $e) {
            Log::error('AlamatController::store failed', ['message' => $e->getMessage()]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withInput()->withErrors(['address' => $e->getMessage()]);
        }

        //--------------------------------------------------
        // STEP 3
        // Kembalikan response
        //--------------------------------------------------
        $addresses = $this->addressService->listForUser(auth('api')->id());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil disimpan.',
                'data' => $addresses,
            ]);
        }

        return back()->with('success', 'Alamat berhasil disimpan.');
    }

    public function update(UpdateAddressRequest $request, int $id)
    {
        $data = $request->validated();

        if ($request->has('is_default')) {
            $data['is_default'] = $request->boolean('is_default');
        }

        try {
            $this->addressService->update(auth('api')->id(), $id, $data);
        } catch (Exception $e) {
            Log::error('AlamatController::update failed', ['message' => $e->getMessage(), 'id' => $id]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withInput()->withErrors(['address' => $e->getMessage()]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Alamat diperbarui.',
                'data' => $this->addressService->listForUser(auth('api')->id()),
            ]);
        }

        return back()->with('success', 'Alamat diperbarui.');
    }

    public function destroy(Request $request, int $id)
    {
        try {
            $this->addressService->delete(auth('api')->id(), $id);
        } catch (Exception $e) {
            Log::error('AlamatController::destroy failed', ['message' => $e->getMessage(), 'id' => $id]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withErrors(['address' => $e->getMessage()]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Alamat dihapus.',
                'data' => $this->addressService->listForUser(auth('api')->id()),
            ]);
        }

        return back()->with('success', 'Alamat dihapus.');
    }
}
