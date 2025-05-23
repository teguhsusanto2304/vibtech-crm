<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Role::create(['name' => 'Super Admin']);
        $admin = Role::create(['name' => 'Super Admin']);
        // $productManager = Role::create(['name' => 'Product Manager']);
        // $user = Role::create(['name' => 'User']);

        $admin->givePermissionTo([
            'create-user',
            'edit-user',
            'delete-user',
            'create-role',
            'edit-role',
            'delete-role',
            'create-permission',
            'edit-permission',
            'delete-permission',
        ]);

        /*$productManager->givePermissionTo([
            'create-product',
            'edit-product',
            'delete-product'
        ]);

        $user->givePermissionTo([
            'view-product'
        ]);*/
    }
}
