<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EuropeCountriesTableSeeder extends Seeder
{
    public function run()
    {
        $now = now();

        DB::table('europe_countries')->insert([
            ['name' => 'France', 'display_order' => 1, 'created_at' => $now, 'updated_at' => $now, 'is_default' => 0],
            ['name' => 'Germany', 'display_order' => 2, 'created_at' => $now, 'updated_at' => $now, 'is_default' => 0],
            ['name' => 'Italy', 'display_order' => 3, 'created_at' => $now, 'updated_at' => $now, 'is_default' => 1],
            ['name' => 'Spain', 'display_order' => 4, 'created_at' => $now, 'updated_at' => $now, 'is_default' => 0],
            ['name' => 'Portugal', 'display_order' => 5, 'created_at' => $now, 'updated_at' => $now, 'is_default' => 0],
        ]);

        $countries = DB::table('europe_countries')->pluck('id', 'name');

        $rawProvinces = [
            // France
            ['country' => 'France', 'name' => 'Île-de-France', 'display_order' => 1],
            ['country' => 'France', 'name' => 'Provence-Alpes-Côte d\'Azur', 'display_order' => 2],
            ['country' => 'France', 'name' => 'Auvergne-Rhône-Alpes', 'display_order' => 3],

            // Germany
            ['country' => 'Germany', 'name' => 'Bavaria', 'display_order' => 1],
            ['country' => 'Germany', 'name' => 'Baden-Württemberg', 'display_order' => 2],
            ['country' => 'Germany', 'name' => 'Berlin', 'display_order' => 3],

            // Italy
            ['country' => 'Italy', 'name' => 'Lombardy', 'display_order' => 1],
            ['country' => 'Italy', 'name' => 'Lazio', 'display_order' => 2],
            ['country' => 'Italy', 'name' => 'Tuscany', 'display_order' => 3],
            ['country' => 'Italy', 'name' => 'Ancona', 'display_order' => 4],
            ['country' => 'Italy', 'name' => 'Bari', 'display_order' => 5],
            ['country' => 'Italy', 'name' => 'Bergamo', 'display_order' => 6],
            ['country' => 'Italy', 'name' => 'Bologna', 'display_order' => 7],
            ['country' => 'Italy', 'name' => 'Bolzano', 'display_order' => 8],
            ['country' => 'Italy', 'name' => 'Cagliari', 'display_order' => 9],
            ['country' => 'Italy', 'name' => 'Catania', 'display_order' => 10],
            ['country' => 'Italy', 'name' => 'Forlì', 'display_order' => 11],
            ['country' => 'Italy', 'name' => 'Firenze', 'display_order' => 12],
            ['country' => 'Italy', 'name' => 'Genova', 'display_order' => 13],
            ['country' => 'Italy', 'name' => 'Latina', 'display_order' => 14],
            ['country' => 'Italy', 'name' => 'Livorno', 'display_order' => 15],
            ['country' => 'Italy', 'name' => 'Messina', 'display_order' => 16],
            ['country' => 'Italy', 'name' => 'Milano', 'display_order' => 17],
            ['country' => 'Italy', 'name' => 'Modena', 'display_order' => 18],
            ['country' => 'Italy', 'name' => 'Monza', 'display_order' => 19],
            ['country' => 'Italy', 'name' => 'Napoli', 'display_order' => 20],
            ['country' => 'Italy', 'name' => 'Padova', 'display_order' => 21],
            ['country' => 'Italy', 'name' => 'Palermo', 'display_order' => 22],
            ['country' => 'Italy', 'name' => 'Parma', 'display_order' => 23],
            ['country' => 'Italy', 'name' => 'Pescara', 'display_order' => 24],
            ['country' => 'Italy', 'name' => 'Perugia', 'display_order' => 25],
            ['country' => 'Italy', 'name' => 'Reggio Calabria', 'display_order' => 26],
            ['country' => 'Italy', 'name' => 'Reggio Emilia', 'display_order' => 27],
            ['country' => 'Italy', 'name' => 'Rimini', 'display_order' => 28],
            ['country' => 'Italy', 'name' => 'Roma', 'display_order' => 29],
            ['country' => 'Italy', 'name' => 'Salerno', 'display_order' => 30],
            ['country' => 'Italy', 'name' => 'Terni', 'display_order' => 31],
            ['country' => 'Italy', 'name' => 'Torino', 'display_order' => 32],
            ['country' => 'Italy', 'name' => 'Trento', 'display_order' => 33],
            ['country' => 'Italy', 'name' => 'Trieste', 'display_order' => 34],
            ['country' => 'Italy', 'name' => 'Venezia', 'display_order' => 35],
            ['country' => 'Italy', 'name' => 'Verona', 'display_order' => 36],

            // Spain
            ['country' => 'Spain', 'name' => 'Catalonia', 'display_order' => 1],
            ['country' => 'Spain', 'name' => 'Madrid', 'display_order' => 2],
            ['country' => 'Spain', 'name' => 'Andalusia', 'display_order' => 3],

            // Portugal
            ['country' => 'Portugal', 'name' => 'Lisbon', 'display_order' => 1],
            ['country' => 'Portugal', 'name' => 'Porto', 'display_order' => 2],
            ['country' => 'Portugal', 'name' => 'Algarve', 'display_order' => 3],
        ];

        $provinces = [];

        foreach ($rawProvinces as $province) {
            $provinces[] = [
                'country_id'   => $countries[$province['country']],
                'name'         => $province['name'],
                'display_order'=> $province['display_order'],
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }

        DB::table('europe_provinces')->insert($provinces);
    }
}
