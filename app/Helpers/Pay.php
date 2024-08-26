<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Order;
use App\Models\Porder;
use App\Models\Sorder;
use App\Models\Inventory;
use App\Models\SprPorder;
use Illuminate\Support\Str;
use App\Models\StoreRequest;
use App\Models\ClientPayment;
use App\Models\PaymentRecord;
use Illuminate\Support\Facades\DB;
use App\Models\StockPurchaseRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class Pay
{
    

    public static function genRefCode()
    {
        $siteName = Auth::user()->site->name;
        $siteInitials = strtoupper(substr($siteName, 0, 3));
        $currentYear = Carbon::now()->year; // e.g., 2024
        $yearSuffix = substr($currentYear, -2); // e.g., 24
        $genRefCode = 'DPR/REQ/'.$siteInitials.'-'.$yearSuffix;
        // $current_year_ = Carbon::now()->format('Y');
        // $current_year = substr($current_year_, 2);
        $lastorderId = Order::orderBy('id', 'desc')->value('id');

        return $genRefCode  . str_pad($lastorderId + 1, 5, "0", STR_PAD_LEFT);
    }

    public static function genPurchaseCode()
    {
        $siteName = Auth::user()->site->name;
        $siteInitials = strtoupper(substr($siteName, 0, 3));
        $currentYear = Carbon::now()->year; // e.g., 2024
        $yearSuffix = substr($currentYear, -2); // e.g., 24
        $genPurchaseCode = 'POD/REQ/'.$siteInitials.'-'.$yearSuffix;
        $lastorderId = Porder::orderBy('id', 'desc')->value('id');
        return $genPurchaseCode . str_pad($lastorderId + 1, 10, "0", STR_PAD_LEFT);
    }

    public static function genSprPurchaseCode()
    {
        $siteName = Auth::user()->site->name;
        $siteInitials = strtoupper(substr($siteName, 0, 3));
        $currentYear = Carbon::now()->year; // e.g., 2024
        $yearSuffix = substr($currentYear, -2); // e.g., 24
        $genSprPurchaseCode = 'POD/REQ/SPR/'.$siteInitials.'-'.$yearSuffix;
        $lastorderId = SprPorder::orderBy('id', 'desc')->value('id');
        return $genSprPurchaseCode . str_pad($lastorderId + 1, 10, "0", STR_PAD_LEFT);
    }

    public static function genRefCode1()
    {
        return date('H') . mt_rand(10000, 999999);
    }
    public static function genRefStCode()
    {
        $siteName = Auth::user()->site->name;
        $siteInitials = strtoupper(substr($siteName, 0, 3));
        $currentYear = Carbon::now()->year; // e.g., 2024
        $yearSuffix = substr($currentYear, -2); // e.g., 24
        $genRefStCode = 'STK/REQ/'.$siteInitials.'-'.$yearSuffix;
        $lastorderId = Sorder::orderBy('id', 'desc')->value('id');

        return $genRefStCode . str_pad($lastorderId + 1, 5, "0", STR_PAD_LEFT);
    }

    public static function genRefSprCode()
    {
        $siteName = Auth::user()->site->name;
        $siteInitials = strtoupper(substr($siteName, 0, 3));
        $currentYear = Carbon::now()->year; // e.g., 2024
        $yearSuffix = substr($currentYear, -2); // e.g., 24
        $genRefSprCode = 'SPR/REQ/'.$siteInitials.'-'.$yearSuffix;
        $lastorderId = StockPurchaseRequest::orderBy('id', 'desc')->value('id');

        return $genRefSprCode . str_pad($lastorderId + 1, 5, "0", STR_PAD_LEFT);
    }

    public static function genGrnCode()
    {
        $siteName = Auth::user()->site->name;
        $siteInitials = strtoupper(substr($siteName, 0, 3));
        $currentYear = Carbon::now()->year; // e.g., 2024
        $yearSuffix = substr($currentYear, -2); // e.g., 24
    
        $genGrnCode = 'GRN/REQ/'.$siteInitials.'-'.$yearSuffix;
        $month = Carbon::now()->format('m');
        $lastorderId = Inventory::orderBy('id', 'desc')->value('id');
        $grn_number = Inventory::orderBy('id', 'desc')->value('grn_number');
        $new = substr($grn_number, 6, 2);
        return $genGrnCode  . '/' . $month . '/' . str_pad($lastorderId + 1, 4, "0", STR_PAD_LEFT);
        // if ($month == $new) {
           
        
    
        // return $genGrnCode  . '/' . $month . '/0001'; // Reset the order number if month changes
    }
    

    public static function genStockCode(Request $request)
    {
        $lastorderId = Item::orderBy('id', 'desc')->value('id');
    }

    public static function genDeliveryNum()
    {
        $siteName = Auth::user()->site->name;
        $siteInitials = strtoupper(substr($siteName, 0, 3));
        $currentYear = Carbon::now()->year; // e.g., 2024
        $yearSuffix = substr($currentYear, -2); // e.g., 24

        $genDeliveryNum = 'SRN/REQ'.$siteInitials.'-'.$yearSuffix;
        $lastorderId = Sorder::orderBy('id', 'desc')->value('id');
        return $genDeliveryNum  . str_pad($lastorderId, 5, "0", STR_PAD_LEFT);
    }
}
