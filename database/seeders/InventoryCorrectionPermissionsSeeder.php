<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class InventoryCorrectionPermissionsSeeder extends Seeder
{
    /**
     * Assign V4.0 correction permissions to roles.
     * Run after PermissionSeeder and RoleSeeder.
     */
    public function run(): void
    {
        $initiate = Permission::firstOrCreate(['name' => 'initiate-inventory-correction']);
        $approve = Permission::firstOrCreate(['name' => 'approve-inventory-correction']);
        $execute = Permission::firstOrCreate(['name' => 'execute-inventory-adjustment']);
        $audit = Permission::firstOrCreate(['name' => 'view-inventory-audit-log']);

        foreach (['store_officer', 'store_assistant'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role && !$role->hasPermissionTo($initiate)) {
                $role->givePermissionTo($initiate);
            }
        }

        foreach (['Super Authoriser', 'admin', 'Admin'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                foreach ([$approve, $execute, $audit] as $perm) {
                    if (!$role->hasPermissionTo($perm)) {
                        $role->givePermissionTo($perm);
                    }
                }
            }
        }
    }
}
