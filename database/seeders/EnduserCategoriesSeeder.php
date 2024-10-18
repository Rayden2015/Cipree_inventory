<?php

namespace Database\Seeders;

use App\Models\EnduserCategory;
use Illuminate\Database\Seeder;
use App\Models\EndUsersCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EnduserCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['name' => 'Staff'],['name' => 'Equipment'],['name' => 'Organisation']
        ];

        // foreach ($items as $item) {
        //     EnduserCategoriesSeeder::create($item);
        // }
        foreach ($items as $item) {
            EndUsersCategory::create($item);
        }
    }
}
