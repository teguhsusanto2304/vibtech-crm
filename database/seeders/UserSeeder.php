<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*User::create([
            'name' => 'Houston Teo',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'department' => 'IT',
            'position' => 'Developer',
            'branch_office' => 'Head Office',
        ]);*/

        //User::factory()->count(10)->create();
        $users = [
            ['name' => 'Houston Teo', 'email' => 'houston.teo@vib-tech.com.sg', 'department' => 'Marketing', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Marketing & Events'],
            ['name' => 'Ruby Chong', 'email' => 'ruby.chong@vib-tech.com.sg', 'department' => 'Admin', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Human Resources'],
            ['name' => 'David Lim', 'email' => 'david.lim@vib-tech.com.sg', 'department' => 'Sales', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'General Manager'],
            ['name' => 'Jimmy Tan', 'email' => 'jimmytan@vib-tech.com.sg', 'department' => 'Sales', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Sales & Application Engineer'],
            ['name' => 'Thasva', 'email' => 'thasva@vib-tech.com.sg', 'department' => 'Operations', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Field Service Engineer'],
            ['name' => 'Jc Lim', 'email' => 'jclim@vib-tech.com.sg', 'department' => 'System Projects', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Automation Engineer'],
            ['name' => 'Tan Li Xue', 'email' => 'tan.lx@vib-tech.com.sg', 'department' => 'System Projects', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Sales & Application Engineer'],
            ['name' => 'Teo Beiling', 'email' => 'beilingteo@vib-tech.com.sg', 'department' => 'Admin', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Assistant Manager'],
            ['name' => 'Trrishen', 'email' => 'trrishen@vib-tech.com.sg', 'department' => 'IT Network', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'IT Manager'],
            ['name' => 'Guan Chong', 'email' => 'gckoh@vib-tech.com.sg', 'department' => 'IT Network', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'IT Executive'],
            ['name' => 'Ron', 'email' => 'ronlogez@vib-tech.com.sg', 'department' => 'Operations', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Field Service Engineer'],
            ['name' => 'Khoo Kay Leng', 'email' => 'kay.leng@vib-tech.com.sg', 'department' => 'Sales', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Consultant'],
            ['name' => 'Minthu', 'email' => 'minthu@vib-tech.com.sg', 'department' => 'Operations', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Operations Manager'],
            ['name' => 'Chong Zixin', 'email' => 'czixin@vib-tech.com.sg', 'department' => 'Admin', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Admin'],
        ];

        foreach ($users as $user) {
            User::create(array_merge($user, [
                'password' => Hash::make('password')
            ]));
        }
    }
}
