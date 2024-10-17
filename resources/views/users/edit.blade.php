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

            <br>
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
