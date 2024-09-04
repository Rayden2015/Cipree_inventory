<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\User;
use App\Models\Order;
use App\Models\Porder;
use App\Models\Sorder;
use App\Models\Enduser;
use App\Models\Section;
use App\Models\Feedback;
use App\Models\Purchase;
use App\Models\Inventory;
use App\Models\Department;
use App\Models\PorderPart;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryItemDetail;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $site_id = Auth::user()->site->id;
        $authid = Auth::id();
        
        // store officer dashboard
        $all_requests = Order::where('site_id', '=', $site_id)
            ->where('status', '=', 'Requested')
            ->where('approval_status', '=', 'Approved')
            ->count();
            
        $all_initiates = Order::where('site_id', '=', $site_id)
            ->where('status', '=', 'Initiated')
            ->where('approval_status', '=', 'Approved')
            ->count();
            
        $all_approves = Order::where('site_id', '=', $site_id)
            ->where('status', '=', 'Approved')
            ->where('approval_status', '=', 'Approved')
            ->count();
            
        $all_orders = Order::where('site_id', '=', $site_id)
            ->where('status', '=', 'Ordered')
            ->where('approval_status', '=', 'Approved')
            ->count();
            
        $all_delivers = Order::where('site_id', '=', $site_id)
            ->where('status', '=', 'Supplied')
            ->where('approval_status', '=', 'Approved')
            ->count();
        
        $store_officer_requests = Sorder::where('site_id', '=', $site_id)
            ->where('approval_status', '=', 'approved')
            ->count();
        
        $total_no_of_parts = Item::join('inventory_items', 'items.id', '=', 'inventory_items.item_id')
            ->where('inventory_items.site_id', '=', $site_id)
            ->where('items.stock_quantity', '>', '0')
            ->count();
            
        $total_cost_of_partss = InventoryItem::where('site_id', '=', $site_id)
            ->where('quantity', '>', '0')
            ->sum('amount');
            
        $total_cost_of_parts_issued = Sorder::join('sorder_parts', 'sorders.id', '=', 'sorder_parts.sorder_id')
            ->where('sorders.site_id', '=', $site_id)
            ->where('sorder_parts.site_id', '=', $site_id)
            ->where('sorders.status', '=', 'Supplied')
            ->sum('sorder_parts.sub_total');
        
        // $sub_total_cost_of_parts =  $total_cost_of_partss - $total_cost_of_parts_issued;
        $sub_total_cost_of_parts = $total_cost_of_partss;
        $total_cost_of_parts = number_format($sub_total_cost_of_parts, 2);
        
        
          $stockValueByType = Inventory::selectRaw('inventories.trans_type, sum(inventory_items.amount) as total_value')
            ->join('inventory_items', 'inventories.id', '=', 'inventory_items.inventory_id')
            ->where('inventory_items.site_id','=', $site_id)
            ->groupBy('inventories.trans_type')
            ->get();
            
        
            
              $total_cost_of_parts_within_the_weeks = Sorder::join('sorder_parts', 'sorders.id', '=', 'sorder_parts.sorder_id')
            ->where('sorders.site_id','=' ,$site_id)
            ->where('sorder_parts.site_id','=',$site_id)
            ->where('sorders.status', '=', 'Supplied')->whereBetween('sorders.delivered_on', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('sorder_parts.sub_total');
            $total_cost_of_parts_within_the_month = number_format($total_cost_of_parts_within_the_weeks, 2);

              // last worked
              $stockDistribution = InventoryItem::select(
                'categories.name as category_name', 
                DB::raw('SUM(inventory_items.amount) as total_amount')
            )
            ->join('items', 'items.id', '=', 'inventory_items.item_id')
            ->join('categories', 'categories.id', '=', 'items.item_category_id')
            ->where('inventory_items.site_id', '=', $site_id)
            ->where('inventory_items.quantity', '>', 0)
            ->groupBy('categories.name')
            ->get();

            $total_value_items_received_mtd1 =  InventoryItem::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->where('inventory_items.site_id','=',$site_id)
            ->sum('amount');
            $total_value_items_received_mtd = number_format($total_value_items_received_mtd1, 2);

          // site admin dashboard
          $active_user_accounts = User::where('site_id','=', $site_id)->where('status', '=', 'Active')->count();
          $disabled_user_accounts = User::where('site_id','=', $site_id)->where('status', '=', 'Inactive')->count();
          $active_endusers = Enduser::where('site_id','=', $site_id)->where('status', '=', 'Active')->count();
          $disabled_endusers = Enduser::where('site_id','=', $site_id)->where('status', '=', 'Inactive')->count();

          $departments =Department::count();
          $sections = Section::count();

          $total_requests = Order::where('user_id', '=', $authid)->count();
          $requested = Order::where('user_id', '=', $authid)->where('status', '=', 'Requested')->count();
          $initiated = Order::where('user_id', '=', $authid)->where('status', '=', 'Initiated')->count();
          $approved = Order::where('user_id', '=', $authid)->where('status', '=', 'Approved')->count();
          $ordered = Order::where('user_id', '=', $authid)->where('status', '=', 'Ordered')->count();
          $delivered = Order::where('user_id', '=', $authid)->where('status', '=', 'Supplied')->count();
          $rfi_pending_approval = Sorder::whereNull('approval_status')->where('requested_by', '=', $authid)->count();
          $rfi_approved_requests = Sorder::where('approval_status', '=', 'Approved')->where('requested_by', '=', $authid)->count();
          $rfi_processed_requests = Sorder::where('status', '=', 'Supplied')->where('requested_by', '=', $authid)->orWhere('status', '=', 'Partially Supplied')->where('requested_by', '=', $authid)->count();
          $rfi_denied = Sorder::where('approval_status', '=', 'Denied')->where('requested_by', '=', $authid)->count();
         

          $dpr_pending_approval = Order::where('site_id','=',$site_id)->whereNull('approval_status')->where('user_id','=',$authid)->count();
          $dpr_approved = Order::where('site_id','=',$site_id)->where('approval_status', '=', 'Approved')->where('user_id','=',$authid)->count();
          $dpr_processed = Order::where('site_id','=',$site_id)->where('status', '=', 'Supplied')->where('user_id','=',$authid)->count();
          $dpr_denied = Order::where('site_id','=',$site_id)->where('approval_status', '=', 'Denied')->where('user_id','=',$authid)->count();

          $new_approvals = Notification::where('site_id', '=', $site_id)->where('read_at', '=', '0')->count();
            $pending_po_approvals = Porder::where('site_id', '=', $site_id)->whereNull('approval_status')->count();
            $pending_request_approvals = Order::where('site_id', '=', $site_id)->whereNull('approval_status')->count();
            $pending_stock_approvals = Sorder::where('site_id', '=', $site_id)->whereNull('approval_status')->count();
            $approved_pos =  Porder::where('site_id', '=', $site_id)->where('approval_status', '=', 'Approved')->count();
            $approved_request =  Order::where('site_id', '=', $site_id)->where('approval_status', '=', 'Approved')->count();
            $processed_pos = Porder::where('site_id', '=', $site_id)->where('status', '=', 'Ordered')->count();
            $processed_request = Sorder::where('site_id', '=', $site_id)->where('status', '=', 'Supplied')->count();

            $po_total_number_of_requests_mtd = Order::where('site_id','=', $site_id)->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count();
            $po_total_value_of_approved_pos_mtd = PorderPart::where('site_id','=', $site_id)->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->where('site_id','=', $site_id)->sum('sub_total');

            $po_total_value_of_supplied_pos_mtd = Porder::join('porder_parts', 'porders.order_id', '=', 'porder_parts.order_id')->whereBetween('porders.created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->where('porders.status', '=', 'Supplied')->where('porder_parts.site_id','=',$site_id)->where('porders.site_id','=',$site_id)->sum('porder_parts.sub_total');
            $po_total_value_of_pending_pos_mtd = Porder::join('porder_parts', 'porders.order_id', '=', 'porder_parts.order_id')->whereBetween('porders.created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->whereNull('porders.approval_status')->sum('porder_parts.sub_total');
            $po_approved_stock_requests = Sorder::where('site_id','=', $site_id)->where('approval_status', '=', 'Approved')->count();
            $po_approved_direct_requests = Order::where('site_id','=', $site_id)->where('approval_status', '=', 'Approved')->count();
            $po_approved_pos = Porder::where('site_id','=', $site_id)->where('approval_status', '=', 'Approved')->count();
            $po_denied_requests = Order::where('site_id','=', $site_id)->where('approval_status', '=', 'Denied')->count();

            $user_complaints_open = Feedback::where('site_id','=', $site_id)->where('reviewed', '=', '0')->count();

            $stock_request_pending = Sorder::whereNUll('approval_status')->count();
            $sofficer_stock_request_pending = Sorder::where('status', '!=', 'Supplied')->where('status', '!=', 'Partially Supplied')->where('approval_status', '=', 'Approved')
            ->where('site_id','=', $site_id)
            ->count();

            

        return view('home', compact(
            'all_requests',
            'all_initiates',
            'all_approves',
            'all_orders',
            'all_delivers',
            'store_officer_requests',
            'total_no_of_parts',
            'total_cost_of_partss',
            'total_cost_of_parts_issued',
            'sub_total_cost_of_parts',
            'total_cost_of_parts','stockValueByType','total_cost_of_parts_within_the_month','stockDistribution','total_value_items_received_mtd','active_user_accounts','disabled_user_accounts','active_endusers','disabled_endusers','departments','sections','total_requests','requested','initiated','approved','ordered','delivered','rfi_pending_approval',
            'rfi_approved_requests','rfi_processed_requests','rfi_denied', 
            'dpr_pending_approval','dpr_approved','dpr_processed','dpr_denied',
            'new_approvals','pending_po_approvals','pending_request_approvals','pending_stock_approvals','approved_pos','approved_request','processed_pos','processed_request',
            'po_total_number_of_requests_mtd','po_total_value_of_approved_pos_mtd','po_total_value_of_supplied_pos_mtd','po_total_value_of_pending_pos_mtd','po_approved_stock_requests','po_approved_direct_requests','po_approved_pos','po_denied_requests','user_complaints_open','stock_request_pending','sofficer_stock_request_pending'
        ));
        
         
        } 
        // catch (\Exception $e) {
        //     $unique_id = floor(time() - 999999999);
        //     Log::channel('error_log')->error('HomeController | Index() Error ' . $unique_id . ': ' . $e->getMessage());
        //     Toastr::error('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the Feedback Button', 'Error');
        // }
        
    }
