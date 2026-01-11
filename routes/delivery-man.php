<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeliveryMan\AuthController as DeliveryManAuthController;

// Delivery Man Auth (Public - Mobile App)
Route::prefix('delivery-man')->group(function () {
    Route::post('send-otp', [DeliveryManAuthController::class, 'sendOtp']);
    Route::post('verify-otp', [DeliveryManAuthController::class, 'verifyOtp']);
    Route::post('register', [DeliveryManAuthController::class, 'register']);
    Route::post('login', [DeliveryManAuthController::class, 'login']);
});

// Backward compatibility - keep old routes as aliases
Route::prefix('delivery-boy')->group(function () {
    Route::post('send-otp', [DeliveryManAuthController::class, 'sendOtp']);
    Route::post('verify-otp', [DeliveryManAuthController::class, 'verifyOtp']);
    Route::post('register', [DeliveryManAuthController::class, 'register']);
    Route::post('login', [DeliveryManAuthController::class, 'login']);
});

// Protected routes for delivery man (if any, after login)
Route::middleware('auth:sanctum')->prefix('delivery-man')->group(function () {
    // Add any routes that require authentication here
    // Example: Route::get('profile', [DeliveryManAuthController::class, 'profile']);
});
