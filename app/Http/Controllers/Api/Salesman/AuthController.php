<?php

namespace App\Http\Controllers\Api\Salesman;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Salesman\LoginRequest;
use App\Models\User;
use App\Models\SalesmanLocation;
use App\Models\OtpVerification;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Salesman Login (OTP)
     */
    public function login(LoginRequest $request)
    {
        $mobile = $request->mobile_number;
        $otp = $request->otp;

        // TEST MODE: Fixed OTP = 1234
        if ($otp !== "1234") {
            return $this->error('api.otp_invalid', 400);
        }

        // Find user with salesman user_type
        $user = User::where('mobile', $mobile)
            ->where('user_type', 'salesman')
            ->first();

        if (!$user) {
            return $this->error('api.not_found', 404);
        }

        // Check if salesman is active
        if (!$user->is_active) {
            return $this->error('salesman.login.inactive', 403);
        }

        // Verify OTP (TEST MODE: accept 1234)
        $otpVerification = OtpVerification::where('mobile', $mobile)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->latest()
            ->first();

        if (!$otpVerification && $otp === "1234") {
            // Create OTP record for consistency in TEST MODE
            $otpVerification = OtpVerification::create([
                'mobile' => $mobile,
                'otp' => Hash::make('1234'),
                'expires_at' => Carbon::now()->addMinutes(5),
                'is_used' => false,
            ]);
        }

        if ($otpVerification) {
            $otpVerification->update(['is_used' => true]);
        }

        // Generate Sanctum token
        $token = $user->createToken('salesman-app')->plainTextToken;

        // Update location if provided
        if ($request->has('latitude') && $request->has('longitude')) {
            SalesmanLocation::updateOrCreate(
                ['salesman_id' => $user->id],
                [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'updated_at' => now(),
                ]
            );
        }

        return $this->success([
            'user' => [
                'id' => $user->id,
                'mobile' => $user->mobile,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
            ],
            'token' => $token,
            'location_required' => !SalesmanLocation::where('salesman_id', $user->id)->exists(),
        ], 'salesman.login.success');
    }
}
