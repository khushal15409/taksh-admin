<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Express30Controller;

// Express 30 Delivery routes (Public)
Route::prefix('express-30')->group(function () {
    Route::post('products', [Express30Controller::class, 'products']);
});

// Express 30 Delivery routes (Protected)
Route::middleware('auth:sanctum')->prefix('express-30')->group(function () {
    Route::post('order', [Express30Controller::class, 'placeOrder']);
});

