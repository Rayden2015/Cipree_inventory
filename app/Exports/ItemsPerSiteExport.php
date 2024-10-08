<?php
namespace App\Exports;

use App\Models\ItemCountPerSite;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemsPerSiteExport implements FromCollection, WithHeadings
{
    // Fetch the data
    public function collection()
    {
        return ItemCountPerSite::all();
    }

    // Define the headings for the Excel sheet
    public function headings(): array
    {
        return [
            'Item ID',
            'Item Description',
            'Site Name',
            'Total Received',
            'Total Supplied',
            'Updated Stock Quantity',
        ];
    }
}
