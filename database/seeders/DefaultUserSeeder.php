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
            'name' => 'Teguh Susanto',
            'email' => 'teguh.susanto@hotmail.com',
            'password' => Hash::make('password')
        ]);
        $superAdmin->assignRole('Super Admin');
    }
}
