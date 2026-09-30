<?php

namespace App\Console\Commands;

use App\Services\OrderService;
use Illuminate\Console\Command;

class ExpireOverdueOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batalkan pesanan yang telah melewati batas waktu pembayaran (24 jam) dan kembalikan stok produk';

    /**
     * Execute the console command.
     */
    public function handle(OrderService $orderService): int
    {
        $this->info('Memeriksa pesanan yang kadaluarsa...');

        $expiredCount = $orderService->expireOverdueOrders();

        if ($expiredCount > 0) {
            $this->info("Berhasil membatalkan {$expiredCount} pesanan kadaluarsa dan mengembalikan stok produk.");
        } else {
            $this->info('Tidak ada pesanan pending yang melewati batas waktu.');
        }

        return Command::SUCCESS;
    }
}
