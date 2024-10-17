<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $item = [
            'name' => 'Admin',
            'email' => 'superadmin@gmail.com',
            'password'=>bcrypt('secret'),
            'role_id'=>'1',
            'description' => 'This is just a brief info about me.'
        ];
        User::Create($item);
    }
}
