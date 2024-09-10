<?php

namespace App\Http\Controllers;

use PDF;
use Carbon\Carbon;
use App\Helpers\Pay;
use App\Models\Item;
use App\Models\User;
use App\Models\Company;
use App\Models\Enduser;
use App\Models\Category;
use App\Models\Location;
use App\Models\Supplier;
use App\Models\Inventory;
use App\Models\SorderPart;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Mail\ProductNotification;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryItemDetail;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Console\Input\Input;


class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['auth', 'permission:view-grn'])->only('show');
        $this->middleware(['auth', 'permission:add-grn'])->only('create');
        $this->middleware(['auth', 'permission:view-grn'])->only('index');
        $this->middleware(['auth', 'permission:edit-grn'])->only('edit');

        $this->middleware(['auth', 'permission:received-history'])->only('inventory_item_history');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $site_id = Auth::user()->site->id;
        $inventories = Inventory::where('site_id', '=', $site_id)->latest()->paginate(20);
        return view('inventories.index', compact('inventories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $site_id = Auth::user()->site->id;
        $requested_date = Carbon::now()->toDateTimeString();
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $requested_date)
            ->format('d-m-Y (H:i)');
        $locations = Location::all();
        $suppliers = Supplier::all();
        $deliveres = User::where('site_id', '=', $site_id)->get();
        $categories = Category::where('site_id', '=', $site_id)->get();
        $grn_number = Pay::genGrnCode();
        $items = Item::all();

        return view('inventories.create', compact('locations', 'suppliers', 'deliveres', 'categories', 'grn_number', 'items', 'date'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Log start of the store method
            Log::info('InventoryController@store - Start', [
                'timestamp' => now(),
                'method' => 'store',
                'request_payload' => $request->all(),
            ]);
    
            // Validate the request
            $request->validate([
                'photo' => 'sometimes|nullable|image|mimes:jpeg,gif,png,jpg|max:9048',
                'desc' => 'nullable',
                'quantity' => 'required|array',
                'quantity.*' => 'numeric|min:0', // Ensure that quantity is not negative
                'invoice_number' => 'nullable|unique:inventories,invoice_number',
                'grn_number' => 'unique:inventories,grn_number',
            ]);
    
            // Check for negative quantities or amounts in the request
            $products = $request->products;
            $quantities = $request->quantity;
            $amounts = $request->unit_cost_exc_vat_gh;
    
            for ($i = 0; $i < count($quantities); $i++) {
                if ($quantities[$i] < 0 || $amounts[$i] < 0) {
                    // Rollback the transaction
                    DB::rollback();
    
                    // Log the error
                    Log::error('InventoryController@store - Negative Value Detected', [
                        'timestamp' => now(),
                        'method' => 'store',
                        'product_index' => $i,
                        'quantity' => $quantities[$i],
                        'amount' => $amounts[$i],
                    ]);
    
                    // Return an error message to the user
                    return redirect()->back()->withError('Negative values are not allowed. Please check your input and try again.');
                }
            }
    
            // Create the inventory
            $date = Carbon::now();
            $authid = Auth::id();
            $site_id = Auth::user()->site->id;
            $inventory = Inventory::create([
                'waybill' => $request->waybill,
                'dollar_rate' => $request->dollar_rate,
                'po_number' => $request->po_number,
                'grn_number' => Pay::genGrnCode(),
                'supplier_id' => $request->supplier_id,
                'trans_type' => $request->trans_type,
                'enduser_id' => $request->enduser_id,
                'delivered_by' => $request->delivered_by,
                'date' => $date,
                'user_id' => $authid,
                'request_number' => $request->request_number,
                'item_id' => $request->item_id,
                'billing_currency' => $request->billing_currency,
                'invoice_number' => $request->invoice_number,
                'exchange_rate' => $request->exchange_rate,
                'site_id' => $site_id
            ]);
    
            // Log inventory creation success
            Log::info('InventoryController@store - Inventory Created Successfully', [
                'user' => Auth::user(),
                'inventory_id' => $inventory->id,
                'Inventory Details' => $inventory
            ]);
    
            // Process products and create inventory items
            if ($inventory) {
                for ($i = 0; $i < count($products); $i++) {
                    $newamount = ($amounts[$i] * $quantities[$i]);
                    $newdiscount = ($request->discount[$i] / 100) * $newamount;
                    $totalamount = $newamount - $newdiscount;
    
                    $singlediscount = ($amounts[$i] * 1);
                    $singlediscount1 = ($request->discount[$i] / 100) * $singlediscount;
                    $singlediscount2 = $singlediscount - $singlediscount1;
    
                    // Log start of inventory item creation
                    Log::info('InventoryController | Store()| Inventory Item Creation Start', [
                        'timestamp' => now(),
                        'method' => 'store',
                        'inventory_details' => $inventory,
                        'product_index' => $i,
                        'product_data' => $products,
                    ]);
    
                    // Create inventory item
                    InventoryItem::create([
                        'inventory_id' => $inventory->id,
                        'location_id' => $request->location_id[$i],
                        'quantity' => $quantities[$i],
                        'item_id' => $request->item_id[$i],
                        'unit_cost_exc_vat_gh' => $singlediscount2,
                        'discount' => $request->discount[$i],
                        'before_discount' => $amounts[$i],
                        'amount' => $totalamount,
                        'site_id' => $site_id,
                    ]);
    
                    // Log inventory item creation success
                    Log::info('InventoryController@store - Inventory Item Created Successfully', [
                        'timestamp' => now(),
                        'method' => 'store',
                        'inventory_id' => $inventory->id,
                        'product_index' => $i,
                    ]);
    
                    // Create inventory item detail
                    InventoryItemDetail::create([
                        'inventory_id' => $inventory->id,
                        'location_id' => $request->location_id[$i],
                        'quantity' => $quantities[$i],
                        'item_id' => $request->item_id[$i],
                        'unit_cost_exc_vat_gh' => $singlediscount2,
                        'discount' => $request->discount[$i],
                        'before_discount' => $amounts[$i],
                        'amount' => $totalamount,
                        'site_id' => $site_id,
                    ]);
    
                    // Log inventory item detail creation success
                    Log::info('InventoryController@store - Inventory Item Detail Created Successfully', [
                        'timestamp' => now(),
                        'method' => 'store',
                        'inventory_id' => $inventory->id,
                        'product_index' => $i,
                    ]);
                }
            }
    
            // Update stock quantities
            DB::select(
                'UPDATE items i
                JOIN (
                    SELECT t.item_id, SUM(t.quantity) AS calculated_quantity
                    FROM items i
                    JOIN inventory_items t ON i.id = t.item_id
                    GROUP BY t.item_id
                ) AS subquery ON i.id = subquery.item_id
                SET i.stock_quantity = subquery.calculated_quantity'
            );
    
            // Log success message
            Log::info('InventoryController@store - Success', [
                'timestamp' => now(),
                'method' => 'store',
                'status' => 'success',
            ]);
    
            // Commit the transaction
            DB::commit();
    
            return back()->withSuccess('Successfully Updated');
        } catch (\Exception $exception) {
            // Rollback the transaction
            DB::rollback();
    
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | Store() Error ' . $unique_id, [
                'exception' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
    
            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }
    


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $company = Company::first();
        $inventory = Inventory::where('id', '=', $id)->first();
        $inventories = InventoryItemDetail::where('inventory_id', '=', $id)->get();
        return view('inventories.show', compact('inventory', 'company', 'inventories'));
        // dd($inventory,$inventories);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $site_id = Auth::user()->site->id;
            $endusers = Enduser::where('site_id', '=', $site_id)->get();

            $inventory = Inventory::where('id', '=', $id)->first();

            // Paginate inventory items
            $inventory_items = InventoryItem::where('site_id', '=', $site_id)->where('inventory_id', '=', $id)->paginate(50); // Adjust the number of items per page as needed

            $locations = Location::where('site_id', '=', $site_id)->get();
            $suppliers = Supplier::all();
            $deliveries = User::where('site_id', '=', $site_id)->get();
            $categories = Category::where('site_id', '=', $site_id)->get();
            $items = Item::all();

            $selectedRole = $inventory_items->isNotEmpty() ? $inventory_items->first()->location_id : null;

            return view('inventories.edit', compact('suppliers', 'deliveries', 'categories', 'locations', 'inventory', 'endusers', 'inventory_items', 'selectedRole', 'items'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | Edit() Error ' . $unique_id,[
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
    try {
        $inventory = Inventory::find($id);
        $auth = Auth::user()->id;

        // Validate the request
        $request->validate([
            'invoice_number' => 'nullable|unique:inventories,invoice_number,' . $id,
        ]);

        // Log the information before the update
        Log::info('InventoryController | Edit', [
            'user' => Auth::user(),
            'before_inventory_edit' => $inventory,
        ]);

        // Update the inventory fields
        $inventory->supplier_id = $request->supplier_id;
        $inventory->enduser_id = $request->enduser_id;
        $inventory->invoice_number = $request->invoice_number;
        $inventory->waybill = $request->waybill;
        $inventory->grn_number = $request->grn_number;
        $inventory->billing_currency = $request->billing_currency;
        $inventory->trans_type = $request->trans_type;
        $inventory->po_number = $request->po_number;
        $inventory->delivered_by = $request->delivered_by;
        $inventory->date = $request->date;
        $inventory->edited_by = $auth;
        $inventory->exchange_rate = $request->exchange_rate;
        $inventory->manual_remarks = $request->manual_remarks;

        // Save the updated inventory
        $inventory->save();
        return redirect()->back()->withSuccess('Successfully Updated');

    } catch (\Exception $e) {
        // Generate a unique error ID for logging
        $unique_id = floor(time() - 999999999);
        
        // Log the error with the exception message and stack trace
        Log::channel('error_log')->error('InventoryController | Update() Error ' . $unique_id, [
            'message' => $e->getMessage(),
            'stack_trace' => $e->getTraceAsString(),
            'user' => Auth::user(),
            'request_data' => $request->all(),
            'inventory_id' => $id,
        ]);

        // Redirect back with the error message
        return redirect()->back()->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
    }
}


    public function update_inventory_item(Request $request, $id)
    {
        try {
            $request->validate([
                'quantity' => 'gte:0',
            ]);
            $auth = Auth::user()->id;
            $inventory = InventoryItem::find($id);
            $invid = InventoryItem::where('id', '=', $id)->value('inventory_id');
            $latest_date = Carbon::now();

            Log::info('InventoryController| Update Inventory Item', [
                'user_details' => Auth::user(),
                'before_InventoryItemDetail_edit' => InventoryItem::find($id),
            ]);

            $sum = $request->quantity * $request->unit_cost_exc_vat_gh;

            $newdiscount =  (($request->discount / 100) * $sum);
            $newamount = $sum - $newdiscount;

            $inventory->description = $request->description;
            $inventory->uom = $request->uom;
            $inventory->part_number = $request->part_number;
            $inventory->stock_code = $request->stock_code;
            $inventory->quantity = $request->quantity;
            $inventory->unit_cost_exc_vat_gh = $request->unit_cost_exc_vat_gh;
            $inventory->amount = $newamount;
            $inventory->discount = $request->discount;
            $inventory->location_id = $request->location_id;
            $inventory->item_id = $request->item_id;
            Inventory::where('id', '=', $invid)->update(['updated_at' => $latest_date, 'edited_by' => $auth]);
            $inventory->save();

            Log::info(
                'edited an InventoryItemDetail',
                [
                    'user_name' => $auth,
                    'InventoryItemDetail_after' => $request->all(),
                    'new_amount' => $newamount,

                ]
            );
            return redirect()->back();
         
            // return back();
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | UpdateInventoryItem() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function bookUpdate(Request $request)
    {
        try {
            $bookData = InventoryItemDetail::find($request->book_id);
            dd($bookData);
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | bookUpdate() Error ' . $unique_id,[
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

            // Assuming $id is the ID of the inventory record you want to delete

            // Check if any of the item_ids exist in sorder_parts
            $conflictingItemIds = SorderPart::whereIn('item_id', function ($query) use ($id) {
                $query->select('item_id')
                    ->from('inventory_item_details')
                    ->where('inventory_id', $id);
            })->pluck('item_id');

            // If there are conflicting item_ids, prevent deletion
            if ($conflictingItemIds->isNotEmpty()) {
                // Handle the case where deletion is not allowed
                // For example, you can return a response indicating that deletion is not allowed
             
                return redirect()->back()->withError('Contact Admin' . $conflictingItemIds);

                // return response()->json(['error' => 'Cannot delete inventory record as it has associated item_ids in sorder_parts'], 422);
            }


            // If no conflicting item_ids are found, proceed with deletion
            $inventory = Inventory::find($id);
            $authId = Auth::user()->name;

            // Log the deletion of the inventory record
            Log::info(
                'ItemController| destroy() | deleted an Inventory',
                [
                    'user_name' => $authId,
                    'Inventory' => $inventory,
                ]
            );

            // Delete the inventory record
            $inventory->delete();

            // Delete associated inventory item details
            $inventoryItems = InventoryItemDetail::where('inventory_id', $id)->pluck('id');
            Log::info(
                'InventoryConroller| destory() | deleted InventoryItemDetails',
                [
                    'user_name' => $authId,
                    'InventoryItemDetails' => $inventoryItems,
                ]
            );
        
            return redirect()->back()->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | Destroy() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function selectCategory(Request $request)
    {
        try {
            $site_id = Auth::user()->site->id;
            $movies = Category::where('site_id', '=', $site_id)->get();

            if ($request->has('q')) {
                $search = $request->q;
                $movies = Category::select("id", "name")
                    ->where('name', 'LIKE', "%$search%")
                    ->where('site_id', '=', $site_id)
                    ->get();
            }
            return response()->json($movies);
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | SelectCategory() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function selectLocation(Request $request)
    {
        try {
            $site_id = Auth::user()->site->id;
            $movies = Location::where('site_id', '=', $site_id)->get();
            if ($request->has('q')) {
                $search = $request->q;
                $movies = Location::select("id", "name")->where('name', 'LIKE', "%$search%")->where('site_id', '=', $site_id)->get();
            }
            return response()->json($movies);
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | SelectLocation() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }



    public function selectDeliveredBy(Request $request)
    {
        try {
            $site_id = Auth::user()->site->id;
            $movies = User::where('site_id', '=', $site_id)->get();
            if ($request->has('q')) {
                $search = $request->q;
                $movies = User::select("id", "name")
                    ->where('name', 'LIKE', "%$search%")->orWhere('phone', 'LIKE', "%$search%")
                    ->where('site_id', '=', $site_id)
                    ->get();
            }
            return response()->json($movies);
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | SelectDeliveredBy() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public static function fetch_locations()
    {
        try {
            $site_id = Auth::user()->site->id;
            $products = Location::all();
            $output = '';
            foreach ($products as $product) {
                $output .= '<option value="' . $product->id . '">' . $product->name . '</option>';
            }
            Log:
            info('InventoryController| fetch_locations()| Locations loaded successfully');
            return $output;
            // return response()->json($products);
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | FetchLocations() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public static function getItem()
    {
        try {
            $items = Item::latest()->get();
            $output = '';
            foreach ($items as $item) {
                $output .= '<option value="' . $item->id . '">' . $item->item_description . ', ' . $item->item_stock_code . ', ' . $item->item_part_number . '</option>';
            }
            Log::info('InventoryController| getItem() | Items loaded succesffully');
            return $output;
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | getItem() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }


    public static function getLocation()
    {
        $site_id = Auth::user()->site->id;
        $items = Location::where('site_id', '=', $site_id)->get();
        $output = '';
        foreach ($items as $item) {
            $output .= '<option value="' . $item->id . '">' . $item->name . '</option>';
        }
        return $output;
    }


    public function inventory_action(Request $request)
    {
        try {
            if ($request->ajax()) {
                if ($request->action == 'edit') {
                    $data = array(

                        'quantity'        =>    $request->quantity,
                        'unit_cost_exc_vat_gh' =>    $request->unit_cost_exc_vat_gh,
                        'part_number' => $request->part_number,
                        'description' => $request->description,
                        'location_id' => $request->location_id,
                        'uom' => $request->uom,
                        'amount' => $request->amount,
                        'discount' => $request->discount,

                    );
                    Log::info('inventory action before edit', [
                        'data_before' => $data,
                    ]);
                    $details =    DB::table('inventory_items')
                        ->where('id', $request->id)
                        ->update($data);
                    $authId = Auth::user()->name;
                    Log::info('after details', [
                        'user_name' => $authId,
                        'data_after' => $details,
                    ]);
                    $quantity = InventoryItemDetail::where('id', '=', $request->id)->value('quantity');
                    $unit_price = InventoryItemDetail::where('id', '=', $request->id)->value('unit_cost_exc_vat_gh');
                    $discount = InventoryItemDetail::where('id', '=', $request->id)->value('discount');
                    $sub_total = $quantity * $unit_price;
                    $total_amount = (($sub_total) - ($sub_total * $discount) / 100);
                    Log::info('inventoryitemdetail before edit', [
                        'quantity' => $quantity,
                        'unit_price' => $unit_price,
                        'discount' => $discount,
                        'sub_total' => $sub_total,
                        'total_amount' => $total_amount,
                    ]);
                    $invitmdet =  InventoryItemDetail::where('id', $request->id)->update(['amount' => $total_amount]);
                    $authId = Auth::user()->name;
                    Log::info('after InventoryItemDetail details', [
                        'user_name' => $authId,
                        'InventoryItemDetail' => $invitmdet,
                    ]);
                    // $sum1.val(Number($sum.val()) - (Number($sum.val()) * Number($num3.val()) / 100));
                }
                $authId = Auth::user()->name;
                Log::info('inventoryitems before edit', [
                    'user_name' => $authId,
                    'data_before_delete' =>  DB::table('inventory_items')
                        ->where('id', $request->id),
                ]);

                if ($request->action == 'delete') {
                    DB::table('inventory_items')
                        ->where('id', $request->id)
                        ->delete();
                }

                return response()->json($request);
            }
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | InventoryAction() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }


    public function inventory_search(Request $request)
    {
        try {
            $site_id = Auth::user()->site->id;
            if ($request->search) {
                $inventories = InventoryItemDetail::join('items', 'items.id', '=', 'inventory_item_details.item_id')
                    // ->where('inventory_items.quantity', '>', '0')
                    ->where('items.item_description', 'like', "%" . $request->search . "%")->where('inventory_item_details.quantity', '>', '0')
                    ->orWhere('items.item_part_number', 'like', "%" . $request->search . "%")->where('inventory_item_details.quantity', '>', '0')
                    ->orWhere('items.item_stock_code', 'like', "%" . $request->search . "%")->where('inventory_item_details.quantity', '>', '0')
                    ->where('inventory_item_details.site_id', '=', $site_id)
                    ->latest('inventory_item_details.created_at')->paginate();
            } else $inventories = InventoryItemDetail::latest()->paginate(20);
            Log::info('InventoryController | inventory_search | search completed successfully');
            return view('inventories.index', compact('inventories'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | InventorySearch() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }


    public function inventory_home_search(Request $request)
    {
        try {
            $site_id = Auth::user()->site->id;
            if ($request->search) {
                $inventories = Inventory::
                    // ->where('inventory_items.quantity', '>', '0')
                    where('waybill', 'like', "%" . $request->search . "%")
                    ->orWhere('grn_number', 'like', "%" . $request->search . "%")
                    ->where('site_id', '=', $site_id)
                    ->latest()->paginate(20);
            } else  $inventories = Inventory::where('site_id', '=', $site_id)->latest()->paginate(20);

            Log::info('InventoryController | inventory_home_search()| Search completed successfully');

            return view('inventories.index', compact('inventories'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | InventoryHomeSearch() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function fetch_single_product(Request $request)
    {
        try {
            $product = DB::select('select item_stock_code,item_part_number from items where  id = "' . $request->id . '"');
            Log::info('InventoryController | inventory_home_search');
            return response()->json($product);
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | FetchSingleProduct() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function inventory_item_history()
    {
        try {
            $site_id = Auth::user()->site->id;
            $inventory_item_history = Inventory::join('inventory_item_details', 'inventories.id', '=', 'inventory_item_details.inventory_id')->where('inventory_item_details.site_id', '=', $site_id)->where('inventories.site_id', '=', $site_id)->latest('inventories.id')->paginate(20);
            Log::info('InventoryController | inventory_item_history() |  search succesfull');
            return view('inventories.history', compact('inventory_item_history'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | InventoryItemHistory() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }


    public function inventory_history_show(string $id)
    {
        try {
            $company = Company::first();

            $inventory_id = InventoryItemDetail::where('id', '=', $id)->value('inventory_id');
            $inventory = Inventory::where('id', '=', $inventory_id)->first();
            $inventories = InventoryItemDetail::where('inventory_id', '=', $inventory_id)->get();
            Log:
            info('InventoryController| inventory_history_show()', [
                'user_details' => Auth::user(),
                'inventory' => 'Inventory History shown successfully' . $inventory_id,
            ]);
            return view('inventories.history_show', compact('inventory', 'company', 'inventories'));
            // dd($inventory,$inventories);
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | InventoryHistoryShow() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }


    public function inventory_history_edit(string $id)
    {
        try {
            $site_id = Auth::user()->site->id;
            $endusers = Enduser::where('site_id', '=', $site_id)->get();
            $inventory_id = InventoryItemDetail::where('id', '=', $id)->value('inventory_id');
            $inventory = Inventory::where('id', '=', $inventory_id)->first();

            $inventory_items = InventoryItemDetail::where('inventory_id', '=', $inventory_id)->get();
            $locations = Location::where('site_id', '=', $site_id)->get();
            $suppliers = Supplier::all();
            $deliveries = User::where('site_id', '=', $site_id)->get();
            $categories = Category::where('site_id', '=', $site_id)->get();
            $items = Item::all();
            $selectedRole = InventoryItemDetail::first()->location_id;
            Log::info('InventoryController | inventory_history_edit()');
            return view('inventories.history_edit', compact('suppliers', 'deliveries', 'categories', 'locations', 'inventory', 'endusers', 'inventory_items', 'selectedRole', 'items'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | InventoryHistoryEdit() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }


    public function inventory_history_update(Request $request, string $id)
    {
        try {
            $inventory = Inventory::find($id);
            Log::info('InventoryController| inventory_history_update()', [
                'user_details' => Auth::user(),
                'before_invhistory_edit' => Inventory::find($id),
            ]);
            //    dd($inventory);
            $inventory->supplier_id = $request->supplier_id;
            $inventory->enduser_id = $request->enduser_id;
            $inventory->invoice_number = $request->invoice_number;
            $inventory->grn_number = $request->grn_number;
            $inventory->billing_currency = $request->billing_currency;
            $inventory->trans_type = $request->trans_type;
            $inventory->po_number = $request->po_number;
            $inventory->delivered_by = $request->delivered_by;
            $inventory->date = $request->date;
            $inventory->save();
            $authId = Auth::user()->name;
            Log::info(
                'InventoryController| inventory_history_update()',
                [
                    'user_name' => $authId,
                    'after_edited_details' => $request->all(),
                ]
            );

            return redirect()->back()->withSuccess('Successfully updated');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | InventoryHistoryUpdate() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function inventory_history_action(Request $request)
    {
        try {
            if ($request->ajax()) {
                if ($request->action == 'edit') {
                    $data = array(

                        'quantity' => $request->quantity,
                        'unit_cost_exc_vat_gh' =>    $request->unit_cost_exc_vat_gh,
                        'part_number' => $request->part_number,
                        'description' => $request->description,
                        'location_id' => $request->location_id,
                        'uom' => $request->uom,
                        'amount' => $request->amount,
                        'discount' => $request->discount,

                    );

                    DB::table('inventory_items')
                        ->where('id', $request->id)
                        ->update($data);
                    $quantity = InventoryItemDetail::where('id', '=', $request->id)->value('quantity');
                    $unit_price = InventoryItemDetail::where('id', '=', $request->id)->value('unit_cost_exc_vat_gh');
                    $discount = InventoryItemDetail::where('id', '=', $request->id)->value('discount');
                    $sub_total = $quantity * $unit_price;
                    $total_amount = (($sub_total) - ($sub_total * $discount) / 100);
                    InventoryItemDetail::where('id', $request->id)->update(['amount' => $total_amount]);
                    // $sum1.val(Number($sum.val()) - (Number($sum.val()) * Number($num3.val()) / 100));
                }
                if ($request->action == 'delete') {
                    DB::table('inventory_items')
                        ->where('id', $request->id)
                        ->delete();
                }

                return response()->json($request);
            }
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | InventoryHistoryAction() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }


    public function update_inventory_history(Request $request, $id)
    {
        try {
            $inventory = InventoryItemDetail::find($id);
            Log::info('InventoryController | update_inventory_history()', [
                'user_details' => Auth::user(),
                'inventory_before_update' => $inventory
            ]);

            $sum = $request->quantity * $request->unit_cost_exc_vat_gh;

            $newdiscount =  (($request->discount / 100) * $sum);
            $newamount = $sum - $newdiscount;

            $inventory->description = $request->description;
            $inventory->uom = $request->uom;
            $inventory->part_number = $request->part_number;
            $inventory->stock_code = $request->stock_code;
            $inventory->quantity = $request->quantity;
            $inventory->unit_cost_exc_vat_gh = $request->unit_cost_exc_vat_gh;
            $inventory->amount = $newamount;
            $inventory->discount = $request->discount;
            $inventory->location_id = $request->location_id;
            $inventory->item_id = $request->item_id;
            $inventory->save();

            Log::info('InventoryController | update_inventory_history()', [
                'user_details' => Auth::user(),
                'inventory' => $inventory,
                'message' => 'Inventory History updated succesfully'
            ]);
            return back();
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | UpdateInventoryHistory() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }


    public function inventory_history_search(Request $request)
    {
        try {
            $site_id = Auth::user()->site->id;
            if ($request->search) {
                $inventory_item_history = Inventory::join('inventory_item_details', 'inventories.id', '=', 'inventory_item_details.inventory_id')
                    ->join('items', 'inventory_item_details.item_id', '=', 'items.id')
                    ->join('endusers', 'inventories.enduser_id', '=', 'endusers.id')
                    ->where('endusers.asset_staff_id', 'like', "%" . $request->search . "%")
                    ->orWhere('items.item_description', 'like', "%" . $request->search . "%")
                    ->orWhere('items.item_part_number', 'like', "%" . $request->search . "%")
                    ->orWhere('items.item_stock_code', 'like', "%" . $request->search . "%")
                    ->orWhere('inventories.po_number', 'like', "%" . $request->search . "%")
                    ->where('inventory_item_details.site_id', '=', $site_id)
                    ->get(['inventories.*', 'items.*', 'inventory_item_details.*', 'inventory_item_details.amount as inv_amount', 'inventory_item_details.quantity as inv_quantity', 'inventory_item_details.created_at as inv_created_at']);
            } else $inventory_item_history = Inventory::join('inventory_item_details', 'inventories.id', '=', 'inventory_item_details.inventory_id')->where('inventory_item_details.site_id', '=', $site_id)->latest('inventories.id')->paginate(20);
            return view('inventories.search_history', compact('inventory_item_history'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | InventoryHistorySearch() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }


    public function inventory_history_date_search(Request $request)
    {
        try {
            $site_id = Auth::user()->site->id;
            $start_date = Carbon::parse(request()->start_date)->toDateString();
            $end_date = Carbon::parse(request()->end_date)->toDateString();
            if ($request->start_date && $request->end_date) {
                $inventory_item_history = Inventory::join('inventory_item_details', 'inventories.id', '=', 'inventory_item_details.inventory_id')
                    ->join('items', 'inventory_item_details.item_id', '=', 'items.id')
                    ->wheredate('inventory_item_details.created_at', '>=', $start_date)
                    ->wheredate('inventory_item_details.created_at', '<=', $end_date)
                    ->where('inventory_item_details.site_id', '=', $site_id)
                    ->get(['inventories.*', 'items.*', 'inventory_item_details.amount as inv_amount', 'inventory_item_details.quantity as inv_quantity', 'inventory_item_details.created_at as inv_created_at']);
                // dd($inventory_item_history);
            } else  $inventory_item_history = Inventory::join('inventory_item_details', 'inventories.id', '=', 'inventory_item_details.inventory_id')
                ->where('inventory_item_details.site_id', '=', $site_id)->latest('inventories.id')->paginate(20);
            // dd($inventory_item_history);
            Log::info('InventoryController | inventory_history_date_search()', [
                'user_details' => Auth::user(),
                'request_payload' =>  $request,
                'message' => 'inventory_history_date_search sucessful'
            ]);
            return view('inventories.search_history', compact('inventory_item_history'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | InventoryHistoryDateSearch() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function inventory_history_destroy($id)
    {
        try {
            $inventory_item_history = InventoryItemDetail::find($id);
            $inventory_item_history->delete();
            Log::info('InventoryController| inventory_history()', [
                'user_details' => Auth::user(),
                'message' => 'inventory_history_destroy sucessfully',
                '$inventory' => $inventory_item_history
            ]);
         
            return back()->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | InventoryHistoryDestroy() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function generateinventoryPDF($id)
    {
        try {
            $site_id = Auth::user()->site->id;
            $company = Company::latest()->first();
            $pdf_filename = Inventory::where('id', '=', $id)->value('grn_number'); // Get the value directly
            $inventory = Inventory::where('id', '=', $id)->first();
            $inventories = InventoryItemDetail::where('site_id', '=', $site_id)->where('inventory_id', '=', $id)->get();
            $inventory = PDF::loadView('inventories.pdf', compact('inventory', 'company', 'inventories'))->setOptions(['defaultFont' => 'sans-serif']);
            Log::info("InventoryController | generateinventoryPDF() | ");
            $filename = $pdf_filename . '.pdf'; // Append '.pdf' to the filename

            return $inventory->download($filename);
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | GenerateInventoryPDF() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function out_of_stock()
    {
        try {
            $site_id = Auth::user()->site->id;
            // $unstocked = Item::where('stock_quantity', '=', '0')->get();
            $unstocked = InventoryItem::join('inventories', 'inventory_items.inventory_id', '=', 'inventories.id')
                ->join('items', 'inventory_items.item_id', '=', 'items.id')
                ->where('quantity', '=', '0')->where('inventories.trans_type', '=', 'Stock Purchase')
                ->where('inventory_items.site_id', '=', $site_id)
                ->groupby('inventory_items.item_id')->get();
            Log::info("InventoryController| out_of_stock() | ", [
                'user_details' => Auth::user(),
                'unstocked' => $unstocked,
                'message' => 'Out of stock details succesfully.'
            ]);
            // dd($unstocked);
            return view('inventories.unstocked', compact('unstocked'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | OutOfStock() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }


    public function out_of_stock_search(Request $request)
    {
        try {
            $site_id = Auth::user()->site->id;
            if ($request->search) {
                $unstocked = InventoryItem::join('items', 'inventory_items.item_id', '=', 'items.id')
                    ->join('inventories', 'inventory_items.inventory_id', '=', 'inventories.id')
                    ->where('items.item_description', 'like', "%" . $request->search . "%")->where('inventory_items.quantity', '=', '0')
                    ->orWhere('items.item_part_number', 'like', "%" . $request->search . "%")->where('inventory_items.quantity', '=', '0')
                    ->orWhere('items.item_stock_code', 'like', "%" . $request->search . "%")->where('inventory_items.quantity', '=', '0')
                    ->where('inventory_items.site_id', '=', $site_id)
                    ->groupBy('inventory_items.item_id')
                    ->latest('items.created_at')->paginate();
            } else $unstocked = InventoryItemDetail::where('site_id', $site_id)->latest()->paginate(20);
            Log::info('InventoryController |  out_of_stock_search()', [
                'user_details' => Auth::user(),
                'request_details' => $request,
                'response' => $unstocked
            ]);
            return view('inventories.unstocked', compact('unstocked'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('InventoryController | OutOfStockSearch() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function check_waybill(Request $request)
    {
        $waybill = $request->input('waybill');
        $isExists = Inventory::where('waybill', $waybill)->first();
        if ($isExists) {
            return response()->json(array("exists" => true));
        } else {
            return response()->json(array("exists" => false));
        }
    }
    public function check_po_number(Request $request)
    {
        $po_number = $request->input('po_number');
        $isExists = Inventory::where('po_number', $po_number)->first();
        if ($isExists) {
            return response()->json(array("exists" => true));
        } else {
            return response()->json(array("exists" => false));
        }
    }
    public function check_invoice_number(Request $request)
    {
        $invoice_number = $request->input('invoice_number');
        $isExists = Inventory::where('invoice_number', $invoice_number)->first();
        if ($isExists) {
            return response()->json(array("exists" => true));
        } else {
            return response()->json(array("exists" => false));
        }
    }
}
