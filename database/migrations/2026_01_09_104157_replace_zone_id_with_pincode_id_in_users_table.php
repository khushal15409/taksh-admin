<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop zone_id column if it exists (this will also drop foreign key)
            if (Schema::hasColumn('users', 'zone_id')) {
                // Drop foreign key first if it exists
                $foreignKeys = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'users' 
                    AND COLUMN_NAME = 'zone_id' 
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");
                
                foreach ($foreignKeys as $fk) {
                    try {
                        DB::statement("ALTER TABLE `users` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
                    } catch (\Exception $e) {
                        // Continue if foreign key doesn't exist
                    }
                }
                
                $table->dropColumn('zone_id');
            }
            
            // Add pincode_id if it doesn't exist
            if (!Schema::hasColumn('users', 'pincode_id')) {
                $table->unsignedBigInteger('pincode_id')->nullable()->after('role_id');
                if (Schema::hasTable('pincodes')) {
                    $table->foreign('pincode_id')->references('id')->on('pincodes')->onDelete('set null');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop pincode_id column if it exists
            if (Schema::hasColumn('users', 'pincode_id')) {
                // Drop foreign key first if it exists
                $foreignKeys = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'users' 
                    AND COLUMN_NAME = 'pincode_id' 
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");
                
                foreach ($foreignKeys as $fk) {
                    try {
                        DB::statement("ALTER TABLE `users` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
                    } catch (\Exception $e) {
                        // Continue if foreign key doesn't exist
                    }
                }
                
                $table->dropColumn('pincode_id');
            }
            
            // Re-add zone_id if zones table exists
            if (Schema::hasTable('zones') && !Schema::hasColumn('users', 'zone_id')) {
                $table->unsignedBigInteger('zone_id')->nullable()->after('role_id');
                $table->foreign('zone_id')->references('id')->on('zones')->onDelete('set null');
            }
        });
    }
};
