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
            // user_type and is_active already exist from previous migrations
            // Just ensure user_type has index if it doesn't already
            if (!Schema::hasColumn('users', 'user_type')) {
                $table->string('user_type')->nullable()->after('name')->index();
            }

            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(false)->after('vendor_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop columns as they may be used by other modules
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
