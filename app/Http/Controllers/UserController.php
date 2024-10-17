<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
<<<<<<< HEAD
use App\Models\Login;
use App\Models\Company;
use App\Models\Section;
use App\Mail\WelcomeMail;
use App\Models\Department;
use Illuminate\Support\Str;
=======
>>>>>>> d29d2b411f82256fddca149984e6cef765ac5ec9
use Illuminate\Http\Request;
use App\Helpers\UploadHelper;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
<<<<<<< HEAD
        $roles = Role::orderBy('id')->get();
        $sites = Site::all();
        $departments = Department::all();
           $sections = Section::all();
        return view('users.create', compact('roles', 'sites','departments','sections'));
=======
        $roles = Role::all();
        return view('users.create', compact('roles'));
>>>>>>> d29d2b411f82256fddca149984e6cef765ac5ec9
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
<<<<<<< HEAD
        try {
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
            $user->department_id = $request->department_id;
               $user->section_id = $request->section_id;
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
    
            return redirect()->route('users.index')->withSuccess('Successfully Updated');
        }
         catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('UserController | Store() Error ' . $unique_id, [
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
        try {
            $user = User::find($id);

            if (!$user) {
            
                return redirect()->route('users.index')->withError('User not found');
            }

            Log::info('UserControlller | show()', [
                'user_details' => Auth::user(),
                'response_payload' => $user
            ]);

            return view('users.show', compact('user'));
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('UserController | Show() Error ' . $unique_id, [
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
        try {
            $user = User::find($id);
    
            if (!$user) {
              
                return redirect()->route('users.index')->withError('User not found');
            }
    
            $roles = Role::all();
            $sites = Site::all();
            $departments = Department::all();
            $sections = Section::all();
            $userRoles = $user->roles->pluck('name')->toArray(); // Fetch the names of the user's roles
    
            Log::info('UserController | edit() ', [
                'user_details' => Auth::user(),
                'message' => 'Edit User Page Displayed Successfully'
            ]);
    
            return view('users.edit', compact('user', 'roles', 'sites', 'userRoles','departments','sections'));
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('UserController | Edit() Error ' . $unique_id, [
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
        $user->department_id = $request->department_id;
        $user->section_id = $request->section_id;
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
        Log::info('UserController | update', [
            'user_details' => Auth::user(),
            'user_name' => $authId,
            'user_name_before' => User::find($id),
=======
        $request->validate([
            'email'=>'email|unique:users,email,except,id',
            'photo' => 'sometimes|nullable|image|mimes:jpeg,gif,png,jpg|max:9048',
>>>>>>> d29d2b411f82256fddca149984e6cef765ac5ec9
        ]);
        $user = new User();
        $user->name = $request->name;
        $user->notes = $request->notes;
        $user->address = $request->address;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->phone = $request->phone;
        $user->mobile = $request->mobile;
        $user->skype = $request->skype;
        $user->facebook_url = $request->facebook_url;
        $user->instagram_url = $request->instagram_url;
        $user->snapchat_url = $request->snapchat_url;
        $user->twitter_url = $request->twitter_url;
        $user->linkedin_url = $request->linkedin_url;
        $user->role_id = $request->role_id;
        $user->save();

        if ($request->picture) {
            $imageName = UploadHelper::upload($request->picture, 'user-' . $user->id, 'images/users');
            $user->picture = $imageName;
            $user->save();
        }
        return redirect()->route('users.index');

    }

    /**
     * Display the specified resource
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
        $user = User::find($id);
        $roles = Role::all();
        return view('users.edit',compact('user','roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $id,

            'picture' => 'sometimes|nullable|image|mimes:jpeg,gif,png,jpg|max:9048',
        ]);
        $user->name = $request->name;
        $user->address = $request->address;
        $user->notes = $request->notes;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->phone = $request->phone;
        $user->mobile = $request->mobile;
        $user->skype = $request->skype;
        $user->facebook_url = $request->facebook_url;
        $user->instagram_url = $request->instagram_url;
        $user->snapchat_url = $request->snapchat_url;
        $user->twitter_url = $request->twitter_url;
        $user->linkedin_url = $request->linkedin_url;
        $user->role_id = $request->role_id;
        if ($request->picture) {
            // Delete the previous image stored & Upload this image
            $imageName = UploadHelper::update($request->picture, 'user-' . $user->id, 'images/users', $user->picture);

            $user->picture = $imageName;
            $user->save();
        }


        $user->update();
        return redirect()->route('users.index');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user =  User::find($id);
        // unlink("images/users/" . $user->image);

        User::where("id", $user->id)->delete();

        return redirect()->route('users.index');
    }
}
