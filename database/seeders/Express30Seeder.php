<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Express30Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update fulfillment centers to enable express_30
        // Ahmedabad and Mumbai warehouses support express 30
        DB::table('fulfillment_centers')
            ->whereIn('id', [1, 2])
            ->update([
                'supports_express_30' => true,
                'express_radius_km' => 7,
                'updated_at' => now(),
            ]);

        // Mark some products as express-eligible
        // Let's mark products 1, 2, 3, 4, 5 as express eligible (all existing products)
        DB::table('products')
            ->whereIn('id', [1, 2, 3, 4, 5])
            ->update([
                'is_express_30' => true,
                'updated_at' => now(),
            ]);

        // Ensure express-eligible products have good stock in express-enabled warehouses
        // Update stock for express products in Ahmedabad and Mumbai warehouses
        $expressWarehouses = [1, 2]; // Ahmedabad and Mumbai
        $expressProductVariants = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]; // All variants

        foreach ($expressWarehouses as $warehouseId) {
            foreach ($expressProductVariants as $variantId) {
                // Check if warehouse product exists
                $exists = DB::table('warehouse_products')
                    ->where('warehouse_id', $warehouseId)
                    ->where('product_variant_id', $variantId)
                    ->exists();

                if ($exists) {
                    // Update stock to ensure good availability (minimum 50 units)
                    DB::table('warehouse_products')
                        ->where('warehouse_id', $warehouseId)
                        ->where('product_variant_id', $variantId)
                        ->update([
                            'stock_qty' => DB::raw('GREATEST(stock_qty, 50)'),
                            'updated_at' => now(),
                        ]);
                } else {
                    // Create warehouse product if it doesn't exist
                    DB::table('warehouse_products')->insert([
                        'warehouse_id' => $warehouseId,
                        'product_variant_id' => $variantId,
                        'stock_qty' => rand(50, 200),
                        'reserved_qty' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info('Express 30 delivery data seeded successfully!');
        $this->command->info('- 2 fulfillment centers enabled for express_30 (Ahmedabad, Mumbai)');
        $this->command->info('- 5 products marked as express-eligible');
        $this->command->info('- Inventory ensured for express products in express warehouses');
    }
}
