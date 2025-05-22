<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExtraCities extends Seeder
{
    public function run()
    {
        $countries = DB::table('europe_countries')->pluck('id', 'name');
        $provinces = [
            // France
           
            // Italy
            // ['country_id' => $countries['Italy'], 'name' => 'Lombardy', 'display_order' => 1],
            // ['country_id' => $countries['Italy'], 'name' => 'Lazio', 'display_order' => 2],
            // ['country_id' => $countries['Italy'], 'name' => 'Tuscany', 'display_order' => 3],

            ['country_id' => $countries['Italy'], 'name' => 'Ancona', 'display_order' => 1],
            ['country_id' => $countries['Italy'], 'name' => 'Bari', 'display_order' => 2],
            ['country_id' => $countries['Italy'], 'name' => 'Bergamo', 'display_order' => 3],
            ['country_id' => $countries['Italy'], 'name' => 'Bologna', 'display_order' => 4],
            ['country_id' => $countries['Italy'], 'name' => 'Bolzano', 'display_order' => 5],
            ['country_id' => $countries['Italy'], 'name' => 'Cagliari', 'display_order' => 6],
            ['country_id' => $countries['Italy'], 'name' => 'Catania', 'display_order' => 7],
            ['country_id' => $countries['Italy'], 'name' => 'Forlì', 'display_order' => 8],
            ['country_id' => $countries['Italy'], 'name' => 'Firenze', 'display_order' => 9],
            ['country_id' => $countries['Italy'], 'name' => 'Genova', 'display_order' => 10],
            ['country_id' => $countries['Italy'], 'name' => 'Latina', 'display_order' => 11],
            ['country_id' => $countries['Italy'], 'name' => 'Livorno', 'display_order' => 12],
            ['country_id' => $countries['Italy'], 'name' => 'Messina', 'display_order' => 13],
            ['country_id' => $countries['Italy'], 'name' => 'Milano', 'display_order' => 14],
            ['country_id' => $countries['Italy'], 'name' => 'Modena', 'display_order' => 15],
            ['country_id' => $countries['Italy'], 'name' => 'Monza', 'display_order' => 16],
            ['country_id' => $countries['Italy'], 'name' => 'Napoli', 'display_order' => 17],
            ['country_id' => $countries['Italy'], 'name' => 'Padova', 'display_order' => 18],
            ['country_id' => $countries['Italy'], 'name' => 'Palermo', 'display_order' => 19],
            ['country_id' => $countries['Italy'], 'name' => 'Parma', 'display_order' => 20],
            ['country_id' => $countries['Italy'], 'name' => 'Pescara', 'display_order' => 21],
            ['country_id' => $countries['Italy'], 'name' => 'Perugia', 'display_order' => 22],
            ['country_id' => $countries['Italy'], 'name' => 'Reggio Calabria', 'display_order' => 23],
            ['country_id' => $countries['Italy'], 'name' => 'Reggio Emilia', 'display_order' => 24],
            ['country_id' => $countries['Italy'], 'name' => 'Rimini', 'display_order' => 25],
            ['country_id' => $countries['Italy'], 'name' => 'Roma', 'display_order' => 26],
            ['country_id' => $countries['Italy'], 'name' => 'Salerno', 'display_order' => 27],
            ['country_id' => $countries['Italy'], 'name' => 'Terni', 'display_order' => 28],
            ['country_id' => $countries['Italy'], 'name' => 'Torino', 'display_order' => 29],
            ['country_id' => $countries['Italy'], 'name' => 'Trento', 'display_order' => 30],
            ['country_id' => $countries['Italy'], 'name' => 'Trieste', 'display_order' => 31],
            ['country_id' => $countries['Italy'], 'name' => 'Venezia', 'display_order' => 32],
            ['country_id' => $countries['Italy'], 'name' => 'Verona', 'display_order' => 33],
            
        ];

        DB::table('europe_provinces')->insert($provinces);
    }
}