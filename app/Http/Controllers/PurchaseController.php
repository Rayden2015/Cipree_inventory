<?php

namespace App\Http\Controllers;

use PDF;
use Carbon\Carbon;
use App\Models\Tax;
use App\Helpers\Pay;
use App\Models\Levy;
use App\Models\Part;
use App\Models\Site;
use App\Models\User;
use App\Models\Order;
use App\Models\Porder;
use App\Models\Company;
use App\Models\DpPoTax;
use App\Models\Enduser;
use App\Models\Category;
use App\Models\Location;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Inventory;
use App\Models\OrderPart;
use App\Models\PorderPart;
use Illuminate\Http\Request;
use App\Helpers\UploadHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Traits\LogsErrors;

class PurchaseController extends Controller
{
    use LogsErrors;
    
    public function __construct(){
        $this->middleware('auth');
    }
    
    public function index()
    {
        $site_id = Auth::user()->site->id;
        $purchases = Order::where('site_id','=',$site_id)->latest()->paginate(10);
        return view('purchases.index', compact('purchases'));
    }

   
    public function create()
    {
        return view('purchases.create');
    }

   
    public function store(Request $request)
    {
        try {
            $authid = Auth::id();
            $request->validate([
                'photo' => 'sometimes|nullable|image|mimes:jpeg,gif,png,jpg|max:9048',

            ]);
            $site_id = Auth::user()->site->id;
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
            Log::info('Purchase Order Details', [
                'Details' => $request->all(),
            ]);
            if ($request->image) {
                $imageName = UploadHelper::upload($request->image, 'purchase-' . $purchase->id, 'images/purchases');
                $purchase->image = $imageName;
                $purchase->save();
            }
            
            return redirect()->route('purchases.index')->withSuccess('Successfully Updated');
        } catch (\Throwable $e) {
            return $this->handleError($e, 'store()');
}

    }
    public function show($id)
    {
        $company = Company::first();
        $purchase = Porder::where('id', '=', $id)->first();
        $order_parts = PorderPart::where('order_id', '=', $id)->get();
        return view('purchases.show', compact('purchase', 'order_parts', 'company'));
        // dd($purchase);
    }

  
    public function edit($id)
    {
        try{
            $site_id = Auth::user()->site->id;
            $purchase = Order::find($id);
            $suppliers = Supplier::all();
            $sites = Site::where('site_id','=',$site_id)->get();
            $locations = Location::where('site_id','=',$site_id)->get();
            $parts = Part::where('site_id','=',$site_id)->get();
            $endusers = Enduser::where('site_id','=',$site_id)->get();
            $order_parts = OrderPart::where('order_id', '=', $id)->get();
            return view('purchases.edit', compact('purchase', 'suppliers', 'sites', 'locations', 'parts', 'endusers', 'order_parts'));
        }catch (\Exception $e){
            return $this->handleError($e, 'edit()');
}

       
    }

 
    public function update(Request $request, $id)
    {
    }

   
    public function destroy($id)
    {
        try{
            $purchase = Order::find($id);
            Log::info('Order Deleted', [
                'Order Details' => Order::find($id),
                'Porder Details' => OrderPart::where("order_id", $purchase->id)->delete(),
    
            ]);
            Order::where("id", $purchase->id)->delete();
            OrderPart::where("order_id", $purchase->id)->delete();
           
            return redirect()->route('purchases.index')->withSuccess('Successfully Updated');
        }catch (\Exception $e){
            return $this->handleError($e, 'destroy()');
}

    
      
    }

    public function selectPart(Request $request)
    {
          $site_id = Auth::user()->site->id;
        $movies = Part::where('site_id','=',$site_id)->get();

        if ($request->has('q')) {
            $search = $request->q;
            $movies = Part::select("id", "name")
                ->where('name', 'LIKE', "%$search%")
                ->where('site_id','=',$site_id)
                ->get();
        }
        return response()->json($movies);
    }
    public function selectSearch(Request $request)
    {
          $site_id = Auth::user()->site->id;
        $movies = Supplier::all();

        if ($request->has('q')) {
            $search = $request->q;
            $movies = Supplier::select("id", "name")
                ->where('name', 'LIKE', "%$search%")->orWhere('phone', 'LIKE', "%$search%")
                ->get();
        }
        return response()->json($movies);
    }
    public function selectEnduser(Request $request)
    {
          $site_id = Auth::user()->site->id;
        $movies = Enduser::where('site_id','=',$site_id)->get();

        if ($request->has('q')) {
            $search = $request->q;
            $movies = Enduser::select("id", "asset_staff_id")
                ->where('asset_staff_id', 'LIKE', "%$search%")
                ->where('site_id','=',$site_id)
                ->get();
        }
        return response()->json($movies);
    }
    public function selectRequester(Request $request)
    {
          $site_id = Auth::user()->site->id;
        $movies = User::where('role_id', '=', '3')->where('site_id','=', $site_id)->get();

        if ($request->has('q')) {
            $search = $request->q;
            $movies = User::select("id", "name")
                ->where('name', 'LIKE', "%$search%")->orWhere('phone', 'LIKE', "%$search%")
                ->where('site_id','=',$site_id)
                ->get();
        }
        return response()->json($movies);
    }
    public function selectTax(Request $request)
    {
          $site_id = Auth::user()->site->id;
        $movies = Tax::all();
          
        if ($request->has('q')) {
            $search = $request->q;
            $movies = Tax::select("id", "description")
                ->where('description', 'LIKE', "%$search%")
               
                ->get();
        }
        return response()->json($movies);
    }
    public function selectLevy(Request $request)
    {
        $site_id = Auth::user()->site->id;
        $movies = Levy::all();

        if ($request->has('q')) {
            $search = $request->q;
            $movies = Levy::select("id", "description")
                ->where('description', 'LIKE', "%$search%")
                ->get();
        }
        return response()->json($movies);
    }

    // requester dashboard links
    public function requested()
    {
          $site_id = Auth::user()->site->id;
        $authid = Auth::id();
        // $purchase = Purchase::find($id);
        $requested = Order::where('user_id', '=', $authid)->where('status', '=', 'Requested')->where('site_id','=', $site_id)->latest()->paginate(15);
        return view('purchases.requested', compact('requested'));
        // dd($requested);
        // return ('hellow word');
    }

    public function initiated()
    {
          $site_id = Auth::user()->site->id;
        $authid = Auth::id();
        $initiated = Purchase::where('user_id', '=', $authid)->where('status', '=', 'Initiated')->where('site_id','=', $site_id)->get();
        return view('purchases.initiated', compact('initiated'));
    }
    public function approved()
    {
          $site_id = Auth::user()->site->id;
        $authid = Auth::id();
        $approved = Order::where('user_id', '=', $authid)->where('status', '=', 'Approved')->where('site_id','=', $site_id)->get();
        return view('purchases.approved', compact('approved'));
    }
    public function ordered()
    {
          $site_id = Auth::user()->site->id;
        $authid = Auth::id();
        $ordered = Order::where('user_id', '=', $authid)->where('status', '=', 'Ordered')->where('site_id','=', $site_id)->get();
        return view('purchases.ordered', compact('ordered'));
    }
    public function delivered()
    {
          $site_id = Auth::user()->site->id;
        $authid = Auth::id();
        $delivered = Order::where('user_id', '=', $authid)->where('status', '=', 'Supplied')->where('site_id','=', $site_id)->get();
        return view('purchases.delivered', compact('delivered'));
    }
    public function req_all()
    {
          $site_id = Auth::user()->site->id;
        $authid = Auth::id();
        $req_all = Order::where('user_id', '=', $authid)->where('site_id','=',$site_id)->latest()->paginate(15);
        return view('purchases.reqall', compact('req_all'));
    }

    // store_officer purchase pages
    public function all_requests()
    {
          $site_id = Auth::user()->site->id;

        $all_requests = Order::where('status', '=', 'Requested')->where('approval_status', '=', 'Approved')->where('site_id','=',$site_id)->latest()->paginate(15);
        return view('purchases.all_requests', compact('all_requests'));
    }

    public function all_initiates()
    {
          $site_id = Auth::user()->site->id;
        $all_initiates = Order::where('status', '=', 'Initiated')->where('approval_status', '=', 'Approved')->where('site_id','=',$site_id)->latest()->paginate(15);
        return view('purchases.all_initiates', compact('all_initiates'));
    }
    public function all_approves()
    {
          $site_id = Auth::user()->site->id;
        $all_approves = Order::where('status', '=', 'Approved')->where('approval_status', '=', 'Approved')->where('site_id','=',$site_id)->latest()->paginate(15);
        return view('purchases.all_approves', compact('all_approves'));
    }
    public function all_orders()
    {
          $site_id = Auth::user()->site->id;
        $all_orders = Order::where('status', '=', 'Ordered')->where('approval_status', '=', 'Approved')->where('site_id','=',$site_id)->latest()->paginate(15);
        return view('purchases.all_orders', compact('all_orders'));
    }
    public function all_delivers()
    {
          $site_id = Auth::user()->site->id;
        $all_delivers = Order::where('status', '=', 'Supplied')->where('approval_status', '=', 'Approved')->where('site_id','=',$site_id)->latest()->paginate(15);
        return view('purchases.all_delivers', compact('all_delivers'));
    }
    // end of store_officer purchases pages
    // purchase order draft
    public function purchase_order_draft($id)
    {
        try{
            $site_id = Auth::user()->site->id;
            $purchase_order = Porder::find($id);
            $order_id = Porder::where('id', '=', $id)->value('order_id');
            $purchase_order_no = Porder::where('id', '=', $id)->value('purchasing_order_number');
            $suppliers = Supplier::all();
            $endusers = Enduser::where('site_id','=', $site_id)->get();
            $order_parts = PorderPart::where('order_id', '=', $order_id)->where('purchasing_order_number', '=', $purchase_order_no)->get();
            $grandtotal = PorderPart::where('order_id', '=', $order_id)->where('purchasing_order_number', '=', $purchase_order_no)->sum('sub_total');
            $date_created = Carbon::now()->toDateTimeString();
            $amount_to_words = $this->numberToWord($grandtotal);
            $taxes = Tax::all();
            $levies = Levy::all();
            return view('purchases.purchase_order_draft', compact('purchase_order', 'suppliers', 'endusers', 'order_parts', 'grandtotal', 'date_created', 'amount_to_words', 'taxes', 'levies'));
            // dd($id,$order_parts);
        }
        catch(\Exception $e){
            return $this->handleError($e, 'purchase_order_draft()');
}
    }


    // generate purchase order
    public function generate_order($id)
    {
        try{

            $authid = Auth::user()->name;

            $purchase_order = Order::find($id);
    
            $new_purchase_order = $purchase_order->replicate();
            $new_purchase_order->setTable('porders');
            $new_purchase_order->order_id = $id;
            $new_purchase_order->created_by = $authid;
            $purchase_order_number = Pay::genPurchaseCode();
            $new_purchase_order->purchasing_order_number = $purchase_order_number;
            $new_purchase_order->save();
    
            $fks = OrderPart::where('order_id', '=', $id)->get();
            foreach ($fks as $fk) {
                $orderpart = $fk->replicate();
                $orderpart->setTable('porder_parts');
                $orderpart->purchasing_order_number = $purchase_order_number;
                $orderpart->save();
                $purchase_order = Porder::latest()->value('id');
            }
       
            return redirect()->route('purchases.purchase_order_draft', $purchase_order)->withSuccess('Successfully Updated');
        }
            catch(\Exception $e){
                return $this->handleError($e, 'generate_order()');
    }
    
       
    }


    public function purchase_list()
    {
        // Get the site ID from the authenticated user
        $site_id = Auth::user()->site->id;
    
        // Check if the user is a 'Department Authoriser'
        if (Auth::user()->hasRole('Department Authoriser')) {
            $department_id = Auth::user()->department->id ?? null;
    
            if ($department_id === null) {
                // User doesn't have a department assigned, return empty results
                $purchase_lists = Porder::where('id', null)->paginate(15);
            } else {
                // Query for department authoriser role
                $purchase_lists = Porder::leftJoin('users', 'users.id', '=', 'porders.user_id')
                    ->where('users.department_id', '=', $department_id)
                    ->where('porders.site_id', '=', $site_id) // Compare site_id to the logged-in user's site
                    ->latest()
                    ->paginate(15);
            }
    
            return view('purchases.list', compact('purchase_lists'));
        } 
    
        // Default query for authoriser role
        $purchase_lists = Porder::where('is_draft', '=', false) // Assuming you want to check for draft orders
            ->where('site_id', '=', $site_id)
            ->latest()
            ->paginate(15);
    
        return view('purchases.list', compact('purchase_lists'));
    }
    
    

    public function drafts()
    {
          $site_id = Auth::user()->site->id;
        $drafts = Porder::where('is_draft', '=', true)->where('site_id','=', $site_id)->latest()->paginate(15);
        return view('purchases.drafts', compact('drafts'));
    }
    //edit purchase list
    public function purchase_edit($id)
    {
        try{
            $site_id = Auth::user()->site->id;
            $purchase = Porder::find($id);
            $orderid = Porder::where('id', '=', $id)->value('order_id');
    
            $suppliers = Supplier::all();
            $sites = Site::where('site_id','=', $site_id)->get();
            $locations = Location::where('site_id','=', $site_id)->get();
            $parts = Part::where('site_id','=', $site_id)->get();
            $endusers = Enduser::where('site_id','=', $site_id)->get();
            $order_parts = OrderPart::where('order_id', '=', $orderid)->where('')->get();
            return view('purchases.purchase_edit', compact('purchase', 'suppliers', 'sites', 'locations', 'parts', 'endusers', 'order_parts'));
        }
            catch(\Exception $e){
                return $this->handleError($e, 'purchase_edit()');
    }
    
      
    }

    public function purchase_update(Request $request, $id)
    {
        try {
            $purchase = Porder::find($id);
            Log::info(
                'PurchaseController| purchase_update() | Before Puchase Update',
                [
                    'user_details' => $purchase,
                    'response_payload' => Porder::find($id)
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
            $purchase->po_number = Pay::genPurchaseCode();
            $purchase->suppliers_reference = $request->suppliers_reference;
            $purchase->site_id = $request->site_id;
            $purchase->date_created = $request->date_created;
            $purchase->deliver_to = $request->deliver_to;
            // $purchase->created_by = $request->created_by;

            $purchase->invoice_number = $request->invoice_number;
            $purchase->notes = $request->notes;
            $purchase->user_id = $authid;
            $purchase->is_draft = false;
            $purchase->save();
            Log::info("Pucahse Controller| purchase_update() |  Details After Edit", [
                'user_details' => Auth::user(),
                'User Name' => $authid,
                'request_payload' => $request->all(),
                'nessage' => 'Purchase Order Updated Successfully'
            ]);
           
            return redirect()->back()->withSuccess('Successfully Updated');
        } catch (\Throwable $e) {
           
                return $this->handleError($e, 'purchase_update()');
    }
    
    }



    public function purchase_update_row(Request $request, $id)
    {
        try {
            $porder = Porder::find($id);
            $order_id = Porder::where('id', '=', $id)->value('order_id');
            $purchasing_order_number = Porder::where('id', '=', $id)->value('purchasing_order_number');
            $site_id = Auth::user()->site->id;
            PorderPart::create([
                'order_id' => $order_id,
                'description' => $request->description,
                'quantity' => $request->quantity,
                'make' => $request->make,
                'model' => $request->model,
                'part_number' => $request->part_number,
                'serial_number' => $request->serial_number,
                'unit_price' => $request->unit_price,
                'comments' => $request->comments,
                'remarks' => $request->remarks,
                'priority' => $request->priority,
                'prefix' => $request->prefix,
                'purchasing_order_number' => $purchasing_order_number,
                'sub_total' => $request->quantity * $request->unit_price,
                'site_id' => $site_id,

            ]);
         
            return redirect()->back()->withSuccess('Successfully updated');
        } catch (\Exception $e) {
          
                return $this->handleError($e, 'purchase_update_row()');
    }
    
    }


    public function purchase_destroy($id)
    {
        try {
            $purchase = Porder::find($id);

            Log::info('PurchaseController | purchase_destroy', [
                'user_details' => auth()->user(),
                'message' => 'Purchase deleted successfully.',
                'details' => $purchase,
            ]);

            Porder::where("id", $purchase->id)->delete();

         
            return redirect()->back()->withSuccess('Successfully Updated');
        }
            catch(\Exception $e){
                return $this->handleError($e, 'purchase_destroy()');
    }
    
    }

    public function showlist($id)
    {
        try {
            $company = Company::first();
            $purchase = Porder::where('id', '=', $id)->first();
            $orderid = Porder::where('id', '=', $id)->value('order_id');
            $purchasing_order_number = Porder::where('id', '=', $id)->value('purchasing_order_number');
            $order_parts = PorderPart::where('order_id', '=', $orderid)->where('purchasing_order_number', '=', $purchasing_order_number)->get();
            $grandtotal = PorderPart::where('order_id', '=', $orderid)->where('purchasing_order_number', '=', $purchasing_order_number)->sum('sub_total');

            Log::info('PurchaseController | showlist', [
                'user_details' => auth()->user(),
                'message' => 'Purchase list details retrieved successfully.',
                'purchase_id' => $id,
            ]);

            return view('purchases.showlist', compact('purchase', 'order_parts', 'company', 'grandtotal'));
        } 
            catch(\Exception $e){
                return $this->handleError($e, 'showlist()');
    }
    
    }

    public function editlist($id)
    {
        try {
            $site_id = Auth::user()->site->id;
            $purchase = Porder::find($id);
            $orderid = Porder::where('id', '=', $id)->value('order_id');
            $purchasing_order_number = Porder::where('id', '=', $id)->value('purchasing_order_number');
            $suppliers = Supplier::all();
            $sites = Site::where('site_id','=', $site_id)->get();
            $locations = Location::where('site_id','=', $site_id)->get();
            $parts = Part::where('site_id','=', $site_id)->get();
            $endusers = Enduser::where('site_id','=', $site_id)->get();
            $order_parts = PorderPart::where('order_id', '=', $orderid)->where('purchasing_order_number', '=', $purchasing_order_number)->get();
            $grandtotal = PorderPart::where('order_id', '=', $orderid)->where('purchasing_order_number', '=', $purchasing_order_number)->sum('sub_total');

            Log::info('PurchaseController | editlist', [
                'user_details' => auth()->user(),
                'message' => 'Purchase list edit form loaded successfully.',
                'purchase_id' => $id,
            ]);

            return view('purchases.editlist', compact('purchase', 'suppliers', 'sites', 'locations', 'parts', 'endusers', 'order_parts', 'grandtotal'));
        } 
            catch(\Exception $e){
                return $this->handleError($e, 'editlist()');
    }
    
    }


  
    public function purchaselist_update(Request $request)
    {

        if ($request->ajax()) {
            Log::info('Purchase Update List Detail', [
                'Details' => PorderPart::findOrFail($request->pk),
            ]);
            $val =    PorderPart::findOrFail($request->pk)
                ->update([
                    $request->quantity => $request->value

                ]);
            Log::info('Update Purchase List Values', ['Details' => $val]);
            return response()->json(['success' => true]);
        }
    }
    public function action(Request $request)
    {
        if ($request->ajax()) {
            if ($request->action == 'edit') {
                $data = array(
                    'uom'        =>    $request->uom,
                    'quantity'        =>    $request->quantity,
                    'unit_price'        =>    $request->unit_price,
                    'remarks'  =>    $request->remarks,
                    'discount'  =>    $request->discount,
                    'rate'  =>    $request->rate,

                );
                Log::info('Action Details', [
                    'Details' => $data,
                ]);
                $val =    DB::table('porder_parts')
                    ->where('id', $request->id)
                    ->update($data);
                $authid = Auth::user()->name;
                Log::info('Action Details', [
                    'Edited By' => $authid,
                    'Details' => $val,
                ]);
                $quantity = PorderPart::where('id', '=', $request->id)->value('quantity');
                $unit_price = PorderPart::where('id', '=', $request->id)->value('unit_price');


                $discount = PorderPart::where('id', '=', $request->id)->value('discount');
                $sub_total = $quantity * $unit_price;
                $sub_total = (($sub_total) - ($sub_total * $discount) / 100);



                $val1 = PorderPart::where('id', $request->id)->update(['sub_total' => $sub_total]);
                Log::info('Updated Values', [
                    'Edited By' => $authid,
                    'Details' => $val1,
                ]);
            }
            $authid = Auth::user()->name;
            if ($request->action == 'delete') {
                Log::info('Deleted Values', [
                    'Deleted By' => $authid,
                    'Details' =>  DB::table('porder_parts')
                        ->where('id', $request->id),
                ]);
                DB::table('porder_parts')
                    ->where('id', $request->id)
                    ->delete();
            }

            return response()->json($request);
        }
    }

    public function po_all_requests()
    {
        $site_id = Auth::user()->site->id;
        $all_requests = Order::where('status', '=', 'Requested')->where('site_id','=', $site_id)->latest()->paginate(15);
        return view('purchases.po_all_requests', compact('all_requests'));
    }

    // generate pdf to send to supplier
    public function generatePDF($id)
    {
        try {
            $company = Company::latest()->first();
            $purchase = Order::find($id);
            $order_parts = OrderPart::where('order_id', '=', $id)->get();
            $pdf_filename = Order::where('id', '=', $id)->value('request_number'); // Get the value directly
            Log::info('PurchaseController | generatePDF', [
                'user_details' => auth()->user(),
                'message' => 'Purchase PDF generated successfully.',
                'purchase_id' => $id,
            ]);

            $purchasePDF = PDF::loadView('purchases.pdf', compact('purchase', 'company', 'order_parts'))->setOptions(['defaultFont' => 'sans-serif']);

            $filename = $pdf_filename . '.pdf'; // Append '.pdf' to the filename

            return $purchasePDF->download($filename);
        } 
            catch(\Exception $e){
                return $this->handleError($e, 'generatePDF()');
    }
    
    }

    public function generatePurchaseOrderPDF($id)
    {
        try {
            $company = Company::first();
            $purchase = Porder::find($id);
            $pdf_filename = Porder::where('id', '=', $id)->value('request_number'); // Get the value directly
            $orderid = $purchase->order_id;
            $purchasing_order_number = $purchase->purchasing_order_number;
            $order_parts = PorderPart::where('order_id', '=', $orderid)
                ->where('purchasing_order_number', '=', $purchasing_order_number)
                ->get();
            $grandtotal = $order_parts->sum('sub_total');

            Log::info('PurchaseController | generatePurchaseOrderPDF', [
                'user_details' => auth()->user(),
                'message' => 'Purchase Order PDF generated successfully.',
                'purchase_id' => $id,
            ]);

            $purchasePDF = PDF::loadView('purchases.porderpdf', compact('purchase', 'order_parts', 'grandtotal', 'company'))
                ->setOptions(['defaultFont' => 'sans-serif'])
                ->setPaper('a3', 'portrait');

            // Customize the filename for download
            // $filename = $pdf_filename . '.pdf'; // Append '.pdf' to the filename
            $filename = $pdf_filename . '.pdf'; // Append '.pdf' to the filename

            return $purchasePDF->download($filename);
        } 
            catch(\Exception $e){
                return $this->handleError($e, 'generatePurchaseOrderPDF()');
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

    public function save_draft(Request $request, $id)
    {
        try{
            $purchase = Porder::find($id);

            $authid = Auth::id();
            $request->validate([
                'supplier_id' => 'nullable',
                'type_of_purchase' => 'nullable'
            ]);
            $site_id = Auth::user()->site->id;
            $purchase->part_id = $request->part_id; //2
    
            $purchase->tax = $request->tax; //10
            $purchase->tax2 = $request->tax2; //11
            $purchase->tax3 = $request->tax3; //12
            $purchase->supplier_id = $request->supplier_id; //1
            $purchase->type_of_purchase = $request->type_of_purchase; //3
            $purchase->enduser_id = $request->enduser_id; //4
            $purchase->status = $request->status;
            $purchase->delivery_reference_number = $request->delivery_reference_number;
            $purchase->po_number = Pay::genPurchaseCode();
            $purchase->suppliers_reference = $request->suppliers_reference;
            $purchase->site_id = $request->site_id;
            $purchase->date_created = $request->date_created;
            $purchase->deliver_to = $request->deliver_to;
            // $purchase->created_by = $request->created_by;
    
            $purchase->invoice_number = $request->invoice_number;
            $purchase->notes = $request->notes;
            $purchase->user_id = $authid;
            $purchase->is_draft = true;
            $purchase->$site_id;
            $purchase->save();
    
           
            return redirect()->back()->withSuccess('Successfully Updated');
        }
            catch(\Exception $e){
                return $this->handleError($e, 'save_draft()');
    }
    
       
    }
   
}
