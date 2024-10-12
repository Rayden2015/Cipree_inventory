@extends('layouts.admin')

@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Add User</title>
        <link rel="stylesheet" href="https://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
            integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA=="
            crossorigin="anonymous" />
    </head>

    <body>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="float-start">
                            Add New User
                        </div>
                        <div class="float-end">
                            <a href="{{ route('users.index') }}" class="btn btn-primary btn-sm">&larr; Back</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('users.store') }}" method="post">
                            @csrf

                           
                            @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
        
                            <div class="mb-3 row">
                                <label for="name" class="col-md-4 col-form-label text-md-end text-start">Name</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name') }}">
                                    @if ($errors->has('name'))
                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="email" class="col-md-4 col-form-label text-md-end text-start">Email
                                    Address</label>
                                <div class="col-md-6">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}">
                                    @if ($errors->has('email'))
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="phone" class="col-md-4 col-form-label text-md-end text-start">Phone</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        id="phone" name="phone" value="{{ old('phone') }}">
                                    @if ($errors->has('phone'))
                                        <span class="text-danger">{{ $errors->first('phone') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="address" class="col-md-4 col-form-label text-md-end text-start">Address</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control @error('address') is-invalid @enderror"
                                        id="address" name="address" value="{{ old('address') }}">
                                    @if ($errors->has('address'))
                                        <span class="text-danger">{{ $errors->first('address') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="dob" class="col-md-4 col-form-label text-md-end text-start">Date of
                                    Birth</label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control @error('dob') is-invalid @enderror"
                                        id="dob" name="dob" value="{{ old('dob') }}">
                                    @if ($errors->has('dob'))
                                        <span class="text-danger">{{ $errors->first('dob') }}</span>
                                    @endif
                                </div>
                            </div>


                            <div class="mb-3 row">
                                <label for="site" class="col-md-4 col-form-label text-md-end text-start">Site
                                    Name</label>
                                <div class="col-md-6">
                                    <label>Site Name: </label>
                                    <select id="site_id" type="text"
                                        class="form-control @error('site_id') is-invalid @enderror" name="site_id"
                                        autocomplete="site_id" autofocus required>
                                        <option value="" selected hidden>Please Select</option>

                                        @foreach ($sites as $st)
                                            <option {{ old('st') == $st->id ? 'selected' : '' }}
                                                value="{{ $st->id }}">
                                                {{ $st->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="mb-3 row">
                                <label for="department id" class="col-md-4 col-form-label text-md-end text-start">
                                    Department</label>
                                <div class="col-md-6">
                                    <label>Department: </label>
                                    <select id="department_id" type="text"
                                        class="form-control @error('department_id') is-invalid @enderror" name="department_id"
                                        autocomplete="department_id" autofocus required>
                                        <option value="" selected hidden>Please Select</option>

                                        @foreach ($departments as $dp)
                                            <option {{ old('dp') == $dp->id ? 'selected' : '' }}
                                                value="{{ $dp->id }}">
                                                {{ $dp->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="status" class="col-md-4 col-form-label text-md-end text-start">Status</label>
                                <div class="col-md-6">

                                    <select class="select form-control" id="status" name="status" required data-fouc
                                        data-placeholder="Choose..">
                                        <option value=""></option>
                                        <option {{ old('status') == 'Active' ? 'selected' : '' }} value="Active">Active
                                        </option>
                                        <option {{ old('status') == 'Inactive' ? 'selected' : '' }} value="Inactive">
                                            Inactive</option>
                                    </select>
                                </div>
                            </div>

                            

                        <div class="mb-3 row">
                            <label for="roles" class="col-md-4 col-form-label text-md-end text-start">Roles</label>
                            <div class="col-md-6">
                                @forelse ($roles as $role)
                                    @if ($role->name != 'Super Admin')
                                        <div class="form-check">
                                            <input class="form-check-input @error('roles') is-invalid @enderror"
                                                type="checkbox"
                                                name="roles[]"
                                                id="role_{{ $role->id }}"
                                                value="{{ $role->name }}"
                                                {{ in_array($role->name, old('roles') ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="role_{{ $role->id }}">
                                                {{ $role->name }}
                                            </label>
                                        </div>
                                    @else
                                        @if (Auth::user()->hasRole('Super Admin'))
                                            <div class="form-check">
                                                <input class="form-check-input @error('roles') is-invalid @enderror"
                                                    type="checkbox"
                                                    name="roles[]"
                                                    id="role_{{ $role->id }}"
                                                    value="{{ $role->name }}"
                                                    {{ in_array($role->name, old('roles') ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role_{{ $role->id }}">
                                                    {{ $role->name }}
                                                </label>
                                            </div>
                                        @endif
                                    @endif
                                @empty
                                    <div>No roles available</div>
                                @endforelse
                        
                                @if ($errors->has('roles'))
                                    <span class="text-danger">{{ $errors->first('roles') }}</span>
                                @endif
                            </div>
                        </div>
                        

                            <div class="mb-3 row">
                                <label for="staff_id" class="col-md-4 col-form-label text-md-end text-start">Staff
                                    ID</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control @error('staff_id') is-invalid @enderror"
                                        id="staff_id" name="staff_id" value="{{ old('staff_id') }}">
                                    @if ($errors->has('staff_id'))
                                        <span class="text-danger">{{ $errors->first('staff_id') }}</span>
                                    @endif
                                </div>
                            </div>


                            <div class="mb-3 row">
                                <input type="submit" class="col-md-3 offset-md-5 btn btn-primary" value="Add User">
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
        </script>
    </body>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
        integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}


    </html>
@endsection
