<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Company::create([
            'name'=>'CIPREE','address'=>'La','phone'=>'1234567890',
            'email'=>'company@gmail.com','vat'=>'','vat_no'=>'5656','website'=>'company.com','image'=>''
        ]);
    }
}
