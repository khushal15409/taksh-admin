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
use App\Http\Controllers\Api\Vendor\AuthController as VendorAuthController;
use App\Http\Controllers\Api\Salesman\AuthController as SalesmanAuthController;
use App\Http\Controllers\Api\Salesman\VendorVerificationController;
use App\Http\Controllers\Api\Salesman\LocationController;
use App\Http\Controllers\Api\Salesman\VendorController as SalesmanVendorController;
use App\Http\Controllers\Admin\VendorApprovalController;
use App\Http\Controllers\Admin\VendorAssignmentController;

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

// Protected routes
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

// Admin API routes (require authentication and super-admin role)
Route::middleware(['auth:sanctum', 'role:super-admin'])->prefix('admin')->group(function () {
    Route::post('vendor/{vendor_id}/assign-salesman', [VendorAssignmentController::class, 'assignSalesman']);
    Route::post('vendor/{vendor_id}/auto-assign-salesman', [VendorAssignmentController::class, 'autoAssignSalesman']);
    Route::post('vendor/{vendor_id}/approve', [VendorApprovalController::class, 'approve']);
    Route::post('vendor/{vendor_id}/reject', [VendorApprovalController::class, 'reject']);
});
