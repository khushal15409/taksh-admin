<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppDashboardSection;
use Illuminate\Http\Request;
use App\CentralLogics\ToastrWrapper as Toastr;

class AppDashboardSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = AppDashboardSection::orderBy('sort_order')->get();
        
        return view('admin-views.app-dashboard.index', compact('sections'));
    }

    /**
     * Update section status
     */
    public function updateStatus(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:app_dashboard_sections,id',
                'status' => 'required|boolean',
            ]);

            $section = AppDashboardSection::findOrFail($request->id);
            $section->is_active = $request->status;
            $section->save();

            Toastr::success(translate('messages.section_status_updated'));
            return back();
        } catch (\Exception $e) {
            Toastr::error(translate('messages.failed_to_update_status') . ': ' . $e->getMessage());
            \Log::error('Section status update error: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Update sort order
     */
    public function updateSortOrder(Request $request)
    {
        try {
            $request->validate([
                'sections' => 'required|array',
                'sections.*.id' => 'required|exists:app_dashboard_sections,id',
                'sections.*.sort_order' => 'required|integer|min:0',
            ]);

            foreach ($request->sections as $sectionData) {
                $section = AppDashboardSection::findOrFail($sectionData['id']);
                $section->sort_order = $sectionData['sort_order'];
                $section->save();
            }

            Toastr::success(translate('messages.sort_order_updated'));
            return response()->json([
                'success' => true,
                'message' => translate('messages.sort_order_updated'),
            ]);
        } catch (\Exception $e) {
            Toastr::error(translate('messages.failed_to_update_sort_order') . ': ' . $e->getMessage());
            \Log::error('Sort order update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => translate('messages.failed_to_update_sort_order'),
            ], 500);
        }
    }

    /**
     * Update single section sort order
     */
    public function updateSingleSortOrder(Request $request, $id)
    {
        try {
            $request->validate([
                'sort_order' => 'required|integer|min:0',
            ]);

            $section = AppDashboardSection::findOrFail($id);
            $section->sort_order = $request->sort_order;
            $section->save();

            Toastr::success(translate('messages.sort_order_updated'));
            return back();
        } catch (\Exception $e) {
            Toastr::error(translate('messages.failed_to_update_sort_order') . ': ' . $e->getMessage());
            \Log::error('Sort order update error: ' . $e->getMessage());
            return back();
        }
    }
}
