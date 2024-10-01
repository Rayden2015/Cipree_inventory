@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Create New Employee</h2>
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

        <form action="{{ route('employees.store') }}" method="POST">
            @csrf

            <!-- Personal Information Section -->
            <h3>Personal Information</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="first name">First Name:</label>
                        <input type="text" name="fname" class="form-control" value="{{ old('fname') }}" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Last Name:</label>
                        <input type="text" name="lname" class="form-control" value="{{ old('lname') }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Other Name:</label>
                        <input type="text" name="oname" class="form-control" value="{{ old('oname') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth:</label>
                        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}" max="{{ date('Y-m-d') }}">
                    </div>
                </div>
                

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="national_id_passport_number">National ID/Passport Number:</label>
                        <input type="text" name="national_id_passport_number" class="form-control"
                            value="{{ old('national_id_passport_number') }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="gender">Gender:</label>
                        <select name="gender" class="form-control">
                            <option value="" disabled selected>Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nationality">Nationality:</label>
                        <select class="form-control" id="nationality" name="nationality">
                            @foreach ($countries as $code => $country)
                                <option value="{{ $code }}" {{ $country['name'] == 'Ghana' ? 'selected' : '' }}>
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
                            <option value="" disabled selected>Select Marital Status</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Divorced">Divorced</option>
                            <option value="Widowed">Widowed</option>
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
                        <input type="text" name="job_title" class="form-control" value="{{ old('job_title') }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="department">Department:</label>
                        <select id="department_id" type="text" 
                        class="form-control @error('department_id') is-invalid @enderror"
                        name="department_id" autocomplete="department_id" autofocus>
                        <option value="" selected hidden>Please Select</option>

                        @foreach ($departments as $dt)
                            <option {{ old('dt') == $dt->id ? 'selected' : '' }}
                                value="{{ $dt->id }}">{{ $dt->name }}</option>
                        @endforeach
                    </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="employment_type">Employment Type:</label>
                        <select name="employment_type" class="form-control">
                            <option value="" disabled selected>Select Employment Type</option>
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="employee_status">Employee Status:</label>
                        <select name="employee_status" class="form-control">
                            <option value="" disabled selected>Select Employee Status</option>
                            <option value="Active">Active</option>
                            <option value="On Leave">On Leave</option>
                            <option value="Terminated">Terminated</option>
                            <option value="Inactive">Inactive</option>
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
                            <option value="" disabled selected>Select Contract Type</option>
                            <option value="Permanent">Permanent</option>
                            <option value="Fixed-Term">Fixed-Term</option>
                            <option value="Temporary">Temporary</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="contract_start_date">Contract Start Date:</label>
                        <input type="date" name="contract_start_date" class="form-control"
                            value="{{ old('contract_start_date') }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="contract_end_date">Contract End Date:</label>
                        <input type="date" name="contract_end_date" class="form-control" value="{{ old('contract_end_date') }}" min="{{ date('Y-m-d') }}">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="probation_period">Probation Period (months):</label>
                        <input type="number" name="probation_period" class="form-control"
                            value="{{ old('probation_period') }}">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="form-group">
                <button type="submit" class="btn btn-success">Create Employee</button>
            </div>
        </form>
    </div>
@endsection
