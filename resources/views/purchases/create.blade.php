@extends('layouts.admin')
@section('content')

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Add Purchase</title>
        <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <link rel="stylesheet" type="text/css"
            href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.0-alpha1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" /> --}}
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

        <link rel="stylesheet" href="{{ asset('/css/select2.min.css') }}">
        <style>
            h2 {
                color: white;
            }

            .select2-selection__rendered {
                line-height: 31px !important;
            }

            .select2-container .select2-selection--single {
                height: 35px !important;
            }

            .select2-selection__arrow {
                height: 34px !important;
            }
        </style>
    </head>

    <body>
        <div>

            <br>
        </div>

        <div class="card">

            <div class="card-header">
                Add Purchase
                <a href="{{ route('purchases.index') }}" class="btn btn-primary float-right">Back</a>
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

                <form action="{{ route('purchases.store') }}" method="POST" enctype="multipart/form-data">
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
                                <div class="col-md-6">
                                    <div class="form-group">

                                        <label> Part: <span class="text-danger"></span></label>
                                        <select class="livesearch form-control p-3" name="part_id" required></select>

                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">

                                        <label> Supplier: <span class="text-danger"></span></label>
                                        <select class="livesearch2 form-control p-3" name="supplier_id" required></select>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">

                                        <label> Enduser : <span class="text-danger"></span></label>
                                        <select class="livesearch3 form-control p-3" name="enduser_id" required></select>

                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Type of Purchase: <span class="text-danger"></span></label>
                                        <select class="select form-control" id="type_of_purchase" name="type_of_purchase"
                                            required data-fouc data-placeholder="Choose..">
                                            <option value=""></option>
                                            <option {{ old('type_of_purchase') == 'Direct' ? 'selected' : '' }}
                                                value="Direct">Direct
                                            </option>
                                            <option {{ old('type_of_purchase') == 'Stock' ? 'selected' : '' }}
                                                value="Stock">
                                                Stock</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Quantity: <span class="text-danger"></span></label>
                                        <input value="{{ old('quantity') }}" required type="number" name="quantity"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Make: <span class="text-danger"></span></label>
                                        <input value="{{ old('make') }}" required type="text" name="make"
                                            class="form-control">
                                    </div>
                                </div>


                            </div>

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Model: <span class="text-danger"></span></label>
                                        <input value="{{ old('model') }}" required type="text" name="model"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Serial Number: </label>
                                        <input type="text" value="{{ old('serial_number') }}" name="serial_number"
                                            required class="form-control">
                                    </div>
                                </div>


                            </div>

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Tax: <span class="text-danger"></span></label>
                                        <input value="{{ old('tax') }}" required type="text" name="tax"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tax 2: </label>
                                        <input type="text" value="{{ old('tax2') }}" name="tax2"
                                            class="form-control">
                                    </div>
                                </div>


                            </div>

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Tax3: <span class="text-danger"></span></label>
                                        <input value="{{ old('tax3') }}" type="text" name="tax3"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Unit Price: </label>
                                        <input type="text" value="{{ old('unit_price') }}" name="unit_price" required
                                            class="form-control">
                                    </div>
                                </div>


                            </div>

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Currency: <span class="text-danger"></span></label>
                                        <input value="{{ old('currency') }}" required type="text" name="currency"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Description: </label>
                                        <input type="text" value="{{ old('description') }}" name="description"
                                            required class="form-control">
                                    </div>
                                </div>


                            </div>

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Intended Recipient: <span class="text-danger"></span></label>
                                        <input value="{{ old('intended_recipient') }}" required type="text"
                                            name="intended_recipient" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Comments: <span class="text-danger"></span></label>
                                        <input value="{{ old('comments') }}" required type="text" name="comments"
                                            class="form-control">
                                    </div>
                                </div>



                            </div>

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Status: <span class="text-danger"></span></label>
                                        <select class="select form-control" id="status" name="status" required
                                            data-fouc data-placeholder="Choose..">
                                            <option value=""></option>
                                            <option {{ old('status') == 'Requested' ? 'selected' : '' }}
                                                value="Requested">Requested
                                            </option>
                                            

                                        </select>
                                    </div>
                                </div>




                            </div>






                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                </form>
            </div>

        </div>

        </div>
    </body>
    <script type="text/javascript">
        $('.livesearch').select2({
            placeholder: 'Select Parts',
            ajax: {
                url: '/ajax-autocomplete-part',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });
    </script>
    <script type="text/javascript">
        $('.livesearch2').select2({
            placeholder: 'Select Supplier',
            ajax: {
                url: '/ajax-autocomplete-search',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });
    </script>

    <script type="text/javascript">
        $('.livesearch3').select2({
            placeholder: 'Select Enduser',
            ajax: {
                url: '/ajax-autocomplete-enduser',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.asset_staff_id,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });
    </script>

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
