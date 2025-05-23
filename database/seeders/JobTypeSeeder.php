<?php

namespace Database\Seeders;

use App\Models\JobType;
use Illuminate\Database\Seeder;

class JobTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'Calibration',
            'Printing',
            'Design',
            'Site Survey',
            'Site Job',
            'Procurement/Purchasing',
            'Project Service',
            'Delivery Pickup',
            'Logistics',
            'IT/Technical Support',
        ];

        // Looping and Inserting Array's Permissions into Permission Table
        foreach ($permissions as $permission) {
            JobType::create(['name' => $permission]);
        }
    }
}
