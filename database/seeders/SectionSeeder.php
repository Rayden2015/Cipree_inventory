<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['id'=>'1','name' => 'Accounts', 'description' => 'Null'],
            ['name' => 'Drill & Blast', 'description' => 'Null'],
            ['name' => 'HR', 'description' => 'Null'],
            ['name' => 'HSE', 'description' => 'Null'],
            ['name' => 'Load & Haul', 'description' => 'Null'],
            ['name' => 'Maintenance', 'description' => 'Null'],
            ['name' => 'Maintenance Planning', 'description' => 'Null'],
            ['name' => 'Planning', 'description' => 'Null'],
            ['name' => 'Production', 'description' => 'Null'],
            ['name' => 'Supply Chain', 'description' => 'Null'],
            ['name' => 'Technical Service', 'description' => 'Null'],
            ['name' => 'Welding', 'description' => 'Null']
        ];

        foreach ($items as $item) {
            Section::create($item);
        }
    }
}
