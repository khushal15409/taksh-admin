<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class VendorApprovalController extends Controller
{
    use ApiResponseTrait;

    /**
     * Approve Vendor (API)
     */
    public function approve(Request $request, $vendorId)
    {
        try {
            $vendor = Vendor::findOrFail($vendorId);

            // Check if vendor is verified
            if ($vendor->verification_status !== 'verified') {
                return $this->error('vendor.approval.not_verified', 400);
            }

            DB::beginTransaction();

            $user = $vendor->user;
            $admin = $request->user();

            // Update vendor status (keep verification_status as 'verified', only update status to 'approved')
            $vendor->update([
                'status' => 'approved',
                'approved_by' => $admin->id,
                'approved_at' => now(),
            ]);

            // Update user status
            $user->update([
                'vendor_status' => 'approved',
                'is_active' => true,
            ]);

            // Ensure vendor role is assigned
            $vendorRole = Role::firstOrCreate(['name' => 'vendor']);
            if (!$user->hasRole('vendor')) {
                $user->assignRole($vendorRole);
            }

            DB::commit();

            return $this->success([
                'vendor_id' => $vendor->id,
                'message' => 'Vendor approved successfully.',
            ], 'vendor.approved');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('api.server_error', 500);
        }
    }

    /**
     * Reject Vendor (API)
     */
    public function reject(Request $request, $vendorId)
    {
        try {
            $vendor = Vendor::findOrFail($vendorId);

            DB::beginTransaction();

            $user = $vendor->user;

            // Update vendor status
            $vendor->update([
                'status' => 'rejected',
                'verification_status' => 'rejected',
            ]);

            // Update user status
            $user->update([
                'vendor_status' => 'rejected',
                'is_active' => false,
            ]);

            DB::commit();

            return $this->success([
                'vendor_id' => $vendor->id,
                'message' => 'Vendor rejected successfully.',
            ], 'vendor.rejected');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('api.server_error', 500);
        }
    }

    /**
     * List Vendors for Approval (Web View)
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'verified');

        $query = Vendor::with(['user', 'state', 'city', 'verifiedBy']);

        if ($status === 'verified') {
            // Show vendors verified by salesman, waiting for admin approval
            $query->where('verification_status', 'verified')->where('status', 'pending');
        } elseif ($status === 'approved') {
            $query->where('status', 'approved');
        } elseif ($status === 'rejected') {
            $query->where('status', 'rejected');
        } elseif ($status === 'pending') {
            // Show pending vendors (not yet verified)
            $query->where('verification_status', 'pending')->where('status', 'pending');
        }
        // If status is not specified or is 'all', show all vendors

        $vendors = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin-views.vendor-approval.index', compact('vendors', 'status'));
    }

    /**
     * Show Vendor Details (Web View)
     */
    public function show($id)
    {
        $vendor = Vendor::with([
            'user',
            'state',
            'city',
            'assignedSalesman',
            'verifiedBy',
            'approvedBy',
            'verifications.salesman',
            'documents'
        ])->findOrFail($id);

        // Note: category is not a relationship, it's a method that returns first category
        // Access it via $vendor->category or $vendor->category_models for multiple categories

        return view('admin-views.vendor-approval.show', compact('vendor'));
    }

    /**
     * Approve Vendor (Web View)
     */
    public function approveWeb(Request $request, $id)
    {
        try {
            $vendor = Vendor::findOrFail($id);

            if ($vendor->verification_status !== 'verified') {
                return redirect()->back()->with('error', 'Vendor must be verified before approval.');
            }

            // Get vendor user
            $user = User::find($vendor->user_id);
            if (!$user) {
                return redirect()->back()->with('error', 'Vendor user not found.');
            }

            $admin = auth()->user();
            if (!$admin) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            DB::beginTransaction();

            $vendor->update([
                'status' => 'approved',
                'approved_by' => $admin->id,
                'approved_at' => now(),
            ]);

            $user->update([
                'vendor_status' => 'approved',
                'is_active' => true,
            ]);

            $vendorRole = Role::firstOrCreate(['name' => 'vendor']);
            if (!$user->hasRole('vendor')) {
                $user->assignRole($vendorRole);
            }

            DB::commit();

            return redirect()->route('admin.vendor.approval.index')
                ->with('success', 'Vendor approved successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::error('Vendor not found: ' . $id);
            return redirect()->back()->with('error', 'Vendor not found.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Vendor approval error: ' . $e->getMessage(), [
                'exception' => $e,
                'vendor_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to approve vendor: ' . $e->getMessage());
        }
    }

    /**
     * Reject Vendor (Web View)
     */
    public function rejectWeb(Request $request, $id)
    {
        try {
            $vendor = Vendor::findOrFail($id);

            $user = User::find($vendor->user_id);
            if (!$user) {
                return redirect()->back()->with('error', 'Vendor user not found.');
            }

            DB::beginTransaction();

            $vendor->update([
                'status' => 'rejected',
                'verification_status' => 'rejected',
            ]);

            $user->update([
                'vendor_status' => 'rejected',
                'is_active' => false,
            ]);

            DB::commit();

            return redirect()->route('admin.vendor.approval.index')
                ->with('success', 'Vendor rejected successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::error('Vendor not found: ' . $id);
            return redirect()->back()->with('error', 'Vendor not found.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Vendor rejection error: ' . $e->getMessage(), [
                'exception' => $e,
                'vendor_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to reject vendor: ' . $e->getMessage());
        }
    }
}
