<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware(['auth', 'permission:view-location'])->only('show');
        $this->middleware(['auth', 'permission:add-location'])->only('create');
        $this->middleware(['auth', 'permission:view-location'])->only('index');
        $this->middleware(['auth', 'permission:edit-location'])->only('edit');
    }
    
    public function index(Request $request)
    {
        try {
            $query = Location::latest();

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $locations = $query->paginate(15)->withQueryString();

            Log::info('LocationController | index() | Locations loaded successfully', [
                'user_details' => Auth::user(),
                'count' => $locations->total(),
                'current_page' => $locations->currentPage(),
            ]);
    
            return view('locations.index', compact('locations'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
    
            Log::channel('error_log')->error('LocationController | Index() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
    
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }
  
    public function create()
    {
        try {
            Log::info('LocationController | create', [
                'user_details' => Auth::user(),
                'message' => 'Viewing location create form'
            ]);

            return view('locations.create');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('LocationController | Create() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');

        }
    }

 
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|regex:/^[A-Za-z0-9. \"(),_-]+$/|max:255',
                'description' => 'nullable|string'
            ]);
            
            $user = Auth::user();
            $site_id = $user->site->id;
            $tenant_id = $user->getCurrentTenant()?->id ?? $user->site->tenant_id ?? null;
            
            Location::create([
                'name' => $request->name,
                'description' => $request->description ?? null,
                'site_id' => $site_id,
                'tenant_id' => $tenant_id,
            ]);

            Log::info('LocationController | store() | Added a location', [
                'user_details' => Auth::user(),
                'location_name' => $request->name,
            ]);

            return redirect()->route('locations.index')->with('success', 'Location created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('LocationController | Store() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }

   
    public function show($id)
    {
        // Not implemented for now.
    }

   
    public function edit($id)
    {
        try {
            $location = Location::find($id);

            Log::info('LocationController | edit', [
                'user_details' => Auth::user(),
                'location_id' => $id,
                'message' => 'Viewing location edit form'
            ]);

            return view('locations.edit', compact('location'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('LocationController | Edit() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

   
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|regex:/^[A-Za-z0-9. \"(),_-]+$/|max:255',
                'description' => 'nullable|string'
            ]);

            $location = Location::findOrFail($id);

            Log::info('LocationController | update() | Before editing location', [
                'user_details' => Auth::user(),
                'location_id' => $id,
                'location_name_before' => $location->name,
            ]);

            $location->name = $request->name;
            $location->description = $request->description ?? $location->description;
            $location->save();

            Log::info('LocationController | update() | Edited location', [
                'user_details' => Auth::user(),
                'location_id' => $id,
                'location_name_after' => $request->name,
            ]);

            return redirect()->route('locations.index')->with('success', 'Location updated successfully');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('LocationController | Update() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }

    public function destroy($id)
    {
        try {
            $location = Location::findOrFail($id);
            $locationName = $location->name;
            $location->delete();

            Log::info('LocationController | destroy() | Deleted location', [
                'user_details' => Auth::user(),
                'location_name' => $locationName,
            ]);

            return redirect()->route('locations.index')->with('success', 'Location deleted successfully');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('LocationController | Destroy() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }
}
