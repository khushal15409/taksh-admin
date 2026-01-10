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
        Schema::create('geographies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('geographies')->onDelete('cascade')->comment('Parent geography for hierarchy');
            $table->enum('level', ['pincode', 'area', 'zone', 'state', 'india'])->comment('Geographic level');
            $table->string('name')->comment('Geography name');
            $table->string('code', 50)->nullable()->comment('Geography code');
            
            // Foreign keys to existing tables for quick lookups
            $table->unsignedBigInteger('pincode_id')->nullable()->comment('Reference to pincodes table');
            $table->unsignedBigInteger('area_id')->nullable()->comment('Reference to areas table');
            $table->unsignedBigInteger('zone_id')->nullable()->comment('Reference to zones table');
            $table->unsignedBigInteger('state_id')->nullable()->comment('Reference to states table');
            
            // Path for hierarchy traversal (e.g., "india/state-1/zone-1/area-1/pincode-1")
            $table->string('path')->nullable()->comment('Hierarchy path for efficient queries');
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['level', 'parent_id']);
            $table->index('path');
            $table->index('pincode_id');
            $table->index('area_id');
            $table->index('zone_id');
            $table->index('state_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geographies');
    }
};
