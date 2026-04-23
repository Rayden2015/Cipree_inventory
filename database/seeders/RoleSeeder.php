<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'Super Admin']);
        $tenantAdmin = Role::firstOrCreate(['name' => 'Tenant Admin']);
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
            'view-item',
            'manage-work-order-number',
            'view-work-order',
            'add-work-order',
            'edit-work-order',
            'maintenance-planner-dashboard',
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

        // Tenant Admin should have full access within their tenant scope (org-wide setup data).
        // Departments and Sections are tenant-wide structures, not site-only.
        $tenantAdminPermissions = [
            // Company/setup (tenant admin)
            'company-module',
            'info',
            'reviews',
            'view-site',
            'add-site',
            'edit-site',
            'delete-site',
            'view-uom',
            'add-uom',
            'edit-uom',
            'delete-uom',
            'view-role',
            'add-role',
            'edit-role',
            'delete-role',
            'view-permission',
            'add-permission',
            'edit-permission',
            'delete-permission',

            // Users (tenant admin manages access)
            'view-user',
            'add-user',
            'edit-user',
            'delete-user',

            'view-department',
            'add-department',
            'edit-department',
            'delete-department',
            'view-section',
            'add-section',
            'edit-section',
            'delete-section',

            // Employees
            'view-employee',
            'add-employee',
            'edit-employee',
            'delete-employee',

            // Endusers split
            'view-asset',
            'add-asset',
            'edit-asset',
            'delete-asset',
            'view-personnel',
            'add-personnel',
            'edit-personnel',
            'delete-personnel',
            'view-enduser',
            'add-enduser',
            'edit-enduser',
            'delete-enduser',
        ];

        foreach ($tenantAdminPermissions as $permissionName) {
            // Ensure the permission exists before assignment (safe for partial seed runs).
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
            if (!$tenantAdmin->hasPermissionTo($permissionName)) {
                $tenantAdmin->givePermissionTo($permissionName);
            }
        }
    }
}
