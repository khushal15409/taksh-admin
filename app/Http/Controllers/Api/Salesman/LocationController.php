<?php

namespace App\Http\Controllers\Api\Salesman;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Salesman\UpdateLocationRequest;
use App\Models\SalesmanLocation;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class LocationController extends Controller
{
    use ApiResponseTrait;

    /**
     * Update Salesman Location
     * POST /api/salesman/location/update
     */
    public function update(UpdateLocationRequest $request)
    {
        try {
            $salesman = $request->user();

            // Rate limiting: max 10 updates per minute
            $key = 'location_update:' . $salesman->id;
            if (RateLimiter::tooManyAttempts($key, 10)) {
                return $this->error('api.rate_limit_exceeded', 429);
            }
            RateLimiter::hit($key, 60);

            // Update or create location
            SalesmanLocation::updateOrCreate(
                ['salesman_id' => $salesman->id],
                [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'updated_at' => now(),
                ]
            );

            return $this->success([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'updated_at' => now()->toDateTimeString(),
            ], 'salesman.location.updated');
        } catch (\Exception $e) {
            return $this->error('api.server_error', 500);
        }
    }
}
