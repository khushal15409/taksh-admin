<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super-admin role if it doesn't exist
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super-admin'],
            ['guard_name' => 'web']
        );

        // Create a default permission (you can add more as needed)
        $permission = Permission::firstOrCreate(
            ['name' => 'all-access'],
            ['guard_name' => 'web']
        );

        // Assign all permissions to super-admin role
        $superAdminRole->givePermissionTo($permission);

        // Create super-admin user if it doesn't exist
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@taksh.com'],
            [
                'mobile' => '9999999999',
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
            ]
        );

        // Update password if user exists but password is not set
        if ($superAdmin->password === null) {
            $superAdmin->password = Hash::make('admin123');
            $superAdmin->save();
        }

        // Update status and is_verified if columns exist
        if (Schema::hasColumn('users', 'is_verified')) {
            $superAdmin->is_verified = true;
        }
        if (Schema::hasColumn('users', 'status')) {
            $superAdmin->status = 'active';
        }
        $superAdmin->save();

        // Assign super-admin role to the user
        if (!$superAdmin->hasRole('super-admin')) {
            $superAdmin->assignRole('super-admin');
        }

        $this->command->info('========================================');
        $this->command->info('Super Admin created successfully!');
        $this->command->info('========================================');
        $this->command->info('Email: admin@taksh.com');
        $this->command->info('Password: admin123');
        $this->command->info('Mobile: 9999999999');
        $this->command->info('Role: super-admin');
        $this->command->info('========================================');
    }
}

