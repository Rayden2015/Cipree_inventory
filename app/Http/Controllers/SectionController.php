<?php

namespace App\Http\Controllers;

use App\Models\Enduser;
use App\Models\Section;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware(['auth', 'permission:view-section'])->only('show');
        $this->middleware(['auth', 'permission:add-section'])->only('create');
        $this->middleware(['auth', 'permission:view-section'])->only('index');
        $this->middleware(['auth', 'permission:edit-section'])->only('edit');
    }
    
 
    public function index()
    {
        $site_id = Auth::user()->site->id;
        $sections = Section::latest()->paginate(15);
        return view('sections.index', compact('sections'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('sections.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'unique:sections,name,except,id',
                'description' => 'unique:sections,description,except,id',
            ]);
            $site_id = Auth::user()->site->id;
            Section::create([
                'name' => $request->name,
                'description' => $request->description,
                'site_id'=>$site_id,
            ]);

            $authId = Auth::user()->name;
            Log::info('Create section', [
                'user ' => $authId,
                'details' => $request,
            ]);
           
            return redirect()->back()->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
                Log::channel('error_log')->error('SectionController | Store() Error ' . $unique_id  ,[
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
        $section =  Section::find($id);
        return view('sections.edit', compact('section'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        try {
            $section =  Section::find($id);
            $section->name = $request->name;
            $section->description = $request->description;
            $section->save();
        
            return redirect()->back()->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
                Log::channel('error_log')->error('SectionController | Update() Error ' . $unique_id  ,[
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
            Log::info('Section before del', [
                'User' => $authId,
                'details' => Section::find($id)
            ]);
            $section = Section::find($id);
            $section->delete();
            return redirect()->back()->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
                Log::channel('error_log')->error('SectionController | Destroy() Error ' . $unique_id  ,[
                    'message' => $e->getMessage(),
                    'stack_trace' => $e->getTraceAsString()
                ]);
    
        // Redirect back with the error message
        return redirect()->back()
                         ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
    }
    
    }
}
