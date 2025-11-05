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
use App\Models\Department;
use App\Models\PorderPart;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryItemDetail;
use Illuminate\Support\Facades\Log;
use App\Exports\ItemsListSiteExport;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\LogsErrors;


class DashboardNavigationController extends Controller
{
    use LogsErrors;
    
    public function __construct()
   {
      $this->middleware('auth');
   }
   public function pending_po_approvals()
   {
      try {
         Log::info('DashboardNavigationController | pending_po_approvals', [
            'user_details' => Auth::user(),
            'message' => 'Fetching pending PO approvals.'
         ]);
         $site_id = Auth::user()->site->id;
         if (Auth::user()->hasRole('Department Authoriser')) {
            $department_id = Auth::user()->department->id;

         $pending_po_approvals = Porder::leftJoin('users', 'users.id', '=', 'porders.user_id')
         ->where('porders.site_id', '=', $site_id)
         ->where('users.department_id', '=', $department_id)
         ->whereNull('porders.approval_status')->latest('porders.created_at')->paginate(15);
         return view('homepages.pending_po_approvals', compact('pending_po_approvals'));
         }
         $pending_po_approvals = Porder::where('site_id', '=', $site_id)->whereNull('approval_status')->latest()->paginate(15);
         return view('homepages.pending_po_approvals', compact('pending_po_approvals'));
      } catch (\Exception $e) {
         return $this->handleError($e, 'pending_po_approvals()');
      }
   }

   public function approved_request()
   {
      try {
         Log::info('DashboardNavigationController | approved_request', [
            'user_details' => Auth::user(),
            'message' => 'Fetching approved requests.'
         ]);
         $site_id = Auth::user()->site->id;
         if (Auth::user()->hasRole('Department Authoriser')) {
            $department_id = Auth::user()->department->id;
         $approved_request = Order::leftJoin('users', 'users.id', '=', 'orders.user_id')->
         where('orders.site_id', '=', $site_id)
         ->where('users.department_id', '=', $department_id)
         ->where('orders.approval_status', '=', 'Approved')->latest('orders.created_at')->paginate(15);
         return view('homepages.approved_request', compact('approved_request'));
         }
         $approved_request = Order::where('site_id', '=', $site_id)->where('approval_status', '=', 'Approved')->latest()->paginate(15);
         return view('homepages.approved_request', compact('approved_request'));
      } catch (\Exception $e) {
         return $this->handleError($e, 'approved_request()');
      }
   }

   public function approved_pos()
   {
      try {
         Log::info('DashboardNavigationController | approved_pos', [
            'user_details' => Auth::user(),
            'message' => 'Fetching approved POs.'
         ]);
         $site_id = Auth::user()->site->id;
         if (Auth::user()->hasRole('Department Authoriser')) {
            $department_id = Auth::user()->department->id;
         $approved_pos = Porder::leftJoin('users', 'users.id', '=', 'porders.user_id')->
         where('porders.site_id', '=', $site_id)
         ->where('users.department_id', '=', $department_id)
         ->where('porders.approval_status', '=', 'Approved')->latest('porders.created_at')->paginate(15);
         return view('homepages.approved_pos', compact('approved_pos'));
         }
         $approved_pos = Porder::where('site_id', '=', $site_id)->where('approval_status', '=', 'Approved')->latest()->paginate(15);
         return view('homepages.approved_pos', compact('approved_pos'));
      } catch (\Exception $e) {
         return $this->handleError($e, 'approved_pos()');
      }
   }

   public function processed_request()
   {
      try {
         Log::info('DashboardNavigationController | processed_request', [
            'user_details' => Auth::user(),
            'message' => 'Fetching processed requests.'
         ]);
         $site_id = Auth::user()->site->id;
         if (Auth::user()->hasRole('Department Authoriser')) {
            $department_id = Auth::user()->department->id;
         $processed_request = Sorder::leftJoin('users', 'users.id', '=', 'sorders.user_id')->
         where('sorders.site_id', '=', $site_id)
         ->where('users.department_id', '=', $department_id)
         ->where('sorders.status', '=', 'Supplied')->latest('sorders.created_at')->paginate(15);
         return view('homepages.processed_request', compact('processed_request'));
         }
         $processed_request = Sorder::where('site_id', '=', $site_id)->where('status', '=', 'Supplied')->latest()->paginate(15);
         return view('homepages.processed_request', compact('processed_request'));
      } catch (\Exception $e) {
         return $this->handleError($e, 'processed_request()');
      }
   }

   public function pending_request_approvals()
   {
      try {
         Log::info('DashboardNavigationController | pending_request_approvals', [
            'user_details' => Auth::user(),
            'message' => 'Fetching pending request approvals.'
         ]);
         $site_id = Auth::user()->site->id;
         if (Auth::user()->hasRole('Department Authoriser')) {
            $department_id = Auth::user()->department->id;
         $pending_request_approvals = Order::leftJoin('users', 'users.id', '=', 'orders.user_id')
         ->where('orders.site_id', '=', $site_id)
         ->where('users.department_id', '=', $department_id)
         ->whereNull('orders.approval_status')
         ->latest('orders.created_at')->
         select('orders.*')->
         paginate(15);
         return view('homepages.pending_request_approvals', compact('pending_request_approvals'));
         }

         $pending_request_approvals = Order::where('site_id', '=', $site_id)->whereNull('approval_status')->latest()->paginate(15);
         return view('homepages.pending_request_approvals', compact('pending_request_approvals'));


      } catch (\Exception $e) {
         return $this->handleError($e, 'pending_request_approvals()');
      }
   }

   public function pending_stock_approvals()
   {
      try {
         Log::info('DashboardNavigationController | pending_stock_approvals', [
            'user_details' => Auth::user(),
            'message' => 'Fetching pending stock approvals.'
         ]);
         $site_id = Auth::user()->site->id;
         if (Auth::user()->hasRole('Department Authoriser')) {
            $department_id = Auth::user()->department->id;
               $pending_stock_approvals = Sorder::with(['request_by', 'enduser'])
               ->leftJoin('users', 'users.id', '=', 'sorders.user_id')
               ->where('sorders.site_id', '=', $site_id)
               ->where('users.department_id', '=', $department_id)
               ->whereNull('sorders.approval_status')
               ->latest('sorders.created_at')
               ->select('sorders.*') // Select all columns from sorders
               ->paginate(15);
      //   dd($pending_stock_approvals);
         return view('homepages.pending_stock_approvals', compact('pending_stock_approvals'));
         // dd($pending_stock_approvals);
         }

         $pending_stock_approvals = Sorder::where('site_id', '=', $site_id)->whereNull('approval_status')->latest()->paginate(15);

         return view('homepages.pending_stock_approvals', compact('pending_stock_approvals'));
      } catch (\Exception $e) {
         return $this->handleError($e, 'pending_stock_approvals()');
      }
   }


   public function processed_pos()
   {
      try {
         Log::info('DashboardNavigationController | processed_pos', [
            'user_details' => Auth::user(),
            'message' => 'Fetching processed POs.'
         ]);
         $site_id = Auth::user()->site->id;
         if (Auth::user()->hasRole('Department Authoriser')) {
            $department_id = Auth::user()->department->id;
         $processed_pos = Porder::leftJoin('users', 'users.id', '=', 'porders.user_id')->
         where('porders.site_id', '=', $site_id)
         ->where('users.department_id', '=', $department_id)
         ->where('porders.status', '=', 'Ordered')->latest('porders.created_at')->paginate(15);
         return view('homepages.processed_pos', compact('processed_pos'));
         }
         $processed_pos = Porder::where('site_id', '=', $site_id)->where('status', '=', 'Ordered')->latest()->paginate(15);
         return view('homepages.processed_pos', compact('processed_pos'));
      } catch (\Exception $e) {
         return $this->handleError($e, 'processed_pos()');
}

   }

   public function stock_request_pending()
   {
      try {
         Log::info('DashboardNavigationController | stock_request_pending', [
            'user_details' => Auth::user(),
            'message' => 'Fetching stock request pending.'
         ]);
         $site_id = Auth::user()->site->id;
         $stock_request_pending = Sorder::where('site_id', '=', $site_id)->whereNull('approval_status')->latest()->paginate(15);
         return view('homepages.stock_request_pending', compact('stock_request_pending'));
      } catch (\Exception $e) {
         return $this->handleError($e, 'stock_request_pending()');
}

   }

   public function sofficer_stock_request_pending()
   {
      try {
         Log::info('DashboardNavigationController | sofficer_stock_request_pending', [
            'user_details' => Auth::user(),
            'message' => 'Fetching SO officer stock request pending.'
         ]);
         $site_id = Auth::user()->site->id;
         // Fix N+1 query by eager loading enduser relationship
         $sofficer_stock_request_pending = Sorder::with(['enduser', 'request_by', 'user'])
            ->where('site_id', '=', $site_id)
            ->where('status', '!=', 'Supplied')
            ->where('status', '!=', 'Partially Supplied')
            ->latest()
            ->paginate(15);

         return view('homepages.sofficer_stock_request_pending', compact('sofficer_stock_request_pending'));
      } catch (\Exception $e) {
         return $this->handleError($e, 'sofficer_stock_request_pending()');
}

   }

   public function rfi_pending_approval()
   {
      try {
         $authid = Auth::id();
         Log::info('DashboardNavigationController | rfi_pending_approval', [
            'user_details' => Auth::user(),
            'message' => 'Fetching RFI pending approval.'
         ]);
         $site_id = Auth::user()->site->id;
         $rfi_pending_approval = Sorder::where('site_id', '=', $site_id)->whereNull('approval_status')->where('requested_by', '=', $authid)->latest()->paginate(15);
         return view('homepages.rfi_pending_approval', compact('rfi_pending_approval'));
      } catch (\Exception $e) {
         return $this->handleError($e, 'rfi_pending_approval()');
}

   }

   public function rfi_approved_requests()
   {
      try {
         $authid = Auth::id();
         Log::info('DashboardNavigationController | rfi_approved_requests', [
            'user_details' => Auth::user(),
            'message' => 'Fetching RFI approved requests.'
         ]);
         $site_id = Auth::user()->site->id;
         $rfi_approved_requests = Sorder::where('site_id', '=', $site_id)->where('approval_status', '=', 'Approved')->where('requested_by', '=', $authid)->latest()->paginate(15);
         return view('homepages.rfi_approved_requests', compact('rfi_approved_requests'));
      } catch (\Exception $e) {
         return $this->handleError($e, 'rfi_approved_requests()');
}

   }

   public function rfi_processed_requests()
   {
      try {
         $authid = Auth::id();
         Log::info('DashboardNavigationController | rfi_processed_requests', [
            'user_details' => Auth::user(),
            'message' => 'Fetching RFI processed requests.'
         ]);
         $site_id = Auth::user()->site->id;
         $rfi_processed_requests = Sorder::where('site_id', '=', $site_id)->where('status', '=', 'Supplied')->where('requested_by', '=', $authid)
            ->orWhere('status', '=', 'Partially Supplied')->where('requested_by', '=', $authid)->latest()->paginate(15);

         return view('homepages.rfi_processed_requests', compact('rfi_processed_requests'));
      } catch (\Exception $e) {
         return $this->handleError($e, 'rfi_processed_requests()');
}

   }

   public function rfi_denied()
   {
      try {
         $authid = Auth::id();
         Log::info('DashboardNavigationController | rfi_denied', [
            'user_details' => Auth::user(),
            'message' => 'Fetching RFI denied requests.'
         ]);
         $site_id = Auth::user()->site->id;
         $rfi_denied =  Sorder::where('site_id', '=', $site_id)->where('approval_status', '=', 'Denied')->where('requested_by', '=', $authid)->latest()->paginate(15);
         return view('homepages.rfi_denied', compact('rfi_denied'));
      } catch (\Exception $e) {
         return $this->handleError($e, 'rfi_denied()');
}

   }
   public function po_total_value_of_approved_pos_mtd()
   {
      try {
         $authid = Auth::id();
         Log::info('DashboardNavigationController | po_total_value_of_approved_pos_mtd', [
            'po_total_value_of_approved_pos_mtd_details' => Auth::user(),
            'message' => 'Fetching po_total_value_of_approved_pos_mtd.'
         ]);
         $site_id = Auth::user()->site->id;
         $po_total_value_of_approved_pos_mtd =  PorderPart::where('site_id', '=', $site_id)->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->latest()->paginate(15);
         return view('homepages.po_total_value_of_approved_pos_mtd', compact('po_total_value_of_approved_pos_mtd'));
      } catch (\Exception $e) {
         return $this->handleError($e, 'po_total_value_of_approved_pos_mtd()');
}

   }

   public function po_total_value_of_supplied_pos_mtd()
   {
      try {
         $authid = Auth::id();
         Log::info('DashboardNavigationController | po_total_value_of_supplied_pos_mtd', [
            'po_total_value_of_supplied_pos_mtd_details' => Auth::user(),
            'message' => 'Fetching po_total_value_of_supplied_pos_mtd.'
         ]);
         $site_id = Auth::user()->site->id;
         $po_total_value_of_supplied_pos_mtd =  Porder::join('porder_parts', 'porders.order_id', '=', 'porder_parts.order_id')->whereBetween('porders.created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->where('porders.status', '=', 'Supplied')->where('site_id', '=', $site_id)->latest()->paginate(15);
         return view('homepages.po_total_value_of_supplied_pos_mtd', compact('po_total_value_of_supplied_pos_mtd'));
      } catch (\Exception $e) {
         return $this->handleError($e, 'po_total_value_of_supplied_pos_mtd()');
}

   }

   public function po_total_value_of_pending_pos_mtd()
   {
      try {
         $authid = Auth::id();
         Log::info('DashboardNavigationController | po_total_value_of_pending_pos_mtd', [
            'po_total_value_of_pending_pos_mtd_details' => Auth::user(),
            'message' => 'Fetching po_total_value_of_pending_pos_mtd.'
         ]);
         $site_id = Auth::user()->site->id;
         $po_total_value_of_pending_pos_mtd =  Porder::join('porder_parts', 'porders.order_id', '=', 'porder_parts.order_id')->whereBetween('porders.created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->whereNull('porders.approval_status')->where('site_id', '=', $site_id)->latest()->paginate(15);
         return view('homepages.po_total_value_of_pending_pos_mtd', compact('po_total_value_of_pending_pos_mtd'));
      } catch (\Exception $e) {
         return $this->handleError($e, 'po_total_value_of_pending_pos_mtd()');
}

   }

   public function po_approved_stock_requests()
   {
      try {
         $authid = Auth::id();
         Log::info('DashboardNavigationController | po_approved_stock_requests', [
            'po_approved_stock_requests_details' => Auth::user(),
            'message' => 'Fetching po_approved_stock_requests.'
         ]);
         $site_id = Auth::user()->site->id;
         $po_approved_stock_requests =  Sorder::where('site_id', '=', $site_id)->where('approval_status', '=', 'Approved')->latest()->paginate(15);
         return view('homepages.po_approved_stock_requests', compact('po_approved_stock_requests'));
      } catch (\Exception $e) {
         return $this->handleError($e, 'po_approved_stock_requests()');
}

   }

   public function po_approved_direct_requests()
   {
      try {
         $authid = Auth::id();
         Log::info('DashboardNavigationController | po_approved_direct_requests', [
            'po_approved_direct_requests_details' => Auth::user(),
            'message' => 'Fetching po_approved_direct_requests.'
         ]);
         $site_id = Auth::user()->site->id;
         $po_approved_direct_requests =   Order::where('site_id', '=', $site_id)->where('approval_status', '=', 'Approved')->latest()->paginate(15);
         return view('homepages.po_approved_direct_requests', compact('po_approved_direct_requests'));
      } catch (\Exception $e) {
         return $this->handleError($e, 'po_approved_direct_requests()');
}

   }

   public function po_approved_pos()
   {
      try {
         $authid = Auth::id();
         Log::info('DashboardNavigationController | po_approved_pos', [
            'po_approved_pos_details' => Auth::user(),
            'message' => 'Fetching po_approved_pos.'
         ]);
         $site_id = Auth::user()->site->id;
         $po_approved_pos =   Porder::where('site_id', '=', $site_id)->where('approval_status', '=', 'Approved')->latest()->paginate(15);
         return view('homepages.po_approved_pos', compact('po_approved_pos'));
      } catch (\Exception $e) {
         return $this->handleError($e, 'po_approved_pos()');
}

   }

   public function po_denied_requests()
   {
      try {
         $authid = Auth::id();
         Log::info('DashboardNavigationController | po_denied_requests', [
            'po_denied_requests_details' => Auth::user(),
            'message' => 'Fetching po_denied_requests.'
         ]);

         $site_id = Auth::user()->site->id;
         $po_denied_requests =   Order::where('site_id', '=', $site_id)->where('approval_status', '=', 'Denied')->latest()->paginate(15);
         return view('homepages.po_denied_requests', compact('po_denied_requests'));
      } catch (\Exception $e) {
         return $this->handleError($e, 'po_denied_requests()');
}

   }

   public function reorder_level()
   {
       $site_id = Auth::user()->site->id;
       $reorder_level = InventoryItem::select('inventory_items.id', 'inventory_items.item_id', 'inventory_items.site_id')
           ->leftJoin('items', 'items.id', '=', 'inventory_items.item_id')
           ->leftJoin('inventories', function($join) {
               $join->on('inventory_items.inventory_id', '=', 'inventories.id')
                    ->where('inventories.trans_type', '=', 'Stock Purchase');
           })
           ->where('inventory_items.site_id', '=', $site_id)
           ->whereRaw('items.stock_quantity <= items.reorder_level')
           ->where('inventory_items.quantity', '>', 0)
           ->get();
   
       return view('homepages.reorder_level', compact('reorder_level'));
   }
   

   public function reorder_level_search(Request $request)
   {
      $site_id = Auth::user()->site->id;
      $query = InventoryItem::select('inventory_items.id', 'inventory_items.item_id')
         ->join('items', 'items.id', '=', 'inventory_items.item_id')
         ->join('inventories', 'inventory_items.inventory_id', '=', 'inventories.id')
         ->whereRaw('items.stock_quantity <= items.reorder_level')
         ->where('inventory_items.quantity', '>', 0)
         ->where('trans_type', '=', 'Stock Purchase')
         ->where('inventory_items.site_id', '=', $site_id);

      // Check if search query exists
      if ($request->has('search')) {
         $searchTerm = $request->input('search');
         $query->where(function ($q) use ($searchTerm) {
            $q->where('items.item_description', 'like', '%' . $searchTerm . '%')
               ->orWhere('items.item_part_number', 'like', '%' . $searchTerm . '%')
               ->orWhere('items.item_stock_code', 'like', '%' . $searchTerm . '%');
         });
      }

      // Fetch the data
      $reorder_level = $query->get();

      return view('homepages.reorder_level', compact('reorder_level'));
   }


   public function low_stock_view($id)
   {
      $low_stock_view = InventoryItem::find($id);
      // dd($low_stock_view);
      return view('homepages.low_stock_view', compact('low_stock_view'));
   }

   public function active_user_accounts()
   {
      $site_id = Auth::user()->site->id;
      $active_user_accounts = User::where('site_id', '=', $site_id)->where('status', '=', 'Active')->latest()->paginate(15);
      return view('homepages.active_user_accounts', compact('active_user_accounts'));
   }
   public function disabled_user_accounts()
   {
      $site_id = Auth::user()->site->id;
      $disabled_user_accounts = User::where('site_id', '=', $site_id)->where('status', '=', 'Inactive')->latest()->paginate(15);
      return view('homepages.disabled_user_accounts', compact('disabled_user_accounts'));
   }

   public function active_endusers()
   {
      $site_id = Auth::user()->site->id;
      $active_endusers = Enduser::where('site_id', '=', $site_id)->where('status', '=', 'Active')->latest()->paginate(15);
      return view('homepages.active_endusers', compact('active_endusers'));
   }
   public function disabled_endusers()
   {
      $site_id = Auth::user()->site->id;
      $disabled_endusers = Enduser::where('site_id', '=', $site_id)->where('status', '=', 'Inactive')->latest()->paginate(15);
      return view('homepages.disabled_endusers', compact('disabled_endusers'));
   }

   public function departments()
   {
      $site_id = Auth::user()->site->id;
      $departments = Department::latest()->paginate(15);
      return view('homepages.departments', compact('departments'));
   }

   public function sections()
   {
      $site_id = Auth::user()->site->id;
      $sections = Section::latest()->paginate(15);
      return view('homepages.sections', compact('sections'));
   }

   public function dpr_pending_approval()
   {
      $site_id = Auth::user()->site->id;
      $authid = Auth::id();
      $dpr_pending_approval = Order::where('site_id', '=', $site_id)->whereNull('approval_status')->where('user_id', '=', $authid)->latest()->paginate(15);
      return view('homepages.dpr_pending_approval', compact('dpr_pending_approval'));
   }

   public function dpr_approved()
   {
      $site_id = Auth::user()->site->id;
      $authid = Auth::id();
      $dpr_approved = Order::where('site_id', '=', $site_id)->where('approval_status', '=', 'Approved')->where('user_id', '=', $authid)->latest()->paginate(15);
      return view('homepages.dpr_approved', compact('dpr_approved'));
   }
   public function dpr_processed()
   {
      $site_id = Auth::user()->site->id;
      $authid = Auth::id();
      $dpr_processed = Order::where('site_id', '=', $site_id)->where('status', '=', 'Supplied')->where('user_id', '=', $authid)->latest()->paginate(15);
      return view('homepages.dpr_processed', compact('dpr_processed'));
   }

   public function dpr_denied()
   {
      $site_id = Auth::user()->site->id;
      $authid = Auth::id();
      $dpr_denied = Order::where('site_id', '=', $site_id)->where('approval_status', '=', 'Denied')->where('user_id', '=', $authid)->latest()->paginate(15);
      return view('homepages.dpr_denied', compact('dpr_denied'));
   }
   public function out_of_stock()
   {
      try {
         $site_id = Auth::user()->site->id;
         // $unstocked = Item::where('stock_quantity', '=', '0')->get();
         $unstocked = InventoryItem::select(
            'items.id', 
            'items.item_description', 
            'items.item_part_number', 
            'items.item_stock_code', 
            'items.stock_quantity', 
            'items.reorder_level'
        )
        ->join('inventories', 'inventory_items.inventory_id', '=', 'inventories.id')
        ->join('items', 'inventory_items.item_id', '=', 'items.id')
        ->where('stock_quantity', '=', '0')
        ->where('inventories.trans_type', '=', 'Stock Purchase')
        ->where('inventory_items.site_id', '=', $site_id)
        ->groupBy(
            'items.id', 
            'items.item_description', 
            'items.item_part_number', 
            'items.item_stock_code', 
            'items.stock_quantity', 
            'items.reorder_level'
        )
        ->get();


         Log::info("InventoryController| out_of_stock() | ", [
            'user_details' => Auth::user(),
            'unstocked' => $unstocked,
            'message' => 'Out of stock details succesfully.'
         ]);
         // dd($unstocked);
         return view('homepages.out_of_stock', compact('unstocked'));
      } catch (\Exception $e) {
         return $this->handleError($e, 'out_of_stock()');
}

   }


   public function out_of_stock_view($id)
   {
      // $out_of_stock_view = InventoryItem::join('inventories','inventory_items.inventory_id','=','inventories.id')
      // ->join('items','inventory_items.item_id','=','items.id')
      // ->where('quantity', '=', '0')->where('inventories.trans_type','=','Stock Purchase')->where('inventory_items.id','=',$id)
      // ->first(); 
      $out_of_stock_view = InventoryItem::find($id);
      // dd($out_of_stock_view);
      // $out_of_stock_items = Item::where('id','=',$out_of_stock_view)->get();
      // dd($out_of_stock_items);
      return view('homepages.out_of_stock_view', compact('out_of_stock_view'));
   }
   public function items_list_site()
   {
      $site_id = Auth::user()->site->id;
      // $items = InventoryItem::join('items','inventory_items.item_id','=','items.id')
      // ->join('locations','locations.id','=','inventory_items.location_id')
      // ->join('inventories','inventories.id','=','inventory_items.inventory_id')
      // ->where('inventory_items.site_id','=',$site_id)
      // ->where('inventory_items.quantity', '>', '0')->get();
      // $items = Item::join('inventory_items', 'inventory_items.item_id', '=', 'items.id')
      //    ->leftjoin('inventories', 'inventories.id', '=', 'inventory_items.inventory_id')
      //    ->where('inventory_items.site_id', '=', $site_id)
      //    ->where('items.stock_quantity', '>', 0)
      //    ->select('items.*', 'inventory_items.*', 'inventories.trans_type')
      //    ->get();
      $items = Item::leftjoin('inventory_items', 'inventory_items.item_id', '=', 'items.id')
      ->leftjoin('inventories', 'inventories.id', '=', 'inventory_items.inventory_id')
      ->where('inventory_items.site_id', '=', $site_id)
      ->where('items.stock_quantity', '>', 0)
      ->select('items.*', 'inventory_items.*', 'inventories.trans_type')
      ->get();
      return view('homepages.items_list_site', compact('items'));
   }
}
