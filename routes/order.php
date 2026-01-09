<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;

// Protected order routes
Route::middleware('auth:sanctum')->prefix('order')->group(function () {
    Route::post('place', [OrderController::class, 'store']);
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{id}', [OrderController::class, 'show']);
});

