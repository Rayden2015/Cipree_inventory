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
public function index(Request $request)
{
    try{
    // Get tenant context (set by TenantContext middleware)
    $tenantId = session('current_tenant_id');
    $user = Auth::user();
    
    // Super Admin can access all tenants (or specific tenant via query param)
    if ($user->isSuperAdmin() && $request->has('tenant_id')) {
        $tenantId = $request->get('tenant_id');
        session(['current_tenant_id' => $tenantId]);
    }
    
    // For non-Super Admins, get tenant from user
    if (!$tenantId && !$user->isSuperAdmin()) {
        $tenant = $user->getCurrentTenant();
        if (!$tenant) {
            Log::error('HomeController | index() | User has no tenant assigned', [
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);
            Toastr::error('Your account is not assigned to a tenant. Please contact the administrator.');
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors(['email' => 'Your account is not assigned to a tenant. Please contact the administrator.']);
        }
        $tenantId = $tenant->id;
        session(['current_tenant_id' => $tenantId]);
    }
    
    // Check if user has a site assigned (for non-Super Admin, non-Tenant Admin users)
    if (!$user->isSuperAdmin() && !$user->isTenantAdmin() && !$user->site) {
        Log::error('HomeController | index() | User has no site assigned', [
            'user_id' => $user->id,
            'user_email' => $user->email
        ]);
        Toastr::error('Your account is not assigned to a site. Please contact the administrator.');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->withErrors(['email' => 'Your account is not assigned to a site. Please contact the administrator.']);
    }
    
    $site_id = $user->site->id ?? null;
    $authid = Auth::id();

    // store officer dashboard
    // Filter by tenant_id if set, and site_id if available (for non-Super Admin)
    $all_requestsQuery = Order::where('status', '=', 'Requested')
        ->where('approval_status', '=', 'Approved');
    
    if ($tenantId) {
        $all_requestsQuery->where('tenant_id', '=', $tenantId);
    }
    
    if ($site_id && !$user->isSuperAdmin()) {
        $all_requestsQuery->where('site_id', '=', $site_id);
    }
    
    $all_requests = $all_requestsQuery->count();

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
    // $total_no_of_parts = InventoryItem::where('site_id', '=', $site_id)
    // ->where('quantity', '>', 0)
    // ->groupBy('item_id')
    // ->count();


    $total_cost_of_partss = InventoryItem::where('site_id', '=', $site_id)
        ->where('quantity', '>', '0')
        ->sum('amount');

    $total_cost_of_parts_issued = Sorder::join('sorder_parts', 'sorders.id', '=', 'sorder_parts.sorder_id')
        ->where('sorders.site_id', '=', $site_id)
        ->where('sorder_parts.site_id', '=', $site_id)
        ->whereIn('sorders.status', ['Supplied', 'Partially Supplied'])
        ->sum('sorder_parts.sub_total');

    // $sub_total_cost_of_parts =  $total_cost_of_partss - $total_cost_of_parts_issued;
    $sub_total_cost_of_parts = $total_cost_of_partss;
    $total_cost_of_parts = number_format($sub_total_cost_of_parts, 2);


    $stockValueByType = Inventory::selectRaw('inventories.trans_type, sum(inventory_items.amount) as total_value')
        ->join('inventory_items', 'inventories.id', '=', 'inventory_items.inventory_id')
        ->where('inventory_items.site_id', '=', $site_id)
        ->groupBy('inventories.trans_type')
        ->get();



    $total_cost_of_parts_within_the_weeks = Sorder::join('sorder_parts', 'sorders.id', '=', 'sorder_parts.sorder_id')
        ->where('sorders.site_id', '=', $site_id)
        ->where('sorder_parts.site_id', '=', $site_id)
        ->whereIn('sorders.status', ['Supplied', 'Partially Supplied'])
        ->whereBetween('sorders.delivered_on', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('sorder_parts.sub_total');
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
        ->where('inventory_items.site_id', '=', $site_id)
        ->sum('amount');
    $total_value_items_received_mtd = number_format($total_value_items_received_mtd1, 2);

    // site admin dashboard
    $active_user_accounts = User::where('site_id', '=', $site_id)->where('status', '=', 'Active')->count();
    $disabled_user_accounts = User::where('site_id', '=', $site_id)->where('status', '=', 'Inactive')->count();
    $active_endusers = Enduser::where('site_id', '=', $site_id)->where('status', '=', 'Active')->count();
    $disabled_endusers = Enduser::where('site_id', '=', $site_id)->where('status', '=', 'Inactive')->count();

    $departments = Department::count();
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


    $dpr_pending_approval = Order::where('site_id', '=', $site_id)->whereNull('approval_status')->where('user_id', '=', $authid)->count();
    $dpr_approved = Order::where('site_id', '=', $site_id)->where('approval_status', '=', 'Approved')->where('user_id', '=', $authid)->count();
    $dpr_processed = Order::where('site_id', '=', $site_id)->where('status', '=', 'Supplied')->where('user_id', '=', $authid)->count();
    $dpr_denied = Order::where('site_id', '=', $site_id)->where('approval_status', '=', 'Denied')->where('user_id', '=', $authid)->count();

    $new_approvals = Notification::where('site_id', '=', $site_id)->where('read_at', '=', '0')->count();
    $pending_po_approvals = Porder::where('site_id', '=', $site_id)->whereNull('approval_status')->count();
    
    // Check if user is a Department Authoriser (but not Super Authoriser) and filter by department
    $isDepartmentOnly = Auth::user()->hasRole('Department Authoriser') && !Auth::user()->hasRole('Super Authoriser');
    if ($isDepartmentOnly) {
        $department_id = Auth::user()->department->id ?? null;
        if ($department_id === null) {
            $pending_request_approvals = 0;
        } else {
            $pending_request_approvals = Order::leftJoin('users', 'users.id', '=', 'orders.user_id')
                ->where('orders.site_id', '=', $site_id)
                ->whereNull('orders.approval_status')
                ->where('users.department_id', '=', $department_id)
                ->count();
        }
    } else {
        $pending_request_approvals = Order::where('site_id', '=', $site_id)->whereNull('approval_status')->count();
    }
    
    $pending_stock_approvals = Sorder::where('site_id', '=', $site_id)->whereNull('approval_status')->count();
    $approved_pos =  Porder::where('site_id', '=', $site_id)->where('approval_status', '=', 'Approved')->count();
    $approved_request =  Order::where('site_id', '=', $site_id)->where('approval_status', '=', 'Approved')->count();
    $processed_pos = Porder::where('site_id', '=', $site_id)->where('status', '=', 'Ordered')->count();
    $processed_request = Sorder::where('site_id', '=', $site_id)->where('status', '=', 'Supplied')->count();

    $po_total_number_of_requests_mtd = Order::where('site_id', '=', $site_id)->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count();
    $po_total_value_of_approved_pos_mtd = PorderPart::where('site_id', '=', $site_id)->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->where('site_id', '=', $site_id)->sum('sub_total');

    $po_total_value_of_supplied_pos_mtd = Porder::join('porder_parts', 'porders.order_id', '=', 'porder_parts.order_id')->whereBetween('porders.created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->where('porders.status', '=', 'Supplied')->where('porder_parts.site_id', '=', $site_id)->where('porders.site_id', '=', $site_id)->sum('porder_parts.sub_total');
    $po_total_value_of_pending_pos_mtd = Porder::join('porder_parts', 'porders.order_id', '=', 'porder_parts.order_id')->whereBetween('porders.created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->whereNull('porders.approval_status')->sum('porder_parts.sub_total');
    $po_approved_stock_requests = Sorder::where('site_id', '=', $site_id)->where('approval_status', '=', 'Approved')->count();
    $po_approved_direct_requests = Order::where('site_id', '=', $site_id)->where('approval_status', '=', 'Approved')->count();
    $po_approved_pos = Porder::where('site_id', '=', $site_id)->where('approval_status', '=', 'Approved')->count();
    $po_denied_requests = Order::where('site_id', '=', $site_id)->where('approval_status', '=', 'Denied')->count();

    $user_complaints_open = Feedback::where('site_id', '=', $site_id)->where('reviewed', '=', '0')->count();

    $stock_request_pending = Sorder::whereNUll('approval_status')->count();

    $sofficer_stock_request_pending = Sorder::where('status', '!=', 'Supplied')->where('status', '!=', 'Partially Supplied')->where('approval_status', '=', 'Approved')
        ->where('site_id', '=', $site_id)
        ->count();
    $out_of_stock_items = InventoryItem::leftjoin('inventories', 'inventory_items.inventory_id', '=', 'inventories.id')
        ->leftjoin('items', 'inventory_items.item_id', '=', 'items.id')
        ->where('inventory_items.site_id', '=', $site_id)
        ->where('stock_quantity', '=', '0')
        ->groupBy('inventory_items.item_id', 'inventories.trans_type')  // Group by both item_id and trans_type
        ->select('inventory_items.item_id', 'inventories.trans_type')  // Select only grouped fields
        ->get();  // Retrieve the result as a collection
    // Filter results based on 'trans_type'
    $out_of_stock_filtered = $out_of_stock_items->where('trans_type', 'Stock Purchase');

    // Count the filtered results
    $out_of_stock = $out_of_stock_filtered->count();

    $reorder_level = InventoryItem::select('inventory_items.id', 'inventory_items.item_id')
    ->leftJoin('items', 'items.id', '=', 'inventory_items.item_id')
    ->leftJoin('inventories', function($join) {
        $join->on('inventory_items.inventory_id', '=', 'inventories.id')
                ->where('inventories.trans_type', '=', 'Stock Purchase');
    })
    ->whereRaw('items.stock_quantity <= items.reorder_level')
    ->where('inventory_items.quantity', '>', 0)
    ->where('inventory_items.site_id', '=', $site_id)
    ->count();

// department authoriser dashboard
$department_id = Auth::user()->department->id ?? null;

if ($department_id === null) {
    $depart_auth_new_approvals = null;
    $depart_auth_pending_request_approvals = null;
    $depart_auth_pending_po_approvals = null;
    $depart_auth_pending_stock_approvals = null;
    $depart_auth_approved_request = null;
    $depart_auth_approved_pos = null;
    $depart_auth_processed_pos = null;
    $depart_auth_processed_request = null;
} else {
    $depart_auth_new_approvals = Notification::where('site_id', '=', $site_id)
        ->where('read_at', '=', '0')->count();

    $depart_auth_pending_request_approvals = Order::leftJoin('users', 'users.id', '=', 'orders.user_id')
        ->where('orders.site_id', '=', $site_id)
        ->whereNull('orders.approval_status')
        ->where('users.department_id', '=', $department_id)
        ->count();

    $depart_auth_pending_po_approvals = Porder::leftJoin('users', 'users.id', '=', 'porders.user_id')
        ->where('porders.site_id', '=', $site_id)
        ->whereNull('porders.approval_status')
        ->where('users.department_id', '=', $department_id)
        ->count();

    $depart_auth_pending_stock_approvals = Sorder::leftJoin('users', 'users.id', '=', 'sorders.user_id')
        ->where('users.department_id', '=', $department_id)
        ->where('sorders.site_id', '=', $site_id)
        ->whereNull('sorders.approval_status')
        ->count();

    $depart_auth_approved_request = Order::leftJoin('users', 'users.id', '=', 'orders.user_id')
        ->where('users.department_id', '=', $department_id)
        ->where('orders.site_id', '=', $site_id)
        ->where('orders.approval_status', '=', 'Approved')
        ->count();

    $depart_auth_approved_pos = Porder::leftJoin('users', 'users.id', '=', 'porders.user_id')
        ->where('users.department_id', '=', $department_id)
        ->where('porders.site_id', '=', $site_id)
        ->where('porders.approval_status', '=', 'Approved')
        ->count();

    $depart_auth_processed_pos = Porder::leftJoin('users', 'users.id', '=', 'porders.user_id')
        ->where('users.department_id', '=', $department_id)
        ->where('porders.site_id', '=', $site_id)
        ->where('porders.status', '=', 'Ordered')
        ->count();

    $depart_auth_processed_request = Sorder::leftJoin('users', 'users.id', '=', 'sorders.user_id')
        ->where('users.department_id', '=', $department_id)
        ->where('sorders.site_id', '=', $site_id)
        ->where('sorders.status', '=', 'Supplied')
        ->count();
}



    return view('home', compact(
        'all_requests',
        'all_initiates','depart_auth_new_approvals','depart_auth_pending_request_approvals','depart_auth_pending_po_approvals',
        'depart_auth_pending_stock_approvals','depart_auth_approved_request','depart_auth_approved_pos','depart_auth_processed_pos','depart_auth_processed_request',
        'all_approves',
        'all_orders',
        'all_delivers',
        'store_officer_requests',
        'total_no_of_parts',
        'total_cost_of_partss',
        'total_cost_of_parts_issued',
        'sub_total_cost_of_parts',
        'total_cost_of_parts',
        'stockValueByType',
        'total_cost_of_parts_within_the_month',
        'stockDistribution',
        'total_value_items_received_mtd',
        'active_user_accounts',
        'disabled_user_accounts',
        'active_endusers',
        'disabled_endusers',
        'departments',
        'sections',
        'total_requests',
        'requested',
        'initiated',
        'approved',
        'ordered',
        'delivered',
        'rfi_pending_approval',
        'rfi_approved_requests',
        'rfi_processed_requests',
        'rfi_denied',
        'dpr_pending_approval',
        'dpr_approved',
        'dpr_processed',
        'dpr_denied',
        'new_approvals',
        'pending_po_approvals',
        'pending_request_approvals',
        'pending_stock_approvals',
        'approved_pos',
        'approved_request',
        'processed_pos',
        'processed_request',
        'po_total_number_of_requests_mtd',
        'po_total_value_of_approved_pos_mtd',
        'po_total_value_of_supplied_pos_mtd',
        'po_total_value_of_pending_pos_mtd',
        'po_approved_stock_requests',
        'po_approved_direct_requests',
        'po_approved_pos',
        'po_denied_requests',
        'user_complaints_open',
        'stock_request_pending',
        'sofficer_stock_request_pending',
        'out_of_stock','reorder_level'
    ));
    }
catch (\Exception $e) {
    $unique_id = floor(time() - 999999999);
    Log::channel('error_log')->error('HomeController | Index() Error ' . $unique_id . ': ' . $e->getMessage());
    Log::channel('error_log')->error('HomeController | Index() Error Stack: ' . $e->getTraceAsString());
    
    Toastr::error('An error occurred while loading the dashboard. Please try again or contact support.');
    // Don't redirect back to avoid loops - show error on home page instead
    return view('home', [
        'error' => 'An error occurred while loading the dashboard. Error ID: ' . $unique_id,
        'all_requests' => 0,
        'all_initiates' => 0,
        'all_approves' => 0,
        'all_orders' => 0,
        'all_delivers' => 0,
        'store_officer_requests' => 0,
        'total_no_of_parts' => 0,
    ]);
}

}

/**
 * Dismiss banner for this session only
 */
public function dismissBannerSession(Request $request)
{
    $request->session()->put('banner_dismissed', true);
    $request->session()->forget('show_banner_on_login');
    return response()->json(['success' => true]);
}

/**
 * Permanently dismiss banner
 */
public function dismissBannerPermanent(Request $request)
{
    try {
        $user = Auth::user();
        $user->banner_dismissed_at = now();
        $user->save();
        
        $request->session()->put('banner_dismissed', true);
        
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        Log::error('HomeController | dismissBannerPermanent() Error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Failed to dismiss banner'], 500);
    }
}
}