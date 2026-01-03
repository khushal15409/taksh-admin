<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [];
        
        // Product 1 (iPhone 15 Pro) - 3 questions
        $questions[] = [
            'product_id' => 1,
            'user_id' => 2,
            'question' => 'Does this phone support wireless charging?',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(15),
            'updated_at' => Carbon::now()->subDays(15),
        ];
        
        $questions[] = [
            'product_id' => 1,
            'user_id' => 3,
            'question' => 'What is the battery capacity?',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(12),
            'updated_at' => Carbon::now()->subDays(12),
        ];
        
        $questions[] = [
            'product_id' => 1,
            'user_id' => 4,
            'question' => 'Is the camera better than iPhone 14 Pro?',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(8),
            'updated_at' => Carbon::now()->subDays(8),
        ];
        
        // Product 2 (Samsung Galaxy S24) - 2 questions
        $questions[] = [
            'product_id' => 2,
            'user_id' => 1,
            'question' => 'Does it come with a charger in the box?',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(14),
            'updated_at' => Carbon::now()->subDays(14),
        ];
        
        $questions[] = [
            'product_id' => 2,
            'user_id' => 3,
            'question' => 'Is the display 120Hz refresh rate?',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(10),
            'updated_at' => Carbon::now()->subDays(10),
        ];
        
        // Product 3 (Nike Air Max 90) - 3 questions
        $questions[] = [
            'product_id' => 3,
            'user_id' => 1,
            'question' => 'Are these suitable for running?',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(13),
            'updated_at' => Carbon::now()->subDays(13),
        ];
        
        $questions[] = [
            'product_id' => 3,
            'user_id' => 2,
            'question' => 'What is the material of the upper?',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(9),
            'updated_at' => Carbon::now()->subDays(9),
        ];
        
        $questions[] = [
            'product_id' => 3,
            'user_id' => 4,
            'question' => 'Do they run true to size?',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(6),
            'updated_at' => Carbon::now()->subDays(6),
        ];
        
        // Product 4 (Adidas Ultraboost 22) - 2 questions
        $questions[] = [
            'product_id' => 4,
            'user_id' => 1,
            'question' => 'Are these good for long distance running?',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(11),
            'updated_at' => Carbon::now()->subDays(11),
        ];
        
        $questions[] = [
            'product_id' => 4,
            'user_id' => 3,
            'question' => 'What is the weight of these shoes?',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(7),
            'updated_at' => Carbon::now()->subDays(7),
        ];
        
        // Product 5 (Sony WH-1000XM5) - 3 questions
        $questions[] = [
            'product_id' => 5,
            'user_id' => 2,
            'question' => 'How long does the battery last?',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(16),
            'updated_at' => Carbon::now()->subDays(16),
        ];
        
        $questions[] = [
            'product_id' => 5,
            'user_id' => 4,
            'question' => 'Is the noise cancellation better than XM4?',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(11),
            'updated_at' => Carbon::now()->subDays(11),
        ];
        
        $questions[] = [
            'product_id' => 5,
            'user_id' => 1,
            'question' => 'Can I use these for gaming?',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(5),
            'updated_at' => Carbon::now()->subDays(5),
        ];

        DB::table('product_questions')->insert($questions);
    }
}
