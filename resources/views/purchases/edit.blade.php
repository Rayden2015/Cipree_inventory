@extends('layouts.admin')
@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Edit Purchase</title>
        <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        {{-- <link rel="stylesheet" type="text/css"
            href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.0-alpha1/css/bootstrap.min.css"> --}}
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
                {{-- @if (Auth::user()->role->name == 'purchasing_officer')
                    <a href="{{ route('authorise.all_requests') }}" class="btn btn-primary float-right">Back</a>
                @else
                    <a href="{{ route('purchases.index') }}" class="btn btn-primary float-right">Back</a>
                @endif --}}
                @if (Auth::user()->hasRole('purchasing_officer'))
                    <a href="{{ route('authorise.all_requests') }}" class="btn btn-primary float-right">Back</a>
                @else
                    <a href="{{ route('purchases.index') }}" class="btn btn-primary float-right">Back</a>
                @endif

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

                <form action="{{ route('orders.update', $purchase->id) }}" method="POST" enctype="multipart/form-data">
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
                            <h3 class="card-title">Edit Purchase</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Supplier: </label>
                                        <select data-placeholder="Choose..." name="supplier_id" id="supplier_id"
                                            class="select-search form-control">
                                            <option value=""></option>
                                            @foreach ($suppliers as $sp)
                                                <option {{ $purchase->supplier_id == $sp->id ? 'selected' : '' }}
                                                    value="{{ $sp->id }}">{{ $sp->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Type of Purchase: </label>
                                        <select class="select form-control" id="type_of_purchase" name="type_of_purchase"
                                            required data-fouc data-placeholder="Choose..">

                                            <option value=""></option>
                                            <option {{ $purchase->type_of_purchase == 'Direct' ? 'selected' : '' }}
                                                value="Direct">Direct</option>
                                            <option {{ $purchase->type_of_purchase == 'Stock' ? 'selected' : '' }}
                                                value="Stock">Stock</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Enduser: </label>
                                        <select data-placeholder="Choose..." name="enduser_id" id="enduser_id"
                                            class="select-search form-control">
                                            <option value=""></option>
                                            @foreach ($endusers as $ed)
                                                <option {{ $purchase->enduser_id == $ed->id ? 'selected' : '' }}
                                                    value="{{ $ed->id }}">{{ $ed->asset_staff_id }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status: </label>
                                        <select class="select form-control" id="status" name="status" required data-fouc
                                            data-placeholder="Choose..">

                                            <option value=""></option>
                                            <option {{ $purchase->status == 'Requested' ? 'selected' : '' }}
                                                value="Requested">Requested</option>
                                            <option {{ $purchase->status == 'Initiated' ? 'selected' : '' }}
                                                value="Initiated">Initiated</option>
                                            <option {{ $purchase->status == 'Approved' ? 'selected' : '' }}
                                                value="Approved">Approved</option>
                                            <option {{ $purchase->status == 'Ordered' ? 'selected' : '' }} value="Ordered">
                                                Ordered</option>
                                            @if (Auth::user()->role->name == 'store_officer')
                                                <option {{ $purchase->status == 'Supplied' ? 'selected' : '' }}
                                                    value="Supplied">Supplied</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>


                            </div>


                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                </form>
            </div>
            <!-- /.card-body -->
            <div class="card-body">
                <table id="editable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Description</th>
                            <th>Part Number</th>
                            <th>UoM</th>

                            <th>Quantity</th>
                            <th>Priority</th>
                            <th>Remarks</th>
                            <th>Action</th>


                        </tr>
                    </thead>
                    @forelse ($order_parts as $ph)
                        <tbody>
                            <tr>
                                <form action="{{ route('orders.action', $ph->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <td>{{ $ph->id }}</td>
                                    <td><input type="text" class="form-control" name="description"
                                            value="{{ $ph->description }}">
                                    </td>
                                    <td><input type="text" class="form-control" name="part_number"
                                            value="{{ $ph->part_number }}">
                                    </td>
                                    <td>

                                        <select class="select form-control" id="uom" name="uom" required data-fouc
                                            data-placeholder="Choose..">

                                            <option value=""></option>
                                            <option {{ $ph->uom == 'Kilograms' ? 'selected' : '' }} value="Kilograms">
                                                Kilograms</option>
                                            <option {{ $ph->uom == 'Meters' ? 'selected' : '' }} value="Meters">
                                                Meters</option>
                                            <option {{ $ph->uom == 'Litres' ? 'selected' : '' }} value="Litres">Litres
                                            </option>
                                            <option {{ $ph->uom == 'Pieces' ? 'selected' : '' }} value="Pieces">
                                                Pieces</option>
                                            <option {{ $ph->uom == 'Kits' ? 'selected' : '' }} value="Kits">
                                                Kits</option>
                                            <option {{ $ph->uom == 'Pair' ? 'selected' : '' }} value="Pair">
                                                Pair</option>
                                            <option {{ $ph->uom == 'Bale' ? 'selected' : '' }} value="Bale">
                                                Bale</option>
                                            <option {{ $ph->uom == 'Bottle' ? 'selected' : '' }} value="Bottle">
                                                Bottle</option>
                                            <option {{ $ph->uom == 'Box' ? 'selected' : '' }} value="Box">
                                                Box</option>
                                            <option {{ $ph->uom == 'Bucket' ? 'selected' : '' }} value="Bucket">
                                                Bucket</option>
                                            <option {{ $ph->uom == 'Carton' ? 'selected' : '' }} value="Carton">Carton
                                            </option>
                                            <option {{ $ph->uom == 'Drum' ? 'selected' : '' }} value="Drum">
                                                Drum</option>
                                            <option {{ $ph->uom == 'Gallon' ? 'selected' : '' }} value="Gallon">Gallon
                                            </option>
                                            <option {{ $ph->uom == 'Pack' ? 'selected' : '' }} value="Pack">
                                                Pack</option>

                                            <option {{ $ph->uom == 'Rim' ? 'selected' : '' }} value="Rim">
                                                Rim</option>
                                            <option {{ $ph->uom == 'Roll' ? 'selected' : '' }} value="Roll">
                                                Roll</option>

                                        </select>
                                    </td>

                                    <td><input type="number" class="form-control" name="quantity"
                                            value="{{ $ph->quantity }}"></td>
                                    <td>

                                        <select name="priority" id="priority" class="select form-control">
                                            <option value=""></option>
                                            <option {{ $ph->priority == 'High' ? 'selected' : '' }} value="High">
                                                High</option>
                                            <option {{ $ph->priority == 'Medium' ? 'selected' : '' }} value="Medium">
                                                Medium</option>
                                            <option {{ $ph->priority == 'Low' ? 'selected' : '' }} value="Low">
                                                Low</option>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control" name="remarks"
                                            value="{{ $ph->remarks }}"></td>
                                    <td> <button type="submit" class="btn btn-primary">Submit</button></td>
                                @empty
                            <tr>
                                <td class="text-center" colspan="12">Data Not Found!</td>
                            </tr>
                    @endforelse
                    </form>
                    </tr>
                    </tbody>

                </table>
            </div>


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
        $('.livesearch').select2({
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
        $('.livesearch').select2({
            placeholder: 'Select Enduser',
            ajax: {
                url: '/ajax-autocomplete-enduser',
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
            placeholder: 'Select Site',
            ajax: {
                url: '/ajax-autocomplete-site',
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
            placeholder: 'Select Location',
            ajax: {
                url: '/ajax-autocomplete-location',
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
