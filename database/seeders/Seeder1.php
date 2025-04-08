<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Seeder1 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_profiles')->insert([
            [
                //'user_email' => 'user1@example.com',
                'gender' => 0, // Assuming 0 = Male, 1 = Female, 2 = Other
                'date_of_birth' => '1990-05-15',
                // 'country' => 'United States',
                // 'province' => 'California',
                // 'nationality' => 'American',
                // 'height' => 175.50,
                // 'weight' => 70.20,
                // 'shoesize' => 42.5,
                // 'eye_color' => 'Brown',
                // 'dress_size' => 'M',
                'available_for' => json_encode(['dating', 'friendship']),
                // 'interests' => 'Hiking, Photography, Cooking',
                // 'social_links' => json_encode([
                //     'instagram' => 'https://instagram.com/user1',
                //     'facebook' => 'https://facebook.com/user1'
                // ]),
                // 'telegram' => '@user1',
                // 'personal_description' => 'Outgoing person who loves outdoor activities and meeting new people.',
                // 'visibility_status' => 1,
                // 'notification_preference' => 1,
                // 'credits' => 100,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                //'user_email' => 'user2@example.com',
                'gender' => 1, // Female
                'date_of_birth' => '1985-08-22',
                // 'country' => 'Canada',
                // 'province' => 'Ontario',
                // 'nationality' => 'Canadian',
                // 'height' => 165.00,
                // 'weight' => 55.80,
                // 'shoesize' => 38.0,
                // 'eye_color' => 'Blue',
                // 'dress_size' => 'S',
                'available_for' => json_encode(['networking', 'language_exchange']),
                // 'interests' => 'Reading, Traveling, Yoga',
                // 'social_links' => json_encode([
                //     'twitter' => 'https://twitter.com/user2',
                //     'linkedin' => 'https://linkedin.com/in/user2'
                // ]),
                // 'telegram' => '@user2',
                // 'personal_description' => 'Book lover and travel enthusiast looking to connect with like-minded people.',
                // 'visibility_status' => 1,
                // 'notification_preference' => 2,
                // 'credits' => 50,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}