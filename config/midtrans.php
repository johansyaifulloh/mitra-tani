<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Nilai default diambil dari .env. Admin masih bisa menimpanya lewat
    | halaman Pengaturan Midtrans (tersimpan di tabel midtrans_settings).
    | Ubah MIDTRANS_MODE ke "production" saat go-live.
    |
    */

    'mode' => env('MIDTRANS_MODE', 'sandbox'),

    'merchant_id' => env('MIDTRANS_MERCHANT_ID', ''),

    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),

    'server_key' => env('MIDTRANS_SERVER_KEY', ''),

    'is_production' => env('MIDTRANS_MODE', 'sandbox') === 'production',

];
