<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing categories (delete all)
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('Cleared existing categories.');

        // Parent Categories
        $parentCategories = [
            ['id' => 1, 'parent_id' => null, 'name' => 'Food', 'slug' => 'food', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'parent_id' => null, 'name' => 'Grocery', 'slug' => 'grocery', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'parent_id' => null, 'name' => 'Fashion & Apparel', 'slug' => 'fashion-apparel', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'parent_id' => null, 'name' => 'Footwear', 'slug' => 'footwear', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'parent_id' => null, 'name' => 'Electronics', 'slug' => 'electronics', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'parent_id' => null, 'name' => 'Beauty, Cosmetics & Imitation Jewellery', 'slug' => 'beauty-cosmetics-imitation-jewellery', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'parent_id' => null, 'name' => 'Kids Toys & Baby Care Products', 'slug' => 'kids-toys-baby-care-products', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'parent_id' => null, 'name' => 'Healthcare & Fitness', 'slug' => 'healthcare-fitness', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'parent_id' => null, 'name' => 'Stationery, Books, Sports & Gift Items', 'slug' => 'stationery-books-sports-gift-items', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'parent_id' => null, 'name' => 'Pet Products', 'slug' => 'pet-products', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'parent_id' => null, 'name' => 'Home, Party Decor & Kitchen Appliances', 'slug' => 'home-party-decor-kitchen-appliances', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'parent_id' => null, 'name' => 'Furniture, Office & Household Equipment', 'slug' => 'furniture-office-household-equipment', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'parent_id' => null, 'name' => 'Auto Parts', 'slug' => 'auto-parts', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'parent_id' => null, 'name' => 'Fashion Accessories', 'slug' => 'fashion-accessories', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($parentCategories as $category) {
            DB::table('categories')->insert($category);
        }

        // Sub Categories
        $subCategories = [
            // Food (ID: 1) -> Ready to eat
            ['id' => 15, 'parent_id' => 1, 'name' => 'Ready to eat', 'slug' => 'ready-to-eat', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],

            // Grocery (ID: 2) -> Kirana, Vegetable, Milk
            ['id' => 16, 'parent_id' => 2, 'name' => 'Kirana', 'slug' => 'kirana', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 17, 'parent_id' => 2, 'name' => 'Vegetable', 'slug' => 'vegetable', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 18, 'parent_id' => 2, 'name' => 'Milk', 'slug' => 'milk', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],

            // Fashion & Apparel (ID: 3) -> Male, Female, Kids
            ['id' => 19, 'parent_id' => 3, 'name' => 'Male', 'slug' => 'male-fashion-apparel', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 20, 'parent_id' => 3, 'name' => 'Female', 'slug' => 'female-fashion-apparel', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 21, 'parent_id' => 3, 'name' => 'Kids', 'slug' => 'kids-fashion-apparel', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],

            // Footwear (ID: 4) -> Male, Female, Kids
            ['id' => 22, 'parent_id' => 4, 'name' => 'Male', 'slug' => 'male-footwear', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 23, 'parent_id' => 4, 'name' => 'Female', 'slug' => 'female-footwear', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 24, 'parent_id' => 4, 'name' => 'Kids', 'slug' => 'kids-footwear', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],

            // Fashion Accessories (ID: 14) -> Male, Female, Kids, Bags, Watches, Belts, etc.
            ['id' => 25, 'parent_id' => 14, 'name' => 'Male', 'slug' => 'male-fashion-accessories', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 26, 'parent_id' => 14, 'name' => 'Female', 'slug' => 'female-fashion-accessories', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 27, 'parent_id' => 14, 'name' => 'Kids', 'slug' => 'kids-fashion-accessories', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 28, 'parent_id' => 14, 'name' => 'Bags, Watches, Belts, etc.', 'slug' => 'bags-watches-belts', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($subCategories as $subCategory) {
            DB::table('categories')->insert($subCategory);
        }

        $this->command->info('Successfully seeded categories with all vendor categories!');
        $this->command->info('Total categories created: ' . (count($parentCategories) + count($subCategories)));
    }
}
