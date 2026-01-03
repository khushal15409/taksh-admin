<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DashboardDataSeeder extends Seeder
{
    public function run(): void
    {
        // Update categories with image URLs
        $categories = [
            [
                'id' => 1,
                'image_url' => 'https://fastly.picsum.photos/id/21/800/600.jpg?hmac=21v21v21v21v21v21v21v21v21v21v21v21v21v21v21v',
                'icon_url' => 'https://fastly.picsum.photos/id/21/200/200.jpg?hmac=21v21v21v21v21v21v21v21v21v21v21v21v21v21v21v',
            ],
            [
                'id' => 2,
                'image_url' => 'https://fastly.picsum.photos/id/22/800/600.jpg?hmac=22v22v22v22v22v22v22v22v22v22v22v22v22v22v22v',
                'icon_url' => 'https://fastly.picsum.photos/id/22/200/200.jpg?hmac=22v22v22v22v22v22v22v22v22v22v22v22v22v22v22v',
            ],
            [
                'id' => 3,
                'image_url' => 'https://fastly.picsum.photos/id/23/800/600.jpg?hmac=23v23v23v23v23v23v23v23v23v23v23v23v23v23v23v',
                'icon_url' => 'https://fastly.picsum.photos/id/23/200/200.jpg?hmac=23v23v23v23v23v23v23v23v23v23v23v23v23v23v23v',
            ],
            [
                'id' => 4,
                'image_url' => 'https://fastly.picsum.photos/id/24/800/600.jpg?hmac=24v24v24v24v24v24v24v24v24v24v24v24v24v24v24v',
                'icon_url' => 'https://fastly.picsum.photos/id/24/200/200.jpg?hmac=24v24v24v24v24v24v24v24v24v24v24v24v24v24v24v',
            ],
            [
                'id' => 5,
                'image_url' => 'https://fastly.picsum.photos/id/25/800/600.jpg?hmac=25v25v25v25v25v25v25v25v25v25v25v25v25v25v25v',
                'icon_url' => 'https://fastly.picsum.photos/id/25/200/200.jpg?hmac=25v25v25v25v25v25v25v25v25v25v25v25v25v25v25v',
            ],
            [
                'id' => 6,
                'image_url' => 'https://fastly.picsum.photos/id/27/800/600.jpg?hmac=27v27v27v27v27v27v27v27v27v27v27v27v27v27v27v',
                'icon_url' => 'https://fastly.picsum.photos/id/27/200/200.jpg?hmac=27v27v27v27v27v27v27v27v27v27v27v27v27v27v27v',
            ],
            [
                'id' => 7,
                'image_url' => 'https://fastly.picsum.photos/id/28/800/600.jpg?hmac=28v28v28v28v28v28v28v28v28v28v28v28v28v28v28v',
                'icon_url' => 'https://fastly.picsum.photos/id/28/200/200.jpg?hmac=28v28v28v28v28v28v28v28v28v28v28v28v28v28v28v',
            ],
            [
                'id' => 8,
                'image_url' => 'https://fastly.picsum.photos/id/29/800/600.jpg?hmac=29v29v29v29v29v29v29v29v29v29v29v29v29v29v29v',
                'icon_url' => 'https://fastly.picsum.photos/id/29/200/200.jpg?hmac=29v29v29v29v29v29v29v29v29v29v29v29v29v29v29v',
            ],
            [
                'id' => 9,
                'image_url' => 'https://fastly.picsum.photos/id/30/800/600.jpg?hmac=30v30v30v30v30v30v30v30v30v30v30v30v30v30v30v',
                'icon_url' => 'https://fastly.picsum.photos/id/30/200/200.jpg?hmac=30v30v30v30v30v30v30v30v30v30v30v30v30v30v30v',
            ],
        ];

        foreach ($categories as $category) {
            DB::table('categories')
                ->where('id', $category['id'])
                ->update([
                    'image_url' => $category['image_url'],
                    'icon_url' => $category['icon_url'],
                ]);
        }

        // Mark some products as trending
        DB::table('products')
            ->whereIn('id', [1, 2, 3]) // iPhone 15 Pro, Samsung Galaxy S24, Nike Air Max 90
            ->update(['is_trending' => true]);
    }
}
