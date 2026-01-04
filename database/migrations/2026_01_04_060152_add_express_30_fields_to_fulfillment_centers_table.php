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
        Schema::table('fulfillment_centers', function (Blueprint $table) {
            $table->boolean('supports_express_30')->default(false)->after('supports_30_min_delivery');
            $table->integer('express_radius_km')->default(5)->after('supports_express_30');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fulfillment_centers', function (Blueprint $table) {
            $table->dropColumn(['supports_express_30', 'express_radius_km']);
        });
    }
};
