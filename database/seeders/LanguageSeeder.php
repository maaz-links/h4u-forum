<?php

namespace Database\Seeders;

use App\Models\SpokenLanguage;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run()
    {
        $languages = [
            [ 'name' => 'English'],
            [ 'name' => 'Spanish'],
            [ 'name' => 'French'],
            [ 'name' => 'German'],
            [ 'name' => 'Italian'],
            [ 'name' => 'Portuguese'],
            [ 'name' => 'Russian'],
            [ 'name' => 'Chinese'],
            [ 'name' => 'Japanese'],
            [ 'name' => 'Arabic'],
        ];

        foreach ($languages as $language) {
            SpokenLanguage::create($language);
        }
    }
}