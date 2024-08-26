<?php

namespace App\Http\Controllers;

use App\Models\Levy;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class LevyController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware(['auth', 'permission:view-levy'])->only('show');
        $this->middleware(['auth', 'permission:add-levy'])->only('create');
        $this->middleware(['auth', 'permission:view-levy'])->only('index');
        $this->middleware(['auth', 'permission:edit-levy'])->only('edit');
    }
    public function index()
    {
        $levies = Levy::latest()->paginate(15);
        return view('levies.index', compact('levies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('levies.create');
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
        Levy::create([
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
        $levy = Levy::find($id);
       return view('levies.edit', compact('levy'));
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
        $levy = Levy::find($id);
        $levy->description = $request->description;
        $levy->rate = $request->rate;
        $levy->others = $request->others;
        $levy->save();
        Toastr::Success('Successfully Updated:)','Success');
        return redirect()->back()->with('success','Successfully Deleted');
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $levy = Levy::find($id);
        $levy->delete();
        // Toastr::Success('Successfully Updated:)','Success');

        return redirect()->back()->with('success','Successfully Deleted');	
        //
    }
}
