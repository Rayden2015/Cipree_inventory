<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class SiteController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware(['auth', 'permission:view-site'])->only('show');
        $this->middleware(['auth', 'permission:add-site'])->only('create');
        $this->middleware(['auth', 'permission:view-site'])->only('index');
        $this->middleware(['auth', 'permission:edit-site'])->only('edit');
    }
    
   
    public function index()
    {
        try {
            $sites = Site::paginate(20);
            return view('sites.index', compact('sites'));
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
                Log::channel('error_log')->error('SiteController | Index() Error ' . $unique_id ,[
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
        return view('sites.create');
    }

 
    public function store(Request $request)
    {
        try {
            Site::create([
                'name' => $request->name,
            ]);

            $authId = Auth::user()->name;
            Log::info('SiteController | store', [
                'user_name' => $authId,
                'site_name' => $request->name,
                'site_code' => $request->code,
                'message' => 'Site added successfully.',
            ]);

           
            return redirect()->route('sites.index')->withSuccess('Successfully Updated');
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
                Log::channel('error_log')->error('SiteController | Store() Error ' . $unique_id ,[
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
        // You can implement this method if needed
    }

    
    public function edit($id)
    {
        try {
            $site = Site::find($id);
            return view('sites.edit', compact('site'));
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('SiteController | Edit() Error ' . $unique_id ,[
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
            $site = Site::find($id);

            Log::info('SiteController | update', [
                'user_details' => Auth::user(),
                'before_site_edit' => $site,
            ]);

            $site->name = $request->name;
            $site->site_code = $request->site_code;
            $site->save();

            $authId = Auth::user()->name;
            Log::info('SiteController | update', [
                'user_name' => $authId,
                'site_name' => $request->name,
                'message' => 'Site updated successfully.',
            ]);

          
            return redirect()->back()->withSuccess('Successfully updated');
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
                Log::channel('error_log')->error('SiteController | Update() Error ' . $unique_id ,[
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
            $site = Site::find($id);
            $authId = Auth::user()->name;

            Log::info('SiteController | destroy', [
                'user_name' => $authId,
                'site_name' => $site->name,
                'message' => 'Site deleted successfully.',
            ]);

            $site->delete();
            return redirect()->back()->withSuccess('Successfully Updated');
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
                Log::channel('error_log')->error('SiteController | Destroy() Error ' . $unique_id ,[
                    'message' => $e->getMessage(),
                    'stack_trace' => $e->getTraceAsString()
                ]);
    
        // Redirect back with the error message
        return redirect()->back()
                         ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
    }
    
    }
}
