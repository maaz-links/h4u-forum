<?php

namespace Database\Seeders;

use App\Models\ProfileType;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfileTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
//     public function run()
// {
//     $types = [
//         ['name' => 'Hostess'],
//         ['name' => 'Sugarbaby'],
//         ['name' => 'Wingwoman']
//     ];
    
//     foreach ($types as $type) {
//         ProfileType::create($type);
//     }

//     $userProfile = UserProfile::first();
//     $userProfile->profileTypes()->sync([1, 2]); // Attach Hostess and Sugarbaby
// }
public function run()
{
    // Create profile types if they don't exist
    $types = [
        ['name' => 'Hostess'],
        ['name' => 'Sugarbaby'],
        ['name' => 'Wingwoman']
    ];
    
    foreach ($types as $type) {
        ProfileType::firstOrCreate($type);
    }

    // Get all profile types IDs
    $profileTypeIds = ProfileType::pluck('id')->toArray();

    // Get all user profiles where associated user has HOSTESS role
    $hostessProfiles = UserProfile::with('user')
        ->whereHas('user', function($query) {
            $query->where('role', User::ROLE_HOSTESS);
        })
        ->get();

    // Randomly assign 1-3 profile types to each hostess profile
    foreach ($hostessProfiles as $profile) {
        $randomCount = rand(1, count($profileTypeIds));
        $randomKeys = array_rand($profileTypeIds, $randomCount);
        
        // Convert to array if single value
        $randomKeys = (array) $randomKeys;
        
        // Get the actual IDs from the keys
        $typesToAttach = array_map(function($key) use ($profileTypeIds) {
            return $profileTypeIds[$key];
        }, $randomKeys);
        
        $profile->profileTypes()->sync($typesToAttach);
    }
}
}
