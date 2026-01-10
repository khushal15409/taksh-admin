<?php

namespace App\Http\Controllers\Admin\AccessControl;

use App\Enums\ViewPaths\Admin\AccessControl;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\DepartmentUnit;
use App\CentralLogics\ToastrWrapper as Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentUnitController extends Controller
{
    public function index(Request $request): View
    {
        $units = DepartmentUnit::with('department')
            ->when($request->get('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->get('search') . '%')
                    ->orWhere('code', 'like', '%' . $request->get('search') . '%')
                    ->orWhereHas('department', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->get('search') . '%');
                    });
            })
            ->when($request->get('department_id'), function ($query) use ($request) {
                $query->where('department_id', $request->get('department_id'));
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(25);

        $departments = Department::active()->orderBy('name')->get();

        return view(AccessControl::DEPARTMENT_UNIT_INDEX['VIEW'], compact('units', 'departments'));
    }

    public function create(): View
    {
        $departments = Department::active()->orderBy('name')->get();
        return view(AccessControl::DEPARTMENT_UNIT_ADD['VIEW'], compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        // Check unique code within department
        $existing = DepartmentUnit::where('department_id', $request->department_id)
            ->where('code', $request->code)
            ->first();

        if ($existing) {
            return back()->withInput()->withErrors(['code' => translate('messages.code_already_exists_in_department')]);
        }

        DepartmentUnit::create([
            'department_id' => $request->department_id,
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        Toastr::success(translate('messages.department_unit_added_successfully'));
        return redirect()->route('admin.access-control.department-unit.index');
    }

    public function edit($id): View
    {
        $unit = DepartmentUnit::with('department')->findOrFail($id);
        $departments = Department::active()->orderBy('name')->get();
        return view(AccessControl::DEPARTMENT_UNIT_UPDATE['VIEW'], compact('unit', 'departments'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $unit = DepartmentUnit::findOrFail($id);

        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        // Check unique code within department
        $existing = DepartmentUnit::where('department_id', $request->department_id)
            ->where('code', $request->code)
            ->where('id', '!=', $id)
            ->first();

        if ($existing) {
            return back()->withInput()->withErrors(['code' => translate('messages.code_already_exists_in_department')]);
        }

        $unit->update([
            'department_id' => $request->department_id,
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        Toastr::success(translate('messages.department_unit_updated_successfully'));
        return redirect()->route('admin.access-control.department-unit.index');
    }

    public function destroy($id): RedirectResponse
    {
        $unit = DepartmentUnit::findOrFail($id);
        
        // Check if unit has assignments
        if ($unit->assignments()->count() > 0) {
            Toastr::error(translate('messages.cannot_delete_unit_with_assignments'));
            return back();
        }

        $unit->delete();
        Toastr::success(translate('messages.department_unit_deleted_successfully'));
        return redirect()->route('admin.access-control.department-unit.index');
    }

    public function status(Request $request, $id)
    {
        $unit = DepartmentUnit::findOrFail($id);
        $unit->is_active = $request->status;
        $unit->save();

        return response()->json([
            'status' => true,
            'message' => translate('messages.unit_status_updated_successfully')
        ]);
    }
}
