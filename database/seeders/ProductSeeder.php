<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('products')->insert([
            [
                'name' => 'XT 770',
                'sku_no' => '155909',
                'product_category_id' => 1,
                'quantity' => 55,
                'image' => null,
                'created_by' => 'Beiling Teo',
                'created_at' => \Carbon\Carbon::parse('2025-05-23'),
                'updated_at' => \Carbon\Carbon::parse('2025-09-05'),
            ],
            [
                'name' => 'D92 BTA',
                'sku_no' => '155908',
                'product_category_id' => 2,
                'quantity' => 1,
                'image' => null,
                'created_by' => 'Beiling Teo',
                'created_at' => \Carbon\Carbon::parse('2025-05-20'),
                'updated_at' => \Carbon\Carbon::parse('2025-06-15'),
            ],
            [
                'name' => 'XT 770',
                'sku_no' => '155909-sdt',
                'product_category_id' => 2,
                'quantity' => 5,
                'image' => null,
                'created_by' => 'Beiling Teo',
                'created_at' => \Carbon\Carbon::parse('2025-05-23'),
                'updated_at' => \Carbon\Carbon::parse('2025-06-07'),
            ],
            [
                'name' => 'XT 770',
                'sku_no' => '155909-ctc',
                'product_category_id' => 1,
                'quantity' => 2,
                'image' => null,
                'created_by' => 3,
                'created_at' => \Carbon\Carbon::parse('2025-05-23'),
                'updated_at' => \Carbon\Carbon::parse('2025-06-07'),
            ],
        ]);
    }
}
