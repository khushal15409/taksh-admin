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
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('fulfillment_center_id')
                ->nullable()
                ->after('brand_id')
                ->constrained('fulfillment_centers')
                ->onDelete('set null');
            
            $table->index('fulfillment_center_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['fulfillment_center_id']);
            $table->dropIndex(['fulfillment_center_id']);
            $table->dropColumn('fulfillment_center_id');
        });
    }
};
