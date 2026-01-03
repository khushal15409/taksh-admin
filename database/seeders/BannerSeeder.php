<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $positions = ['home_top', 'home_middle', 'home_bottom', 'dashboard'];
        $redirectTypes = ['product', 'category', 'external', 'none'];
        
        $banners = [];
        
        // Home Top Banners
        $banners[] = [
            'title' => 'Big Sale - Up to 70% Off',
            'description' => 'Shop now and save big on all products',
            'image_url' => 'https://cdn.example.com/banners/big-sale-70-off.jpg',
            'redirect_type' => 'category',
            'redirect_id' => 1,
            'redirect_url' => null,
            'position' => 'home_top',
            'start_date' => Carbon::now()->subDays(2),
            'end_date' => Carbon::now()->addDays(15),
            'is_active' => true,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        $banners[] = [
            'title' => 'New Arrivals',
            'description' => 'Check out our latest products',
            'image_url' => 'https://cdn.example.com/banners/new-arrivals.jpg',
            'redirect_type' => 'category',
            'redirect_id' => 4,
            'redirect_url' => null,
            'position' => 'home_top',
            'start_date' => Carbon::now()->subDays(1),
            'end_date' => Carbon::now()->addDays(30),
            'is_active' => true,
            'sort_order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        $banners[] = [
            'title' => 'Flash Sale - Limited Time',
            'description' => 'Hurry up! Limited time offer',
            'image_url' => 'https://cdn.example.com/banners/flash-sale.jpg',
            'redirect_type' => 'product',
            'redirect_id' => 1,
            'redirect_url' => null,
            'position' => 'home_top',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays(3),
            'is_active' => true,
            'sort_order' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        // Home Middle Banners
        $banners[] = [
            'title' => 'Electronics Sale',
            'description' => 'Best deals on electronics',
            'image_url' => 'https://cdn.example.com/banners/electronics-sale.jpg',
            'redirect_type' => 'category',
            'redirect_id' => 1,
            'redirect_url' => null,
            'position' => 'home_middle',
            'start_date' => Carbon::now()->subDays(5),
            'end_date' => Carbon::now()->addDays(20),
            'is_active' => true,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        $banners[] = [
            'title' => 'Fashion Week',
            'description' => 'Latest fashion trends',
            'image_url' => 'https://cdn.example.com/banners/fashion-week.jpg',
            'redirect_type' => 'category',
            'redirect_id' => 2,
            'redirect_url' => null,
            'position' => 'home_middle',
            'start_date' => Carbon::now()->subDays(3),
            'end_date' => Carbon::now()->addDays(25),
            'is_active' => true,
            'sort_order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        $banners[] = [
            'title' => 'Free Shipping',
            'description' => 'Free shipping on orders above ₹999',
            'image_url' => 'https://cdn.example.com/banners/free-shipping.jpg',
            'redirect_type' => 'none',
            'redirect_id' => null,
            'redirect_url' => null,
            'position' => 'home_middle',
            'start_date' => Carbon::now()->subDays(10),
            'end_date' => Carbon::now()->addDays(60),
            'is_active' => true,
            'sort_order' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        // Home Bottom Banners
        $banners[] = [
            'title' => 'Weekend Special',
            'description' => 'Special weekend offers',
            'image_url' => 'https://cdn.example.com/banners/weekend-special.jpg',
            'redirect_type' => 'external',
            'redirect_id' => null,
            'redirect_url' => 'https://example.com/weekend-sale',
            'position' => 'home_bottom',
            'start_date' => Carbon::now()->subDays(1),
            'end_date' => Carbon::now()->addDays(2),
            'is_active' => true,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        $banners[] = [
            'title' => 'Brand Spotlight',
            'description' => 'Featured brands',
            'image_url' => 'https://cdn.example.com/banners/brand-spotlight.jpg',
            'redirect_type' => 'category',
            'redirect_id' => 3,
            'redirect_url' => null,
            'position' => 'home_bottom',
            'start_date' => Carbon::now()->subDays(7),
            'end_date' => Carbon::now()->addDays(40),
            'is_active' => true,
            'sort_order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        // Dashboard Banners
        $banners[] = [
            'title' => 'Welcome Offer',
            'description' => 'Get 20% off on your first order',
            'image_url' => 'https://cdn.example.com/banners/welcome-offer.jpg',
            'redirect_type' => 'product',
            'redirect_id' => 2,
            'redirect_url' => null,
            'position' => 'dashboard',
            'start_date' => Carbon::now()->subDays(15),
            'end_date' => Carbon::now()->addDays(45),
            'is_active' => true,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        $banners[] = [
            'title' => 'Refer & Earn',
            'description' => 'Refer friends and earn rewards',
            'image_url' => 'https://cdn.example.com/banners/refer-earn.jpg',
            'redirect_type' => 'external',
            'redirect_id' => null,
            'redirect_url' => 'https://example.com/referral',
            'position' => 'dashboard',
            'start_date' => Carbon::now()->subDays(20),
            'end_date' => Carbon::now()->addDays(100),
            'is_active' => true,
            'sort_order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        $banners[] = [
            'title' => 'Premium Membership',
            'description' => 'Join premium and get exclusive benefits',
            'image_url' => 'https://cdn.example.com/banners/premium-membership.jpg',
            'redirect_type' => 'external',
            'redirect_id' => null,
            'redirect_url' => 'https://example.com/premium',
            'position' => 'dashboard',
            'start_date' => Carbon::now()->subDays(5),
            'end_date' => Carbon::now()->addDays(90),
            'is_active' => true,
            'sort_order' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        // Some inactive banners for testing
        $banners[] = [
            'title' => 'Expired Banner',
            'description' => 'This banner is inactive',
            'image_url' => 'https://cdn.example.com/banners/expired.jpg',
            'redirect_type' => 'none',
            'redirect_id' => null,
            'redirect_url' => null,
            'position' => 'home_top',
            'start_date' => Carbon::now()->subDays(30),
            'end_date' => Carbon::now()->subDays(1),
            'is_active' => false,
            'sort_order' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        $banners[] = [
            'title' => 'Future Banner',
            'description' => 'This banner starts in future',
            'image_url' => 'https://cdn.example.com/banners/future.jpg',
            'redirect_type' => 'category',
            'redirect_id' => 5,
            'redirect_url' => null,
            'position' => 'home_middle',
            'start_date' => Carbon::now()->addDays(5),
            'end_date' => Carbon::now()->addDays(30),
            'is_active' => true,
            'sort_order' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        $banners[] = [
            'title' => 'Inactive Banner',
            'description' => 'This banner is disabled',
            'image_url' => 'https://cdn.example.com/banners/inactive.jpg',
            'redirect_type' => 'product',
            'redirect_id' => 3,
            'redirect_url' => null,
            'position' => 'dashboard',
            'start_date' => Carbon::now()->subDays(10),
            'end_date' => Carbon::now()->addDays(20),
            'is_active' => false,
            'sort_order' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        $banners[] = [
            'title' => 'Special Promotion',
            'description' => 'Limited time special offer',
            'image_url' => 'https://cdn.example.com/banners/special-promotion.jpg',
            'redirect_type' => 'product',
            'redirect_id' => 5,
            'redirect_url' => null,
            'position' => 'home_top',
            'start_date' => Carbon::now()->subDays(2),
            'end_date' => Carbon::now()->addDays(10),
            'is_active' => true,
            'sort_order' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        $banners[] = [
            'title' => 'Customer Appreciation',
            'description' => 'Thank you for being with us',
            'image_url' => 'https://cdn.example.com/banners/customer-appreciation.jpg',
            'redirect_type' => 'none',
            'redirect_id' => null,
            'redirect_url' => null,
            'position' => 'home_bottom',
            'start_date' => Carbon::now()->subDays(1),
            'end_date' => Carbon::now()->addDays(50),
            'is_active' => true,
            'sort_order' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('banners')->insert($banners);
    }
}
