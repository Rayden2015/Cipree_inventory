<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AuthoriserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['name' => 'authoriser', 'email' => 'authoriser@gmail.com', 'status' => 'Active', 'site_id' => '1', 'password' => bcrypt('secret'), 'remember_token' => '', 'role_id' => '7'],

        ];
        foreach ($items as $items) {
            $user =  User::create($items);
        }
        $user->assignRole('authoriser');
    }
}
