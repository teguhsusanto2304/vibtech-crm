<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'sa@vib-tech.com.sg',
            'password' => Hash::make('password')
        ]);
        $superAdmin->assignRole('Super Admin');
    }
}
