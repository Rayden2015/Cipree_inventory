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
            
            // Fix N+1 query by eager loading department relationship
            $endusers = Enduser::with(['department', 'section'])
                ->where('site_id','=',$site_id)
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
        $site_id = Auth::user()->site->id;
        $query = $request->input('query');
        $endusers = Enduser::join('departments', 'endusers.department_id', '=', 'departments.id')
            ->select('endusers.*') // Select all fields from the endusers table
            ->where('endusers.asset_staff_id', 'like', '%' . $query . '%')
        
            ->orWhere('endusers.model', 'like', '%' . $query . '%')
            ->orWhere('departments.name', 'like', '%' . $query . '%')
            ->orWhere('endusers.serial_number', 'like', '%' . $query . '%')
            ->orWhere('endusers.name_description', 'like', '%' . $query . '%')
            ->where('endusers.site_id','=',$site_id)
            // ->where('departments.site_id','=',$site_id)
            ->paginate(15);
            $endusercategories = Enduser::where('site_id','=',$site_id)->groupBy('type')->pluck('type');
        return view('endusers.index', compact('endusers','endusercategories'));
        
    }

    public function show($id){
        $enduser = Enduser::find($id);
        return view('endusers.show', compact('enduser'));
    }
    
    public function endusersort(Request $request)
    {
        $site_id = Auth::user()->site->id;
        $enduserCategoryId = $request->input('enduser_category_id');

    if ($enduserCategoryId === 'all') {
        $endusers = Enduser::where('site_id','=',$site_id)->latest()->paginate(15);
    } else {
        $endusers = Enduser::join('departments', 'endusers.department_id', '=', 'departments.id')
        ->where('endusers.site_id','=',$site_id)
        ->where('departments.site_id','=',$site_id)
            ->select('endusers.*')
            ->when($enduserCategoryId, function ($query) use ($enduserCategoryId) {
                return $query->where('endusers.type', $enduserCategoryId);
            })
            ->paginate(15);
    }

    $endusercategories = Enduser::where('site_id')->groupBy('type')->pluck('type');

    return view('endusers.index', compact('endusers','endusercategories'));
        
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
            $site_id = Auth::user()->site->id;
            Enduser::create([
                'asset_staff_id' => $request->asset_staff_id,
                'name_description' => $request->name_description,
                'department' => $request->department,
                'section' => $request->section,
                'model' => $request->model,
                'serial_number' => $request->serial_number,
                'type' => $request->type,
                // 'bic' => $request->bic,
                'manufacturer' => $request->manufacturer,
                'designation' => $request->designation,
                'section_id' => $request->section_id,
                'department_id' => $request->department_id,
                'site_id' => $site_id,
                // 'enduser_category_id' => $request->enduser_category_id,
            ]);
            $authId = Auth::user()->name;
            Log::info(
                'EnduserController| store() |  Added an Enduser',
                [
                    'user_details' => Auth::user(),
                    'response_payload' => $request->all(),
                ]
            );
          
            return redirect()->route('endusers.index')->withSuccess('Successfully updated');
        }catch(\Exception $e){
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('EndUserController | Store() Error ' . $unique_id ,[
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
            $enduser = Enduser::find($id);

            $request->validate([
                'asset_staff_id' => 'unique:endusers,asset_staff_id,' . $id, // Unique validation except for the current user
                // Add other validation rules as needed
            ]);
            
            // Check if Enduser exists
            if (!$enduser) {
                return redirect()->back()->withError(['Enduser not found']);
            }
            
            // Update the Enduser fields
            $enduser->asset_staff_id = $request->input('asset_staff_id');
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
            $enduser->enduser_category_id = $request->input('enduser_category_id');
            
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
}
