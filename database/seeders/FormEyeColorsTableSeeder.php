<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormEyeColorsTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $colors = [
            ['name' => 'Brown', 'is_default' => 1],
            ['name' => 'Blue', 'is_default' => 0],
            ['name' => 'Green', 'is_default' => 0],
            ['name' => 'Hazel', 'is_default' => 0],
            ['name' => 'Gray', 'is_default' => 0],
        ];

        $data = array_map(fn($color) => array_merge($color, [
            'created_at' => $now,
            'updated_at' => $now,
        ]), $colors);

        DB::table('form_eye_colors')->insert($data);
    }
}
