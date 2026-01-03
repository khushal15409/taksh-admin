<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // Verified users
            ['id' => 1, 'mobile' => '9000000001', 'name' => 'John Doe', 'is_verified' => true, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'mobile' => '9000000002', 'name' => 'Jane Smith', 'is_verified' => true, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'mobile' => '9000000003', 'name' => 'Raj Patel', 'is_verified' => true, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'mobile' => '9000000004', 'name' => 'Priya Sharma', 'is_verified' => true, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'mobile' => '9000000005', 'name' => 'Amit Kumar', 'is_verified' => true, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            
            // Unverified users (for testing OTP flow)
            ['id' => 6, 'mobile' => '9000000006', 'name' => null, 'is_verified' => false, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'mobile' => '9000000007', 'name' => null, 'is_verified' => false, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('users')->insert($users);
    }
}
