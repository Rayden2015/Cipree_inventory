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
    $inventoryItems = InventoryItem::leftJoin('items', 'inventory_items.item_id', '=', 'items.id')
        ->leftJoin('locations', 'locations.id', '=', 'inventory_items.location_id')
        ->leftJoin('inventories', 'inventories.id', '=', 'inventory_items.inventory_id')
        ->leftJoin('suppliers', 'suppliers.id', '=', 'inventories.supplier_id') // Join suppliers table
        ->where('inventory_items.site_id', '=', $site_id)
        ->where('items.stock_quantity', '>', 0)
        ->select(
            'items.item_description', 
            'items.item_part_number', 
            'items.item_stock_code', 
            'items.stock_quantity',
            'inventory_items.quantity', 
            'items.amount', 
            'inventory_items.unit_cost_exc_vat_gh', 
            'inventory_items.created_at',  // Select created_at field to calculate age
            'locations.name', 
            'inventories.trans_type',
            'inventories.grn_number',
            'inventories.po_number',
            'suppliers.name as supplier_name' // Select supplier name
        )
        ->get();

    // Use map to calculate the age using Carbon for each item
    return $inventoryItems->map(function($item) {
        // Calculate the age in days using Carbon
        $age = Carbon::parse($item->created_at)->diffInDays(now());

        // Check if supplier_name is null, provide a default value if necessary
        $supplierName = $item->supplier_name ?? '';

        // Return a new array with the calculated 'age' and original fields
        return [
            'item_description'   => $item->item_description,
            'item_part_number'   => $item->item_part_number,
            'item_stock_code'    => $item->item_stock_code,
            'stock_quantity'           => $item->stock_quantity,
            'unit_cost_exc_vat_gh' => $item->unit_cost_exc_vat_gh,
            'amount'             => $item->amount,
            'location_name'      => $item->name,
            'trans_type'         => $item->trans_type,
            'grn_number'         => $item->grn_number,
            'po_number'          => $item->po_number,
            'supplier_name'      => $supplierName,
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
        'Unit Cost',
        'Amount',
        'Location',
        'Purchase Type',
        'GRN Number',
        'PO Number',
        'Supplier',
        'Age (Days)',  // Add the age column heading
    ];
}

}
