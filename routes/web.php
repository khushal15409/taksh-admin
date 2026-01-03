<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

// Admin routes
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'dashboard'])->name('dashboard');
    
    // Dashboard stats routes
    Route::group(['prefix' => 'dashboard-stats', 'as' => 'dashboard-stats.'], function () {
        Route::post('order', [App\Http\Controllers\Admin\DashboardController::class, 'order'])->name('order');
        Route::post('zone', [App\Http\Controllers\Admin\DashboardController::class, 'zone'])->name('zone');
        Route::post('user-overview', [App\Http\Controllers\Admin\DashboardController::class, 'user_overview'])->name('user-overview');
    });
});
