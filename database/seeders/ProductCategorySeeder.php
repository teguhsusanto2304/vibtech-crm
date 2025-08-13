<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('product_categories')->insert([
            ['name' => 'Easy-Laser'],
            ['name' => 'SDT'],
            ['name' => 'CTC'],
        ]);
    }
}
