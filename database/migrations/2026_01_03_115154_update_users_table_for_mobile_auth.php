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
        // This migration is for existing installations
        // For fresh installs, the users table is already created correctly
        if (Schema::hasColumn('users', 'email')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['email', 'email_verified_at', 'password', 'remember_token']);
            });
        }

        if (!Schema::hasColumn('users', 'mobile')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('mobile', 15)->unique()->after('id');
                $table->string('name')->nullable()->after('mobile');
                $table->boolean('is_verified')->default(false)->after('name');
                $table->enum('status', ['active', 'inactive', 'blocked'])->default('active')->after('is_verified');
                $table->index('mobile');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversing for safety
    }
};
