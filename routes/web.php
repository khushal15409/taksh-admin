<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Login Routes
Route::get('login', [App\Http\Controllers\LoginController::class, 'login'])->name('login');
Route::post('login', [App\Http\Controllers\LoginController::class, 'submit'])->name('login.post');
Route::post('logout', [App\Http\Controllers\LoginController::class, 'logout'])->name('logout');
Route::get('logout', [App\Http\Controllers\LoginController::class, 'logout'])->name('logout'); // Fallback for GET requests
Route::get('reload-captcha', [App\Http\Controllers\LoginController::class, 'reloadCaptcha'])->name('reload-captcha');
Route::get('reset-password', [App\Http\Controllers\LoginController::class, 'reset_password'])->name('reset-password');

// Admin routes - protected by auth middleware
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'dashboard'])->name('dashboard');
    
    // Dashboard stats routes
    Route::group(['prefix' => 'dashboard-stats', 'as' => 'dashboard-stats.'], function () {
        Route::post('order', [App\Http\Controllers\Admin\DashboardController::class, 'order'])->name('order');
        Route::post('zone', [App\Http\Controllers\Admin\DashboardController::class, 'zone'])->name('zone');
        Route::post('user-overview', [App\Http\Controllers\Admin\DashboardController::class, 'user_overview'])->name('user-overview');
        Route::post('commission-overview', [App\Http\Controllers\Admin\DashboardController::class, 'commission_overview'])->name('commission-overview');
    });
    
    // Logistics Routes
    Route::group(['prefix' => 'logistics', 'as' => 'logistics.'], function () {
        Route::get('warehouse', [App\Http\Controllers\Admin\Logistics\WarehouseController::class, 'index'])->name('warehouse.index');
        Route::get('warehouse/create', [App\Http\Controllers\Admin\Logistics\WarehouseController::class, 'create'])->name('warehouse.create');
        Route::post('warehouse', [App\Http\Controllers\Admin\Logistics\WarehouseController::class, 'store'])->name('warehouse.store');
        Route::get('warehouse/{id}/edit', [App\Http\Controllers\Admin\Logistics\WarehouseController::class, 'edit'])->name('warehouse.edit');
        Route::put('warehouse/{id}', [App\Http\Controllers\Admin\Logistics\WarehouseController::class, 'update'])->name('warehouse.update');
        Route::delete('warehouse/{id}', [App\Http\Controllers\Admin\Logistics\WarehouseController::class, 'destroy'])->name('warehouse.destroy');
        Route::post('warehouse/status', [App\Http\Controllers\Admin\Logistics\WarehouseController::class, 'status'])->name('warehouse.status');
        
        Route::get('miniwarehouse', [App\Http\Controllers\Admin\Logistics\MiniwarehouseController::class, 'index'])->name('miniwarehouse.index');
        Route::get('miniwarehouse/create', [App\Http\Controllers\Admin\Logistics\MiniwarehouseController::class, 'create'])->name('miniwarehouse.create');
        Route::post('miniwarehouse', [App\Http\Controllers\Admin\Logistics\MiniwarehouseController::class, 'store'])->name('miniwarehouse.store');
        Route::get('miniwarehouse/{id}/edit', [App\Http\Controllers\Admin\Logistics\MiniwarehouseController::class, 'edit'])->name('miniwarehouse.edit');
        Route::put('miniwarehouse/{id}', [App\Http\Controllers\Admin\Logistics\MiniwarehouseController::class, 'update'])->name('miniwarehouse.update');
        Route::delete('miniwarehouse/{id}', [App\Http\Controllers\Admin\Logistics\MiniwarehouseController::class, 'destroy'])->name('miniwarehouse.destroy');
        Route::post('miniwarehouse/status', [App\Http\Controllers\Admin\Logistics\MiniwarehouseController::class, 'status'])->name('miniwarehouse.status');
        
        Route::get('lm-center', [App\Http\Controllers\Admin\Logistics\LmCenterController::class, 'index'])->name('lm-center.index');
        Route::get('lm-center/create', [App\Http\Controllers\Admin\Logistics\LmCenterController::class, 'create'])->name('lm-center.create');
        Route::post('lm-center', [App\Http\Controllers\Admin\Logistics\LmCenterController::class, 'store'])->name('lm-center.store');
        Route::get('lm-center/{id}/edit', [App\Http\Controllers\Admin\Logistics\LmCenterController::class, 'edit'])->name('lm-center.edit');
        Route::put('lm-center/{id}', [App\Http\Controllers\Admin\Logistics\LmCenterController::class, 'update'])->name('lm-center.update');
        Route::delete('lm-center/{id}', [App\Http\Controllers\Admin\Logistics\LmCenterController::class, 'destroy'])->name('lm-center.destroy');
        Route::post('lm-center/status', [App\Http\Controllers\Admin\Logistics\LmCenterController::class, 'status'])->name('lm-center.status');
        Route::post('lm-center/verify-document', [App\Http\Controllers\Admin\Logistics\LmCenterController::class, 'verifyDocument'])->name('lm-center.verify-document');
        
        Route::get('fm-rt-center', [App\Http\Controllers\Admin\Logistics\FmRtCenterController::class, 'index'])->name('fm-rt-center.index');
        Route::get('fm-rt-center/create', [App\Http\Controllers\Admin\Logistics\FmRtCenterController::class, 'create'])->name('fm-rt-center.create');
        Route::post('fm-rt-center', [App\Http\Controllers\Admin\Logistics\FmRtCenterController::class, 'store'])->name('fm-rt-center.store');
        Route::get('fm-rt-center/{id}/edit', [App\Http\Controllers\Admin\Logistics\FmRtCenterController::class, 'edit'])->name('fm-rt-center.edit');
        Route::put('fm-rt-center/{id}', [App\Http\Controllers\Admin\Logistics\FmRtCenterController::class, 'update'])->name('fm-rt-center.update');
        Route::delete('fm-rt-center/{id}', [App\Http\Controllers\Admin\Logistics\FmRtCenterController::class, 'destroy'])->name('fm-rt-center.destroy');
        Route::post('fm-rt-center/status', [App\Http\Controllers\Admin\Logistics\FmRtCenterController::class, 'status'])->name('fm-rt-center.status');
        
        Route::get('pending-mapping', [App\Http\Controllers\Admin\Logistics\PendingMappingController::class, 'index'])->name('pending-mapping.index');
    });
    
    // Banner Routes
    Route::resource('banner', App\Http\Controllers\Admin\BannerController::class);
    Route::post('banner/status', [App\Http\Controllers\Admin\BannerController::class, 'statusToggle'])->name('banner.status');
});
