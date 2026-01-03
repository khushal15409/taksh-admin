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
            'image_url' => 'https://fastly.picsum.photos/id/26/4209/2769.jpg?hmac=vcInmowFvPCyKGtV7Vfh7zWcA_Z0kStrPDW3ppP0iGI',
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
            'image_url' => 'https://fastly.picsum.photos/id/0/5000/3333.jpg?hmac=_j6ghY5fCfSD6tvtcV74zXivkJSPIfR9B8w34XeQmvU',
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
            'image_url' => 'https://fastly.picsum.photos/id/1/5000/3333.jpg?hmac=Asv2DU3rA_5D1xSe22xZK47WEYN0dujQfrsH6a7koOE',
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
            'image_url' => 'https://fastly.picsum.photos/id/2/5000/3333.jpg?hmac=wfahntYS3o_ctsVt8tl5EJaircW8ZJ0x0T5k8x3VF6M',
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
            'image_url' => 'https://fastly.picsum.photos/id/3/5000/3333.jpg?hmac=GDjZ2uNWE3V59Y0x7m9b4x9J9Z5J5J5J5J5J5J5J5J5',
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
            'image_url' => 'https://fastly.picsum.photos/id/4/5000/3333.jpg?hmac=Hv0t6qZ8qZ8qZ8qZ8qZ8qZ8qZ8qZ8qZ8qZ8qZ8qZ8',
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
            'image_url' => 'https://fastly.picsum.photos/id/5/5000/3333.jpg?hmac=5v5v5v5v5v5v5v5v5v5v5v5v5v5v5v5v5v5v5v5v',
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
            'image_url' => 'https://fastly.picsum.photos/id/6/5000/3333.jpg?hmac=6v6v6v6v6v6v6v6v6v6v6v6v6v6v6v6v6v6v6v6v',
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
            'image_url' => 'https://fastly.picsum.photos/id/7/5000/3333.jpg?hmac=7v7v7v7v7v7v7v7v7v7v7v7v7v7v7v7v7v7v7v7v',
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
            'image_url' => 'https://fastly.picsum.photos/id/8/5000/3333.jpg?hmac=8v8v8v8v8v8v8v8v8v8v8v8v8v8v8v8v8v8v8v8v',
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
            'image_url' => 'https://fastly.picsum.photos/id/9/5000/3333.jpg?hmac=9v9v9v9v9v9v9v9v9v9v9v9v9v9v9v9v9v9v9v9v',
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
            'image_url' => 'https://fastly.picsum.photos/id/10/5000/3333.jpg?hmac=10v10v10v10v10v10v10v10v10v10v10v10v10v10v10v',
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
            'image_url' => 'https://fastly.picsum.photos/id/11/5000/3333.jpg?hmac=11v11v11v11v11v11v11v11v11v11v11v11v11v11v11v',
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
            'image_url' => 'https://fastly.picsum.photos/id/12/5000/3333.jpg?hmac=12v12v12v12v12v12v12v12v12v12v12v12v12v12v12v',
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
            'image_url' => 'https://fastly.picsum.photos/id/13/5000/3333.jpg?hmac=13v13v13v13v13v13v13v13v13v13v13v13v13v13v13v',
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
            'image_url' => 'https://fastly.picsum.photos/id/14/5000/3333.jpg?hmac=14v14v14v14v14v14v14v14v14v14v14v14v14v14v14v',
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
