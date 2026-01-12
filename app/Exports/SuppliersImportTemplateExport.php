<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SuppliersImportTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['ABC Supplies Ltd', '123 Main Street', 'Accra', '0302123456', '0244123456', 'info@abcsupplies.com', 'Office Supplies, Electronics', 'John Doe', 'Dollars', 'RC123456', 'VAT123456', 'Electronics', 'Office Supplies', 'Stationery'],
            ['XYZ Trading Company', '456 Business Ave', 'Kumasi', '0322123456', '0500123456', 'contact@xyztrading.com', 'Hardware, Tools', 'Jane Smith', 'Pounds', 'RC789012', 'VAT789012', 'Hardware', 'Tools', 'Construction Materials'],
        ];
    }

    public function headings(): array
    {
        return [
            'name',
            'address',
            'location',
            'tel',
            'phone',
            'email',
            'items_supplied',
            'contact_person',
            'primary_currency',
            'comp_reg_no',
            'vat_reg_no',
            'item_cat1',
            'item_cat2',
            'item_cat3',
        ];
    }
}
