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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('vendor_name');
            $table->string('shop_name');
            $table->string('email');
            $table->string('mobile_number', 15);
            $table->string('gst_number')->nullable();
            $table->string('pan_number')->nullable();
            $table->text('address');
            $table->foreignId('state_id')->constrained('states')->onDelete('cascade');
            $table->foreignId('city_id')->constrained('cities')->onDelete('cascade');
            $table->string('pincode', 10);
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('ifsc_code');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->foreignId('assigned_salesman_id')->nullable()->constrained('users')->onDelete('set null')->index();
            $table->enum('verification_status', ['pending', 'assigned', 'verified', 'rejected'])->default('pending')->index();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index('state_id');
            $table->index('city_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
