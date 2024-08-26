<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Array of permissions
        $permissions = [
            // Modules
            'company-module',
            'endusers-module',
            'suppliers-module',
            'inventory-management-module',
            'navigate-module',
            'purchase-management-module',
            'reports-module',

            // Info & Reviews
            'info',
            'reviews',

            // Roles Permissions
            'add-role',
            'edit-role',
            'delete-role',
            'view-role',

            // Users Permissions
            'add-user',
            'edit-user',
            'delete-user',
            'view-user',

            // Permissions Permissions
            'view-permission',
            'add-permission',
            'edit-permission',
            'delete-permission',

            // Endusers Permissions
            'view-enduser',
            'add-enduser',
            'edit-enduser',
            'delete-enduser',

            // Departments Permissions
            'view-department',
            'add-department',
            'edit-department',
            'delete-department',

            // Sections Permissions
            'view-section',
            'add-section',
            'edit-section',
            'delete-section',

            // Sites Permissions
            'view-site',
            'add-site',
            'edit-site',
            'delete-site',

            // Requester Permissions
            'purchase-requests',
            'requester-stock-requests',
            'purchase-orders',
            'transfer-requests',

            // Authoriser Permissions
            'authoriser-request-lists',
            'authoriser-purchase-lists',
            'authoriser-stock-requests',
            'authoriser-stock-purchase-requests',

            // Store Officer Permissions
            'view-item',
            'add-item',
            'edit-item',
            'delete-item',

            'view-location',
            'add-location',
            'edit-location',
            'delete-location',

            'view-item-group',
            'add-item-group',
            'edit-item-group',
            'delete-item-group',

            'view-grn',
            'add-grn',
            'edit-grn',
            'delete-grn',

            'stock-request-lists',
            'stock-purchase-requests',
            'request-lists',
            'purchase-lists',
            'received-history',
            'supply-history',
            'stock-requests',
            'monthly-reports',

            // Purchasing Officer Permissions
            'direct-purchase-requests',
            'direct-purchase-orders',
            'purchasing-officer-stock-purchase-requests',
            'stock-purchase-orders',
            'draft-purchase-orders',
            'stock-purchase-request-pos',

            // Supplier Permissions
            'view-supplier',
            'add-supplier',
            'edit-supplier',
            'delete-supplier',

            // Levy Permissions
            'view-levy',
            'add-levy',
            'edit-levy',
            'delete-levy',

            // Tax Permissions
            'view-tax',
            'add-tax',
            'edit-tax',
            'delete-tax',

            // Site Admin Permissions
            // Add your site admin permissions here
        ];

        // Looping and inserting permissions into the Permission table
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
