<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LaporanService;
use App\Support\FormatHelper;
use App\Support\SampleData;

class LaporanController extends Controller
{
    public function __construct(
        private LaporanService $laporanService,
    ) {}

    public function index()
    {
        $dateFrom = request('date_from');
        $dateTo = request('date_to');

        $paginator = $this->laporanService->paginate($dateFrom, $dateTo, 10);
        $transactions = $paginator->through(function ($row) {
            $item = FormatHelper::orderForAdmin($row);
            $item['status'] = $row->pickup_status ?? $row->payment_status;
            $item['status_label'] = SampleData::statusLabel($item['status']);

            return $item;
        });

        $summary = $this->laporanService->summary($dateFrom, $dateTo);

        return view('admin.laporan.index', compact('transactions', 'summary', 'dateFrom', 'dateTo'));
    }
}
