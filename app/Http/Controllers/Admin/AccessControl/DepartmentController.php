<?php

namespace App\Http\Controllers\Admin\AccessControl;

use App\Enums\ViewPaths\Admin\AccessControl;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\CentralLogics\ToastrWrapper as Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(Request $request): View
    {
        $departments = Department::with('units')
            ->when($request->get('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->get('search') . '%')
                    ->orWhere('code', 'like', '%' . $request->get('search') . '%');
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(25);

        return view(AccessControl::DEPARTMENT_INDEX['VIEW'], compact('departments'));
    }

    public function create(): View
    {
        return view(AccessControl::DEPARTMENT_ADD['VIEW']);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'code' => 'required|string|max:20|unique:departments,code',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        Department::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        Toastr::success(translate('messages.department_added_successfully'));
        return redirect()->route('admin.access-control.department.index');
    }

    public function edit($id): View
    {
        $department = Department::findOrFail($id);
        return view(AccessControl::DEPARTMENT_UPDATE['VIEW'], compact('department'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $department = Department::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $id,
            'code' => 'required|string|max:20|unique:departments,code,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $department->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        Toastr::success(translate('messages.department_updated_successfully'));
        return redirect()->route('admin.access-control.department.index');
    }

    public function destroy($id): RedirectResponse
    {
        $department = Department::findOrFail($id);
        
        // Check if department has units
        if ($department->units()->count() > 0) {
            Toastr::error(translate('messages.cannot_delete_department_with_units'));
            return back();
        }

        $department->delete();
        Toastr::success(translate('messages.department_deleted_successfully'));
        return redirect()->route('admin.access-control.department.index');
    }

    public function status(Request $request, $id)
    {
        $department = Department::findOrFail($id);
        $department->is_active = $request->status;
        $department->save();

        return response()->json([
            'status' => true,
            'message' => translate('messages.department_status_updated_successfully')
        ]);
    }
}
