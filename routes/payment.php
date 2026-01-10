<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;

// Protected payment routes
Route::middleware('auth:sanctum')->prefix('payment')->group(function () {
    Route::post('initiate', [PaymentController::class, 'initiate']);
    Route::post('verify', [PaymentController::class, 'verify']);
});

