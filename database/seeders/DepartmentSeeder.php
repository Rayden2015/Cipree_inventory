<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['id'=>'1','name' => 'Human Resource', 'description' => 'Null'],
            ['name' => 'Admin', 'description' => 'Null'],
            ['name' => 'Production', 'description' => 'Null'],
            ['name' => 'HSE', 'description' => 'Null'],
            ['name' => 'Engineering', 'description' => 'Null'],
            ['name' => 'Finance & Administration', 'description' => 'Null'],
            ['name' => 'Health Safety & Environment', 'description' => 'Null'],
            ['name' => 'Mining', 'description' => 'Null']
        ];

        foreach ($items as $item) {
            Department::create($item);
        }
    }
}
