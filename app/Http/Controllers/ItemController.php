<?php

namespace App\Http\Controllers;

use App\Models\Uom;
use App\Models\Item;
use App\Models\Category;
use App\Models\SorderPart;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\ItemCountPerSite;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryItemDetail;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['auth', 'permission:view-item'])->only('show');
        $this->middleware(['auth', 'permission:add-item'])->only('create');
        $this->middleware(['auth', 'permission:view-item'])->only('index');
        $this->middleware(['auth', 'permission:edit-item'])->only('edit');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
           
            // $items = Item::all();
            $items = Item::orderBy('item_description')->paginate(20);
            // $items = Item::join('inventory_items','items.id', '=','inventory_items.item_id')->latest('items.created_at')->paginate(20);

            // Log successful user input and request
            Log::info('ItemController| Index() | Items listed', [
                'user_details' => Auth::user(),
                'message' => 'Items Listed Successfully',
                //'itmes' => $items
            ]);
            return view('items.index', compact('items'));
          

        } catch (\Exception $e) {
            // Log errors
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('HomeController | Index() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $categories = Category::all();
        $uom = Uom::all();
        return view('items.create', compact('categories', 'uom'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            // 'item_part_number'=>'unique:items,item_part_number',
            'item_description' => 'regex:/^[A-Za-z0-9. \"(),_-]+$/',
            'item_part_number' => 'unique:items|regex:/^[A-Za-z0-9. \"(),_-]+$/',
        ]);

        try {
            $site_id = Auth::user()->site->id;
            $added_by = Auth::id();
            $lastorderId = Item::orderBy('id', 'desc')->value('id');
            $category_name = Category::where('id', $request->item_category_id)->value('name');
            // $num = $lastorderId + 1;
            $initials = substr($category_name, 0, 2);
            $stock_codes = $initials . str_pad($lastorderId + 1, 4, "0", STR_PAD_LEFT);
            // $stock_code = $initials.$num;
            $item = new Item();
            $item->item_description = $request->item_description;
            $item->item_uom = $request->item_uom;
            $item->uom_id = $request->uom_id;

            $item->item_part_number = $request->item_part_number;
            $item->item_stock_code = $stock_codes;
            $item->added_by = $added_by;
            $item->item_category_id = $request->item_category_id;
            $item->reorder_level = $request->reorder_level;
            $item->new_category = $request->new_category;
            $item->site_id = $site_id;
            $item->save();

            // Log successful user input and request
            Log::info('Item stored successfully', [
                'user_id' => Auth::id(),
                'user_details' => Auth::user(),
                'item_description' => $request->item_description,
                'item_part_number' => $request->item_part_number,
                // Add other relevant information
            ]);


            return redirect()->back()->withSuccess('Successfully Updated');
            // dd($stock_codes );
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('ItemController | Store() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $item = Item::find($id);
        $inventory_items = InventoryItem::where('item_id', '=', $id)->paginate(15);
        // return view('items.show', compact('item'));
        return view('items.show', compact('item', 'inventory_items'));
        // dd($inventory_items);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $item = Item::find($id);
            $categories = Category::all();
            $uom = Uom::all();

            // Log successful user input and request
            Log::info('Item edit form displayed', [
                'user_id' => Auth::id(),
                'user_details' => Auth::user(),
                'item_id' => $id,
                // Add other relevant information
            ]);
            return view('items.edit', compact('item', 'categories', 'uom'));
        } catch (\Exception $e) {
            // Log errors
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('HomeController | Index() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = Item::find($id);
        $request->validate([
            'item_stock_code' => 'unique:items,item_stock_code,' . $id,
            'item_description' => 'regex:/^[A-Za-z0-9. \"(),_-]+$/',
            'item_part_number' => 'regex:/^[A-Za-z0-9. \"(),_-]+$/',

        ]);

        try {
            $modified_by = Auth::id();
            $lastorderId = Item::orderBy('id', 'desc')->value('id');
            $category_name = Category::where('id', $request->item_category_id)->value('name');
            // $num = $lastorderId + 1;
            $initials = substr($category_name, 0, 2);
            $stock_codes = $initials . str_pad($lastorderId, 4, "0", STR_PAD_LEFT);
            $item->item_description = $request->item_description;
            $item->item_uom = $request->item_uom;
            $item->uom_id = $request->uom_id;
            $item->item_part_number = $request->item_part_number;
            $item->item_stock_code = $request->item_stock_code;
            $item->modified_by = $modified_by;
            $item->item_category_id = $request->item_category_id;
            $item->reorder_level = $request->reorder_level;
            $item->new_category = $request->new_category;
            // dd($lastorderId,$category_name,$item);
            $item->save();

            // Log successful user input and request
            Log::info('Item updated successfully', [
                'user_id' => Auth::id(),
                'user_details' => Auth::user(),
                'item_id' => $id,
                'item_description' => $request->item_description,
                'item_part_number' => $request->item_part_number,
                // Add other relevant information
            ]);


            return redirect()->back()->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            // Log errors
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('ItemController | Update() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $item = Item::find($id);
            $item->delete();

            // Log successful user input and request
            Log::info('Item deleted successfully', [
                'user_id' => Auth::id(),
                'user_details' => Auth::user(),
                'item_id' => $id,
                // Add other relevant information
            ]);


            return redirect()->back()->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            // Log errors
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('ItemController | Destroy() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }

    public function item_search(Request $request)
    {

        if ($request->search) {

            $items = Item::where('item_description', 'like', "%" . $request->search . "%")
                ->orWhere('item_part_number', 'like', "%" . $request->search . "%")
                ->orWhere('item_stock_code', 'like', "%" . $request->search . "%")
                ->latest()->paginate();
            if ($items->isEmpty()) {

                return redirect()->back()->withError('Item not found');
            } elseif ($items->isNotEmpty()) {
                return view('items.index', compact('items'));
            }
        } else     $items = Item::latest()->paginate(20);

        return view('items.index', compact('items'));
    }

    public function product_history(Request $request)
    {
        $query = Item::query();
    
        // Check if there's a search query and apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('item_description', 'like', "%{$search}%")
                  ->orWhere('item_part_number', 'like', "%{$search}%")
                  ->orWhere('item_stock_code', 'like', "%{$search}%");
            });
        }
    
        // Paginate results
        $product_history = $query->latest()->paginate(20);
        
        return view('product_history.index', compact('product_history'));
    }
    

    public function product_history_show($id)
    {
        $product_history = Item::find($id);
        $received = InventoryItemDetail::where('item_id','=',$id)->get();
        $supplied = SorderPart::
        leftjoin('sorders','sorders.id','=','sorder_parts.sorder_id')->
        where('sorder_parts.item_id','=',$id)->
        where('sorders.status','=','Supplied')->get();
        return view('product_history.show', compact('product_history','received','supplied'));
    }


    public function itemspersite(){
        {
            // Fetch all records from the MySQL view
            $items = ItemCountPerSite::all();
    
            // Pass the data to the Blade file
            return view('items.item_count', compact('items'));
        }
    }
}
