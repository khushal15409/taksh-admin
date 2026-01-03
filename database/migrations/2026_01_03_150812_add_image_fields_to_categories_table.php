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
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'image_url')) {
                $table->string('image_url')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('categories', 'icon_url')) {
                $table->string('icon_url')->nullable()->after('image_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'image_url')) {
                $table->dropColumn('image_url');
            }
            if (Schema::hasColumn('categories', 'icon_url')) {
                $table->dropColumn('icon_url');
            }
        });
    }
};
