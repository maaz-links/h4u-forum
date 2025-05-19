<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Page;

class PagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Page::create([
            'slug' => 'terms-and-conditions',
            'content' => '<h1>Initial Terms and Conditions</h1><p>Edit this content using the Quill editor.</p>',
        ]);

        Page::create([
            'slug' => 'privacy-policy',
            'content' => '<h1>Initial Privacy Policy</h1><p>Edit this content using the Quill editor.</p>',
        ]);

        Page::create([
            'slug' => 'credits-and-payment',
            'content' => '<h1>Initial Credits and Payment</h1><p>Edit this content using the Quill editor.</p>',
        ]);

        Page::create([
            'slug' => 'cookies-info',
            'content' => '<h1>Initial Cookies info</h1><p>Edit this content using the Quill editor.</p>',
        ]);

    }
}