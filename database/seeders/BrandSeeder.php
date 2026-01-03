<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['id' => 1, 'name' => 'Samsung', 'slug' => 'samsung', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Apple', 'slug' => 'apple', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Nike', 'slug' => 'nike', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Adidas', 'slug' => 'adidas', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Sony', 'slug' => 'sony', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('brands')->insert($brands);
    }
}
