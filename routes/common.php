<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\BannerController;

// Common routes without prefix
Route::get('dashboard', [DashboardController::class, 'index']);
Route::get('categories', [ProductController::class, 'categories']);
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{id}', [ProductController::class, 'show']);
Route::get('banners', [BannerController::class, 'index']);

