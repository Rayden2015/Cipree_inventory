<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creating Super Admin User
        $superAdmin = User::create([
            'name' => 'Javed Ur Rehman', 
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('secret'),
            'site_id'=>1,
        ]);
        $superAdmin->assignRole('Super Admin');

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