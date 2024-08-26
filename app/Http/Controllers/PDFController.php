<?php

namespace App\Http\Controllers;

use PDF;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Auth;

class PDFController extends Controller
{
    public function downloadItemsListPdf(){
        $site_id = Auth::user()->site->id;
        $items = InventoryItem::join('items', 'inventory_items.item_id', '=', 'items.id')
            ->join('locations', 'locations.id', '=', 'inventory_items.location_id')
            ->join('inventories', 'inventories.id', '=', 'inventory_items.inventory_id')
            ->where('inventory_items.site_id', '=', $site_id)
            ->where('inventory_items.quantity', '>', '0')->get();

        // $pdf = PDF::loadView('pdfs.items_list_site_pdf', compact('items'));
        // return $pdf->download('items_list.pdf');
        $inventory = PDF::loadView('pdfs.items_list_site_pdf', compact('items'));
       
        $filename = 'Cipree' . '.pdf'; // Append '.pdf' to the filename

        return $inventory->download($filename);



        
    }
}
