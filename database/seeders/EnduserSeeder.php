<?php

namespace Database\Seeders;

use App\Models\Enduser;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EnduserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Enduser::create([
            'asset_staff_id' => 'Enduser1',
            'type' => 'Machine',
            'department' => 'Department1',
            'section' => 'Section1',
            'model' => 'Model1',
            'serial_number' => 'Serial1',
            // 'bic' => 'bBic1',
            'manufacturer' => 'Manufact1'
        ]);
    }
}
