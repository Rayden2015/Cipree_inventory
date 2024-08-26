@extends('layouts.admin')
@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
        <link rel="stylesheet" href="https://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
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

                <h3 class="card-title">Edit EndUser</h3>

                <a href="{{ route('endusers.index') }}" class="btn btn-primary float-right">Back</a>
            </div>

            <div class="card-body">

                <form action="{{ route('endusers.update', $enduser->id) }}" method="POST" enctype="multipart/form-data">
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

                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Edit Site</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> Asset Staff ID: <span class="text-danger"></span></label>
                                        <input value="{{ $enduser->asset_staff_id }}" type="text" name="asset_staff_id"
                                            class="form-control">
                                        @if ($errors->has('asset_staff_id'))
                                            <span class="text-danger">{{ $errors->first('asset_staff_id') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> Name/Description: <span class="text-danger"></span></label>
                                        <input value="{{ $enduser->name_description }}" type="text"
                                            name="name_description" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Department: </label>
                                        <select data-placeholder="Choose..." name="department_id" id="department_id"
                                            class="select-search form-control">
                                            <option value=""></option>
                                            @foreach ($departments as $dt)
                                                <option {{ $enduser->department_id == $dt->id ? 'selected' : '' }}
                                                    value="{{ $dt->id }}">{{ $dt->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>



                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Section: </label>
                                        <select data-placeholder="Choose..." name="section_id" id="section_id"
                                            class="select-search form-control">
                                            <option value=""></option>
                                            @foreach ($sections as $st)
                                                <option {{ $enduser->section_id == $st->id ? 'selected' : '' }}
                                                    value="{{ $st->id }}">{{ $st->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> Model: <span class="text-danger"></span></label>
                                        <input value="{{ $enduser->model }}" type="text" name="model"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> Serial Number: <span class="text-danger"></span></label>
                                        <input value="{{ $enduser->serial_number }}" type="text" name="serial_number"
                                            class="form-control">
                                    </div>
                                </div>



                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Category: </label>
                                        <select class="select form-control" id="type" name="type" required data-fouc
                                            data-placeholder="Choose..">

                                            <option value=""></option>
                                            <option {{ $enduser->type == 'Equipment' ? 'selected' : '' }}
                                                value="Equipment">Equipment</option>
                                            <option {{ $enduser->type == 'Personnel' ? 'selected' : '' }}
                                                value="Personnel">Personnel</option>
                                            <option {{ $enduser->type == 'Organisation' ? 'selected' : '' }}
                                                value="Organisation">Organisation</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> Manufacturer: <span class="text-danger"></span></label>
                                        <input value="{{ $enduser->manufacturer }}" type="text" name="manufacturer"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> Designation: <span class="text-danger"></span></label>
                                        <input value="{{ $enduser->designation }}" type="text" name="designation"
                                            class="form-control">
                                    </div>
                                </div>



                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Status: </label>
                                        <select class="select form-control" id="status" name="status" required
                                            data-fouc data-placeholder="Choose..">

                                            <option value=""></option>
                                            <option {{ $enduser->status == 'Active' ? 'selected' : '' }} value="Active">
                                                Active</option>
                                            <option {{ $enduser->status == 'Inactive' ? 'selected' : '' }}
                                                value="Inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>

                                @if (Auth::user()->hasRole('Admin'))
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Sites: </label>

                                            <select data-placeholder="Choose..." name="site_id" id="site_id"
                                                class="select-search form-control">
                                                <option value=""></option>
                                                @foreach ($sites as $st)
                                                    <option {{ $enduser->site_id == $st->id ? 'selected' : '' }}
                                                        value="{{ $st->id }}">{{ $st->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif

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
    <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}


    </html>
@endsection
