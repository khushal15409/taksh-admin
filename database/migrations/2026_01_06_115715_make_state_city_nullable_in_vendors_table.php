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
            // Make state_id and city_id nullable to support new registration flow
            $table->foreignId('state_id')->nullable()->change();
            $table->foreignId('city_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Note: This may fail if there are NULL values
            $table->foreignId('state_id')->nullable(false)->change();
            $table->foreignId('city_id')->nullable(false)->change();
        });
    }
};
