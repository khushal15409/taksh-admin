<?php

namespace App\Http\Controllers\Api\Salesman;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Salesman\LoginRequest;
use App\Models\User;
use App\Models\SalesmanLocation;
use App\Models\OtpVerification;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Carbon\Carbon;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Send OTP to salesman mobile number
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string|regex:/^[0-9]{10}$/',
        ]);

        $mobile = $request->input('mobile_number');
        $key = 'salesman-send-otp:' . $mobile;

        // Rate limiting: max 5 requests per minute
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return $this->error('api.otp_rate_limit', 429);
        }

        RateLimiter::hit($key, 60);

        // Check if salesman exists
        $user = User::where('mobile', $mobile)
            ->where('user_type', 'salesman')
            ->first();

        if (!$user) {
            return $this->error('salesman.not_found', 404);
        }

        // Check if salesman is active
        if (!$user->is_active) {
            return $this->error('salesman.login.inactive', 403);
        }

        // TEST MODE: Use fixed OTP = 1234
        $otp = '1234';
        $expiresAt = Carbon::now()->addMinutes(5);

        // Hash OTP before storing (still store for future SMS integration)
        $hashedOtp = Hash::make($otp);

        // Store OTP
        OtpVerification::create([
            'mobile' => $mobile,
            'otp' => $hashedOtp,
            'expires_at' => $expiresAt,
            'is_used' => false,
        ]);

        // In production, send OTP via SMS gateway
        // TEST MODE: Fixed OTP = 1234
        return $this->success([
            'message' => 'OTP sent. Use 1234 for verification (TEST MODE)',
            'expires_at' => $expiresAt->toDateTimeString(),
        ], 'salesman.otp_sent');
    }

    /**
     * Verify OTP and login salesman
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string|regex:/^[0-9]{10}$/',
            'otp' => 'required|string',
        ]);

        $mobile = $request->input('mobile_number');
        $otp = $request->input('otp');

        // TEST MODE: Fixed OTP = 1234
        if ($otp !== "1234") {
            return $this->error('api.otp_invalid', 400);
        }

        // Find user with salesman user_type
        $user = User::where('mobile', $mobile)
            ->where('user_type', 'salesman')
            ->first();

        if (!$user) {
            return $this->error('salesman.not_found', 404);
        }

        // Check if salesman is active
        if (!$user->is_active) {
            return $this->error('salesman.login.inactive', 403);
        }

        // Verify OTP
        $otpVerification = OtpVerification::where('mobile', $mobile)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->latest()
            ->first();

        // In TEST MODE: If OTP is 1234 and no record exists, create one
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

    /**
     * Salesman Login (OTP) - Legacy method, kept for backward compatibility
     * @deprecated Use verifyOtp instead
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
