<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        // Guest Carts (using fixed tokens for testing)
        $guestCarts = [
            [
                'id' => 1,
                'user_id' => null,
                'guest_token' => 'guest_token_test_12345678901234567890',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'user_id' => null,
                'guest_token' => 'guest_token_test_09876543210987654321',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('carts')->insert($guestCarts);

        // User Carts
        $userCarts = [
            [
                'id' => 3,
                'user_id' => 1, // John Doe
                'guest_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'user_id' => 2, // Jane Smith
                'guest_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'user_id' => 3, // Raj Patel
                'guest_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('carts')->insert($userCarts);

        // Cart Items
        $cartItems = [
            // Guest Cart 1 items
            ['id' => 1, 'cart_id' => 1, 'product_variant_id' => 1, 'qty' => 2, 'created_at' => now(), 'updated_at' => now()], // iPhone 15 Pro 128GB
            ['id' => 2, 'cart_id' => 1, 'product_variant_id' => 6, 'qty' => 1, 'created_at' => now(), 'updated_at' => now()], // Nike Air Max 90 Size S
            
            // Guest Cart 2 items
            ['id' => 3, 'cart_id' => 2, 'product_variant_id' => 4, 'qty' => 1, 'created_at' => now(), 'updated_at' => now()], // Samsung Galaxy S24 128GB
            ['id' => 4, 'cart_id' => 2, 'product_variant_id' => 11, 'qty' => 1, 'created_at' => now(), 'updated_at' => now()], // Sony Headphones
            
            // User Cart 1 (John Doe) items
            ['id' => 5, 'cart_id' => 3, 'product_variant_id' => 2, 'qty' => 1, 'created_at' => now(), 'updated_at' => now()], // iPhone 15 Pro 256GB
            ['id' => 6, 'cart_id' => 3, 'product_variant_id' => 9, 'qty' => 2, 'created_at' => now(), 'updated_at' => now()], // Adidas Ultraboost Black
            
            // User Cart 2 (Jane Smith) items
            ['id' => 7, 'cart_id' => 4, 'product_variant_id' => 5, 'qty' => 1, 'created_at' => now(), 'updated_at' => now()], // Samsung Galaxy S24 256GB
            ['id' => 8, 'cart_id' => 4, 'product_variant_id' => 7, 'qty' => 1, 'created_at' => now(), 'updated_at' => now()], // Nike Air Max 90 Size M
            ['id' => 9, 'cart_id' => 4, 'product_variant_id' => 10, 'qty' => 1, 'created_at' => now(), 'updated_at' => now()], // Adidas Ultraboost White
            
            // User Cart 3 (Raj Patel) items
            ['id' => 10, 'cart_id' => 5, 'product_variant_id' => 3, 'qty' => 1, 'created_at' => now(), 'updated_at' => now()], // iPhone 15 Pro 512GB
            ['id' => 11, 'cart_id' => 5, 'product_variant_id' => 8, 'qty' => 1, 'created_at' => now(), 'updated_at' => now()], // Nike Air Max 90 Size L
        ];

        DB::table('cart_items')->insert($cartItems);
    }
}
