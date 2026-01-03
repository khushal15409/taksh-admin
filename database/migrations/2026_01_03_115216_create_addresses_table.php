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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('state_id')->constrained('states')->onDelete('cascade');
            $table->foreignId('city_id')->constrained('cities')->onDelete('cascade');
            $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
            $table->string('name');
            $table->string('mobile', 15);
            $table->text('address_line_1');
            $table->text('address_line_2')->nullable();
            $table->string('pincode', 10);
            $table->string('landmark')->nullable();
            $table->enum('type', ['home', 'work', 'other'])->default('home');
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
