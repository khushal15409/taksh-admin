<?php

namespace App\Repositories;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomRoleRepository
{
    public function __construct(protected Role $role)
    {
    }

    public function add(array $data): string|object
    {
        // Create role using Spatie
        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);
        
        // Assign permissions if provided
        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $permissions = [];
            foreach ($data['permissions'] as $permissionName) {
                // Create permission if it doesn't exist
                $permission = Permission::firstOrCreate(
                    ['name' => $permissionName],
                    ['guard_name' => $data['guard_name'] ?? 'web']
                );
                $permissions[] = $permission;
            }
            $role->syncPermissions($permissions);
        }
        
        return $role;
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->role->where($params)->with('permissions')->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = 25, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->role->where('name', '!=', 'super-admin')
            ->with('permissions')
            ->get();
    }

    public function getListWhere(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = 25, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);
        return $this->role->where('name', '!=', 'super-admin')
            ->when(isset($searchValue), function($query) use($key) {
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })
            ->with('permissions')
            ->latest()
            ->paginate($dataLimit);
    }

    public function update(string $id, array $data): bool|string|object
    {
        $role = $this->role->find($id);
        if ($role) {
            $role->update([
                'name' => $data['name'],
            ]);
            
            // Sync permissions if provided
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $permissions = [];
                foreach ($data['permissions'] as $permissionName) {
                    // Create permission if it doesn't exist
                    $permission = Permission::firstOrCreate(
                        ['name' => $permissionName],
                        ['guard_name' => $data['guard_name'] ?? 'web']
                    );
                    $permissions[] = $permission;
                }
                $role->syncPermissions($permissions);
            }
        }
        return $role;
    }

    public function delete(string $id): bool
    {
        try {
            $role = $this->role->find($id);
            if (!$role) {
                \Log::warning('CustomRoleRepository: Role not found', ['id' => $id]);
                return false;
            }
            
            // Store role name before deletion for logging
            $roleName = $role->name;
            
            // Prevent deletion of super-admin role
            if ($roleName === 'super-admin') {
                \Log::warning('CustomRoleRepository: Attempted to delete super-admin role', ['id' => $id]);
                return false;
            }
            
            // Remove all permissions from role first
            try {
                $role->syncPermissions([]);
            } catch (\Exception $e) {
                \Log::warning('CustomRoleRepository: Error syncing permissions before delete', [
                    'id' => $id,
                    'error' => $e->getMessage()
                ]);
                // Continue with deletion even if permission sync fails
            }
            
            // Delete the role
            $deleted = $role->delete();
            
            if ($deleted) {
                \Log::info('CustomRoleRepository: Role deleted successfully', ['id' => $id, 'name' => $roleName]);
            } else {
                \Log::warning('CustomRoleRepository: Role delete returned false', ['id' => $id, 'name' => $roleName]);
            }
            
            return $deleted;
        } catch (\Exception $e) {
            \Log::error('CustomRoleRepository Delete Error', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function getSearchList(Request $request): Collection
    {
        $key = explode(' ', $request['search']);
        return $this->role->where('name', '!=', 'super-admin')
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            })
            ->with('permissions')
            ->latest()
            ->limit(50)
            ->get();
    }

    public function getFirstWithoutGlobalScopeWhere(array $params, array $relations = []): ?Model
    {
        return $this->role->where($params)->with('permissions')->first();
    }
}
