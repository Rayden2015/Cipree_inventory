<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    // Display a listing of the employees
    public function index()
    {
        $employees = Employee::latest()->paginate(15);
        return view('employees.index', compact('employees'));
    }

    // Show the form for creating a new employee
    public function create()
    {
        return view('employees.create');
    }

    // Store a newly created employee in the database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:employees',
            // Add validation for other fields
        ]);
        $site_id = Auth::user()->site->id;
        $added_by = Auth::id();
        $employee = new Employee();
        $employee->name = $request->name;
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
        $employee->department = $request->department;
        $employee->employment_type = $request->employment_type;
        $employee->employee_status = $request->employee_status;
        $employee->date_of_joining = $request->date_of_joining;
        $employee->reporting_manager = $request->reporting_manager;
        $employee->employee_grade_level = $request->employee_grade_level;
        $employee->work_location = $request->work_location;

        // Employment Contracts
        $employee->contract_type = $request->contract_type;
        $employee->contract_start_date = $request->contract_start_date;
        $employee->contract_end_date = $request->contract_end_date;
        $employee->probation_period = $request->probation_period;
        $employee->site_id = $site_id;
        $employee->user_id = $added_by;

        $employee->save();
        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    // Show the form for editing the specified employee
    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    // Update the specified employee in the database
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            // Add validation for other fields
        ]);

        $employee->update($request->all());
        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    // Remove the specified employee from the database
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }

    public function show(Employee $employee){
        return view('employees.show', compact('employee'));
    }
}
