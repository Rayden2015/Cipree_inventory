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

        // Initialize PDF with landscape orientation using setPaper
        $pdf = PDF::loadView('pdfs.items_list_site_pdf', ['items' => []])
                  ->setPaper('a4', 'landscape'); // Set to A4 size and landscape orientation

        InventoryItem::leftjoin('items', 'inventory_items.item_id', '=', 'items.id')
            ->leftjoin('locations', 'locations.id', '=', 'inventory_items.location_id')
            ->leftjoin('inventories', 'inventories.id', '=', 'inventory_items.inventory_id')
            ->where('inventory_items.site_id', '=', $site_id)
            ->where('inventory_items.quantity', '>', 0)
            ->chunk(1000, function ($items) use ($pdf) {
                // Update PDF view with chunked items
                $pdf->loadView('pdfs.items_list_site_pdf', ['items' => $items]);
            });

        return $pdf->download('items_list.pdf');
    }
}
