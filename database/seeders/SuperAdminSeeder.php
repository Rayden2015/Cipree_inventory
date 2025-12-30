<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Site;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure a default site exists, create if it doesn't
        $site = Site::firstOrCreate(
            ['name' => 'Default Site'],
            ['site_code' => 'DEFAULT']
        );

        // Creating Super Admin User (only if doesn't exist)
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Javed Ur Rehman', 
                'password' => Hash::make('secret'),
                'site_id' => $site->id,
                'status' => 'Active',
            ]
        );
        
        // Assign role if not already assigned
        if (!$superAdmin->hasRole('Super Admin')) {
            $superAdmin->assignRole('Super Admin');
        }

        // Creating Admin User
        // $admin = User::create([
        //     'name' => 'Syed Ahsan Kamal', 
        //     'email' => 'admin@gmail.com',
        //     'password' => Hash::make('secret'),
        //    'site_id'=>2,
        // ]);
        // $admin->assignRole('Admin');

        // Creating Product Manager User
        // $productManager = User::create([
        //     'name' => 'Abdul Muqeet', 
        //     'email' => 'user@gmail.com',
        //     'password' => Hash::make('secret'),
        //     'site_id'=>2,
        // ]);
        // $productManager->assignRole('Product Manager');
    }
}