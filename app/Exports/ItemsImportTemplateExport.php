<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemsImportTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['Sample Item 1', 'PART-001', 'Consumables', 'PCS', 10, 100, 7, 'FIFO'],
            ['Sample Item 2', 'PART-002', 'Tools', 'EA', 5, 50, 5, 'Weighted Average'],
        ];
    }

    public function headings(): array
    {
        return [
            'item_description',
            'item_part_number',
            'category_name',
            'uom_name',
            'reorder_level',
            'max_stock_level',
            'lead_time_days',
            'valuation_method',
        ];
    }
}
