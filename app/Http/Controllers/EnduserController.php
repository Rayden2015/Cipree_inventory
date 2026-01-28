<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Enduser;
use App\Models\Section;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\EndUsersCategory;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Traits\LogsErrors;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EndusersImport;
use App\Exports\EndusersImportTemplateExport;

class EnduserController extends Controller
{
    use LogsErrors;
    
    public function __construct() {
        $this->middleware('auth');
        $this->middleware(['auth', 'permission:view-enduser'])->only('show');
        $this->middleware(['auth', 'permission:add-enduser'])->only('create');
        $this->middleware(['auth', 'permission:view-enduser'])->only('index');
        $this->middleware(['auth', 'permission:edit-enduser'])->only('edit');
    }
    

    public function index()
    {
        try {
            Log::info('EnduserController | index()', [
                'user_details' => Auth::user(),
                'message' => 'Fetching endusers.'
            ]);
            
            // Fix: Check if user has a site assigned
            if (!Auth::user()->site) {
                Log::error('EnduserController | index() | User has no site assigned', [
                    'user_id' => Auth::user()->id,
                    'user_email' => Auth::user()->email
                ]);
                return redirect()->back()
                    ->withError('Your account is not assigned to a site. Please contact the administrator.');
            }
            
            $site_id = Auth::user()->site->id;
            
            // Fix N+1 query by eager loading department and section relationships
            // Use 'departmente' and 'sectione' to match the view expectations
            $endusers = Enduser::with(['departmente', 'sectione'])
                ->where('site_id', '=', $site_id)
                ->latest()
                ->paginate(15);
            
            // Fix: Filter categories by site_id
            $endusercategories = Enduser::where('site_id','=',$site_id)
                ->groupBy('type')
                ->pluck('type');
                
            return view('endusers.index', compact('endusers','endusercategories'));
        } catch (\Exception $e) {
            return $this->handleError($e, 'index()');
        }
    }

    public function search(Request $request)
    {
        try {
            if (!Auth::user()->site) {
                return redirect()->back()
                    ->withError('Your account is not assigned to a site. Please contact the administrator.');
            }
            
            $site_id = Auth::user()->site->id;
            $query = $request->input('query');
            
            // Fix: Group where clauses properly to avoid SQL logic issues
            // Use eager loading to match view expectations
            $endusers = Enduser::with(['departmente', 'sectione'])
                ->where('site_id', '=', $site_id)
                ->where(function($q) use ($query) {
                    $q->where('asset_staff_id', 'like', '%' . $query . '%')
                      ->orWhere('model', 'like', '%' . $query . '%')
                      ->orWhere('serial_number', 'like', '%' . $query . '%')
                      ->orWhere('name_description', 'like', '%' . $query . '%')
                      ->orWhereHas('departmente', function($q) use ($query) {
                          $q->where('name', 'like', '%' . $query . '%');
                      });
                })
                ->latest()
                ->paginate(15);
                
            $endusercategories = Enduser::where('site_id', '=', $site_id)
                ->groupBy('type')
                ->pluck('type');
                
            return view('endusers.index', compact('endusers', 'endusercategories'));
        } catch (\Exception $e) {
            return $this->handleError($e, 'search()');
        }
    }

    public function show($id){
        $enduser = Enduser::find($id);
        return view('endusers.show', compact('enduser'));
    }
    
    public function endusersort(Request $request)
    {
        try {
            if (!Auth::user()->site) {
                return redirect()->back()
                    ->withError('Your account is not assigned to a site. Please contact the administrator.');
            }
            
            $site_id = Auth::user()->site->id;
            $enduserCategoryId = $request->input('enduser_category_id');

            // Use eager loading to match view expectations
            if ($enduserCategoryId === 'all' || empty($enduserCategoryId)) {
                $endusers = Enduser::with(['departmente', 'sectione'])
                    ->where('site_id', '=', $site_id)
                    ->latest()
                    ->paginate(15);
            } else {
                $endusers = Enduser::with(['departmente', 'sectione'])
                    ->where('site_id', '=', $site_id)
                    ->where('type', '=', $enduserCategoryId)
                    ->latest()
                    ->paginate(15);
            }

            $endusercategories = Enduser::where('site_id', '=', $site_id)
                ->groupBy('type')
                ->pluck('type');

            return view('endusers.index', compact('endusers', 'endusercategories'));
        } catch (\Exception $e) {
            return $this->handleError($e, 'endusersort()');
        }
    }

    public function create()
    {
        $site_id = Auth::user()->site->id;
        $sites = Site::all();
        $departments = Department::all();
        $sections = Section::all();
        $endusercategories = EndUsersCategory::where('site_id','=',$site_id)->get();
        return view('endusers.create', compact('sites', 'departments', 'sections','endusercategories'));
    }

    public function edit($id)
    {
        try {
            Log::info('EnduserController | edit()', [
                'user_details' => Auth::user(),
                'message' => 'Editing enduser with ID: ' . $id
            ]);
            $site_id = Auth::user()->site->id;
            $enduser = Enduser::find($id);
            $sites = Site::all();
            $sections = Section::all();
            $departments = Department::all();
            $endusercategories = Enduser::where('site_id','=',$site_id)->pluck('type');
            return view('endusers.edit', compact('enduser', 'sites', 'sections', 'departments','endusercategories'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('EndUserController | Edit() Error ' . $unique_id ,[
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
            // Validate required fields
            $request->validate([
                'department_id' => 'required|exists:departments,id',
                'type' => 'required|string',
                'name_description' => 'required|string',
            ]);

            // Check if user has a site assigned
            if (!Auth::user()->site) {
                Log::error('EnduserController | store() | User has no site assigned', [
                    'user_id' => Auth::user()->id,
                    'user_email' => Auth::user()->email
                ]);
                return redirect()->back()
                    ->withInput()
                    ->withError('Your account is not assigned to a site. Please contact the administrator.');
            }

            $user = Auth::user();
            $site_id = $user->site->id;
            $tenant_id = $user->getCurrentTenant()?->id ?? $user->site->tenant_id ?? null;
            
            // Create the enduser
            // Note: 'name' field is required by database schema, use name_description value
            $enduser = Enduser::create([
                'name' => $request->name ?? $request->name_description, // Required by schema
                'asset_staff_id' => $request->asset_staff_id,
                'name_description' => $request->name_description,
                'department' => $request->department,
                'section' => $request->section,
                'model' => $request->model,
                'serial_number' => $request->serial_number,
                'type' => $request->type,
                'manufacturer' => $request->manufacturer,
                'designation' => $request->designation,
                'status' => $request->status ?? 'Active',
                'section_id' => $request->section_id,
                'department_id' => $request->department_id,
                'site_id' => $site_id,
                'tenant_id' => $tenant_id,
                'enduser_category_id' => $request->enduser_category_id,
            ]);

            Log::info(
                'EnduserController| store() | Added an Enduser',
                [
                    'user_details' => Auth::user(),
                    'enduser_id' => $enduser->id,
                    'response_payload' => $request->all(),
                ]
            );
          
            return redirect()->route('endusers.index')->withSuccess('Enduser created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('EndUserController | Store() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withInput()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'asset_staff_id' => 'unique:endusers,asset_staff_id,' . $id, // Unique validation except for the current user
            'department_id' => 'required|exists:departments,id',
            // Add other validation rules as needed
        ]);

        try {
            // Use withoutGlobalScopes to ensure we can find the record
            // even when tenant or other global scopes are applied.
            $enduser = Enduser::withoutGlobalScopes()->find($id);

            // Check if Enduser exists
            if (!$enduser) {
                return redirect()->back()->withError(['Enduser not found']);
            }
            
            // Update the Enduser fields
            // Note: 'name' field is required by database schema (even if not used in production)
            $enduser->asset_staff_id = $request->input('asset_staff_id');
            $enduser->name = $request->input('name') ?? $request->input('name_description') ?? $enduser->name;
            $enduser->name_description = $request->input('name_description');
            $enduser->department = $request->input('department');
            $enduser->section = $request->input('section');
            $enduser->model = $request->input('model');
            $enduser->serial_number = $request->input('serial_number');
            $enduser->type = $request->input('type');
            $enduser->designation = $request->input('designation');
            $enduser->manufacturer = $request->input('manufacturer');
            $enduser->status = $request->input('status');
            
            // Only update the site_id if the user is a Super Admin
            if (Auth::user()->hasRole('Super Admin')) {
                $enduser->site_id = $request->input('site_id');
            }
            
            $enduser->department_id = $request->input('department_id');
            $enduser->section_id = $request->input('section_id');
            $enduser->enduser_category_id = $request->input('enduser_category_id', $enduser->enduser_category_id);
            
            // Save the changes
            $enduser->save();
              // Log the update operation
              $authId = Auth::user()->name;
              Log::info('EnduserController| update() | Edited an Enduser', [
                  'user_details' => Auth::user(),
                  'user_name' => $authId,
                  'response_payload' => $enduser,
              ]);

        
            return redirect()->back()->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('EndUserController | Update() Error ' . $unique_id ,[
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
            $enduser = Enduser::find($id);

            Log::info('EnduserController | destroy', [
                'user_details' => Auth::user(),
                'enduser_id' => $id,
                'message' => 'Deleting enduser.'
            ]);

            $enduser->delete();
          
            return redirect()->route('endusers.index')->with('success','Deleted Successfuly');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('EndUserController | Destroy() Error ' . $unique_id ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
    }

    }

    /**
     * Show bulk import form
     */
    public function showImportForm()
    {
        try {
            return view('endusers.import');
        } catch (\Exception $e) {
            Log::error('EnduserController | showImportForm | Error: ' . $e->getMessage());
            Toastr::error('An error occurred while loading the import form.');
            return redirect()->route('endusers.index');
        }
    }

    /**
     * Handle bulk import of endusers from CSV/XLSX
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:10240', // 10MB max
        ]);

        try {
            $import = new EndusersImport();
            
            Excel::import($import, $request->file('file'));

            $stats = $import->getStats();
            $failures = $import->failures();

            if ($stats['success'] > 0) {
                Toastr::success("Successfully imported {$stats['success']} enduser(s).");
            }

            if ($stats['failed'] > 0 || count($failures) > 0) {
                $errorMessage = "Failed to import {$stats['failed']} enduser(s).";
                if (count($failures) > 0) {
                    $errorMessage .= " Please check the file format and try again.";
                }
                Toastr::warning($errorMessage);
            }

            Log::info('EnduserController | import | Bulk import completed', [
                'user_id' => Auth::id(),
                'success_count' => $stats['success'],
                'failed_count' => $stats['failed'],
            ]);

            return redirect()->route('endusers.index')
                ->with('import_stats', $stats)
                ->with('import_failures', $failures);

        } catch (\Exception $e) {
            Log::error('EnduserController | import | Error: ' . $e->getMessage(), [
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'user_id' => Auth::id(),
            ]);
            Toastr::error('An error occurred while importing endusers. Please check the file format and try again.');
            return redirect()->back();
        }
    }

    /**
     * Download sample import template
     */
    public function downloadImportTemplate()
    {
        return Excel::download(new EndusersImportTemplateExport(), 'endusers_import_template.xlsx');
    }
}
