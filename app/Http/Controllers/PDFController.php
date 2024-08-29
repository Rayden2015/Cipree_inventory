<?php

namespace App\Http\Controllers;

use PDF;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Auth;

class PDFController extends Controller
{
    public function downloadItemsListPdf()
    {
        ini_set('memory_limit', '512M'); // Increase memory limit
        
        $site_id = Auth::user()->site->id;
        $pdf = PDF::loadView('pdfs.items_list_site_pdf', ['items' => []]); // Initialize PDF
    
        InventoryItem::join('items', 'inventory_items.item_id', '=', 'items.id')
            ->join('locations', 'locations.id', '=', 'inventory_items.location_id')
            ->join('inventories', 'inventories.id', '=', 'inventory_items.inventory_id')
            ->where('inventory_items.site_id', '=', $site_id)
            ->where('inventory_items.quantity', '>', 0)
            ->chunk(1000, function ($items) use ($pdf) {
                $pdf->loadView('pdfs.items_list_site_pdf', ['items' => $items]);
                // Append data or create separate pages for each chunk
            });
    
        return $pdf->download('items_list.pdf');
    
}
}