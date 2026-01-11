<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use App\Models\Geography;
use App\Models\UserAssignment;
use App\Models\Pincode;
use Illuminate\Database\Eloquent\Collection;

/**
 * Example Policy demonstrating access control using:
 * - Permissions (actions like view, edit, delete)
 * - Geography (data scope based on location hierarchy)
 * - Department Unit (data scope based on organizational unit)
 * - Role Hierarchy (reporting relationships)
 */
class OrderAccessPolicy
{
    /**
     * Helper: Get user's active assignments with all related data.
     */
    protected function getUserAssignments(User $user): Collection
    {
        return $user->activeAssignments()
            ->with(['department', 'departmentUnit', 'geography', 'role'])
            ->get();
    }

    /**
     * Helper: Check if user has permission (using Spatie Permission).
     */
    protected function hasPermission(User $user, string $permission): bool
    {
        return $user->hasPermissionTo($permission) || $user->hasRole('super-admin');
    }

    /**
     * Helper: Check if order's geography is accessible by user.
     * This checks if the order's geography matches or is descendant/ancestor of user's geography.
     */
    protected function hasGeographyAccess(User $user, Order $order): bool
    {
        $userAssignments = $this->getUserAssignments($user);

        // If user has assignment without geography restriction (null), they have access to all
        if ($userAssignments->contains(fn($assignment) => $assignment->geography_id === null)) {
            return true;
        }

        // Get order's geography from address pincode
        $orderGeography = $this->getOrderGeography($order);
        
        if (!$orderGeography) {
            // If order has no geography, check if user has unrestricted access
            return $userAssignments->contains(fn($assignment) => $assignment->geography_id === null);
        }

        // Check if user's geography assignments include this order's geography
        foreach ($userAssignments as $assignment) {
            if (!$assignment->geography) {
                continue;
            }

            $userGeography = $assignment->geography;

            // Exact match
            if ($userGeography->id === $orderGeography->id) {
                return true;
            }

            // User's geography is ancestor of order's geography (broader scope)
            if ($userGeography->isAncestorOf($orderGeography)) {
                return true;
            }

            // User's geography is descendant of order's geography (narrower scope - shouldn't happen but check anyway)
            if ($orderGeography->isAncestorOf($userGeography)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Helper: Get geography from order's address.
     * Since Address has area_id, state_id, city_id, we'll find geography by these.
     */
    protected function getOrderGeography(Order $order): ?Geography
    {
        if (!$order->address) {
            return null;
        }

        $address = $order->address;

        // Try to find by area first (most specific)
        if ($address->area_id) {
            $geography = Geography::where('area_id', $address->area_id)
                ->where('level', 'area')
                ->first();
            if ($geography) {
                return $geography;
            }
        }

        // Try state
        if ($address->state_id) {
            $geography = Geography::where('state_id', $address->state_id)
                ->where('level', 'state')
                ->first();
            if ($geography) {
                return $geography;
            }
        }

        // Try to find by pincode string match (if pincode table has relationship)
        if ($address->pincode) {
            $pincode = Pincode::where('pincode', $address->pincode)->first();
            if ($pincode) {
                return Geography::where('pincode_id', $pincode->id)
                    ->where('level', 'pincode')
                    ->first();
            }
        }

        return null;
    }

    /**
     * Helper: Check if order is accessible by user's department unit.
     */
    protected function hasUnitAccess(User $user, Order $order): bool
    {
        $userAssignments = $this->getUserAssignments($user);

        // If user has assignment without unit restriction (null), they have access
        if ($userAssignments->contains(fn($assignment) => $assignment->department_unit_id === null)) {
            return true;
        }

        // Check if order's warehouse/fulfillment center matches user's unit
        // This is a simplified example - you may need to map warehouses to units differently
        foreach ($userAssignments as $assignment) {
            if (!$assignment->departmentUnit) {
                continue;
            }

            // Example: Check if order's warehouse belongs to user's unit
            // You may need to add a warehouse_id or unit_id field to orders/warehouses
            // For now, we'll assume all orders are accessible if unit matches department
            if ($assignment->departmentUnit->department_id) {
                return true; // Simplified - implement your actual unit matching logic
            }
        }

        return false;
    }

    /**
     * Helper: Check if user can access data based on role hierarchy.
     * Users can view data of their subordinates.
     */
    protected function canAccessByHierarchy(User $user, User $targetUser): bool
    {
        // Get user's role hierarchy level
        $userRoleLevel = $this->getRoleLevel($user);
        $targetRoleLevel = $this->getRoleLevel($targetUser);

        // If user's role is higher or equal, they can access
        return $userRoleLevel <= $targetRoleLevel;
    }

    /**
     * Helper: Get numeric level for role hierarchy.
     * Lower number = higher in hierarchy.
     */
    protected function getRoleLevel(User $user): int
    {
        $userAssignments = $this->getUserAssignments($user);
        
        $levels = [
            'HOD' => 1,
            'Senior Manager' => 2,
            'Manager' => 3,
            'TL' => 4,
            'Executive' => 5,
        ];

        $minLevel = 999;
        foreach ($userAssignments as $assignment) {
            if ($assignment->role) {
                $roleName = $assignment->role->name;
                $level = $levels[$roleName] ?? 999;
                $minLevel = min($minLevel, $level);
            }
        }

        return $minLevel;
    }

    /**
     * Determine whether the user can view any orders.
     */
    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, 'view orders');
    }

    /**
     * Determine whether the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        // Check permission first
        if (!$this->hasPermission($user, 'view orders')) {
            return false;
        }

        // Check geography access
        if (!$this->hasGeographyAccess($user, $order)) {
            return false;
        }

        // Check unit access
        if (!$this->hasUnitAccess($user, $order)) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can create orders.
     */
    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'create orders');
    }

    /**
     * Determine whether the user can update the order.
     */
    public function update(User $user, Order $order): bool
    {
        // Check permission
        if (!$this->hasPermission($user, 'edit orders')) {
            return false;
        }

        // Check geography and unit access
        if (!$this->hasGeographyAccess($user, $order) || !$this->hasUnitAccess($user, $order)) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the order.
     */
    public function delete(User $user, Order $order): bool
    {
        // Check permission
        if (!$this->hasPermission($user, 'delete orders')) {
            return false;
        }

        // Only HOD and Senior Manager can delete (based on role)
        $userRoleLevel = $this->getRoleLevel($user);
        if ($userRoleLevel > 2) {
            return false;
        }

        // Check geography and unit access
        if (!$this->hasGeographyAccess($user, $order) || !$this->hasUnitAccess($user, $order)) {
            return false;
        }

        return true;
    }

    /**
     * Scope orders query based on user's access.
     * This method should be called in controllers to filter orders.
     */
    public function scopeForUser($query, User $user)
    {
        $userAssignments = $this->getUserAssignments($user);

        // Get accessible geography IDs
        $accessibleGeographyIds = collect();
        $hasUnrestrictedGeography = false;

        foreach ($userAssignments as $assignment) {
            if ($assignment->geography_id === null) {
                $hasUnrestrictedGeography = true;
                break;
            }

            $geography = $assignment->geography;
            if ($geography) {
                // Add the geography itself
                $accessibleGeographyIds->push($geography->id);
                
                // Add all descendants (child geographies)
                $descendants = Geography::where('path', 'like', ($geography->path ?? '') . '/%')
                    ->orWhere('path', 'like', ($geography->path ?? '') . '%')
                    ->pluck('id');
                
                $accessibleGeographyIds = $accessibleGeographyIds->merge($descendants);
            }
        }

        // If no geography restriction, don't filter by geography
        if (!$hasUnrestrictedGeography && $accessibleGeographyIds->isNotEmpty()) {
            // Filter orders by geography (through address area, state, or pincode)
            $query->whereHas('address', function ($q) use ($accessibleGeographyIds) {
                $q->where(function ($addressQuery) use ($accessibleGeographyIds) {
                    // Check by area_id
                    $addressQuery->whereHas('area', function ($areaQuery) use ($accessibleGeographyIds) {
                        $areaQuery->whereHas('geographies', function ($geoQuery) use ($accessibleGeographyIds) {
                            $geoQuery->whereIn('geographies.id', $accessibleGeographyIds);
                        });
                    })
                    // Or by state_id
                    ->orWhereHas('state', function ($stateQuery) use ($accessibleGeographyIds) {
                        $stateQuery->whereHas('geographies', function ($geoQuery) use ($accessibleGeographyIds) {
                            $geoQuery->whereIn('geographies.id', $accessibleGeographyIds);
                        });
                    });
                });
            });
        }

        // Filter by department unit if needed
        // This is simplified - implement based on your actual data structure
        $restrictedUnits = $userAssignments->whereNotNull('department_unit_id')->pluck('department_unit_id');
        
        if ($restrictedUnits->isNotEmpty() && !$userAssignments->contains(fn($a) => $a->department_unit_id === null)) {
            // Filter by unit - implement based on your warehouse/order relationship
            // $query->whereHas('warehouse', function ($q) use ($restrictedUnits) { ... });
        }

        return $query;
    }
}
