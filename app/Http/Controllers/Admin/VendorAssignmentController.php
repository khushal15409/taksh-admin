<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignSalesmanRequest;
use App\Models\Vendor;
use App\Models\User;
use App\Services\VendorAssignmentService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VendorAssignmentController extends Controller
{
    use ApiResponseTrait;

    /**
     * Assign Salesman to Vendor (API)
     */
    public function assignSalesman(AssignSalesmanRequest $request, $vendorId)
    {
        try {
            $vendor = Vendor::findOrFail($vendorId);

            // Check if salesman exists and has salesman user_type
            $salesman = User::where('id', $request->salesman_id)
                ->where('user_type', 'salesman')
                ->first();

            if (!$salesman) {
                return $this->error('api.not_found', 404);
            }

            DB::beginTransaction();

            // Assign salesman
            $vendor->update([
                'assigned_salesman_id' => $request->salesman_id,
                'verification_status' => 'assigned',
            ]);

            DB::commit();

            return $this->success([
                'vendor_id' => $vendor->id,
                'salesman_id' => $salesman->id,
                'message' => 'Salesman assigned successfully.',
            ], 'vendor.assigned_to_salesman');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('api.server_error', 500);
        }
    }

    /**
     * List Vendors for Assignment (Web View)
     * NOTE: Vendors are now auto-assigned based on pincode matching.
     * This view shows pending and assigned vendors.
     */
    public function index(Request $request)
    {
        $vendors = Vendor::with(['user', 'state', 'city', 'assignedSalesman'])
            ->whereIn('verification_status', ['pending', 'assigned'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Load pending counts for assigned salesmen
        $assignmentService = new VendorAssignmentService();
        $vendors->getCollection()->transform(function ($vendor) use ($assignmentService) {
            if ($vendor->assignedSalesman) {
                $vendor->assignedSalesman->pending_count = $assignmentService->getPendingVerificationCount($vendor->assignedSalesman);
            }
            return $vendor;
        });

        return view('admin-views.vendor-assignment.index', compact('vendors'));
    }

    /**
     * Show Vendor Details (Web View)
     */
    public function show($id)
    {
        try {
            $vendor = Vendor::with([
                'user',
                'state',
                'city',
                'assignedSalesman',
                'verifications',
                'documents'
            ])->findOrFail($id);
            
            // Note: category is not a relationship, it's a method that returns first category
            // So we don't eager load it

            // Get all salesmen
            $salesmen = User::where('user_type', 'salesman')
                ->where('is_active', true)
                ->get();

            // Get pending verification count for assigned salesman
            $assignmentService = new VendorAssignmentService();
            $pendingCount = null;
            
            if ($vendor->assignedSalesman) {
                try {
                    $pendingCount = $assignmentService->getPendingVerificationCount($vendor->assignedSalesman);
                } catch (\Exception $e) {
                    Log::warning('Error getting pending count: ' . $e->getMessage());
                    $pendingCount = 0;
                }
            }

            return view('admin-views.vendor-assignment.show', compact('vendor', 'salesmen', 'pendingCount'));
        } catch (\Exception $e) {
            Log::error('Vendor assignment show error: ' . $e->getMessage(), [
                'vendor_id' => $id,
                'exception' => $e,
            ]);
            return redirect()->route('admin.vendor.assignment.index')
                ->with('error', 'Vendor not found or error loading vendor details.');
        }
    }

    /**
     * Assign Salesman (Web View)
     */
    public function assign(Request $request, $id)
    {
        $request->validate([
            'salesman_id' => 'required|exists:users,id',
        ]);

        try {
            $vendor = Vendor::findOrFail($id);

            $salesman = User::where('id', $request->salesman_id)
                ->where('user_type', 'salesman')
                ->first();

            if (!$salesman) {
                return redirect()->back()->with('error', 'Invalid salesman selected.');
            }

            DB::beginTransaction();

            $vendor->update([
                'assigned_salesman_id' => $request->salesman_id,
                'verification_status' => 'assigned',
            ]);

            DB::commit();

            return redirect()->route('admin.vendor.assignment.index')
                ->with('success', 'Salesman assigned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Vendor assignment error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Failed to assign salesman. Please try again.');
        }
    }

    /**
     * Auto-assign salesman to vendor using pincode-based assignment
     * Works for both API and Web requests
     */
    public function autoAssignSalesman(Request $request, $vendorId)
    {
        try {
            $vendor = Vendor::findOrFail($vendorId);

            $assignmentService = new VendorAssignmentService();
            $assignedSalesman = $assignmentService->autoAssignByPincode($vendor);

            if (!$assignedSalesman) {
                // Handle web request
                if (!$request->expectsJson()) {
                    return redirect()->back()
                        ->with('error', 'No active salesman found with matching pincode.');
                }
                // Handle API request
                return $this->error('vendor.auto_assign.no_salesman_found', 404);
            }

            // Handle web request
            if (!$request->expectsJson()) {
                return redirect()->route('admin.vendor.assignment.show', $vendor->id)
                    ->with('success', 'Vendor re-assigned to salesman successfully.');
            }

            // Handle API request
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
            ], 'vendor.auto_assigned');
        } catch (\Exception $e) {
            Log::error('Auto-assign salesman error: ' . $e->getMessage(), [
                'vendor_id' => $vendorId,
                'exception' => $e,
            ]);
            
            // Handle web request
            if (!$request->expectsJson()) {
                return redirect()->back()
                    ->with('error', 'Failed to re-assign salesman. Please try again.');
            }
            
            // Handle API request
            return $this->error('api.server_error', 500);
        }
    }
}
