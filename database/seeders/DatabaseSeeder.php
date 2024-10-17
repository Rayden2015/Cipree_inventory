<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use CountriesTableSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        // User::create([
        //     'name' => 'Admin',
        //     'email' => 'larteysurviver@gmail.com',
        //     'phone' => '0243246888',
        //     'mobile' => '0591557389',
        //     'password'=>bcrypt('secret'),
        //     'skype'=>'none',
        //     'address'=>'labadi'

        // ]);
        $this->call(RoleSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(UserSeeder::class);
     $this->call(CountriesSeeder::class);
    }
}
