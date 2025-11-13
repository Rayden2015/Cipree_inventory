@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Edit Employee</h2>
<br>
<a href="{{ route('employees.index') }}" class="btn btn-primary float-right">Back</a>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('employees.update', $employee->id) }}" method="POST">
            @csrf
            @method('PUT') <!-- Use PUT for updating the record -->

            <!-- Personal Information Section -->
            <h3>Personal Information</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">First Name:</label>
                        <input type="text" name="fname" class="form-control" value="{{ $employee->fname }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Last Name:</label>
                        <input type="text" name="lname" class="form-control" value="{{ $employee->lname }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Other Name:</label>
                        <input type="text" name="oname" class="form-control" value="{{ $employee->oname }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" class="form-control" value="{{ $employee->email }}" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="text" name="phone" class="form-control" value="{{ $employee->phone }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth:</label>
                        <input type="date" name="date_of_birth" class="form-control" 
                               value="{{ $employee->date_of_birth }}" 
                               max="{{ date('Y-m-d') }}">
                    </div>
                </div>
                

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="national_id_passport_number">National ID/Passport Number:</label>
                        <input type="text" name="national_id_passport_number" class="form-control" value="{{ $employee->national_id_passport_number }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="gender">Gender:</label>
                        <select name="gender" class="form-control">
                            <option value="Male" {{ $employee->gender == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ $employee->gender == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ $employee->gender == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nationality">Nationality:</label>
                        <select class="form-control" id="nationality" name="nationality">
                            @foreach ($countries as $code => $country)
                                <option value="{{ $code }}" {{ $code == $employee->nationality ? 'selected' : '' }}>
                                    {{ $country['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="marital_status">Marital Status:</label>
                        <select name="marital_status" class="form-control">
                            <option value="Single" {{ $employee->marital_status == 'Single' ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ $employee->marital_status == 'Married' ? 'selected' : '' }}>Married</option>
                            <option value="Divorced" {{ $employee->marital_status == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                            <option value="Widowed" {{ $employee->marital_status == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Job Information Section -->
            <h3>Job Information</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="job_title">Job Title:</label>
                        <input type="text" name="job_title" class="form-control" value="{{ $employee->job_title }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="department">Department:</label>
                        <select data-placeholder="Choose..." name="department_id" id="department_id"
                        class="select-search form-control @error('department_id') is-invalid @enderror" required>
                        <option value="" disabled {{ $employee->department_id ? '' : 'selected' }}>Please Select</option>
                        @foreach ($departments as $dt)
                            <option {{ $employee->department_id == $dt->id ? 'selected' : '' }}
                                value="{{ $dt->id }}">{{ $dt->name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('department_id'))
                        <span class="text-danger">{{ $errors->first('department_id') }}</span>
                    @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="employment_type">Employment Type:</label>
                        <select name="employment_type" class="form-control">
                            <option value="Full-time" {{ $employee->employment_type == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                            <option value="Part-time" {{ $employee->employment_type == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                            <option value="Contract" {{ $employee->employment_type == 'Contract' ? 'selected' : '' }}>Contract</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="employee_status">Employee Status:</label>
                        <select name="employee_status" class="form-control">
                            <option value="Active" {{ $employee->employee_status == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="On Leave" {{ $employee->employee_status == 'On Leave' ? 'selected' : '' }}>On Leave</option>
                            <option value="Terminated" {{ $employee->employee_status == 'Terminated' ? 'selected' : '' }}>Terminated</option>
                            <option value="Inactive" {{ $employee->employee_status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Employment Contracts Section -->
            <h3>Employment Contracts</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="contract_type">Contract Type:</label>
                        <select name="contract_type" class="form-control">
                            <option value="Permanent" {{ $employee->contract_type == 'Permanent' ? 'selected' : '' }}>Permanent</option>
                            <option value="Fixed-Term" {{ $employee->contract_type == 'Fixed-Term' ? 'selected' : '' }}>Fixed-Term</option>
                            <option value="Temporary" {{ $employee->contract_type == 'Temporary' ? 'selected' : '' }}>Temporary</option>
                            <option value="Other" {{ $employee->contract_type == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="contract_start_date">Contract Start Date:</label>
                        <input type="date" name="contract_start_date" class="form-control" value="{{ $employee->contract_start_date }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="contract_end_date">Contract End Date:</label>
                        <input type="date" name="contract_end_date" class="form-control" value="{{ $employee->contract_end_date }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="duration">Duration:</label>
                        <input type="number" name="duration" class="form-control" value="{{ $employee->duration }}">
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label for="probation_period">Probation Period (months):</label>
                        <input type="number" name="probation_period" class="form-control" value="{{ $employee->probation_period }}">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Update Employee</button> <br>
            </div>
        </form>
    </div>
@endsection
