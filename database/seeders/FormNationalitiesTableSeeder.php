<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormNationalitiesTableSeeder extends Seeder
{
    public function run()
    {
        $now = now();

        $nationalities = [
            ['name' => 'French', 'is_default' => 0],
            ['name' => 'German', 'is_default' => 0],
            ['name' => 'Italian', 'is_default' => 1], // default
            ['name' => 'Spanish', 'is_default' => 0],
            ['name' => 'Portuguese', 'is_default' => 0],
            ['name' => 'Dutch', 'is_default' => 0],
            ['name' => 'Belgian', 'is_default' => 0],
            ['name' => 'Greek', 'is_default' => 0],
            ['name' => 'Swedish', 'is_default' => 0],
            ['name' => 'Norwegian', 'is_default' => 0],
            ['name' => 'Finnish', 'is_default' => 0],
            ['name' => 'Danish', 'is_default' => 0],
            ['name' => 'Austrian', 'is_default' => 0],
            ['name' => 'Swiss', 'is_default' => 0],
            ['name' => 'Polish', 'is_default' => 0],
            ['name' => 'Lithuanian', 'is_default' => 0],
            ['name' => 'Irish', 'is_default' => 0],
            ['name' => 'British', 'is_default' => 0],
        ];

        // Add timestamps to each nationality
        $nationalities = array_map(function ($item) use ($now) {
            return array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }, $nationalities);

        DB::table('form_nationalities')->insert($nationalities);
    }
}
