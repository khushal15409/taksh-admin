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
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->tinyInteger('rating'); // 1-5
            $table->string('review_title')->nullable();
            $table->text('review_text')->nullable();
            $table->boolean('is_verified_purchase')->default(false);
            $table->boolean('is_approved')->default(true);
            $table->timestamps();

            $table->index('product_id');
            $table->index('rating');
            $table->index('is_approved');

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
