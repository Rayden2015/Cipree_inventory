@extends('layouts.admin')
@section('content')

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Edit Item</title>
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
                <h3 class="card-title">Edit Item</h3>
                <a href="{{ route('items.index') }}" class="btn btn-primary float-right">Back</a>
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

                <form action="{{ route('items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
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
                            <h3 class="card-title">Edit Item</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> Description: <span class="text-danger"></span></label>
                                        <input value="{{ $item->item_description }}" required type="text" name="item_description"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> Part Number: <span class="text-danger"></span></label>
                                        <input value="{{ $item->item_part_number }}" required type="text" name="item_part_number"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> Stock Code: <span class="text-danger"></span></label>
                                        <input value="{{ $item->item_stock_code }}" readonly type="text" name="item_stock_code"
                                            class="form-control">
                                    </div>
                                </div>

                                

                               
                            </div>



                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label> Reorder Level: <span class="text-danger"></span></label>
                                        <input value="{{ $item->reorder_level }}"  type="number" name="reorder_level"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label> Maximum Stock Level: <span class="text-danger"></span></label>
                                        <input value="{{ $item->max_stock_level }}"  type="number" min="0" name="max_stock_level"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label> Lead Time (Days): <span class="text-danger"></span></label>
                                        <input value="{{ $item->lead_time_days }}"  type="number" min="0" name="lead_time_days"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label> Valuation Method: </label>
                                        <select data-placeholder="Choose..." name="valuation_method" id="valuation_method"
                                            class="select-search form-control">
                                            <option value=""></option>
                                            <option {{ $item->valuation_method == 'FIFO' ? 'selected' : '' }} value="FIFO">FIFO</option>
                                            <option {{ $item->valuation_method == 'LIFO' ? 'selected' : '' }} value="LIFO">LIFO</option>
                                            <option {{ $item->valuation_method == 'Weighted Average' ? 'selected' : '' }} value="Weighted Average">Weighted Average</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>UOM: </label>
                                        <select data-placeholder="Choose..." name="uom_id" id="uom_id"
                                        class="select-search form-control">
                                        <option value=""></option>
                                        @foreach ($uom as $um)
                                            <option {{ $item->uom_id == $um->id ? 'selected' : '' }}
                                                value="{{ $um->id }}">{{ $um->name }}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Group: </label>
                                        <select data-placeholder="Choose..." name="item_category_id" id="item_category_id"
                                            class="select-search form-control">
                                            <option value=""></option>
                                            @foreach ($categories as $ct)
                                                <option {{ $item->item_category_id == $ct->id ? 'selected' : '' }}
                                                    value="{{ $ct->id }}">{{ $ct->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Category: </label>
                                        <select class="select form-control" id="new_category" name="new_category" required data-fouc
                                        data-placeholder="Choose..">

                                        <option value=""></option>
                                        <option {{ ($item->new_category  == 'Goods' ? 'selected' : '') }} value="Goods">Goods</option>
                                        <option {{ ($item->new_category  == 'Services' ? 'selected' : '') }} value="Services">Services</option>
                                    </select>
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
