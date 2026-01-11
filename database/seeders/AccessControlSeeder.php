<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\DepartmentUnit;
use App\Models\Geography;
use App\Models\State;
use App\Models\Zone;
use App\Models\Area;
use App\Models\User;
use App\Models\UserAssignment;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AccessControlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder creates:
     * 1. Geography hierarchy (India -> States -> Zones -> Areas -> Pincodes)
     * 2. Sample users with roles
     * 3. User assignments for Logistics TL, Logistics Manager, and Ecommerce HOD
     */
    public function run(): void
    {
        // First, ensure departments and units are seeded
        $this->call([
            DepartmentSeeder::class,
            DepartmentUnitSeeder::class,
        ]);

        // Create or get roles (assuming they exist or creating them)
        $roles = $this->ensureRolesExist();

        // Create geography hierarchy
        $geographies = $this->createGeographyHierarchy();

        // Create sample users
        $users = $this->createSampleUsers();

        // Create user assignments
        $this->createUserAssignments($users, $roles, $geographies);
    }

    /**
     * Ensure roles exist or create them.
     */
    protected function ensureRolesExist(): array
    {
        $roleNames = ['HOD', 'Senior Manager', 'Manager', 'TL', 'Executive'];
        $roles = [];

        foreach ($roleNames as $roleName) {
            $roles[$roleName] = Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web'],
                ['name' => $roleName, 'guard_name' => 'web']
            );
        }

        return $roles;
    }

    /**
     * Create geography hierarchy.
     */
    protected function createGeographyHierarchy(): array
    {
        $geographies = [];

        // 1. Create India (root)
        $india = Geography::firstOrCreate(
            [
                'level' => 'india',
                'code' => 'india',
            ],
            [
                'parent_id' => null,
                'name' => 'India',
                'path' => 'india',
                'is_active' => true,
            ]
        );
        $geographies['india'] = $india;

        // 2. Create States (Maharashtra and Gujarat)
        $maharashtra = Geography::firstOrCreate(
            [
                'level' => 'state',
                'code' => 'maharashtra',
            ],
            [
                'parent_id' => $india->id,
                'name' => 'Maharashtra',
                'path' => 'india/maharashtra',
                'is_active' => true,
            ]
        );
        $geographies['maharashtra'] = $maharashtra;

        $gujarat = Geography::firstOrCreate(
            [
                'level' => 'state',
                'code' => 'gujarat',
            ],
            [
                'parent_id' => $india->id,
                'name' => 'Gujarat',
                'path' => 'india/gujarat',
                'is_active' => true,
            ]
        );
        $geographies['gujarat'] = $gujarat;

        // 3. Create Zones (Mumbai Zone and Ahmedabad Zone)
        $mumbaiZone = Geography::firstOrCreate(
            [
                'level' => 'zone',
                'code' => 'mumbai-zone',
            ],
            [
                'parent_id' => $maharashtra->id,
                'name' => 'Mumbai Zone',
                'path' => 'india/maharashtra/mumbai-zone',
                'is_active' => true,
            ]
        );
        $geographies['mumbai-zone'] = $mumbaiZone;

        $ahmedabadZone = Geography::firstOrCreate(
            [
                'level' => 'zone',
                'code' => 'ahmedabad-zone',
            ],
            [
                'parent_id' => $gujarat->id,
                'name' => 'Ahmedabad Zone',
                'path' => 'india/gujarat/ahmedabad-zone',
                'is_active' => true,
            ]
        );
        $geographies['ahmedabad-zone'] = $ahmedabadZone;

        // 4. Create Areas (sample areas)
        $mumbaiArea = Geography::firstOrCreate(
            [
                'level' => 'area',
                'code' => 'mumbai-south',
            ],
            [
                'parent_id' => $mumbaiZone->id,
                'name' => 'Mumbai South',
                'path' => 'india/maharashtra/mumbai-zone/mumbai-south',
                'is_active' => true,
            ]
        );
        $geographies['mumbai-south'] = $mumbaiArea;

        $ahmedabadArea = Geography::firstOrCreate(
            [
                'level' => 'area',
                'code' => 'ahmedabad-central',
            ],
            [
                'parent_id' => $ahmedabadZone->id,
                'name' => 'Ahmedabad Central',
                'path' => 'india/gujarat/ahmedabad-zone/ahmedabad-central',
                'is_active' => true,
            ]
        );
        $geographies['ahmedabad-central'] = $ahmedabadArea;

        return $geographies;
    }

    /**
     * Create sample users.
     */
    protected function createSampleUsers(): array
    {
        $users = [];

        // Logistics TL
        $logisticsTL = User::firstOrCreate(
            ['email' => 'logistics.tl@taksh.com'],
            [
                'name' => 'Logistics Team Lead',
                'f_name' => 'Logistics',
                'l_name' => 'Team Lead',
                'email' => 'logistics.tl@taksh.com',
                'mobile' => '9876543210',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $users['logistics-tl'] = $logisticsTL;

        // Logistics Manager
        $logisticsManager = User::firstOrCreate(
            ['email' => 'logistics.manager@taksh.com'],
            [
                'name' => 'Logistics Manager',
                'f_name' => 'Logistics',
                'l_name' => 'Manager',
                'email' => 'logistics.manager@taksh.com',
                'mobile' => '9876543211',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $users['logistics-manager'] = $logisticsManager;

        // Ecommerce HOD
        $ecommerceHOD = User::firstOrCreate(
            ['email' => 'ecommerce.hod@taksh.com'],
            [
                'name' => 'Ecommerce HOD',
                'f_name' => 'Ecommerce',
                'l_name' => 'HOD',
                'email' => 'ecommerce.hod@taksh.com',
                'mobile' => '9876543212',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $users['ecommerce-hod'] = $ecommerceHOD;

        return $users;
    }

    /**
     * Create user assignments.
     */
    protected function createUserAssignments(array $users, array $roles, array $geographies): void
    {
        $logisticsDept = Department::where('code', 'LOG')->first();
        $ecommerceDept = Department::where('code', 'ECOM')->first();
        $mumbaiWarehouse = DepartmentUnit::where('code', 'warehouse-mumbai')->first();
        $mumbaiLMCenter = DepartmentUnit::where('code', 'lm-center-mumbai')->first();
        $mumbaiOffice = DepartmentUnit::where('code', 'office-mumbai')->first();

        // 1. Logistics TL - Assigned to Mumbai Warehouse, Mumbai Zone, TL role
        UserAssignment::updateOrCreate(
            [
                'user_id' => $users['logistics-tl']->id,
                'department_id' => $logisticsDept->id,
            ],
            [
                'department_unit_id' => $mumbaiWarehouse->id,
                'geography_id' => $geographies['mumbai-zone']->id,
                'role_id' => $roles['TL']->id,
                'effective_from' => now(),
                'effective_to' => null, // Indefinite
                'is_active' => true,
                'notes' => 'Logistics TL for Mumbai Warehouse and Mumbai Zone',
            ]
        );

        // Assign TL role to user
        $users['logistics-tl']->assignRole($roles['TL']);

        // 2. Logistics Manager - Assigned to Logistics Department, All Maharashtra, Manager role
        UserAssignment::updateOrCreate(
            [
                'user_id' => $users['logistics-manager']->id,
                'department_id' => $logisticsDept->id,
            ],
            [
                'department_unit_id' => null, // No specific unit - access to all logistics units
                'geography_id' => $geographies['maharashtra']->id, // Broader geography access
                'role_id' => $roles['Manager']->id,
                'effective_from' => now(),
                'effective_to' => null,
                'is_active' => true,
                'notes' => 'Logistics Manager for entire Maharashtra state',
            ]
        );

        // Assign Manager role to user
        $users['logistics-manager']->assignRole($roles['Manager']);

        // 3. Ecommerce HOD - Assigned to Ecommerce Department, All India, HOD role
        UserAssignment::updateOrCreate(
            [
                'user_id' => $users['ecommerce-hod']->id,
                'department_id' => $ecommerceDept->id,
            ],
            [
                'department_unit_id' => null, // No specific unit - access to all ecommerce units
                'geography_id' => $geographies['india']->id, // Access to entire India
                'role_id' => $roles['HOD']->id,
                'effective_from' => now(),
                'effective_to' => null,
                'is_active' => true,
                'notes' => 'Ecommerce Head of Department with pan-India access',
            ]
        );

        // Assign HOD role to user
        $users['ecommerce-hod']->assignRole($roles['HOD']);

        // Assign some permissions (example - you should define these in your permission seeder)
        // $users['logistics-tl']->givePermissionTo(['view orders', 'edit orders']);
        // $users['logistics-manager']->givePermissionTo(['view orders', 'edit orders', 'delete orders']);
        // $users['ecommerce-hod']->givePermissionTo(['view orders', 'edit orders', 'delete orders', 'create orders']);
    }
}
