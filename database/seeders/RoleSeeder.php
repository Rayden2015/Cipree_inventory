<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'Super Admin']);
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $productManager = Role::firstOrCreate(['name' => 'Product Manager']);

        // Only assign permissions if they don't already exist
        $adminPermissions = [
            'add-user',
            'edit-user',
            'delete-user',
            'view-user',
            'add-item',
            'edit-item',
            'delete-item',
            'view-item'
        ];

        foreach ($adminPermissions as $permission) {
            if (!$admin->hasPermissionTo($permission)) {
                $admin->givePermissionTo($permission);
            }
        }

        $productManagerPermissions = [
            'add-item',
            'edit-item',
            'delete-item',
            'view-item'
        ];

        foreach ($productManagerPermissions as $permission) {
            if (!$productManager->hasPermissionTo($permission)) {
                $productManager->givePermissionTo($permission);
            }
        }
    }
}
