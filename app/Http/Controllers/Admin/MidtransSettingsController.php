<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestMidtransConnectionRequest;
use App\Http\Requests\Admin\UpdateMidtransSettingRequest;
use App\Services\MidtransSettingService;
use Exception;
use Illuminate\Support\Facades\Log;

class MidtransSettingsController extends Controller
{
    public function __construct(
        private MidtransSettingService $midtransSettingService,
    ) {}

    public function index()
    {
        //--------------------------------------------------
        // STEP 1
        // Tampilkan halaman pengaturan Midtrans
        //--------------------------------------------------
        return view('admin.settings.midtrans', [
            'settings' => $this->midtransSettingService->getSettingsArray(),
        ]);
    }

    public function update(UpdateMidtransSettingRequest $request)
    {
        //--------------------------------------------------
        // STEP 1
        // Ambil data hasil validasi request
        //--------------------------------------------------
        $data = $request->validated();

        //--------------------------------------------------
        // STEP 2
        // Simpan pengaturan melalui service
        //--------------------------------------------------
        try {
            $this->midtransSettingService->update($data);
        } catch (Exception $e) {
            Log::error('MidtransSettingsController::update failed', ['message' => $e->getMessage()]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withInput()->withErrors(['settings' => $e->getMessage()]);
        }

        //--------------------------------------------------
        // STEP 3
        // Kembalikan response
        //--------------------------------------------------
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan Midtrans disimpan.',
            ]);
        }

        return back()->with('success', 'Pengaturan Midtrans disimpan.');
    }

    public function testConnection(TestMidtransConnectionRequest $request)
    {
        //--------------------------------------------------
        // STEP 1
        // Ambil data hasil validasi request
        //--------------------------------------------------
        $data = $request->validated();

        //--------------------------------------------------
        // STEP 2
        // Uji koneksi ke Midtrans melalui service
        //--------------------------------------------------
        try {
            $result = $this->midtransSettingService->testConnection(
                $data['server_key'],
                $data['mode'],
            );
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        //--------------------------------------------------
        // STEP 3
        // Kembalikan JSON hasil test
        //--------------------------------------------------
        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => $result,
        ]);
    }
}
