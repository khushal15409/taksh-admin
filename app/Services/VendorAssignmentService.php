<?php

namespace App\Services;

use App\Models\Vendor;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VendorAssignmentService
{
    /**
     * Auto-assign vendor to salesman based on pincode matching
     * Selects salesman with least pending verifications
     * 
     * @param Vendor $vendor
     * @return User|null Assigned salesman or null if no match found
     */
    public function autoAssignByPincode(Vendor $vendor): ?User
    {
        // Get vendor pincode (prefer shop_pincode, fallback to pincode)
        $vendorPincode = $vendor->shop_pincode ?? $vendor->pincode;

        if (!$vendorPincode) {
            Log::warning('Vendor assignment failed: Vendor pincode not set', [
                'vendor_id' => $vendor->id,
            ]);
            return null;
        }

        // Find salesmen with matching pincode
        $salesmen = User::where('user_type', 'salesman')
            ->where('pincode', $vendorPincode)
            ->where('is_active', true)
            ->get();

        if ($salesmen->isEmpty()) {
            Log::info('No active salesman found for pincode', [
                'vendor_id' => $vendor->id,
                'pincode' => $vendorPincode,
            ]);
            return null;
        }

        // Calculate pending verification count for each salesman
        $salesmenWithCounts = $salesmen->map(function ($salesman) {
            $pendingCount = Vendor::where('assigned_salesman_id', $salesman->id)
                ->whereIn('verification_status', ['pending', 'assigned'])
                ->count();

            return [
                'salesman' => $salesman,
                'pending_count' => $pendingCount,
            ];
        });

        // Sort by pending count (ascending) and select the one with least pending
        $selected = $salesmenWithCounts->sortBy('pending_count')->first();

        if (!$selected) {
            return null;
        }

        $assignedSalesman = $selected['salesman'];

        // Update vendor assignment
        DB::beginTransaction();
        try {
            $vendor->update([
                'assigned_salesman_id' => $assignedSalesman->id,
                'verification_status' => 'assigned',
            ]);

            DB::commit();

            Log::info('Vendor auto-assigned to salesman', [
                'vendor_id' => $vendor->id,
                'salesman_id' => $assignedSalesman->id,
                'pincode' => $vendorPincode,
                'pending_count' => $selected['pending_count'],
            ]);

            return $assignedSalesman;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Vendor assignment failed', [
                'vendor_id' => $vendor->id,
                'salesman_id' => $assignedSalesman->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get pending verification count for a salesman
     * 
     * @param User $salesman
     * @return int
     */
    public function getPendingVerificationCount(User $salesman): int
    {
        return Vendor::where('assigned_salesman_id', $salesman->id)
            ->whereIn('verification_status', ['pending', 'assigned'])
            ->count();
    }
}

