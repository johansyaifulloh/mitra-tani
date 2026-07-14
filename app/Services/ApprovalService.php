<?php

namespace App\Services;

use App\Repositories\ActivityLogRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PickupProofRepository;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ApprovalService
{
    public function __construct(
        private OrderRepository $orderRepository,
        private PickupProofRepository $pickupProofRepository,
        private ActivityLogRepository $activityLogRepository,
    ) {}

    public function paginate(int $perPage = 6): LengthAwarePaginator
    {
        return $this->orderRepository->listForApproval($perPage);
    }

    public function findByCode(string $code): ?object
    {
        return $this->orderRepository->findByCode($code);
    }

    public function verify(int $orderId, int $adminId, $photo, ?string $note = null): void
    {
        // STEP 1: Get DB reference
        $order = $this->orderRepository->findById($orderId);

        // STEP 2: Validate DB results
        if (! $order) {
            throw new Exception('Pesanan tidak ditemukan.');
        }

        if ($order->payment_status !== 'lunas') {
            throw new Exception('Pesanan belum lunas.');
        }

        if (! in_array($order->pickup_status, ['menunggu_approval', 'disetujui'], true)) {
            throw new Exception('Status pesanan tidak valid untuk verifikasi.');
        }

        // STEP 3: Business logic — store photo
        $path = $photo->store('pickup-proofs', 'public');

        // STEP 4: DB transaction
        DB::beginTransaction();

        try {
            $existing = $this->pickupProofRepository->findByOrderId($orderId);

            if (! $existing) {
                $this->pickupProofRepository->insert([
                    'order_id' => $orderId,
                    'verified_by' => $adminId,
                    'photo_path' => $path,
                    'note' => $note,
                    'verified_at' => now(),
                ]);
            }

            $this->orderRepository->update($orderId, ['pickup_status' => 'disetujui']);

            $this->activityLogRepository->insert([
                'user_id' => $adminId,
                'action' => 'approval.verify',
                'subject_type' => 'order',
                'subject_id' => $orderId,
                'description' => "Verifikasi pengambilan pesanan {$order->code}",
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            Log::error('ApprovalService::verify failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function reject(int $orderId, int $adminId, ?string $note = null): void
    {
        $order = $this->orderRepository->findById($orderId);

        if (! $order) {
            throw new Exception('Pesanan tidak ditemukan.');
        }

        if ($order->pickup_status !== 'menunggu_approval') {
            throw new Exception('Pesanan tidak dapat ditolak.');
        }

        DB::beginTransaction();

        try {
            $this->orderRepository->update($orderId, ['pickup_status' => 'ditolak']);

            $this->activityLogRepository->insert([
                'user_id' => $adminId,
                'action' => 'approval.reject',
                'subject_type' => 'order',
                'subject_id' => $orderId,
                'description' => "Penolakan pengambilan pesanan {$order->code}".($note ? ": {$note}" : ''),
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('ApprovalService::reject failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
