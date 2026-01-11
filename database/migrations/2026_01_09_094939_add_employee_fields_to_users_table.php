<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add employee fields if they don't exist
            if (!Schema::hasColumn('users', 'f_name')) {
                $table->string('f_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'l_name')) {
                $table->string('l_name')->nullable()->after('f_name');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('mobile');
            }
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->unsignedBigInteger('role_id')->nullable()->after('user_type');
            }
            if (!Schema::hasColumn('users', 'zone_id')) {
                $table->unsignedBigInteger('zone_id')->nullable()->after('role_id');
            }
            if (!Schema::hasColumn('users', 'is_logged_in')) {
                $table->boolean('is_logged_in')->default(0)->after('zone_id');
            }
            
            // Add foreign keys if tables exist
            if (Schema::hasTable('roles') && Schema::hasColumn('users', 'role_id')) {
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            }
            if (Schema::hasTable('zones') && Schema::hasColumn('users', 'zone_id')) {
                $table->foreign('zone_id')->references('id')->on('zones')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign keys first
            if (Schema::hasColumn('users', 'zone_id')) {
                $table->dropForeign(['zone_id']);
            }
            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropForeign(['role_id']);
            }
            
            // Drop columns
            $columns = ['f_name', 'l_name', 'phone', 'role_id', 'zone_id', 'is_logged_in'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
