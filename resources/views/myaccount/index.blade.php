@extends('layouts.admin')
@section('page_title', 'My Account')
@section('content')
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <title>Document</title>

</head>
    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">My Account</h6>
            {{-- {!! Qs::getPanelOptions() !!} --}}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#change-pass" class="nav-link active" data-toggle="tab">Change Password</a></li>
                {{-- @if (Qs::userIsPTA()) --}}
                <li class="nav-item"><a href="#edit-profile" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i>
                        Manage Profile</a></li>
                {{-- @endif --}}
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="change-pass">
                    <div class="row">

                        <div class="card-body">
                            <form method="post" action="{{ route('myaccounts.changepassword') }}">
                                @csrf
                                @method('put')

                                @foreach ($errors->all() as $error)
                                    <p class="text-danger">{{ $error }}</p>
                                @endforeach

                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">Current
                                        Password</label>

                                    <div class="col-md-6">
                                        <input id="password" type="password" class="form-control" name="current_password"
                                            autocomplete="current-password">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">New Password</label>

                                    <div class="col-md-6">
                                        <input id="new_password" type="password" class="form-control" name="new_password"
                                            autocomplete="current-password">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">New Confirm
                                        Password</label>

                                    <div class="col-md-6">
                                        <input id="new_confirm_password" type="password" class="form-control"
                                            name="new_password_confirmation" autocomplete="current-password">
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-8 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            Update Password
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                {{-- @if (Qs::userIsPTA()) --}}
                <div class="tab-pane fade" id="edit-profile">
                    <div class="row">
                        <div class="col-md-6">
                            <form enctype="multipart/form-data" method="post"
                                action="{{ route('myaccounts.update', $my->id) }}">
                                @csrf @method('put')

                                <div class="form-group row">
                                    <label for="name" class="col-lg-3 col-form-label font-weight-semibold">Name</label>
                                    <div class="col-lg-9">
                                        <input disabled="disabled" id="name" class="form-control" type="text"
                                            value="{{ $my->name }}">
                                    </div>
                                </div>



                                <div class="form-group row">
                                    <label for="email" class="col-lg-3 col-form-label font-weight-semibold">Email
                                    </label>
                                    <div class="col-lg-9">
                                        <input id="email" value="{{ $my->email }}" name="email" type="email"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="phone" class="col-lg-3 col-form-label font-weight-semibold">Phone
                                    </label>
                                    <div class="col-lg-9">
                                        <input id="phone" value="{{ $my->phone }}" name="phone" type="text"
                                            class="form-control">
                                    </div>
                                </div>



                                <div class="form-group row">
                                    <label for="address" class="col-lg-3 col-form-label font-weight-semibold">Address
                                    </label>
                                    <div class="col-lg-9">
                                        <input id="address" value="{{ $my->address }}" name="address" type="text"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="image" class="col-lg-3 col-form-label font-weight-semibold">Change
                                        Photo </label>
                                    <div class="col-lg-9">
                                        <input accept="image/*" type="file" name="image" class="form-input-styled"
                                            data-fouc>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-danger">Submit form <i
                                            class="icon-paperplane ml-2"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                {{-- @endif --}}
            </div>
        </div>
    </div>

    {{-- My Profile Ends --}}
    <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}
@endsection
