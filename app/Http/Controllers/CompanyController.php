<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Helpers\UploadHelper;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware(['auth', 'permission:view-item-group'])->only('show');
        // $this->middleware(['auth', 'permission:add-item-group'])->only('create');
        $this->middleware(['auth', 'permission:info'])->only('index');
        // $this->middleware(['auth', 'permission:edit-item-group'])->only('edit');
    }

    public function index()
    {
        try {
            $company = Company::all();

            Log::info('CompanyController@index: Company data fetched successfully', [
                'user_details' => auth()->user(),
            ]);

            return view('company.index', compact('company'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('CategoryController | Index() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }
    public function edit($id)
    {
        $company = Company::find($id);
        return view('company.edit', compact('company'));
    }
    public function create()
    {
        return view('company.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                // Validation rules...
            ]);
            $site_id = Auth::user()->site->id;
            $company = new Company();
            // Populate company attributes...

            $company->save();

            if ($request->image) {
                $imageName = UploadHelper::upload($request->image, 'company-' . $company->id, 'images/company');
                $company->image = $imageName;
                $company->save();
            }

            Log::info('CompanyController@store: Company data stored successfully', [
                'user_details' => auth()->user(),
                'company_id' => $company->id,
            ]);


            return redirect()->route('company.index')->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('CompanyController | Store() Error ' . $unique_id, [
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
            $company = Company::find($id);
            // Validation rules and update logic...
            $company->address = $request->address;
            $company->phone = $request->phone;
            $company->email = $request->email;
            $company->vat = $request->vat;
            $company->vat_no = $request->vat_no;
            $company->website = $request->website;
            // $company->image = $request->image;

            $company->save();

            Log::info('CompanyController@update: Company data updated successfully', [
                'user_details' => auth()->user(),
                'company_id' => $id,
            ]);

            return redirect()->route('company.index')->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('CompanyController | Update() Error ' . $unique_id,[
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
            $company = Company::find($id);
            $company->delete();

            Log::info('CompanyController | destroy() | Company data deleted successfully', [
                'user_details' => auth()->user(),
                'company_id' => $id,
            ]);

            return redirect()->route('company.index')->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('CompanyController | Destroy() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }
}
