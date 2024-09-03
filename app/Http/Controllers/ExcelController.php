<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\SorderPart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Exports\ItemsListSiteExport;
use App\Exports\SearchResultsExport;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;

class ExcelController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function exportSearchResults(Request $request)
    {

        // try{

            $site_id = Auth::user()->site->id;

        $start_date = Carbon::parse(request()->start_date)->toDateString();
        $end_date = Carbon::parse(request()->end_date)->toDateString();

        $total_cost_of_parts_within_the_month = SorderPart::select('sorders.delivered_on', 'sorders.request_number', 'items.item_description', 'items.item_part_number', 'items.item_stock_code', 'sorder_parts.quantity', 'sorder_parts.sub_total')
        ->where('sorder_parts.site_id','=', $site_id)
            ->join('sorders', 'sorders.id', '=', 'sorder_parts.sorder_id')->where('sorders.status', '=', 'Supplied')->where('sorders.site_id','=',$site_id)
            ->join('items', 'sorder_parts.item_id', '=', 'items.id')
            ->whereDate('sorders.delivered_on', '>=', $start_date)->whereDate('sorders.delivered_on', '<=', $end_date)
            ->latest('sorders.created_at')->get();


        Log::info("StoreReqquestController | supply_history_search()", [
            'user_details' => Auth::user(),
            'request_payload' => $request,
            'response_message' => 'Supply history search successful',
            'response_payload' => $total_cost_of_parts_within_the_month
        ]);
        // dd($total_cost_of_parts_within_the_month,$url,$url_start,$url_end);
        return Excel::download(new SearchResultsExport($total_cost_of_parts_within_the_month), 'search_results.xlsx');


    }
    public function exportItemsListSite()
    {
        return Excel::download(new ItemsListSiteExport, 'items.xlsx');
    }
}
