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
use App\Http\Controllers\Admin\MidtransSettingsController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\LaporanController as OwnerLaporanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProdukController::class, 'index'])->name('home');
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

        Route::get('/pengaturan/midtrans', [MidtransSettingsController::class, 'index'])->name('settings.midtrans');
        Route::post('/pengaturan/midtrans', [MidtransSettingsController::class, 'update'])->name('settings.midtrans.update');
        Route::post('/pengaturan/midtrans/test', [MidtransSettingsController::class, 'testConnection'])->name('settings.midtrans.test');
    });
});

Route::prefix('owner')->name('owner.')->middleware(['jwt.web:admin', 'role:owner'])->group(function () {
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/laporan', [OwnerLaporanController::class, 'index'])->name('laporan.index');
});
