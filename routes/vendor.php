<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Vendor\AuthController as VendorAuthController;

// Vendor Registration and Auth (Public)
Route::prefix('vendor')->group(function () {
    Route::get('categories', [VendorAuthController::class, 'categories']); // Get categories for vendor registration
    Route::post('register', [VendorAuthController::class, 'register']);
    Route::post('send-otp', [VendorAuthController::class, 'sendOtp']);
    Route::post('verify-otp', [VendorAuthController::class, 'verifyOtp']);
    Route::post('login', [VendorAuthController::class, 'login']); // Legacy, kept for backward compatibility
    Route::post('auto-assign-salesman', [VendorAuthController::class, 'autoAssignSalesman']); // Mobile app API for auto-assignment
    Route::get('categories', [VendorAuthController::class, 'categories']); // Get categories list for vendor registration
});

