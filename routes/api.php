<?php

use App\Http\Controllers\Api\AddressController as ApiAddressController;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\CartController as ApiCartController;
use App\Http\Controllers\Api\CategoryController as ApiCategoryController;
use App\Http\Controllers\Api\MidtransWebhookController;
use App\Http\Controllers\Api\OrderController as ApiOrderController;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [ApiAuthController::class, 'register']);
    Route::post('/login', [ApiAuthController::class, 'login']);
    Route::post('/refresh', [ApiAuthController::class, 'refresh']);
});

Route::get('/products', [ApiProductController::class, 'index']);
Route::get('/products/{slug}', [ApiProductController::class, 'show']);
Route::get('/categories', [ApiCategoryController::class, 'index']);

Route::post('/midtrans/notification', [MidtransWebhookController::class, 'notification']);

Route::middleware('jwt.auth')->group(function () {
    Route::post('/auth/logout', [ApiAuthController::class, 'logout']);
    Route::get('/auth/me', [ApiAuthController::class, 'me']);

    Route::get('/cart', [ApiCartController::class, 'index']);
    Route::post('/cart', [ApiCartController::class, 'store']);
    Route::put('/cart/{id}', [ApiCartController::class, 'update']);
    Route::delete('/cart/{id}', [ApiCartController::class, 'destroy']);

    Route::get('/addresses', [ApiAddressController::class, 'index']);
    Route::post('/addresses', [ApiAddressController::class, 'store']);
    Route::put('/addresses/{id}', [ApiAddressController::class, 'update']);
    Route::delete('/addresses/{id}', [ApiAddressController::class, 'destroy']);

    Route::post('/checkout', [ApiOrderController::class, 'checkout']);
});
