<?php

namespace Database\Seeders;

use App\Models\Part;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PartsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Part::create([
            'supplier_id'=> '1',
            'name'=> 'Gear Box',
            'description'=>'None Yet',
            'site_id'=>'1',
            'location_id'=> '1',
            'quantity'=>'1',
        ]);
    }
}
