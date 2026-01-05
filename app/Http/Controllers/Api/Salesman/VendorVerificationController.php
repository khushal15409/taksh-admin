<?php

namespace App\Http\Controllers\Api\Salesman;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Salesman\VerifyVendorRequest;
use App\Models\Vendor;
use App\Models\VendorVerification;
use App\Models\SalesmanLocation;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VendorVerificationController extends Controller
{
    use ApiResponseTrait;

    /**
     * View Assigned Vendors (DEPRECATED - Use nearby vendors instead)
     * This endpoint is kept for backward compatibility but returns empty
     */
    public function index(Request $request)
    {
        // This endpoint is deprecated - use /api/salesman/vendors/nearby instead
        return $this->success([
            'vendors' => [],
            'message' => 'Please use /api/salesman/vendors/nearby endpoint to get nearby vendors',
        ], 'api.success');
    }

    /**
     * Submit Vendor Verification
     */
    public function verify(VerifyVendorRequest $request, $vendorId)
    {
        try {
            $user = $request->user();
            $vendor = Vendor::findOrFail($vendorId);

            // Check if vendor is in pending status
            if ($vendor->verification_status !== 'pending' || $vendor->status !== 'pending') {
                return $this->error('vendor.verification.invalid_status', 400);
            }

            // Verify distance (must be within 15 KM)
            $location = SalesmanLocation::where('salesman_id', $user->id)->first();
            if (!$location) {
                return $this->error('salesman.location.required', 400);
            }

            if (!$vendor->shop_latitude || !$vendor->shop_longitude) {
                return $this->error('vendor.location.missing', 400);
            }

            // Calculate distance using Haversine formula
            $distance = $this->calculateDistance(
                $location->latitude,
                $location->longitude,
                $vendor->shop_latitude,
                $vendor->shop_longitude
            );

            if ($distance > 15) {
                return $this->error('vendor.verification.too_far', 403, [
                    'distance' => round($distance, 2),
                    'max_distance' => 15,
                ]);
            }

            DB::beginTransaction();

            // Upload shop photo
            $shopPhotoPath = null;
            if ($request->hasFile('shop_photo')) {
                $shopPhotoPath = $request->file('shop_photo')->store('vendor_verifications', 'public');
            }

            // Upload license photo (optional)
            $licensePhotoPath = null;
            if ($request->hasFile('license_photo')) {
                $licensePhotoPath = $request->file('license_photo')->store('vendor_verifications', 'public');
            }

            // Create verification record
            VendorVerification::create([
                'vendor_id' => $vendor->id,
                'salesman_id' => $user->id,
                'shop_photo' => $shopPhotoPath,
                'license_photo' => $licensePhotoPath,
                'remarks' => $request->remarks,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            // Update vendor status (no permanent assignment, just verification)
            $vendor->update([
                'verification_status' => 'verified',
                'verified_by' => $user->id,
                'verified_at' => now(),
                // Note: We don't set assigned_salesman_id - vendor is not permanently assigned
            ]);

            DB::commit();

            return $this->success([
                'vendor_id' => $vendor->id,
                'distance' => round($distance, 2),
                'message' => 'Vendor verification submitted successfully. Waiting for admin approval.',
            ], 'salesman.verification.submitted');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Vendor verification error: ' . $e->getMessage());
            return $this->error('api.server_error', 500);
        }
    }

    /**
     * Calculate distance between two points using Haversine formula
     * Returns distance in kilometers
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
