<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwalkan pembatalan pesanan yang telah melewati batas waktu 24 jam setiap 5 menit
Schedule::command('orders:expire')->everyFiveMinutes();
