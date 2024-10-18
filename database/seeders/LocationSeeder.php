<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
       $items = [
        ['name' => 'Ware house - Main'],
        ['name' => 'Ware house - CNT01'],
        ['name' => 'Ware house - CNT02'],
        ['name' => 'Ware house - CNT03'],
        ['name' => 'Ware house - CNT04'],
        ['name' => 'Ware house - CNT05'],
        ['name' => 'Tyre Bay'],
        ['name' => 'Lube Bay - Main'],
        ['name' => 'Lube Bay - Aux'],
        ['name' => 'GET Bay - Main'],
        ['name' => 'GET Bay - Aux'],
       ];
       foreach ($items as $item) {
        Location::create($item);
       }
    }
}
