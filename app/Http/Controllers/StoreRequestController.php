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

class StoreRequestController extends Controller
{
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
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StoreRequestController | RequestSearch() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
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
                // $inventory = InventoryItem::join('items','items.id','=','inventory_items.item_id')
                $inventory = Item::join('inventory_items', 'items.id', '=', 'inventory_items.item_id')

                    ->where('items.item_description', 'like', "%" . $request->search . "%")->where('inventory_items.quantity', '>', '0')
                    ->orWhere('items.item_part_number', 'like', "%" . $request->search . "%")->where('inventory_items.quantity', '>', '0')
                    ->orWhere('items.item_stock_code', 'like', "%" . $request->search . "%")->where('inventory_items.quantity', '>', '0')
                    ->where('inventory_items.site_id', '=', $site_id)
                    ->get();

                Log::info('StoreRequestController | requester_search() | ', [
                    'user_details' => Auth::user(),
                    'response' => $inventory
                ]);

                if ($inventory->isEmpty()) {

                    return redirect()->back()->withError('Item not in stock', 'Oops');
                } elseif ($inventory->isNotEmpty()) {
                    return view('purchases.request_search', compact('inventory'));
                }
                return view('purchases.request_search', compact('inventory'));
            }
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StoreRequestController | RequesterSearch() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
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
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StoreRequestController | Cart() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
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
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StoreRequestController | AddToCart() Error ' . $unique_id, [
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
                Log::info('StoreRequestController | update()', [
                    'user_details' => Auth::user(),
                    'message' =>  'Cart updated successfully',
                    'cart' => $cart
                ]);
                session()->flash('success', 'Cart updated successfully');
            }
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StoreRequestController | Update() Error ' . $unique_id, [
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
                Log::info('StoreReqeustController() | remove()', [
                    'user_details' => Auth::user(),
                    'request_payload' => $request,
                    'message' => 'Product removed successfully '
                ]);
                session()->flash('success', 'Product removed successfully');
            }
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StoreRequestController | Remove() Error ' . $unique_id, [
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
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StoreRequestController | Store() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }

    public function store_lists()
    {
        $site_id = Auth::user()->site->id;
        $store_requests = Sorder::where('site_id', '=', $site_id)->latest()->paginate(15);
        return view('stores.index', compact('store_requests'));
    }

    public function store_list_view($id)
    {
        $sorder = Sorder::find($id);
        $company = Company::first();
        $sorder_parts = SorderPart::where('sorder_id', '=', $id)->get();
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
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StoreRequestController | GenerateSorderPDF() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
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
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StoreRequestController | StoreListEdit() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
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
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StoreRequestController | StoreListUpdate() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }


    public function sorder_update(Request $request, $id)
    {
        try {
            $request->validate([
                'qty_supplied' => 'gte:0',
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

            if ($request->qty_supplied > $quantity || $request->qty_supplied > $sorder_quantity) {

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
                Log::info('Quantity before', [
                    'Edited_by' => $authid,
                    'Details' => $request->all(),
                ]);
                if ($sorder->qty_supplied < 1) {
                    $quantity = SorderPart::where('id', '=', $request->id)->value('qty_supplied');
                    $unit_price = SorderPart::where('id', '=', $request->id)->value('unit_price');
                    $sub_total = $quantity * $unit_price;
                    SorderPart::where('id', $request->id)->update(['sub_total' => $sub_total]);
                } else {
                    $qty_supplied = SorderPart::where('id', '=', $request->id)->value('qty_supplied');
                    $unit_price = SorderPart::where('id', '=', $request->id)->value('unit_price');
                    $sub_total = $qty_supplied * $unit_price;
                    SorderPart::where('id', $request->id)->update(['sub_total' => $sub_total]);
                }

                return redirect()->back();
            }
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StoreRequestController | SorderUpdate() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
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
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StoreRequestController | StoresAction() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
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
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StoreRequestController | ApprovedStatus() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
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
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StoreRequestController | DeniedStatus() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }


    public function store_officer_lists()
    {
        try {
            $site_id = Auth::user()->site->id;
            $officer_lists = Sorder::where('approval_status', '=', 'approved')->where('site_id', '=', $site_id)->latest('id')->paginate(15);
            Log::info('StoreRequestController | store_officer_lists()', [
                'user_details' => Auth::user(),
                'response_payload' => $officer_lists
            ]);
            return view('stores.officer_lists', compact('officer_lists'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StoreRequestController | StoreOfficerLists() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
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
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('StoreRequestController | StoreRequesterLists() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
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
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('An error occurred with id ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
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
        ]);

        $deliverynum = Sorder::where('id', '=', $id)->value('request_number');

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
            DB::commit();



            return redirect()->back()->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('An error occurred with id ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            DB::rollback();

            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id);
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
            $unique_id = floor(time() - 999999999);

            // Log the error with details
            Log::channel('error_log')->error('SorderController | update_manual_remarks() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }


    public function supply_history()
    {
        try {
            $site_id = Auth::user()->site->id;

            $total_cost_of_parts_within_the_month =
                SorderPart::leftJoin('sorders', 'sorders.id', '=', 'sorder_parts.sorder_id')
                ->leftJoin('items', 'items.id', '=', 'sorder_parts.item_id')
                ->leftJoin('endusers', 'sorders.enduser_id', '=', 'endusers.id')
                ->leftJoin('inventory_items', 'sorder_parts.inventory_id', '=', 'inventory_items.id')
                ->leftJoin('inventories', 'inventory_items.inventory_id', '=', 'inventories.id')
                ->where('sorders.site_id', '=', $site_id)
                ->where('sorder_parts.site_id', '=', $site_id)
                ->whereIn('sorders.status', ['Supplied', 'Partially Supplied']);
                ->latest('sorders.delivered_on')
                ->select(
                    'sorder_parts.id',
                    'sorder_parts.qty_supplied',
                    'sorder_parts.sub_total',
                    'sorders.delivery_reference_number',
                    'sorders.delivered_on',
                    'items.item_description',
                    'items.item_part_number',
                    'items.item_stock_code',
                    'sorders.enduser_id',
                    'inventory_items.location_id',
                    'inventories.grn_number'

                )
                ->paginate(100);

            Log::info("StoreReqquestController | store_officer_update() | Sorder before edit", [
                'user_details' => Auth::user(),
                'response_message' => 'Supply history displayed succesfully'
            ]);
            // dd($total_cost_of_parts_within_the_month);
            return view('stores.supply_history', compact('total_cost_of_parts_within_the_month'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('An error occurred with id ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }

    public function supply_history_search(Request $request)
    {
        try {
            $site_id = Auth::user()->site->id;
            $start_date = Carbon::parse(request()->start_date)->toDateString();
            $end_date = Carbon::parse(request()->end_date)->toDateString();
            $total_cost_of_parts_within_the_month = null;

            if ($request->start_date && $request->end_date) {
                $total_cost_of_parts_within_the_month =
                    SorderPart::leftjoin('sorders', 'sorders.id', '=', 'sorder_parts.sorder_id')
                    ->leftjoin('items', 'items.id', '=', 'sorder_parts.item_id')
                    ->leftjoin('endusers', 'sorders.enduser_id', '=', 'endusers.id')
                    ->leftjoin('inventory_items', 'sorder_parts.inventory_id', '=', 'inventory_items.id')
                    ->leftJoin('inventories', 'inventory_items.inventory_id', '=', 'inventories.id')
                    ->whereIn('sorders.status', ['Supplied', 'Partially Supplied']);
                    ->where('sorders.site_id', '=', $site_id)
                    ->where('sorder_parts.site_id', '=', $site_id)
                    ->whereDate('sorders.delivered_on', '>=', $start_date)
                    ->whereDate('sorders.delivered_on', '<=', $end_date)
                    ->select(
                        'sorder_parts.id',
                        'sorder_parts.qty_supplied',
                        'sorder_parts.sub_total',
                        'sorders.delivery_reference_number',
                        'sorders.delivered_on',
                        'items.item_description',
                        'items.item_part_number',
                        'items.item_stock_code',
                        'endusers.asset_staff_id', // Select the required enduser column
                        'inventory_items.location_id'
                    )
                    ->latest('sorders.created_at')
                    ->paginate(10000);
            }

            Log::info("StoreReqquestController | supply_history_search()", [
                'user_details' => Auth::user(),
                'request_payload' => $request,
                'response_message' => 'Supply history search successful',
                'response_payload' => $total_cost_of_parts_within_the_month
            ]);

            return view('stores.supply_history', compact('total_cost_of_parts_within_the_month', 'start_date', 'end_date'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('An error occurred with id ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }




    public function supply_history_search_item(Request $request)
    {
        try {
            Log::info("StoreReqquestController | supply_history_search_item() ", [
                'user_details' => Auth::user(),
                //'request_message' => $message,
                'request_payload' => $request
            ]);

            $site_id = Auth::user()->site->id;

            // Initialize the query builder with your base join and where conditions
            $query = SorderPart::leftjoin('sorders', 'sorders.id', '=', 'sorder_parts.sorder_id')
                ->leftjoin('items', 'items.id', '=', 'sorder_parts.item_id')
                ->leftjoin('endusers', 'sorders.enduser_id', '=', 'endusers.id')
                ->leftjoin('inventory_items', 'sorder_parts.inventory_id', '=', 'inventory_items.id')

                ->leftJoin('inventories', 'inventory_items.inventory_id', '=', 'inventories.id')
                ->where('sorders.site_id', '=', $site_id)
                ->where('sorder_parts.site_id', '=', $site_id)
                ->whereIn('sorders.status', ['Supplied', 'Partially Supplied']);;

            // Check if a search query is provided
            $searchTerm = null;
            if ($request->search) {
                $searchTerm = '%' . $request->search . '%';
                $query->where(function ($subQuery) use ($searchTerm) {
                    $subQuery->where('endusers.asset_staff_id', 'like', $searchTerm)
                        ->orWhere('items.item_description', 'like', $searchTerm)
                        ->orWhere('items.item_part_number', 'like', $searchTerm)
                        ->orWhere('items.item_stock_code', 'like', $searchTerm);
                });
            }

            // Check if date range is provided
            $startDate = null;
            $endDate = null;
            if ($request->start_date && $request->end_date) {
                $startDate = $request->start_date;
                $endDate = $request->end_date;
                $query->whereBetween('sorders.created_at', [$startDate, $endDate]);
            }

            // Finalize the query with ordering and pagination
            $total_cost_of_parts_within_the_month = $query->latest('sorders.created_at')
                ->paginate(100)
                ->appends([
                    'search' => $request->search,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);

            Log::info("StoreReqquestController | supply_history_search_item() ", [
                'user_details' => Auth::user(),
                'request_message' => 'Supply History Search Item Successful',
                'response_payload' => $total_cost_of_parts_within_the_month
            ]);

            return view('stores.supply_history_search_item', compact('total_cost_of_parts_within_the_month'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('An error occurred with id ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
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
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('An error occurred with id ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
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
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('An error occurred with id ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }

    public function requester_store_lists()
    {
        try {
            $site_id = Auth::user()->site->id;
            $auth = Auth::id();
            $requester_store_lists = Sorder::where('requested_by', '=', $auth)->where('site_id', '=', $site_id)->latest()->paginate(15);
            Log::info("StoreReqquestController | requester_store_lists() ", [
                'user_details' => Auth::user(),
                'response_payload' => $requester_store_lists
            ]);
            return view('stores.requester_store_lists', compact('requester_store_lists'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('An error occurred with id ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
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
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('An error occurred with id ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
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
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('An error occurred with id ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
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
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('An error occurred with id ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
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
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('An error occurred with id ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
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
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('An error occurred with id ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
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
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('An error occurred with id ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }
    public function authoriser_store_list_view_dash($id)
    {
        try {
            $sorder = Sorder::find($id);
            $company = Company::first();
            $sorder_parts = SorderPart::where('sorder_id', '=', $id)->get();
            $total_amount = $sorder_parts->sum('sub_total');

            // Update the total column of the Sorder record
            $sorder->total = $total_amount;

            $sorder->save();
            Log::info("StoreReqquestController | store_officer_update() ", [
                'user_details' => Auth::user(),
                'response_message' => 'Authoriser Store List View Dashboard Successfully',
                //'response_payload' => $purchase
            ]);
            return view('stores.authoriser_store_list_view', compact('sorder', 'sorder_parts', 'company'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('An error occurred with id ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
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
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('An error occurred with id ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
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
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('An error occurred with id ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
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
}
