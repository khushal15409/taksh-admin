<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignSalesmanRequest;
use App\Models\Vendor;
use App\Models\User;
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
     * NOTE: Manual assignment is deprecated. This view now shows pending vendors.
     * Salesmen will see nearby vendors automatically based on location.
     */
    public function index(Request $request)
    {
        $vendors = Vendor::with(['user', 'state', 'city'])
            ->where('verification_status', 'pending')
            ->where('status', 'pending')
            ->whereNotNull('shop_latitude')
            ->whereNotNull('shop_longitude')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin-views.vendor-assignment.index', compact('vendors'));
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
            'category',
            'assignedSalesman',
            'verifications',
            'documents'
        ])->findOrFail($id);

        // Get all salesmen
        $salesmen = User::where('user_type', 'salesman')
            ->where('is_active', true)
            ->get();

        return view('admin-views.vendor-assignment.show', compact('vendor', 'salesmen'));
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
}
