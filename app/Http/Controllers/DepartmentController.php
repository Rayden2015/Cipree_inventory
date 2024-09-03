<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware(['auth', 'permission:view-department'])->only('show');
        $this->middleware(['auth', 'permission:add-department'])->only('create');
        $this->middleware(['auth', 'permission:view-department'])->only('index');
        $this->middleware(['auth', 'permission:edit-department'])->only('edit');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $site_id = Auth::user()->site->id;
        $departments = Department::latest()->paginate(15);
        return view('departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('departments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'unique:departments,name,except,id',
                'description' => 'unique:departments,description,except,'
            ]);
            $site_id = Auth::user()->site->id;
            Department::create([
                'name' => $request->name,
                'description' => $request->description,
                'site_id'=>$site_id,
            ]);

            $authId = Auth::user()->name;
            Log::info('Create department', [
                'user ' => $authId,
                'details' => $request,
            ]);
          
            return redirect()->back()->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::error('DepartmentController | Store() Error ' . $unique_id ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

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
        $department =  Department::find($id);
        return view('departments.edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        try {
            $department =  Department::find($id);
            $department->name = $request->name;
            $department->description = $request->description;
            $department->save();
            return redirect()->back()->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::error('DepartmentController | Update() Error ' . $unique_id ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $authId = Auth::user();
            Log::info('Department before del', [
                'User' => $authId,
                'details' => Department::find($id)
            ]);
            $department = Department::find($id);
            $department->delete();
        
            return redirect()->back()->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::error('DepartmentController | Destroy() Error ' . $unique_id ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }
}
