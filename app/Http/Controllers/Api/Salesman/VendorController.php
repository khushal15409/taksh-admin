<?php

namespace App\Http\Controllers\Api\Salesman;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\SalesmanLocation;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get Nearby Vendors (within 15 KM)
     * GET /api/salesman/vendors/nearby
     */
    public function nearby(Request $request)
    {
        try {
            $salesman = $request->user();

            // Get salesman location
            $location = SalesmanLocation::where('salesman_id', $salesman->id)->first();

            if (!$location) {
                return $this->success([], 'salesman.location.required');
            }

            // Haversine formula to calculate distance (in KM)
            $lat = $location->latitude;
            $lng = $location->longitude;

            $vendors = Vendor::select([
                'vendors.*',
                DB::raw("(
                    6371 * acos(
                        cos(radians({$lat})) *
                        cos(radians(vendors.shop_latitude)) *
                        cos(radians(vendors.shop_longitude) - radians({$lng})) +
                        sin(radians({$lat})) *
                        sin(radians(vendors.shop_latitude))
                    )
                ) AS distance")
            ])
                ->where('vendors.status', 'pending')
                ->where('vendors.verification_status', 'pending')
                ->whereNotNull('vendors.shop_latitude')
                ->whereNotNull('vendors.shop_longitude')
                ->havingRaw('distance <= 15')
                ->orderBy('distance', 'asc')
                ->with(['state', 'city'])
                ->get();

            // Format response
            $formattedVendors = $vendors->map(function ($vendor) {
                // Get all categories for this vendor
                $categories = $vendor->category_models;
                $categoryNames = $categories->pluck('name')->toArray();
                
                return [
                    'id' => $vendor->id,
                    'shop_name' => $vendor->shop_name,
                    'owner_name' => $vendor->owner_name ?? $vendor->vendor_name,
                    'mobile_number' => $vendor->mobile_number,
                    'email' => $vendor->email,
                    'shop_address' => $vendor->shop_address ?? $vendor->address,
                    'shop_pincode' => $vendor->shop_pincode ?? $vendor->pincode,
                    'category' => !empty($categoryNames) ? implode(', ', $categoryNames) : null,
                    'categories' => $categoryNames, // Also include as array
                    'location' => [
                        'city' => $vendor->city ? $vendor->city->name : null,
                        'state' => $vendor->state ? $vendor->state->name : null,
                    ],
                    'distance' => round($vendor->distance, 2),
                    'latitude' => $vendor->shop_latitude,
                    'longitude' => $vendor->shop_longitude,
                ];
            });

            return $this->success([
                'vendors' => $formattedVendors,
                'count' => $formattedVendors->count(),
                'salesman_location' => [
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                ],
            ], 'salesman.vendors.nearby');
        } catch (\Exception $e) {
            \Log::error('Nearby vendors error: ' . $e->getMessage());
            return $this->error('api.server_error', 500);
        }
    }
}
