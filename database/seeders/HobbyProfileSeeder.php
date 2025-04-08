<?php

namespace Database\Seeders;

use App\Models\Hobby;
use App\Models\HostessService;
use App\Models\Profile;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;

class HobbyProfileSeeder extends Seeder
{
    public function run()
    {
        // Get all profiles and hobbies
        $profiles = UserProfile::all();
        $hobbies = HostessService::all();
        //dd($profiles);
        // For each profile, attach 3-5 random hobbies
        $profiles->each(function ($profile) use ($hobbies) {
            //dd($profile,$hobbies);
            $randomHobbies = $hobbies->random(rand(3, 5))->pluck('id')->toArray();
            //dd('ok');
            //dd($profile->hostess_services()->sync($randomHobbies));
            //dd($profile->id);
            //foreach($randomHobbies as $h){}
            $profile->hostess_services()->sync($randomHobbies);
        });
    }
}