<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Supplier::create([
            'name' => 'Supplier1',
            'address' => 'Achimota',
            'location' => 'Achimota Mall',
            'tel' => '0123456789',
            'phone' => '1234567890',
            'email' => 'supplier1@gmail.com',
            'items_supplied' => 'gears,brakes'
        ]);
    }
}
