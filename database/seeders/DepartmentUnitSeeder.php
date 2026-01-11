<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\DepartmentUnit;
use Illuminate\Database\Seeder;

class DepartmentUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $logisticsDept = Department::where('code', 'LOG')->first();
        $ecommerceDept = Department::where('code', 'ECOM')->first();

        $units = [
            // Logistics Units
            [
                'department_id' => $logisticsDept->id,
                'name' => 'Mumbai Warehouse',
                'code' => 'warehouse-mumbai',
                'description' => 'Main warehouse in Mumbai',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'department_id' => $logisticsDept->id,
                'name' => 'Delhi Warehouse',
                'code' => 'warehouse-delhi',
                'description' => 'Main warehouse in Delhi',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'department_id' => $logisticsDept->id,
                'name' => 'Mumbai LM Center',
                'code' => 'lm-center-mumbai',
                'description' => 'Last Mile center in Mumbai',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'department_id' => $logisticsDept->id,
                'name' => 'Delhi LM Center',
                'code' => 'lm-center-delhi',
                'description' => 'Last Mile center in Delhi',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'department_id' => $logisticsDept->id,
                'name' => 'Ahmedabad Franchisee',
                'code' => 'franchisee-ahmedabad',
                'description' => 'Franchisee partner in Ahmedabad',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'department_id' => $logisticsDept->id,
                'name' => 'Pune Office',
                'code' => 'office-pune',
                'description' => 'Regional office in Pune',
                'is_active' => true,
                'sort_order' => 6,
            ],
            // Ecommerce Units
            [
                'department_id' => $ecommerceDept->id,
                'name' => 'Mumbai Office',
                'code' => 'office-mumbai',
                'description' => 'Ecommerce office in Mumbai',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'department_id' => $ecommerceDept->id,
                'name' => 'Delhi Office',
                'code' => 'office-delhi',
                'description' => 'Ecommerce office in Delhi',
                'is_active' => true,
                'sort_order' => 2,
            ],
        ];

        foreach ($units as $unit) {
            DepartmentUnit::updateOrCreate(
                [
                    'department_id' => $unit['department_id'],
                    'code' => $unit['code']
                ],
                $unit
            );
        }
    }
}
