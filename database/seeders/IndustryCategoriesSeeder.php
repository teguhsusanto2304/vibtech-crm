<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndustryCategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Biochemical',
            'WaterWorks',
            'Oil & Gas',
            'Energy',
            'Manufacturing',
            'Paper & Pulp',
            'Food & Beverage',
            'Pharmaceutical',
            'Public Facility',
            'Petrochemical',
            'Aerospace & Aviation',
            'Semiconductor',
            'Maritime',
            'Data Center',
            'Others',
        ];

        foreach ($categories as $category) {
            DB::table('industry_categories')->insert([
                'name' => $category,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
