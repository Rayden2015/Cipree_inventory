@extends('layouts.admin')
@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
        <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
            integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA=="
            crossorigin="anonymous" />
    </head>

    <body>
        <div>

<<<<<<< HEAD
                <div class="card-body">
                    <form action="{{ route('users.update', $user->id) }}" method="post">
                        @csrf
                        @method('PUT')

                        <div class="mb-3 row">
                            <label for="name" class="col-md-4 col-form-label text-md-end text-start">Name</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ $user->name }}">
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
                                    id="email" name="email" value="{{ $user->email }}">
                                @if ($errors->has('email'))
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="address" class="col-md-4 col-form-label text-md-end text-start">Address</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control @error('address') is-invalid @enderror"
                                    id="address" name="address" value="{{ $user->address }}">
                                @if ($errors->has('address'))
                                    <span class="text-danger">{{ $errors->first('address') }}</span>
                                @endif
                            </div>
                        </div>

                        
                        <div class="mb-3 row">
                            <label for="phone" class="col-md-4 col-form-label text-md-end text-start">Phone</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ $user->phone }}">
                                @if ($errors->has('phone'))
                                    <span class="text-danger">{{ $errors->first('phone') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="dob" class="col-md-4 col-form-label text-md-end text-start">Date of
                                Birth</label>
                            <div class="col-md-6">
                                <input type="date" class="form-control @error('dob') is-invalid @enderror" id="dob"
                                    name="dob" value="{{ $user->dob }}">
                                @if ($errors->has('dob'))
                                    <span class="text-danger">{{ $errors->first('dob') }}</span>
                                @endif
                            </div>
                        </div>


                        <div class="mb-3 row">
                            <label for="password" class="col-md-4 col-form-label text-md-end text-start">Password</label>
                            <div class="col-md-6">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password">
                                @if ($errors->has('password'))
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="password_confirmation"
                                class="col-md-4 col-form-label text-md-end text-start">Confirm Password</label>
                            <div class="col-md-6">
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation">
                            </div>
                        </div>

                        <div class="mb-3 row">

                            <label for="password_confirmation"
                                class="col-md-4 col-form-label text-md-end text-start">Status</label>
                            <div class="col-md-6">
                                <select class="select form-control" id="status" name="status" required data-fouc
                                    data-placeholder="Choose..">

                                    <option value=""></option>
                                    <option {{ $user->status == 'Active' ? 'selected' : '' }} value="Active">
                                        Active</option>
                                    <option {{ $user->status == 'Inactive' ? 'selected' : '' }} value="Inactive">Inactive
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row">

                            <label for="password_confirmation"
                                class="col-md-4 col-form-label text-md-end text-start">Site Name</label>
                            <div class="col-md-6">
                                <select data-placeholder="Choose..." name="site_id" id="site_id"
                                    class="select-search form-control">
                                    <option value=""></option>
                                    @foreach ($sites as $st)
                                        <option {{ $user->site_id == $st->id ? 'selected' : '' }}
                                            value="{{ $st->id }}">{{ $st->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="mb-3 row">

                            <label for="department_id"
                                class="col-md-4 col-form-label text-md-end text-start">Department</label>
                            <div class="col-md-6">
                                <select data-placeholder="Choose..." name="department_id" id="department_id"
                                    class="select-search form-control">
                                    <option value=""></option>
                                    @foreach ($departments as $dp)
                                        <option {{ $user->department_id == $dp->id ? 'selected' : '' }}
                                            value="{{ $dp->id }}">{{ $dp->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="mb-3 row">

                            <label for="section_id"
                                class="col-md-4 col-form-label text-md-end text-start">Section</label>
                            <div class="col-md-6">
                                <select data-placeholder="Choose..." name="section_id" id="section_id"
                                    class="select-search form-control">
                                    <option value=""></option>
                                    @foreach ($sections as $sct)
                                        <option {{ $user->section_id == $sct->id ? 'selected' : '' }}
                                            value="{{ $sct->id }}">{{ $sct->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>


                        <div class="mb-3 row">
                            <label for="staff_id" class="col-md-4 col-form-label text-md-end text-start">Staff ID</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control @error('staff_id') is-invalid @enderror"
                                    id="staff_id" name="staff_id" value="{{ $user->staff_id }}">
                                @if ($errors->has('staff_id'))
                                    <span class="text-danger">{{ $errors->first('staff_id') }}</span>
                                @endif
                            </div>
                        </div>


                        <div class="mb-3 row">
                            <label for="roles" class="col-md-4 col-form-label text-md-end text-start">Roles</label>
                            <div class="col-md-6">
                                @forelse ($roles as $role)
                                    @if ($role->name != 'Super Admin')
                                        <!-- Checkbox for roles -->
                                        <div class="form-check">
                                            <input class="form-check-input @error('roles') is-invalid @enderror"
                                                type="checkbox"
                                                name="roles[]"
                                                id="role_{{ $role->id }}"
                                                value="{{ $role->name }}"
                                                {{ in_array($role->name, $userRoles ?? []) ? 'checked' : '' }}>
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
                                                    {{ in_array($role->name, $userRoles ?? []) ? 'checked' : '' }}>
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
                            <input type="submit" class="col-md-3 offset-md-5 btn btn-primary" value="Update User">
                        </div>

                    </form>
                </div>
            </div>
=======
            <br>
>>>>>>> d29d2b411f82256fddca149984e6cef765ac5ec9
        </div>

        <div class="card">

            <div class="card-header">

                <a href="{{ route('users.index') }}" class="btn btn-primary float-right">Back</a>
            </div>

            <div class="card-body">

                <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Edit User</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Name: <span class="text-danger"></span></label>
                                        <input value="{{ $user->name }}" required type="text" name="name"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Email: <span class="text-danger"></span></label>
                                        <input value="{{ $user->email }}" required type="email" name="email"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>



                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Date of Birth: <span class="text-danger"></span></label>
                                        <input name="dob" value="{{ $user->dob }}" type="date"
                                            class="form-control date-pick" placeholder="Select Date...">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Role: </label>
                                        <select data-placeholder="Choose..." name="role_id" id="role_id"
                                            class="select-search form-control">
                                            <option value=""></option>
                                            @foreach ($roles as $na)
                                                <option {{ $user->role_id == $na->id ? 'selected' : '' }}
                                                    value="{{ $na->id }}">{{ $na->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Address: </label>
                                        <input type="text" value="{{ old('address') }}" name="address"
                                            class="form-control">
                                    </div>
                                </div>                       


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Phone: </label>
                                        <input type="text" value="{{ $user->phone}}" name="phone"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Facebook URL: </label>
                                        <input type="text" value="{{ $user->facebook_url }}" name="facebook_url"
                                            class="form-control">
                                    </div>
                                </div> 
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Twitter URL: </label>
                                        <input type="text" value="{{$user->twitter_url }}" name="twitter_url"
                                            class="form-control">
                                    </div>
                                </div>                       


                               
                            </div>
                            <div class="row">
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Instagram URL: </label>
                                        <input type="text" value="{{ $user->instagram_url }}" name="instagram_url"
                                            class="form-control">
                                    </div>
                                </div> 
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>LinkedIn URL: </label>
                                        <input type="text" value="{{ $user->linkedin_url }}" name="linkedin_url"
                                            class="form-control">
                                    </div>
                                </div>                       


                               
                            </div>
                            <div class="row">
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Profile Picture: </label>
                                        <input type="file" value="{{ old('picture') }}" name="picture"
                                            class="form-control">
                                    </div>
                                </div> 
                                                     


                               
                            </div>
                            <div class="row">



                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Password: </label>
                                        <input type="password"  name="password"
                                            class="form-control">
                                    </div>
                                </div>
                               
                            </div>

                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                </form>
            </div>

        </div>

        </div>
    </body>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
        integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

        <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
        <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
        {!! Toastr::message() !!}

    </html>
@endsection
