<?php

namespace App\Http\Controllers;

use App\Models\Uom;
use Illuminate\Http\Request;

class UomController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['auth', 'permission:view-uom'])->only('show');
        $this->middleware(['auth', 'permission:add-uom'])->only('create');
        $this->middleware(['auth', 'permission:view-uom'])->only('index');
        $this->middleware(['auth', 'permission:edit-uom'])->only('edit');
    }
    public function index()
    {
        $uom = Uom::latest()->paginate(20);
        return view('uom.index', compact('uom'));
    }

    public function create()
    {
        return view('uom.create');
    }

    public function edit($id){

        $uom = Uom::find($id);
        return view('uom.edit',compact('uom'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|unique:uoms,name,except,id'
        ]);

        $uom = new Uom();
        $uom->name = $request->name;
        $uom->save();
    
        return redirect()->back()->with('success','Successfully Saved');
    }

    public function update(Request $request,$id){
       
        $request->validate([
            'name' => 'nullable|unique:uoms,name,'.$id
        ]);
        
        $uom = Uom::find($id);
        $uom->name = $request->name;
        $uom->save();
        return redirect()->back()->with('success','Updated Successfully');

    }

    public function destroy($id){
        $uom = Uom::find($id);
        $uom->delete();
        return redirect()->back()->with('success','Deleted Successfully');
    }
}
