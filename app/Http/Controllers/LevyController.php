<?php

namespace App\Http\Controllers;

use App\Models\Levy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
    try {
        // Validate the input
        $request->validate([
            'rate' => ['numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'description' => 'required|unique:levies,description', // Validate for unique description
        ]);

        // Get the site ID of the authenticated user
        $site_id = Auth::user()->site->id;

        // Create a new levy record
        Levy::create([
            'description' => $request->description,
            'rate' => $request->rate,
            'others' => $request->others,
            'site_id' => $site_id,
        ]);

        // Redirect back with a success message
        return redirect()->back()->withSuccess('Successfully Updated');
    } catch (\Illuminate\Validation\ValidationException $e) {
        // If validation fails, return back with errors
        return redirect()->back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        // Generate a unique error ID
        $unique_id = floor(time() - 999999999);

        // Log the error with details in the custom error log channel
        Log::channel('error_log')->error('LevyController | store() Error ' . $unique_id, [
            'message' => $e->getMessage(),
            'stack_trace' => $e->getTraceAsString(),
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
        $levy = Levy::find($id);
       return view('levies.edit', compact('levy'));
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    try {
        // Validate the input
        $request->validate([
            'rate' => ['numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'description' => 'required|unique:levies,description,' . $id,
        ]);

        // Find the levy by ID
        $levy = Levy::find($id);

        if (!$levy) {
            return redirect()->back()->withError('Levy not found.');
        }

        // Update the levy details
        $levy->description = $request->description;
        $levy->rate = $request->rate;
        $levy->others = $request->others;
        $levy->save();

        // Redirect back with a success message
        return redirect()->back()->withSuccess('Successfully Updated');
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Redirect back with validation errors
        return redirect()->back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        // Generate a unique error ID
        $unique_id = floor(time() - 999999999);
        
        // Log the error with details
        Log::channel('error_log')->error('LevyController | update() Error ' . $unique_id, [
            'message' => $e->getMessage(),
            'stack_trace' => $e->getTraceAsString(),
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
        $levy = Levy::find($id);
        $levy->delete();

        return redirect()->back()->withSuccess('Successfully Deleted');	
        //
    }
}
