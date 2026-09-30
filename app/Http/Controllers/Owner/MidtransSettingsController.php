<?php

namespace App\Http\Controllers\Owner;

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
        return view('owner.settings.midtrans', [
            'settings' => $this->midtransSettingService->getSettingsArray(),
        ]);
    }

    public function update(UpdateMidtransSettingRequest $request)
    {
        $data = $request->validated();

        try {
            $this->midtransSettingService->update($data);
        } catch (Exception $e) {
            Log::error('Owner\MidtransSettingsController::update failed', ['message' => $e->getMessage()]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withInput()->withErrors(['settings' => $e->getMessage()]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan Midtrans berhasil disimpan.',
            ]);
        }

        return back()->with('success', 'Pengaturan Midtrans berhasil disimpan.');
    }

    public function testConnection(TestMidtransConnectionRequest $request)
    {
        $data = $request->validated();

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

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => $result,
        ]);
    }
}
