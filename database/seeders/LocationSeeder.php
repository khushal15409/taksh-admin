<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        // States
        $states = [
            ['id' => 1, 'name' => 'Gujarat', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Maharashtra', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Delhi', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('states')->insert($states);

        // Cities
        $cities = [
            ['id' => 1, 'state_id' => 1, 'name' => 'Ahmedabad', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'state_id' => 1, 'name' => 'Surat', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'state_id' => 2, 'name' => 'Mumbai', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'state_id' => 2, 'name' => 'Pune', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'state_id' => 3, 'name' => 'New Delhi', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('cities')->insert($cities);

        // Areas
        $areas = [
            ['id' => 1, 'city_id' => 1, 'name' => 'Navrangpura', 'pincode' => '380009', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'city_id' => 1, 'name' => 'Vastrapur', 'pincode' => '380015', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'city_id' => 2, 'name' => 'Adajan', 'pincode' => '395009', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'city_id' => 3, 'name' => 'Andheri', 'pincode' => '400053', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'city_id' => 3, 'name' => 'Bandra', 'pincode' => '400050', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'city_id' => 4, 'name' => 'Hinjewadi', 'pincode' => '411057', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'city_id' => 5, 'name' => 'Connaught Place', 'pincode' => '110001', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('areas')->insert($areas);
    }
}
