<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'GBL5343R',
            'GBK2108D',
            'GBE8427A',
            'SKV8915L'
        ];
        foreach($data as $item):
            Vehicle::create(['name'=>$item,'path_image'=>'-']);
        endforeach;
    }
}
