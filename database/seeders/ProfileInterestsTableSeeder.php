<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserProfile;
use App\Models\Interest;

class ProfileInterestsTableSeeder extends Seeder
{
    public function run()
    {
        // Get all profiles and interests
        $profiles = UserProfile::all();
        $interests = Interest::all();

        // For each profile, attach random interests
        $profiles->each(function ($profile) use ($interests) {
            // Get random number of interests (between 3 and 8)
            $randomInterests = $interests->random(rand(3, 8));
            
            // Attach these interests to the profile
            $profile->interests()->attach($randomInterests->pluck('id')->toArray());
        });
    }
}