<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/auth/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/shops', [ShopController::class, 'index']);
    Route::get('/shops/{id}', [ShopController::class, 'show']);
    Route::get('/shops/{shopId}/products', [ProductController::class, 'byShop']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/profile', [AuthController::class, 'updateProfile']);

        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart/items', [CartController::class, 'store']);
        Route::put('/cart/items/{productId}', [CartController::class, 'update']);
        Route::delete('/cart/items/{productId}', [CartController::class, 'destroy']);

        Route::post('/addresses/from-coordinates', [AddressController::class, 'fromCoordinates']);
        Route::resource('/addresses', AddressController::class, ['only' => ['index', 'store', 'update', 'destroy']]);
        Route::post('/addresses/{address}/set-default', [AddressController::class, 'setDefault']);

        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::get('/orders/{id}/tracking', [OrderController::class, 'tracking']);
        Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    });
});

