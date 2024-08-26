<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use App\Models\TotalTax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class TotalTaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $total_taxes = TotalTax::latest()->paginate(15);
        return view('total_taxes.index', compact('total_taxes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $taxes = Tax::all();
    return view('total_taxes.create', compact('taxes'));
    }
    public static function getTax()
    {
        try{
            $items = Tax::latest()->get();
            $output = '';
            foreach ($items as $item) {
                $output .= '<option value="' . $item->id . '">' . $item->description . ', ' . $item->rate .   '</option>';
            }
            Log::info('InventoryController| getItem() | Items loaded succesffully');
            return $output;
        }catch(\Exception $e){
            $unique_id = floor(time() - 999999999);
            Log::error('An error occurred with id ' . $unique_id);
            Toastr::error('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the Feedback Button', 'Error');
            Log::error('TotalTaxController | GetTax() | ', [
                'user_details' => Auth::user(),
                'error_message' => $e->getMessage()
            ]);
            return redirect()->back();
        }
        
    }
    public function fetch_single_tax(Request $request)
    {
        try{    
            $product = DB::select('select rate from taxes where  id = "' . $request->id . '"');
            Log::info('InventoryController | inventory_home_search');
            return response()->json($product);
        }catch(\Exception $e){
            $unique_id = floor(time() - 999999999);
            Log::error('An error occurred with id ' . $unique_id);
            Toastr::error('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the Feedback Button', 'Error');
            Log::error('TotalTaxController | FetchSingleTax() | ', [
                'user_details' => Auth::user(),
                'error_message' => $e->getMessage()
            ]);
            return redirect()->back();
        }
      
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TotalTax $totalTax)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TotalTax $totalTax)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TotalTax $totalTax)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TotalTax $totalTax)
    {
        //
    }
}
