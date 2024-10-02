<?php

namespace App\Http\Controllers;

use PDF;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Auth;

class PDFController extends Controller
{
    public function downloadItemsListPdf()
    {
        ini_set('memory_limit', '512M'); // Increase memory limit

        $site_id = Auth::user()->site->id;

        // Initialize PDF with landscape orientation using setPaper
        $pdf = PDF::loadView('pdfs.items_list_site_pdf', ['items' => []])
                  ->setPaper('a3', 'landscape'); // Set to A4 size and landscape orientation

        // InventoryItem::leftjoin('items', 'inventory_items.item_id', '=', 'items.id')
        //     ->leftjoin('locations', 'locations.id', '=', 'inventory_items.location_id')
        //     ->leftjoin('inventories', 'inventories.id', '=', 'inventory_items.inventory_id')
        //     ->leftjoin('suppliers', 'suppliers.id','=','inventories.supplier_id')
        //     ->where('inventory_items.site_id', '=', $site_id)
        //     ->where('items.stock_quantity', '>', 0)
        InventoryItem::leftJoin('items', 'inventory_items.item_id', '=', 'items.id')
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
            'inventory_items.amount', 
            'inventory_items.unit_cost_exc_vat_gh', 
            'inventory_items.created_at',  // Select created_at field to calculate age
            'locations.name', 
            'inventories.trans_type',
            'inventories.grn_number',
            'inventories.po_number',
            'suppliers.name as supplier_name' // Select supplier name
        )
        ->chunk(1000, function ($items) use ($pdf) {
            // Render the PDF view with chunked data
            $pdf->loadView('pdfs.items_list_site_pdf', ['items' => $items]);
        });
    
    return $pdf->download('items_list.pdf');
    
   
}
}