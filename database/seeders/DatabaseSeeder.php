<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed in order to respect foreign key constraints
        $this->call([
            LocationSeeder::class,        // States, Cities, Areas
            DeliveryTypeSeeder::class,     // Delivery Types
            BrandSeeder::class,            // Brands
            CategorySeeder::class,        // Categories
            ProductSeeder::class,         // Products, Variants, Attributes, Images
            WarehouseSeeder::class,        // Warehouses & Inventory
            UserSeeder::class,            // Users
            AddressSeeder::class,          // Addresses
            CartSeeder::class,            // Carts & Cart Items
            OrderSeeder::class,           // Orders, Payments, Returns, Refunds
        ]);
    }
}
