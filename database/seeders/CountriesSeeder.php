<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = json_decode(file_get_contents(database_path('data/countries.json')), true);

        foreach ($countries as $country) {
            DB::table('countries')->insert([
                'name' => $country['name'],
                'iso_code' => $country['iso2'],
            ]);
        }
    }
}
