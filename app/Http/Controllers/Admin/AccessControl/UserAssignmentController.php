<?php

namespace App\Http\Controllers\Admin\AccessControl;

use App\Enums\ViewPaths\Admin\AccessControl;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\DepartmentUnit;
use App\Models\Geography;
use App\Models\UserAssignment;
use Spatie\Permission\Models\Role;
use App\CentralLogics\ToastrWrapper as Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserAssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $assignments = UserAssignment::with(['user', 'department', 'departmentUnit', 'geography', 'role'])
            ->when($request->get('search'), function ($query) use ($request) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->get('search') . '%')
                      ->orWhere('email', 'like', '%' . $request->get('search') . '%')
                      ->orWhere('f_name', 'like', '%' . $request->get('search') . '%')
                      ->orWhere('l_name', 'like', '%' . $request->get('search') . '%');
                })
                ->orWhereHas('department', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->get('search') . '%');
                });
            })
            ->when($request->get('department_id'), function ($query) use ($request) {
                $query->where('department_id', $request->get('department_id'));
            })
            ->when($request->get('user_id'), function ($query) use ($request) {
                $query->where('user_id', $request->get('user_id'));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        $departments = Department::active()->orderBy('name')->get();
        $users = User::orderBy('name')->limit(100)->get();

        return view(AccessControl::USER_ASSIGNMENT_INDEX['VIEW'], compact('assignments', 'departments', 'users'));
    }

    public function create(Request $request): View
    {
        $users = User::orderBy('name')->get();
        $departments = Department::active()->orderBy('name')->get();
        $roles = Role::where('name', '!=', 'super-admin')->orderBy('name')->get();
        $geographies = Geography::active()->orderByRaw("FIELD(level, 'india', 'state', 'zone', 'area', 'pincode')")->orderBy('name')->get();
        
        // Pre-select user if provided in query
        $selectedUserId = $request->get('user_id');

        return view(AccessControl::USER_ASSIGNMENT_ADD['VIEW'], compact('users', 'departments', 'roles', 'geographies', 'selectedUserId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'department_id' => 'required|exists:departments,id',
            'department_unit_id' => 'nullable|exists:department_units,id',
            'geography_id' => 'nullable|exists:geographies,id',
            'role_id' => 'required|exists:roles,id',
            'effective_from' => 'nullable|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Validate department_unit belongs to department
        if ($request->department_unit_id) {
            $unit = DepartmentUnit::findOrFail($request->department_unit_id);
            if ($unit->department_id != $request->department_id) {
                return back()->withInput()->withErrors(['department_unit_id' => translate('messages.unit_must_belong_to_department')]);
            }
        }

        UserAssignment::create([
            'user_id' => $request->user_id,
            'department_id' => $request->department_id,
            'department_unit_id' => $request->department_unit_id,
            'geography_id' => $request->geography_id,
            'role_id' => $request->role_id,
            'effective_from' => $request->effective_from,
            'effective_to' => $request->effective_to,
            'notes' => $request->notes,
            'is_active' => $request->has('is_active'),
            'assigned_by' => auth()->id(),
        ]);

        // Assign role to user (Spatie Permission)
        $user = User::findOrFail($request->user_id);
        $role = Role::findOrFail($request->role_id);
        $user->assignRole($role);

        Toastr::success(translate('messages.user_assignment_added_successfully'));
        return redirect()->route('admin.access-control.user-assignment.index');
    }

    public function edit($id): View
    {
        $assignment = UserAssignment::with(['user', 'department', 'departmentUnit', 'geography', 'role'])->findOrFail($id);
        $users = User::orderBy('name')->get();
        $departments = Department::active()->orderBy('name')->get();
        $departmentUnits = $assignment->department_id 
            ? DepartmentUnit::where('department_id', $assignment->department_id)->active()->orderBy('name')->get() 
            : collect();
        $roles = Role::where('name', '!=', 'super-admin')->orderBy('name')->get();
        $geographies = Geography::active()->orderByRaw("FIELD(level, 'india', 'state', 'zone', 'area', 'pincode')")->orderBy('name')->get();

        return view(AccessControl::USER_ASSIGNMENT_UPDATE['VIEW'], compact('assignment', 'users', 'departments', 'departmentUnits', 'roles', 'geographies'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $assignment = UserAssignment::findOrFail($id);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'department_id' => 'required|exists:departments,id',
            'department_unit_id' => 'nullable|exists:department_units,id',
            'geography_id' => 'nullable|exists:geographies,id',
            'role_id' => 'required|exists:roles,id',
            'effective_from' => 'nullable|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Validate department_unit belongs to department
        if ($request->department_unit_id) {
            $unit = DepartmentUnit::findOrFail($request->department_unit_id);
            if ($unit->department_id != $request->department_id) {
                return back()->withInput()->withErrors(['department_unit_id' => translate('messages.unit_must_belong_to_department')]);
            }
        }

        $oldRoleId = $assignment->role_id;
        $oldUserId = $assignment->user_id;

        $assignment->update([
            'user_id' => $request->user_id,
            'department_id' => $request->department_id,
            'department_unit_id' => $request->department_unit_id,
            'geography_id' => $request->geography_id,
            'role_id' => $request->role_id,
            'effective_from' => $request->effective_from,
            'effective_to' => $request->effective_to,
            'notes' => $request->notes,
            'is_active' => $request->has('is_active'),
        ]);

        // Update role assignment if changed
        if ($oldRoleId != $request->role_id || $oldUserId != $request->user_id) {
            if ($oldUserId == $request->user_id) {
                // Same user, update role
                $user = User::findOrFail($request->user_id);
                $oldRole = Role::find($oldRoleId);
                $newRole = Role::findOrFail($request->role_id);
                
                if ($oldRole) {
                    $user->removeRole($oldRole);
                }
                $user->assignRole($newRole);
            } else {
                // Different user, update both
                $oldUser = User::find($oldUserId);
                $newUser = User::findOrFail($request->user_id);
                
                if ($oldUser && $oldRoleId) {
                    $oldRole = Role::find($oldRoleId);
                    if ($oldRole) {
                        $oldUser->removeRole($oldRole);
                    }
                }
                
                $newRole = Role::findOrFail($request->role_id);
                $newUser->assignRole($newRole);
            }
        }

        Toastr::success(translate('messages.user_assignment_updated_successfully'));
        return redirect()->route('admin.access-control.user-assignment.index');
    }

    public function destroy($id): RedirectResponse
    {
        $assignment = UserAssignment::findOrFail($id);
        
        // Remove role from user if this is the only assignment with this role
        $user = $assignment->user;
        $role = $assignment->role;
        
        if ($user && $role) {
            // Check if user has other assignments with this role
            $otherAssignments = UserAssignment::where('user_id', $user->id)
                ->where('role_id', $role->id)
                ->where('id', '!=', $id)
                ->currentlyEffective()
                ->count();
            
            if ($otherAssignments == 0) {
                $user->removeRole($role);
            }
        }

        $assignment->delete();
        Toastr::success(translate('messages.user_assignment_deleted_successfully'));
        return redirect()->route('admin.access-control.user-assignment.index');
    }

    public function getUnitsByDepartment(Request $request)
    {
        $departmentId = $request->get('department_id');
        
        if (!$departmentId) {
            return response()->json(['units' => []]);
        }

        $units = DepartmentUnit::where('department_id', $departmentId)
            ->active()
            ->orderBy('name')
            ->get()
            ->map(function ($unit) {
                return [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'code' => $unit->code,
                ];
            });

        return response()->json(['units' => $units]);
    }
}
