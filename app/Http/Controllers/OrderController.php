<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Uom;
use App\Helpers\Pay;
use App\Models\Part;
use App\Models\Site;
use App\Models\User;
use App\Models\Order;
use App\Models\Company;
use App\Models\Enduser;
use App\Models\Location;
use App\Models\Supplier;
use App\Models\OrderPart;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helpers\UploadHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Traits\LogsErrors;

class OrderController extends Controller
{
    use LogsErrors;
    
    public function __construct(){
        $this->middleware('auth');
    }
    public function index()
    {
        try {
            $site_id = Auth::user()->site->id;
            $orders = Order::select("*")->where('site_id','=',$site_id)->orderBy('id', 'desc')->paginate(10);
          
            Log::info('OrderController | index() ', [
                'user_details' => Auth::user(),
                'message' => 'Orders loaddes succesfully',
                'response_payload' => $orders
            ]);

            return view('orders.index', compact('orders'));
            // dd($orders);
        } catch (\Exception $e) {
            return $this->handleError($e, 'index()');
}

    }

    
    public function create()
    {
        try {
            $site_id = Auth::user()->site->id;
            $customers = Supplier::all();
            $uom = Uom::all();
            $products = Part::where('site_id','=',$site_id)->get();
            $request_number = Pay::genRefCode();
            $requested_date = Carbon::now()->toDateTimeString();
            $request_date = Carbon::createFromFormat('Y-m-d H:i:s', $requested_date)
                ->format('d-m-Y (H:i)');
            Log::info('OrderController | create()', [
                'user_details' => Auth::user(),
                'messasge' => 'Create Order Page Loaded Successfully'
            ]);
            return view('orders.create', compact('customers', 'products', 'request_number', 'request_date','uom'));
        } catch (\Exception $e) {
            return $this->handleError($e, 'create()');
}

    }

    public function admincreate()
    {
        try {
            $site_id = Auth::user()->site->id;
            $customers = Supplier::all();
            $products = Part::where('site_id','=',$site_id)->get();
            $request_number = Pay::genRefCode();
            $request_date = Carbon::now()->toDateTimeString();
            Log::info('OrderController | create()', [
                'user_details' => Auth::user(),
                'message' => 'Admin Create Order Page Loaded Successfully',
                'payload' => $request_number + $request_date
            ]);
            return view('orders.admincreate', compact('customers', 'products', 'request_number', 'request_date'));
        } catch (\Exception $e) {
            return $this->handleError($e, 'admincreate()');
}

        }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'photo' => 'sometimes|nullable|image|mimes:jpeg,gif,png,jpg|max:9048',
                'desc' => 'nullable',
            ]);
            $request_date = Carbon::now()->toDateTimeString();
            $authid = Auth::id();
            $site_id = Auth::user()->site->id;
            $statusvalue = 'Requested';
            $order = Order::create([
                'tax'    => $request->tax,
                'tax2'    => $request->tax2,
                'tax3'    => $request->tax3,
                'currency'    => $request->currency,
                'supplier_id' => $request->supplier_id,
                'type_of_purchase' => $request->type_of_purchase,
                'enduser_id' => $request->enduser_id,
                'status' => $statusvalue,
                'request_date' => $request_date,
                'user_id' => $authid,
                'request_number' => $request->request_number,
                'work_order_ref' => $request->work_order_ref,
                'site_id'=>$site_id,
            ]);


            if ($request->image) {
                $imageName = UploadHelper::upload($request->image, 'order-' . $order->id, 'images/orders');
                $order->image = $imageName;
                $order->save();
            }
            if ($order) {
                $products = ['products' => $request->products, 'quantity' => $request->quantity, 'description' => $request->description, 'make' => $request->make, 'model' => $request->model, 'serial_number' => $request->serial_number, 'unit_price' => $request->unit_price, 'comments' => $request->comments, 'remarks' => $request->remarks, 'priority' => $request->priority, 'part_number' => $request->part_number,'site_id'=>$request->site_id,'uom_id'=>$request->uom_id];

                for ($i = 0; $i < (count($products['products'])); $i++) {
                    OrderPart::create([
                        'order_id'   => $order->id,
                        'part_id' => $products['products'][$i],
                        'description' => $products['description'][$i],
                        'quantity' => $products['quantity'][$i],
                        // 'make' => $products['make'][$i],
                        // 'model' => $products['model'][$i],
                        // 'serial_number' => $products['serial_number'][$i],
                        'unit_price' => $products['unit_price'][$i],
                        // 'comments'   => $products['comments'][$i],
                        'remarks'   => $products['remarks'][$i],
                        'priority'   => $products['priority'][$i],
                        // 'uom'   => $products['uom'][$i],
                        'uom_id'   => $products['uom_id'][$i],
                        'part_number'   => $products['part_number'][$i],
                        'site_id'=>$site_id,
                    ]);
                }
            }

            $orderUserId = Order::latest()->value('user_id');
            $authId = Auth::user()->name;
            Log::info(
                'OrderController | store() | Added an Order',
                [
                    'user_details' => Auth::user(),
                    'user_name' => $authId,
                    'request_payload' => $request->all(),
                ]
            );
            $title = 'You have a new request from';
            Notification::create([
                'title' => $title,
                'user_id' => $orderUserId,
            ]);

            return back()->withSuccess('Order #' . $order->id . ' Placed Successfully');
        } catch (\Exception $e) {
            return $this->handleError($e, 'store()');
}

        }
    
    // admin ordering a request

    public function orderstore(Request $request)
    {
        try {
            $request->validate([
                'photo' => 'sometimes|nullable|image|mimes:jpeg,gif,png,jpg|max:9048',
                'desc' => 'nullable',
            ]);
            $site_id = Auth::user()->site->id;
            // $authid = Auth::id();
            $statusvalue = 'Requested';
            $order = Order::create([
                'tax'    => $request->tax,
                'tax2'    => $request->tax2,
                'tax3'    => $request->tax3,
                'currency'    => $request->currency,
                'supplier_id' => $request->supplier_id,
                'type_of_purchase' => $request->type_of_purchase,
                'enduser_id' => $request->enduser_id,
                'status' => $statusvalue,
                'request_date' => $request->request_date,
                'user_id' => $request->user_id,
                'request_number' => $request->request_number,
                'work_order_ref' => $request->work_order_ref,
                'site_id'=>$site_id,
            ]);


            if ($request->image) {
                $imageName = UploadHelper::upload($request->image, 'order-' . $order->id, 'images/orders');
                $order->image = $imageName;
                $order->save();
            }
            if ($order) {
                $products = ['products' => $request->products, 'quantity' => $request->quantity, 'description' => $request->description, 'make' => $request->make, 'model' => $request->model, 'serial_number' => $request->serial_number, 'unit_price' => $request->unit_price, 'comments' => $request->comments, 'remarks' => $request->remarks, 'priority' => $request->priority, 'prefix' => $request->prefix, 'part_number' => $request->part_number,'site_id'=>$request->site_id];

                for ($i = 0; $i < (count($products['products'])); $i++) {
                    OrderPart::create([
                        'order_id'   => $order->id,
                        'part_id' => $products['products'][$i],
                        'description' => $products['description'][$i],
                        'quantity' => $products['quantity'][$i],
                        // 'make' => $products['make'][$i],
                        // 'model' => $products['model'][$i],
                        // 'serial_number' => $products['serial_number'][$i],
                        'unit_price' => $products['unit_price'][$i],
                        // 'comments'   => $products['comments'][$i],
                        'remarks'   => $products['remarks'][$i],
                        'priority'   => $products['priority'][$i],
                        'prefix'   => $products['prefix'][$i],
                        'part_number'   => $products['part_number'][$i],
                        'site_id'=>$site_id,
                    ]);
                }
            }

            $orderUserId = Order::latest()->value('user_id');
            $authId = Auth::user()->name;
            Log::info(
                'OrderController | orderStore() | Added an Order',
                [
                    'user_details' => Auth::user(),
                    'user_name' => $authId,
                    'request_payload' => $request->all(),
                    'message' => 'Order Placed Successfully'
                ]
            );
            $title = 'You have a new request from';
            Notification::create([
                'title' => $title,
                'user_id' => $orderUserId,

            ]);

            return back()->withSuccess('Order #' . $order->id . ' Placed Successfully');
        } catch (\Exception $e) {
            return $this->handleError($e, 'orderstore()');
}

    }

    public function show($id)
    {
        try {
            $order = Order::find($id);
            $company = Company::first();
            $order_parts = OrderPart::where('order_id', '=', $id)->get();
            Log::info('OrderController | show', [
                'user_details' => Auth::user(),
                'message' => 'Order details retrieved successfully.',
                'response_payload' => $order,
                'order_id' => $id,
            ]);
            return view('orders.show', compact('order', 'company', 'order_parts'));
        } catch (\Throwable $e) {
            return $this->handleError($e, 'show()');
}

    }

    public function view($id)
    {
        try {
            $order = Order::join('order_products', 'orders.id', '=', 'order_products.order_id')->where('order_products.id', '=', $id)->first();
            Log::info('OrderController | view', [
                'user_details' => Auth::user(),
                'message' => 'Order details for a specific product retrieved successfully.',
                'order_product_id' => $id,
            ]);
            return view('orders.view', compact('order'));
        } catch (\Throwable $e) {
            return $this->handleError($e, 'view()');
}

        
    }
    
    public function edit( $id)
    {
        try{
            $site_id = Auth::user()->site->id;
            $purchase = Order::find($id);
            $suppliers = Supplier::all();
            $sites = Site::all();
            $uom = Uom::all();
            $locations = Location::where('site_id','=',$site_id)->get();
            $parts = Part::where('site_id','=',$site_id)->get();
            $endusers = Enduser::where('site_id','=',$site_id)->get();
            $order_parts = OrderPart::where('order_id', '=', $id)->get();
            return view('orders.edit', compact('purchase', 'suppliers', 'sites', 'locations', 'parts', 'endusers', 'order_parts','uom'));
        }catch (\Exception $e){
            return $this->handleError($e, 'edit()');
}

       
    }


   
    public function update(Request $request, $id)
    {
        try {
            $purchase = Order::find($id);
            // $authid = Auth::id();
            $purchase->part_id = $request->part_id; //2
            $purchase->tax = $request->tax; //10
            $purchase->tax2 = $request->tax2; //11
            $purchase->tax3 = $request->tax3; //12
            $purchase->supplier_id = $request->supplier_id; //1
            $purchase->type_of_purchase = $request->type_of_purchase; //3
            $purchase->enduser_id = $request->enduser_id; //4
            $purchase->status = $request->status;
            // $purchase->user_id = $authid;
            $purchase->save();
            Log::info('Details', [
                'Details' => $request->all(),
            ]);
           
            return redirect()->back()->withSuccess('Successfully Updated');
        } catch (\Throwable $e) {
            return $this->handleError($e, 'update()');
}

    }

   
    public function destroy(Order $order)
    {
        try {
            if ($order->delete()) {
                Log::info('OrderController | destroy', [
                    'user_details' => Auth::user(),
                    'message' => 'Order deleted successfully',
                    'order_id' => $order->id,
                ]);
                return back()->with('success', 'Order deleted successfully');
            } else {
                Log::warning('OrderController | destroy', [
                    'user_details' => Auth::user(),
                    'message' => 'Failed to delete order',
                    'order_id' => $order->id,
                ]);
                return back()->with('error', 'Whoops! Something went wrong.');
            }
        } catch (\Throwable $e) {
            return $this->handleError($e, 'destroy()');
}

    }
    public static function fetch_products()
    {
        $site_id = Auth::user()->site->id;
        $products = Part::where('quantity', '>=', 0)->where('site_id','=', $site_id)->get();
        $output = '';
        foreach ($products as $product) {
            $output .= '<option value="' . $product->id . '">' . $product->name . '</option>';
        }
        return $output;
        // return response()->json($products);
    }


    public function fetch_single_product(Request $request)
    {
        try {
            $site_id = Auth::user()->site->id;

            $product = DB::select('select price,quantity from products where id = ? and site_id = ?', [$request->id, $site_id]);
            
            return response()->json($product);
        } catch (\Throwable $e) {
            return $this->handleError($e, 'fetch_single_product()');
}

    }


    public function orders_search(Request $request)
    {
        try {
            $site_id = Auth::user()->site->id;

$product = DB::select('select price,quantity from products where id = ? and site_id = ?', [$request->id, $site_id]);

            $userDetails = Auth::check() ? Auth::user() : 'Guest';

            if ($request->search) {
                $orders = Order::select('orders.id as id', 'orders.status as status', 'users.name as user', 'orders.created_at as created_at', 'clients.name as name')->join('clients', 'orders.client_id', '=', 'clients.id')->where('clients.phone', 'like', "%" . $request->search . "%")->orWhere('clients.name', 'like', "%" . $request->search . "%")->where('orders.site_id','=',$site_id)->join('users', 'orders.order_by', '=', 'users.id')->get();
            } else {
                $orders = Order::select("*")->where('orders.site_id','=',$site_id)->orderBy('id', 'desc')->paginate(10);
            }

            Log::info('OrderController | orders_search()', [
                'user_details' => Auth::user(),
                'request_payload' => $request,
                'message' => 'Orders search completed successfully.',
            ]);

            return view('orders.search', compact('orders'));
        } catch (\Throwable $e) {
            return $this->handleError($e, 'orders_search()');
}

    }
    public function orders_action(Request $request, $id)
    {
        try {

            $order = OrderPart::find($id);
            $order->description = $request->description;
            $order->part_number = $request->part_number;
            $order->uom = $request->uom;
            $order->uom_id = $request->uom_id;
            $order->quantity = $request->quantity;
            $order->remarks = $request->remarks;
            $order->priority = $request->priority;
            $order->save();
      
            return redirect()->back()->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            return $this->handleError($e, 'orders_action()');
}

    }
}
