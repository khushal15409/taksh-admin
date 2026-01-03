<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        // Warehouses
        $warehouses = [
            [
                'id' => 1,
                'state_id' => 1, // Gujarat
                'city_id' => 1, // Ahmedabad
                'area_id' => 1, // Navrangpura
                'name' => 'Ahmedabad Central Warehouse',
                'latitude' => 23.0225,
                'longitude' => 72.5714,
                'supports_30_min_delivery' => true,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'state_id' => 2, // Maharashtra
                'city_id' => 3, // Mumbai
                'area_id' => 4, // Andheri
                'name' => 'Mumbai West Warehouse',
                'latitude' => 19.0760,
                'longitude' => 72.8777,
                'supports_30_min_delivery' => true,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'state_id' => 3, // Delhi
                'city_id' => 5, // New Delhi
                'area_id' => 7, // Connaught Place
                'name' => 'Delhi North Warehouse',
                'latitude' => 28.6139,
                'longitude' => 77.2090,
                'supports_30_min_delivery' => false,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('warehouses')->insert($warehouses);

        // Warehouse Products (Inventory)
        // Ensure all product variants have stock in at least one warehouse
        $warehouseProducts = [];
        
        // Warehouse 1 (Ahmedabad) - All variants
        foreach (range(1, 11) as $variantId) {
            $warehouseProducts[] = [
                'warehouse_id' => 1,
                'product_variant_id' => $variantId,
                'stock_qty' => rand(50, 200),
                'reserved_qty' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        // Warehouse 2 (Mumbai) - All variants
        foreach (range(1, 11) as $variantId) {
            $warehouseProducts[] = [
                'warehouse_id' => 2,
                'product_variant_id' => $variantId,
                'stock_qty' => rand(30, 150),
                'reserved_qty' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        // Warehouse 3 (Delhi) - Selected variants
        foreach ([1, 2, 4, 5, 6, 7, 8, 11] as $variantId) {
            $warehouseProducts[] = [
                'warehouse_id' => 3,
                'product_variant_id' => $variantId,
                'stock_qty' => rand(20, 100),
                'reserved_qty' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('warehouse_products')->insert($warehouseProducts);
    }
}
