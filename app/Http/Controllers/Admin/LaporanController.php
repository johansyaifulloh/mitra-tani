<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LaporanService;

class LaporanController extends Controller
{
    public function __construct(
        private LaporanService $laporanService,
    ) {}

    public function index()
    {
        $dateFrom = request('date_from');
        $dateTo = request('date_to');

        $transactions = $this->laporanService->paginateTransactions($dateFrom, $dateTo, 10);
        $summary = $this->laporanService->summary($dateFrom, $dateTo);

        return view('admin.laporan.index', compact('transactions', 'summary', 'dateFrom', 'dateTo'));
    }

    public function exportPdf()
    {
        $dateFrom = request('date_from');
        $dateTo = request('date_to');

        $data = $this->laporanService->exportData($dateFrom, $dateTo);
        $data['userRole'] = 'Admin';
        $data['userName'] = auth('api')->user()?->name ?? 'Admin Mantri Tani';

        return view('admin.laporan.pdf', $data);
    }
}
