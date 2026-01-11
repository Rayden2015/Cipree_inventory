<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Site;
use App\Models\Department;
use App\Models\Order;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TestAuthorisersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure roles exist
        $superAuthoriserRole = Role::firstOrCreate(['name' => 'Super Authoriser', 'guard_name' => 'web']);
        $deptAuthoriserRole = Role::firstOrCreate(['name' => 'Department Authoriser', 'guard_name' => 'web']);

        // Ensure a site exists
        $site = Site::firstOrCreate(
            ['name' => 'Main Site'],
            ['site_code' => 'MAIN']
        );

        // Create departments for testing
        $department1 = Department::firstOrCreate(
            ['name' => 'Engineering'],
            ['description' => 'Engineering Department', 'site_id' => $site->id]
        );

        $department2 = Department::firstOrCreate(
            ['name' => 'Finance'],
            ['description' => 'Finance Department', 'site_id' => $site->id]
        );

        // Create Super Authoriser user
        $superAuthoriser = User::firstOrCreate(
            ['email' => 'super.authoriser@test.com'],
            [
                'name' => 'Super Authoriser',
                'password' => Hash::make('password'),
                'site_id' => $site->id,
                'status' => 'Active',
            ]
        );
        
        if (!$superAuthoriser->hasRole('Super Authoriser')) {
            $superAuthoriser->assignRole('Super Authoriser');
        }

        // Create Department Authoriser user (Engineering)
        $deptAuthoriser = User::firstOrCreate(
            ['email' => 'dept.authoriser@test.com'],
            [
                'name' => 'Department Authoriser',
                'password' => Hash::make('password'),
                'site_id' => $site->id,
                'department_id' => $department1->id,
                'status' => 'Active',
            ]
        );
        
        if (!$deptAuthoriser->hasRole('Department Authoriser')) {
            $deptAuthoriser->assignRole('Department Authoriser');
        }

        // Create a requester user in Engineering department to create orders
        $requester = User::firstOrCreate(
            ['email' => 'requester@test.com'],
            [
                'name' => 'Test Requester',
                'password' => Hash::make('password'),
                'site_id' => $site->id,
                'department_id' => $department1->id,
                'status' => 'Active',
            ]
        );

        // Create a requester user in Finance department
        $requester2 = User::firstOrCreate(
            ['email' => 'requester2@test.com'],
            [
                'name' => 'Test Requester Finance',
                'password' => Hash::make('password'),
                'site_id' => $site->id,
                'department_id' => $department2->id,
                'status' => 'Active',
            ]
        );

        $this->command->info('Test users created successfully!');
        $this->command->info('Super Authoriser: super.authoriser@test.com / password');
        $this->command->info('Department Authoriser: dept.authoriser@test.com / password');
        $this->command->info('Requester (Engineering): requester@test.com / password');
        $this->command->info('Requester (Finance): requester2@test.com / password');
    }
}
