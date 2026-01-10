<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AddressController;

// Protected address routes
Route::middleware('auth:sanctum')->prefix('address')->group(function () {
    Route::post('add', [AddressController::class, 'store']);
    Route::get('addresses', [AddressController::class, 'index']);
});

