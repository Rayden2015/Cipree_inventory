<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Company;
use App\Models\SorderPart;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryItemDetail;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware(['auth', 'permission:monthly-reports'])->only('monthlyreport');
     
    }
    public function monthlyreport()
    {
        $site_id = Auth::user()->site->id;
        $company = Company::first();
      

        // Get the current date
        $currentDate = Carbon::now();
        
        // Calculate the date 12 months ago
        $date12MonthsAgo = $currentDate->copy()->subMonths(12);
        
        // Query to sum the data for each month for the last 12 months
        $received = InventoryItem::select(
                DB::raw("MONTHNAME(created_at) as monthname"),
                DB::raw("SUM(amount) as received")
            )
            ->whereBetween('created_at', [$date12MonthsAgo, $currentDate])
            ->where('inventory_items.site_id','=',$site_id)
            ->groupBy('monthname')
            ->latest()
            ->get();
        
        // Create an array to store the result
        $result_received = [];
        
        // Loop through the last 12 months
        for ($i = 0; $i < 12; $i++) {
            // Calculate the month name for the current iteration
            $monthName = Carbon::now()->subMonths($i)->format('F');
        
            // Check if the current month exists in the result set
            $found = false;
            foreach ($received as $data) {
                if ($data->monthname === $monthName) {
                    $result_received[] = [
                        'monthname' => $data->monthname,
                        'received' => $data->received,
                    ];
                    $found = true;
                    break;
                }
            }
        
            // If the month is not found, add it with received set to zero
            if (!$found) {
                $result_received[] = [
                    'monthname' => $monthName,
                    'received' => 0,
                ];
            }
        }
        
        // Output the result
        // dd($result);
        
   

      

        // Get the current date
        $currentDate = Carbon::now();
        
        // Calculate the date 12 months ago
        $date12MonthsAgo = $currentDate->copy()->subMonths(12);
        
        // Query to sum the data for each month for the last 12 months where sorder.status is 'Supplied'
        $supplied = SorderPart::join('sorders', 'sorder_parts.sorder_id', '=', 'sorders.id')
            ->whereIn('sorders.status', ['Supplied', 'Partially Supplied']);
            ->whereBetween('sorders.created_at', [$date12MonthsAgo, $currentDate])
            ->where('sorder_parts.site_id','=',$site_id)
            ->where('sorders.site_id','=',$site_id)
            ->select(
                DB::raw("MONTH(sorders.created_at) as month"),
                DB::raw("YEAR(sorders.created_at) as year"),
                DB::raw("SUM(sorder_parts.sub_total) as totalSupplied")
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        // Create an array to store the result
        $result_supplied = [];
        
        // Loop through the last 12 months
        for ($i = 0; $i < 12; $i++) {
            // Calculate the month and year for the current iteration
            $monthYear = Carbon::now()->subMonths($i)->format('Y-m');
        
            // Check if the current month and year exists in the result set
            $found = false;
            foreach ($supplied as $data) {
                if ($data->year . '-' . sprintf('%02d', $data->month) === $monthYear) {
                    $result_supplied[] = [
                        'year' => $data->year,
                        'month' => $data->month,
                        'totalSupplied' => $data->totalSupplied,
                    ];
                    $found = true;
                    break;
                }
            }
        
            // If the month and year is not found, add it with totalSupplied set to zero
            if (!$found) {
                $result_supplied[] = [
                    'year' => Carbon::now()->subMonths($i)->year,
                    'month' => Carbon::now()->subMonths($i)->month,
                    'totalSupplied' => 0,
                ];
            }
        }
        

        // Get the current date
        $currentDate = Carbon::now();
        
        // Initialize arrays to store opening and closing balances
        $openingBalances = [];
        $closingBalances = [];
        
        // Loop through the last 12 months
        for ($i = 0; $i < 12; $i++) {
            // Calculate the start and end dates for the current month
            $startDate = $currentDate->copy()->startOfMonth()->subMonths($i);
            $endDate = $currentDate->copy()->endOfMonth()->subMonths($i);
        
            // Calculate the opening balance for the current month
            $openingBalance = InventoryItem::whereDate('created_at', '<', $startDate)
            ->where('site_id','=', $site_id)
                                                 ->sum('amount');
        
            // Calculate the closing balance for the current month
            $closingBalance = InventoryItem::whereDate('created_at', '>=', $startDate)
                                                 ->whereDate('created_at', '<=', $endDate)
                                                 ->where('site_id','=', $site_id)
                                                 ->sum('amount');
        
            // Store the opening and closing balances for the current month
            $openingBalances[$startDate->format('F Y')] = $openingBalance;
            $closingBalances[$startDate->format('F Y')] = $closingBalance;
        }
        
        // Output or use the opening and closing balances as needed
        // dd($openingBalances, $closingBalances,$result_supplied,$result_received);
        

        return view('reports.monthly', compact(
            'company','openingBalances', 'closingBalances','result_supplied','result_received'
            ));
    }
}
