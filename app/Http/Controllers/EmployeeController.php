<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Rinvex\Country\CountryLoader;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    // Display a listing of the employees
    public function index()
    {
        $employees = Employee::latest()->paginate(15);
        return view('employees.index', compact('employees'));
    }

    // Show the form for creating a new employee
    public function create()
    {
        $countries = CountryLoader::countries();
        $departments = Department::all();
        return view('employees.create', compact('countries', 'departments'));
    }

    // Store a newly created employee in the database
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:employees',
            'department_id' => 'required|exists:departments,id',
            // Add validation for other fields
        ]);
        
        $site_id = Auth::user()->site->id;
        $added_by = Auth::id();
    
        $employee = new Employee();
        $employee->fname = $request->fname;
        $employee->oname = $request->oname;
        $employee->lname = $request->lname;
        $employee->date_of_birth = $request->date_of_birth;
        $employee->national_id_passport_number = $request->national_id_passport_number;
        $employee->gender = $request->gender;
        $employee->phone = $request->phone;
        $employee->email = $request->email;
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
        $contractStartDate = Carbon::parse($request->contract_start_date); // Ensure this is a Carbon instance
        $duration = $request->duration; // Number of months to add
    
        // Calculate contract end date
        $contractEndDate = $contractStartDate->copy()->addMonths($duration); // Use copy() to avoid mutating $contractStartDate
    
        // Assign the contract details
        $employee->contract_type = $request->contract_type;
        $employee->duration = $duration; // Store the duration in the employee record
        $employee->contract_start_date = $contractStartDate; // Store as Carbon instance
        $employee->contract_end_date = $contractEndDate; // Store as Carbon instance
    
        $employee->probation_period = $request->probation_period;
    
        // Additional data
        $employee->site_id = $site_id;
        $employee->user_id = $added_by;
    
        $employee->save(); // Save the employee record
    
        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
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
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'department_id' => 'required|exists:departments,id',
            // Add validation for other fields
        ]);
    
        $auth = Auth::id();
        $site_id = Auth::user()->site->id;
    
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
            'site_id' => $site_id,
        ]);
    
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
