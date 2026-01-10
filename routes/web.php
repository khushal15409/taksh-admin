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
    
    // Profile routes
    Route::get('profile', [App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile');
    Route::post('profile/update', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/update-password', [App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.update-password');
    
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
        Route::get('lm-center/search-pincodes', [App\Http\Controllers\Admin\Logistics\LmCenterController::class, 'searchPincodes'])->name('lm-center.search-pincodes');
        
        Route::get('fm-rt-center', [App\Http\Controllers\Admin\Logistics\FmRtCenterController::class, 'index'])->name('fm-rt-center.index');
        Route::get('fm-rt-center/create', [App\Http\Controllers\Admin\Logistics\FmRtCenterController::class, 'create'])->name('fm-rt-center.create');
        Route::post('fm-rt-center', [App\Http\Controllers\Admin\Logistics\FmRtCenterController::class, 'store'])->name('fm-rt-center.store');
        Route::get('fm-rt-center/{id}/edit', [App\Http\Controllers\Admin\Logistics\FmRtCenterController::class, 'edit'])->name('fm-rt-center.edit');
        Route::put('fm-rt-center/{id}', [App\Http\Controllers\Admin\Logistics\FmRtCenterController::class, 'update'])->name('fm-rt-center.update');
        Route::delete('fm-rt-center/{id}', [App\Http\Controllers\Admin\Logistics\FmRtCenterController::class, 'destroy'])->name('fm-rt-center.destroy');
        Route::post('fm-rt-center/status', [App\Http\Controllers\Admin\Logistics\FmRtCenterController::class, 'status'])->name('fm-rt-center.status');
        Route::get('fm-rt-center/search-pincodes', [App\Http\Controllers\Admin\Logistics\FmRtCenterController::class, 'searchPincodes'])->name('fm-rt-center.search-pincodes');
        
        Route::get('pending-mapping', [App\Http\Controllers\Admin\Logistics\PendingMappingController::class, 'index'])->name('pending-mapping.index');
        Route::get('pending-mapping/pending-pincodes', [App\Http\Controllers\Admin\Logistics\PendingMappingController::class, 'getPendingPincodes'])->name('pending-mapping.pending-pincodes');
        Route::get('pending-mapping/active-pincodes', [App\Http\Controllers\Admin\Logistics\PendingMappingController::class, 'getActivePincodes'])->name('pending-mapping.active-pincodes');
        Route::get('pending-mapping/live-ecommerce-pincodes', [App\Http\Controllers\Admin\Logistics\PendingMappingController::class, 'getLiveEcommercePincodes'])->name('pending-mapping.live-ecommerce-pincodes');
        Route::get('pending-mapping/pending-logistic-pincodes', [App\Http\Controllers\Admin\Logistics\PendingMappingController::class, 'getPendingLogisticPincodes'])->name('pending-mapping.pending-logistic-pincodes');
        Route::post('pending-mapping/pincode-status', [App\Http\Controllers\Admin\Logistics\PendingMappingController::class, 'updatePincodeStatus'])->name('pending-mapping.pincode-status');
    });
    
    // Banner Routes
    Route::resource('banner', App\Http\Controllers\Admin\BannerController::class);
    Route::post('banner/status', [App\Http\Controllers\Admin\BannerController::class, 'statusToggle'])->name('banner.status');
    
    // App Dashboard Sections Routes
    Route::prefix('app-dashboard')->name('app-dashboard.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AppDashboardSectionController::class, 'index'])->name('index');
        Route::post('update-status', [App\Http\Controllers\Admin\AppDashboardSectionController::class, 'updateStatus'])->name('update-status');
        Route::post('update-sort-orders', [App\Http\Controllers\Admin\AppDashboardSectionController::class, 'updateSortOrder'])->name('update-sort-orders');
        Route::put('update-sort-order/{id}', [App\Http\Controllers\Admin\AppDashboardSectionController::class, 'updateSingleSortOrder'])->name('update-sort-order');
    });
    
    // Category Routes
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
    Route::post('categories/status', [App\Http\Controllers\Admin\CategoryController::class, 'statusToggle'])->name('categories.status');
    
    // Product Routes
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
    Route::post('products/status', [App\Http\Controllers\Admin\ProductController::class, 'statusToggle'])->name('products.status');
    Route::post('products/flag', [App\Http\Controllers\Admin\ProductController::class, 'flagToggle'])->name('products.flag');
    Route::delete('products/image/{id}', [App\Http\Controllers\Admin\ProductController::class, 'deleteImage'])->name('products.delete-image');
    Route::get('get-cities', [App\Http\Controllers\Admin\ProductController::class, 'getCities'])->name('get-cities');
    Route::get('get-fulfillment-centers', [App\Http\Controllers\Admin\ProductController::class, 'getFulfillmentCenters'])->name('get-fulfillment-centers');
    
    // Order Routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('index');
        Route::get('pending', [App\Http\Controllers\Admin\OrderController::class, 'pending'])->name('pending');
        Route::get('confirmed', [App\Http\Controllers\Admin\OrderController::class, 'confirmed'])->name('confirmed');
        Route::get('delivered', [App\Http\Controllers\Admin\OrderController::class, 'delivered'])->name('delivered');
        Route::get('cancelled', [App\Http\Controllers\Admin\OrderController::class, 'cancelled'])->name('cancelled');
        
        // Express-30 Orders (must be before {id} route)
        Route::prefix('express-30')->name('express-30.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ExpressOrderController::class, 'index'])->name('index');
            Route::get('{id}', [App\Http\Controllers\Admin\ExpressOrderController::class, 'show'])->name('show');
        });
        
        // Order detail (must be last to avoid conflicts)
        Route::get('{id}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('show');
    });
    
    // Customer Routes
    Route::prefix('customer')->name('customer.')->group(function () {
        Route::get('list', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('list');
        Route::get('view/{id}', [App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('view');
    });
    
    // Vendor Management Routes
    Route::prefix('vendor')->name('vendor.')->group(function () {
        // Vendor Assignment
        Route::prefix('assignment')->name('assignment.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\VendorAssignmentController::class, 'index'])->name('index');
            Route::get('{id}', [App\Http\Controllers\Admin\VendorAssignmentController::class, 'show'])->name('show');
            Route::post('{id}/assign', [App\Http\Controllers\Admin\VendorAssignmentController::class, 'assign'])->name('assign');
            Route::post('{id}/auto-assign', [App\Http\Controllers\Admin\VendorAssignmentController::class, 'autoAssignSalesman'])->name('auto-assign');
        });
        
        // Vendor Approval
        Route::prefix('approval')->name('approval.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\VendorApprovalController::class, 'index'])->name('index');
            Route::get('{id}', [App\Http\Controllers\Admin\VendorApprovalController::class, 'show'])->name('show');
            Route::post('{id}/approve', [App\Http\Controllers\Admin\VendorApprovalController::class, 'approveWeb'])->name('approve');
            Route::post('{id}/reject', [App\Http\Controllers\Admin\VendorApprovalController::class, 'rejectWeb'])->name('reject');
        });
    });
    
    // Salesmen Management Routes
    Route::prefix('salesmen')->name('salesmen.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\SalesmanController::class, 'index'])->name('index');
        Route::get('create', [App\Http\Controllers\Admin\SalesmanController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\SalesmanController::class, 'store'])->name('store');
        Route::get('{id}/edit', [App\Http\Controllers\Admin\SalesmanController::class, 'edit'])->name('edit');
        Route::put('{id}', [App\Http\Controllers\Admin\SalesmanController::class, 'update'])->name('update');
        Route::post('{id}/toggle-status', [App\Http\Controllers\Admin\SalesmanController::class, 'toggleStatus'])->name('toggle-status');
    });
    
    // Custom Role (Employee Role) Routes
    Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
        Route::group(['prefix' => 'custom-role', 'as' => 'custom-role.'], function () {
            Route::get('create', [App\Http\Controllers\Admin\Employee\CustomRoleController::class, 'index'])->name('create');
            Route::post('create', [App\Http\Controllers\Admin\Employee\CustomRoleController::class, 'add'])->name('create');
            Route::get('edit/{id}', [App\Http\Controllers\Admin\Employee\CustomRoleController::class, 'getUpdateView'])->name('edit');
            Route::post('update/{id}', [App\Http\Controllers\Admin\Employee\CustomRoleController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [App\Http\Controllers\Admin\Employee\CustomRoleController::class, 'delete'])->name('delete');
            Route::post('search', [App\Http\Controllers\Admin\Employee\CustomRoleController::class, 'search'])->name('search');
        });
        
        // Employee Routes
        Route::group(['prefix' => 'employee', 'as' => 'employee.'], function () {
            Route::get('list', [App\Http\Controllers\Admin\Employee\EmployeeController::class, 'index'])->name('list');
            Route::get('add-new', [App\Http\Controllers\Admin\Employee\EmployeeController::class, 'getAddView'])->name('add-new');
            Route::post('add-new', [App\Http\Controllers\Admin\Employee\EmployeeController::class, 'add'])->name('add-new');
            Route::get('edit/{id}', [App\Http\Controllers\Admin\Employee\EmployeeController::class, 'getUpdateView'])->name('edit');
            Route::post('update/{id}', [App\Http\Controllers\Admin\Employee\EmployeeController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [App\Http\Controllers\Admin\Employee\EmployeeController::class, 'delete'])->name('delete');
            Route::post('search', [App\Http\Controllers\Admin\Employee\EmployeeController::class, 'search'])->name('search');
            Route::get('search-pincodes', [App\Http\Controllers\Admin\Employee\EmployeeController::class, 'searchPincodes'])->name('search-pincodes');
        });
    });
});
