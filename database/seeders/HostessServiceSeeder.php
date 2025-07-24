<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HostessServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Model Photo',
            ],
            [
                'name' => 'Talk',
            ],
            [
                'name' => 'Dinners',
            ],
            [
                'name' => 'Fake Girlfriend',
            ],
            [
                'name' => 'Company',
            ],
            [
                'name' => 'Friendship',
            ],
            [
                'name' => 'Dating',
            ],
            [
                'name' => 'Parties',
            ],
        ];

        foreach ($services as $service) {
            DB::table('hostess_services')->insert([
                'name' => $service['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}