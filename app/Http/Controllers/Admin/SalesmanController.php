<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SalesmanProfile;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class SalesmanController extends Controller
{
    /**
     * Display a listing of salesmen
     */
    public function index()
    {
        $salesmen = User::where('user_type', 'salesman')
            ->with(['salesmanProfile.state', 'salesmanProfile.city', 'location'])
            ->withCount('assignedVendors')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin-views.salesmen.index', compact('salesmen'));
    }

    /**
     * Show the form for creating a new salesman
     */
    public function create()
    {
        $states = State::orderBy('name')->get();
        $cities = City::orderBy('name')->get();

        return view('admin-views.salesmen.create', compact('states', 'cities'));
    }

    /**
     * Store a newly created salesman
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile_number' => 'required|string|regex:/^[0-9]{10}$/|unique:users,mobile|unique:salesmen_profiles,mobile_number',
            'email' => 'nullable|email|max:255',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Create user account
            $user = User::create([
                'mobile' => $request->mobile_number,
                'name' => $request->name,
                'email' => $request->email,
                'user_type' => 'salesman',
                'is_active' => $request->is_active ?? true,
                'is_verified' => true,
                'status' => 'active',
            ]);

            // Assign salesman role
            $salesmanRole = Role::firstOrCreate(['name' => 'salesman']);
            $user->assignRole($salesmanRole);

            // Create salesman profile
            SalesmanProfile::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'mobile_number' => $request->mobile_number,
                'email' => $request->email,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'is_active' => $request->is_active ?? true,
            ]);

            DB::commit();

            return redirect()->route('admin.salesmen.index')
                ->with('success', 'Salesman created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Salesman creation error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create salesman. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified salesman
     */
    public function edit($id)
    {
        $salesman = User::where('user_type', 'salesman')
            ->with('salesmanProfile')
            ->findOrFail($id);

        $states = State::orderBy('name')->get();
        $cities = City::orderBy('name')->get();

        return view('admin-views.salesmen.edit', compact('salesman', 'states', 'cities'));
    }

    /**
     * Update the specified salesman
     */
    public function update(Request $request, $id)
    {
        $salesman = User::where('user_type', 'salesman')
            ->with('salesmanProfile')
            ->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Update user
            $salesman->update([
                'name' => $request->name,
                'is_active' => $request->is_active ?? $salesman->is_active,
            ]);

            // Update or create profile
            if ($salesman->salesmanProfile) {
                $salesman->salesmanProfile->update([
                    'name' => $request->name,
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                    'is_active' => $request->is_active ?? $salesman->salesmanProfile->is_active,
                ]);
            } else {
                SalesmanProfile::create([
                    'user_id' => $salesman->id,
                    'name' => $request->name,
                    'mobile_number' => $salesman->mobile,
                    'email' => $salesman->email,
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                    'is_active' => $request->is_active ?? true,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.salesmen.index')
                ->with('success', 'Salesman updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Salesman update error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update salesman. Please try again.');
        }
    }

    /**
     * Toggle salesman status (Enable/Disable)
     */
    public function toggleStatus($id)
    {
        try {
            $salesman = User::where('user_type', 'salesman')
                ->with('salesmanProfile')
                ->findOrFail($id);

            DB::beginTransaction();

            $newStatus = !$salesman->is_active;

            // Update user status
            $salesman->update(['is_active' => $newStatus]);

            // Update profile status if exists
            if ($salesman->salesmanProfile) {
                $salesman->salesmanProfile->update(['is_active' => $newStatus]);
            }

            DB::commit();

            $message = $newStatus ? 'Salesman enabled successfully.' : 'Salesman disabled successfully.';
            return redirect()->route('admin.salesmen.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Salesman status toggle error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->with('error', 'Failed to update salesman status. Please try again.');
        }
    }
}
