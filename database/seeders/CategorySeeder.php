<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['name' => 'RIG CONSUMABLE'],
            ['name' => 'SAFETY SIGNAGE'],
            ['name' => 'STATIONARY'],
            ['name' => 'TOOL'],
            ['name' => 'TYRE N ACESS'],
            ['name' => 'UNDERCARRIAGE'],
            ['name' => 'WELDING'],
            ['name' => 'ANCILLARY'],
            ['name' => 'ELECTRICAL'],
            ['name' => 'GET'],
            ['name' => 'LUBES'],
            ['name' => 'MECHANICAL'],
            ['name' => 'PM KIT'],
            ['name' => 'PPE'],
        ];
        foreach($items as $item) {
            Category::create($item);
        }
    }
}
