<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            'products.view',
            'products.create',
            'products.edit',
            'products.delete',

            'orders.view',
            'orders.update'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission
            ]);
        }

        $admin = Role::firstOrCreate([
            'name' => 'admin'
        ]);

        $admin->givePermissionTo($permissions);

        $manager = Role::firstOrCreate([
            'name' => 'manager'
        ]);

        $manager->givePermissionTo([
            'users.view',
            'products.view',
            'orders.view'
        ]);
    }
}