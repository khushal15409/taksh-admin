<?php

namespace App\Traits;

use App\Models\Geography;
use App\Models\UserAssignment;
use Illuminate\Database\Eloquent\Collection;

/**
 * Trait for access control checks on models.
 * Use this trait in controllers or services to check user access.
 */
trait HasAccessControl
{
    /**
     * Check if user has access to a geography.
     */
    public function userHasGeographyAccess($user, ?Geography $geography): bool
    {
        if (!$geography) {
            // If no geography specified, check if user has unrestricted access
            return $this->userHasUnrestrictedGeographyAccess($user);
        }

        $assignments = $user->activeAssignments()->with('geography')->get();

        foreach ($assignments as $assignment) {
            // Null geography means unrestricted access
            if ($assignment->geography_id === null) {
                return true;
            }

            $userGeography = $assignment->geography;
            if (!$userGeography) {
                continue;
            }

            // Exact match
            if ($userGeography->id === $geography->id) {
                return true;
            }

            // User's geography is ancestor (broader scope)
            if ($userGeography->isAncestorOf($geography)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has unrestricted geography access.
     */
    public function userHasUnrestrictedGeographyAccess($user): bool
    {
        return $user->activeAssignments()
            ->whereNull('geography_id')
            ->exists();
    }

    /**
     * Check if user has access to a department unit.
     */
    public function userHasUnitAccess($user, ?int $unitId): bool
    {
        if ($unitId === null) {
            return true; // No unit restriction
        }

        $assignments = $user->activeAssignments()->get();

        foreach ($assignments as $assignment) {
            // Null unit means access to all units in department
            if ($assignment->department_unit_id === null) {
                // Check if unit belongs to user's department
                if ($assignment->department_id) {
                    return true; // Simplified - should check if unit belongs to department
                }
            }

            // Exact match
            if ($assignment->department_unit_id === $unitId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has permission (using Spatie Permission).
     */
    public function userHasPermission($user, string $permission): bool
    {
        return $user->hasPermissionTo($permission) || $user->hasRole('super-admin');
    }

    /**
     * Get user's accessible geography IDs (including descendants).
     */
    public function getUserAccessibleGeographyIds($user): Collection
    {
        $assignments = $user->activeAssignments()->with('geography')->get();
        $geographyIds = collect();

        foreach ($assignments as $assignment) {
            if ($assignment->geography_id === null) {
                // Unrestricted - return all geography IDs
                return Geography::active()->pluck('id');
            }

            $geography = $assignment->geography;
            if ($geography) {
                $geographyIds->push($geography->id);
                
                // Add descendants
                $descendants = Geography::where('path', 'like', ($geography->path ?? '') . '/%')
                    ->orWhere('path', 'like', ($geography->path ?? '') . '%')
                    ->pluck('id');
                
                $geographyIds = $geographyIds->merge($descendants);
            }
        }

        return $geographyIds->unique();
    }

    /**
     * Get user's accessible department unit IDs.
     */
    public function getUserAccessibleUnitIds($user): Collection
    {
        $assignments = $user->activeAssignments()->get();
        $unitIds = collect();

        foreach ($assignments as $assignment) {
            if ($assignment->department_unit_id === null) {
                // If null, user has access to all units in department
                // Return all unit IDs for the department
                $departmentUnits = \App\Models\DepartmentUnit::where('department_id', $assignment->department_id)
                    ->pluck('id');
                $unitIds = $unitIds->merge($departmentUnits);
            } else {
                $unitIds->push($assignment->department_unit_id);
            }
        }

        return $unitIds->unique();
    }

    /**
     * Check if user can access data based on role hierarchy.
     */
    public function userCanAccessByHierarchy($user, $targetUser): bool
    {
        $roleLevels = [
            'HOD' => 1,
            'Senior Manager' => 2,
            'Manager' => 3,
            'TL' => 4,
            'Executive' => 5,
        ];

        $userLevel = $this->getUserRoleLevel($user);
        $targetLevel = $this->getUserRoleLevel($targetUser);

        // User can access if their level is equal or higher (lower number)
        return $userLevel <= $targetLevel;
    }

    /**
     * Get user's role level (lower = higher in hierarchy).
     */
    protected function getUserRoleLevel($user): int
    {
        $roleLevels = [
            'HOD' => 1,
            'Senior Manager' => 2,
            'Manager' => 3,
            'TL' => 4,
            'Executive' => 5,
        ];

        $assignments = $user->activeAssignments()->with('role')->get();
        $minLevel = 999;

        foreach ($assignments as $assignment) {
            if ($assignment->role) {
                $level = $roleLevels[$assignment->role->name] ?? 999;
                $minLevel = min($minLevel, $level);
            }
        }

        return $minLevel;
    }
}

