<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Parent Categories (only insert if they don't exist)
        $categories = [
            ['id' => 1, 'parent_id' => null, 'name' => 'Electronics', 'slug' => 'electronics', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'parent_id' => null, 'name' => 'Fashion', 'slug' => 'fashion', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'parent_id' => null, 'name' => 'Home & Kitchen', 'slug' => 'home-kitchen', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['id' => $category['id']],
                $category
            );
        }

        // Sub Categories (only insert if they don't exist)
        $subCategories = [
            ['id' => 4, 'parent_id' => 1, 'name' => 'Mobile Phones', 'slug' => 'mobile-phones', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'parent_id' => 1, 'name' => 'Laptops', 'slug' => 'laptops', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'parent_id' => 1, 'name' => 'Headphones', 'slug' => 'headphones', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'parent_id' => 2, 'name' => 'Men\'s Clothing', 'slug' => 'mens-clothing', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'parent_id' => 2, 'name' => 'Women\'s Clothing', 'slug' => 'womens-clothing', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'parent_id' => 2, 'name' => 'Shoes', 'slug' => 'shoes', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($subCategories as $subCategory) {
            DB::table('categories')->updateOrInsert(
                ['id' => $subCategory['id']],
                $subCategory
            );
        }

        // Additional 5 Dummy Ecommerce Categories
        $dummyCategories = [
            ['id' => 10, 'parent_id' => null, 'name' => 'Beauty & Personal Care', 'slug' => 'beauty-personal-care', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'parent_id' => null, 'name' => 'Sports & Fitness', 'slug' => 'sports-fitness', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'parent_id' => null, 'name' => 'Books & Media', 'slug' => 'books-media', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'parent_id' => null, 'name' => 'Toys & Games', 'slug' => 'toys-games', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'parent_id' => null, 'name' => 'Automotive', 'slug' => 'automotive', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($dummyCategories as $category) {
            DB::table('categories')->updateOrInsert(
                ['id' => $category['id']],
                $category
            );
        }

        $this->command->info('Successfully seeded categories including 5 dummy ecommerce categories!');
    }
}
