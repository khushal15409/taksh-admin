<?php

namespace App\Http\Controllers\Api\DeliveryMan;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DeliveryMan;
use App\Models\OtpVerification;
use App\Traits\ApiResponseTrait;
use App\CentralLogics\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Send OTP to mobile number
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string|regex:/^[0-9]{10}$/',
        ]);

        $mobileNumber = $request->input('mobile_number');
        $key = 'send-otp-delivery-man:' . $mobileNumber;

        // Rate limiting: max 5 requests per minute
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return $this->error('api.otp_rate_limit', 429);
        }

        RateLimiter::hit($key, 60);

        // TEST MODE: Use fixed OTP = 1234
        $otp = '1234';
        $expiresAt = Carbon::now()->addMinutes(5);

        // Hash OTP before storing
        $hashedOtp = Hash::make($otp);

        // Store OTP (use mobile field in otp_verifications table)
        OtpVerification::create([
            'mobile' => $mobileNumber,
            'otp' => $hashedOtp,
            'expires_at' => $expiresAt,
            'is_used' => false,
        ]);

        // In production, send OTP via SMS gateway
        return $this->success([
            'message' => 'OTP sent. Use 1234 for verification (TEST MODE)',
            'expires_at' => $expiresAt->toDateTimeString(),
        ], 'api.delivery.otp.sent');
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string|regex:/^[0-9]{10}$/',
            'otp' => 'required|string',
        ]);

        $mobileNumber = $request->input('mobile_number');
        $otp = $request->input('otp');

        // TEST MODE: Fixed OTP = 1234
        if ($otp !== "1234") {
            return $this->error('api.delivery.otp.invalid', 400);
        }

        // Find latest unused OTP for this mobile
        $otpVerification = OtpVerification::where('mobile', $mobileNumber)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->latest()
            ->first();

        // In TEST MODE: If OTP is 1234 and no record exists, create one
        if (!$otpVerification) {
            $otpVerification = OtpVerification::create([
                'mobile' => $mobileNumber,
                'otp' => Hash::make('1234'),
                'expires_at' => Carbon::now()->addMinutes(5),
                'is_used' => false,
            ]);
        }

        // Mark OTP as used
        $otpVerification->update(['is_used' => true]);

        // Check if delivery man exists
        $deliveryMan = DeliveryMan::where('mobile_number', $mobileNumber)->first();

        if ($deliveryMan) {
            // Delivery man exists - check status
            if ($deliveryMan->status === 'approved' && $deliveryMan->user->is_active) {
                // Already approved - allow login
                return $this->success([
                    'can_register' => false,
                    'can_login' => true,
                    'status' => $deliveryMan->status,
                ], 'api.delivery.otp.verified');
            } else {
                // Pending approval
                return $this->success([
                    'can_register' => false,
                    'can_login' => false,
                    'status' => $deliveryMan->status,
                    'message' => 'Your registration is pending approval',
                ], 'api.delivery.register.pending');
            }
        } else {
            // Delivery man doesn't exist - allow registration
            return $this->success([
                'can_register' => true,
                'can_login' => false,
            ], 'delivery.otp.verified');
        }
    }

    /**
     * Register delivery man
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'nullable|email|max:191',
            'mobile_number' => 'required|string|regex:/^[0-9]{10}$/|unique:delivery_men,mobile_number',
            'address' => 'required|string',
            'pincode' => 'required|string|max:10',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'fulfillment_center_id' => 'required|exists:fulfillment_centers,id',
            'vehicle_type' => 'required|in:bike,cycle,scooter',
            'deliveryman_type' => 'required|in:freelancer,salary_based',
            'vehicle_number' => 'nullable|string|max:50',
            'driving_license_number' => 'required|string|max:50',
            'aadhaar_number' => 'required|string|regex:/^[0-9]{12}$/',
            'profile_photo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'aadhaar_front' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'aadhaar_back' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'driving_license_photo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Create or get user
            $user = User::firstOrCreate(
                ['mobile' => $request->mobile_number],
                [
                    'name' => $request->name,
                    'user_type' => 'delivery_man',
                    'is_verified' => true,
                    'status' => 'active',
                    'is_active' => false, // Will be activated after approval
                ]
            );

            // Update user type and name if user already exists
            $user->user_type = 'delivery_man';
            $user->name = $request->name;
            $user->is_active = false;
            $user->save();

            // Upload files
            $profilePhoto = null;
            $aadhaarFront = null;
            $aadhaarBack = null;
            $drivingLicensePhoto = null;

            if ($request->hasFile('profile_photo')) {
                $profilePhoto = Helpers::upload('delivery-man/', 'png', $request->file('profile_photo'));
            }
            if ($request->hasFile('aadhaar_front')) {
                $aadhaarFront = Helpers::upload('delivery-man/documents/', 'png', $request->file('aadhaar_front'));
            }
            if ($request->hasFile('aadhaar_back')) {
                $aadhaarBack = Helpers::upload('delivery-man/documents/', 'png', $request->file('aadhaar_back'));
            }
            if ($request->hasFile('driving_license_photo')) {
                $drivingLicensePhoto = Helpers::upload('delivery-man/documents/', 'png', $request->file('driving_license_photo'));
            }

            // Create delivery man
            $deliveryMan = DeliveryMan::create([
                'user_id' => $user->id,
                'fulfillment_center_id' => $request->fulfillment_center_id,
                'name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
                'address' => $request->address,
                'pincode' => $request->pincode,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'vehicle_type' => $request->vehicle_type,
                'deliveryman_type' => $request->deliveryman_type,
                'vehicle_number' => $request->vehicle_number,
                'driving_license_number' => $request->driving_license_number,
                'aadhaar_number' => $request->aadhaar_number,
                'profile_photo' => $profilePhoto,
                'aadhaar_front' => $aadhaarFront,
                'aadhaar_back' => $aadhaarBack,
                'driving_license_photo' => $drivingLicensePhoto,
                'status' => 'pending',
            ]);

            // Assign delivery-man role (inactive until approved)
            $role = Role::firstOrCreate(
                ['name' => 'delivery-man'],
                ['guard_name' => 'web']
            );
            if (!$user->hasRole('delivery-man')) {
                $user->assignRole($role);
            }

            DB::commit();

            return $this->success([
                'delivery_man' => [
                    'id' => $deliveryMan->id,
                    'name' => $deliveryMan->name,
                    'mobile_number' => $deliveryMan->mobile_number,
                    'status' => $deliveryMan->status,
                    'deliveryman_type' => $deliveryMan->deliveryman_type,
                ],
            ], 'delivery.register.pending');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('api.delivery.register.failed', 500);
        }
    }

    /**
     * Login delivery man (after approval)
     */
    public function login(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string|regex:/^[0-9]{10}$/',
            'otp' => 'required|string',
        ]);

        $mobileNumber = $request->input('mobile_number');
        $otp = $request->input('otp');

        // TEST MODE: Fixed OTP = 1234
        if ($otp !== "1234") {
            return $this->error('api.delivery.otp.invalid', 400);
        }

        // Find delivery man
        $deliveryMan = DeliveryMan::where('mobile_number', $mobileNumber)
            ->with('user')
            ->first();

        if (!$deliveryMan) {
            return $this->error('api.delivery.login.not_found', 404);
        }

        // Check if approved and active
        if ($deliveryMan->status !== 'approved') {
            return $this->error('api.delivery.login.pending_approval', 403);
        }

        if (!$deliveryMan->user->is_active) {
            return $this->error('api.delivery.login.inactive', 403);
        }

        // Verify OTP
        $otpVerification = OtpVerification::where('mobile', $mobileNumber)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->latest()
            ->first();

        // In TEST MODE: Create OTP record if not exists
        if (!$otpVerification) {
            $otpVerification = OtpVerification::create([
                'mobile' => $mobileNumber,
                'otp' => Hash::make('1234'),
                'expires_at' => Carbon::now()->addMinutes(5),
                'is_used' => false,
            ]);
        }

        // Mark OTP as used
        $otpVerification->update(['is_used' => true]);

        // Generate Sanctum token
        $token = $deliveryMan->user->createToken('delivery-man-app')->plainTextToken;

        return $this->success([
            'delivery_man' => [
                'id' => $deliveryMan->id,
                'name' => $deliveryMan->name,
                'mobile_number' => $deliveryMan->mobile_number,
                'email' => $deliveryMan->email,
                'status' => $deliveryMan->status,
                'fulfillment_center_id' => $deliveryMan->fulfillment_center_id,
                'deliveryman_type' => $deliveryMan->deliveryman_type ?? 'freelancer',
            ],
            'token' => $token,
        ], 'api.delivery.login.success');
    }
}
