<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryTypeSeeder extends Seeder
{
    public function run(): void
    {
        $deliveryTypes = [
            ['id' => 1, 'code' => '30_min', 'estimated_minutes' => 30, 'price' => 50.00, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'code' => '1_day', 'estimated_minutes' => 1440, 'price' => 30.00, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'code' => 'normal', 'estimated_minutes' => 2880, 'price' => 20.00, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('delivery_types')->insert($deliveryTypes);
    }
}
