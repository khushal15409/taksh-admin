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
        Schema::create('user_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('department_unit_id')->nullable()->constrained('department_units')->onDelete('cascade')->comment('Null means user is assigned to department but no specific unit');
            $table->foreignId('geography_id')->nullable()->constrained('geographies')->onDelete('cascade')->comment('Null means user has access to all geographies');
            
            // Reference to Spatie Permission Role (not foreign key as it's in different table)
            $table->unsignedBigInteger('role_id')->nullable()->comment('Spatie Permission Role ID');
            
            // Effective date range for assignment
            $table->date('effective_from')->nullable()->comment('Assignment start date');
            $table->date('effective_to')->nullable()->comment('Assignment end date (null = indefinite)');
            
            // Additional metadata
            $table->text('notes')->nullable()->comment('Admin notes about this assignment');
            $table->boolean('is_active')->default(true);
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null')->comment('User who created this assignment');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['user_id', 'is_active']);
            $table->index(['department_id', 'is_active']);
            $table->index(['geography_id', 'is_active']);
            $table->index(['role_id', 'is_active']);
            $table->index(['effective_from', 'effective_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_assignments');
    }
};
