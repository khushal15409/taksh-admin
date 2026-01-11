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
        Schema::create('delivery_men', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('fulfillment_center_id')->constrained('fulfillment_centers')->onDelete('cascade');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('mobile_number', 15);
            $table->text('address');
            $table->string('pincode', 10);
            $table->foreignId('state_id')->constrained('states')->onDelete('cascade');
            $table->foreignId('city_id')->constrained('cities')->onDelete('cascade');
            $table->enum('vehicle_type', ['bike', 'cycle', 'scooter']);
            $table->string('vehicle_number')->nullable();
            $table->string('driving_license_number');
            $table->string('aadhaar_number', 12);
            $table->string('profile_photo')->nullable();
            $table->string('aadhaar_front')->nullable();
            $table->string('aadhaar_back')->nullable();
            $table->string('driving_license_photo')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('fulfillment_center_id');
            $table->index('pincode');
            $table->index('mobile_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_men');
    }
};
