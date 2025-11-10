<?php

namespace App\Http\Controllers;

use \PDF;
use Carbon\Carbon;
use App\Helpers\Pay;
use App\Models\Part;
use App\Models\Site;
use App\Models\User;
use App\Models\Order;
use App\Models\Porder;
use App\Models\Company;
use App\Models\Enduser;
use App\Models\Location;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\OrderPart;
use App\Models\PorderPart;
use Illuminate\Http\Request;
use App\Helpers\UploadHelper;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Traits\LogsErrors;

class AuthoriserController extends Controller
{
    use LogsErrors;
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $site_id = Auth::user()->site->id;  // Fetch the logged-in user's site ID
     
           
    
        // If the user is a 'Department Authoriser', load the second query
        if (Auth::user()->hasRole('Department Authoriser')) {
            $department_id = Auth::user()->department->id ?? null;
            
            if ($department_id === null) {
                // User doesn't have a department assigned, return empty results
                $all_requests = collect([]);
            } else {
                $all_requests = Order::leftJoin('users', 'users.id', '=', 'orders.user_id')
                    ->where('orders.status', '=', 'Requested')
                    ->where('orders.site_id', '=', $site_id)
                    ->where('users.department_id', '=', $department_id)
                    ->get();
            }

            return view('authoriser.index', compact('all_requests'));
        }
        $purchases = Order::where('site_id', '=', $site_id)->latest()->paginate(10);
        return view('authoriser.index', compact('purchases'));
    
    }
    

    public function create()
    {
        return view('authoriser.create');
    }


    public function store(Request $request)
    {
        try {
            $authid = Auth::id();
            $site_id = Auth::user()->site->id;
            $request->validate([
                'photo' => 'sometimes|nullable|image|mimes:jpeg,gif,png,jpg|max:9048',
            ]);
            $purchase =  Purchase::create([
                'part_id' => $request->part_id,
                'description' => $request->description,
                'quantity' => $request->quantity,
                'make' => $request->make,
                'model' => $request->model,
                'serial_number' => $request->serial_number,
                'tax' => $request->tax,
                'tax2' => $request->tax2,
                'tax3' => $request->tax3,
                'intended_recipient' => $request->intended_recipient,
                'unit_price' => $request->unit_price,
                'currency' => $request->currency,
                'supplier_id' => $request->supplier_id,
                'comments' => $request->comments,
                'type_of_purchase' => $request->type_of_purchase,
                'enduser_id' => $request->enduser_id,
                'status' => $request->status,
                'user_id' => $authid,
                'site_id' => $site_id,

            ]);
            if ($request->image) {
                $imageName = UploadHelper::upload($request->image, 'purchase-' . $purchase->id, 'images/purchases');
                $purchase->image = $imageName;
                $purchase->save();
            }
            Log::info('AuthoriserController | store() |Pruchase Created Successfully ');
            return redirect()->route('purchases.index')->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            return $this->handleError($e, 'store()');
        }
    }


    public function show($id)
    {
        try {
            $company = Company::first();
            $purchase = Order::where('id', '=', $id)->first();
            $order_parts = OrderPart::where('order_id', '=', $id)->get();
            Log::info('AuthoriserController | show() ', [
                'user_details' => Auth::user(),
                'message' => 'Authoriser page displyaed successfully',
                'company' => $company,
                'purchase' => $purchase,
                'order_parts' => $order_parts
            ]);
            return view('authoriser.show', compact('purchase', 'order_parts', 'company'));
        } catch (\Exception $e) {
            return $this->handleError($e, 'show()');
        }
    }


    public function edit($id)
    {
        try {
            $site_id = Auth::user()->site->id;
            $purchase = Order::find($id);
            $suppliers = Supplier::all();
            $sites = Site::where('site_id', '=', $site_id)->get();
            $locations = Location::where('site_id', '=', $site_id)->get();
            $parts = Part::where('site_id', '=', $site_id)->get();
            $endusers = Enduser::where('site_id', '=', $site_id)->get();
            $order_parts = OrderPart::where('order_id', '=', $id)->where('site_id', '=', $site_id)->get();
            Log::info('AuthoriserController| Edit() | ', [
                'User Details' => Auth::user(),
                'Message' => 'AuthoriserController| edit() Edit Page Loaded Succsfully'
            ]);
            return view('authoriser.edit', compact('purchase', 'suppliers', 'sites', 'locations', 'parts', 'endusers', 'order_parts'));
        } catch (\Exception $e) {
            return $this->handleError($e, 'edit()');
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $purchase = Order::find($id);
            Log::info('AuthoriserController| Update() | Before Authoriser Update', [
                'details' => Order::find($id)
            ]);
            $authid = Auth::id();
            $purchase->part_id = $request->part_id; //2
            $purchase->tax = $request->tax; //10
            $purchase->tax2 = $request->tax2; //11
            $purchase->tax3 = $request->tax3; //12
            $purchase->supplier_id = $request->supplier_id; //1
            $purchase->type_of_purchase = $request->type_of_purchase; //3
            $purchase->enduser_id = $request->enduser_id; //4
            $purchase->status = $request->status;
            $purchase->user_id = $authid;
            $purchase->save();

            Log::info('AuthoriserController | Update() | After Edit', [
                'user_details' => Auth::user(),
                'details' => $request->all(),
            ]);
            return redirect()->route('home')->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            return $this->handleError($e, 'update()');
        }
    }

    public function destroy($id)
    {
        try {
            $purchase = Order::find($id);
            Order::where("id", $purchase->id)->delete();
            OrderPart::where("order_id", $purchase->id)->delete();
            // $purchase->destroy();
            Log::success('AuthoriserController| Destroy()| Successfully Updated', 'Sucess');
            return redirect()->route('purchases.index')->withSuceess('Successfully Updated');
        } catch (\Exception $e) {
            return $this->handleError($e, 'destroy()');
        }
    }

    public function selectPart(Request $request)
    {
        try {
            $movies = Part::all();
            if ($request->has('q')) {
                $search = $request->q;
                $movies = Part::select("id", "name")
                    ->where('name', 'LIKE', "%$search%")
                    ->get();
            }
            return response()->json($movies);
        } catch (\Exception $e) {
            return $this->handleError($e, 'selectPart()');
        }
    }


    public function selectSearch(Request $request)
    {
        try {
            $site_id = Auth::user()->site->id;
            $movies = Supplier::all();
            if ($request->has('q')) {
                $search = $request->q;
                $movies = Supplier::select("id", "name")
                    ->where('name', 'LIKE', "%$search%")->orWhere('phone', 'LIKE', "%$search%")
                    ->get();
            }

            Log::info('AuthoriserController | selectSearch() | ', [
                'user_details' => Auth::user(),
                'message' =>  'Supplier searched successfully'
            ]);
            return response()->json($movies);
        } catch (\Exception $e) {
            return $this->handleError($e, 'selectSearch()');
        }
    }
    public function selectEnduser(Request $request)
    {
        try {
            $site_id = Auth::user()->site->id;
            $movies = Enduser::where('site_id', '=', $site_id)->get();

            if ($request->has('q')) {
                $search = $request->q;
                $movies = Enduser::select("id", "name")
                    ->where('name', 'LIKE', "%$search%")->orWhere('phone', 'LIKE', "%$search%")
                    ->where('site_id', '=', $site_id)
                    ->get();
            }
            return response()->json($movies);
        } catch (\Exception $e) {
            return $this->handleError($e, 'selectEnduser()');
        }
    }

    public function selectRequester(Request $request)
    {
        try {
            $site_id = Auth::user()->site->id;
            $movies = User::where('role_id', '=', '3')->where('site_id', '=', $site_id)->get();

            if ($request->has('q')) {
                $search = $request->q;
                $movies = User::select("id", "name")
                    ->where('name', 'LIKE', "%$search%")->orWhere('phone', 'LIKE', "%$search%")
                    ->where('site_id', '=', $site_id)
                    ->get();
            }
            return response()->json($movies);
        } catch (\Exception $e) {
            return $this->handleError($e, 'selectRequester()');
        }
    }

    // requester dashboard links
    public function requested()
    {
        try {
            $site_id = Auth::user()->site->id;
            $authid = Auth::id();
            // $purchase = Purchase::find($id);
            $requested = Order::where('user_id', '=', $authid)->where('status', '=', 'Requested')->where('site_id', '=', $site_id)->paginate(15);
            return view('authoriser.requested', compact('requested'));
            // dd($requested);
            // return ('hellow word');
        } catch (\Exception $e) {
            return $this->handleError($e, 'requested()');
        }
    }

    public function initiated()
    {
        try {
            $site_id = Auth::user()->site->id;
            $authid = Auth::id();
            $initiated = Purchase::where('user_id', '=', $authid)->where('status', '=', 'Initiated')->where('site_id', '=', $site_id)->paginate(15);
            return view('authoriser.initiated', compact('initiated'));
        } catch (\Exception $e) {
            return $this->handleError($e, 'initiated()');
        }
    }
    public function approved()
    {
        try {
            $site_id = Auth::user()->site->id;
            $authid = Auth::id();
            $approved = Order::where('user_id', '=', $authid)->where('status', '=', 'Approved')->where('site_id', '=', $site_id)->paginate(15);
            return view('authoriser.approved', compact('approved'));
        } catch (\Exception $e) {
            return $this->handleError($e, 'approved()');
        }
    }
    public function ordered()
    {
        $site_id = Auth::user()->site->id;
        $authid = Auth::id();
        $ordered = Order::where('user_id', '=', $authid)->where('status', '=', 'Ordered')->where('site_id', '=', $site_id)->paginate(15);
        return view('authoriser.ordered', compact('ordered'));
    }
    public function delivered()
    {
        $site_id = Auth::user()->site->id;
        $authid = Auth::id();
        $delivered = Order::where('user_id', '=', $authid)->where('status', '=', 'Supplied')->where('site_id', '=', $site_id)->paginate(15);
        return view('authoriser.delivered', compact('delivered'));
    }
    public function req_all()
    {
        $site_id = Auth::user()->site->id;
        $authid = Auth::id();
        $req_all = Order::where('user_id', '=', $authid)->where('site_id', '=', $site_id)->latest()->paginate(15);
        return view('authoriser.reqall', compact('req_all'));
    }

    // store_officer purchase pages
    public function all_requests()
    {
        $site_id = Auth::user()->site->id;
        $department_id = Auth::user()->department_id;
        if(Auth::user()->hasRole('Department Authoriser')){
            $all_requests = Order::leftjoin('users','users.id','=','orders.user_id')->
            where('users.department_id','=',$department_id)->
            where('orders.status', '=', 'Requested')->where('orders.site_id', '=', $site_id)->latest('orders.created_at')->paginate(15);
        }
        $all_requests = Order::where('status', '=', 'Requested')->where('site_id', '=', $site_id)->latest()->paginate(15);
        return view('authoriser.all_requests', compact('all_requests'));
    }

    public function all_initiates()
    {
        $site_id = Auth::user()->site->id;
        $all_initiates = Order::where('status', '=', 'Initiated')->where('site_id', '=', $site_id)->latest()->paginate(15);
        return view('authoriser.all_initiates', compact('all_initiates'));
    }
    public function all_approves()
    {
        $site_id = Auth::user()->site->id;
        $all_approves = Order::where('status', '=', 'Approved')->where('site_id', '=', $site_id)->latest()->paginate(15);
        return view('authoriser.all_approves', compact('all_approves'));
    }
    public function all_orders()
    {
        $site_id = Auth::user()->site->id;
        $all_orders = Order::where('status', '=', 'Ordered')->where('site_id', '=', $site_id)->latest()->paginate(15);
        return view('authoriser.all_orders', compact('all_orders'));
    }
    public function all_delivers()
    {
        $site_id = Auth::user()->site->id;
        $all_delivers = Order::where('status', '=', 'Supplied')->where('site_id', '=', $site_id)->latest()->paginate(15);
        return view('authoriser.all_delivers', compact('all_delivers'));
    }
    // end of store_officer purchases pages


    public function generate_order($id)
    {
        try {
            // Find the existing order by ID
            $purchase_order = Order::find($id);

            if (!$purchase_order) {
                return back()->withError('Order not found');
            }

            // Replicate the order and save it as a new purchase order
            $new_purchase_order = $purchase_order->replicate();
            $new_purchase_order->setTable('porders');
            $new_purchase_order->order_id = $id;
            $purchase_order_number = Pay::genPurchaseCode();
            $new_purchase_order->purchasing_order_number = $purchase_order_number;
            $new_purchase_order->save();

            // Replicate related order parts and save them under the new purchase order number
            $fks = OrderPart::where('order_id', '=', $id)->get();
            foreach ($fks as $fk) {
                $orderpart = $fk->replicate();
                $orderpart->setTable('porder_parts');
                $orderpart->purchasing_order_number = $purchase_order_number;
                $orderpart->save();
                Log::success('AuthoriserController | generate_order() Success');
            }

            // Redirect back with a success message
            return redirect()->back()->withSuccess('Order generated successfully.');
        } catch (\Exception $e) {
            // Generate a unique error ID for logging
            return $this->handleError($e, 'generate_order()');
        }
    }


    public function purchase_list()
    {
        $site_id = Auth::user()->site->id;
        $purchase_lists = Porder::where('site_id', '=', $site_id)->latest()->paginate(15);
        return view('authoriser.list', compact('purchase_lists'));
    }
    //edit purchase list
    public function purchase_edit($id)
    {
        $site_id = Auth::user()->site->id;
        $purchase = Porder::find($id);
        $orderid = Porder::where('id', '=', $id)->value('order_id');

        $suppliers = Supplier::all();
        $sites = Site::where('site_id', '=', $site_id)->get();
        $locations = Location::where('site_id', '=', $site_id)->get();
        $parts = Part::where('site_id', '=', $site_id)->get();
        $endusers = Enduser::where('site_id', '=', $site_id)->get();
        $order_parts = OrderPart::where('order_id', '=', $orderid)->where('site_id', '=', $site_id)->get();
        return view('authoriser.purchase_edit', compact('purchase', 'suppliers', 'sites', 'locations', 'parts', 'endusers', 'order_parts'));
    }


    public function purchase_update(Request $request, $id)
    {
        try {
            $purchase = Porder::find($id);
            Log::info('Purchase Order Edit', [
                'before_purchase_order_edit' => Porder::find($id),
            ]);
            $authid = Auth::id();
            $purchase->part_id = $request->part_id; //2
            $purchase->tax = $request->tax; //10
            $purchase->tax2 = $request->tax2; //11
            $purchase->tax3 = $request->tax3; //12
            $purchase->supplier_id = $request->supplier_id; //1
            $purchase->type_of_purchase = $request->type_of_purchase; //3
            $purchase->enduser_id = $request->enduser_id; //4
            $purchase->status = $request->status;
            $purchase->user_id = $authid;
            $purchase->save();
            return redirect()->back()->withSucess('Successfully Updated:');
        } catch (\Exception $e) {
            return $this->handleError($e, 'purchase_update()');
        }
    }

    public function purchase_destroy($id)
    {
        try {
            // Find the purchase order by ID
            $purchase = Porder::find($id);

            if (!$purchase) {
                return redirect()->back()->withError('Purchase order not found');
            }

            // Delete the purchase order
            Porder::where("id", $purchase->id)->delete();

            // Log the user who deleted the record
            $user = auth()->user();
            Log::info('Purchase order deleted by user ID: ' . $user->id . ', Email: ' . $user->email . ', Purchase ID: ' . $id);

            // Success message
            return back()->withSuccess('Successfully Deleted:)');
        } catch (\Throwable $th) {
            // Generate a unique error ID
            return $this->handleError($e, 'purchase_destroy()');
        }
    }



    // purchase list show record
    public function showlist($id)
    {
        $company = Company::first();
        $purchase = Porder::where('id', '=', $id)->first();
        $orderid = Porder::where('id', '=', $id)->value('order_id');
        $purchasing_order_number = Porder::where('id', '=', $id)->value('purchasing_order_number');
        $order_parts = PorderPart::where('order_id', '=', $orderid)->where('purchasing_order_number', '=', $purchasing_order_number)->get();
        $grandtotal = PorderPart::where('order_id', '=', $orderid)->where('purchasing_order_number', '=', $purchasing_order_number)->sum('sub_total');
        return view('authoriser.showlist', compact('purchase', 'order_parts', 'company', 'grandtotal'));
    }

    // purchase list edit record
    public function editlist($id)
    {
        $purchase = Porder::find($id);
        $orderid = Porder::where('id', '=', $id)->value('order_id');
        $purchasing_order_number = Porder::where('id', '=', $id)->value('purchasing_order_number');
        $suppliers = Supplier::all();
        $sites = Site::all();
        $locations = Location::all();
        $parts = Part::all();
        $endusers = Enduser::all();
        $order_parts = PorderPart::where('order_id', '=', $orderid)->where('purchasing_order_number', '=', $purchasing_order_number)->get();
        $grandtotal = PorderPart::where('order_id', '=', $orderid)->where('purchasing_order_number', '=', $purchasing_order_number)->sum('sub_total');
        return view('authoriser.editlist', compact('purchase', 'suppliers', 'sites', 'locations', 'parts', 'endusers', 'order_parts', 'grandtotal'));

        // dd($grandtotal);
    }


    public function purchaselist_update(Request $request)
    {
        try {
            if ($request->ajax()) {

                PorderPart::findOrFail($request->pk)
                    ->update([
                        $request->quantity => $request->value

                    ]);

                return response()->json(['success' => true]);
            }
        } catch (\Throwable $e) {
            return $this->handleError($e, 'purchaselist_update()');
        }
    }

    public function action(Request $request)
    {
        try {
            if ($request->ajax()) {
                if ($request->action == 'edit') {
                    $data = array(

                        'quantity'        =>    $request->quantity,
                        'unit_price'        =>    $request->unit_price
                    );
                    Log::info('Action', [
                        'details' => $data
                    ]);
                    $ndata =   DB::table('porder_parts')
                        ->where('id', $request->id)
                        ->update($data);
                    Log::info('After Action', [
                        'details' => $ndata
                    ]);
                    $quantity = PorderPart::where('id', '=', $request->id)->value('quantity');
                    $unit_price = PorderPart::where('id', '=', $request->id)->value('unit_price');
                    $sub_total = $quantity * $unit_price;
                    PorderPart::where('id', $request->id)->update(['sub_total' => $sub_total]);
                }

                if ($request->action == 'delete') {
                    // Retrieve the record that is going to be deleted
                    $porderPart = DB::table('porder_parts')->where('id', $request->id)->first();

                    if ($porderPart) {
                        // Log the details of the record that will be deleted
                        Log::info('Deleted porder part', ['id' => $porderPart->id, 'details' => $porderPart]);

                        // Delete the record
                        DB::table('porder_parts')->where('id', $request->id)->delete();
                    } else {
                        Log::warning('Attempted to delete porder part but record not found', ['id' => $request->id]);
                    }
                }


                return response()->json($request);
            }
        } catch (\Exception $e) {
            return $this->handleError($e, 'action()');
        }
    }

    public function approved_status($id)
    {
        try {
            $authid = Auth::id();
            $approved_on = Carbon::now();
            $order = Order::find($id);
            Order::where('id', '=', $id)->update(['approval_status' => 'Approved', 'approved_by' => $authid, 'approved_on' => $approved_on]);

            Log::info('AuthoriserController | approved_status() | Order Approval Status Updated to Approved', [
                'user_details' => Auth::user(),
                'order_id' => $id,
            ]);

            return redirect()->back()->withSucess('Successfully Updated');
        } catch (\Exception $e) {
            return $this->handleError($e, 'approved_status()');
        }
    }

    public function depart_auth_approved_status($id)
    {
        try {
            $authid = Auth::id();
            $depart_auth_approved_on = Carbon::now();
            $order = Order::find($id);
            Order::where('id', '=', $id)->update(['depart_auth_approval_status' => 'Approved', 'depart_auth_approved_by' => $authid, 'depart_auth_approved_on' => $depart_auth_approved_on]);

            Log::info('AuthoriserController | depart_auth_approved_status() | depart auth Order Approval Status Updated to Approved', [
                'user_details' => Auth::user(),
                'order_id' => $id,
            ]);

            return redirect()->back()->withSucess('Successfully Updated');
        } catch (\Exception $e) {
            return $this->handleError($e, 'depart_auth_approved_status()');
        }
    }


    public function denied_status($id)
    {
        try {
            $order = Order::find($id);
            $authid = Auth::id();
            $approved_on = Carbon::now()->toDateTimeString();
            Order::where('id', '=', $id)->update(['approval_status' => 'Denied', 'approved_by' => $authid, 'approved_on' => $approved_on]);

            Log::info('AuthoriserController | denied_status() | Order Approval Status Updated to Denied', [
                'user_details' => Auth::user(),
                'order_id' => $id,
            ]);

            return redirect()->back()->withSucess('Successfully Updated');
        } catch (\Exception $e) {
            return $this->handleError($e, 'denied_status()');
        }
    }
    public function depart_auth_denied_status($id)
    {
        try {
            $order = Order::find($id);
            $authid = Auth::id();
            $denied_on = Carbon::now()->toDateTimeString();
            Order::where('id', '=', $id)->update(['depart_auth_approval_status' => 'Denied', 'depart_auth_denied_by' => $authid, 'depart_auth_denied_on' => $denied_on]);

            Log::info('AuthoriserController | depart_auth_denied_status() | Order Approval Status Updated to Denied', [
                'user_details' => Auth::user(),
                'order_id' => $id,
            ]);

            return redirect()->back()->withSucess('Successfully Updated');
        } catch (\Exception $e) {
            return $this->handleError($e, 'depart_auth_denied_status()');
        }
    }
    // generate pdf to send to supplier
    // public function generatePDF($id)
    // {
    //     $purchase = Order::find($id);

    //     $order_parts = OrderPart::where('order_id', '=', $id)->get();

    //     $purchase = PDF::loadView('purchases.pdf', compact('purchase', 'order_parts'))->setOptions(['defaultFont' => 'sans-serif']);
    //     return $purchase->download('purchase.pdf');
    // }

    // public function generatePurchaseOrderPDF($id)
    // {
    //     $purchase = Porder::find($id);
    //     $company= Company::first();
    //     $order_parts = PorderPart::where('order_id', '=', $id)->get();
    //     $purchasing_order_number = Porder::where('id', '=', $id)->value('purchasing_order_number');
    //     $grandtotal = PorderPart::where('order_id', '=', $id)->where('purchasing_order_number', '=', $purchasing_order_number)->sum('sub_total');
    //     $purchase = PDF::loadView('purchases.porderpdf', compact('purchase', 'order_parts','grandtotal','company'))->setOptions(['defaultFont' => 'sans-serif']);
    //     return $purchase->download('purchase.porderpdf');
    // }


}
