<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductAnswerSeeder extends Seeder
{
    public function run(): void
    {
        $answers = [];
        
        // Answers for Product 1 questions
        // Question 1: Does this phone support wireless charging?
        $answers[] = [
            'question_id' => 1,
            'user_id' => 1,
            'answer' => 'Yes, it supports MagSafe wireless charging and Qi wireless charging.',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(14),
            'updated_at' => Carbon::now()->subDays(14),
        ];
        
        $answers[] = [
            'question_id' => 1,
            'user_id' => null,
            'answer' => 'Yes, iPhone 15 Pro supports both MagSafe and standard Qi wireless charging.',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(13),
            'updated_at' => Carbon::now()->subDays(13),
        ];
        
        // Question 2: What is the battery capacity?
        $answers[] = [
            'question_id' => 2,
            'user_id' => 1,
            'answer' => 'The battery capacity is approximately 3274 mAh. It provides all-day battery life.',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(11),
            'updated_at' => Carbon::now()->subDays(11),
        ];
        
        // Question 3: Is the camera better than iPhone 14 Pro?
        $answers[] = [
            'question_id' => 3,
            'user_id' => 2,
            'answer' => 'Yes, the camera system has been improved with better low-light performance and new features.',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(7),
            'updated_at' => Carbon::now()->subDays(7),
        ];
        
        // Answers for Product 2 questions
        // Question 4: Does it come with a charger in the box?
        $answers[] = [
            'question_id' => 4,
            'user_id' => 2,
            'answer' => 'No, Samsung phones no longer come with a charger in the box. You need to purchase it separately.',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(13),
            'updated_at' => Carbon::now()->subDays(13),
        ];
        
        // Question 5: Is the display 120Hz refresh rate?
        $answers[] = [
            'question_id' => 5,
            'user_id' => 1,
            'answer' => 'Yes, it has a 120Hz adaptive refresh rate display which provides smooth scrolling and better gaming experience.',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(9),
            'updated_at' => Carbon::now()->subDays(9),
        ];
        
        $answers[] = [
            'question_id' => 5,
            'user_id' => 4,
            'answer' => 'Yes, 120Hz LTPO display with adaptive refresh rate.',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(8),
            'updated_at' => Carbon::now()->subDays(8),
        ];
        
        // Answers for Product 3 questions
        // Question 6: Are these suitable for running?
        $answers[] = [
            'question_id' => 6,
            'user_id' => 3,
            'answer' => 'While they are comfortable, Air Max 90 are more suited for casual wear. For running, I would recommend dedicated running shoes.',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(12),
            'updated_at' => Carbon::now()->subDays(12),
        ];
        
        // Question 7: What is the material of the upper?
        $answers[] = [
            'question_id' => 7,
            'user_id' => 1,
            'answer' => 'The upper is made of synthetic leather and mesh material for breathability.',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(8),
            'updated_at' => Carbon::now()->subDays(8),
        ];
        
        // Question 8: Do they run true to size?
        $answers[] = [
            'question_id' => 8,
            'user_id' => 3,
            'answer' => 'Yes, they run true to size. I ordered my regular size and they fit perfectly.',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(5),
            'updated_at' => Carbon::now()->subDays(5),
        ];
        
        // Answers for Product 4 questions
        // Question 9: Are these good for long distance running?
        $answers[] = [
            'question_id' => 9,
            'user_id' => 2,
            'answer' => 'Yes, Ultraboost 22 are excellent for long distance running. The Boost midsole provides great cushioning and energy return.',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(10),
            'updated_at' => Carbon::now()->subDays(10),
        ];
        
        $answers[] = [
            'question_id' => 9,
            'user_id' => 4,
            'answer' => 'Absolutely! These are designed for long distance running with excellent support and comfort.',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(9),
            'updated_at' => Carbon::now()->subDays(9),
        ];
        
        // Question 10: What is the weight of these shoes?
        $answers[] = [
            'question_id' => 10,
            'user_id' => 1,
            'answer' => 'The weight is approximately 310 grams per shoe (size 9). They are lightweight for a running shoe.',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(6),
            'updated_at' => Carbon::now()->subDays(6),
        ];
        
        // Answers for Product 5 questions
        // Question 11: How long does the battery last?
        $answers[] = [
            'question_id' => 11,
            'user_id' => 1,
            'answer' => 'With noise cancellation on, battery lasts about 30 hours. With ANC off, it can last up to 40 hours.',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(15),
            'updated_at' => Carbon::now()->subDays(15),
        ];
        
        $answers[] = [
            'question_id' => 11,
            'user_id' => 3,
            'answer' => 'Around 30 hours with ANC enabled. Quick charge gives 3 hours of playback in just 3 minutes.',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(14),
            'updated_at' => Carbon::now()->subDays(14),
        ];
        
        // Question 12: Is the noise cancellation better than XM4?
        $answers[] = [
            'question_id' => 12,
            'user_id' => 2,
            'answer' => 'Yes, the noise cancellation has been improved. It uses dual processors for better ANC performance.',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(10),
            'updated_at' => Carbon::now()->subDays(10),
        ];
        
        // Question 13: Can I use these for gaming?
        $answers[] = [
            'question_id' => 13,
            'user_id' => 3,
            'answer' => 'Yes, you can use them for gaming. However, there might be slight latency. For competitive gaming, wired headphones are better.',
            'is_approved' => true,
            'created_at' => Carbon::now()->subDays(4),
            'updated_at' => Carbon::now()->subDays(4),
        ];

        DB::table('product_answers')->insert($answers);
    }
}
