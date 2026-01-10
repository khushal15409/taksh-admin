<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Salesman\AuthController as SalesmanAuthController;
use App\Http\Controllers\Api\Salesman\VendorVerificationController;
use App\Http\Controllers\Api\Salesman\LocationController;
use App\Http\Controllers\Api\Salesman\VendorController as SalesmanVendorController;

// Salesman Auth (Public)
Route::prefix('salesman')->group(function () {
    Route::post('send-otp', [SalesmanAuthController::class, 'sendOtp']);
    Route::post('verify-otp', [SalesmanAuthController::class, 'verifyOtp']);
    Route::post('login', [SalesmanAuthController::class, 'login']); // Legacy, kept for backward compatibility
});

// Salesman routes (require authentication and salesman role)
Route::middleware(['auth:sanctum', 'role:salesman'])->prefix('salesman')->group(function () {
    // Location management
    Route::post('location/update', [LocationController::class, 'update']);

    // Nearby vendors (auto-matched by location)
    Route::get('vendors/nearby', [SalesmanVendorController::class, 'nearby']);

    // Vendor verification
    Route::post('vendor/{vendor_id}/verify', [VendorVerificationController::class, 'verify']);
});

// Nearby vendors route (alternative path: /api/vendors/nearby)
Route::middleware(['auth:sanctum', 'role:salesman'])->get('vendors/nearby', [SalesmanVendorController::class, 'nearby']);

