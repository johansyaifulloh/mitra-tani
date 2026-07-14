<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LaporanService
{
    public function __construct(
        private OrderRepository $orderRepository,
    ) {}

    public function paginate(?string $dateFrom, ?string $dateTo, int $perPage = 10): LengthAwarePaginator
    {
        return $this->orderRepository->paginateFiltered($perPage, $dateFrom, $dateTo);
    }

    public function summary(?string $dateFrom, ?string $dateTo): array
    {
        $revenue = $this->orderRepository->sumRevenue($dateFrom, $dateTo);
        $orders = $this->orderRepository->paginateFiltered(1, $dateFrom, $dateTo);

        return [
            'total_revenue' => $revenue,
            'total_orders' => $orders->total(),
        ];
    }
}
