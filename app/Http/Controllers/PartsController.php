<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\Site;
use App\Models\Location;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class PartsController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
  
    public function index()
    {
        try {
            $site_id = Auth::user()->site->id;
            $parts = Part::where('site_id','=',$site_id)->get();

            Log::info('PartsController | index', [
                'user_details' => auth()->user(),
                'message' => 'Parts index loaded successfully.',
            ]);

            return view('parts.index', compact('parts'));
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('PartsController | Index() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            Log::info('PartsController | create', [
                'user_details' => auth()->user(),
                'message' => 'Create part form loaded successfully.',
            ]);

            return view('parts.create');
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('PartsController | Create() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'string'
            ]);

            Part::create([
                'name' => $request->name,
                'supplier_id' => $request->supplier_id,
                'description' => $request->description,
                'site_id' => $request->site_id,
                'location_id' => $request->location_id,
                'quantity' => $request->quantity
            ]);

          
            Log::info('PartsController | store', [
                'user_details' => auth()->user(),
                'message' => 'Part stored successfully.',
            ]);

            return redirect()->route('parts.index')->withSuccess('Successfully Updated');
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('PartsController | Store() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    // ... other methods ...

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $part = Part::find($id);
            $part->delete();

           
            Log::info('PartsController | destroy', [
                'user_details' => auth()->user(),
                'message' => 'Part deleted successfully.',
            ]);

            return redirect()->route('parts.index')->withSuccess('Updated Successfully');
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('PartsController | Destroy() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    // ... other methods ...

    public function selectSearch(Request $request)
    {
        try {
            $movies = Supplier::all();

            if ($request->has('q')) {
                $search = $request->q;
                $movies = Supplier::select("id", "name")
                    ->where('name', 'LIKE', "%$search%")->orWhere('phone', 'LIKE', "%$search%")
                    ->get();
            }

            Log::info('PartsController | selectSearch', [
                'user_details' => auth()->user(),
                'message' => 'Supplier search completed successfully.',
            ]);

            return response()->json($movies);
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('PartsController | SelectSearch() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}
    }


    // ... other methods ...

    public function selectSite(Request $request)
    {
        try {
            $site_id = Auth::user()->site->id;
            $movies = Site::where('site_id','=',$site_id)->get();

            if ($request->has('q')) {
                $search = $request->q;
                $movies = Site::select("id", "name")
                    ->where('name', 'LIKE', "%$search%")->orWhere('phone', 'LIKE', "%$search%")
                    ->where('site_id','=', $site_id)
                    ->get();
            }

            Log::info('PartsController | selectSite', [
                'user_details' => auth()->user(),
                'message' => 'Site search completed successfully.',
            ]);

            return response()->json($movies);
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('PartsController | SelectSite() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }
}
