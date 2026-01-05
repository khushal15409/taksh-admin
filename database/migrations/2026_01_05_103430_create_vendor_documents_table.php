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
        Schema::create('vendor_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->enum('document_type', [
                'aadhaar',
                'pan',
                'bank',
                'gst',
                'non_gst',
                'msme',
                'fssai',
                'shop_agreement'
            ]);
            $table->string('document_number')->nullable();
            $table->string('document_file');
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            $table->index('vendor_id');
            $table->index('document_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_documents');
    }
};
