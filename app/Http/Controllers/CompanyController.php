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
            Log::error('CategoryController | Index() Error ' . $unique_id);
            Toastr::error('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the Feedback Button', 'Error');
            return redirect()->back();
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

            Toastr::success('Successfully Updated:)', 'Success');
            return redirect()->route('company.index');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::error('CompanyController | Store() Error ' . $unique_id);
            Toastr::error('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the Feedback Button', 'Error');
            return redirect()->back();
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

            Toastr::success('Successfully Updated:)', 'Sucess');
            return redirect()->route('company.index');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::error('CompanyController | Update() Error ' . $unique_id);
            Toastr::error('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the Feedback Button', 'Error');
            return redirect()->back();
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

            Toastr::success('Successfully Deleted:)', 'Success');
            return redirect()->route('company.index');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::error('CompanyController | Destroy() Error ' . $unique_id);
            Toastr::error('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the Feedback Button', 'Error');
            return redirect()->back();
        }
    }
}
