<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Enums\ViewPaths\Admin\CustomRole as CustomRoleViewPath;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomRoleAddRequest;
use App\Http\Requests\Admin\CustomRoleUpdateRequest;
use App\Repositories\CustomRoleRepository;
use App\Services\CustomRoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\CentralLogics\ToastrWrapper as Toastr;
use Illuminate\View\View;

class CustomRoleController extends Controller
{
    public function __construct(
        protected CustomRoleRepository $roleRepo,
        protected CustomRoleService $roleService
    )
    {
    }

    public function index(?Request $request): View
    {
        return $this->getAddView();
    }

    private function getAddView(): View
    {
        $roles = $this->roleRepo->getListWhere(
            searchValue: request()?->search,
            dataLimit: 25
        );
        $language = []; // Simplified - can be enhanced later
        $defaultLang = str_replace('_', '-', app()->getLocale());
        return view(CustomRoleViewPath::ADD['VIEW'], compact('roles','language','defaultLang'));
    }

    public function add(CustomRoleAddRequest $request): RedirectResponse
    {
        try {
            $data = $this->roleService->getAddData(request: $request);
            
            // Validate that permissions array is not empty
            if (empty($data['permissions']) || count($data['permissions']) === 0) {
                Toastr::error(translate('messages.Please select atleast one module'));
                return back()->withInput();
            }
            
            // Validate that name is not empty
            if (empty($data['name']) || trim($data['name']) === '') {
                Toastr::error(translate('messages.Role name is required!'));
                return back()->withInput();
            }
            
            $role = $this->roleRepo->add(data: $data);
            
            Toastr::success(translate('messages.role_added_successfully'));
            return redirect()->route('admin.users.custom-role.create');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors and show only first error as Toastr (single Toastr)
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
            Toastr::error(translate('messages.failed_to_add_role') . ': ' . $e->getMessage());
            \Log::error('CustomRole Add Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput();
        }
    }

    public function getUpdateView(string|int $id): View
    {
        $data = $this->roleService->roleCheck(role: $id);

        if (array_key_exists('flag', $data) && $data['flag'] == 'unauthorized') {
            return view('errors.404');
        }
        $role = $this->roleRepo->getFirstWithoutGlobalScopeWhere(params: ['id' => $id]);
        $language = []; // Simplified - can be enhanced later
        $defaultLang = str_replace('_', '-', app()->getLocale());
        return view(CustomRoleViewPath::UPDATE['VIEW'], compact('role','language','defaultLang'));
    }

    public function update(CustomRoleUpdateRequest $request, $id): RedirectResponse|View
    {
        $data = $this->roleService->roleCheck(role: $id);

        if (array_key_exists('flag', $data) && $data['flag'] == 'unauthorized') {
            return view('errors.404');
        }

        try {
            $updateData = $this->roleService->getAddData(request: $request);
            $role = $this->roleRepo->update(id: $id, data: $updateData);
            Toastr::success(translate('messages.role_updated_successfully'));
            return redirect()->route('admin.users.custom-role.create');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors and show only first error as Toastr (single Toastr)
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
            Toastr::error(translate('messages.failed_to_update_role') . ': ' . $e->getMessage());
            \Log::error('CustomRole Update Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput();
        }
    }

    public function delete($id): RedirectResponse|View|JsonResponse
    {
        \Log::info('CustomRoleController: Delete method called', [
            'id' => $id, 
            'request_method' => request()->method(), 
            'is_ajax' => request()->ajax(),
            'wants_json' => request()->wantsJson(),
            'accept_header' => request()->header('Accept')
        ]);
        
        try {
            // Validate ID
            if (empty($id)) {
                \Log::warning('CustomRoleController: Empty ID provided');
                $errorMsg = translate('messages.invalid_role_id');
                
                if (request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json(['success' => false, 'message' => $errorMsg], 400);
                }
                Toastr::error($errorMsg);
                return redirect()->route('admin.users.custom-role.create');
            }

            // Check if role exists
            $role = \Spatie\Permission\Models\Role::find($id);
            if (!$role) {
                \Log::warning('CustomRoleController: Role not found', ['id' => $id]);
                $errorMsg = translate('messages.role_not_found');
                
                if (request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json(['success' => false, 'message' => $errorMsg], 404);
                }
                Toastr::error($errorMsg);
                return redirect()->route('admin.users.custom-role.create');
            }

            \Log::info('CustomRoleController: Role found', ['id' => $id, 'name' => $role->name]);

            // Check authorization (prevent deletion of super-admin)
            $data = $this->roleService->roleCheck(role: $id);
            if (array_key_exists('flag', $data) && $data['flag'] == 'unauthorized') {
                \Log::warning('CustomRoleController: Unauthorized deletion attempt', ['id' => $id, 'name' => $role->name]);
                $errorMsg = translate('messages.cannot_delete_super_admin_role');
                
                if (request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json(['success' => false, 'message' => $errorMsg], 403);
                }
                Toastr::error($errorMsg);
                return redirect()->route('admin.users.custom-role.create');
            }
            
            // Store role name for success message
            $roleName = $role->name;
            
            \Log::info('CustomRoleController: Attempting to delete role', ['id' => $id, 'name' => $roleName]);
            
            // Attempt to delete
            $result = $this->roleRepo->delete(id: $id);
            
            if ($result) {
                \Log::info('CustomRoleController: Role deleted successfully', ['id' => $id, 'name' => $roleName]);
                $successMsg = translate('messages.role_deleted_successfully') . ': ' . $roleName;
                
                if (request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json([
                        'success' => true, 
                        'message' => $successMsg,
                        'role_id' => $id,
                        'role_name' => $roleName
                    ]);
                }
                Toastr::success($successMsg);
            } else {
                \Log::warning('CustomRoleController: Delete returned false', ['id' => $id, 'name' => $roleName]);
                $errorMsg = translate('messages.failed_to_delete_role') . ': ' . $roleName;
                
                if (request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json(['success' => false, 'message' => $errorMsg], 500);
                }
                Toastr::error($errorMsg);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // Database constraint errors (e.g., role has users assigned)
            $errorMessage = 'Database error occurred';
            if (str_contains($e->getMessage(), 'foreign key') || str_contains($e->getMessage(), 'constraint')) {
                $errorMessage = translate('messages.role_cannot_be_deleted_has_users');
            }
            \Log::error('CustomRole Delete Query Error', [
                'id' => $id,
                'message' => $e->getMessage(),
            ]);
            
            if (request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => false, 'message' => $errorMessage], 500);
            }
            Toastr::error(translate('messages.failed_to_delete_role') . ': ' . $errorMessage);
        } catch (\Exception $e) {
            $errorMsg = translate('messages.failed_to_delete_role') . ': ' . $e->getMessage();
            \Log::error('CustomRole Delete Error', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            if (request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => false, 'message' => $errorMsg], 500);
            }
            Toastr::error($errorMsg);
        }
        
        return redirect()->route('admin.users.custom-role.create');
    }

    public function search(Request $request): JsonResponse
    {
        $roles=$this->roleRepo->getSearchList($request);
        return response()->json([
            'view'=>view(CustomRoleViewPath::SEARCH['VIEW'],compact('roles'))->render(),
            'count'=>$roles->count()
        ]);
    }
}
