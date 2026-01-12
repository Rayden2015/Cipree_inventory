<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EndusersImportTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['AS-001', 'Laptop Computer', 'Equipment', 'IT', 'Support', 'Dell Latitude', 'SN123456', 'Dell', 'Officer', 'Active'],
            ['AS-002', 'John Doe', 'Personnel', 'HR', 'Recruitment', null, null, null, 'Manager', 'Active'],
        ];
    }

    public function headings(): array
    {
        return [
            'asset_staff_id',
            'name_description',
            'type',
            'department_name',
            'section_name',
            'model',
            'serial_number',
            'manufacturer',
            'designation',
            'status',
        ];
    }
}
