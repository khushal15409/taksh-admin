<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpVerification;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Send OTP to mobile number
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|regex:/^[0-9]{10}$/',
        ]);

        $mobile = $request->input('mobile');
        $key = 'send-otp:' . $mobile;

        // Rate limiting: max 5 requests per minute
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return $this->error('api.otp_rate_limit', 429);
        }

        RateLimiter::hit($key, 60);

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
        ], 'api.otp_sent');
    }

    /**
     * Verify OTP and login/register user
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|regex:/^[0-9]{10}$/',
            'otp' => 'required|string',
        ]);

        $mobile = $request->input('mobile');
        $otp = $request->input('otp');

        // TEST MODE: Fixed OTP = 1234
        if ($otp !== "1234") {
            return $this->error('api.otp_invalid', 400);
        }

        // Find latest unused OTP for this mobile
        $otpVerification = OtpVerification::where('mobile', $mobile)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->latest()
            ->first();

        // In TEST MODE: If OTP is 1234 and no record exists, create one or skip validation
        if (!$otpVerification) {
            // For TEST MODE, if OTP is correct (1234), allow verification even without DB record
            // Create a record for consistency
            $otpVerification = OtpVerification::create([
                'mobile' => $mobile,
                'otp' => Hash::make('1234'),
                'expires_at' => Carbon::now()->addMinutes(5),
                'is_used' => false,
            ]);
        }

        // Mark OTP as used
        $otpVerification->update(['is_used' => true]);

        // Find existing user or auto-register new user
        // If user doesn't exist, create new user account automatically
        $user = User::firstOrCreate(
            ['mobile' => $mobile],
            [
                'name' => null,
                'is_verified' => true,
                'status' => 'active',
            ]
        );

        // Update user verification status (in case user existed but wasn't verified)
        if (!$user->is_verified) {
            $user->update(['is_verified' => true]);
        }

        // Ensure user status is active
        if ($user->status !== 'active') {
            $user->update(['status' => 'active']);
        }

        // Generate Sanctum token
        $token = $user->createToken('mobile-app')->plainTextToken;

        // Merge guest cart if guest_token is provided
        $guestToken = $request->input('guest_token');
        if ($guestToken) {
            $this->mergeGuestCart($user, $guestToken);
        }

        return $this->success([
            'user' => [
                'id' => $user->id,
                'mobile' => $user->mobile,
                'name' => $user->name,
                'is_verified' => $user->is_verified,
            ],
            'token' => $token,
        ], 'api.otp_verified');
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success([], 'api.logged_out');
    }

    /**
     * Merge guest cart into user cart
     */
    private function mergeGuestCart($user, $guestToken)
    {
        $guestCart = \App\Models\Cart::where('guest_token', $guestToken)
            ->whereNull('user_id')
            ->first();

        if (!$guestCart) {
            return;
        }

        $userCart = \App\Models\Cart::firstOrCreate([
            'user_id' => $user->id,
        ]);

        // Merge cart items
        foreach ($guestCart->items as $guestItem) {
            $existingItem = $userCart->items()
                ->where('product_variant_id', $guestItem->product_variant_id)
                ->first();

            if ($existingItem) {
                $existingItem->update([
                    'qty' => $existingItem->qty + $guestItem->qty,
                ]);
            } else {
                $userCart->items()->create([
                    'product_variant_id' => $guestItem->product_variant_id,
                    'qty' => $guestItem->qty,
                ]);
            }
        }

        // Delete guest cart
        $guestCart->delete();
    }
}
