<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Interest;

class InterestsTableSeeder extends Seeder
{
    public function run()
    {
        $interests = [
            'Photography',
            'Travel',
            'Fashion',
            'Fitness',
            'Cooking',
            'Music',
            'Art',
            'Reading',
            'Sports',
            'Technology',
            'Gaming',
            'Dancing',
            'Yoga',
            'Meditation',
            'Movies',
            'Writing',
            'Blogging',
            'Hiking',
            'Cycling',
            'Swimming'
        ];

        foreach ($interests as $interest) {
            Interest::create(['name' => $interest]);
        }
    }
}