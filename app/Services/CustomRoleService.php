<?php

namespace App\Services;

class CustomRoleService
{
    public function getAddData(Object $request): array
    {
        // Get role name - handle both array and string formats
        $name = '';
        if (is_array($request->name)) {
            // Find the index of 'default' in lang array, or use first element
            $defaultIndex = array_search('default', $request->lang ?? []);
            if ($defaultIndex !== false && isset($request->name[$defaultIndex])) {
                $name = trim($request->name[$defaultIndex]);
            } elseif (isset($request->name[0])) {
                $name = trim($request->name[0]);
            }
        } else {
            $name = trim($request->name ?? '');
        }
        
        // Filter out empty module values and convert to permission names
        $permissions = [];
        if (is_array($request->modules)) {
            $modules = array_filter($request->modules, function($module) {
                return !empty($module) && trim($module) !== '';
            });
            
            // Convert module names to permission names (e.g., 'logistics' -> 'access-logistics')
            foreach ($modules as $module) {
                $permissions[] = 'access-' . $module;
            }
        }
        
        // Ensure we have at least one permission
        if (empty($permissions)) {
            throw new \Exception('At least one module must be selected');
        }
        
        return [
            'name' => $name,
            'permissions' => $permissions,
            'guard_name' => 'web',
        ];
    }

    public function roleCheck(string|int $role): array
    {
        // Get role by ID or name
        $roleModel = \Spatie\Permission\Models\Role::find($role);
        if (!$roleModel) {
            $roleModel = \Spatie\Permission\Models\Role::where('name', $role)->first();
        }
        
        // Prevent deletion of super-admin role
        if($roleModel && $roleModel->name === 'super-admin') {
            return ['flag' => 'unauthorized'];
        }
        return ['flag' => 'authorized'];
    }
}
