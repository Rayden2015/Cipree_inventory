@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Employee Details</h2>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Personal Information Section -->
        <h3>Personal Information</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>First Name:</label>
                    <p>{{ $employee->fname }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Last Name:</label>
                    <p>{{ $employee->lname }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Other Name:</label>
                    <p>{{ $employee->oname }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Email:</label>
                    <p>{{ $employee->email }}</p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Phone:</label>
                    <p>{{ $employee->phone }}</p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Date of Birth:</label>
                    <p>{{ $employee->date_of_birth }}</p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>National ID/Passport Number:</label>
                    <p>{{ $employee->national_id_passport_number }}</p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Gender:</label>
                    <p>{{ $employee->gender }}</p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Nationality:</label>
                    <p>
                        @if (!empty($employee->nationality) && isset($countries[strtolower($employee->nationality)]))
                            {{ $countries[strtolower($employee->nationality)]['name'] }}
                        @else
                            Unknown
                        @endif
                    </p>
                    
                    
                    
                    
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Marital Status:</label>
                    <p>{{ $employee->marital_status }}</p>
                </div>
            </div>
        </div>

        <!-- Job Information Section -->
        <h3>Job Information</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Job Title:</label>
                    <p>{{ $employee->job_title }}</p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Department:</label>
                    <p>{{ $employee->department->name ?? '' }}</p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Employment Type:</label>
                    <p>{{ $employee->employment_type }}</p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Employee Status:</label>
                    <p>{{ $employee->employee_status }}</p>
                </div>
            </div>
        </div>

        <!-- Employment Contracts Section -->
        <h3>Employment Contracts</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Contract Type:</label>
                    <p>{{ $employee->contract_type }}</p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Contract Start Date:</label>
                    <p>{{ $employee->contract_start_date }}</p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Contract End Date:</label>
                    <p>{{ $employee->contract_end_date }}</p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Probation Period (months):</label>
                    <p>{{ $employee->probation_period }}</p>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="form-group">
            <a href="{{ route('employees.index') }}" class="btn btn-primary">Back to List</a>
        </div>
    </div>
@endsection
