<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $addresses = [
            [
                'id' => 1,
                'user_id' => 1, // John Doe
                'state_id' => 1, // Gujarat
                'city_id' => 1, // Ahmedabad
                'area_id' => 1, // Navrangpura
                'name' => 'John Doe',
                'mobile' => '9000000001',
                'address_line_1' => '123, ABC Street',
                'address_line_2' => 'Near XYZ Mall',
                'pincode' => '380009',
                'landmark' => 'Opposite Bank',
                'type' => 'home',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'user_id' => 2, // Jane Smith
                'state_id' => 2, // Maharashtra
                'city_id' => 3, // Mumbai
                'area_id' => 4, // Andheri
                'name' => 'Jane Smith',
                'mobile' => '9000000002',
                'address_line_1' => '456, DEF Road',
                'address_line_2' => 'Building 2, Flat 301',
                'pincode' => '400053',
                'landmark' => 'Near Metro Station',
                'type' => 'home',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'user_id' => 3, // Raj Patel
                'state_id' => 1, // Gujarat
                'city_id' => 1, // Ahmedabad
                'area_id' => 2, // Vastrapur
                'name' => 'Raj Patel',
                'mobile' => '9000000003',
                'address_line_1' => '789, GHI Avenue',
                'address_line_2' => null,
                'pincode' => '380015',
                'landmark' => 'Near Park',
                'type' => 'home',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'user_id' => 4, // Priya Sharma
                'state_id' => 3, // Delhi
                'city_id' => 5, // New Delhi
                'area_id' => 7, // Connaught Place
                'name' => 'Priya Sharma',
                'mobile' => '9000000004',
                'address_line_1' => '321, JKL Street',
                'address_line_2' => 'Office Building, Floor 5',
                'pincode' => '110001',
                'landmark' => 'Near CP Metro',
                'type' => 'work',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'user_id' => 5, // Amit Kumar
                'state_id' => 2, // Maharashtra
                'city_id' => 4, // Pune
                'area_id' => 6, // Hinjewadi
                'name' => 'Amit Kumar',
                'mobile' => '9000000005',
                'address_line_1' => '654, MNO Road',
                'address_line_2' => 'Tech Park, Block A',
                'pincode' => '411057',
                'landmark' => 'IT Park',
                'type' => 'work',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('addresses')->insert($addresses);
    }
}
