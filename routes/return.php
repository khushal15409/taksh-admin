<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReturnController;

// Protected return routes
Route::middleware('auth:sanctum')->prefix('return')->group(function () {
    Route::post('request', [ReturnController::class, 'store']);
});

