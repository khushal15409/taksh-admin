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
        Schema::table('vendors', function (Blueprint $table) {
            // Drop foreign key constraint if exists
            if (Schema::hasColumn('vendors', 'category_id')) {
                $table->dropForeign(['category_id']);
                // Change column type from foreignId to string to store comma-separated category IDs
                $table->string('category_id')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            if (Schema::hasColumn('vendors', 'category_id')) {
                // Change back to foreignId (will only work if all values are valid single IDs)
                $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null')->change();
            }
        });
    }
};
