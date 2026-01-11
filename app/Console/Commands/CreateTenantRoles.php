<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateTenantRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Super Admin and Tenant Admin roles for multi-tenancy';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating tenant roles...');

        // Create Super Admin role
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $this->info('✓ Super Admin role created/verified');

        // Create Tenant Admin role
        $tenantAdmin = Role::firstOrCreate(['name' => 'Tenant Admin']);
        $this->info('✓ Tenant Admin role created/verified');

        // Create permissions for tenant management
        $permissions = [
            'manage tenants',
            'create tenants',
            'edit tenants',
            'delete tenants',
            'view all tenants',
            'manage tenant admins',
            'create tenant admins',
            'manage own tenant',
            'manage tenant sites',
            'manage tenant users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to Super Admin (all permissions)
        $superAdmin->syncPermissions(Permission::all());
        $this->info('✓ Permissions assigned to Super Admin');

        // Assign permissions to Tenant Admin (tenant-specific permissions)
        $tenantAdminPermissions = Permission::whereIn('name', [
            'manage own tenant',
            'manage tenant sites',
            'manage tenant users',
        ])->get();
        $tenantAdmin->syncPermissions($tenantAdminPermissions);
        $this->info('✓ Permissions assigned to Tenant Admin');

        $this->info('✅ Tenant roles created successfully!');
        
        return Command::SUCCESS;
    }
}
