<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Ecommerce',
                'code' => 'ECOM',
                'description' => 'Ecommerce operations and management',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Logistics',
                'code' => 'LOG',
                'description' => 'Logistics and supply chain management',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'description' => 'Human resources and employee management',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'description' => 'IT infrastructure and systems',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Finance',
                'code' => 'FIN',
                'description' => 'Finance and accounting',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(
                ['code' => $department['code']],
                $department
            );
        }
    }
}
