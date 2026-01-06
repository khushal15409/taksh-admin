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
            StateSeeder::class,           // Import states from SQL file
            CitySeeder::class,            // Import cities from SQL file
            // LocationSeeder::class,     // Commented out - using SQL file imports instead
            DeliveryTypeSeeder::class,     // Delivery Types
            BrandSeeder::class,            // Brands
            CategorySeeder::class,        // Categories
            ProductSeeder::class,         // Products, Variants, Attributes, Images
            WarehouseSeeder::class,        // Warehouses & Inventory
            UserSeeder::class,            // Users (must be before reviews/questions/answers)
            ProductReviewSeeder::class,   // Product Reviews
            ProductQuestionSeeder::class, // Product Questions
            ProductAnswerSeeder::class,   // Product Answers
            AddressSeeder::class,          // Addresses
            CartSeeder::class,            // Carts & Cart Items
            OrderSeeder::class,           // Orders, Payments, Returns, Refunds
            BannerSeeder::class,          // Banners
            DashboardDataSeeder::class,  // Dashboard Data (Category Images, Trending Products)
            SuperAdminSeeder::class,     // Super Admin
            Express30Seeder::class,       // Express 30 Delivery

        ]);
    }
}
