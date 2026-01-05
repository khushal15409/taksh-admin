<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReturnController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\Express30Controller;

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('send-otp', [AuthController::class, 'sendOtp']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
});

Route::get('dashboard', [DashboardController::class, 'index']);
Route::get('categories', [ProductController::class, 'categories']);
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{id}', [ProductController::class, 'show']);
Route::get('banners', [BannerController::class, 'index']);

// Express 30 Delivery routes
Route::prefix('express-30')->group(function () {
    Route::get('products', [Express30Controller::class, 'products']);
});

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });

    Route::prefix('address')->group(function () {
        Route::post('add', [AddressController::class, 'store']);
        Route::get('addresses', [AddressController::class, 'index']);
    });

    Route::prefix('order')->group(function () {
        Route::post('place', [OrderController::class, 'store']);
        Route::get('orders', [OrderController::class, 'index']);
        Route::get('orders/{id}', [OrderController::class, 'show']);
    });

    Route::prefix('payment')->group(function () {
        Route::post('initiate', [PaymentController::class, 'initiate']);
        Route::post('verify', [PaymentController::class, 'verify']);
    });

    Route::prefix('return')->group(function () {
        Route::post('request', [ReturnController::class, 'store']);
    });

    Route::prefix('express-30')->group(function () {
        Route::post('order', [Express30Controller::class, 'placeOrder']);
    });
});

// Cart routes (can be accessed with or without auth)
Route::prefix('cart')->group(function () {
    Route::post('add', [CartController::class, 'add']);
    Route::get('cart', [CartController::class, 'index']);
    Route::post('update', [CartController::class, 'update']);
    Route::delete('item/{id}', [CartController::class, 'destroy']);
});
