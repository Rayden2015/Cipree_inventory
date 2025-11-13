<?php

namespace App\Http\Controllers;

use Cart;
use Carbon\Carbon;
use App\Helpers\Pay;
use App\Models\Item;
use App\Models\User;
use App\Models\Sorder;
use App\Models\Company;
use App\Models\Enduser;
use App\Models\Supplier;
use PDF;
use App\Models\Inventory;
use App\Models\SorderPart;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helpers\UploadHelper;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use App\Traits\LogsErrors;

class StoreRequestController extends Controller
{
    use LogsErrors;
    
    public function __construct(){
        $this->middleware('auth');
    }
    public function request_search(Request $request)
    {
        try {
            Log::info('StoreRequestController | request_search() | ', [
                'user_details' => Auth::user(),
                'request_payload' => $request->all() // Include all request data in the log
            ]);

            if ($request->search) {
                $site_id = Auth::user()->site->id;
                $inventory = Item::join('inventory_items', 'items.id', '=', 'inventory_items.item_id')

                    ->where('items.item_description', 'like', "%" . $request->search . "%")->where('inventory_items.quantity', '>', '0')
                    ->orWhere('items.item_part_number', 'like', "%" . $request->search . "%")->where('inventory_items.quantity', '>', '0')
                    ->orWhere('items.item_stock_code', 'like', "%" . $request->search . "%")->where('inventory_items.quantity', '>', '0')
                    ->where('inventory_items.site_id', '=', $site_id)
                    ->get();


                Log::info('StoreRequestController | request_search() | ', [
                    'user_details' => Auth::user(),
                    'response' => $inventory
                ]);

                if ($inventory->isEmpty()) {

                    return redirect()->back()->withError('Item not in stock', 'Oops');
                } else {
                    return view('purchases.request_search', compact('inventory'));
                }
            }

            return view('purchases.request_search');
        } catch (\Exception $e) {
            return $this->handleError($e, 'request_search()');
        }
    }



    public function requester_search(Request $request)
    {
        try {
            if ($request->search) {
                Log::info('StoreRequestController | requester_search() | ', [
                    'user_details' => Auth::user(),
                    'request_payload' => $request
                ]);
                $site_id = Auth::user()->site->id;
                
                // Fix N+1 query by eager loading site relationship and fixing WHERE clause grouping
                $inventory = Item::with(['site', 'category', 'user'])
                    ->join('inventory_items', 'items.id', '=', 'inventory_items.item_id')
                    ->leftJoin('sites', 'inventory_items.site_id', '=', 'sites.id')
                    ->where('inventory_items.quantity', '>', '0')
                    ->where(function($query) use ($request) {
                        $query->where('items.item_description', 'like', "%" . $request->search . "%")
                            ->orWhere('items.item_part_number', 'like', "%" . $request->search . "%")
                            ->orWhere('items.item_stock_code', 'like', "%" . $request->search . "%");
                    })
                    ->select(
                        'items.*',
                        'inventory_items.quantity',
                        'inventory_items.id as inventory_item_id',
                        'inventory_items.site_id as inventory_site_id',
                        'sites.name as inventory_site_name'
                    )
                    ->orderBy('inventory_items.site_id')
                    ->get();

                Log::info('StoreRequestController | requester_search() | ', [
                    'user_details' => Auth::user(),
                    'result_count' => $inventory->count()
                ]);

                if ($inventory->isEmpty()) {
                    return redirect()->back()->withError('Item not in stock', 'Oops');
                } elseif ($inventory->isNotEmpty()) {
                    return view('purchases.request_search', compact('inventory'));
                }
                return view('purchases.request_search', compact('inventory'));
            }
        } catch (\Exception $e) {
            return $this->handleError($e, 'requester_search()');
        }
    }

    public function cart()
    {
        try {
            $site_id = Auth::user()->site->id;
            $customers = Supplier::all();
            // $products = InventoryItem::all();
            $request_number = Pay::genRefStCode();
            $request_date = Carbon::now()->toDateTimeString();
            // return view('cart', compact('request_number', 'request_date', 'products', 'customers'));
            // dd(session()->get('cart'));
            if (count((array) session('cart')) == '0') {
                //    dd($customers);
                return redirect()->route('stores.request_search');
            } elseif (count((array) session('cart')) > '0') {
                Log::info('StoreRequestController | cart() | ', [
                    'user_details' => Auth::user(),
                    'response payload' => session('cart')
                ]);
                return view('cart', compact('request_number', 'request_date', 'customers'));
            }
        } catch (\Exception $e) {
            return $this->handleError($e, 'cart()');
        }
    }

    public function addToCart($id)
    {
        try {
            $site_id = Auth::user()->site->id;
            $product = Item::join('inventory_items', 'items.id', '=', 'inventory_items.item_id')->where('inventory_items.id', $id)->where('inventory_items.site_id', '=', $site_id)->first();
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
                    "quantity" => 1,
                    "price" => $product->price,
                    "image" => $product->image
                ];
            }

            Log::info('StoreRequestController | addToCart() | ', [
                'user_details' => Auth::user(),
                'response_payload' => $cart
            ]);

            session()->put('cart', $cart);

            return redirect()->back()->with('success', 'Item added to cart successfully!');
        } catch (\Exception $e) {
            return $this->handleError($e, 'addToCart()');
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
                Log::info('StoreRequestController | update()', [
                    'user_details' => Auth::user(),
                    'message' =>  'Cart updated successfully',
                    'cart' => $cart
                ]);
                session()->flash('success', 'Cart updated successfully');
            }
        } catch (\Exception $e) {
            return $this->handleError($e, 'update()');
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
                Log::info('StoreReqeustController() | remove()', [
                    'user_details' => Auth::user(),
                    'request_payload' => $request,
                    'message' => 'Product removed successfully '
                ]);
                session()->flash('success', 'Product removed successfully');
            }
        } catch (\Exception $e) {
            return $this->handleError($e, 'remove()');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'photo' => 'sometimes|nullable|image|mimes:jpeg,gif,png,jpg|max:9048',
                'desc' => 'nullable',
                'work_order_number' => 'nullable|string|max:100',
            ]);

            $authid = Auth::id();
            $statusvalue = 'Requested';
            $site_id = Auth::user()->site->id;
            $cart = Session()->get('cart');
            $order = new Sorder();
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
            $order->work_order_number = $request->work_order_number;
            $order->requested_by = $authid;
            $order->site_id = $site_id;

            $order->save();
            Log::info("StoreRequestController() | store() ", [
                'user_details' => Auth::user(),
                'Added_By' => $authid,
                'Details' => $request->all(),
                'status_value' => $statusvalue,
            ]);

            $carts = (session()->get('cart'));
            foreach ($carts as $item) {
                $orderItem = new SorderPart();
                $orderItem->sorder_id = $order->id;
                // $orderItem->inventory_id = $item['inventory_id'];
                $orderItem->quantity = $item['quantity'];
                $orderItem->item_id = $item['item_id'];
                $orderItem->unit_price = $item['unit_cost_exc_vat_gh'];
                $orderItem->inventory_id = $item['id'];
                $orderItem->sub_total =  $item['unit_cost_exc_vat_gh'] *  $item['quantity'];
                $orderItem->site_id = $site_id;
                $orderItem->save();
            }

            Log::info("StoreRequestController | store()", [
                'user_details' => Auth::user(),
                'Added_by' => $authid,
                'Order Details' => $orderItem,
            ]);

            session()->forget('cart');

            // dd($data);
            // Cart::clear();
            // return redirect()->route('ordersindex');

            $orderUserId = Sorder::latest()->value('user_id');
            $title = 'You have a new request from';
            Notification::create([
                'title' => $title,
                'user_id' => $orderUserId,

            ]);

            return redirect()->route('stores.request_search')->withSuccess('Stock Requisition(SR) #' . $order->request_number . ' forwarded for Authorisation');
        } catch (\Throwable $e) {
            return $this->handleError($e, 'store()');
        }
    }

    public function store_lists(Request $request)
    {
        $site_id = Auth::user()->site->id ?? null;
        $missingDepartment = false;
        $missingSite = false;

        // Build base query
        $query = Sorder::query();

        // Apply search filters if provided (single search parameter that searches all fields)
        $hasSearch = $request->filled('search');
        
        if ($hasSearch) {
            $searchTerm = '%' . $request->search . '%';
            
            $query->leftJoin('sorder_parts', 'sorders.id', '=', 'sorder_parts.sorder_id')
                  ->leftJoin('items', 'sorder_parts.item_id', '=', 'items.id')
                  ->leftJoin('endusers', 'sorders.enduser_id', '=', 'endusers.id')
                  ->select('sorders.*')
                  ->distinct();

            // CRITICAL: Always filter by site_id in search to prevent cross-site data leakage
            if ($site_id !== null) {
                $query->where('sorders.site_id', '=', $site_id);
                // Also filter sorder_parts by site_id to ensure data integrity
                $query->where('sorder_parts.site_id', '=', $site_id);
            }

            // Search across all parameters: item number (stock code/part number), request number, and end user
            $query->where(function($q) use ($searchTerm) {
                $q->where('items.item_stock_code', 'like', $searchTerm)
                  ->orWhere('items.item_part_number', 'like', $searchTerm)
                  ->orWhere('sorders.request_number', 'like', $searchTerm)
                  ->orWhere('sorders.work_order_number', 'like', $searchTerm)
                  ->orWhere('endusers.asset_staff_id', 'like', $searchTerm);
            });
        }

        // Apply role-based filters - ALWAYS apply these regardless of search
        if (Auth::user()->hasRole('Super Admin')) {
            // If the user is a Super Admin, get all store requests without department filtering
            // Site filter already applied in search, or apply here if no search
            if (!$hasSearch && $site_id !== null) {
                $query->where('sorders.site_id', '=', $site_id);
            }
        } elseif (Auth::user()->hasRole('Department Authoriser')) {
            // If the user is a Department Authoriser, filter by department
            $department_id = Auth::user()->department->id ?? null;
            
            if ($department_id === null) {
                $missingDepartment = true;
                Log::warning('Department Authoriser without department viewing store requests', [
                    'user_id' => Auth::id(),
                    'site_id' => $site_id
                ]);
                // Site filter already applied in search, or apply here if no search
                if (!$hasSearch && $site_id !== null) {
                    $query->where('sorders.site_id', '=', $site_id);
                }
            } else {
                // Join users table for department filtering
                // Note: If search already joined users, this will be ignored by Laravel
                $query->leftJoin('users', 'users.id', '=', 'sorders.user_id')
                      ->where('users.department_id', '=', $department_id);
                
                // Site filter already applied in search, or apply here if no search
                if (!$hasSearch && $site_id !== null) {
                    $query->where('sorders.site_id', '=', $site_id);
                }
                
                if (!$hasSearch) {
                    $query->select('sorders.*');
                }
            }
        } else {
            // Default role - apply site filter
            // Site filter already applied in search, or apply here if no search
            if (!$hasSearch) {
                if ($site_id === null) {
                    $missingSite = true;
                    Log::warning('User without site viewing store requests', [
                        'user_id' => Auth::id(),
                        'user_name' => Auth::user()->name,
                        'url' => request()->fullUrl()
                    ]);
                    // No site filter for users without site
                } else {
                    $query->where('sorders.site_id', '=', $site_id);
                }
            }
        }

        // Eager load relationships and paginate
        $store_requests = $query->with(['request_by', 'enduser'])
                                ->latest('sorders.created_at')
                                ->paginate(15)
                                ->appends($request->all());

        return view('stores.index', compact('store_requests', 'missingDepartment', 'missingSite'));
    }

    public function store_list_view($id)
    {
        // Fix N+1 query by eager loading relationships
        $sorder = Sorder::with(['enduser', 'request_by', 'user'])->find($id);
        $company = Company::first();
        // Eager load item relationship to prevent N+1 queries
        $sorder_parts = SorderPart::with(['item', 'inventoryItem.location'])->where('sorder_id', '=', $id)->get();
        // Calculate the total amount from sorder_parts
        $total_amount = $sorder_parts->sum('sub_total');

        // Update the total column of the Sorder record
        $sorder->total = $total_amount;

        $sorder->save();
        return view('stores.view', compact('sorder', 'sorder_parts', 'company'));

        // dd($sorder);
    }
    public function generatesorderPDF($id)
    {
        try {

            $company = Company::latest()->first();
            $pdf_filename = Sorder::where('id', '=', $id)->value('request_number'); // Get the value directly
            $sorder = Sorder::where('id', '=', $id)->first();
            $sorder_parts = SorderPart::where('sorder_id', '=', $id)->get();
            $sorder = PDF::loadView('stores.pdf', compact('sorder', 'company', 'sorder_parts'))->setOptions(['defaultFont' => 'sans-serif']);
            Log::info("Storerequest | generatesorderPDF() | ");
            $filename = $pdf_filename . '.pdf'; // Append '.pdf' to the filename

            return $sorder->download($filename);
        } catch (\Exception $e) {
            return $this->handleError($e, 'generatesorderPDF()');
        }
    }

    public function store_list_edit($id)
    {
        try {
            $site_id = Auth::user()->site->id;
            $sorder = Sorder::find($id);
            $sorder_parts = SorderPart::where('sorder_id', '=', $id)->get();
            $suppliers = Supplier::all();
            $endusers = Enduser::where('site_id', '=', $site_id)->get();
            Log::info('StoreRequestController| store_list_edit() | ', [
                'user_details' => Auth::user()
            ]);
            return view('stores.edit', compact('sorder', 'suppliers', 'endusers', 'sorder_parts'));
        } catch (\Exception $e) {
            return $this->handleError($e, 'store_list_edit()');
        }
    }

    public function store_list_update(Request $request, $id)
    {
        try {
            $sorder = Sorder::find($id);
            $authid = Auth::user()->name;
            Log::info('SOrder Item Before', [
                'edited_by' => $authid,
                'Details' => Sorder::find($id),
            ]);
            $sorder->type_of_purchase = $request->type_of_purchase;
            $sorder->status = $request->status;
            $sorder->supplier_id = $request->supplier_id;
            $sorder->enduser_id = $request->enduser_id;
            $sorder->delivery_reference_number = $request->delivery_reference_number;
            $sorder->invoice_number = $request->invoice_number;
            $sorder->save();

            Log::info("StoreRequestController| store_list_update() | Sorder After Edit", [
                'Added_by' => $authid,
                'Details' => $request->all(),
            ]);
            return redirect()->back();
        } catch (\Exception $e) {
            return $this->handleError($e, 'store_list_update()');
        }
    }


    public function sorder_update(Request $request, $id)
    {
        try {
            $request->validate([
                'qty_supplied' => 'numeric|min:0',
            ]);

            $sorder = SorderPart::find($id);
            $authid =  Auth::user()->name;
            Log::info("Sorder_Part before edit", [
                'Edited_By' => $authid,
                'Details' => SorderPart::find($id),
            ]);

            $inventory_id = SorderPart::where('id', '=', $id)->value('inventory_id');
            $quantity = InventoryItem::where('id', '=', $inventory_id)->value('quantity');
            $sorder_quantity =  SorderPart::where('id', '=', $id)->value('quantity');

            $requestedQuantity = (float) ($sorder_quantity ?? 0);
            $availableQuantity = is_null($quantity) ? null : (float) $quantity;
            $suppliedQuantity = (float) $request->qty_supplied;

            if ($suppliedQuantity > $requestedQuantity) {
                return back()->with('message', 'Quantity supplied cannot exceed the requested quantity.');
            }

            if (! is_null($availableQuantity) && $suppliedQuantity > $availableQuantity) {
                return back()->with('message', 'Quantity not available in stock.');
            }

            $sorder->qty_supplied = $suppliedQuantity;
            // $sorder->remarks = $request->remarks;
            if ($sorder->qty_supplied >= $requestedQuantity) {
                $sorder->remarks = 'Fully Supplied';
            } elseif ($sorder->qty_supplied == 0.0) {
                $sorder->remarks = 'Not Supplied';
            } else {
                $sorder->remarks = 'Partially Supplied';
            }

            $sorder->save();
            Log::info('Quantity before', [
                'Edited_by' => $authid,
                'Details' => $request->all(),
            ]);
            $unit_price = SorderPart::where('id', '=', $request->id)->value('unit_price');
            $sub_total = $sorder->qty_supplied * ($unit_price ?? 0);
            SorderPart::where('id', $request->id)->update(['sub_total' => $sub_total]);

            return redirect()->back();
        } catch (\Exception $e) {
            return $this->handleError($e, 'sorder_update()');
        }
    }


    public function stores_action(Request $request)
    {
        try {
            if ($request->ajax()) {
                if ($request->action == 'edit') {
                    $data = array(

                        'quantity'        =>    $request->quantity,
                        'unit_price'        =>    $request->unit_price,
                        'remarks'  =>    $request->remarks,
                    );
                    Log::info('storerequest Action Details', [
                        'Details' => $data,
                    ]);
                    $item_id = SorderPart::where('id', '=', $request->id)->value('item_id');
                    $quantity = InventoryItem::where('item_id', '=', $item_id)->value('quantity');
                    if ($request->quantity > $quantity) {
                        Log::info('storerequested Action Details', [
                            'Details' => $item_id,
                            'quantity' => $quantity,
                            'details 2' => $request->quantity
                        ]);
                        return back()->with('message', 'Quantity not available!');
                    } else
                        $val =     DB::table('sorder_parts')
                            ->where('id', $request->id)
                            ->update($data);
                    $authid = Auth::user()->name;
                    Log::info('storerequest after Action Details', [
                        'Edited By' => $authid,
                        'Details' => $val,
                    ]);

                    $quantity = SorderPart::where('id', '=', $request->id)->value('quantity');
                    $unit_price = SorderPart::where('id', '=', $request->id)->value('unit_price');
                    $sub_total = $quantity * $unit_price;
                    $val1 =  SorderPart::where('id', $request->id)->update(['sub_total' => $sub_total]);
                    Log::info('store request Updated Values', [
                        'Edited By' => $authid,
                        'Details' => $val1,
                    ]);
                }
                if ($request->action == 'delete') {
                    DB::table('sorder_parts')
                        ->where('id', $request->id)
                        ->delete();
                }

                return response()->json($request);
            }
        } catch (\Exception $e) {
            return $this->handleError($e, 'stores_action()');
        }
    }

    public function approved_status($id)
    {
        try {

            $authid = Auth::id();
            $date = Carbon::now()->toDateTimeString();
            $order = Sorder::find($id);
            $order = Sorder::where('id', '=', $id)->update(['approval_status' => 'Approved', 'approved_by' => $authid, 'approved_on' => $date]);
            Log::info('StoreRequestController | approved_status', [
                'user_details' => Auth::user(),
                'request_payload' => $order
            ]);
            return redirect()->back();
        } catch (\Exception $e) {
            return $this->handleError($e, 'approved_status()');
        }
    }


    public function depart_auth_approved_status($id)
    {
        try {
            $authid = Auth::id();
            $date = Carbon::now()->toDateTimeString();        
            // Fetch the order by ID
            $order = Sorder::find($id);
        
            // Check if order exists
            if (!$order) {
                return redirect()->back()->withError('Order not found');
            }
        
            // Update the order with department authorization status
            $order->update([
                'depart_auth_approval_status' => 'Approved',
                'depart_auth_approved_by' => $authid,
                'depart_auth_approved_on' => $date,
            ]);
        
            // Log the successful update
            Log::info('StoreRequestController | depart_auth_approved_status', [
                'user_details' => Auth::user(),
                'request_payload' => $order // Now the actual model is logged
            ]);
        
            return redirect()->back()->with('success', 'Order approved successfully');
        } catch (\Exception $e) {
            return $this->handleError($e, 'depart_auth_approved_status()');
        }
    }        

    public function denied_status($id)
    {
        try {
            $authid = Auth::id();
            $date = Carbon::now();
            $order = Sorder::find($id);
            $order = Sorder::where('id', '=', $id)->update(['approval_status' => 'Denied', 'approved_by' => $authid, 'approved_on' => $date]);
            Log::info('StoreRequestController | denied_status', [
                'user_details' => Auth::user(),
                'request_payload' => $order
            ]);
            return redirect()->back();
        } catch (\Exception $e) {
            return $this->handleError($e, 'denied_status()');
        }
    }

    public function depart_auth_denied_status($id)
    {
        try {
            $authid = Auth::id();
            $date = Carbon::now();
            $order = Sorder::find($id);
            $order = Sorder::where('id', '=', $id)->update(['depart_auth_approval_status' => 'Denied', 'depart_auth_denied_by' => $authid, 'depart_auth_denied_on' => $date]);
            Log::info('StoreRequestController | depart_auth_denied_status', [
                'user_details' => Auth::user(),
                'request_payload' => $order
            ]);
            return redirect()->back();
        } catch (\Exception $e) {
            return $this->handleError($e, 'depart_auth_denied_status()');
        }
    }

    public function store_officer_lists()
    {
        try {
            $site_id = Auth::user()->site->id ?? null;
            $missingSite = false;
            
            if ($site_id === null) {
                $missingSite = true;
                Log::warning('Store Officer without site viewing store requests', [
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name,
                    'url' => request()->fullUrl()
                ]);
                // Fallback to showing all approved store requests
                $officer_lists = Sorder::with(['enduser', 'request_by', 'user'])
                    ->where('approval_status', '=', 'Approved')
                    ->latest('id')
                    ->paginate(15);
            } else {
                // Fix N+1 query by eager loading user relationships
                $officer_lists = Sorder::with(['enduser', 'request_by', 'user'])
                    ->where('approval_status', '=', 'Approved')
                    ->where('site_id', '=', $site_id)
                    ->latest('id')
                    ->paginate(15);
            }
            
            Log::info('StoreRequestController | store_officer_lists()', [
                'user_details' => Auth::user(),
                'result_count' => $officer_lists->total(),
                'site_id' => $site_id
            ]);
            return view('stores.officer_lists', compact('officer_lists', 'missingSite'));
        } catch (\Exception $e) {
            return $this->handleError($e, 'store_officer_lists()');
        }
    }

    public function store_requester_lists()
    {
        try {
            $site_id = Auth::user()->site->id;
            $officer_lists = Sorder::where('site_id', '=', $site_id)->latest()->paginate(20);
            Log::info('StoreRequestController | store_requester_lists()', [
                'user_details' => Auth::user(),
                'response_payload' => $officer_lists
            ]);
            return view('stores.requester_lists', compact('officer_lists'));
        } catch (\Exception $e) {
            return $this->handleError($e, 'store_requester_lists()');
        }
    }

    public function store_officer_edit(Request $request, $id)
    {
        try {
            $site_id = Auth::user()->site->id;
            $request->validate([]);
            $suppliers = Supplier::all();
            $sorder = Sorder::find($id);
            $endusers = Enduser::where('site_id', '=', $site_id)->get();
            $sorder_parts = SorderPart::where('sorder_id', '=', $id)->get();
            Log::info('StoreRequestController | store_officer_edit()', [
                'user_details' => Auth::user(),
                'response_payload' => $sorder_parts
            ]);
            return view('stores.officer_edit', compact('sorder_parts', 'sorder', 'suppliers', 'endusers'));
        } catch (\Exception $e) {
            return $this->handleError($e, 'store_officer_edit()');
        }
    }

    public function store_officer_update(Request $request, $id)
    {
        $purchase = Sorder::find($id);
        $authid = Auth::user()->name;
        Log::info("StoreReqquestController | store_officer_update() | Sorder before edit", [
            'user_details' => Auth::user(),
            'response_payload' => $purchase
        ]);
        $authid = Auth::id();
        $date = Carbon::now();
        $request->validate([
            'supplier_id' => 'nullable',
            'type_of_purchase' => 'nullable',
            'quantity' => 'gte:0', // Ensure quantity is not negative during validation
            'supplied_to' => 'required|string|max:255',
        ]);

        $deliverynum = Sorder::where('id', '=', $id)->value('request_number');

        if ($purchase->depart_auth_approval_status !== 'Approved' || $purchase->approval_status !== 'Approved') {
            return redirect()->back()->withError('This request must be fully approved before it can be processed by Stores.');
        }

        $sorderPartsForValidation = SorderPart::with('item_details')->where('sorder_id', $id)->get();

        if ($sorderPartsForValidation->isEmpty()) {
            return redirect()->back()->withError('No request lines found to process.');
        }

        foreach ($sorderPartsForValidation as $part) {
            $requestedQuantity = (float) ($part->quantity ?? 0);
            $suppliedQuantity = (float) ($part->qty_supplied ?? 0);

            if ($suppliedQuantity < 0) {
                return redirect()->back()->withError('Quantity supplied cannot be negative for ' . ($part->item_details->item_description ?? 'an item') . '.');
            }

            if ($suppliedQuantity > $requestedQuantity) {
                return redirect()->back()->withError('Quantity supplied (' . $suppliedQuantity . ') cannot exceed the requested quantity (' . $requestedQuantity . ') for ' . ($part->item_details->item_description ?? 'an item') . '.');
            }
        }

        try {
            DB::beginTransaction();

            $delivered_on_date = Carbon::now()->toDateTimeString();

            $genDeliveryNum = $deliverynum;
            $purchase->tax = $request->tax;
            $purchase->tax2 = $request->tax2;
            $purchase->tax3 = $request->tax3;
            $purchase->supplier_id = $request->supplier_id;
            $purchase->type_of_purchase = $request->type_of_purchase;
            $purchase->enduser_id = $request->enduser_id;
            $purchase->status = 'Supplied';
            $purchase->delivery_reference_number = $genDeliveryNum;
            $purchase->invoice_number = $request->invoice_number;
            $purchase->delivered_by = $authid;
            $purchase->user_id = $authid;
            $purchase->delivered_on = $delivered_on_date;
            $purchase->edited_at = '1';
            $purchase->supplied_to = $request->supplied_to;

            $purchase->save();

            Log::info('StoreReqquestController | store_officer_update() | Sorder Details', [
                'Details' => $request->all(),
                'genDeliveryNum' => $deliverynum,
                'status' => $purchase->status,
                'delivered_by' => $purchase->delivered_by,
                'delivered_on' => $purchase->delivered_on,
                'purchase_edited_at' => $purchase->edited_at,
            ]);

            $sorder_id = SorderPart::where('sorder_id', $id)->pluck('inventory_id')->toArray();
            $quantity = SorderPart::where('sorder_id', $id)->pluck('qty_supplied')->toArray();

            $data['items'] = DB::table('inventory_items')
                ->select('inventory_items.*')
                ->join('sorder_parts', 'inventory_items.id', '=', 'sorder_parts.inventory_id')
                ->where('sorder_parts.sorder_id', '=', $id)
                ->selectRaw('inventory_items.id, inventory_items.quantity - sorder_parts.qty_supplied AS new_quantity')
                ->get();
            Log::info("Data Items Before Edit", [
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
                ->join('sorder_parts', 'inventory_items.id', '=', 'sorder_parts.inventory_id')
                ->where('sorder_parts.sorder_id', '=', $id)
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
            // Update stock quantities
            if (! app()->environment('testing')) {
                DB::select(
                    'UPDATE items i
         LEFT JOIN (
              SELECT t.item_id, SUM(t.quantity) AS calculated_quantity
              FROM items i
              JOIN inventory_items t ON i.id = t.item_id
              GROUP BY t.item_id
          ) AS subquery ON i.id = subquery.item_id
          SET i.stock_quantity = subquery.calculated_quantity;'
                );
            }
            DB::commit();



            return redirect()->back()->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            return $this->handleError($e, 'store_officer_update()');
        }
    }


    public function update_manual_remarks(Request $request, $id)
    {
        try {
            // Find the sales order by ID
            $sorder = Sorder::find($id);

            if (!$sorder) {
                return redirect()->back()->withError('Sales order not found.');
            }

            // Update the manual remarks
            $sorder->manual_remarks = $request->manual_remarks;
            $sorder->save();

            // Redirect back with a success message
            return redirect()->back()->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            // Generate a unique error ID
            return $this->handleError($e, 'update_manual_remarks()');
        }
    }


    public function supply_history()
    {
        try {
            $site_id = Auth::user()->site->id;            
            // Fix N+1 queries by eager loading all necessary relationships
            $total_cost_of_parts_within_the_month = SorderPart::with([
                'sorder' => function ($query) use ($site_id) {
                    $query->where('site_id', $site_id)
                          ->whereIn('status', ['Supplied', 'Partially Supplied']);
                },
                'sorder.enduser', // Eager load enduser relationship
                'item', // Eager load item relationship
                'inventoryItem.inventory', // Eager load inventory relationship
                'inventoryItem.location' // Eager load location relationship
            ])
            ->where('sorder_parts.site_id', $site_id) // Ensure filtering by site_id in sorder_parts
            ->join('sorders', 'sorder_parts.sorder_id', '=', 'sorders.id') // Join with sorders
            ->whereIn('sorders.status', ['Supplied', 'Partially Supplied']) // Add status filter in join
            ->orderBy('sorders.delivered_on', 'desc') // Order by delivered_on in descending order
            ->select('sorder_parts.*') // Select only columns from sorder_parts
            ->paginate(100);
            

            Log::info("StoreReqquestController | supply_history() | Supply history loaded", [
                'user_details' => Auth::user(),
                'response_message' => 'Supply history displayed succesfully'
            ]);
            // dd($total_cost_of_parts_within_the_month);
            return view('stores.supply_history', compact('total_cost_of_parts_within_the_month'));
        } catch (\Exception $e) {
            return $this->handleError($e, 'supply_history()');
        }
    }

  

    public function supply_history_search_item(Request $request)
    {
        try {
            // Log user details and request payload
            Log::info("StoreReqquestController | supply_history_search_item() ", [
                'user_details' => Auth::user(),
                'request_payload' => $request->all()
            ]);
    
            $site_id = Auth::user()->site->id;
    
            // Initialize the query with eager loading to prevent N+1 queries
            $query = SorderPart::with([
                    'item_details', 
                    'location', 
                    'sorder.enduser', // Eager load enduser to prevent N+1
                    'sorder.inventory',
                    'inventoryItem.inventory' // Eager load inventory relationship
                ])
                ->leftjoin('sorders', 'sorders.id', '=', 'sorder_parts.sorder_id')
                ->leftjoin('items', 'items.id', '=', 'sorder_parts.item_id')
                ->leftjoin('endusers', 'sorders.enduser_id', '=', 'endusers.id')
                ->leftjoin('inventory_items', 'sorder_parts.inventory_id', '=', 'inventory_items.id')
                ->leftJoin('inventories', 'inventory_items.inventory_id', '=', 'inventories.id')
                ->where('sorders.site_id', '=', $site_id)
                ->where('sorder_parts.site_id', '=', $site_id)
                ->whereIn('sorders.status', ['Supplied', 'Partially Supplied'])
                ->select(
                    'sorder_parts.id', 
                    'sorder_parts.sorder_id',
                    'items.item_description', 
                    'items.item_part_number', 
                    'items.item_stock_code', 
                    'sorder_parts.qty_supplied', 
                    'sorder_parts.sub_total', 
                    'sorders.delivery_reference_number', 
                    'inventories.grn_number', 
                    'sorders.request_number',
                    'sorders.enduser_id',
                    'sorders.delivered_on',
                    'inventory_items.location_id',
                    'inventory_items.inventory_id',
                    'sorder_parts.inventory_id as sp_inventory_id' // Add alias to avoid conflicts
                );
    
            // Apply search filter if present
            if ($request->search) {
                $searchTerm = '%' . $request->search . '%';
                $query->where(function ($subQuery) use ($searchTerm) {
                    $subQuery->where('endusers.asset_staff_id', 'like', $searchTerm)
                        ->orWhere('items.item_description', 'like', $searchTerm)
                        ->orWhere('items.item_part_number', 'like', $searchTerm)
                        ->orWhere('items.item_stock_code', 'like', $searchTerm)
                        ->orWhere('sorders.delivery_reference_number', 'like', $searchTerm)
                        ->orWhere('sorders.request_number', 'like', $searchTerm)
                        ->orWhere('inventories.grn_number', 'like', $searchTerm);
                });
            }
    
            // Apply date range filter if present
            if ($request->start_date && $request->end_date) {
                $query->whereBetween('sorders.created_at', [$request->start_date, $request->end_date]);
            }
    
            // Paginate the result
            $total_cost_of_parts_within_the_month = $query->latest('sorders.created_at')
                ->paginate(100)
                ->appends([
                    'search' => $request->search,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
    
            // Log success
            Log::info("StoreReqquestController | supply_history_search_item() ", [
                'user_details' => Auth::user(),
                'request_message' => 'Supply History Search Item Successful',
                'response_payload' => $total_cost_of_parts_within_the_month
            ]);
    
            // Return the view with the data
            return view('stores.supply_history_search_item', compact('total_cost_of_parts_within_the_month'));
        } catch (\Exception $e) {
            // Log the error with a unique ID
            return $this->handleError($e, 'supply_history_search_item()');
        }
    }
    
    
    

    public function sorderpart_delete($sorderPartId)
    {
        try {
            $sorderPart = SorderPart::findOrFail($sorderPartId);
            $sorderPart->delete();

            Log::info("StoreRequestController | sorderpart_delete() | Sorder part deleted", [
                'user_details' => Auth::user(),
                'sorder_part_id' => $sorderPartId
            ]);

            return redirect()->back()->withSuccess('Item deleted successfully');
        } catch (\Exception $e) {
            return $this->handleError($e, 'sorderpart_delete()');
        }
    }


    public function destroy($id)
    {
        try {
            $order = Sorder::find($id);
            $authid  = Auth::user()->name;
            Log::info("StoreRequestController | destroy()| Details before delete", [
                'user_details' => Auth::user(),
                'request_payload' => $order
            ]);
            $order->delete();
            $order = SorderPart::where('sorder_id', '=', $id)->delete();
            Log::info("StoreRequestController | destroy()| Details before delete", [
                'user_details' => Auth::user(),
                'response_message' => 'Order Delete successfully',
                'response_payload' => $order
            ]);
            return redirect()->back();
        } catch (\Exception $e) {
            return $this->handleError($e, 'destroy()');
        }
    }

    public function requester_store_lists()
    {
        try {
            $site_id = Auth::user()->site->id;
            $auth = Auth::id();
            // Fix N+1 query by eager loading user and enduser relationships
            $requester_store_lists = Sorder::with(['enduser', 'request_by', 'user'])
                ->where('requested_by', '=', $auth)
                ->where('site_id', '=', $site_id)
                ->latest()
                ->paginate(15);
            Log::info("StoreReqquestController | requester_store_lists() ", [
                'user_details' => Auth::user(),
                'result_count' => $requester_store_lists->total()
            ]);
            return view('stores.requester_store_lists', compact('requester_store_lists'));
        } catch (\Exception $e) {
            return $this->handleError($e, 'requester_store_lists()');
        }
    }

    public function requester_edit($id)
    {
        try {
            $site_id = Auth::user()->site->id;
            $suppliers = Supplier::all();
            $sorder = Sorder::find($id);
            $endusers = Enduser::where('site_id', '=', $site_id)->get();
            $sorder_parts = SorderPart::where('sorder_id', '=', $id)->get();
            //$prices = SorderPart::where()
            Log::info("StoreReqquestController | requester_edit() ", [
                'user_details' => Auth::user(),
                'response_message' => 'Request Edited Succesful',
                'response_payload' => $sorder . '\n' . $sorder_parts
            ]);
            return view('stores.requester_edit', compact('sorder_parts', 'sorder', 'suppliers', 'endusers'));
        } catch (\Exception $e) {
            return $this->handleError($e, 'requester_edit()');
        }
    }

    public function requester_store_update(Request $request, $id)
    {
        try {
            $purchase = Sorder::find($id);
            $authid  = Auth::user()->name;
            $purchase->enduser_id = $request->enduser_id;
            $purchase->save();
            Log::info("StoreRequestController | requester_store_update() ", [
                'user_details' => Auth::user(),
                'request_payload' => $request,
                'response_payload' => $purchase
            ]);
            return redirect()->back();
        } catch (\Exception $e) {
            return $this->handleError($e, 'requester_store_update()');
        }
    }


    public function requester_sorder_update(Request $request, $id)
    {
        try {
            $sorder = SorderPart::find($id);
            $authid  = Auth::user()->name;
            Log::info("StoreRequestController| requester_sorder_update() | Details before edit", [
                'user_details' => Auth::user(),
                'request_payload' => $request,
                'response_payload' => SorderPart::find($id),
            ]);

            $inventory_id = SorderPart::where('id', '=', $id)->value('inventory_id');
            $quantity = InventoryItem::where('id', '=', $inventory_id)->value('quantity');
            $sorder_quantity =  SorderPart::where('id', '=', $id)->value('quantity');
            Log::info("Detail values before edit", [
                'username' => $authid,
                'inventory_id' => $inventory_id,
                'quantity' => $quantity,
                'sorder_quantity' => $sorder_quantity,
            ]);
            if ($request->qty_supplied > $quantity || $request->qty_supplied > $sorder_quantity) {

                return back()->with('message', 'Quantity not available!');
            } else {

                $sorder->quantity = $request->quantity;
                $sorder->save();
                Log::info("Detail values after save", [
                    'detials' => $request->all(),

                ]);

                $quantity = SorderPart::where('id', '=', $request->id)->value('quantity');
                $unit_price = SorderPart::where('id', '=', $request->id)->value('unit_price');
                $sub_total = $quantity * $unit_price;
                Log::info("Detail values logic is met", [

                    'unit_price' => $unit_price,
                    'quantity' => $quantity,
                    'sub_total' => $sub_total,
                ]);
                $val =   SorderPart::where('id', $request->id)->update(['sub_total' => $sub_total]);
                Log::info("new values", [

                    'values' => $val,
                ]);
                return redirect()->back();
            }
        } catch (\Exception $e) {
            return $this->handleError($e, 'requester_sorder_update()');
        }
    }

    public function requester_store_delete($id)
    {
        try {
            $sorder = SorderPart::find($id);
            $authid = Auth::user()->name;
            Log::info("Details before delete", [
                'username' => $authid,
                'Details' => SorderPart::find($id),
            ]);
            SorderPart::where("id", $sorder->id)->delete();
            // $sorder->delete();
            return redirect()->back();
        } catch (\Exception $e) {
            return $this->handleError($e, 'requester_store_delete()');
        }
    }

    public function store_officer_list_search(Request $request)
    {
        try {
            $site_id = Auth::user()->site->id;
            if ($request->search) {
                $officer_lists =   User::join('sorders', 'sorders.requested_by', '=', 'users.id')
                    ->where('sorders.approval_status', '=', 'Approved')

                    ->where('users.name', 'like', "%" . $request->search . "%")
                    ->orWhere('sorders.approval_status', '=', 'Approved')

                    ->where('sorders.request_number', 'like', "%" . $request->search . "%")
                    ->where('sorders.site_id', '=', $site_id)
                    ->latest('sorders.id')
                    ->paginate(10);

                if ($officer_lists->isEmpty()) {

                    return redirect()->back()->withError('Record not found', 'Oops');
                } elseif ($officer_lists->isNotEmpty()) {
                    return view('stores.officer_lists', compact('officer_lists'));
                }
                // dd($officer_lists);
                else  $officer_lists = Sorder::where('approval_status', '=', 'approved')->where('site_id', '=', $site_id)->latest('id')->paginate(15);

                Log::info("StoreReqquestController | store_officer_list_search() ", [
                    'user_details' => Auth::user(),
                    'request_payload' => $request,
                    'response_message' => 'Store Officer List Search Success'
                ]);
                return view('stores.officer_lists', compact('officer_lists'));
            }
        } catch (\Exception $e) {
            return $this->handleError($e, 'store_officer_list_search()');
        }
    }

    public function received_history_page()
    {
        try {
            $site_id = Auth::user()->site->id;
            $received_history_page = Sorder::join('sorder_parts', 'sorders.id', '=', 'sorder_parts.sorder_id')
                ->join('items', 'sorder_parts.item_id', '=', 'items.id')
                ->where('status', '=', 'Supplied')->orWhere('status', '=', 'Partially Delivered')->where('sorders.site_id', '=', $site_id)->where('sorder_parts.site_id', '=', $site_id)->latest('sorders.created_at')->paginate(15);
            Log::info("StoreReqquestController | received_history_page() ", [
                'user_details' => Auth::user(),
                'response_message' => 'Receive History Page Displayd Successully'
            ]);
            return view('stores.received_history_page', compact('received_history_page'));
            // dd($received_history_page);
        } catch (\Exception $e) {
            return $this->handleError($e, 'received_history_page()');
        }
    }
    public function authoriser_store_list_view_dash($id)
    {
        try {
            // Fix N+1 query by eager loading relationships
            $sorder = Sorder::with(['enduser', 'request_by', 'user'])->find($id);
            $company = Company::first();
            // Eager load item relationship to prevent N+1 queries
            $sorder_parts = SorderPart::with(['item', 'inventoryItem.location'])->where('sorder_id', '=', $id)->get();
            $total_amount = $sorder_parts->sum('sub_total');

            // Update the total column of the Sorder record
            $sorder->total = $total_amount;

            $sorder->save();
            Log::info("StoreReqquestController | authoriser_store_list_view_dash() ", [
                'user_details' => Auth::user(),
                'response_message' => 'Authoriser Store List View Dashboard Successfully'
            ]);
            return view('stores.authoriser_store_list_view', compact('sorder', 'sorder_parts', 'company'));
        } catch (\Exception $e) {
            return $this->handleError($e, 'authoriser_store_list_view_dash()');
        }




        // dd($sorder);
    }


    public function fetch_single_enduser(Request $request, $id)
    {
        try {
            $site_id = Auth::user()->site->id;
            // $product = DB::select('select name_description,designation from endusers where  id = "' . $request->id . '"');
            $fill = DB::table('endusers')->where('id', $id)->where('site_id', '=', $site_id)->pluck('name_description');
            // return response()->json($fill);
            Log::info("StoreReqquestController | fetch_single_enduser() ", [
                'user_details' => Auth::user(),
                'request_paylaod' => $request,
                'response_payload' => $fill
            ]);
            return Response::json(['success' => true, 'info' => $fill]);
        } catch (\Exception $e) {
            return $this->handleError($e, 'fetch_single_enduser()');
        }
    }


    public function fetch_single_enduser1(Request $request, $id)
    {
        try {
            $site_id = Auth::user()->site->id;
            // $product = DB::select('select name_description,designation from endusers where  id = "' . $request->id . '"');
            $fill = DB::table('endusers')->where('id', $id)->where('site_id', '=', $site_id)->pluck('designation');
            // return response()->json($fill);
            Log::info("StoreReqquestController | fetch_single_enduser1() ", [
                'user_details' => Auth::user(),
                'request_paylaod' => $request,
                'response_payload' => $fill
            ]);
            // return response()->json($fill);
            return Response::json(['success' => true, 'info' => $fill]);
        } catch (\Exception $e) {
            return $this->handleError($e, 'fetch_single_enduser1()');
        }
    }

    public function requester_store_list_view($id)
    {
        $sorder = Sorder::find($id);
        $company = Company::first();
        $sorder_parts = SorderPart::where('sorder_id', '=', $id)->get();
        // Calculate the total amount from sorder_parts
        $total_amount = $sorder_parts->sum('sub_total');

        // Update the total column of the Sorder record
        $sorder->total = $total_amount;

        $sorder->save();
        return view('stores.requester_store_list_view', compact('sorder', 'sorder_parts', 'company'));
    }
   public function deleteSorderPart($id)
{
    try {
        // Find the sorder part by id
        $sorderPart = SorderPart::find($id);

        // Check if the part exists
        if (!$sorderPart) {
            Log::error('Sorder part not found for id: ' . $id);
            return redirect()->back()->withError('Sorder part not found.');
        }

        Log::info('Sorder part found for id: ' . $id);

        // Capture the item_id before deleting the part
        $itemId = $sorderPart->item_id;

        // Try to delete the part
        $sorderPart->delete();
        Log::info('Sorder part deleted for id: ' . $id);

        // Update the items table based on product history logic
        DB::statement("
            UPDATE items i
            LEFT JOIN (
                SELECT d.item_id, SUM(d.quantity) AS total_received
                FROM inventory_item_details d
                WHERE d.item_id = :itemId1
                GROUP BY d.item_id
            ) r ON i.id = r.item_id
            LEFT JOIN (
                SELECT sp.item_id, SUM(sp.qty_supplied) AS total_supplied
                FROM sorder_parts sp
                JOIN sorders s ON s.id = sp.sorder_id
                WHERE sp.item_id = :itemId2
                AND s.status IN ('Supplied', 'Partially Supplied')
                GROUP BY sp.item_id
            ) s ON i.id = s.item_id
            SET i.stock_quantity = COALESCE(r.total_received, 0) - COALESCE(s.total_supplied, 0)
            WHERE i.id = :itemId3;
        ", ['itemId1' => $itemId, 'itemId2' => $itemId, 'itemId3' => $itemId]);

        // Redirect back with success message
        return redirect()->back()->withSuccess('Sorder part deleted and stock quantity updated successfully.');

    } catch (\Exception $e) {
        return $this->handleError($e, 'deleteSorderPart()');
    }
}


}
