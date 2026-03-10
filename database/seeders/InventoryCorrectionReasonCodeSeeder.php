<?php

namespace Database\Seeders;

use App\Models\InventoryCorrectionReasonCode;
use Illuminate\Database\Seeder;

class InventoryCorrectionReasonCodeSeeder extends Seeder
{
    public function run(): void
    {
        $codes = [
            ['code' => 'TYP-01', 'type' => 'Data Entry', 'use_case' => 'Clerical typo by Stores Officer (e.g. "22" instead of "2").'],
            ['code' => 'PRC-02', 'type' => 'Pricing Error', 'use_case' => 'Correcting Unit Cost to fix Asset TCO.'],
            ['code' => 'LOC-03', 'type' => 'Site Mismatch', 'use_case' => 'Stores Officer received items into the wrong physical location.'],
            ['code' => 'DMG-04', 'type' => 'Damaged/QA', 'use_case' => 'Items received but found broken during unboxing.'],
            ['code' => 'DUP-05', 'type' => 'Duplicate Entry', 'use_case' => 'Same GRN/Invoice entered twice by mistake.'],
            ['code' => 'MIS-06', 'type' => 'Mismatched GRN', 'use_case' => 'Physical count does not match the supplier delivery note.'],
        ];

        foreach ($codes as $row) {
            InventoryCorrectionReasonCode::firstOrCreate(
                ['code' => $row['code']],
                $row
            );
        }
    }
}
