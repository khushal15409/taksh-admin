<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AppDashboardSection;

class AppDashboardSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            [
                'section_key' => 'banners',
                'section_name' => 'Banners',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'section_key' => 'trending_products',
                'section_name' => 'Trending Products',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'section_key' => 'latest_products',
                'section_name' => 'Latest Products',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'section_key' => 'categories',
                'section_name' => 'Categories',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'section_key' => 'express_30_products',
                'section_name' => 'Express 30 Products',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($sections as $section) {
            AppDashboardSection::updateOrCreate(
                ['section_key' => $section['section_key']],
                $section
            );
        }
    }
}
