<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Vendor\RegisterRequest;
use App\Http\Requests\Api\Vendor\LoginRequest;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorDocument;
use App\Models\OtpVerification;
use App\Models\Category;
use App\Services\VendorAssignmentService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Vendor Registration
     */
    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();

            // Use owner_name as vendor_name if vendor_name not provided (backward compatibility)
            $vendorName = $request->vendor_name ?? $request->owner_name;

            // Create user account
            $user = User::create([
                'mobile' => $request->mobile_number,
                'name' => $vendorName,
                'email' => $request->email,
                'user_type' => 'vendor',
                'vendor_status' => 'pending',
                'is_active' => false,
                'is_verified' => false,
                'status' => 'active',
            ]);

            // Assign vendor role (inactive)
            $vendorRole = Role::firstOrCreate(['name' => 'vendor']);
            $user->assignRole($vendorRole);

            // Upload shop images
            $shopImages = [];
            if ($request->hasFile('shop_images')) {
                foreach ($request->file('shop_images') as $image) {
                    $path = $image->store('vendors/shop_images', 'public');
                    $shopImages[] = $path;
                }
            }

            // Upload owner image
            $ownerImagePath = null;
            if ($request->hasFile('owner_image')) {
                $ownerImagePath = $request->file('owner_image')->store('vendors/owner_images', 'public');
            }

            // Process category_id - accept comma-separated values
            $categoryId = null;
            if ($request->has('category_id') && !empty($request->category_id)) {
                // If it's already a string with commas, use it as is
                // If it's a single ID, convert to string
                $categoryId = is_string($request->category_id)
                    ? $request->category_id
                    : (string) $request->category_id;

                // Clean up: remove spaces and ensure proper comma separation
                $categoryId = preg_replace('/\s+/', '', $categoryId); // Remove spaces
                $categoryId = trim($categoryId, ','); // Remove leading/trailing commas
            }

            // Prepare vendor data
            $vendorData = [
                'user_id' => $user->id,
                'vendor_name' => $vendorName,
                'owner_name' => $request->owner_name,
                'shop_name' => $request->shop_name,
                'shop_address' => $request->shop_address,
                'shop_pincode' => $request->shop_pincode,
                'shop_latitude' => $request->shop_latitude,
                'shop_longitude' => $request->shop_longitude,
                'category_id' => $categoryId,
                'shop_images' => !empty($shopImages) ? $shopImages : null,
                'owner_address' => $request->owner_address,
                'owner_pincode' => $request->owner_pincode,
                'owner_latitude' => $request->owner_latitude,
                'owner_longitude' => $request->owner_longitude,
                'owner_image' => $ownerImagePath,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
                'status' => 'pending',
                'verification_status' => 'pending',
            ];

            // Backward compatibility: Use old fields if provided, otherwise use new fields
            $vendorData['address'] = $request->address ?? $request->shop_address ?? '';
            $vendorData['state_id'] = $request->state_id ?? null;
            $vendorData['city_id'] = $request->city_id ?? null;
            $vendorData['pincode'] = $request->pincode ?? $request->shop_pincode ?? '';

            // Bank details (backward compatibility) - ensure required fields are set
            $vendorData['bank_name'] = $request->bank_name ?? '';
            if ($request->account_number) {
                $vendorData['account_number'] = $request->account_number;
            } elseif ($request->bank_account_number) {
                $vendorData['account_number'] = $request->bank_account_number;
            } else {
                $vendorData['account_number'] = '';
            }
            $vendorData['ifsc_code'] = $request->ifsc_code ?? '';

            // GST and PAN (backward compatibility)
            if ($request->gst_number) {
                $vendorData['gst_number'] = $request->gst_number;
            }
            if ($request->pan_number) {
                $vendorData['pan_number'] = $request->pan_number;
            }

            // Create vendor record
            $vendor = Vendor::create($vendorData);

            // Upload and save documents
            $documents = [];

            // Aadhaar
            if ($request->hasFile('aadhaar_file')) {
                $aadhaarPath = $request->file('aadhaar_file')->store('vendors/documents', 'public');
                $documents[] = [
                    'vendor_id' => $vendor->id,
                    'document_type' => 'aadhaar',
                    'document_number' => $request->aadhaar_number,
                    'document_file' => $aadhaarPath,
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // PAN
            if ($request->hasFile('pan_file')) {
                $panPath = $request->file('pan_file')->store('vendors/documents', 'public');
                $documents[] = [
                    'vendor_id' => $vendor->id,
                    'document_type' => 'pan',
                    'document_number' => $request->pan_number,
                    'document_file' => $panPath,
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Bank
            if ($request->hasFile('bank_file')) {
                $bankPath = $request->file('bank_file')->store('vendors/documents', 'public');
                $documents[] = [
                    'vendor_id' => $vendor->id,
                    'document_type' => 'bank',
                    'document_number' => $request->bank_account_number,
                    'document_file' => $bankPath,
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // GST or Non-GST
            if ($request->hasFile('gst_file')) {
                $gstPath = $request->file('gst_file')->store('vendors/documents', 'public');
                $documents[] = [
                    'vendor_id' => $vendor->id,
                    'document_type' => 'gst',
                    'document_number' => $request->gst_number,
                    'document_file' => $gstPath,
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } elseif ($request->hasFile('non_gst_file')) {
                $nonGstPath = $request->file('non_gst_file')->store('vendors/documents', 'public');
                $documents[] = [
                    'vendor_id' => $vendor->id,
                    'document_type' => 'non_gst',
                    'document_number' => null,
                    'document_file' => $nonGstPath,
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // MSME (optional)
            if ($request->hasFile('msme_file')) {
                $msmePath = $request->file('msme_file')->store('vendors/documents', 'public');
                $documents[] = [
                    'vendor_id' => $vendor->id,
                    'document_type' => 'msme',
                    'document_number' => null,
                    'document_file' => $msmePath,
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // FSSAI
            if ($request->hasFile('fssai_file')) {
                $fssaiPath = $request->file('fssai_file')->store('vendors/documents', 'public');
                $documents[] = [
                    'vendor_id' => $vendor->id,
                    'document_type' => 'fssai',
                    'document_number' => null,
                    'document_file' => $fssaiPath,
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Shop Agreement (optional)
            if ($request->hasFile('shop_agreement_file')) {
                $agreementPath = $request->file('shop_agreement_file')->store('vendors/documents', 'public');
                $documents[] = [
                    'vendor_id' => $vendor->id,
                    'document_type' => 'shop_agreement',
                    'document_number' => null,
                    'document_file' => $agreementPath,
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Bulk insert documents
            if (!empty($documents)) {
                VendorDocument::insert($documents);
            }

            // Auto-assign salesman based on pincode
            $assignmentService = new VendorAssignmentService();
            $assignedSalesman = $assignmentService->autoAssignByPincode($vendor);

            $assignmentInfo = null;
            if ($assignedSalesman) {
                $assignmentInfo = [
                    'salesman_id' => $assignedSalesman->id,
                    'salesman_name' => $assignedSalesman->name,
                    'pincode' => $assignedSalesman->pincode,
                ];
            }

            DB::commit();

            return $this->success([
                'vendor_id' => $vendor->id,
                'user_id' => $user->id,
                'documents_uploaded' => count($documents),
                'assigned_salesman' => $assignmentInfo,
                'message' => $assignedSalesman
                    ? 'Vendor registration successful. Auto-assigned to salesman based on pincode.'
                    : 'Vendor registration successful. Waiting for admin approval.',
            ], 'vendor.register.pending');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Vendor registration error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return $this->error('api.server_error', 500);
        }
    }

    /**
     * Send OTP to vendor mobile number
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string|regex:/^[0-9]{10}$/',
        ]);

        $mobile = $request->input('mobile_number');
        $key = 'vendor-send-otp:' . $mobile;

        // Rate limiting: max 5 requests per minute
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return $this->error('api.otp_rate_limit', 429);
        }

        RateLimiter::hit($key, 60);

        // Check if vendor exists
        $user = User::where('mobile', $mobile)
            ->where('user_type', 'vendor')
            ->first();

        if (!$user) {
            return $this->error('vendor.not_found', 404);
        }

        // Check if vendor is approved
        $vendor = $user->vendor;
        if (!$vendor) {
            return $this->error('vendor.login.not_approved', 403);
        }

        // Check approval status
        if (
            $user->vendor_status !== 'approved' ||
            $vendor->status !== 'approved' ||
            !$user->is_active
        ) {
            return $this->error('vendor.login.not_approved', 403);
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
        ], 'vendor.otp_sent');
    }

    /**
     * Verify OTP and login vendor
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

        // Find user
        $user = User::where('mobile', $mobile)
            ->where('user_type', 'vendor')
            ->first();

        if (!$user) {
            return $this->error('vendor.not_found', 404);
        }

        // Check if vendor is approved
        $vendor = $user->vendor;
        if (!$vendor) {
            return $this->error('vendor.login.not_approved', 403);
        }

        // Check approval status
        if (
            $user->vendor_status !== 'approved' ||
            $vendor->status !== 'approved' ||
            !$user->is_active
        ) {
            return $this->error('vendor.login.not_approved', 403);
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
        $token = $user->createToken('vendor-app')->plainTextToken;

        return $this->success([
            'user' => [
                'id' => $user->id,
                'mobile' => $user->mobile,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
            ],
            'vendor' => [
                'id' => $vendor->id,
                'vendor_name' => $vendor->vendor_name,
                'shop_name' => $vendor->shop_name,
            ],
            'token' => $token,
        ], 'vendor.login.success');
    }

    /**
     * Vendor Login (OTP) - Legacy method, kept for backward compatibility
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

        // Find user
        $user = User::where('mobile', $mobile)
            ->where('user_type', 'vendor')
            ->first();

        if (!$user) {
            return $this->error('api.not_found', 404);
        }

        // Check if vendor is approved
        $vendor = $user->vendor;
        if (!$vendor) {
            return $this->error('vendor.login.not_approved', 403);
        }

        // Check approval status
        if (
            $user->vendor_status !== 'approved' ||
            $vendor->status !== 'approved' ||
            !$user->is_active
        ) {
            return $this->error('vendor.login.not_approved', 403);
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
        $token = $user->createToken('vendor-app')->plainTextToken;

        return $this->success([
            'user' => [
                'id' => $user->id,
                'mobile' => $user->mobile,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
            ],
            'vendor' => [
                'id' => $vendor->id,
                'vendor_name' => $vendor->vendor_name,
                'shop_name' => $vendor->shop_name,
            ],
            'token' => $token,
        ], 'vendor.login.success');
    }

    /**
     * Auto-assign salesman to vendor (Mobile App API)
     * POST /api/vendor/auto-assign-salesman
     * 
     * This API can be called after vendor registration to auto-assign a salesman
     * based on pincode matching. Accepts vendor_id in form data.
     */
    public function autoAssignSalesman(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
        ]);

        try {
            $vendor = Vendor::findOrFail($request->vendor_id);

            // Check if vendor is already assigned
            if ($vendor->assigned_salesman_id && $vendor->verification_status === 'assigned') {
                return $this->success([
                    'vendor_id' => $vendor->id,
                    'salesman' => [
                        'id' => $vendor->assignedSalesman->id,
                        'name' => $vendor->assignedSalesman->name,
                        'mobile' => $vendor->assignedSalesman->mobile,
                        'pincode' => $vendor->assignedSalesman->pincode,
                    ],
                    'message' => 'Vendor is already assigned to a salesman.',
                    'already_assigned' => true,
                ], 'vendor.already_assigned');
            }

            $assignmentService = new VendorAssignmentService();
            $assignedSalesman = $assignmentService->autoAssignByPincode($vendor);

            if (!$assignedSalesman) {
                return $this->success([
                    'vendor_id' => $vendor->id,
                    'salesman' => null,
                    'message' => 'No active salesman found with matching pincode. Please contact admin.',
                    'assigned' => false,
                ], 'vendor.auto_assign.no_salesman_found');
            }

            return $this->success([
                'vendor_id' => $vendor->id,
                'salesman' => [
                    'id' => $assignedSalesman->id,
                    'name' => $assignedSalesman->name,
                    'mobile' => $assignedSalesman->mobile,
                    'pincode' => $assignedSalesman->pincode,
                    'pending_verifications' => $assignmentService->getPendingVerificationCount($assignedSalesman),
                ],
                'message' => 'Vendor auto-assigned to salesman successfully.',
                'assigned' => true,
            ], 'vendor.auto_assigned');
        } catch (\Exception $e) {
            Log::error('Vendor auto-assign salesman error: ' . $e->getMessage(), [
                'vendor_id' => $request->vendor_id,
                'exception' => $e,
            ]);
            return $this->error('api.server_error', 500);
        }
    }

    /**
     * Get Categories List (Vendor API)
     * GET /api/vendor/categories
     * 
     * Returns all active categories for vendor registration/selection
     */
    public function categories(Request $request)
    {
        try {
            // Get all active categories
            $categories = Category::where('status', 'active')
                ->orderBy('name', 'asc')
                ->whereNull('parent_id')
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'parent_id' => $category->parent_id,
                        'image_url' => $category->image_url,
                        'icon_url' => $category->icon_url,
                    ];
                });

            // Optionally return with hierarchy (parent-child structure)
            if ($request->has('hierarchy') && $request->hierarchy == 'true') {
                $parentCategories = Category::where('status', 'active')
                    ->whereNull('parent_id')
                    ->with(['children' => function ($query) {
                        $query->where('status', 'active')->orderBy('name', 'asc');
                    }])
                    ->orderBy('name', 'asc')
                    ->get()
                    ->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                            'slug' => $category->slug,
                            'parent_id' => $category->parent_id,
                            'image_url' => $category->image_url,
                            'icon_url' => $category->icon_url,
                            'children' => $category->children->map(function ($child) {
                                return [
                                    'id' => $child->id,
                                    'name' => $child->name,
                                    'slug' => $child->slug,
                                    'parent_id' => $child->parent_id,
                                    'image_url' => $child->image_url,
                                    'icon_url' => $child->icon_url,
                                ];
                            }),
                        ];
                    });

                return $this->success([
                    'categories' => $parentCategories,
                    'count' => $parentCategories->count(),
                    'format' => 'hierarchy',
                ], 'api.categories_fetched');
            }

            // Return flat list
            return $this->success([
                'categories' => $categories,
                'count' => $categories->count(),
                'format' => 'flat',
            ], 'api.categories_fetched');
        } catch (\Exception $e) {
            Log::error('Vendor categories fetch error: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            return $this->error('api.server_error', 500);
        }
    }

    /**
     * Get categories list for vendor registration
     * GET /api/vendor/categories
     */
    // public function categories()
    // {
    //     try {
    //         // Get all active categories (parent categories only for vendor selection)
    //         $categories = Category::where('status', 'active')
    //             ->whereNull('parent_id')
    //             ->select('id', 'name', 'slug', 'status')
    //             ->orderBy('name', 'asc')
    //             ->get();

    //         return $this->success([
    //             'categories' => $categories,
    //             'count' => $categories->count(),
    //         ], 'vendor.categories_fetched');
    //     } catch (\Exception $e) {
    //         Log::error('Vendor categories fetch error: ' . $e->getMessage(), [
    //             'exception' => $e,
    //             'trace' => $e->getTraceAsString()
    //         ]);
    //         return $this->error('api.server_error', 500);
    //     }
    // }
}
