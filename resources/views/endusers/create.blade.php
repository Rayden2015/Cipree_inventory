@extends('layouts.admin')
@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Add Enduser</title>

    </head>

    <body>

        <div>

            <br>
        </div>

        <div class="card">

            <div class="card-header">
                Add Enduser
                <a href="{{ route('endusers.index') }}" class="btn btn-primary float-right">Back</a>
            </div>
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
            <div class="card-body">

                <form action="{{ route('endusers.store') }}" method="POST" enctype="multipart/form-data">
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
                            {{-- <h3 class="card-title">Quick Example</h3> --}}
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> Asset ID / Name: <span class="text-danger"></span></label>
                                        <input value="{{ old('asset_staff_id') }}" required type="text"
                                            name="asset_staff_id" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> Category: <span class="text-danger"></span></label>
                                        <select class="select form-control" id="type" name="type"
                                            required data-fouc data-placeholder="Choose..">
                                            <option value=""></option>
                                            <option {{ old('type') == 'Equipment' ? 'selected' : '' }}
                                                value="Equipment">Equipment
                                            </option>
                                            <option {{ old('type') == 'Personnel' ? 'selected' : '' }}
                                                value="Personnel">
                                                Personnel</option>

                                                <option {{ old('type') == 'Organisation' ? 'selected' : '' }}
                                                value="Organisation">
                                                Organisation</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> Name/Description : <span class="text-danger"></span></label>

                                        <input value="{{ old('name_description') }}" required type="text"
                                            name="name_description" class="form-control">
                                    </div>
                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Department: </label>
                                        <select id="department_id" type="text" required
                                            class="form-control @error('department_id') is-invalid @enderror"
                                            name="department_id" autocomplete="department_id" autofocus>
                                            <option value="" selected hidden>Please Select</option>

                                            @foreach ($departments as $dt)
                                                <option {{ old('department_id') == $dt->id ? 'selected' : '' }}
                                                    value="{{ $dt->id }}">{{ $dt->name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('department_id'))
                                            <span class="text-danger">{{ $errors->first('department_id') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Section: </label>
                                        <select id="section_id" type="text" required
                                            class="form-control @error('section_id') is-invalid @enderror" name="section_id"
                                            autocomplete="section_id" autofocus>
                                            <option value="" selected hidden>Please Select</option>

                                            @foreach ($sections as $stn)
                                                <option {{ old('stn') == $stn->id ? 'selected' : '' }}
                                                    value="{{ $stn->id }}">{{ $stn->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> Model: <span class="text-danger"></span></label>
                                        <input value="{{ old('model') }}" type="text" name="model"
                                            class="form-control">
                                    </div>
                                </div>

                            </div>

                            <div class="row box">



                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> Serial Number: <span class="text-danger"></span></label>
                                        <input value="{{ old('serial_number') }}" type="text" name="serial_number"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> Manufacturer: <span class="text-danger"></span></label>
                                        <input value="{{ old('manufacturer') }}" type="text" name="manufacturer"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> Designation: <span class="text-danger"></span></label>
                                        <input value="{{ old('designation') }}" type="text" name="designation"
                                            class="form-control">
                                    </div>
                                </div>

                            </div>

                            {{--  --}}


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


    </html>
    {{-- <script>
        function toggleDiv(value) {

            if (value == "") {
                alert("Please select an option");
            }
            const box = document.getElementsByClassName("box");
            // box.style.display = value == 'Person' ? 'block' : 'none';
            for (let i = 0; i < box.length; i++) {
                box[i].style.display = value == 'Machine' ? 'block' : 'none';
            }
        }
    </script> --}}
@endsection
