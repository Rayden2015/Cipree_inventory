<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $item = [
            'name' => 'User',
            'email' => 'user@gmail.com',
            'password'=>bcrypt('secret'),
            'role_id'=>'2'
        ];
        User::Create($item);
    }
}
