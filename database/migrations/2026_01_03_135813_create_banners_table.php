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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image_url');
            $table->enum('redirect_type', ['product', 'category', 'external', 'none'])->default('none');
            $table->unsignedBigInteger('redirect_id')->nullable();
            $table->string('redirect_url')->nullable();
            $table->enum('position', ['home_top', 'home_middle', 'home_bottom', 'dashboard'])->default('home_top');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('is_active');
            $table->index('position');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
