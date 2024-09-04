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
    
    public function index()
    {
        try {
            $site_id = Auth::user()->site->id;
            Log::info('LocationController | index', [
                'user_details' => Auth::user(),
                'message' => 'Viewing location index'
            ]);

            $locations = Location::where('site_id', '=', $site_id)->latest()->paginate(15);
            return view('locations.index', compact('locations'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('LocationController | Index() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
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
                'name' => 'regex:/^[A-Za-z0-9. \"(),_-]+$/',
            ]);
            $site_id = Auth::user()->site->id;
            Location::create([
                'name' => $request->name,
                'site_id' => $site_id,
            ]);

            $authId = Auth::user()->name;
            Log::info('LocationController | store', [
                'user_details' => $authId,
                'located_name' => $request->name,
                'message' => 'Added a location'
            ]);

          
            return redirect()->route('locations.index')->with('success','Successfully Added');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('LocationController | Store() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
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
                'name' => 'regex:/^[A-Za-z0-9. \"(),_-]+$/',
            ]);

            $location = Location::find($id);
            $authId = Auth::user()->name;

            Log::info('LocationController | update (Before Editing)', [
                'user_details' => $authId,
                'location_name_before' => $location->name,
                'message' => 'Before editing location'
            ]);

            $location->name = $request->name;
            $location->save();

            Log::info('LocationController | update (After Editing)', [
                'user_details' => $authId,
                'location_name_after' => $request->name,
                'message' => 'Edited a location'
            ]);

            return redirect()->back()->with('success','Successfully Updated');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('LocationController | Update() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function destroy($id)
    {
        try {
            $location = Location::find($id);
            $authId = Auth::user()->name;

            Log::info('LocationController | destroy', [
                'user_details' => $authId,
                'location_name' => $location->name,
                'message' => 'Deleted a location'
            ]);

            $location->delete();

            // Toastr::success('Successfully Updated:)', 'Sucess');
            return redirect()->route('locations.index')->with('success', 'Successfully Deleted');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('LocationController | Destroy() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }
}
