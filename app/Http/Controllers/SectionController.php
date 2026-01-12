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
    
 
    public function index(Request $request)
    {
        try {
            $query = Section::latest();

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $sections = $query->paginate(15)->withQueryString();

            Log::info('SectionController | index() | Sections loaded successfully', [
                'user_details' => Auth::user(),
                'count' => $sections->total(),
            ]);

            return view('sections.index', compact('sections'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('SectionController | Index() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
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
                'name' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);
            
            $user = Auth::user();
            $site_id = $user->site->id;
            $tenant_id = $user->getCurrentTenant()?->id ?? $user->site->tenant_id ?? null;
            
            Section::create([
                'name' => $request->name,
                'description' => $request->description,
                'site_id' => $site_id,
                'tenant_id' => $tenant_id,
            ]);

            Log::info('SectionController | store() | Created section', [
                'user_details' => Auth::user(),
                'section_name' => $request->name,
            ]);
           
            return redirect()->route('sectionslist.index')->with('success', 'Section created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('SectionController | Store() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
    
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
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);
            
            $section = Section::findOrFail($id);
            $section->name = $request->name;
            $section->description = $request->description;
            $section->save();
            
            Log::info('SectionController | update() | Updated section', [
                'user_details' => Auth::user(),
                'section_id' => $id,
                'section_name' => $request->name,
            ]);
        
            return redirect()->route('sectionslist.index')->with('success', 'Section updated successfully');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('SectionController | Update() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
    
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
            $section = Section::findOrFail($id);
            $sectionName = $section->name;
            $section->delete();
            
            Log::info('SectionController | destroy() | Deleted section', [
                'user_details' => Auth::user(),
                'section_name' => $sectionName,
            ]);
            
            return redirect()->route('sectionslist.index')->with('success', 'Section deleted successfully');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('SectionController | Destroy() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
    
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }
}
