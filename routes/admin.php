<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\VendorApprovalController;
use App\Http\Controllers\Admin\VendorAssignmentController;

// Admin API routes (require authentication and super-admin role)
Route::middleware(['auth:sanctum', 'role:super-admin'])->prefix('admin')->group(function () {
    Route::post('vendor/{vendor_id}/assign-salesman', [VendorAssignmentController::class, 'assignSalesman']);
    Route::post('vendor/{vendor_id}/auto-assign-salesman', [VendorAssignmentController::class, 'autoAssignSalesman']);
    Route::post('vendor/{vendor_id}/approve', [VendorApprovalController::class, 'approve']);
    Route::post('vendor/{vendor_id}/reject', [VendorApprovalController::class, 'reject']);
});

