<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryMan;
use App\Models\User;
use App\Models\FulfillmentCenter;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use App\CentralLogics\Helpers;

class DeliveryManController extends Controller
{
    /**
     * Display all delivery men list
     */
    public function index(Request $request)
    {
        $query = DeliveryMan::with(['user', 'fulfillmentCenter', 'state', 'city', 'approvedBy']);

        // Apply filters
        $this->applyFilters($query, $request);

        $deliveryMen = $query->orderBy('created_at', 'desc')->paginate(25);

        // Get filter options
        $fulfillmentCenters = FulfillmentCenter::where('status', 'active')->orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $cities = City::orderBy('name')->get();

        return view('admin-views.delivery-men.index', compact('deliveryMen', 'fulfillmentCenters', 'states', 'cities'));
    }

    /**
     * Display approved delivery men
     */
    public function approved(Request $request)
    {
        $request->merge(['status' => 'approved']);
        return $this->index($request);
    }

    /**
     * Display rejected delivery men
     */
    public function rejected(Request $request)
    {
        $request->merge(['status' => 'rejected']);
        return $this->index($request);
    }

    /**
     * Display pending delivery men (new joining requests)
     */
    public function pending(Request $request)
    {
        $query = DeliveryMan::with(['user', 'fulfillmentCenter', 'state', 'city'])
            ->where('status', 'pending');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('mobile_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $deliveryMen = $query->orderBy('created_at', 'desc')->paginate(25);

        return view('admin-views.delivery-men.pending', compact('deliveryMen'));
    }

    /**
     * Show the form for creating a new delivery man (Admin Add)
     */
    public function create()
    {
        $fulfillmentCenters = FulfillmentCenter::where('status', 'active')->orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $cities = City::orderBy('name')->get();

        return view('admin-views.delivery-men.create', compact('fulfillmentCenters', 'states', 'cities'));
    }

    /**
     * Store a newly created delivery man (Admin creates directly)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email|max:255',
            'mobile_number' => 'required|string|regex:/^[0-9]{10}$/|unique:users,mobile|unique:delivery_men,mobile_number',
            'address' => 'nullable|string|max:255',
            'pincode' => 'nullable|string|max:10',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'fulfillment_center_id' => 'required|exists:fulfillment_centers,id',
            'vehicle_type' => 'required|in:bike,cycle,scooter',
            'vehicle_number' => 'nullable|string|max:255',
            'driving_license_number' => 'required|string|max:255',
            'aadhaar_number' => 'required|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'aadhaar_front' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'aadhaar_back' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'driving_license_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Create User entry - Admin created, so approved and active
            $user = User::create([
                'name' => $request->name,
                'mobile' => $request->mobile_number,
                'email' => $request->email,
                'user_type' => 'delivery_man',
                'is_active' => true, // Admin created = active
                'is_verified' => true, // Admin created = verified
                'status' => 'active', // Admin created = active
            ]);

            // Handle file uploads
            $profilePhotoName = $request->hasFile('profile_photo')
                ? Helpers::upload('delivery-man/profile/', 'png', $request->file('profile_photo'))
                : null;
            $aadhaarFrontName = Helpers::upload('delivery-man/aadhaar/', 'png', $request->file('aadhaar_front'));
            $aadhaarBackName = Helpers::upload('delivery-man/aadhaar/', 'png', $request->file('aadhaar_back'));
            $drivingLicensePhotoName = Helpers::upload('delivery-man/driving-license/', 'png', $request->file('driving_license_photo'));

            // Create DeliveryMan entry - Admin created = approved
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
                'vehicle_number' => $request->vehicle_number,
                'driving_license_number' => $request->driving_license_number,
                'aadhaar_number' => $request->aadhaar_number,
                'profile_photo' => $profilePhotoName,
                'aadhaar_front' => $aadhaarFrontName,
                'aadhaar_back' => $aadhaarBackName,
                'driving_license_photo' => $drivingLicensePhotoName,
                'status' => 'approved', // Admin created = approved
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // Assign Spatie role
            $role = Role::firstOrCreate(['name' => 'delivery-man', 'guard_name' => 'web']);
            $user->assignRole($role);

            DB::commit();

            return redirect()->route('admin.delivery-men.index')
                ->with('success', translate('messages.delivery_men.approved'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delivery man creation failed: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create delivery man: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified delivery man
     */
    public function show($id)
    {
        $deliveryMan = DeliveryMan::with([
            'user',
            'fulfillmentCenter',
            'state',
            'city',
            'approvedBy',
        ])->findOrFail($id);

        return view('admin-views.delivery-men.show', compact('deliveryMan'));
    }

    /**
     * Approve pending delivery man
     */
    public function approve(Request $request, $id)
    {
        try {
            $deliveryMan = DeliveryMan::with('user')->findOrFail($id);

            if ($deliveryMan->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending delivery men can be approved.');
            }

            $admin = Auth::user();
            if (!$admin) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            DB::beginTransaction();

            // Update delivery man status
            $deliveryMan->update([
                'status' => 'approved',
                'approved_by' => $admin->id,
                'approved_at' => now(),
            ]);

            // Activate user
            $user = $deliveryMan->user;
            $user->update([
                'is_active' => true,
                'status' => 'active',
            ]);

            // Ensure delivery-man role is assigned
            $role = Role::firstOrCreate(
                ['name' => 'delivery-man'],
                ['guard_name' => 'web']
            );
            if (!$user->hasRole('delivery-man')) {
                $user->assignRole($role);
            }

            DB::commit();

            return redirect()->route('admin.delivery-men.pending')
                ->with('success', translate('messages.delivery_men.approved'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delivery man approval error: ' . $e->getMessage(), [
                'exception' => $e,
                'delivery_man_id' => $id,
            ]);
            return redirect()->back()->with('error', 'Failed to approve delivery man: ' . $e->getMessage());
        }
    }

    /**
     * Reject pending delivery man
     */
    public function reject(Request $request, $id)
    {
        try {
            $deliveryMan = DeliveryMan::with('user')->findOrFail($id);

            if ($deliveryMan->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending delivery men can be rejected.');
            }

            DB::beginTransaction();

            $admin = Auth::user();

            // Update delivery man status
            $deliveryMan->update([
                'status' => 'rejected',
                'approved_by' => $admin ? $admin->id : null, // Admin who rejected
                'approved_at' => now(),
            ]);

            // Deactivate user
            $user = $deliveryMan->user;
            $user->update([
                'is_active' => false,
                'status' => 'blocked',
            ]);

            DB::commit();

            return redirect()->route('admin.delivery-men.pending')
                ->with('success', translate('messages.delivery_men.rejected'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delivery man rejection error: ' . $e->getMessage(), [
                'exception' => $e,
                'delivery_man_id' => $id,
            ]);
            return redirect()->back()->with('error', 'Failed to reject delivery man: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified delivery man
     */
    public function edit($id)
    {
        $deliveryMan = DeliveryMan::with(['user', 'fulfillmentCenter', 'state', 'city'])->findOrFail($id);
        $fulfillmentCenters = FulfillmentCenter::where('status', 'active')->orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $cities = City::orderBy('name')->get();

        return view('admin-views.delivery-men.edit', compact('deliveryMan', 'fulfillmentCenters', 'states', 'cities'));
    }

    /**
     * Update the specified delivery man
     */
    public function update(Request $request, $id)
    {
        $deliveryMan = DeliveryMan::with('user')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $deliveryMan->user_id . ',id',
            'mobile_number' => 'required|string|regex:/^[0-9]{10}$/|unique:users,mobile,' . $deliveryMan->user_id . ',id|unique:delivery_men,mobile_number,' . $id . ',id',
            'address' => 'nullable|string|max:255',
            'pincode' => 'nullable|string|max:10',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'fulfillment_center_id' => 'required|exists:fulfillment_centers,id',
            'vehicle_type' => 'required|in:bike,cycle,scooter',
            'vehicle_number' => 'nullable|string|max:255',
            'driving_license_number' => 'required|string|max:255',
            'aadhaar_number' => 'required|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'aadhaar_front' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'aadhaar_back' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'driving_license_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Update User
            $user = $deliveryMan->user;
            $user->update([
                'name' => $request->name,
                'mobile' => $request->mobile_number,
                'email' => $request->email,
            ]);

            // Handle file uploads (only if new files are provided)
            $profilePhotoName = $deliveryMan->profile_photo;
            if ($request->hasFile('profile_photo')) {
                // Delete old file if exists
                if ($profilePhotoName && file_exists(public_path('storage/' . $profilePhotoName))) {
                    unlink(public_path('storage/' . $profilePhotoName));
                }
                $profilePhotoName = Helpers::upload('delivery-man/profile/', 'png', $request->file('profile_photo'));
            }

            $aadhaarFrontName = $deliveryMan->aadhaar_front;
            if ($request->hasFile('aadhaar_front')) {
                if ($aadhaarFrontName && file_exists(public_path('storage/' . $aadhaarFrontName))) {
                    unlink(public_path('storage/' . $aadhaarFrontName));
                }
                $aadhaarFrontName = Helpers::upload('delivery-man/aadhaar/', 'png', $request->file('aadhaar_front'));
            }

            $aadhaarBackName = $deliveryMan->aadhaar_back;
            if ($request->hasFile('aadhaar_back')) {
                if ($aadhaarBackName && file_exists(public_path('storage/' . $aadhaarBackName))) {
                    unlink(public_path('storage/' . $aadhaarBackName));
                }
                $aadhaarBackName = Helpers::upload('delivery-man/aadhaar/', 'png', $request->file('aadhaar_back'));
            }

            $drivingLicensePhotoName = $deliveryMan->driving_license_photo;
            if ($request->hasFile('driving_license_photo')) {
                if ($drivingLicensePhotoName && file_exists(public_path('storage/' . $drivingLicensePhotoName))) {
                    unlink(public_path('storage/' . $drivingLicensePhotoName));
                }
                $drivingLicensePhotoName = Helpers::upload('delivery-man/driving-license/', 'png', $request->file('driving_license_photo'));
            }

            // Update DeliveryMan
            $deliveryMan->update([
                'fulfillment_center_id' => $request->fulfillment_center_id,
                'name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
                'address' => $request->address,
                'pincode' => $request->pincode,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'vehicle_type' => $request->vehicle_type,
                'vehicle_number' => $request->vehicle_number,
                'driving_license_number' => $request->driving_license_number,
                'aadhaar_number' => $request->aadhaar_number,
                'profile_photo' => $profilePhotoName,
                'aadhaar_front' => $aadhaarFrontName,
                'aadhaar_back' => $aadhaarBackName,
                'driving_license_photo' => $drivingLicensePhotoName,
            ]);

            DB::commit();

            return redirect()->route('admin.delivery-men.show', $id)
                ->with('success', 'Delivery man updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delivery man update failed: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update delivery man: ' . $e->getMessage());
        }
    }

    /**
     * Toggle delivery man status (activate/deactivate)
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $deliveryMan = DeliveryMan::with('user')->findOrFail($id);

            if ($deliveryMan->status !== 'approved') {
                return redirect()->back()->with('error', 'Only approved delivery men can be activated/deactivated.');
            }

            DB::beginTransaction();

            $user = $deliveryMan->user;
            $user->update([
                'is_active' => !$user->is_active,
            ]);

            DB::commit();

            $message = $user->is_active
                ? 'Delivery man activated successfully.'
                : 'Delivery man deactivated successfully.';

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delivery man status toggle error: ' . $e->getMessage(), [
                'exception' => $e,
                'delivery_man_id' => $id,
            ]);
            return redirect()->back()->with('error', 'Failed to toggle delivery man status: ' . $e->getMessage());
        }
    }

    /**
     * Assign/Change Fulfillment Center for delivery man
     */
    public function assignFulfillmentCenter(Request $request, $id)
    {
        $request->validate([
            'fulfillment_center_id' => 'required|exists:fulfillment_centers,id',
        ]);

        try {
            $deliveryMan = DeliveryMan::findOrFail($id);

            DB::beginTransaction();

            $deliveryMan->update([
                'fulfillment_center_id' => $request->fulfillment_center_id,
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Fulfillment center assigned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Fulfillment center assignment error: ' . $e->getMessage(), [
                'exception' => $e,
                'delivery_man_id' => $id,
            ]);
            return redirect()->back()->with('error', 'Failed to assign fulfillment center: ' . $e->getMessage());
        }
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, Request $request)
    {
        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by name, mobile_number, or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('mobile_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by fulfillment center
        if ($request->filled('fulfillment_center_id')) {
            $query->where('fulfillment_center_id', $request->fulfillment_center_id);
        }

        // Filter by city
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Filter by state
        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
    }
}
