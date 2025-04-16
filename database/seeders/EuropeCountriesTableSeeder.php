<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EuropeCountriesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('europe_countries')->insert([
            ['name' => 'France', 'display_order' => 1],
            ['name' => 'Germany', 'display_order' => 2],
            ['name' => 'Italy', 'display_order' => 3],
            ['name' => 'Spain', 'display_order' => 4],
            ['name' => 'Portugal', 'display_order' => 5],
        ]);

        $countries = DB::table('europe_countries')->pluck('id', 'name');
        $provinces = [
            // France
            ['country_id' => $countries['France'], 'name' => 'Île-de-France', 'display_order' => 1],
            ['country_id' => $countries['France'], 'name' => 'Provence-Alpes-Côte d\'Azur', 'display_order' => 2],
            ['country_id' => $countries['France'], 'name' => 'Auvergne-Rhône-Alpes', 'display_order' => 3],
            
            // Germany
            ['country_id' => $countries['Germany'], 'name' => 'Bavaria', 'display_order' => 1],
            ['country_id' => $countries['Germany'], 'name' => 'Baden-Württemberg', 'display_order' => 2],
            ['country_id' => $countries['Germany'], 'name' => 'Berlin', 'display_order' => 3],
            
            // Italy
            ['country_id' => $countries['Italy'], 'name' => 'Lombardy', 'display_order' => 1],
            ['country_id' => $countries['Italy'], 'name' => 'Lazio', 'display_order' => 2],
            ['country_id' => $countries['Italy'], 'name' => 'Tuscany', 'display_order' => 3],
            
            // Spain
            ['country_id' => $countries['Spain'], 'name' => 'Catalonia', 'display_order' => 1],
            ['country_id' => $countries['Spain'], 'name' => 'Madrid', 'display_order' => 2],
            ['country_id' => $countries['Spain'], 'name' => 'Andalusia', 'display_order' => 3],
            
            // Portugal
            ['country_id' => $countries['Portugal'], 'name' => 'Lisbon', 'display_order' => 1],
            ['country_id' => $countries['Portugal'], 'name' => 'Porto', 'display_order' => 2],
            ['country_id' => $countries['Portugal'], 'name' => 'Algarve', 'display_order' => 3],
        ];

        DB::table('europe_provinces')->insert($provinces);
    }
}