<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use App\Models\SalesmanProfile;
use App\Models\State;
use App\Models\City;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class PincodeAssignmentTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates test data for pincode-based vendor assignment:
     * - 3 Salesmen with same pincode but different pending verification counts
     * - 2 Vendors with same pincode (pending verification)
     * 
     * Expected: Vendors should be assigned to salesman with least pending load
     */
    public function run(): void
    {
        DB::beginTransaction();
        try {
            // Get or create salesman role
            $salesmanRole = Role::firstOrCreate(['name' => 'salesman']);
            $vendorRole = Role::firstOrCreate(['name' => 'vendor']);

            // Get first state and city (or create defaults)
            $state = State::first();
            $city = City::first();

            if (!$state || !$city) {
                $this->command->warn('No state/city found. Please run location seeder first.');
                return;
            }

            $testPincode = '380001'; // Test pincode

            // Create 3 Salesmen with same pincode
            $salesman1 = User::create([
                'mobile' => '9876543210',
                'name' => 'Salesman One (0 pending)',
                'email' => 'salesman1@test.com',
                'user_type' => 'salesman',
                'address' => 'Test Address 1',
                'pincode' => $testPincode,
                'is_active' => true,
                'is_verified' => true,
                'status' => 'active',
            ]);
            $salesman1->assignRole($salesmanRole);
            SalesmanProfile::create([
                'user_id' => $salesman1->id,
                'name' => $salesman1->name,
                'mobile_number' => $salesman1->mobile,
                'email' => $salesman1->email,
                'state_id' => $state->id,
                'city_id' => $city->id,
                'is_active' => true,
            ]);

            $salesman2 = User::create([
                'mobile' => '9876543211',
                'name' => 'Salesman Two (1 pending)',
                'email' => 'salesman2@test.com',
                'user_type' => 'salesman',
                'address' => 'Test Address 2',
                'pincode' => $testPincode,
                'is_active' => true,
                'is_verified' => true,
                'status' => 'active',
            ]);
            $salesman2->assignRole($salesmanRole);
            SalesmanProfile::create([
                'user_id' => $salesman2->id,
                'name' => $salesman2->name,
                'mobile_number' => $salesman2->mobile,
                'email' => $salesman2->email,
                'state_id' => $state->id,
                'city_id' => $city->id,
                'is_active' => true,
            ]);

            $salesman3 = User::create([
                'mobile' => '9876543212',
                'name' => 'Salesman Three (2 pending)',
                'email' => 'salesman3@test.com',
                'user_type' => 'salesman',
                'address' => 'Test Address 3',
                'pincode' => $testPincode,
                'is_active' => true,
                'is_verified' => true,
                'status' => 'active',
            ]);
            $salesman3->assignRole($salesmanRole);
            SalesmanProfile::create([
                'user_id' => $salesman3->id,
                'name' => $salesman3->name,
                'mobile_number' => $salesman3->mobile,
                'email' => $salesman3->email,
                'state_id' => $state->id,
                'city_id' => $city->id,
                'is_active' => true,
            ]);

            // Create 1 vendor assigned to salesman2 (to give him 1 pending)
            $vendorUser1 = User::create([
                'mobile' => '9876543220',
                'name' => 'Test Vendor User 1',
                'email' => 'vendor1@test.com',
                'user_type' => 'vendor',
                'vendor_status' => 'pending',
                'is_active' => false,
                'is_verified' => false,
                'status' => 'active',
            ]);
            $vendorUser1->assignRole($vendorRole);

            $existingVendor1 = Vendor::create([
                'user_id' => $vendorUser1->id,
                'vendor_name' => 'Existing Test Vendor 1',
                'shop_name' => 'Test Shop 1',
                'email' => 'vendor1@test.com',
                'mobile_number' => '9876543220',
                'address' => 'Test Shop Address 1',
                'shop_address' => 'Test Shop Address 1',
                'pincode' => $testPincode,
                'shop_pincode' => $testPincode,
                'state_id' => $state->id,
                'city_id' => $city->id,
                'bank_name' => 'Test Bank',
                'account_number' => '1234567890',
                'ifsc_code' => 'TEST0001234',
                'status' => 'pending',
                'assigned_salesman_id' => $salesman2->id,
                'verification_status' => 'assigned',
            ]);

            // Create 2 vendors assigned to salesman3 (to give him 2 pending)
            $vendorUser2 = User::create([
                'mobile' => '9876543221',
                'name' => 'Test Vendor User 2',
                'email' => 'vendor2@test.com',
                'user_type' => 'vendor',
                'vendor_status' => 'pending',
                'is_active' => false,
                'is_verified' => false,
                'status' => 'active',
            ]);
            $vendorUser2->assignRole($vendorRole);

            $existingVendor2 = Vendor::create([
                'user_id' => $vendorUser2->id,
                'vendor_name' => 'Existing Test Vendor 2',
                'shop_name' => 'Test Shop 2',
                'email' => 'vendor2@test.com',
                'mobile_number' => '9876543221',
                'address' => 'Test Shop Address 2',
                'shop_address' => 'Test Shop Address 2',
                'pincode' => $testPincode,
                'shop_pincode' => $testPincode,
                'state_id' => $state->id,
                'city_id' => $city->id,
                'bank_name' => 'Test Bank',
                'account_number' => '1234567891',
                'ifsc_code' => 'TEST0001235',
                'status' => 'pending',
                'assigned_salesman_id' => $salesman3->id,
                'verification_status' => 'assigned',
            ]);

            $vendorUser3 = User::create([
                'mobile' => '9876543222',
                'name' => 'Test Vendor User 3',
                'email' => 'vendor3@test.com',
                'user_type' => 'vendor',
                'vendor_status' => 'pending',
                'is_active' => false,
                'is_verified' => false,
                'status' => 'active',
            ]);
            $vendorUser3->assignRole($vendorRole);

            $existingVendor3 = Vendor::create([
                'user_id' => $vendorUser3->id,
                'vendor_name' => 'Existing Test Vendor 3',
                'shop_name' => 'Test Shop 3',
                'email' => 'vendor3@test.com',
                'mobile_number' => '9876543222',
                'address' => 'Test Shop Address 3',
                'shop_address' => 'Test Shop Address 3',
                'pincode' => $testPincode,
                'shop_pincode' => $testPincode,
                'state_id' => $state->id,
                'city_id' => $city->id,
                'bank_name' => 'Test Bank',
                'account_number' => '1234567892',
                'ifsc_code' => 'TEST0001236',
                'status' => 'pending',
                'assigned_salesman_id' => $salesman3->id,
                'verification_status' => 'assigned',
            ]);

            // Create 2 NEW vendors (pending, not assigned) - these should auto-assign to salesman1
            $vendorUser4 = User::create([
                'mobile' => '9876543223',
                'name' => 'New Test Vendor User 4',
                'email' => 'vendor4@test.com',
                'user_type' => 'vendor',
                'vendor_status' => 'pending',
                'is_active' => false,
                'is_verified' => false,
                'status' => 'active',
            ]);
            $vendorUser4->assignRole($vendorRole);

            $newVendor1 = Vendor::create([
                'user_id' => $vendorUser4->id,
                'vendor_name' => 'New Test Vendor 1',
                'shop_name' => 'New Test Shop 1',
                'email' => 'vendor4@test.com',
                'mobile_number' => '9876543223',
                'address' => 'New Test Shop Address 1',
                'shop_address' => 'New Test Shop Address 1',
                'pincode' => $testPincode,
                'shop_pincode' => $testPincode,
                'state_id' => $state->id,
                'city_id' => $city->id,
                'bank_name' => 'Test Bank',
                'account_number' => '1234567893',
                'ifsc_code' => 'TEST0001237',
                'status' => 'pending',
                'verification_status' => 'pending',
            ]);

            $vendorUser5 = User::create([
                'mobile' => '9876543224',
                'name' => 'New Test Vendor User 5',
                'email' => 'vendor5@test.com',
                'user_type' => 'vendor',
                'vendor_status' => 'pending',
                'is_active' => false,
                'is_verified' => false,
                'status' => 'active',
            ]);
            $vendorUser5->assignRole($vendorRole);

            $newVendor2 = Vendor::create([
                'user_id' => $vendorUser5->id,
                'vendor_name' => 'New Test Vendor 2',
                'shop_name' => 'New Test Shop 2',
                'email' => 'vendor5@test.com',
                'mobile_number' => '9876543224',
                'address' => 'New Test Shop Address 2',
                'shop_address' => 'New Test Shop Address 2',
                'pincode' => $testPincode,
                'shop_pincode' => $testPincode,
                'state_id' => $state->id,
                'city_id' => $city->id,
                'bank_name' => 'Test Bank',
                'account_number' => '1234567894',
                'ifsc_code' => 'TEST0001238',
                'status' => 'pending',
                'verification_status' => 'pending',
            ]);

            // Auto-assign new vendors using the service
            $assignmentService = new \App\Services\VendorAssignmentService();
            $assignmentService->autoAssignByPincode($newVendor1);
            $assignmentService->autoAssignByPincode($newVendor2);

            DB::commit();

            $this->command->info('✅ Pincode Assignment Test Data Created Successfully!');
            $this->command->info('');
            $this->command->info('Test Pincode: ' . $testPincode);
            $this->command->info('');
            $this->command->info('Salesmen Created:');
            $this->command->info('  - Salesman 1: 0 pending (should get new vendors)');
            $this->command->info('  - Salesman 2: 1 pending');
            $this->command->info('  - Salesman 3: 2 pending');
            $this->command->info('');
            $this->command->info('Vendors Created:');
            $this->command->info('  - 3 Existing vendors (pre-assigned)');
            $this->command->info('  - 2 New vendors (auto-assigned to Salesman 1)');
            $this->command->info('');
            $this->command->info('✅ Verify: New vendors should be assigned to Salesman 1 (least pending)');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error creating test data: ' . $e->getMessage());
            throw $e;
        }
    }
}
