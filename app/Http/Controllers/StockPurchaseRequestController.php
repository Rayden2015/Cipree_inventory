<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tax;
use App\Helpers\Pay;
use App\Models\Item;
use App\Models\Levy;
use App\Models\Part;
use App\Models\Site;
use App\Models\Company;
use App\Models\Enduser;
use App\Models\Location;
use App\Models\Supplier;
use App\Models\SprPorder;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\SprPorderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\StockPurchaseRequest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Models\StockPurchaseRequestItem;

class StockPurchaseRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function spr_lists()
    {
        try {
            $site_id = Auth::user()->site->id;
            $auth = Auth::id();
            $spr_lists = StockPurchaseRequest::where('site_id', '=', $site_id)->latest()->paginate(15);
            Log::info("StockPurchaseReqquestController | spr_lists() ", [
                'user_details' => Auth::user(),
                'response_payload' => $spr_lists
            ]);
            return view('stockpurchases.spr_lists', compact('spr_lists'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | SprLists() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }
    public function index(Request $request)
    {
        try {
            Log::info('StockPurchasesRequestController | request_search() | ', [
                'user_details' => Auth::user(),
                'request_payload' => $request
            ]);
            $site_id = Auth::user()->site->id;
            if ($request->search) {
                $inventory = Item::join('inventory_items', 'items.id', '=', 'inventory_items.item_id')
                    // ->join('inventories','inventories.id','=', 'inventory_items.inventory_id')
                    //  ->join('endusers','endusers.id','=', 'inventories.enduser_id')
                    ->where('items.item_description', 'like', "%" . $request->search . "%")->where('inventory_items.quantity', '>=', '0')
                    ->orWhere('items.item_part_number', 'like', "%" . $request->search . "%")->where('inventory_items.quantity', '>=', '0')
                    ->orWhere('items.item_stock_code', 'like', "%" . $request->search . "%")->where('inventory_items.quantity', '>=', '0')
                    ->where('inventory_items.site_id', '=', $site_id)
                    ->get();

                Log::info('StockPurchasesRequestController | request_search() | ', [
                    'user_details' => Auth::user(),
                    'response' => $inventory
                ]);


                if ($inventory->isEmpty()) {
                    // Toastr::error('Item not in stock:)', 'Oops');
                    return redirect()->back()->withError('Item not in stock', 'Oops');
                } elseif ($inventory->isNotEmpty()) {
                    return view('stockpurchases.request_search', compact('inventory'));
                }
            } else  $inventory = Item::join('inventory_items', 'items.id', '=', 'inventory_items.item_id')
                // ->join('inventories','inventories.id','=', 'inventory_items.inventory_id')
                // ->join('endusers','endusers.id','=', 'inventories.enduser_id')
                ->where('inventory_items.site_id', '=', $site_id)
                ->get();

            return view('stockpurchases.request_search', compact('inventory'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | Index() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }


    public function stock_purchase_cart()
    {
        try {
            $site_id = Auth::user()->site->id;
            $customers = Supplier::all();
            // $products = InventoryItem::all();
            $request_number = Pay::genRefSprCode();
            $request_date = Carbon::now()->toDateTimeString();
            // return view('cart', compact('request_number', 'request_date', 'products', 'customers'));
            // dd(session()->get('cart'));
            if (count((array) session('cart')) == '0') {
                //    dd($customers);
                return redirect()->route('spr_create');
            } elseif (count((array) session('cart')) > '0') {
                Log::info('StockPurchasesRequestController | cart() | ', [
                    'user_details' => Auth::user(),
                    'response payload' => session('cart')
                ]);
                return view('stockpurchases.cart', compact('request_number', 'request_date', 'customers'));
            }
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | StockPurchaseCart() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function addToStock($id)
    {
        try {
            $product = Item::join('inventory_items', 'items.id', '=', 'inventory_items.item_id')->where('inventory_items.id', $id)->first();
            // $product = DB::table('items')->join('inventory_items','items.id','=','inventory_items.item_id')->find('items.id',$id)->select('items.id,items.description as item_description,items.stock_code as item_code');

            $cart = session()->get('cart', []);

            if (isset($cart[$id])) {
                $cart[$id]['quantity'];
            } else {
                $cart[$id] = [
                    "id" => $product->id,
                    'item_id' => $product->item_id,

                    "item_description" => $product->item_description,
                    "item_uom" => $product->item_uom,
                    "item_part_number" => $product->item_part_number,
                    "item_stock_code" => $product->item_stock_code,
                    "unit_cost_exc_vat_gh" => $product->unit_cost_exc_vat_gh,
                    // "location_id" => $product->location_id,
                    // "quantity" => 1,
                    "price" => $product->price,
                    "image" => $product->image
                ];
            }

            Log::info('StockPurchasesRequestController | addToStock() | ', [
                'user_details' => Auth::user(),
                'response_payload' => $cart
            ]);

            session()->put('cart', $cart);

            return redirect()->back()->with('success', 'Item added to cart successfully!');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | AddToStock() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function update(Request $request)
    {
        try {
            // $products = Item::join('inventory_items','items.id','=','inventory_items.item_id')->value('quantity');
            $products = InventoryItem::where('id', '=', $request->id)->value('quantity');
            if ($request->id && $request->quantity) {
                $cart = session()->get('cart');
                $cart[$request->id]["quantity"] = $request->quantity;
                if ($request->id && $request->quantity > $products) {
                    return  session()->flash('errors', 'Out of quantity');
                }
                session()->put('cart', $cart);
                Log::info('StockPurchasesRequestController | update()', [
                    'user_details' => Auth::user(),
                    'message' =>  'Cart updated successfully',
                    'cart' => $cart
                ]);
                session()->flash('success', 'Cart updated successfully');
            }
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | Update() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }
    public function remove(Request $request)
    {
        try {
            if ($request->id) {
                $cart = session()->get('cart');
                if (isset($cart[$request->id])) {
                    unset($cart[$request->id]);
                    session()->put('cart', $cart);
                }
                Log::info('StockPurchasesReqeustController() | remove()', [
                    'user_details' => Auth::user(),
                    'request_payload' => $request,
                    'message' => 'Product removed successfully '
                ]);
                session()->flash('success', 'Product removed successfully');
            }
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | Remove() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }
    public function store(Request $request)
    {
        try {
            $request->validate([
                'photo' => 'sometimes|nullable|image|mimes:jpeg,gif,png,jpg|max:9048',
                'desc' => 'nullable',
            ]);

            $authid = Auth::id();
            $statusvalue = 'Requested';
            $site_id = Auth::user()->site->id;
            $cart = Session()->get('cart');
            $order = new StockPurchaseRequest();
            // $order->cart = serialize($cartItems);
            $order->tax   = $request->tax;
            $order->tax2    = $request->tax2;
            $order->tax3    = $request->tax3;
            $order->currency    = $request->currency;
            $order->supplier_id = $request->supplier_id;
            $order->type_of_purchase = $request->type_of_purchase;
            $order->enduser_id = $request->enduser_id;
            $order->status = $statusvalue;
            $order->request_date = $request->request_date;
            $order->user_id = $authid;
            $order->request_number = $request->request_number;
            $order->requested_by = $authid;
            $order->site_id = $site_id;

            $order->save();
            Log::info("StockPurchasesRequestController() | store() ", [
                'user_details' => Auth::user(),
                'Added_By' => $authid,
                'Details' => $request->all(),
                'status_value' => $statusvalue,
            ]);

            $carts = session()->get('cart');

            $quantity = $request->input('quantity');

            foreach ($carts as $item) {
                $orderItem = new StockPurchaseRequestItem();
                $orderItem->spr_id = $order->id;
                // $orderItem->quantity = $item['quantity'];
                $orderItem->item_id = $item['item_id'];
                $orderItem->unit_price = $item['unit_cost_exc_vat_gh'];
                $orderItem->inventory_id = $item['id'];
                // $orderItem->sub_total =  $item['unit_cost_exc_vat_gh'] *  $item['quantity'];

                // Check if serial number exists for this item ID

                if (isset($quantity[$item['id']])) {
                    $orderItem->quantity = $quantity[$item['id']];
                    $orderItem->sub_total =  $item['unit_cost_exc_vat_gh'] * $quantity[$item['id']];
                }

                $orderItem->save();
            }


            Log::info("StockPurchasesRequestController | store_items()", [
                'user_details' => Auth::user(),
                'Added_by' => $authid,
                'Order Details' => $orderItem,
            ]);

            session()->forget('cart');


            $orderUserId = StockPurchaseRequest::latest()->value('user_id');
            $title = 'You have a new request from';
            Notification::create([
                'title' => $title,
                'user_id' => $orderUserId,

            ]);

            return redirect()->route('spr_create')->withSuccess('Stock Purchase Request(SPR) #' . $order->request_number . ' forwarded for Authorisation');
          
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | Store() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function store_officer_spr_edit(Request $request, $id)
    {
        try {
            $site_id = Auth::user()->site->id;
            $request->validate([]);
            $suppliers = Supplier::all();
            $sorder = StockPurchaseRequest::find($id);
            $endusers = Enduser::where('site_id', '=', $site_id)->get();
            $sorder_parts = StockPurchaseRequestItem::where('sorder_id', '=', $id)->get();
            Log::info('StockPurchaseRequestController | store_officer_spr_edit()', [
                'user_details' => Auth::user(),
                'response_payload' => $sorder_parts
            ]);
            return view('stockpurchases.so_spr_edit', compact('sorder_parts', 'sorder', 'suppliers', 'endusers'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | StoreOfficerSprEdit() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }
    public function store_officer_spr_update(Request $request, $id)
    {
        $purchase = StockPurchaseRequest::find($id);
        $authid = Auth::user()->name;
        Log::info("StockPurchaseRequestController | store_officer_spr_update() | Spr before edit", [
            'user_details' => Auth::user(),
            'response_payload' => $purchase
        ]);
        $authid = Auth::id();
        $date = Carbon::now();
        $request->validate([
            'supplier_id' => 'nullable',
            'type_of_purchase' => 'nullable',
            'quantity' => 'gte:0',
        ]);

        $deliverynum = StockPurchaseRequest::where('id', '=', $id)->value('request_number');
        //   
        try {
            DB::beginTransaction();

            $delivered_on_date = Carbon::now()->toDateTimeString();

            // if ($request->status == 'Supplied' || $request->status == 'Partially Supplied') {

            // dd($data, $sorder_id);
            $genDeliveryNum = $deliverynum;
            $purchase->tax = $request->tax; //10
            $purchase->tax2 = $request->tax2; //11
            $purchase->tax3 = $request->tax3; //12
            $purchase->supplier_id = $request->supplier_id; //1
            $purchase->type_of_purchase = $request->type_of_purchase; //3
            $purchase->enduser_id = $request->enduser_id; //4
            $purchase->status = 'Supplied';
            $purchase->delivery_reference_number = $genDeliveryNum;
            $purchase->invoice_number = $request->invoice_number;
            $purchase->delivered_by = $authid;
            $purchase->user_id = $authid;
            $purchase->delivered_on = $delivered_on_date;
            $purchase->edited_at = '1';
            $purchase->supplied_to = $request->supplied_to;

            $purchase->save();
            Log::info('StockPurchaseRequestController | store_officer_spr_update() | Spr Details', [
                'Details' => $request->all(),
                'genDeliveryNum' => $deliverynum,
                'status' => $purchase->status,
                'delivered_by' => $purchase->delivered_by = $authid,
                'delivered_on' =>  $purchase->delivered_on = $delivered_on_date,
                'purchase_edited_at' =>   $purchase->edited_at = '1',
            ]);

            $sorder_id = StockPurchaseRequestItem::where('sorder_id', $id)->pluck('inventory_id')->toArray();
            $quantity = StockPurchaseRequestItem::where('sorder_id', $id)->pluck('qty_supplied')->toArray();

            $data['items'] = DB::table('inventory_items')
                ->select('inventory_items.*')
                ->join('stock_purchase_request_items', 'inventory_items.id', '=', 'stock_purchase_request_items.inventory_id')
                ->where('stock_purchase_request_items.sorder_id', '=', $id)
                ->selectRaw('inventory_items.id, inventory_items.quantity - stock_purchase_request_items.qty_supplied AS new_quantity')
                ->get();
            Log::info("Spr Data Items Before Edit", [
                'DataItems' => $data['items'],
            ]);

            foreach ($data['items'] as $product_item) {
                $r1 =  InventoryItem::updateOrCreate(
                    ['id' => $product_item->id], // Use $product_item->id instead of $product_item['items']['id']
                    ['quantity' => $product_item->new_quantity] // Use $product_item->new_quantity instead of $product_item['quantity']['new_quantity']
                );
                if ($r1->wasRecentlyCreated) {
                    Log::info("Itemsa which was newly created", [
                        'Details' => $r1
                    ]);
                    $r1->delete();
                }
            }
            $data2['items'] = DB::table('inventory_items')
                ->select('inventory_items.*')
                ->join('stock_purchase_request_items', 'inventory_items.id', '=', 'stock_purchase_request_items.inventory_id')
                ->where('stock_purchase_request_items.sorder_id', '=', $id)
                ->selectRaw('inventory_items.id, inventory_items.quantity * inventory_items.unit_cost_exc_vat_gh AS new_amount')
                ->get();
            foreach ($data2['items'] as $product_itemb) {
                $r2 =    InventoryItem::updateOrCreate(
                    ['id' => $product_itemb->id], // Use $product_item->id instead of $product_item['items']['id']
                    ['amount' => $product_itemb->new_amount] // Use $product_item->new_quantity instead of $product_item['quantity']['new_quantity']
                );
                if ($r2->wasRecentlyCreated) {
                    Log::info("Itemsa which was newly created", [
                        'Details' => $r2
                    ]);
                    $r2->delete();
                }
            }

            DB::commit();
            DB::select('UPDATE items i
            JOIN (
                SELECT t.item_id, SUM(t.quantity) AS calculated_quantity
                FROM items i
                JOIN inventory_items t ON i.id = t.item_id
                GROUP BY t.item_id
            ) AS subquery ON i.id = subquery.item_id
            SET i.stock_quantity = subquery.calculated_quantity;');
         
            return redirect()->back()->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | StoreOfficerSprLists() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}
    }
           

    

public function authoriser_remarks_update(Request $request, $id)
{
    try {
        // Find the StockPurchaseRequest by ID
        $spr = StockPurchaseRequest::find($id);

        if (!$spr) {
            return redirect()->back()->withError('Stock Purchase Request not found.');
        }

        // Update the authoriser remarks
        $spr->authoriser_remarks = $request->authoriser_remarks;
        $spr->save();

        // Display a success message
       
        return redirect()->back()->withSuccess('Successfully Updated');
    } catch (\Exception $e) {
        // Generate a unique error ID
        $unique_id = floor(time() - 999999999);
        
        // Log the error with details
        Log::channel('error_log')->error('AuthoriserController | authoriser_remarks_update() Error ' . $unique_id ,[
            'message' => $e->getMessage(),
            'stack_trace' => $e->getTraceAsString()
        ]);

        // Redirect back with the error message
        return redirect()->back()
                         ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
    }
}


    public function so_spr_update(Request $request, $id)
    {
        try {
            $request->validate([
                'qty_supplied' => 'gte:0',
            ]);

            $sorder = StockPurchaseRequestItem::find($id);
            $authid =  Auth::user()->name;
            Log::info("stock purchase requests item before edit", [
                'Edited_By' => $authid,
                'Details' => StockPurchaseRequestItem::find($id),
            ]);

            $inventory_id = StockPurchaseRequestItem::where('id', '=', $id)->value('inventory_id');
            $quantity = InventoryItem::where('id', '=', $inventory_id)->value('quantity');
            $sorder_quantity =  StockPurchaseRequestItem::where('id', '=', $id)->value('quantity');

            if ($request->qty_supplied > $quantity || $request->qty_supplied > $sorder_quantity) {
                Log::info('Quantity before', [
                    'Edited_by' => $quantity,
                    'Details' => $request->all(),
                ]);
                return back()->with('message', 'Quantity not available!');
            } else {

                $sorder->qty_supplied = $request->qty_supplied;
                // $sorder->remarks = $request->remarks;
                if ($sorder->qty_supplied >= $request->quantity) {
                    $sorder->remarks = 'Fully Supplied';
                } else if ($sorder->qty_supplied == '0') {
                    $sorder->remarks = 'Not Supplied';
                } else {
                    $sorder->remarks = 'Partially Supplied';
                }

                $sorder->save();

                if ($sorder->qty_supplied < 1) {
                    $quantity = StockPurchaseRequestItem::where('id', '=', $request->id)->value('qty_supplied');
                    $unit_price = StockPurchaseRequestItem::where('id', '=', $request->id)->value('unit_price');
                    $sub_total = $quantity * $unit_price;
                    StockPurchaseRequestItem::where('id', $request->id)->update(['sub_total' => $sub_total]);
                } else {
                    $qty_supplied = StockPurchaseRequestItem::where('id', '=', $request->id)->value('qty_supplied');
                    $unit_price = StockPurchaseRequestItem::where('id', '=', $request->id)->value('unit_price');
                    $sub_total = $qty_supplied * $unit_price;
                    StockPurchaseRequestItem::where('id', $request->id)->update(['sub_total' => $sub_total]);
                }

                return redirect()->back();
            }
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | SoSprUpdate() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function auth_spr_lists()
{
    try {
        $auth = Auth::id();
        $site_id = Auth::user()->site->id;
        $department_id = Auth::user()->department->id ?? null;

        if(Auth::user()->hasRole('Department Authoriser')) {
            if ($department_id === null) {
                // User doesn't have a department assigned, return empty results
                $spr_lists = StockPurchaseRequest::where('id', null)->paginate(15);
            } else {
                $spr_lists = StockPurchaseRequest::join('users', 'users.id', '=', 'stock_purchase_requests.user_id')
                    ->where('stock_purchase_requests.site_id', '=', $site_id)
                    ->latest('stock_purchase_requests.created_at') // Specify the table name here
                    ->select('stock_purchase_requests.*')
                    ->paginate(15);
            }

            Log::info("StockPurchaseRequestController | spr_lists() ", [
                'user_details' => Auth::user(),
                'response_payload' => $spr_lists
            ]);

            return view('stockpurchases.auth_spr_lists', compact('spr_lists'));
        }

        $spr_lists = StockPurchaseRequest::where('site_id', '=', $site_id)
            ->latest('created_at')
            ->paginate(15);

        Log::info("StockPurchaseRequestController | spr_lists() ", [
            'user_details' => Auth::user(),
            'response_payload' => $spr_lists
        ]);

        return view('stockpurchases.auth_spr_lists', compact('spr_lists'));
    } catch (\Exception $e) {
        $unique_id = floor(time() - 999999999);
        Log::channel('error_log')->error('StockPurchaseRequestController | AuthSprLists() Error ' . $unique_id, [
            'message' => $e->getMessage(),
            'stack_trace' => $e->getTraceAsString()
        ]);

        return redirect()->back()
            ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
    }
}

    public function auth_spr_list_edit($id)
    {
        try {
            $site_id = Auth::user()->site->id;
            $sorder = StockPurchaseRequest::find($id);
            $sorder_parts = StockPurchaseRequestItem::where('spr_id', '=', $id)->get();
            $suppliers = Supplier::all();
            $endusers = Enduser::where('site_id', '=', $site_id)->get();
            Log::info('StockPurchaseRequestController| auth_spr_list_edit() | ', [
                'user_details' => Auth::user()
            ]);
            return view('stockpurchases.auth_spr_list_edit', compact('sorder', 'suppliers', 'endusers', 'sorder_parts'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | AuthSprListEdit() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }
    public function auth_spr_action(Request $request)
    {
        try {
            if ($request->ajax()) {
                if ($request->action == 'edit') {
                    $data = array(

                        'quantity'        =>    $request->quantity,
                        'unit_price'        =>    $request->unit_price,
                        'remarks'  =>    $request->remarks,
                    );
                    Log::info('stockpurchaserequest Action Details', [
                        'Details' => $data,
                    ]);
                    $item_id = StockPurchaseRequestItem::where('id', '=', $request->id)->value('item_id');
                    $quantity = InventoryItem::where('item_id', '=', $item_id)->value('quantity');
                    if ($request->quantity > $quantity) {
                        Log::info('stockpurchaserequest Action Details', [
                            'Details' => $item_id,
                            'quantity' => $quantity,
                            'details 2' => $request->quantity
                        ]);
                        return back()->with('message', 'Quantity not available!');
                      
                    } else
                        $val =     DB::table('stock_purchase_request_items')
                            ->where('id', $request->id)
                            ->update($data);
                    $authid = Auth::user()->name;
                    Log::info('stockpurchaserequest after Action Details', [
                        'Edited By' => $authid,
                        'Details' => $val,
                    ]);

                    $quantity = StockPurchaseRequestItem::where('id', '=', $request->id)->value('quantity');
                    $unit_price = StockPurchaseRequestItem::where('id', '=', $request->id)->value('unit_price');
                    $sub_total = $quantity * $unit_price;
                    $val1 =  StockPurchaseRequestItem::where('id', $request->id)->update(['sub_total' => $sub_total]);
                    Log::info('store request Updated Values', [
                        'Edited By' => $authid,
                        'Details' => $val1,
                    ]);
                }
                if ($request->action == 'delete') {
                    DB::table('stock_purchase_request_items')
                        ->where('id', $request->id)
                        ->delete();
                }

                return response()->json($request);
            }
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | AuthSprAction() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }
    public function auth_spr_list_view($id)
    {
        $sorder = StockPurchaseRequest::find($id);
        $company = Company::first();
        $sorder_parts = StockPurchaseRequestItem::where('spr_id', '=', $id)->get();
        return view('stockpurchases.auth_view', compact('sorder', 'sorder_parts', 'company'));

        // dd($sorder);
    }

    public function auth_spr_approved_status($id)
    {
        try {
            $authid = Auth::id();
            $date = Carbon::now()->toDateTimeString();
            $order = StockPurchaseRequest::find($id);
            $order = StockPurchaseRequest::where('id', '=', $id)->update(['approval_status' => 'Approved', 'approved_by' => $authid, 'approved_on' => $date]);
            Log::info('StockPurchaseRequestController | auth_spr_approved_status', [
                'user_details' => Auth::user(),
                'request_payload' => $order
            ]);
            return redirect()->back();
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | AuthSprApprovedStatus() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function auth_spr_denied_status($id)
    {
        try {
            $authid = Auth::id();
            $date = Carbon::now();
            $order = StockPurchaseRequest::find($id);
            $order = StockPurchaseRequest::where('id', '=', $id)->update(['approval_status' => 'Denied', 'approved_by' => $authid, 'approved_on' => $date]);
            Log::info('StockPurchaseRequestController | auth_spr_denied_status', [
                'user_details' => Auth::user(),
                'request_payload' => $order
            ]);
            return redirect()->back();
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | AuthSprDeniedStatus() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function po_spr_lists()
    {
        try {
            $auth = Auth::id();
            $site_id = Auth::user()->site->id;
            $spr_lists = StockPurchaseRequest::where('site_id', '=', $site_id)->latest()->paginate(15);
            Log::info("StockPurchaseReqquestController | spr_lists() ", [
                'user_details' => Auth::user(),
                'response_payload' => $spr_lists
            ]);
            return view('stockpurchases.po_spr_lists', compact('spr_lists'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | PoSprLists() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }
    public function po_spr_list_edit($id)
    {
        try {
            $site_id = Auth::user()->site->id;
            $sorder = StockPurchaseRequest::find($id);
            $sorder_parts = StockPurchaseRequestItem::where('sorder_id', '=', $id)->get();
            $suppliers = Supplier::all();
            $endusers = Enduser::where('site_id', '=', $site_id)->get();
            Log::info('StockPurchaseRequestController| auth_spr_list_edit() | ', [
                'user_details' => Auth::user()
            ]);
            return view('stockpurchases.po_spr_list_edit', compact('sorder', 'suppliers', 'endusers', 'sorder_parts'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | PoSprListEdit() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }
    public function generate_spr_porder($id)
    {
        try {
            $id = (int) $id;
            $authid = Auth::user()->name;

            $purchase_order = StockPurchaseRequest::find($id);
            // $sprit = ;
            $new_purchase_order = $purchase_order->replicate();
            $new_purchase_order->setTable('spr_porders');
            $new_purchase_order->spr_id = $id;
            $new_purchase_order->order_id = $id;
            $new_purchase_order->created_by = $authid;
            $purchase_order_number = Pay::genSprPurchaseCode();
            $new_purchase_order->purchasing_order_number = $purchase_order_number;
            $new_purchase_order->save();
            // dd($sprit);

            $fks = StockPurchaseRequestItem::where('spr_id', '=', $id)->get();
            foreach ($fks as $fk) {
                $orderpart = $fk->replicate();
                $orderpart->setTable('spr_porder_items');
                $orderpart->purchasing_order_number = $purchase_order_number;
                $orderpart->sorder_id = $id;
                // $orderpart->spr_id = $sprit;
                $orderpart->save();
            }
            $purchase_order = SprPorder::latest()->value('id');
            return redirect()->route('spr_purchase_order_draft', $purchase_order)->withSuccess('Successfully Updated');

            // dd($id);
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | GenerateSprPorder() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }
    public function spr_purchase_order_draft($id)
    {
        try {
            $id = (int) $id;
            $purchase_order = SprPorder::find($id);
            $order_id = SprPorder::where('id', '=', $id)->value('spr_id');
            $purchase_order_no = SprPorder::where('id', '=', $id)->latest()->value('purchasing_order_number');

            $suppliers = Supplier::all();
            $endusers = Enduser::all();
            $order_parts = SprPorderItem::where('purchasing_order_number', '=', $purchase_order_no)->get();
            $grandtotal = SprPorderItem::where('id', '=', $id)->where('purchasing_order_number', '=', $purchase_order_no)->sum('sub_total');
            $date_created = Carbon::now()->toDateTimeString();
            $amount_to_words = $this->numberToWord($grandtotal);
            $taxes = Tax::all();
            $levies = Levy::all();
            return view('stockpurchases.purchase_order_draft', compact('purchase_order', 'suppliers', 'endusers', 'order_parts', 'grandtotal', 'date_created', 'amount_to_words', 'taxes', 'levies'));
            // dd($id,$purchase_order);
            // dd($id,$purchase_order_no,$order_parts);
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | SprPurchaseOrderDraft() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }
    public function numberToWord($num = '')
    {
        $num    = (string) ((int) $num);

        if ((int) ($num) && ctype_digit($num)) {
            $words  = array();

            $num    = str_replace(array(',', ' '), '', trim($num));

            $list1  = array(
                '', 'one', 'two', 'three', 'four', 'five', 'six', 'seven',
                'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen',
                'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
            );

            $list2  = array(
                '', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty',
                'seventy', 'eighty', 'ninety', 'hundred'
            );

            $list3  = array(
                '', 'thousand', 'million', 'billion', 'trillion',
                'quadrillion', 'quintillion', 'sextillion', 'septillion',
                'octillion', 'nonillion', 'decillion', 'undecillion',
                'duodecillion', 'tredecillion', 'quattuordecillion',
                'quindecillion', 'sexdecillion', 'septendecillion',
                'octodecillion', 'novemdecillion', 'vigintillion'
            );

            $num_length = strlen($num);
            $levels = (int) (($num_length + 2) / 3);
            $max_length = $levels * 3;
            $num    = substr('00' . $num, -$max_length);
            $num_levels = str_split($num, 3);

            foreach ($num_levels as $num_part) {
                $levels--;
                $hundreds   = (int) ($num_part / 100);
                $hundreds   = ($hundreds ? ' ' . $list1[$hundreds] . ' Hundred' . ($hundreds == 1 ? '' : '') . ' ' : '');
                $tens       = (int) ($num_part % 100);
                $singles    = '';

                if ($tens < 20) {
                    $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '');
                } else {
                    $tens = (int) ($tens / 10);
                    $tens = ' ' . $list2[$tens] . ' ';
                    $singles = (int) ($num_part % 10);
                    $singles = ' ' . $list1[$singles] . ' ';
                }
                $words[] = $hundreds . $tens . $singles . (($levels && (int) ($num_part)) ? ' ' . $list3[$levels] . ' ' : '');
            }
            $commas = count($words);
            if ($commas > 1) {
                $commas = $commas - 1;
            }

            $words  = implode(', ', $words);

            $words  = trim(str_replace(' ,', ',', ucwords($words)), ', ');
            if ($commas) {
                $words  = str_replace(',', ' and', $words);
            }

            return $words;
        } else if (!((int) $num)) {
            return 'Zero';
        }
        return '';
    }
    public function spr_purchase_update(Request $request, $id)
    {
        try {
            $id = (int) $id;
            $purchase = SprPorder::find($id);
            Log::info(
                'StockPurchaseRequestController| spr_purchase_update() | Before Spr Puchase Update',
                [
                    'user_details' => $purchase,
                    'response_payload' => SprPorder::find($id)
                ]
            );
            $authid = Auth::id();
            $request->validate([
                'supplier_id' => 'nullable',
                'type_of_purchase' => 'nullable'
            ]);
            $purchase->part_id = $request->part_id; //2

            $purchase->tax = $request->tax; //10
            $purchase->tax2 = $request->tax2; //11
            $purchase->tax3 = $request->tax3; //12
            $purchase->supplier_id = $request->supplier_id; //1
            $purchase->type_of_purchase = $request->type_of_purchase; //3
            $purchase->enduser_id = $request->enduser_id; //4
            $purchase->status = $request->status;
            $purchase->delivery_reference_number = $request->delivery_reference_number;
            // $purchase->po_number = Pay::genSprPurchaseCode();
            // $purchase->suppliers_reference = $request->suppliers_reference;
            // $purchase->site_id = $request->site_id;
            // $purchase->date_created = $request->date_created;
            // $purchase->deliver_to = $request->deliver_to;
            // $purchase->created_by = $request->created_by;

            $purchase->invoice_number = $request->invoice_number;
            $purchase->notes = $request->notes;
            $purchase->user_id = $authid;
            $purchase->is_draft = false;
            $purchase->save();
            Log::info("Pucahse Controller| spr_purchase_update() |  Details After Edit", [
                'user_details' => Auth::user(),
                'User Name' => $authid,
                'request_payload' => $request->all(),
                'nessage' => 'Purchase Order Updated Successfully'
            ]);
           
            return redirect()->back()->withSuccess('Successfully Updated');
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | SprPurchaseUpdate() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }
    public function spr_save_draft(Request $request, $id)
    {
        try {
            $purchase = SprPorder::find($id);

            $authid = Auth::id();
            $request->validate([
                'supplier_id' => 'nullable',
                'type_of_purchase' => 'nullable'
            ]);
            $purchase->part_id = $request->part_id; //2

            $purchase->tax = $request->tax; //10
            $purchase->tax2 = $request->tax2; //11
            $purchase->tax3 = $request->tax3; //12
            $purchase->supplier_id = $request->supplier_id; //1
            $purchase->type_of_purchase = $request->type_of_purchase; //3
            $purchase->enduser_id = $request->enduser_id; //4
            $purchase->status = $request->status;
            $purchase->delivery_reference_number = $request->delivery_reference_number;
            // $purchase->purchasing_order_number  = Pay::genSprPurchaseCode();
            // $purchase->suppliers_reference = $request->suppliers_reference;
            // $purchase->site_id = $request->site_id;
            // $purchase->date_created = $request->date_created;
            // $purchase->deliver_to = $request->deliver_to;
            // $purchase->created_by = $request->created_by;

            $purchase->invoice_number = $request->invoice_number;
            $purchase->notes = $request->notes;
            $purchase->user_id = $authid;
            $purchase->is_draft = true;
            $purchase->save();

         
            return redirect()->back()->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | SprSaveDraft() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }
    public function spr_purchase_update_row(Request $request, $id)
    {
        try {
            $porder = SprPorder::find($id);
            $order_id = SprPorder::where('id', '=', $id)->value('order_id');
            $purchasing_order_number = SprPorder::where('id', '=', $id)->value('purchasing_order_number');

            SprPorderItem::create([
                'order_id' => $order_id,
                'unit_price' => $request->unit_price,
                'comments' => $request->comments,
                'remarks' => $request->remarks,
                'priority' => $request->priority,
                'prefix' => $request->prefix,
                'purchasing_order_number' => $purchasing_order_number,
                'sub_total' => $request->quantity * $request->unit_price,

            ]);
         
            return redirect()->back()->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | SprPurchaseUpdateRow() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }
    public function spr_porder_action(Request $request)
    {
        try {
            if ($request->ajax()) {
                if ($request->action == 'edit') {
                    $data = array(
                        'uom'        =>    $request->uom,
                        'quantity'        =>    $request->quantity,
                        'unit_price'        =>    $request->unit_price,
                        'remarks'  =>    $request->remarks,
                        // 'discount'  =>    $request->discount,
                        'rate'  =>    $request->rate,

                    );
                    Log::info('Action Details', [
                        'Details' => $data,
                    ]);
                    $val =    DB::table('spr_porder_items')
                        ->where('id', $request->id)
                        ->update($data);
                    $authid = Auth::user()->name;
                    Log::info('Action Details', [
                        'Edited By' => $authid,
                        'Details' => $val,
                    ]);
                    $quantity = SprPorderItem::where('id', '=', $request->id)->value('quantity');
                    $unit_price = SprPorderItem::where('id', '=', $request->id)->value('unit_price');


                    // $discount = SprPorderItem::where('id', '=', $request->id)->value('discount');
                    $sub_total = $quantity * $unit_price;
                    // $sub_total = (($sub_total) - ($sub_total * $discount) / 100);

                    Log::info('myresults Details', [
                        'Edited By' => $sub_total,
                        'Details' => $val,
                    ]);

                    $val1 = SprPorderItem::where('id', $request->id)->update(['sub_total' => $sub_total]);
                    Log::info('Updated Values', [
                        'Edited By' => $authid,
                        'Details' => $val1,
                    ]);
                }
                $authid = Auth::user()->name;
                if ($request->action == 'delete') {
                    Log::info('Deleted Values', [
                        'Deleted By' => $authid,
                        'Details' =>  DB::table('spr_porder_items')
                            ->where('id', $request->id),
                    ]);
                    DB::table('spr_porder_items')
                        ->where('id', $request->id)
                        ->delete();
                }

                return response()->json($request);
            }
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | SprPorderAction() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }


    public function spr_pos()
    {
        $site_id = Auth::user()->site->id;
        $spr_pos = SprPorder::where('is_draft', '=', false)->where('site_id', '=', $site_id)->latest()->paginate(15);
        return view('stockpurchases.spr_pos', compact('spr_pos'));
    }

    public function spr_pos_show($id)
    {
        $site_id = Auth::user()->site->id;
        $company = Company::first();
        $spr_pos = SprPorder::where('id', '=', $id)->first();
        $orderid = SprPorder::where('id', '=', $id)->value('order_id');
        $purchasing_order_number = SprPorder::where('id', '=', $id)->value('purchasing_order_number');
        $order_parts = SprPorderItem::where('purchasing_order_number', '=', $purchasing_order_number)->get();
        $grandtotal = SprPorderItem::where('purchasing_order_number', '=', $purchasing_order_number)->sum('sub_total');

        return view('stockpurchases.spr_pos_show', compact('spr_pos', 'order_parts', 'company', 'grandtotal'));
    }

    public function spr_pos_edit($id)
    {
        try{
            $site_id = Auth::user()->site->id;
            $purchase = SprPorder::find($id);
            $orderid = SprPorder::where('id', '=', $id)->value('order_id');
            $purchasing_order_number = SprPorder::where('id', '=', $id)->value('purchasing_order_number');
            $suppliers = Supplier::all();
            $sites = Site::where('site_id', '=', $site_id)->get();
            $locations = Location::where('site_id', '=', $site_id)->get();
            $parts = Part::where('site_id', '=', $site_id)->get();
            $endusers = Enduser::where('site_id', '=', $site_id)->get();
            $order_parts = SprPorderItem::where('purchasing_order_number', '=', $purchasing_order_number)->get();
            $grandtotal = SprPorderItem::where('purchasing_order_number', '=', $purchasing_order_number)->sum('sub_total');
    
            Log::info('PurchaseController | editlist', [
                'user_details' => auth()->user(),
                'message' => 'Purchase list edit form loaded successfully.',
                'purchase_id' => $id,
            ]);
    
            return view('stockpurchses.spr_edit', compact('purchase', 'suppliers', 'sites', 'locations', 'parts', 'endusers', 'order_parts', 'grandtotal'));
        }catch (\Exception $e){
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | SprPosEdit() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

       
    }

    public function spr_pos_delete($id)
    {
        try{
            $purchase = SprPorder::find($id);
            Log::info('SprPorder Deleted', [
                'Spr Details' => SprPorder::find($id),
                'Sprporder Details' => SprPorderItem::where("purchasing_order_number", $purchase->id)->delete(),
    
            ]);
            SprPorder::where("id", $purchase->id)->delete();
            SprPorderItem::where("purchasing_order_number", $purchase->id)->delete();
            // $purchase->destroy();
           
            return redirect()->back()->withSuccess('Successfully Updated');
        }catch(\Exception $e){
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StockPurchaseRequestController | SprPosDelete() Error ' . $unique_id
            ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

      
    }
}
