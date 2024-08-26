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
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $sites = Site::paginate(20);
            return view('sites.index', compact('sites'));
        } catch (\Throwable $th) {
            $unique_id = floor(time() - 999999999);
                Log::error('SiteController | Index() Error ' . $unique_id);
                Toastr::error('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the Feedback Button', 'Error');
                return redirect()->back();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sites.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

            Toastr::success('Successfully Updated:)', 'Success');
            return redirect()->route('sites.index');
        } catch (\Throwable $th) {
            $unique_id = floor(time() - 999999999);
                Log::error('SiteController | Store() Error ' . $unique_id);
                Toastr::error('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the Feedback Button', 'Error');
                return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // You can implement this method if needed
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $site = Site::find($id);
            return view('sites.edit', compact('site'));
        } catch (\Throwable $th) {
            $unique_id = floor(time() - 999999999);
            Log::error('SiteController | Edit() Error ' . $unique_id);
            Toastr::error('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the Feedback Button', 'Error');
            return redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

            Toastr::success('Successfully Updated:)', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            $unique_id = floor(time() - 999999999);
                Log::error('SiteController | Update() Error ' . $unique_id);
                Toastr::error('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the Feedback Button', 'Error');
                return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

            Toastr::success('Successfully Updated:)', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            $unique_id = floor(time() - 999999999);
                Log::error('SiteController | Destroy() Error ' . $unique_id);
                Toastr::error('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the Feedback Button', 'Error');
                return redirect()->back();
        }
    }
}
