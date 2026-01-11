<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update user_type in users table
        DB::table('users')
            ->where('user_type', 'delivery_boy')
            ->update(['user_type' => 'delivery_man']);

        // 2. Rename delivery_boys table to delivery_men
        if (Schema::hasTable('delivery_boys') && !Schema::hasTable('delivery_men')) {
            Schema::rename('delivery_boys', 'delivery_men');
        }

        // 3. Update Spatie roles
        // Create new delivery-man role if it doesn't exist
        $newRole = Role::firstOrCreate(
            ['name' => 'delivery-man', 'guard_name' => 'web'],
            ['name' => 'delivery-man', 'guard_name' => 'web']
        );

        // Get old role
        $oldRole = Role::where('name', 'delivery-boy')->where('guard_name', 'web')->first();
        
        if ($oldRole) {
            // Assign new role to all users who have old role
            $usersWithOldRole = DB::table('model_has_roles')
                ->where('role_id', $oldRole->id)
                ->get();

            foreach ($usersWithOldRole as $userRole) {
                // Check if user already has new role
                $hasNewRole = DB::table('model_has_roles')
                    ->where('model_id', $userRole->model_id)
                    ->where('model_type', $userRole->model_type)
                    ->where('role_id', $newRole->id)
                    ->exists();

                if (!$hasNewRole) {
                    DB::table('model_has_roles')->insert([
                        'role_id' => $newRole->id,
                        'model_type' => $userRole->model_type,
                        'model_id' => $userRole->model_id,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Revert user_type
        DB::table('users')
            ->where('user_type', 'delivery_man')
            ->update(['user_type' => 'delivery_boy']);

        // 2. Rename table back
        if (Schema::hasTable('delivery_men') && !Schema::hasTable('delivery_boys')) {
            Schema::rename('delivery_men', 'delivery_boys');
        }

        // 3. Revert roles (keep both for safety)
        // Don't delete new role, just keep both
    }
};
