<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartController;

// Cart routes (can be accessed with or without auth)
Route::prefix('cart')->group(function () {
    Route::post('add', [CartController::class, 'add']);
    Route::get('cart', [CartController::class, 'index']);
    Route::post('update', [CartController::class, 'update']);
    Route::delete('item/{id}', [CartController::class, 'destroy']);
});

