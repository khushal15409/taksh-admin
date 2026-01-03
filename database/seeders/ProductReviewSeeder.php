<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductReviewSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = [];
        
        // Product 1 (iPhone 15 Pro) - 5 reviews
        $reviews[] = [
            'product_id' => 1,
            'user_id' => 1,
            'rating' => 5,
            'review_title' => 'Excellent phone!',
            'review_text' => 'Amazing camera quality and performance. Battery life is great too. Highly recommended!',
            'is_verified_purchase' => true,
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(10),
            'updated_at' => Carbon::now()->subDays(10),
        ];
        
        $reviews[] = [
            'product_id' => 1,
            'user_id' => 2,
            'rating' => 4,
            'review_title' => 'Good but expensive',
            'review_text' => 'Great features but price is on the higher side. Overall satisfied with the purchase.',
            'is_verified_purchase' => true,
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(8),
            'updated_at' => Carbon::now()->subDays(8),
        ];
        
        $reviews[] = [
            'product_id' => 1,
            'user_id' => 3,
            'rating' => 5,
            'review_title' => 'Best iPhone yet',
            'review_text' => 'Love the titanium build and the new action button. Camera improvements are noticeable.',
            'is_verified_purchase' => true,
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(5),
            'updated_at' => Carbon::now()->subDays(5),
        ];
        
        $reviews[] = [
            'product_id' => 1,
            'user_id' => 4,
            'rating' => 4,
            'review_title' => 'Solid upgrade',
            'review_text' => 'Upgraded from iPhone 13. The improvements are worth it, especially the camera.',
            'is_verified_purchase' => false,
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(3),
            'updated_at' => Carbon::now()->subDays(3),
        ];
        
        $reviews[] = [
            'product_id' => 1,
            'user_id' => 5,
            'rating' => 5,
            'review_title' => 'Perfect!',
            'review_text' => 'Everything I expected and more. Fast delivery and great packaging.',
            'is_verified_purchase' => true,
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(1),
            'updated_at' => Carbon::now()->subDays(1),
        ];
        
        // Product 2 (Samsung Galaxy S24) - 4 reviews
        $reviews[] = [
            'product_id' => 2,
            'user_id' => 1,
            'rating' => 5,
            'review_title' => 'Amazing display',
            'review_text' => 'The AMOLED display is stunning. Performance is smooth and battery lasts all day.',
            'is_verified_purchase' => true,
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(9),
            'updated_at' => Carbon::now()->subDays(9),
        ];
        
        $reviews[] = [
            'product_id' => 2,
            'user_id' => 2,
            'rating' => 4,
            'review_title' => 'Good value',
            'review_text' => 'Great phone for the price. Camera could be better but overall satisfied.',
            'is_verified_purchase' => true,
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(7),
            'updated_at' => Carbon::now()->subDays(7),
        ];
        
        $reviews[] = [
            'product_id' => 2,
            'user_id' => 3,
            'rating' => 5,
            'review_title' => 'Love it!',
            'review_text' => 'Switched from iPhone and loving the customization options. One UI is great.',
            'is_verified_purchase' => true,
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(4),
            'updated_at' => Carbon::now()->subDays(4),
        ];
        
        $reviews[] = [
            'product_id' => 2,
            'user_id' => 4,
            'rating' => 3,
            'review_title' => 'Average',
            'review_text' => 'Expected more for this price. It\'s okay but not exceptional.',
            'is_verified_purchase' => false,
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(2),
        ];
        
        // Product 3 (Nike Air Max 90) - 4 reviews
        $reviews[] = [
            'product_id' => 3,
            'user_id' => 1,
            'rating' => 5,
            'review_title' => 'Comfortable and stylish',
            'review_text' => 'Great fit and very comfortable for daily wear. True to size.',
            'is_verified_purchase' => true,
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(11),
            'updated_at' => Carbon::now()->subDays(11),
        ];
        
        $reviews[] = [
            'product_id' => 3,
            'user_id' => 2,
            'rating' => 4,
            'review_title' => 'Good quality',
            'review_text' => 'Well made shoes. Comfortable but a bit pricey. Would recommend.',
            'is_verified_purchase' => true,
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(6),
            'updated_at' => Carbon::now()->subDays(6),
        ];
        
        $reviews[] = [
            'product_id' => 3,
            'user_id' => 3,
            'rating' => 5,
            'review_title' => 'Classic design',
            'review_text' => 'Love the retro look. Quality is excellent and they look great.',
            'is_verified_purchase' => true,
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(3),
            'updated_at' => Carbon::now()->subDays(3),
        ];
        
        $reviews[] = [
            'product_id' => 3,
            'user_id' => 4,
            'rating' => 4,
            'review_title' => 'Nice shoes',
            'review_text' => 'Good quality and comfortable. Sizing is accurate.',
            'is_verified_purchase' => false,
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(1),
            'updated_at' => Carbon::now()->subDays(1),
        ];
        
        // Product 4 (Adidas Ultraboost 22) - 3 reviews
        $reviews[] = [
            'product_id' => 4,
            'user_id' => 1,
            'rating' => 5,
            'review_title' => 'Best running shoes',
            'review_text' => 'Extremely comfortable for running. Great cushioning and support.',
            'is_verified_purchase' => true,
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(8),
            'updated_at' => Carbon::now()->subDays(8),
        ];
        
        $reviews[] = [
            'product_id' => 4,
            'user_id' => 2,
            'rating' => 4,
            'review_title' => 'Great for workouts',
            'review_text' => 'Comfortable and supportive. Good for gym and running.',
            'is_verified_purchase' => true,
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(5),
            'updated_at' => Carbon::now()->subDays(5),
        ];
        
        $reviews[] = [
            'product_id' => 4,
            'user_id' => 3,
            'rating' => 5,
            'review_title' => 'Excellent quality',
            'review_text' => 'Worth every penny. Great build quality and very comfortable.',
            'is_verified_purchase' => true,
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(2),
        ];
        
        // Product 5 (Sony WH-1000XM5) - 4 reviews
        $reviews[] = [
            'product_id' => 5,
            'user_id' => 1,
            'rating' => 5,
            'review_title' => 'Amazing noise cancellation',
            'review_text' => 'Best headphones I\'ve ever owned. Noise cancellation is incredible.',
            'is_verified_purchase' => true,
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(10),
            'updated_at' => Carbon::now()->subDays(10),
        ];
        
        $reviews[] = [
            'product_id' => 5,
            'user_id' => 2,
            'rating' => 5,
            'review_title' => 'Perfect for travel',
            'review_text' => 'Great for flights. Battery lasts long and sound quality is excellent.',
            'is_verified_purchase' => true,
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(7),
            'updated_at' => Carbon::now()->subDays(7),
        ];
        
        $reviews[] = [
            'product_id' => 5,
            'user_id' => 3,
            'rating' => 4,
            'review_title' => 'Great sound',
            'review_text' => 'Excellent sound quality and comfortable to wear for long periods.',
            'is_verified_purchase' => true,
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(4),
            'updated_at' => Carbon::now()->subDays(4),
        ];
        
        $reviews[] = [
            'product_id' => 5,
            'user_id' => 4,
            'rating' => 5,
            'review_title' => 'Worth it!',
            'review_text' => 'Expensive but worth every rupee. Best headphones in the market.',
            'is_verified_purchase' => false,
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(1),
            'updated_at' => Carbon::now()->subDays(1),
        ];

        DB::table('product_reviews')->insert($reviews);
    }
}
