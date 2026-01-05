<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorDocument;
use App\Models\SalesmanProfile;
use App\Models\SalesmanLocation;
use App\Models\State;
use App\Models\City;
use App\Models\Category;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class VendorSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Spatie Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $salesmanRole = Role::firstOrCreate(['name' => 'salesman']);
        $vendorRole = Role::firstOrCreate(['name' => 'vendor']);

        // Get or create a state and city for vendors
        $state = State::firstOrCreate(['name' => 'Maharashtra']);
        $city = City::firstOrCreate([
            'state_id' => $state->id,
            'name' => 'Mumbai'
        ]);

        // Get or create a category
        $category = Category::firstOrCreate([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ], [
            'status' => 'active',
        ]);

        // 1. Create Super Admin
        $superAdmin = User::firstOrCreate(
            ['mobile' => '9999999999'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@taksh.com',
                'password' => Hash::make('password'),
                'user_type' => 'super_admin',
                'is_active' => true,
                'is_verified' => true,
                'status' => 'active',
            ]
        );
        $superAdmin->assignRole($superAdminRole);

        // 2. Create Salesmen
        $salesman1 = User::firstOrCreate(
            ['mobile' => '8888888881'],
            [
                'name' => 'Salesman 1',
                'email' => 'salesman1@taksh.com',
                'user_type' => 'salesman',
                'is_active' => true,
                'is_verified' => true,
                'status' => 'active',
            ]
        );
        $salesman1->assignRole($salesmanRole);

        // Create salesman profile
        SalesmanProfile::firstOrCreate(
            ['user_id' => $salesman1->id],
            [
                'name' => 'Salesman 1',
                'mobile_number' => '8888888881',
                'email' => 'salesman1@taksh.com',
                'state_id' => $state->id,
                'city_id' => $city->id,
                'is_active' => true,
            ]
        );

        // Create salesman location
        SalesmanLocation::firstOrCreate(
            ['salesman_id' => $salesman1->id],
            [
                'latitude' => 19.0760,
                'longitude' => 72.8777,
                'updated_at' => now(),
            ]
        );

        $salesman2 = User::firstOrCreate(
            ['mobile' => '8888888882'],
            [
                'name' => 'Salesman 2',
                'email' => 'salesman2@taksh.com',
                'user_type' => 'salesman',
                'is_active' => true,
                'is_verified' => true,
                'status' => 'active',
            ]
        );
        $salesman2->assignRole($salesmanRole);

        // Create salesman profile
        SalesmanProfile::firstOrCreate(
            ['user_id' => $salesman2->id],
            [
                'name' => 'Salesman 2',
                'mobile_number' => '8888888882',
                'email' => 'salesman2@taksh.com',
                'state_id' => $state->id,
                'city_id' => $city->id,
                'is_active' => true,
            ]
        );

        // Create salesman location
        SalesmanLocation::firstOrCreate(
            ['salesman_id' => $salesman2->id],
            [
                'latitude' => 19.0760,
                'longitude' => 72.8777,
                'updated_at' => now(),
            ]
        );

        // 3. Create Vendors
        // Vendor 1: Pending (not assigned to salesman)
        $vendorUser1 = User::firstOrCreate(
            ['mobile' => '7777777771'],
            [
                'name' => 'Vendor 1',
                'email' => 'vendor1@taksh.com',
                'user_type' => 'vendor',
                'vendor_status' => 'pending',
                'is_active' => false,
                'is_verified' => false,
                'status' => 'active',
            ]
        );
        $vendorUser1->assignRole($vendorRole);

        $vendor1 = Vendor::firstOrCreate(
            ['user_id' => $vendorUser1->id],
            [
                'vendor_name' => 'Vendor 1',
                'owner_name' => 'Owner 1',
                'shop_name' => 'Shop 1',
                'shop_address' => '123 Main Street, Shop Area',
                'shop_pincode' => '400001',
                'shop_latitude' => 19.0760,
                'shop_longitude' => 72.8777,
                'category_id' => $category->id,
                'shop_images' => json_encode(['vendors/shop_images/shop1_1.jpg', 'vendors/shop_images/shop1_2.jpg']),
                'owner_address' => '456 Owner Street',
                'owner_pincode' => '400002',
                'owner_latitude' => 19.0760,
                'owner_longitude' => 72.8777,
                'owner_image' => 'vendors/owner_images/owner1.jpg',
                'email' => 'vendor1@taksh.com',
                'mobile_number' => '7777777771',
                'address' => '123 Main Street',
                'state_id' => $state->id,
                'city_id' => $city->id,
                'pincode' => '400001',
                'status' => 'pending',
                'verification_status' => 'pending',
            ]
        );

        // Create documents for vendor 1
        if ($vendor1->wasRecentlyCreated || $vendor1->documents()->count() === 0) {
            VendorDocument::insert([
                [
                    'vendor_id' => $vendor1->id,
                    'document_type' => 'aadhaar',
                    'document_number' => '123456789012',
                    'document_file' => 'vendors/documents/aadhaar1.pdf',
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'vendor_id' => $vendor1->id,
                    'document_type' => 'pan',
                    'document_number' => 'ABCDE1234F',
                    'document_file' => 'vendors/documents/pan1.pdf',
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'vendor_id' => $vendor1->id,
                    'document_type' => 'bank',
                    'document_number' => '1234567890',
                    'document_file' => 'vendors/documents/bank1.pdf',
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'vendor_id' => $vendor1->id,
                    'document_type' => 'gst',
                    'document_number' => 'GST123456',
                    'document_file' => 'vendors/documents/gst1.pdf',
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'vendor_id' => $vendor1->id,
                    'document_type' => 'fssai',
                    'document_number' => null,
                    'document_file' => 'vendors/documents/fssai1.pdf',
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // Vendor 2: Verified (assigned and verified by salesman, waiting for admin approval)
        $vendorUser2 = User::firstOrCreate(
            ['mobile' => '7777777772'],
            [
                'name' => 'Vendor 2',
                'email' => 'vendor2@taksh.com',
                'user_type' => 'vendor',
                'vendor_status' => 'pending',
                'is_active' => false,
                'is_verified' => false,
                'status' => 'active',
            ]
        );
        $vendorUser2->assignRole($vendorRole);

        $vendor2 = Vendor::firstOrCreate(
            ['user_id' => $vendorUser2->id],
            [
                'vendor_name' => 'Vendor 2',
                'owner_name' => 'Owner 2',
                'shop_name' => 'Shop 2',
                'shop_address' => '456 Second Street, Shop Area',
                'shop_pincode' => '400002',
                'shop_latitude' => 19.0760,
                'shop_longitude' => 72.8777,
                'category_id' => $category->id,
                'shop_images' => json_encode(['vendors/shop_images/shop2_1.jpg']),
                'owner_address' => '789 Owner Street',
                'owner_pincode' => '400003',
                'owner_latitude' => 19.0760,
                'owner_longitude' => 72.8777,
                'owner_image' => 'vendors/owner_images/owner2.jpg',
                'email' => 'vendor2@taksh.com',
                'mobile_number' => '7777777772',
                'address' => '456 Second Street',
                'state_id' => $state->id,
                'city_id' => $city->id,
                'pincode' => '400002',
                'status' => 'pending',
                'verification_status' => 'verified',
                'assigned_salesman_id' => $salesman1->id,
                'verified_by' => $salesman1->id,
                'verified_at' => now(),
            ]
        );

        // Create documents for vendor 2
        if ($vendor2->wasRecentlyCreated || $vendor2->documents()->count() === 0) {
            VendorDocument::insert([
                [
                    'vendor_id' => $vendor2->id,
                    'document_type' => 'aadhaar',
                    'document_number' => '987654321098',
                    'document_file' => 'vendors/documents/aadhaar2.pdf',
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'vendor_id' => $vendor2->id,
                    'document_type' => 'pan',
                    'document_number' => 'FGHIJ5678K',
                    'document_file' => 'vendors/documents/pan2.pdf',
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'vendor_id' => $vendor2->id,
                    'document_type' => 'bank',
                    'document_number' => '9876543210',
                    'document_file' => 'vendors/documents/bank2.pdf',
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'vendor_id' => $vendor2->id,
                    'document_type' => 'non_gst',
                    'document_number' => null,
                    'document_file' => 'vendors/documents/non_gst2.pdf',
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'vendor_id' => $vendor2->id,
                    'document_type' => 'fssai',
                    'document_number' => null,
                    'document_file' => 'vendors/documents/fssai2.pdf',
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        $this->command->info('Vendor System Seeder completed successfully!');
        $this->command->info('Super Admin: 9999999999');
        $this->command->info('Salesman 1: 8888888881');
        $this->command->info('Salesman 2: 8888888882');
        $this->command->info('Vendor 1 (Pending): 7777777771');
        $this->command->info('Vendor 2 (Verified): 7777777772');
        $this->command->info('Default OTP for all: 1234');
    }
}
