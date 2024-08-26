<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class TaxLeviesController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware(['auth', 'permission:view-tax'])->only('show');
        $this->middleware(['auth', 'permission:add-tax'])->only('create');
        $this->middleware(['auth', 'permission:view-tax'])->only('index');
        $this->middleware(['auth', 'permission:edit-tax'])->only('edit');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taxes = Tax::latest()->paginate(15);
        return view('taxes.index', compact('taxes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('taxes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'rate' => ['numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
        ]);
        $site_id = Auth::user()->site->id;
        Tax::create([
            'description' => $request->description,
            'rate' => $request->rate,
            'others' => $request->others,
            // 'site_id'=>$site_id,

        ]);
        Toastr::Success('Successfully Updated:)', 'Success');

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $tax = Tax::find($id);
        return view('taxes.edit', compact('tax'));
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'rate' => ['numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
        ]);
        $tax = Tax::find($id);
        $tax->description = $request->description;
        $tax->rate = $request->rate;
        $tax->others = $request->others;
        $tax->save();
        Toastr::Success('Successfully Updated:)', 'Success');
        return redirect()->back();
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tax = Tax::find($id);
        $tax->delete();
        // Toastr::Success('Successfully Updated:)', 'Success');
        return redirect()->back()->with('success', 'Successfully Deleted');
        //
    }
}
