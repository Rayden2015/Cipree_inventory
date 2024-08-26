<?php

namespace App\Exports;

use App\Models\InventoryItem;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemsListSiteExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $site_id = Auth::user()->site->id;
        return InventoryItem::join('items', 'inventory_items.item_id', '=', 'items.id')
            ->join('locations', 'locations.id', '=', 'inventory_items.location_id')
            ->join('inventories', 'inventories.id', '=', 'inventory_items.inventory_id')
            ->where('inventory_items.site_id', '=', $site_id)
            ->where('inventory_items.quantity', '>', '0')
            ->select(
                'items.item_description', 
                'items.item_part_number', 
                'items.item_stock_code', 
                'inventory_items.quantity', 
                'inventory_items.amount', 
                'locations.name', 
                'inventories.trans_type'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'Description',
            'Part Number',
            'Stock Code',
            'Qty in Stock',
            'Amount',
            'Location',
            'Purchase Type',
        ];
    }
}
