<?php

namespace App\Exports;
use App\Models\SorderPart;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SearchResultsExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
    public function headings(): array
    {
        // Set your Excel headers here
        return [
            'Supply Date',
            'SR Number',
            'Description',
            'Part Number',
            'Stock Code',
            'Quantity',
            'Cost',
           
            // Add more headers as needed
        ];
    }

    public function collection()
    {
        return $this->data;
    }
}