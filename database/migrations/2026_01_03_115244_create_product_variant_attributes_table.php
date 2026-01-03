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
        Schema::create('product_variant_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->foreignId('product_attribute_id')->constrained('product_attributes')->onDelete('cascade');
            $table->foreignId('product_attribute_value_id')->constrained('product_attribute_values')->onDelete('cascade');
            $table->timestamps();
            
            $table->index('product_variant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variant_attributes');
    }
};
