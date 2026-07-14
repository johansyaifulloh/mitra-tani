<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Support\FormatHelper;
use App\Support\SampleData;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService,
    ) {}

    public function index()
    {
        $paginator = $this->dashboardService->recentTransactions(5);
        $transactions = $paginator->through(function ($row) {
            $item = FormatHelper::orderForAdmin($row);
            $item['status'] = $row->pickup_status ?? $row->payment_status;

            return $item;
        });

        return view('admin.dashboard', [
            'transactions' => $transactions,
            'stats' => $this->dashboardService->stats(),
            'chart' => $this->dashboardService->revenueChart(),
            'topProducts' => $this->dashboardService->topProducts(),
            'categories' => $this->dashboardService->categoryStats(),
            'statusSummary' => $this->dashboardService->statusSummary(),
            'activities' => $this->dashboardService->recentActivity(),
        ]);
    }
}
