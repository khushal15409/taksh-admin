<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Enums\ViewPaths\Admin\Employee as EmployeeViewPath;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EmployeeAddRequest;
use App\Http\Requests\Admin\EmployeeUpdateRequest;
use App\Repositories\EmployeeRepository;
use App\Services\EmployeeService;
use App\CentralLogics\ToastrWrapper as Toastr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use App\Models\Pincode;
use Illuminate\Support\Facades\Schema;

class EmployeeController extends Controller
{
    public function __construct(
        protected EmployeeRepository $employeeRepo,
        protected EmployeeService $employeeService,
    )
    {
    }

    public function index(?Request $request): View
    {
        return $this->getListView($request);
    }

    private function getListView(Request $request): View
    {
        $pincodeId = $request->query('pincode_id', 'all');
        $employees = $this->employeeRepo->getPincodeWiseListWhere(
            searchValue: $request->get('search'),
            relations: ['role', 'pincode'],
            pincodeId: $pincodeId,
            dataLimit: 25
        );
        return view(EmployeeViewPath::INDEX['VIEW'], compact('employees'));
    }

    public function getAddView(): View
    {
        $roles = Role::where('name', '!=', 'super-admin')->get();
        return view(EmployeeViewPath::ADD['VIEW'], compact('roles'));
    }
    
    public function searchPincodes(Request $request): JsonResponse
    {
        $search = $request->get('search', '');
        $query = Pincode::where('status', 1);
        
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('pincode', 'like', "%{$search}%")
                  ->orWhere('officename', 'like', "%{$search}%")
                  ->orWhere('district', 'like', "%{$search}%")
                  ->orWhere('statename', 'like', "%{$search}%");
            });
        }
        
        $pincodes = $query->orderBy('pincode', 'asc')->get();
        
        return response()->json([
            'success' => true,
            'pincodes' => $pincodes->map(function($pincode) {
                return [
                    'id' => $pincode->id,
                    'pincode' => $pincode->pincode,
                    'officename' => $pincode->officename,
                    'district' => $pincode->district,
                    'statename' => $pincode->statename,
                    'display' => $pincode->pincode . ' - ' . $pincode->officename . ', ' . $pincode->district . ', ' . $pincode->statename,
                ];
            })
        ]);
    }

    public function add(EmployeeAddRequest $request): RedirectResponse
    {
        try {
            $data = $this->employeeService->getAddData(request: $request);
            $employee = $this->employeeRepo->add(data: $data);
            
            if ($employee && $employee->id) {
                Toastr::success(translate('messages.employee_added_successfully'));
                return redirect()->route('admin.users.employee.list');
            } else {
                session()->flash('validation_error', translate('messages.failed_to_add_employee'));
                return back()->withInput();
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors are already handled by FormRequest failedValidation
            // This catch is for any additional validation that might occur
            $errors = $e->errors();
            $firstError = null;
            foreach ($errors as $field => $messages) {
                if (!empty($messages)) {
                    $firstError = $messages[0];
                    break;
                }
            }
            if ($firstError) {
                session()->flash('validation_error', $firstError);
            }
            return back()->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            $errorMessage = translate('messages.failed_to_add_employee');
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                if (str_contains($e->getMessage(), 'email') || str_contains($e->getMessage(), 'users.email')) {
                    $errorMessage = translate('messages.email_already_exists');
                } elseif (str_contains($e->getMessage(), 'phone') || str_contains($e->getMessage(), 'users.phone')) {
                    $errorMessage = translate('messages.phone_already_exists');
                }
            } elseif (str_contains($e->getMessage(), 'foreign key constraint')) {
                $errorMessage = translate('messages.invalid_role_or_zone');
            }
            session()->flash('validation_error', $errorMessage);
            \Log::error('Employee Add Query Error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
            return back()->withInput();
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            // Don't show technical error messages to users
            if (str_contains($errorMessage, 'SQLSTATE') || str_contains($errorMessage, 'PDOException')) {
                $errorMessage = translate('messages.failed_to_add_employee');
            }
            session()->flash('validation_error', $errorMessage);
            \Log::error('Employee Add Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput();
        }
    }

    public function getUpdateView(string|int $id): RedirectResponse|View
    {
        $employee = $this->employeeRepo->getFirstWhereExceptAdmin(params: ['id' => $id], relations: ['role', 'zone']);
        if (!$employee) {
            Toastr::error(translate('messages.employee_not_found'));
            return redirect()->route('admin.users.employee.list');
        }

        $data = $this->employeeService->adminCheck(employee: $employee);
        $roles = Role::where('name', '!=', 'super-admin')->get();
        $zones = [];
        if (class_exists(Zone::class) && \Illuminate\Support\Facades\Schema::hasTable('zones')) {
            $zones = Zone::active()->get();
        }

        // Load access control assignments for this employee
        $assignments = $employee->activeAssignments()
            ->with(['department', 'departmentUnit', 'geography', 'role'])
            ->get();

        if (array_key_exists('flag', $data) && $data['flag'] == 'authorized') {
            return view(EmployeeViewPath::UPDATE['VIEW'], compact('roles', 'employee', 'zones', 'assignments'));
        }

        Toastr::warning(translate('messages.access_denied'));
        return back();
    }

    public function update(EmployeeUpdateRequest $request, $id): RedirectResponse|View
    {
        try {
            $employee = $this->employeeRepo->getFirstWhereExceptAdmin(params: ['id' => $id]);
            if (!$employee) {
                Toastr::error(translate('messages.employee_not_found'));
                return redirect()->route('admin.users.employee.list');
            }

            $this->employeeRepo->update(id: $id, data: $this->employeeService->getUpdateData(request: $request, employee: $employee));
            Toastr::success(translate('messages.employee_updated_successfully'));
            return redirect()->route('admin.users.employee.list');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            $firstError = null;
            foreach ($errors as $field => $messages) {
                if (!empty($messages)) {
                    $firstError = $messages[0];
                    break;
                }
            }
            if ($firstError) {
                Toastr::error($firstError);
            }
            return back()->withInput();
        } catch (\Exception $e) {
            Toastr::error(translate('messages.failed_to_update_employee') . ': ' . $e->getMessage());
            \Log::error('Employee Update Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput();
        }
    }

    public function delete($id): RedirectResponse|View
    {
        try {
            $employee = $this->employeeRepo->getFirstWhereExceptAdmin(params: ['id' => $id]);
            if (!$employee) {
                Toastr::error(translate('messages.employee_not_found'));
                return redirect()->route('admin.users.employee.list');
            }

            // Prevent self-deletion
            if (auth()->check() && auth()->id() == $employee->id) {
                Toastr::error(translate('messages.cannot_delete_yourself'));
                return redirect()->route('admin.users.employee.list');
            }

            $this->employeeRepo->delete(id: $id);
            Toastr::success(translate('messages.employee_deleted_successfully'));
        } catch (\Exception $e) {
            Toastr::error(translate('messages.failed_to_delete_employee') . ': ' . $e->getMessage());
            \Log::error('Employee Delete Error', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
        
        return redirect()->route('admin.users.employee.list');
    }

    public function search(Request $request): JsonResponse
    {
        $employees = $this->employeeRepo->getSearchList($request);
        return response()->json([
            'view' => view(EmployeeViewPath::SEARCH['VIEW'], compact('employees'))->render(),
            'count' => $employees->count()
        ]);
    }
}
