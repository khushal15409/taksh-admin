<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Parent Categories
        $categories = [
            ['id' => 1, 'parent_id' => null, 'name' => 'Electronics', 'slug' => 'electronics', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'parent_id' => null, 'name' => 'Fashion', 'slug' => 'fashion', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'parent_id' => null, 'name' => 'Home & Kitchen', 'slug' => 'home-kitchen', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('categories')->insert($categories);

        // Sub Categories
        $subCategories = [
            ['id' => 4, 'parent_id' => 1, 'name' => 'Mobile Phones', 'slug' => 'mobile-phones', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'parent_id' => 1, 'name' => 'Laptops', 'slug' => 'laptops', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'parent_id' => 1, 'name' => 'Headphones', 'slug' => 'headphones', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'parent_id' => 2, 'name' => 'Men\'s Clothing', 'slug' => 'mens-clothing', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'parent_id' => 2, 'name' => 'Women\'s Clothing', 'slug' => 'womens-clothing', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'parent_id' => 2, 'name' => 'Shoes', 'slug' => 'shoes', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('categories')->insert($subCategories);
    }
}
