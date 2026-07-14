<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\OrderItemRepository;
use App\Repositories\PickupProofRepository;
use App\Services\ApprovalService;
use App\Support\FormatHelper;
use App\Support\SampleData;
use Exception;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function __construct(
        private ApprovalService $approvalService,
        private \App\Repositories\PickupProofRepository $pickupProofRepository,
        private OrderItemRepository $orderItemRepository,
    ) {}

    public function index()
    {
        $paginator = $this->approvalService->paginate(6);
        $transactions = $paginator->through(function ($row) {
            $item = FormatHelper::orderForAdmin($row);
            $proof = $this->pickupProofRepository->findByOrderId($row->id);

            if ($proof) {
                $item['pickup_proof'] = [
                    'verified_at' => date('d/m/Y H:i', strtotime($proof->verified_at)),
                    'verified_by' => $proof->verified_by_name,
                    'note' => $proof->note ?? '',
                ];
            }

            $item['status'] = $row->pickup_status ?? $row->payment_status;

            return $item;
        });

        $codeInput = request('code');
        $selected = null;
        $codeError = false;

        if ($codeInput) {
            $normalized = strtoupper(trim(str_replace('#', '', $codeInput)));
            if (! str_starts_with($normalized, 'TRX-')) {
                $normalized = 'TRX-'.$normalized;
            }
            $orderRow = $this->approvalService->findByCode($normalized);
            $selected = $orderRow ? $this->buildSelected($orderRow) : null;
            $codeError = ! $selected;
        } elseif (request('order')) {
            $orderRow = $this->approvalService->findByCode(request('order'));
            $selected = $orderRow ? $this->buildSelected($orderRow) : null;
        }

        return view('admin.approval.index', compact('transactions', 'selected', 'codeInput', 'codeError'));
    }

    private function buildSelected(object $orderRow): array
    {
        $selected = FormatHelper::orderForAdmin($orderRow);

        $selected['items'] = $this->orderItemRepository->listForOrder($orderRow->id)
            ->map(fn ($item) => [
                'name' => $item->product_name,
                'quantity' => (int) $item->quantity,
                'subtotal_label' => FormatHelper::rupiah($item->subtotal),
            ])
            ->all();

        $proof = $this->pickupProofRepository->findByOrderId($orderRow->id);

        if ($proof) {
            $selected['pickup_proof'] = [
                'verified_at' => date('d/m/Y H:i', strtotime($proof->verified_at)),
                'verified_by' => $proof->verified_by_name,
                'note' => $proof->note ?? '',
                'photo' => $proof->photo_path ? asset('storage/'.$proof->photo_path) : null,
            ];
        }

        return $selected;
    }

    public function verify(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required', 'integer'],
            'photo' => ['required', 'image', 'max:4096'],
            'note' => ['nullable', 'string'],
        ]);

        try {
            $this->approvalService->verify(
                (int) $data['order_id'],
                auth('api')->id(),
                $request->file('photo'),
                $data['note'] ?? null,
            );

            return back()->with('success', 'Pengambilan barang berhasil diverifikasi.');
        } catch (Exception $e) {
            return back()->withErrors(['approval' => $e->getMessage()]);
        }
    }

    public function reject(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required', 'integer'],
            'note' => ['nullable', 'string'],
        ]);

        try {
            $this->approvalService->reject(
                (int) $data['order_id'],
                auth('api')->id(),
                $data['note'] ?? null,
            );

            return back()->with('success', 'Pengambilan barang ditolak.');
        } catch (Exception $e) {
            return back()->withErrors(['approval' => $e->getMessage()]);
        }
    }
}
