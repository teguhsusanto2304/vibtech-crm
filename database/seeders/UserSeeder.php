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
            ['name' => 'Houston Teo','nick_name'=>'Houston', 'email' => 'houston.teo@vib-tech.com.sg', 'department' => 'Marketing', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Marketing & Events'],
            ['name' => 'Ruby Chong','nick_name'=>'Ruby', 'email' => 'ruby.chong@vib-tech.com.sg', 'department' => 'Admin', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Human Resources'],
            ['name' => 'David Lim','nick_name'=>'David', 'email' => 'david.lim@vib-tech.com.sg', 'department' => 'Sales', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'General Manager'],
            ['name' => 'Jimmy Tan','nick_name'=>'Jimmy', 'email' => 'jimmytan@vib-tech.com.sg', 'department' => 'Sales', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Sales & Application Engineer'],
            ['name' => 'Thasvanithan','nick_name'=>'Thasva', 'email' => 'thasva@vib-tech.com.sg', 'department' => 'Operations', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Field Service Engineer'],
            ['name' => 'Jc Lim','nick_name'=>'JC', 'email' => 'jclim@vib-tech.com.sg', 'department' => 'System Projects', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Automation Engineer'],
            ['name' => 'Tan Li Xue','nick_name'=>'Li Xue', 'email' => 'tan.lx@vib-tech.com.sg', 'department' => 'System Projects', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Sales & Application Engineer'],
            ['name' => 'Teo Beiling','nick_name'=>'Beiling', 'email' => 'beilingteo@vib-tech.com.sg', 'department' => 'Admin', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Assistant Manager'],
            ['name' => 'Trrishen Chandra','nick_name'=>'Trrishen', 'email' => 'trrishen@vib-tech.com.sg', 'department' => 'IT Network', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'IT Manager'],
            ['name' => 'Koh Guan Chong','nick_name'=>'Chong', 'email' => 'gckoh@vib-tech.com.sg', 'department' => 'IT Network', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'IT Executive'],
            ['name' => 'Logeswaran','nick_name'=>'Ron', 'email' => 'ronlogez@vib-tech.com.sg', 'department' => 'Operations', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Field Service Engineer'],
            ['name' => 'Khoo Kay Leng','nick_name'=>'Khoo', 'email' => 'kay.leng@vib-tech.com.sg', 'department' => 'Sales', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Consultant'],
            ['name'=>'Kyaw Min Thu','nick_name' => 'Minthu', 'email' => 'minthu@vib-tech.com.sg', 'department' => 'Operations', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Operations Manager'],
            ['name' => 'Chong Zi Xin','nick_name' => 'Zi Xin', 'email' => 'czixin@vib-tech.com.sg', 'department' => 'Admin', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Admin'],
            ['name' => 'Nick Kang','nick_name' => 'Nick', 'email' => 'nick@vib-tech.com.sg', 'department' => 'Admin', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Admin'],
            ['name' => 'Helen Kiew','nick_name' => 'Helen', 'email' => 'helen@vib-tech.com.sg', 'department' => 'Admin', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Admin'],
            ['name' => 'Teguh Susanto','nick_name' => 'Teguh', 'email' => 'teguh.susanto@hotmail.com', 'department' => 'IT Network', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Admin'],
            ['name' => 'Bryan Chan','nick_name' => 'Bryan', 'email' => 'bryan@email.com', 'department' => 'Sales', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Admin'],
            ['name' => 'Saravanan','nick_name'=> 'Saras', 'email' => 'saras@email.com', 'department' => 'Project', 'branch_office' => '60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569', 'position' => 'Admin'],
        ];

        foreach ($users as $user) {
            User::create(array_merge($user, [
                'password' => Hash::make('password')
            ]));
        }
    }
}
