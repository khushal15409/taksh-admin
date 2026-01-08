<?php

namespace App\Http\Controllers\Admin\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\Miniwarehouse;
use App\Models\LmCenter;
use App\Models\FmRtCenter;
use App\Models\Pincode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendingMappingController extends Controller
{
    /**
     * Display a listing of pending mappings.
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'warehouse');
        
        // Get pending warehouses (not mapped to any warehouse, miniwarehouse, lm center, or fm/rt center)
        // A warehouse is considered "pending" if it has NO mappings at all
        $mappedWarehouseIds = collect();
        
        // Check warehouse_warehouse table (both directions)
        if (DB::getSchemaBuilder()->hasTable('warehouse_warehouse')) {
            $mappedWarehouseIds = $mappedWarehouseIds->merge(DB::table('warehouse_warehouse')->pluck('warehouse_id'));
            $mappedWarehouseIds = $mappedWarehouseIds->merge(DB::table('warehouse_warehouse')->pluck('mapped_warehouse_id'));
        }
        
        // Check warehouse_miniwarehouse table
        if (DB::getSchemaBuilder()->hasTable('warehouse_miniwarehouse')) {
            $mappedWarehouseIds = $mappedWarehouseIds->merge(DB::table('warehouse_miniwarehouse')->pluck('warehouse_id'));
        }
        
        // Check warehouse_lm_center table
        if (DB::getSchemaBuilder()->hasTable('warehouse_lm_center')) {
            $mappedWarehouseIds = $mappedWarehouseIds->merge(DB::table('warehouse_lm_center')->pluck('warehouse_id'));
        }
        
        // Check warehouse_fm_rt_center table
        if (DB::getSchemaBuilder()->hasTable('warehouse_fm_rt_center')) {
            $mappedWarehouseIds = $mappedWarehouseIds->merge(DB::table('warehouse_fm_rt_center')->pluck('warehouse_id'));
        }
        
        $mappedWarehouseIds = $mappedWarehouseIds->unique()->toArray();
        
        $pendingWarehouses = Warehouse::where('status', 1)
            ->whereNotIn('id', $mappedWarehouseIds)
            ->latest()
            ->get();
        
        // Get pending miniwarehouses (not mapped to any warehouse, lm center, or fm/rt center)
        $mappedMiniwarehouseIds = collect();
        
        // Check warehouse_miniwarehouse table
        if (DB::getSchemaBuilder()->hasTable('warehouse_miniwarehouse')) {
            $mappedMiniwarehouseIds = $mappedMiniwarehouseIds->merge(DB::table('warehouse_miniwarehouse')->pluck('miniwarehouse_id'));
        }
        
        // Check miniwarehouse_lm_center table
        if (DB::getSchemaBuilder()->hasTable('miniwarehouse_lm_center')) {
            $mappedMiniwarehouseIds = $mappedMiniwarehouseIds->merge(DB::table('miniwarehouse_lm_center')->pluck('miniwarehouse_id'));
        }
        
        // Check miniwarehouse_fm_rt_center table
        if (DB::getSchemaBuilder()->hasTable('miniwarehouse_fm_rt_center')) {
            $mappedMiniwarehouseIds = $mappedMiniwarehouseIds->merge(DB::table('miniwarehouse_fm_rt_center')->pluck('miniwarehouse_id'));
        }
        
        $mappedMiniwarehouseIds = $mappedMiniwarehouseIds->unique()->toArray();
        
        $pendingMiniwarehouses = Miniwarehouse::where('status', 1)
            ->whereNotIn('id', $mappedMiniwarehouseIds)
            ->latest()
            ->get();
        
        // Get pending LM Centers (not mapped to any warehouse, miniwarehouse, fm/rt center, or pincode)
        $mappedLmCenterIds = collect();
        
        // Check warehouse_lm_center table
        if (DB::getSchemaBuilder()->hasTable('warehouse_lm_center')) {
            $mappedLmCenterIds = $mappedLmCenterIds->merge(DB::table('warehouse_lm_center')->pluck('lm_center_id'));
        }
        
        // Check miniwarehouse_lm_center table
        if (DB::getSchemaBuilder()->hasTable('miniwarehouse_lm_center')) {
            $mappedLmCenterIds = $mappedLmCenterIds->merge(DB::table('miniwarehouse_lm_center')->pluck('lm_center_id'));
        }
        
        // Check lm_center_fm_rt_center table
        if (DB::getSchemaBuilder()->hasTable('lm_center_fm_rt_center')) {
            $mappedLmCenterIds = $mappedLmCenterIds->merge(DB::table('lm_center_fm_rt_center')->pluck('lm_center_id'));
        }
        
        // Check lm_center_pincode table (pincode mappings)
        if (DB::getSchemaBuilder()->hasTable('lm_center_pincode')) {
            $mappedLmCenterIds = $mappedLmCenterIds->merge(DB::table('lm_center_pincode')->pluck('lm_center_id'));
        }
        
        $mappedLmCenterIds = $mappedLmCenterIds->unique()->toArray();
        
        $pendingLmCenters = LmCenter::where('status', 1)
            ->whereNotIn('id', $mappedLmCenterIds)
            ->latest()
            ->get();
        
        // Get pending FM/RT Centers (not mapped to any warehouse, miniwarehouse, LM center, or pincode)
        $mappedFmRtCenterIds = collect();
        
        // Check warehouse_fm_rt_center table
        if (DB::getSchemaBuilder()->hasTable('warehouse_fm_rt_center')) {
            $mappedFmRtCenterIds = $mappedFmRtCenterIds->merge(DB::table('warehouse_fm_rt_center')->pluck('fm_rt_center_id'));
        }
        
        // Check miniwarehouse_fm_rt_center table
        if (DB::getSchemaBuilder()->hasTable('miniwarehouse_fm_rt_center')) {
            $mappedFmRtCenterIds = $mappedFmRtCenterIds->merge(DB::table('miniwarehouse_fm_rt_center')->pluck('fm_rt_center_id'));
        }
        
        // Check lm_center_fm_rt_center table
        if (DB::getSchemaBuilder()->hasTable('lm_center_fm_rt_center')) {
            $mappedFmRtCenterIds = $mappedFmRtCenterIds->merge(DB::table('lm_center_fm_rt_center')->pluck('fm_rt_center_id'));
        }
        
        // Check fm_rt_center_pincode table (pincode mappings)
        if (DB::getSchemaBuilder()->hasTable('fm_rt_center_pincode')) {
            $mappedFmRtCenterIds = $mappedFmRtCenterIds->merge(DB::table('fm_rt_center_pincode')->pluck('fm_rt_center_id'));
        }
        
        $mappedFmRtCenterIds = $mappedFmRtCenterIds->unique()->toArray();
        
        $pendingFmRtCenters = FmRtCenter::where('status', 1)
            ->whereNotIn('id', $mappedFmRtCenterIds)
            ->latest()
            ->get();
        
        // Don't load all pincodes - will be loaded via AJAX/server-side DataTables
        // Just get counts for badge display
        $mappedPincodeIds = collect();
        if (DB::getSchemaBuilder()->hasTable('lm_center_pincode')) {
            $mappedPincodeIds = $mappedPincodeIds->merge(DB::table('lm_center_pincode')->pluck('pincode_id'));
        }
        if (DB::getSchemaBuilder()->hasTable('fm_rt_center_pincode')) {
            $mappedPincodeIds = $mappedPincodeIds->merge(DB::table('fm_rt_center_pincode')->pluck('pincode_id'));
        }
        $mappedPincodeIds = $mappedPincodeIds->unique()->toArray();
        
        $pendingPincodesCount = Pincode::whereNotIn('id', $mappedPincodeIds)->count();
        $activePincodesCount = DB::getSchemaBuilder()->hasTable('lm_center_pincode') 
            ? DB::table('lm_center_pincode')->count() 
            : 0;
        
        // Get count of active pincodes (status = 1) for Live pincode Ecommerce tab
        $liveEcommercePincodesCount = Pincode::where('status', 1)->count();
        
        // Get count of pending logistic pincodes (active pincodes not mapped to any LM center)
        $lmCenterMappedPincodeIds = collect();
        if (DB::getSchemaBuilder()->hasTable('lm_center_pincode')) {
            $lmCenterMappedPincodeIds = DB::table('lm_center_pincode')->pluck('pincode_id')->unique()->toArray();
        } else {
            $lmCenterMappedPincodeIds = [];
        }
        
        $pendingLogisticPincodesCount = Pincode::where('status', 1)
            ->whereNotIn('id', $lmCenterMappedPincodeIds)
            ->count();
        
        // Pass empty collections for table data (will be loaded via AJAX)
        $pendingPincodes = collect();
        $activePincodes = collect();
        
        return view('admin-views.logistics.pending-mapping.index', compact(
            'tab',
            'pendingWarehouses',
            'pendingMiniwarehouses',
            'pendingLmCenters',
            'pendingFmRtCenters',
            'pendingPincodes',
            'activePincodes',
            'pendingPincodesCount',
            'activePincodesCount',
            'liveEcommercePincodesCount',
            'pendingLogisticPincodesCount'
        ));
    }

    /**
     * Get pending pincodes via AJAX for DataTables server-side processing
     */
    public function getPendingPincodes(Request $request)
    {
        // Get mapped pincode IDs
        $mappedPincodeIds = collect();
        
        if (DB::getSchemaBuilder()->hasTable('lm_center_pincode')) {
            $mappedPincodeIds = $mappedPincodeIds->merge(DB::table('lm_center_pincode')->pluck('pincode_id'));
        }
        
        if (DB::getSchemaBuilder()->hasTable('fm_rt_center_pincode')) {
            $mappedPincodeIds = $mappedPincodeIds->merge(DB::table('fm_rt_center_pincode')->pluck('pincode_id'));
        }
        
        $mappedPincodeIds = $mappedPincodeIds->unique()->toArray();
        
        // Build query
        $query = Pincode::whereNotIn('id', $mappedPincodeIds);
        
        // DataTables server-side processing parameters
        $draw = $request->get('draw');
        $start = $request->get('start', 0);
        $length = $request->get('length', 25);
        $search = $request->get('search', []);
        $searchValue = $search['value'] ?? '';
        
        // Apply search filter
        if (!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->where('pincode', 'like', "%{$searchValue}%")
                  ->orWhere('officename', 'like', "%{$searchValue}%")
                  ->orWhere('district', 'like', "%{$searchValue}%")
                  ->orWhere('statename', 'like', "%{$searchValue}%");
            });
        }
        
        // Get total count before pagination
        $totalRecords = $query->count();
        
        // Apply ordering
        $query->orderBy('pincode');
        
        // Apply pagination
        $pincodes = $query->skip($start)->take($length)->get();
        
        // Format data for DataTables
        $data = [];
        foreach ($pincodes as $index => $pincode) {
            // Status switch button HTML
            $status = $pincode->status ?? 0;
            $statusSwitch = '<div class="text-center">
                <label class="toggle-switch toggle-switch-sm" for="pincode-status-' . $pincode->id . '">
                    <input type="checkbox" 
                           class="toggle-switch-input dynamic-checkbox" 
                           id="pincode-status-' . $pincode->id . '"
                           data-id="pincode-status-' . $pincode->id . '"
                           data-type="status"
                           data-image-on="' . asset('assets/admin/img/modal/zone-status-on.png') . '"
                           data-image-off="' . asset('assets/admin/img/modal/zone-status-off.png') . '"
                           data-title-on="Want to activate this pincode?"
                           data-title-off="Want to deactivate this pincode?"
                           ' . ($status == 1 ? 'checked' : '') . '>
                    <span class="toggle-switch-label">
                        <span class="toggle-switch-indicator"></span>
                    </span>
                </label>
                <form action="' . route('admin.logistics.pending-mapping.pincode-status') . '" method="post" id="pincode-status-' . $pincode->id . '_form">
                    ' . csrf_field() . '
                    <input type="hidden" name="id" value="' . $pincode->id . '">
                    <input type="hidden" name="status" value="' . ($status == 1 ? 0 : 1) . '">
                </form>
            </div>';
            
            $data[] = [
                'DT_RowIndex' => $start + $index + 1,
                'pincode' => $pincode->pincode,
                'officename' => $pincode->officename ?? 'N/A',
                'district' => $pincode->district ?? 'N/A',
                'statename' => $pincode->statename ?? 'N/A',
                'action' => $statusSwitch,
            ];
        }
        
        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => Pincode::whereNotIn('id', $mappedPincodeIds)->count(),
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
    }

    /**
     * Get active pincodes via AJAX for DataTables server-side processing
     */
    public function getActivePincodes(Request $request)
    {
        // DataTables server-side processing parameters
        $draw = $request->get('draw');
        $start = $request->get('start', 0);
        $length = $request->get('length', 25);
        $search = $request->get('search', []);
        $searchValue = $search['value'] ?? '';
        
        // Get delivery type filter - check both query string and request data
        // Default changed to 'both' (Taksh Logistic LM Live pincode)
        $deliveryType = $request->input('delivery_type', $request->query('delivery_type', 'both'));
        
        // Validate delivery type
        $allowedTypes = ['lm_fm_rt', 'both', 'thirty_min', 'normal'];
        if (!in_array($deliveryType, $allowedTypes)) {
            $deliveryType = 'both'; // Default fallback
        }
        
        // Log filter for debugging - verify filter is received
        \Log::info('Active Pincodes Filter Request', [
            'delivery_type' => $deliveryType,
            'all_params' => $request->all(),
            'query_params' => $request->query(),
            'search' => $searchValue
        ]);
        
        // DEFAULT: LM & FM RT - Show union of all pincodes from both modules
        if ($deliveryType === 'lm_fm_rt') {
            // Get LM center pincodes (all pincodes mapped to LM centers)
            $lmPincodes = DB::table('lm_center_pincode')
                ->join('pincodes', 'lm_center_pincode.pincode_id', '=', 'pincodes.id')
                ->join('lm_centers', 'lm_center_pincode.lm_center_id', '=', 'lm_centers.id')
                ->select(
                    'pincodes.id as pincode_id',
                    'pincodes.pincode',
                    'pincodes.officename',
                    'pincodes.district',
                    'pincodes.statename',
                    DB::raw('lm_centers.id as center_id'),
                    DB::raw('lm_centers.center_name as center_name'),
                    DB::raw("'LM Center' as center_type"),
                    'lm_center_pincode.created_at as mapped_at'
                );
            
            // Get FM RT center pincodes
            $fmRtPincodes = collect();
            if (DB::getSchemaBuilder()->hasTable('fm_rt_center_pincode')) {
                $fmRtPincodes = DB::table('fm_rt_center_pincode')
                    ->join('pincodes', 'fm_rt_center_pincode.pincode_id', '=', 'pincodes.id')
                    ->join('fm_rt_centers', 'fm_rt_center_pincode.fm_rt_center_id', '=', 'fm_rt_centers.id')
                    ->select(
                        'pincodes.id as pincode_id',
                        'pincodes.pincode',
                        'pincodes.officename',
                        'pincodes.district',
                        'pincodes.statename',
                        DB::raw('fm_rt_centers.id as center_id'),
                        DB::raw('fm_rt_centers.center_name as center_name'),
                        DB::raw("'FM/RT Center' as center_type"),
                        'fm_rt_center_pincode.created_at as mapped_at'
                    );
            }
            
            // Apply search filter to both queries
            if (!empty($searchValue)) {
                $lmPincodes->where(function($q) use ($searchValue) {
                    $q->where('pincodes.pincode', 'like', "%{$searchValue}%")
                      ->orWhere('pincodes.officename', 'like', "%{$searchValue}%")
                      ->orWhere('pincodes.district', 'like', "%{$searchValue}%")
                      ->orWhere('pincodes.statename', 'like', "%{$searchValue}%")
                      ->orWhere('lm_centers.center_name', 'like', "%{$searchValue}%");
                });
                
                if ($fmRtPincodes instanceof \Illuminate\Database\Query\Builder) {
                    $fmRtPincodes->where(function($q) use ($searchValue) {
                        $q->where('pincodes.pincode', 'like', "%{$searchValue}%")
                          ->orWhere('pincodes.officename', 'like', "%{$searchValue}%")
                          ->orWhere('pincodes.district', 'like', "%{$searchValue}%")
                          ->orWhere('pincodes.statename', 'like', "%{$searchValue}%")
                          ->orWhere('fm_rt_centers.center_name', 'like', "%{$searchValue}%");
                    });
                }
            }
            
            // Get all results and merge
            $lmResults = $lmPincodes->get();
            $fmRtResults = ($fmRtPincodes instanceof \Illuminate\Database\Query\Builder) ? $fmRtPincodes->get() : collect();
            
            // Merge and sort
            $allPincodes = $lmResults->merge($fmRtResults)->sortBy('pincode')->values();
            
            // Get total count before pagination
            $totalRecords = $allPincodes->count();
            
            // Apply pagination
            $activePincodes = $allPincodes->slice($start, $length)->values();
            
            // Format data for DataTables
            $data = [];
            foreach ($activePincodes as $index => $activePincode) {
                $mappedAt = $activePincode->mapped_at ? \Carbon\Carbon::parse($activePincode->mapped_at)->format('d M Y') : 'N/A';
                
                // Create center link based on center type
                if ($activePincode->center_type === 'LM Center') {
                    $centerLink = '<a href="' . route('admin.logistics.lm-center.edit', [$activePincode->center_id]) . '" class="text-primary">' . e($activePincode->center_name) . '</a>';
                } else {
                    $centerLink = '<a href="' . route('admin.logistics.fm-rt-center.edit', [$activePincode->center_id]) . '" class="text-primary">' . e($activePincode->center_name) . '</a>';
                }
                
                $data[] = [
                    'DT_RowIndex' => $start + $index + 1,
                    'pincode' => $activePincode->pincode,
                    'officename' => $activePincode->officename ?? 'N/A',
                    'district' => $activePincode->district ?? 'N/A',
                    'statename' => $activePincode->statename ?? 'N/A',
                    'center_name' => $centerLink . ' <span class="badge badge-soft-info">' . $activePincode->center_type . '</span>',
                    'mapped_at' => $mappedAt,
                ];
            }
            
            // Get total count
            $totalCount = DB::table('lm_center_pincode')->count() + 
                         (DB::getSchemaBuilder()->hasTable('fm_rt_center_pincode') ? DB::table('fm_rt_center_pincode')->count() : 0);
            
            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => $totalCount,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ]);
        }
        
        // DELIVERY TYPE FILTERS: Only LM centers (FM RT completely ignored)
        // Build query for active pincodes (mapped to LM centers ONLY)
        $query = DB::table('lm_center_pincode')
            ->join('pincodes', 'lm_center_pincode.pincode_id', '=', 'pincodes.id')
            ->join('lm_centers', 'lm_center_pincode.lm_center_id', '=', 'lm_centers.id')
            ->select(
                'pincodes.id as pincode_id',
                'pincodes.pincode',
                'pincodes.officename',
                'pincodes.district',
                'pincodes.statename',
                'lm_centers.id as lm_center_id',
                'lm_centers.center_name',
                'lm_centers.thirty_min_delivery',
                'lm_centers.normal_delivery',
                'lm_center_pincode.created_at as mapped_at'
            );
        
        // Apply delivery type filter - STRICT: Only LM centers, FM RT NEVER included
        if ($deliveryType === 'both') {
            // 30 Min & Normal Delivery: LM centers with EITHER checkbox checked
            $query->where(function($q) {
                $q->where('lm_centers.thirty_min_delivery', 1)
                  ->orWhere('lm_centers.normal_delivery', 1);
            });
        } elseif ($deliveryType === 'thirty_min') {
            // 30 Min Delivery: ONLY LM centers where thirty_min_delivery = 1
            // FM RT centers MUST NOT appear
            $query->where('lm_centers.thirty_min_delivery', 1);
        } elseif ($deliveryType === 'normal') {
            // Normal Delivery: ONLY LM centers where normal_delivery = 1
            // FM RT centers MUST NOT appear
            $query->where('lm_centers.normal_delivery', 1);
        }
        
        // Apply search filter
        if (!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->where('pincodes.pincode', 'like', "%{$searchValue}%")
                  ->orWhere('pincodes.officename', 'like', "%{$searchValue}%")
                  ->orWhere('pincodes.district', 'like', "%{$searchValue}%")
                  ->orWhere('pincodes.statename', 'like', "%{$searchValue}%")
                  ->orWhere('lm_centers.center_name', 'like', "%{$searchValue}%");
            });
        }
        
        // Get total count before pagination
        $totalRecords = $query->count();
        
        // Log query for debugging
        \Log::info('Active Pincodes Query', [
            'filter' => $deliveryType,
            'total_records' => $totalRecords,
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);
        
        // Apply ordering
        $query->orderBy('pincodes.pincode');
        
        // Apply pagination
        $activePincodes = $query->skip($start)->take($length)->get();
        
        // Format data for DataTables
        $data = [];
        foreach ($activePincodes as $index => $activePincode) {
            $mappedAt = $activePincode->mapped_at ? \Carbon\Carbon::parse($activePincode->mapped_at)->format('d M Y') : 'N/A';
            $centerLink = '<a href="' . route('admin.logistics.lm-center.edit', [$activePincode->lm_center_id]) . '" class="text-primary">' . e($activePincode->center_name) . '</a>';
            
            $data[] = [
                'DT_RowIndex' => $start + $index + 1,
                'pincode' => $activePincode->pincode,
                'officename' => $activePincode->officename ?? 'N/A',
                'district' => $activePincode->district ?? 'N/A',
                'statename' => $activePincode->statename ?? 'N/A',
                'center_name' => $centerLink,
                'mapped_at' => $mappedAt,
            ];
        }
        
        // Get total count without filters for recordsTotal (based on delivery type filter)
        $totalCountQuery = DB::table('lm_center_pincode')
            ->join('lm_centers', 'lm_center_pincode.lm_center_id', '=', 'lm_centers.id');
        
        if ($deliveryType === 'both') {
            // Either 30 min OR normal delivery checked (or both)
            $totalCountQuery->where(function($q) {
                $q->where('lm_centers.thirty_min_delivery', 1)
                  ->orWhere('lm_centers.normal_delivery', 1);
            });
        } elseif ($deliveryType === 'thirty_min') {
            // Only 30 min delivery checked
            $totalCountQuery->where('lm_centers.thirty_min_delivery', 1);
        } elseif ($deliveryType === 'normal') {
            // Only normal delivery checked
            $totalCountQuery->where('lm_centers.normal_delivery', 1);
        }
        
        $totalCount = $totalCountQuery->count();
        
        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalCount,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
    }

    /**
     * Update pincode status
     */
    public function updatePincodeStatus(Request $request)
    {
        try {
            // Log incoming request for debugging
            \Log::info('Pincode status update request', [
                'request_data' => $request->all(),
                'id' => $request->id,
                'status' => $request->status,
                'status_type' => gettype($request->status)
            ]);

            $request->validate([
                'id' => 'required|exists:pincodes,id',
                'status' => 'required|in:0,1'
            ]);

            $pincode = Pincode::findOrFail($request->id);
            $oldStatus = $pincode->status;
            
            // Normalize status value - handle string "1"/"0" or integer 1/0
            $statusValue = $request->status;
            if (is_string($statusValue)) {
                $statusValue = trim($statusValue);
            }
            
            // Convert to integer (0 or 1)
            $newStatus = (int)$statusValue;
            
            // Validate status value must be exactly 0 or 1
            if ($newStatus !== 0 && $newStatus !== 1) {
                \Log::error('Invalid status value received', [
                    'pincode_id' => $pincode->id,
                    'pincode' => $pincode->pincode,
                    'received_status' => $request->status,
                    'received_type' => gettype($request->status),
                    'converted_status' => $newStatus
                ]);
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid status value. Must be 0 or 1. Received: ' . $request->status
                ], 422);
            }
            
            // CRITICAL: Update status in database using integer value
            // Even though model casts to boolean, we save as integer
            // Laravel will handle the boolean cast automatically
            $pincode->status = $newStatus;
            
            // Save and check if successful
            $saved = $pincode->save();
            
            if (!$saved) {
                \Log::error('Failed to save pincode status', [
                    'pincode_id' => $pincode->id,
                    'new_status' => $newStatus
                ]);
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to save status to database'
                ], 500);
            }
            
            // Refresh to get latest data from database
            $pincode->refresh();
            
            // Verify the status was actually saved
            $actualStatus = (int)$pincode->status; // Convert boolean to int for comparison
            if ($actualStatus !== $newStatus) {
                \Log::error('Status mismatch after save', [
                    'pincode_id' => $pincode->id,
                    'expected_status' => $newStatus,
                    'actual_status' => $actualStatus,
                    'actual_status_raw' => $pincode->status
                ]);
            }

            // Log the status change with detailed information
            \Log::info('Pincode status updated successfully', [
                'pincode_id' => $pincode->id,
                'pincode' => $pincode->pincode,
                'old_status' => $oldStatus,
                'old_status_int' => (int)$oldStatus,
                'new_status' => $newStatus,
                'saved' => $saved,
                'current_db_status' => $pincode->status,
                'current_db_status_int' => (int)$pincode->status,
                'status_match' => ((int)$pincode->status) === $newStatus
            ]);

            $message = $newStatus == 1 
                ? 'Pincode activated successfully' 
                : 'Pincode deactivated successfully';

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => [
                    'id' => $pincode->id,
                    'status' => $pincode->status,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed: ' . $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pincode not found'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Failed to update pincode status', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update pincode status. Please try again.'
            ], 500);
        }
    }

    /**
     * Get live ecommerce pincodes (active pincodes - status = 1) via AJAX for DataTables server-side processing
     */
    public function getLiveEcommercePincodes(Request $request)
    {
        // Build query - only get pincodes with status = 1 (active)
        $query = Pincode::where('status', 1);
        
        // DataTables server-side processing parameters
        $draw = $request->get('draw');
        $start = $request->get('start', 0);
        $length = $request->get('length', 25);
        $search = $request->get('search', []);
        $searchValue = $search['value'] ?? '';
        
        // Apply search filter
        if (!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->where('pincode', 'like', "%{$searchValue}%")
                  ->orWhere('officename', 'like', "%{$searchValue}%")
                  ->orWhere('district', 'like', "%{$searchValue}%")
                  ->orWhere('statename', 'like', "%{$searchValue}%");
            });
        }
        
        // Get total count before pagination
        $totalRecords = $query->count();
        
        // Apply ordering
        $query->orderBy('pincode');
        
        // Apply pagination
        $pincodes = $query->skip($start)->take($length)->get();
        
        // Format data for DataTables
        $data = [];
        foreach ($pincodes as $index => $pincode) {
            // Status switch button HTML
            $status = $pincode->status ?? 0;
            $statusSwitch = '<div class="text-center">
                <label class="toggle-switch toggle-switch-sm" for="live-pincode-status-' . $pincode->id . '">
                    <input type="checkbox" 
                           class="toggle-switch-input dynamic-checkbox" 
                           id="live-pincode-status-' . $pincode->id . '"
                           data-id="live-pincode-status-' . $pincode->id . '"
                           data-type="status"
                           data-image-on="' . asset('assets/admin/img/modal/zone-status-on.png') . '"
                           data-image-off="' . asset('assets/admin/img/modal/zone-status-off.png') . '"
                           data-title-on="Want to activate this pincode?"
                           data-title-off="Want to deactivate this pincode?"
                           ' . ($status == 1 ? 'checked' : '') . '>
                    <span class="toggle-switch-label">
                        <span class="toggle-switch-indicator"></span>
                    </span>
                </label>
                <form action="' . route('admin.logistics.pending-mapping.pincode-status') . '" method="post" id="live-pincode-status-' . $pincode->id . '_form">
                    ' . csrf_field() . '
                    <input type="hidden" name="id" value="' . $pincode->id . '">
                    <input type="hidden" name="status" value="' . ($status == 1 ? 0 : 1) . '">
                </form>
            </div>';
            
            $data[] = [
                'DT_RowIndex' => $start + $index + 1,
                'pincode' => $pincode->pincode,
                'officename' => $pincode->officename ?? 'N/A',
                'district' => $pincode->district ?? 'N/A',
                'statename' => $pincode->statename ?? 'N/A',
                'action' => $statusSwitch,
            ];
        }
        
        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => Pincode::where('status', 1)->count(),
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
    }

    /**
     * Get pending logistic pincodes (active pincodes - status = 1, but not mapped to any LM center) via AJAX for DataTables server-side processing
     */
    public function getPendingLogisticPincodes(Request $request)
    {
        // Get pincode IDs mapped to LM centers
        $lmCenterMappedPincodeIds = collect();
        
        if (DB::getSchemaBuilder()->hasTable('lm_center_pincode')) {
            $lmCenterMappedPincodeIds = DB::table('lm_center_pincode')->pluck('pincode_id')->unique()->toArray();
        } else {
            $lmCenterMappedPincodeIds = [];
        }
        
        // Build query - active pincodes (status = 1) that are NOT mapped to any LM center
        $query = Pincode::where('status', 1)
            ->whereNotIn('id', $lmCenterMappedPincodeIds);
        
        // DataTables server-side processing parameters
        $draw = $request->get('draw');
        $start = $request->get('start', 0);
        $length = $request->get('length', 25);
        $search = $request->get('search', []);
        $searchValue = $search['value'] ?? '';
        
        // Apply search filter
        if (!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->where('pincode', 'like', "%{$searchValue}%")
                  ->orWhere('officename', 'like', "%{$searchValue}%")
                  ->orWhere('district', 'like', "%{$searchValue}%")
                  ->orWhere('statename', 'like', "%{$searchValue}%");
            });
        }
        
        // Get total count before pagination
        $totalRecords = $query->count();
        
        // Apply ordering
        $query->orderBy('pincode');
        
        // Apply pagination
        $pincodes = $query->skip($start)->take($length)->get();
        
        // Format data for DataTables
        $data = [];
        foreach ($pincodes as $index => $pincode) {
            // Status switch button HTML
            $status = $pincode->status ?? 0;
            $statusSwitch = '<div class="text-center">
                <label class="toggle-switch toggle-switch-sm" for="pending-logistic-pincode-status-' . $pincode->id . '">
                    <input type="checkbox" 
                           class="toggle-switch-input dynamic-checkbox" 
                           id="pending-logistic-pincode-status-' . $pincode->id . '"
                           data-id="pending-logistic-pincode-status-' . $pincode->id . '"
                           data-type="status"
                           data-image-on="' . asset('assets/admin/img/modal/zone-status-on.png') . '"
                           data-image-off="' . asset('assets/admin/img/modal/zone-status-off.png') . '"
                           data-title-on="Want to activate this pincode?"
                           data-title-off="Want to deactivate this pincode?"
                           ' . ($status == 1 ? 'checked' : '') . '>
                    <span class="toggle-switch-label">
                        <span class="toggle-switch-indicator"></span>
                    </span>
                </label>
                <form action="' . route('admin.logistics.pending-mapping.pincode-status') . '" method="post" id="pending-logistic-pincode-status-' . $pincode->id . '_form">
                    ' . csrf_field() . '
                    <input type="hidden" name="id" value="' . $pincode->id . '">
                    <input type="hidden" name="status" value="' . ($status == 1 ? 0 : 1) . '">
                </form>
            </div>';
            
            $data[] = [
                'DT_RowIndex' => $start + $index + 1,
                'pincode' => $pincode->pincode,
                'officename' => $pincode->officename ?? 'N/A',
                'district' => $pincode->district ?? 'N/A',
                'statename' => $pincode->statename ?? 'N/A',
                'action' => $statusSwitch,
            ];
        }
        
        // Get total count for recordsTotal
        $totalCount = Pincode::where('status', 1)
            ->whereNotIn('id', $lmCenterMappedPincodeIds)
            ->count();
        
        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalCount,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
    }
}

