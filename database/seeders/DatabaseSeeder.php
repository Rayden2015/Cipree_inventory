<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            SuperAdminSeeder::class,
            // AuthoriserSeeder::class,
            // CategorySeeder::class,
            // DepartmentSeeder::class,
            // EnduserCategoriesSeeder::class,
            // EnduserSeeder::class,
            // InventoryTableSeeder::class,
            // LocationSeeder::class,
            // SectionSeeder::class,
            // SiteSeeder::class,
            // SupplierSeeder::class,
        ]);
    }
}