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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained('returns')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cod', 'online']);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->timestamps();
            
            $table->index('return_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
