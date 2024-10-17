@extends('layouts.admin')
@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
            integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA=="
            crossorigin="anonymous" />
    </head>

    <body>
        <div>

            <br>
        </div>

        <div class="card">

            <div class="card-header">
                Create Users
                <a href="{{ route('users.index') }}" class="btn btn-primary float-right">Back</a>
            </div>

            <div class="card-body">

                <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

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
                            <h3 class="card-title">Create User</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Name: <span class="text-danger"></span></label>
                                        <input value="{{ old('name') }}" required type="text" name="name"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Email: <span class="text-danger"></span></label>
                                        <input value="{{ old('email') }}" required type="email" name="email"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>



                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Date of Birth: <span class="text-danger"></span></label>
                                        <input name="dob" value="{{ old('dob') }}" type="date"
                                            class="form-control date-pick" placeholder="Select Date...">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Role: </label>
                                        <select id="role_id" type="text"
                                            class="form-control @error('role_id') is-invalid @enderror" name="role_id"
                                            autocomplete="role_id" autofocus>
                                            <option value="" selected hidden>Please Select</option>

                                            @foreach ($roles as $rt)
                                                <option {{ old('rt') == $rt->id ? 'selected' : '' }}
                                                    value="{{ $rt->id }}">{{ $rt->name }}</option>
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
                                        <label>Facebook URL: </label>
                                        <input type="text" value="{{ old('facebook_url') }}" name="facebook_url"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Twitter URL: </label>
                                        <input type="text" value="{{ old('twitter_url') }}" name="twitter_url"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>LinkedIn URL: </label>
                                        <input type="text" value="{{ old('linkedin_url') }}" name="linkedin_url"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Instagram URL: </label>
                                        <input type="text" value="{{ old('instagram_url') }}" name="instagram_url"
                                            class="form-control">
                                    </div>
                                </div>
                                {{-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Facebook URL: </label>
                                        <input type="text" value="{{ old('facebook_url') }}" name="facebook_url"
                                            class="form-control">
                                    </div>
                                </div> --}}
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Password: <span class="text-danger"></span></label>
                                        <input value="{{ old('password') }}" required type="password" name="password"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Confirm Password: </label>
                                        <input type="password" value="{{ old('password_confirmation') }}"
                                            name="password_confirmation" class="form-control">
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


    </html>
@endsection
