<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Product Attributes
        $attributes = [
            ['id' => 1, 'name' => 'Color', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Storage', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'RAM', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Size', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('product_attributes')->insert($attributes);

        // Attribute Values
        $attributeValues = [
            ['id' => 1, 'product_attribute_id' => 1, 'value' => 'Black', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'product_attribute_id' => 1, 'value' => 'White', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'product_attribute_id' => 1, 'value' => 'Blue', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'product_attribute_id' => 1, 'value' => 'Red', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'product_attribute_id' => 2, 'value' => '128GB', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'product_attribute_id' => 2, 'value' => '256GB', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'product_attribute_id' => 2, 'value' => '512GB', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'product_attribute_id' => 3, 'value' => '8GB', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'product_attribute_id' => 3, 'value' => '16GB', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'product_attribute_id' => 4, 'value' => 'S', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'product_attribute_id' => 4, 'value' => 'M', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'product_attribute_id' => 4, 'value' => 'L', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'product_attribute_id' => 4, 'value' => 'XL', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('product_attribute_values')->insert($attributeValues);

        // Products
        $products = [
            [
                'id' => 1,
                'category_id' => 4, // Mobile Phones
                'brand_id' => 2, // Apple
                'name' => 'iPhone 15 Pro',
                'slug' => 'iphone-15-pro',
                'description' => 'Latest iPhone with A17 Pro chip, titanium design, and advanced camera system.',
                'short_description' => 'Premium smartphone with cutting-edge technology',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'category_id' => 4, // Mobile Phones
                'brand_id' => 1, // Samsung
                'name' => 'Samsung Galaxy S24',
                'slug' => 'samsung-galaxy-s24',
                'description' => 'Flagship Android smartphone with AI features and stunning display.',
                'short_description' => 'Powerful Android device with AI capabilities',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'category_id' => 9, // Shoes
                'brand_id' => 3, // Nike
                'name' => 'Nike Air Max 90',
                'slug' => 'nike-air-max-90',
                'description' => 'Classic running shoes with air cushioning technology.',
                'short_description' => 'Comfortable running shoes',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'category_id' => 9, // Shoes
                'brand_id' => 4, // Adidas
                'name' => 'Adidas Ultraboost 22',
                'slug' => 'adidas-ultraboost-22',
                'description' => 'Premium running shoes with boost technology for maximum comfort.',
                'short_description' => 'High-performance running shoes',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'category_id' => 6, // Headphones
                'brand_id' => 5, // Sony
                'name' => 'Sony WH-1000XM5',
                'slug' => 'sony-wh-1000xm5',
                'description' => 'Premium noise-cancelling wireless headphones with exceptional sound quality.',
                'short_description' => 'Top-tier noise-cancelling headphones',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('products')->insert($products);

        // Product Variants
        $variants = [
            // iPhone 15 Pro variants
            ['id' => 1, 'product_id' => 1, 'sku' => 'IPH15P-128-BLK', 'price' => 99900.00, 'sale_price' => 94900.00, 'weight' => 0.187, 'length' => 15.9, 'width' => 7.6, 'height' => 0.83, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'product_id' => 1, 'sku' => 'IPH15P-256-BLK', 'price' => 114900.00, 'sale_price' => 109900.00, 'weight' => 0.187, 'length' => 15.9, 'width' => 7.6, 'height' => 0.83, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'product_id' => 1, 'sku' => 'IPH15P-512-BLK', 'price' => 134900.00, 'sale_price' => null, 'weight' => 0.187, 'length' => 15.9, 'width' => 7.6, 'height' => 0.83, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            
            // Samsung Galaxy S24 variants
            ['id' => 4, 'product_id' => 2, 'sku' => 'SGS24-128-BLK', 'price' => 79900.00, 'sale_price' => 74900.00, 'weight' => 0.168, 'length' => 14.7, 'width' => 7.0, 'height' => 0.79, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'product_id' => 2, 'sku' => 'SGS24-256-BLK', 'price' => 89900.00, 'sale_price' => 84900.00, 'weight' => 0.168, 'length' => 14.7, 'width' => 7.0, 'height' => 0.79, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            
            // Nike Air Max 90 variants
            ['id' => 6, 'product_id' => 3, 'sku' => 'NIKE-AM90-S', 'price' => 8999.00, 'sale_price' => 7999.00, 'weight' => 0.35, 'length' => 30, 'width' => 12, 'height' => 10, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'product_id' => 3, 'sku' => 'NIKE-AM90-M', 'price' => 8999.00, 'sale_price' => 7999.00, 'weight' => 0.35, 'length' => 30, 'width' => 12, 'height' => 10, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'product_id' => 3, 'sku' => 'NIKE-AM90-L', 'price' => 8999.00, 'sale_price' => 7999.00, 'weight' => 0.35, 'length' => 30, 'width' => 12, 'height' => 10, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            
            // Adidas Ultraboost variants
            ['id' => 9, 'product_id' => 4, 'sku' => 'ADID-UB22-BLK', 'price' => 12999.00, 'sale_price' => 11999.00, 'weight' => 0.32, 'length' => 30, 'width' => 12, 'height' => 10, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'product_id' => 4, 'sku' => 'ADID-UB22-WHT', 'price' => 12999.00, 'sale_price' => 11999.00, 'weight' => 0.32, 'length' => 30, 'width' => 12, 'height' => 10, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            
            // Sony Headphones (no variants, single product)
            ['id' => 11, 'product_id' => 5, 'sku' => 'SONY-WH1000XM5', 'price' => 29990.00, 'sale_price' => 27990.00, 'weight' => 0.25, 'length' => 20, 'width' => 18, 'height' => 8, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('product_variants')->insert($variants);

        // Product Variant Attributes (linking variants to attributes)
        $variantAttributes = [
            // iPhone 15 Pro 128GB Black
            ['id' => 1, 'product_variant_id' => 1, 'product_attribute_id' => 1, 'product_attribute_value_id' => 1, 'created_at' => now(), 'updated_at' => now()], // Color: Black
            ['id' => 2, 'product_variant_id' => 1, 'product_attribute_id' => 2, 'product_attribute_value_id' => 5, 'created_at' => now(), 'updated_at' => now()], // Storage: 128GB
            
            // iPhone 15 Pro 256GB Black
            ['id' => 3, 'product_variant_id' => 2, 'product_attribute_id' => 1, 'product_attribute_value_id' => 1, 'created_at' => now(), 'updated_at' => now()], // Color: Black
            ['id' => 4, 'product_variant_id' => 2, 'product_attribute_id' => 2, 'product_attribute_value_id' => 6, 'created_at' => now(), 'updated_at' => now()], // Storage: 256GB
            
            // iPhone 15 Pro 512GB Black
            ['id' => 5, 'product_variant_id' => 3, 'product_attribute_id' => 1, 'product_attribute_value_id' => 1, 'created_at' => now(), 'updated_at' => now()], // Color: Black
            ['id' => 6, 'product_variant_id' => 3, 'product_attribute_id' => 2, 'product_attribute_value_id' => 7, 'created_at' => now(), 'updated_at' => now()], // Storage: 512GB
            
            // Samsung Galaxy S24 128GB Black
            ['id' => 7, 'product_variant_id' => 4, 'product_attribute_id' => 1, 'product_attribute_value_id' => 1, 'created_at' => now(), 'updated_at' => now()], // Color: Black
            ['id' => 8, 'product_variant_id' => 4, 'product_attribute_id' => 2, 'product_attribute_value_id' => 5, 'created_at' => now(), 'updated_at' => now()], // Storage: 128GB
            
            // Samsung Galaxy S24 256GB Black
            ['id' => 9, 'product_variant_id' => 5, 'product_attribute_id' => 1, 'product_attribute_value_id' => 1, 'created_at' => now(), 'updated_at' => now()], // Color: Black
            ['id' => 10, 'product_variant_id' => 5, 'product_attribute_id' => 2, 'product_attribute_value_id' => 6, 'created_at' => now(), 'updated_at' => now()], // Storage: 256GB
            
            // Nike Air Max 90 - Size S
            ['id' => 11, 'product_variant_id' => 6, 'product_attribute_id' => 4, 'product_attribute_value_id' => 10, 'created_at' => now(), 'updated_at' => now()], // Size: S
            
            // Nike Air Max 90 - Size M
            ['id' => 12, 'product_variant_id' => 7, 'product_attribute_id' => 4, 'product_attribute_value_id' => 11, 'created_at' => now(), 'updated_at' => now()], // Size: M
            
            // Nike Air Max 90 - Size L
            ['id' => 13, 'product_variant_id' => 8, 'product_attribute_id' => 4, 'product_attribute_value_id' => 12, 'created_at' => now(), 'updated_at' => now()], // Size: L
            
            // Adidas Ultraboost Black
            ['id' => 14, 'product_variant_id' => 9, 'product_attribute_id' => 1, 'product_attribute_value_id' => 1, 'created_at' => now(), 'updated_at' => now()], // Color: Black
            
            // Adidas Ultraboost White
            ['id' => 15, 'product_variant_id' => 10, 'product_attribute_id' => 1, 'product_attribute_value_id' => 2, 'created_at' => now(), 'updated_at' => now()], // Color: White
        ];

        DB::table('product_variant_attributes')->insert($variantAttributes);

        // Product Images
        $images = [
            // iPhone 15 Pro images
            ['id' => 1, 'product_id' => 1, 'product_variant_id' => null, 'image_url' => 'https://via.placeholder.com/500x500?text=iPhone+15+Pro', 'is_primary' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'product_id' => 1, 'product_variant_id' => 1, 'image_url' => 'https://via.placeholder.com/500x500?text=iPhone+15+Pro+128GB', 'is_primary' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Samsung Galaxy S24 images
            ['id' => 3, 'product_id' => 2, 'product_variant_id' => null, 'image_url' => 'https://via.placeholder.com/500x500?text=Samsung+Galaxy+S24', 'is_primary' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Nike Air Max 90 images
            ['id' => 4, 'product_id' => 3, 'product_variant_id' => null, 'image_url' => 'https://via.placeholder.com/500x500?text=Nike+Air+Max+90', 'is_primary' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Adidas Ultraboost images
            ['id' => 5, 'product_id' => 4, 'product_variant_id' => null, 'image_url' => 'https://via.placeholder.com/500x500?text=Adidas+Ultraboost', 'is_primary' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Sony Headphones images
            ['id' => 6, 'product_id' => 5, 'product_variant_id' => null, 'image_url' => 'https://via.placeholder.com/500x500?text=Sony+WH-1000XM5', 'is_primary' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('product_images')->insert($images);
    }
}
