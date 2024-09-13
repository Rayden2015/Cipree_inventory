<?php

namespace App\Exports;

use App\Models\InventoryItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Carbon\Carbon;

class ItemsListSiteExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $site_id = Auth::user()->site->id;

        // Fetch all necessary data including the created_at field
        $inventoryItems = InventoryItem::join('items', 'inventory_items.item_id', '=', 'items.id')
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
                'inventories.trans_type',
                'inventories.grn_number',
                'inventory_items.created_at'
            )
            ->get();

        // Use map to calculate the age using Carbon for each item
        return $inventoryItems->map(function($item) {
            // Calculate the age in days using Carbon
            $age = Carbon::parse($item->created_at)->diffInDays(now());

            // Return a new array with the calculated 'age' and original fields
            return [
                'item_description'   => $item->item_description,
                'item_part_number'   => $item->item_part_number,
                'item_stock_code'    => $item->item_stock_code,
                'quantity'           => $item->quantity,
                'amount'             => $item->amount,
                'location_name'      => $item->name,
                'trans_type'         => $item->trans_type,
                'grn_number'         => $item->grn_number,
                'age'                => $age // Calculated age in days
            ];
        });
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
            'GRN Number',
            'Age (Days)'
        ];
    }
}
