<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Site;
use App\Models\User;
use App\Models\Login;
use App\Models\Company;
use App\Mail\WelcomeMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\UploadHelper;
use App\Models\SiteAdminPrivilege;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\SMSController;

class UserController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware(['auth', 'permission:view-user'])->only('show');
        $this->middleware(['auth', 'permission:add-user'])->only('create');
        $this->middleware(['auth', 'permission:view-user'])->only('index');
        $this->middleware(['auth', 'permission:edit-user'])->only('edit');
    }
    
    public function searchUsers(Request $request)
    {
        $query = $request->get('query');
        
        // Fetch users and include role names
        $users = User::where('name', 'like', '%' . $query . '%')
            ->orWhere('email', 'like', '%' . $query . '%')
            ->get()
            ->map(function ($user) {
                $user->role_names = $user->getRoleNames(); // Add role names to the user object
                return $user;
            });
    
        return response()->json($users);
    }
    
   
    public function index()
    {
        $roles = Role::all();
        $users = User::all();
        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::orderBy('id')->get();
        $sites = Site::all();
        return view('users.create', compact('roles', 'sites'));
    }

    public function store(Request $request)
    {
        // try {
            $request->validate([
                'email' => 'required|email|unique:users,email',
                'name' => 'required',
                'dob' => 'nullable',
                'role_id' => 'nullable',
                'site_id' => 'nullable',
                'staff_id' => 'nullable|string|unique:users',
                'phone' => 'string|nullable|unique:users',
                'address' => 'nullable',
                'photo' => 'sometimes|nullable|image|mimes:jpeg,gif,png,jpg|max:9048',
            ]);
    
            // Generate a random alphanumeric password of length 6
            $password = strtoupper(Str::random(3)) . rand(100, 999);
    
            // Create a new user instance
            $user = new User();
           
            $user->name = $request->name;
            $user->email = $request->email;
            $user->dob = $request->dob;
            $user->password = Hash::make($password); // Hash the random password
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->status = $request->status;
            $user->staff_id = $request->staff_id;
            $user->site_id = $request->site_id;
            $user->role_id = $request->role_id;
            $user->save();
    
             // Assign roles to the user
        if ($request->roles) {
            foreach ($request->roles as $role) {
                if (!$user->hasRole($role)) {
                    $user->assignRole($role);
                }
            }
        }
    
        // Handle the image upload if an image is provided
        if ($request->image) {
            $imageName = UploadHelper::upload($request->image, 'user-' . $user->id, 'images/users');
            $user->image = $imageName;
            $user->save();
        }
            // Send email to the user with the generated password
            Mail::to($user->email)->send(new WelcomeMail([
                'name' => $user->name,
                'email' => $user->email,
                'password' => $password, // Pass the generated password to the email template
            ]));
    
            // Send SMS to the user
            $message = "Welcome! Your account is ready. Please login at www.test.cipree.com\nEmail: " . $user->email . "\nPassword: " . $password . "\nThanks - Cipree Team";
            $smsController = new SMSController();
            $smsController->sendSms($user->phone, $message);
    
            Log::info('UserController | store', [
                'user_details' => auth()->user(),
                'message' => 'User created successfully.',
                'request_payload' => $request->all(),
            ]);
    
            Toastr::success('Successfully Updated:)', 'Sucess');
            return redirect()->route('users.index');
        }
    //      catch (\Exception $e) {
    //         $unique_id = floor(time() - 999999999);
    //         Log::error('An error occurred with id ' . $unique_id);
    //         Toastr::error('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the Feedback Button', 'Error');
    //         Log::error('UserController | Store() | ', [
    //             'user_details' => Auth::user(),
    //             'error_message' => $e->getMessage()
    //         ]);
    //         return redirect()->back();
       
    // }


    // The rest of the methods remain unchanged.



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     **/


    public function show($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                Toastr::error('User not found.', 'Error');
                return redirect()->route('users.index');
            }

            Log::info('UserControlller | show()', [
                'user_details' => Auth::user(),
                'response_payload' => $user
            ]);

            return view('users.show', compact('user'));
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::error('An error occurred with id ' . $unique_id);
            Toastr::error('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the Feedback Button', 'Error');
            Log::error('UserController | Show() | ', [
                'user_details' => Auth::user(),
                'error_message' => $e->getMessage()
            ]);
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        try {
            $user = User::find($id);
    
            if (!$user) {
                Toastr::error('User not found.', 'Error');
                return redirect()->route('users.index');
            }
    
            $roles = Role::all();
            $sites = Site::all();
            $userRoles = $user->roles->pluck('name')->toArray(); // Fetch the names of the user's roles
    
            Log::info('UserController | edit() ', [
                'user_details' => Auth::user(),
                'message' => 'Edit User Page Displayed Successfully'
            ]);
    
            return view('users.edit', compact('user', 'roles', 'sites', 'userRoles'));
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::error('An error occurred with id ' . $unique_id);
            Toastr::error('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the Feedback Button', 'Error');
            Log::error('UserController | Edit() | ', [
                'user_details' => Auth::user(),
                'error_message' => $e->getMessage()
            ]);
            return redirect()->back();
        }
    }
    

    public function update(Request $request, $id)
    {
        // try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|unique:users,phone,' . $id, // Nullable and unique validation except for the current user
                'dob' => 'nullable|date',
                'address' => 'nullable|string',
                // Add other validation rules as needed
            ]);
            
        $user = User::find($id);
        //   dd($user);

        $authId = Auth::user()->name;


        $user->name = $request->name;
        $user->email = $request->email;
        $user->dob = $request->dob;
        $user->address = $request->address;
        $user->phone = $request->phone;
        $user->status = $request->status;
        $user->role_id = $request->role_id;
        $user->staff_id = $request->staff_id;
        $user->site_id = $request->site_id;
        $user->add_admin = $request->add_admin;
        $user->add_site_admin = $request->add_site_admin;
        $user->add_requester = $request->add_requester;
        $user->add_finance_officer = $request->add_finance_officer;
        $user->add_store_officer = $request->add_store_officer;


        $user->add_purchasing_officer = $request->add_purchasing_officer;
        $user->add_authoriser = $request->add_authoriser;
        $user->add_store_assistant = $request->add_store_assistant;
        $user->add_procurement_assistant = $request->add_procurement_assistant;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }
        $user->save();
          // Assign roles to the user
        //   if ($request->roles) {
        //     foreach ($request->roles as $role) {
        //         if (!$user->hasRole($role)) {
        //             $user->assignRole($role);
        //         }
        //     }
        // }

         // Sync roles - this will update the user's roles, adding new roles and removing unchecked ones
    if ($request->roles) {
        $user->syncRoles($request->roles);  // Use syncRoles to replace current roles with the ones submitted
    } else {
        $user->syncRoles([]); // If no roles are checked, remove all roles
    }
    
        // Handle the image upload if an image is provided
        if ($request->image) {
            $imageName = UploadHelper::upload($request->image, 'user-' . $user->id, 'images/users');
            $user->image = $imageName;
            $user->save();
        }



        Toastr::success('Successfully Updated:)', 'Sucess');
        return redirect()->back();



        //     Log::info('UserController | update', [
        //         'user_details' => Auth::user(),
        //         'user_name' => $authId,
        //         'user_name_before' => User::find($id),
        //     ]);

        //     // ... (rest of the update method remains unchanged)
    //     } catch (\Throwable $e) {
    //         $unique_id = floor(time() - 999999999);
    //         Log::error('An error occurred with id ' . $unique_id);
    //         Toastr::error('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the Feedback Button', 'Error');
    //         Log::error('UserController | Update() | ', [
    //             'user_details' => Auth::user(),
    //             'error_message' => $e->getMessage()
    //         ]);
    //         return redirect()->back();

    // }
    }
    public function destroy($id)
    {
        try {
            $user = User::find($id);
            $authId = Auth::user()->name;
    
            if (!$user) {
                Toastr::error('User not found.', 'Error');
                Log::warning('UserController | destroy | User not found', [
                    'user_id' => $id,
                    'user_name' => $authId,
                ]);
                return redirect()->route('users.index');
            }
    
            Log::info('UserController | destroy', [
                'user_details' => Auth::user(),
                'user_name' => $authId,
                'user_deleted' => $user,
            ]);
    
            $user->delete();
    
            Toastr::success('User deleted successfully.', 'Success');
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::error('An error occurred with id ' . $unique_id);
            Toastr::error('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the Feedback Button', 'Error');
            Log::error('UserController | Destroy() | ', [
                'user_details' => Auth::user(),
                'error_message' => $e->getMessage()
            ]);
            return redirect()->back();
        }
    }
    



    public static function username()
    {
        try {
            $name = Auth::user()->name;
            $first_name = strtok($name, " ");
            Log::info('UserController | username', [
                'user_details' => Auth::user(),
                'user_name' => $name,
            ]);
            return $first_name;
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::error('An error occurred with id ' . $unique_id);
            Toastr::error('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the Feedback Button', 'Error');
            Log::error('UserController | Username() | ', [
                'user_details' => Auth::user(),
                'error_message' => $e->getMessage()
            ]);
      
            return null; // or handle the error accordingly
        }
    }

    public static function logo()
    {
        try {
            $logo = Company::first()->value('image');
            Log::info('UserController | logo', [
                'user_details' => Auth::user(),
                'company_logo' => $logo,
            ]);
            return $logo;
        } catch (\Throwable $th) {
            $unique_id = floor(time() - 999999999);
            Log::error('An error occurred with id ' . $unique_id);
            Toastr::error('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the Feedback Button', 'Error');
            Log::error('UserController | Logo() | ', [
                'user_details' => Auth::user(),
                'error_message' => $th->getMessage()
            ]);
            // return redirect()->back();
            return null; // or handle the error accordingly
        }
    }

    public static function lastlogin()
    {
        $authid = Auth::id();
        $lastlogin = Login::where('user_id', '=', $authid)->latest()->skip(2)->first();
        return $lastlogin;
    }
}
