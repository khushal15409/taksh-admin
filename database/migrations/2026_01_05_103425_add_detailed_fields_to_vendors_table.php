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
        Schema::table('vendors', function (Blueprint $table) {
            // Shop Details
            if (!Schema::hasColumn('vendors', 'shop_address')) {
                $table->text('shop_address')->nullable()->after('shop_name');
            }
            if (!Schema::hasColumn('vendors', 'shop_pincode')) {
                $table->string('shop_pincode', 10)->nullable()->after('shop_address');
            }
            if (!Schema::hasColumn('vendors', 'shop_latitude')) {
                $table->decimal('shop_latitude', 10, 8)->nullable()->after('shop_pincode');
            }
            if (!Schema::hasColumn('vendors', 'shop_longitude')) {
                $table->decimal('shop_longitude', 11, 8)->nullable()->after('shop_latitude');
            }
            if (!Schema::hasColumn('vendors', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null')->after('shop_longitude');
            }
            if (!Schema::hasColumn('vendors', 'shop_images')) {
                $table->json('shop_images')->nullable()->after('category_id');
            }
            
            // Owner Details
            if (!Schema::hasColumn('vendors', 'owner_name')) {
                $table->string('owner_name')->nullable()->after('vendor_name');
            }
            if (!Schema::hasColumn('vendors', 'owner_address')) {
                $table->text('owner_address')->nullable()->after('owner_name');
            }
            if (!Schema::hasColumn('vendors', 'owner_pincode')) {
                $table->string('owner_pincode', 10)->nullable()->after('owner_address');
            }
            if (!Schema::hasColumn('vendors', 'owner_latitude')) {
                $table->decimal('owner_latitude', 10, 8)->nullable()->after('owner_pincode');
            }
            if (!Schema::hasColumn('vendors', 'owner_longitude')) {
                $table->decimal('owner_longitude', 11, 8)->nullable()->after('owner_latitude');
            }
            if (!Schema::hasColumn('vendors', 'owner_image')) {
                $table->string('owner_image')->nullable()->after('owner_longitude');
            }
            
            // Keep existing address, pincode, state_id, city_id for backward compatibility
            // They can be used as shop details if new fields are not provided
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $columns = [
                'shop_address', 'shop_pincode', 'shop_latitude', 'shop_longitude', 
                'category_id', 'shop_images',
                'owner_name', 'owner_address', 'owner_pincode', 
                'owner_latitude', 'owner_longitude', 'owner_image'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('vendors', $column)) {
                    if ($column === 'category_id') {
                        $table->dropForeign(['category_id']);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};
