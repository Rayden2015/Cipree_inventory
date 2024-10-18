<?php

namespace Database\Seeders;

use App\Models\Site;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Site::create([
            'name'=>'Site1',
             'name'=>'Site2'
        ]);
    }
}
