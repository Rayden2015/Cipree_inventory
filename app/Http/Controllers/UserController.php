<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Site;
use App\Models\User;
use App\Models\Login;
use App\Models\Company;
use App\Models\Section;
use App\Mail\WelcomeMail;
use App\Models\Department;
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
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\SMSController;
use App\Traits\LogsErrors;

class UserController extends Controller
{
    use LogsErrors;
    
    public function __construct() {
        $this->middleware('auth');
        $this->middleware(['auth', 'permission:view-user'])->only('show');
        $this->middleware(['auth', 'permission:add-user'])->only(['create', 'store']); // FIXED: Added store method
        $this->middleware(['auth', 'permission:view-user'])->only('index');
        $this->middleware(['auth', 'permission:edit-user'])->only(['edit', 'update']);
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
    // Get all roles without caching
    $roles = Role::all();

    // Cache the users data for 5 minutes
    $users = Cache::remember('users_all', 60 * 5, function () {
        return User::all();
    });

    return view('users.index', compact('users', 'roles'));
}

    public function create()
    {
        $roles = Role::orderBy('id')->get();
        $sites = Site::all();
        $departments = Department::all();
           $sections = Section::all();
        return view('users.create', compact('roles', 'sites','departments','sections'));
    }

    public function store(Request $request)
    {
        try {
            // Log user creation attempt
            Log::info('UserController | store | Attempt started', [
                'created_by' => Auth::user()->name,
                'request_data' => $request->except(['password', 'image'])
            ]);

            // Validate request with comprehensive rules
            try {
                $request->validate([
                    'email' => 'required|email|max:255|unique:users,email',
                    'name' => 'required|string|max:255',
                    'dob' => 'nullable|date',
                    'role_id' => 'nullable|integer',
                    'site_id' => 'required|integer|exists:sites,id', // Site is required
                    'staff_id' => 'nullable|string|unique:users,staff_id',
                    'phone' => 'nullable|string|unique:users,phone',
                    'address' => 'nullable|string|max:500',
                    'image' => 'sometimes|nullable|image|mimes:jpeg,gif,png,jpg|max:10240', // 10MB
                    'department_id' => 'nullable|integer|exists:departments,id',
                    'section_id' => 'nullable|integer|exists:sections,id',
                    'status' => 'nullable|in:Active,Inactive',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::warning('UserController | store | Validation failed', [
                    'created_by' => Auth::user()->name,
                    'validation_errors' => $e->errors()
                ]);
                
                return redirect()->back()
                    ->withInput()
                    ->withErrors($e->errors());
            }

            // Validate roles if provided
            if ($request->roles) {
                $validRoles = \App\Models\Role::whereIn('name', $request->roles)->pluck('name')->toArray();
                if (count($validRoles) !== count($request->roles)) {
                    $invalidRoles = array_diff($request->roles, $validRoles);
                    Log::warning('UserController | store | Invalid roles provided', [
                        'created_by' => Auth::user()->name,
                        'invalid_roles' => $invalidRoles
                    ]);
                    return redirect()->back()
                        ->withInput()
                        ->withError('Some selected roles are invalid. Please contact the administrator.');
                }
            }
    
            // Generate a random alphanumeric password of length 6
            $password = strtoupper(Str::random(3)) . rand(100, 999);
    
            // Create a new user instance
            $user = new User();
           
            $user->name = $request->name;
            $user->email = $request->email;
            $user->dob = $request->dob;
            $user->password = Hash::make($password);
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->status = $request->status ?? 'Active'; // Default to Active if not provided
            $user->staff_id = $request->staff_id;
            $user->site_id = $request->site_id;
            $user->role_id = $request->role_id;
            $user->department_id = $request->department_id;
            $user->section_id = $request->section_id;
            
            // Save user
            try {
                $user->save();
                Log::info('UserController | store | User created in database', [
                    'user_id' => $user->id,
                    'created_by' => Auth::user()->name
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                Log::error('UserController | store | Database error during user creation', [
                    'created_by' => Auth::user()->name,
                    'error_code' => $e->getCode(),
                    'error_message' => $e->getMessage()
                ]);
                
                if ($e->getCode() == '23000') {
                    return redirect()->back()
                        ->withInput()
                        ->withError('A database constraint was violated. Email, phone, or staff ID may already be in use.');
                }
                
                throw $e;
            }
    
            // Assign roles to the user
            if ($request->roles) {
                try {
                    foreach ($request->roles as $role) {
                        if (!$user->hasRole($role)) {
                            $user->assignRole($role);
                        }
                    }
                    Log::info('UserController | store | Roles assigned', [
                        'user_id' => $user->id,
                        'roles' => $request->roles
                    ]);
                } catch (\Exception $e) {
                    Log::error('UserController | store | Role assignment failed', [
                        'user_id' => $user->id,
                        'error_message' => $e->getMessage()
                    ]);
                    // Continue - user created but roles failed
                }
            }
    
            // Handle the image upload if an image is provided
            if ($request->hasFile('image')) {
                try {
                    $uploadDir = 'images/users';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    $imageName = UploadHelper::upload($request->image, 'user-' . $user->id, $uploadDir);
                    $user->image = $imageName;
                    $user->save();
                    
                    Log::info('UserController | store | Image uploaded', [
                        'user_id' => $user->id,
                        'image_name' => $imageName
                    ]);
                } catch (\Exception $e) {
                    Log::error('UserController | store | Image upload failed', [
                        'user_id' => $user->id,
                        'error_message' => $e->getMessage()
                    ]);
                    // Continue - user created but image upload failed
                }
            }
            
            // Send email to the user with the generated password
            try {
                Mail::to($user->email)->send(new WelcomeMail([
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $password,
                ]));
                
                Log::info('UserController | store | Welcome email sent', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            } catch (\Exception $e) {
                Log::warning('UserController | store | Email sending failed', [
                    'user_id' => $user->id,
                    'error_message' => $e->getMessage()
                ]);
                // Continue - user created but email failed (non-critical)
            }
    
            // Send SMS to the user
            try {
                if ($user->phone) {
                    $message = "Welcome! Your account is ready. Please login at www.test.cipree.com\nEmail: " . $user->email . "\nPassword: " . $password . "\nThanks - Cipree Team";
                    $smsController = new SMSController();
                    $smsController->sendSms($user->phone, $message);
                    
                    Log::info('UserController | store | SMS sent', [
                        'user_id' => $user->id,
                        'phone' => $user->phone
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('UserController | store | SMS sending failed', [
                    'user_id' => $user->id,
                    'error_message' => $e->getMessage()
                ]);
                // Continue - user created but SMS failed (non-critical)
            }
    
            Log::info('UserController | store | User created successfully', [
                'user_id' => $user->id,
                'created_by' => Auth::user()->name,
                'user_email' => $user->email
            ]);
    
            return redirect()->route('users.index')
                ->withSuccess('User created successfully! Login credentials sent to ' . $user->email);
                
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::warning('UserController | store | Unauthorized user creation attempt', [
                'attempted_by' => Auth::user()->id ?? 'guest',
                'error_message' => $e->getMessage()
            ]);
            return redirect()->back()
                ->withError('You do not have permission to create users.');
                
        } catch (\Exception $e) {
            return $this->handleError($e, 'store()', [
                'created_by' => Auth::user()->name ?? 'unknown',
                'attempted_user_email' => $request->input('email')
            ]);
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
            return $this->handleError($e, 'show()', ['user_id' => $id]);
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
            return $this->handleError($e, 'edit()', ['user_id' => $id]);
        }

    }
    

    public function update(Request $request, $id)
    {
        try {
            $authId = Auth::user()->name;
            
            // Log the update attempt
            Log::info('UserController | update | Attempt started', [
                'user_id' => $id,
                'updated_by' => $authId,
                'request_data' => $request->except(['password', 'image'])
            ]);

            // Check if user exists
            $user = User::find($id);
            if (!$user) {
                Log::warning('UserController | update | User not found', [
                    'user_id' => $id,
                    'updated_by' => $authId
                ]);
                return redirect()->back()->withError('User not found. The user may have been deleted.');
            }

            // Store original user data for logging
            $originalUserData = $user->toArray();

            // Validate request with proper uniqueness rules
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:users,email,' . $id,
                    'phone' => 'nullable|string|unique:users,phone,' . $id,
                    'staff_id' => 'nullable|string|unique:users,staff_id,' . $id,
                'dob' => 'nullable|date',
                'address' => 'nullable|string',
                    'password' => 'nullable|string|min:8',
                    'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10240', // 10MB max
                    'status' => 'nullable|string',
                    'role_id' => 'nullable|integer',
                    'site_id' => 'nullable|integer|exists:sites,id',
                    'department_id' => 'nullable|integer|exists:departments,id',
                    'section_id' => 'nullable|integer|exists:sections,id',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::warning('UserController | update | Validation failed', [
                    'user_id' => $id,
                    'updated_by' => $authId,
                    'validation_errors' => $e->errors()
                ]);
                
                // Create user-friendly error messages
                $errors = $e->errors();
                $friendlyMessages = [];
                
                if (isset($errors['email'])) {
                    $friendlyMessages[] = 'The email address is already in use by another user.';
                }
                if (isset($errors['phone'])) {
                    $friendlyMessages[] = 'The phone number is already in use by another user.';
                }
                if (isset($errors['staff_id'])) {
                    $friendlyMessages[] = 'The staff ID is already in use by another user.';
                }
                if (isset($errors['site_id'])) {
                    $friendlyMessages[] = 'The selected site does not exist.';
                }
                if (isset($errors['department_id'])) {
                    $friendlyMessages[] = 'The selected department does not exist.';
                }
                if (isset($errors['section_id'])) {
                    $friendlyMessages[] = 'The selected section does not exist.';
                }
                if (isset($errors['image'])) {
                    $friendlyMessages[] = 'The uploaded image must be a valid image file (JPEG, PNG, GIF) and less than 10MB.';
                }
                if (isset($errors['dob'])) {
                    $friendlyMessages[] = 'The date of birth must be a valid date.';
                }
                if (isset($errors['password'])) {
                    $friendlyMessages[] = 'The password must be at least 8 characters long.';
                }
                
                // If no specific friendly message, use generic validation error
                if (empty($friendlyMessages)) {
                    $friendlyMessages[] = 'Please check your input and try again.';
                }
                
                return redirect()->back()
                    ->withInput()
                    ->withError(implode(' ', $friendlyMessages));
            }

            // Validate roles if provided
            if ($request->roles) {
                $validRoles = Role::whereIn('name', $request->roles)->pluck('name')->toArray();
                if (count($validRoles) !== count($request->roles)) {
                    $invalidRoles = array_diff($request->roles, $validRoles);
                    Log::warning('UserController | update | Invalid roles provided', [
                        'user_id' => $id,
                        'updated_by' => $authId,
                        'invalid_roles' => $invalidRoles
                    ]);
                    return redirect()->back()
                        ->withInput()
                        ->withError('Some selected roles are invalid. Please contact the administrator.');
                }
            }

            // Update user basic information
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
            
            // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
                Log::info('UserController | update | Password updated', [
                    'user_id' => $id,
                    'updated_by' => $authId
                ]);
        }

            // Save user data
            try {
        $user->save();
                Log::info('UserController | update | User data saved', [
                    'user_id' => $id,
                    'updated_by' => $authId
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                Log::error('UserController | update | Database error during save', [
                    'user_id' => $id,
                    'updated_by' => $authId,
                    'error_code' => $e->getCode(),
                    'error_message' => $e->getMessage()
                ]);
                
                // Check for specific database errors
                if ($e->getCode() == '23000') {
                    return redirect()->back()
                        ->withInput()
                        ->withError('A database constraint was violated. This email, phone, or staff ID may already be in use.');
                }
                
                throw $e; // Re-throw to be caught by outer catch
            }

            // Sync roles - this will update the user's roles
            try {
    if ($request->roles) {
                    $user->syncRoles($request->roles);
                    Log::info('UserController | update | Roles synced', [
                        'user_id' => $id,
                        'updated_by' => $authId,
                        'roles' => $request->roles
                    ]);
    } else {
                    $user->syncRoles([]);
                    Log::info('UserController | update | All roles removed', [
                        'user_id' => $id,
                        'updated_by' => $authId
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('UserController | update | Role sync failed', [
                    'user_id' => $id,
                    'updated_by' => $authId,
                    'error_message' => $e->getMessage(),
                    'stack_trace' => $e->getTraceAsString()
                ]);
                return redirect()->back()
                    ->withError('User updated but role assignment failed. Please try updating the roles again.');
    }
    
        // Handle the image upload if an image is provided
            if ($request->hasFile('image')) {
                try {
                    // Check if directory exists and is writable
                    $uploadDir = 'images/users';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                        Log::info('UserController | update | Created upload directory', [
                            'directory' => $uploadDir
                        ]);
                    }
                    
                    if (!is_writable($uploadDir)) {
                        Log::error('UserController | update | Upload directory not writable', [
                            'directory' => $uploadDir,
                            'user_id' => $id,
                            'updated_by' => $authId
                        ]);
                        return redirect()->back()
                            ->withError('User updated but image upload failed due to server permissions. Please contact the administrator.');
                    }

                    $imageName = UploadHelper::upload($request->image, 'user-' . $user->id, $uploadDir);
            $user->image = $imageName;
            $user->save();
                    
                    Log::info('UserController | update | Image uploaded', [
                        'user_id' => $id,
                        'updated_by' => $authId,
                        'image_name' => $imageName
                    ]);
                } catch (\Exception $e) {
                    Log::error('UserController | update | Image upload failed', [
                        'user_id' => $id,
                        'updated_by' => $authId,
                        'error_message' => $e->getMessage(),
                        'stack_trace' => $e->getTraceAsString()
                    ]);
                    return redirect()->back()
                        ->withError('User updated but image upload failed. Please try uploading the image again.');
                }
            }

            // Log successful update with before/after data
            Log::info('UserController | update | Update completed successfully', [
                'user_id' => $id,
                'updated_by' => $authId,
                'original_data' => $originalUserData,
                'updated_data' => $user->fresh()->toArray()
            ]);

            return redirect()->back()->withSuccess('User updated successfully!');

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::warning('UserController | update | Unauthorized access attempt', [
                'user_id' => $id,
                'attempted_by' => Auth::user()->id ?? 'guest',
                'error_message' => $e->getMessage()
            ]);
            return redirect()->back()
                ->withError('You do not have permission to update users.');

        } catch (\Throwable $e) {
            return $this->handleError($e, 'update()', [
                'user_id' => $id,
                'updated_by' => Auth::user()->name ?? 'unknown'
            ]);
        }
    }
    public function destroy($id)
    {
        try {
            $user = User::find($id);
            $authId = Auth::user()->name;
    
            if (!$user) {
               
                Log::warning('UserController | destroy | User not found', [
                    'user_id' => $id,
                    'user_name' => $authId,
                ]);
                return redirect()->route('users.index')->withError('User not found');
            }
    
            Log::info('UserController | destroy', [
                'user_details' => Auth::user(),
                'user_name' => $authId,
                'user_deleted' => $user,
            ]);
    
            $user->delete();
            return redirect()->route('users.index')->withSuccess('Successfully Updated');
        } catch (\Exception $e) {
            return $this->handleError($e, 'destroy()', ['user_id' => $id]);
        }

    }
    



    public static function username()
    {
        try {
            $name = Auth::user()->name;
            $first_name = strtok($name, " ");
            // Log::info('UserController | username', [
            //     'user_details' => Auth::user(),
            //     'user_name' => $name,
            // ]);
            return $first_name;
        } catch (\Throwable $e) {
            $errorId = $this->logError($e, 'username()');
            return 'User';  // Fallback name
        }

    }

    public static function logo()
    {
        try {
            $logo = Company::first()->value('image');
            // Log::info('UserController | logo', [
            //     'user_details' => Auth::user(),
            //     'company_logo' => $logo,
            // ]);
            return $logo;
        } catch (\Throwable $e) {
            $errorId = $this->logError($e, 'logo()');
            return null;  // Fallback logo
        }

    }

    public static function lastlogin()
    {
        try {
            if (!Auth::check()) {
                return null;
            }
            
        $authid = Auth::id();
            
            // Get the previous login (skip current session, get the one before)
            // The logins table tracks each login, skip(1) gets the PREVIOUS login
            $lastlogin = Login::where('user_id', '=', $authid)
                ->where('attempt', '=', 1) // Only successful logins (attempt = 1)
                ->latest()
                ->skip(1) // Skip current login, get previous one
                ->first();
            
            // If no previous login found in logins table, create a response with last_login_at
            if (!$lastlogin) {
                $user = Auth::user();
                
                // Check if user has last_login_at timestamp
                if ($user->last_login_at) {
                    // Create a mock Login object for display compatibility
                    $lastlogin = new \stdClass();
                    $lastlogin->created_at = $user->last_login_at;
                    return $lastlogin;
                }
                
                // First time login - no previous login
                return null;
            }
            
        return $lastlogin;
        } catch (\Exception $e) {
            Log::error('UserController | lastlogin() | Error retrieving last login', [
                'user_id' => Auth::id() ?? 'unknown',
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}