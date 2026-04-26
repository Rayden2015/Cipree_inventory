<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Enduser;
use App\Models\User;
use App\Models\EndUsersCategory;
use Illuminate\Http\Request;
use Rinvex\Country\CountryLoader;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['auth', 'permission:view-employee'])->only(['index', 'show']);
        $this->middleware(['auth', 'permission:add-employee'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:edit-employee'])->only(['edit', 'update']);
        $this->middleware(['auth', 'permission:delete-employee'])->only(['destroy']);
    }
    // Display a listing of the employees
    public function index()
    {
        $siteId = Auth::user()?->site?->id;
        $employees = Employee::query()
            ->when($siteId, fn ($q) => $q->where('site_id', $siteId))
            ->latest()
            ->paginate(15);
        return view('employees.index', compact('employees'));
    }

    // Show the form for creating a new employee
    public function create()
    {
        $countries = CountryLoader::countries();
        $departments = Department::all();
        $roles = Role::query()->orderBy('name')->get();

        return view('employees.create', compact('countries', 'departments', 'roles'));
    }

    // Store a newly created employee in the database
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email|unique:employees,email',
            'department_id' => 'required|exists:departments,id',

            // Optional login user creation
            'create_user' => 'nullable|boolean',
            'role' => 'required_if:create_user,1|nullable|exists:roles,name',
            // When creating a login, ensure the email is present and unique in users table.
            'user_email' => 'required_if:create_user,1|nullable|email|unique:users,email',
            'user_password' => 'nullable|string|min:8',
        ]);
        
        $site_id = Auth::user()?->site?->id;
        $added_by = Auth::id();
        $tenant_id = Auth::user()?->getCurrentTenant()?->id;

        $createUser = (bool) $request->boolean('create_user');

        $employee = null;

        DB::transaction(function () use ($request, $site_id, $added_by, $tenant_id, $createUser, &$employee) {
            $employee = new Employee();
            $employee->fname = $request->fname;
            $employee->oname = $request->oname;
            $employee->lname = $request->lname;
            $employee->date_of_birth = $request->date_of_birth;
            $employee->national_id_passport_number = $request->national_id_passport_number;
            $employee->gender = $request->gender;
            $employee->phone = $request->phone;
            // Employee email is optional; if we are creating a login, we store the login email on the employee too.
            $employee->email = $createUser ? $request->input('user_email') : $request->email;
            $employee->address = $request->address;
            $employee->emergency_contact_name = $request->emergency_contact_name;
            $employee->emergency_contact_phone = $request->emergency_contact_phone;
            $employee->nationality = $request->nationality;
            $employee->marital_status = $request->marital_status;

            // Job Information
            $employee->job_title = $request->job_title;
            $employee->department_id = $request->department_id;
            $employee->employment_type = $request->employment_type;
            $employee->employee_status = $request->employee_status;
            $employee->date_of_joining = $request->date_of_joining;
            $employee->reporting_manager = $request->reporting_manager;
            $employee->employee_grade_level = $request->employee_grade_level;
            $employee->work_location = $request->work_location;

            // Employment Contracts
            $contractStartDate = $request->filled('contract_start_date') ? Carbon::parse($request->contract_start_date) : null;
            $duration = $request->duration;
            $contractEndDate = ($contractStartDate && $duration) ? $contractStartDate->copy()->addMonths($duration) : null;

            $employee->contract_type = $request->contract_type;
            $employee->duration = $duration;
            $employee->contract_start_date = $contractStartDate;
            $employee->contract_end_date = $contractEndDate;

            $employee->probation_period = $request->probation_period;

            // Additional data
            $employee->site_id = $site_id;
            $employee->user_id = $added_by;
            $employee->edited_by = $added_by;
            $employee->tenant_id = $tenant_id;

            $employee->save();

            if ($createUser) {
                $fullName = trim(collect([$employee->fname, $employee->oname, $employee->lname])->filter()->join(' '));

                $user = new User();
                $user->employee_id = $employee->id;
                $user->name = $fullName ?: ('Employee #' . $employee->id);
                $user->email = $request->input('user_email');
                $user->password = Hash::make((string) $request->input('user_password', 'password'));
                $user->department_id = $employee->department_id;
                $user->site_id = $employee->site_id;
                $user->tenant_id = $employee->tenant_id;
                $user->status = 'Active';
                $user->save();

                $roleName = $request->input('role');
                if ($roleName) {
                    $user->assignRole($roleName);
                }

                $employee->login_user_id = $user->id;
                $employee->save();
            }

            // Ensure employee is also represented as an Enduser (Personnel) for dropdowns/workflows.
            $staffCategoryId = EndUsersCategory::query()
                ->firstOrCreate(['name' => 'Staff'])
                ->id;

            $departmentName = Department::whereKey($employee->department_id)->value('name') ?? 'N/A';
            Enduser::withoutTenantScope()->updateOrCreate(
                ['employee_id' => $employee->id],
                [
                    'name' => trim(collect([$employee->fname, $employee->oname, $employee->lname])->filter()->join(' ')) ?: ($employee->email ?? 'Employee #' . $employee->id),
                    'type' => 'Person',
                    'department' => $departmentName,
                    'section' => null,
                    'site_id' => $employee->site_id,
                    'tenant_id' => $employee->tenant_id,
                    'department_id' => $employee->department_id,
                    'section_id' => null,
                    'enduser_category_id' => $staffCategoryId,
                    'status' => 'Active',
                ]
            );
        });

        return redirect()->route('employees.index')->with('success', $createUser ? 'Employee and user account created successfully.' : 'Employee created successfully.');
    }
    

    // Show the form for editing the specified employee
    public function edit(Employee $employee)
    {
        $departments = Department::all();
        $countries = CountryLoader::countries();
        return view('employees.edit', compact('employee', 'departments', 'countries'));
    }

    // Update the specified employee in the database
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'email' => 'nullable|email|unique:employees,email,' . $employee->id,
            'department_id' => 'required|exists:departments,id',
            // Add validation for other fields
        ]);
    
        $auth = Auth::id();
        $site_id = Auth::user()?->site?->id;
        $tenant_id = Auth::user()?->getCurrentTenant()?->id;
    
        // Parse the contract start date
        $startDate = Carbon::parse($request->input('contract_start_date'));
        $months = $request->input('duration');
    
        // Clone the start date and add months to it for the end date
        $endDate = $startDate->clone()->addMonths($months);
    
        // Update employee record
        $employee->update([
            'fname' => $request->input('fname'),
            'lname' => $request->input('lname'),
            'oname' => $request->input('oname'),
            'date_of_birth' => $request->input('date_of_birth'),
            'national_id_passport_number' => $request->input('national_id_passport_number'),
            'gender' => $request->input('gender'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'emergency_contact_name' => $request->input('emergency_contact_name'),
            'emergency_contact_phone' => $request->input('emergency_contact_phone'),
            'nationality' => $request->input('nationality'),
            'marital_status' => $request->input('marital_status'),
            'job_title' => $request->input('job_title'),
            'department_id' => $request->input('department_id'),
            'employment_type' => $request->input('employment_type'),
            'employee_status' => $request->input('employee_status'),
            'date_of_joining' => $request->input('date_of_joining'),
            'reporting_manager' => $request->input('reporting_manager'),
            'employee_grade_level' => $request->input('employee_grade_level'),
            'work_location' => $request->input('work_location'),
            'contract_type' => $request->input('contract_type'),
            'duration' => $request->input('duration'),
            'contract_start_date' => $startDate,
            'contract_end_date' => $endDate, // Recalculated end date
            'probation_period' => $request->input('probation_period'),
            'user_id' => $auth,
            'edited_by' => $auth,
            'site_id' => $site_id,
            'tenant_id' => $tenant_id,
        ]);

        // Keep linked Enduser(Person) in sync (name/site/tenant/department).
        try {
            $staffCategoryId = EndUsersCategory::query()
                ->firstOrCreate(['name' => 'Staff'])
                ->id;

            $departmentName = Department::whereKey($employee->department_id)->value('name') ?? 'N/A';
            Enduser::withoutTenantScope()->updateOrCreate(
                ['employee_id' => $employee->id],
                [
                    'name' => trim(collect([$employee->fname, $employee->oname, $employee->lname])->filter()->join(' ')) ?: ($employee->email ?? 'Employee #' . $employee->id),
                    'type' => 'Person',
                    'department' => $departmentName,
                    'section' => null,
                    'site_id' => $employee->site_id,
                    'tenant_id' => $employee->tenant_id,
                    'department_id' => $employee->department_id,
                    'section_id' => null,
                    'enduser_category_id' => $staffCategoryId,
                    'status' => $employee->employee_status === 'Inactive' ? 'Inactive' : 'Active',
                ]
            );
        } catch (\Throwable $e) {
            // Don't fail employee updates because of enduser sync issues.
        }

        // If the employee has a linked login user, keep basic identity fields in sync.
        try {
            if ($employee->login_user_id) {
                $user = User::query()->whereKey($employee->login_user_id)->first();
                if ($user) {
                    $fullName = trim(collect([$employee->fname, $employee->oname, $employee->lname])->filter()->join(' '));
                    $user->name = $fullName ?: $user->name;
                    if ($employee->email) {
                        $user->email = $employee->email;
                    }
                    $user->department_id = $employee->department_id;
                    $user->site_id = $employee->site_id;
                    $user->tenant_id = $employee->tenant_id;
                    $user->employee_id = $employee->id;
                    $user->save();
                }
            }
        } catch (\Throwable $e) {
            // Don't fail employee updates because of user sync issues.
        }
    
        return redirect()->back()->with('success', 'Employee updated successfully.');
    }
    

    // Remove the specified employee from the database
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }

    public function show(Employee $employee)
    {
        $countries = CountryLoader::countries();
        return view('employees.show', compact('employee', 'countries'));
    }
}
