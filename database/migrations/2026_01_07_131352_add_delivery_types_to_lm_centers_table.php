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
        Schema::table('lm_centers', function (Blueprint $table) {
            $table->boolean('thirty_min_delivery')->default(0)->after('status');
            $table->boolean('normal_delivery')->default(1)->after('thirty_min_delivery');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lm_centers', function (Blueprint $table) {
            $table->dropColumn(['thirty_min_delivery', 'normal_delivery']);
        });
    }
};
