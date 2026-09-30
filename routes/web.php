<?php

use App\Http\Controllers\Toko\AlamatController;
use App\Http\Controllers\Toko\AuthController as TokoAuthController;
use App\Http\Controllers\Toko\CheckoutController;
use App\Http\Controllers\Toko\KeranjangController;
use App\Http\Controllers\Toko\KategoriController;
use App\Http\Controllers\Toko\OnboardingController;
use App\Http\Controllers\Toko\PembayaranController;
use App\Http\Controllers\Toko\ProfilController;
use App\Http\Controllers\Toko\ProdukController;
use App\Http\Controllers\Toko\TransaksiController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\KategoriController as AdminKategoriController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\ProdukController as AdminProdukController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\LaporanController as OwnerLaporanController;
use App\Http\Controllers\Owner\MidtransSettingsController as OwnerMidtransSettingsController;
use App\Http\Controllers\Owner\UserController as OwnerUserController;
use Illuminate\Support\Facades\Route;

// Route khusus untuk melayani foto bukti pengambilan asli secara langsung & andal
Route::get('/bukti-foto/{filename}', function (string $filename) {
    $clean = basename($filename);
    $paths = [
        storage_path('app/public/pickup-proofs/'.$clean),
        storage_path('app/public/'.$clean),
        public_path('storage/pickup-proofs/'.$clean),
        public_path('pickup-proofs/'.$clean),
    ];

    foreach ($paths as $filePath) {
        if (file_exists($filePath) && ! is_dir($filePath)) {
            // Mirror ke public/storage jika belum ada
            $publicTarget = public_path('storage/pickup-proofs/'.$clean);
            if (! file_exists($publicTarget)) {
                $dir = dirname($publicTarget);
                if (! is_dir($dir)) {
                    @mkdir($dir, 0755, true);
                }
                @copy($filePath, $publicTarget);
            }

            $mime = mime_content_type($filePath) ?: 'image/jpeg';

            return response()->file($filePath, [
                'Content-Type' => $mime,
                'Cache-Control' => 'no-cache, private',
            ]);
        }
    }

    // Fallback elegan jika file fisik memang belum pernah diupload / dummy sample seeder
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="360" viewBox="0 0 600 360" fill="#f8fafc">'
        .'<rect width="100%" height="100%" rx="16" fill="#f8fafc" stroke="#e2e8f0" stroke-width="2"/>'
        .'<circle cx="300" cy="150" r="45" fill="#ecfdf5"/>'
        .'<path d="M285 150 L295 160 L318 137" stroke="#10b981" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>'
        .'<text x="300" y="225" text-anchor="middle" font-family="sans-serif" font-size="18" font-weight="700" fill="#0f172a">Foto Bukti Pengambilan</text>'
        .'<text x="300" y="252" text-anchor="middle" font-family="sans-serif" font-size="13" fill="#64748b">Mitra Tani Selorejo</text>'
        .'</svg>';

    return response($svg, 200, ['Content-Type' => 'image/svg+xml']);
})->name('bukti-foto');

// Route untuk melayani file storage publik lainnya
Route::get('/storage/{path}', function (string $path) {
    $fullPath = storage_path('app/public/'.$path);
    if (file_exists($fullPath) && ! is_dir($fullPath)) {
        return response()->file($fullPath);
    }

    if (str_starts_with($path, 'pickup-proofs/')) {
        return redirect()->route('bukti-foto', ['filename' => basename($path)]);
    }

    abort(404);
})->where('path', '.*')->name('storage.file');

Route::get('/', [ProdukController::class, 'index'])->name('home');
Route::get('/hub', fn () => view('welcome'))->name('hub');
Route::get('/toko/produk', [ProdukController::class, 'index'])->name('toko.produk.index');
Route::get('/toko/produk/data', [ProdukController::class, 'fetchData'])->name('toko.produk.data');

Route::prefix('toko')->name('toko.')->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding');
    Route::get('/produk/{slug}', [ProdukController::class, 'show'])->name('produk.show');
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::get('/login', [TokoAuthController::class, 'login'])->name('login');
    Route::post('/login', [TokoAuthController::class, 'loginSubmit'])->name('login.submit');
    Route::post('/logout', [TokoAuthController::class, 'logout'])->name('logout');
    Route::get('/register', [TokoAuthController::class, 'register'])->name('register');
    Route::post('/register', [TokoAuthController::class, 'registerStore'])->name('register.store');
    Route::get('/lupa-password', [TokoAuthController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('/lupa-password', [TokoAuthController::class, 'forgotPasswordSubmit'])->name('forgot-password.submit');

    Route::middleware('jwt.web')->group(function () {
        Route::get('/alamat', [AlamatController::class, 'index'])->name('alamat.index');
        Route::post('/alamat', [AlamatController::class, 'store'])->name('alamat.store');
        Route::put('/alamat/{id}', [AlamatController::class, 'update'])->name('alamat.update');
        Route::delete('/alamat/{id}', [AlamatController::class, 'destroy'])->name('alamat.destroy');

        Route::post('/keranjang', [KeranjangController::class, 'store'])->name('keranjang.store');
        Route::get('/keranjang/data', [KeranjangController::class, 'fetchData'])->name('keranjang.data');
        Route::get('/keranjang/count', [KeranjangController::class, 'count'])->name('keranjang.count');
        Route::post('/keranjang/select-all', [KeranjangController::class, 'selectAll'])->name('keranjang.select-all');
        Route::put('/keranjang/{id}', [KeranjangController::class, 'update'])->name('keranjang.update');
        Route::delete('/keranjang/{id}', [KeranjangController::class, 'destroy'])->name('keranjang.destroy');

        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

        Route::get('/pembayaran/{order}', [PembayaranController::class, 'show'])->name('pembayaran.show');
        Route::post('/pembayaran/{order}/snap', [PembayaranController::class, 'snap'])->name('pembayaran.snap');
        Route::post('/pembayaran/{order}/sync', [PembayaranController::class, 'syncStatus'])->name('pembayaran.sync');

        Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    });
});

// Panel Admin (Operasional: Produk, Kategori, Approval, Laporan)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'login'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'loginSubmit'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware(['jwt.web:admin', 'role:admin,owner'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/produk', [AdminProdukController::class, 'index'])->name('produk.index');
        Route::get('/produk/data', [AdminProdukController::class, 'fetchData'])->name('produk.data');
        Route::get('/produk/tambah', [AdminProdukController::class, 'create'])->name('produk.create');
        Route::post('/produk', [AdminProdukController::class, 'store'])->name('produk.store');
        Route::get('/produk/{id}/edit', [AdminProdukController::class, 'edit'])->name('produk.edit');
        Route::put('/produk/{id}', [AdminProdukController::class, 'update'])->name('produk.update');
        Route::post('/produk/{id}/toggle-status', [AdminProdukController::class, 'toggleStatus'])->name('produk.toggle-status');
        Route::post('/produk/{id}/archive', [AdminProdukController::class, 'archive'])->name('produk.archive');
        Route::delete('/produk/{id}', [AdminProdukController::class, 'destroy'])->name('produk.destroy');

        Route::get('/kategori', [AdminKategoriController::class, 'index'])->name('kategori.index');
        Route::get('/kategori/data', [AdminKategoriController::class, 'fetchData'])->name('kategori.data');
        Route::get('/kategori/tambah', [AdminKategoriController::class, 'create'])->name('kategori.create');
        Route::post('/kategori', [AdminKategoriController::class, 'store'])->name('kategori.store');
        Route::get('/kategori/{id}/edit', [AdminKategoriController::class, 'edit'])->name('kategori.edit');
        Route::put('/kategori/{id}', [AdminKategoriController::class, 'update'])->name('kategori.update');
        Route::post('/kategori/{id}/toggle-status', [AdminKategoriController::class, 'toggleStatus'])->name('kategori.toggle-status');
        Route::delete('/kategori/{id}', [AdminKategoriController::class, 'destroy'])->name('kategori.destroy');

        Route::get('/approval', [ApprovalController::class, 'index'])->name('approval.index');
        Route::post('/approval/verify', [ApprovalController::class, 'verify'])->name('approval.verify');
        Route::post('/approval/reject', [ApprovalController::class, 'reject'])->name('approval.reject');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.pdf');
    });
});

// Panel Owner (Full Akses: Dashboard Bisnis, Manajemen Akun Admin/Owner, Midtrans Settings, Laporan)
Route::prefix('owner')->name('owner.')->middleware(['jwt.web:admin', 'role:owner'])->group(function () {
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/laporan', [OwnerLaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/pdf', [OwnerLaporanController::class, 'exportPdf'])->name('laporan.pdf');

    // Manajemen Akun Admin & Owner
    Route::get('/users', [OwnerUserController::class, 'index'])->name('users.index');
    Route::get('/users/tambah', [OwnerUserController::class, 'create'])->name('users.create');
    Route::post('/users', [OwnerUserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [OwnerUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [OwnerUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [OwnerUserController::class, 'destroy'])->name('users.destroy');

    // Pengaturan Midtrans Payment Gateway
    Route::get('/pengaturan/midtrans', [OwnerMidtransSettingsController::class, 'index'])->name('settings.midtrans');
    Route::post('/pengaturan/midtrans', [OwnerMidtransSettingsController::class, 'update'])->name('settings.midtrans.update');
    Route::post('/pengaturan/midtrans/test', [OwnerMidtransSettingsController::class, 'testConnection'])->name('settings.midtrans.test');
});
