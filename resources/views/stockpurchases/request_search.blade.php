@extends('layouts.admin')

@section('content')
    @if (Auth::user()->hasRole('store_officer'))
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <link rel="stylesheet" href="https://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
            <link rel="stylesheet"
                href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

            <!-- Font Awesome -->
            <link rel="stylesheet" href="{{ asset('/assets/plugins/fontawesome-free/css/all.min.css') }}">
            <!-- DataTables -->
            <link rel="stylesheet" href="{{ asset('/assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
            <link rel="stylesheet"
                href="{{ asset('/assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
            <link rel="stylesheet" href="{{ asset('/assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
            <!-- Theme style -->
            <link rel="stylesheet" href="{{ asset('/assets/dist/css/adminlte.min.css') }}">

            <title>Document</title>

        </head>

        <body>
            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                        <div class="row">
                            <div class="col-12">
                                <div class="card-box">

                                    <h2>Add to Cart <p style="float:right;"><i class="fa fa-shopping-cart"
                                                aria-hidden="true"></i> <span class="badge badge-pill badge-danger">
                                                {{-- @if (count((array) session('cart')) == '0')
                                                    <a href="{{ route('spr_create') }}">
                                                        {{ count((array) session('cart')) }}
                                                    </a> --}}
                                                @if(session('cart') > '0')
                                                    <a href="{{ route('stock_purchase_cart') }}">
                                                        {{ count((array) session('cart')) }}
                                                    </a>
                                                @endif
                                            </span> </p>
                                    </h2>
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
                                    <form action="" method="GET">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control"
                                                placeholder="Enter Description or Part number or Stock Code"
                                                aria-describedby="basic-addon2" name="search">
                                            <div class="input-group-append">
                                                <button class="btn btn-secondary" type="submit">Search</button>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="responsive-table-plugin" style="padding-bottom: 15px;">
                                        @if (Session::has('success'))
                                            <div class="alert alert-success">
                                                <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">&times;</button>
                                                <strong>Success!</strong> {{ Session::get('success') }}
                                            </div>
                                        @endif

                                        <div class="table-rep-plugin">
                                            <div class="table-responsive" data-pattern="priority-columns">
                                                <table id="tech-companies-1" class="table table-striped mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th data-priority="1">Location</th>
                                                            <th>Description</th>
                                                            <th>Part Number</th>
                                                            <th>Stock Code</th>
                                                            <th>Stock Quantity</th>
                                                            {{-- <th>Enduser</th> --}}
                                                            <th>Age</th>
                                                            <th>Action</th>



                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if (isset($inventory))
                                                            @foreach ($inventory as $order)
                                                                {{-- expr --}}

                                                                <tr>
                                                                    <td>{{ $order->id }}</td>
                                                                    <td>{{ $order->location->name ?? 'not set' }}</td>
                                                                    <th>{{ $order->item_description ?? 'not set ' }}</th>
                                                                    <th>{{ $order->item_part_number ?? 'not set ' }}</th>
                                                                    <th>{{ $order->item_stock_code ?? 'not set ' }}</th>
                                                                    <td>{{ $order->quantity ?? '' }}</td>
                                                                    {{-- <td>{{ $order->asset_staff_id ?? '' }}</td> --}}
                                                                    <?php
                                                                    $date = \Carbon\Carbon::parse($order->created_at);
                                                                    $difference = $date->diffInDays(\Carbon\Carbon::now());
                                                                    ?>
                                                                    <td>{{ $difference }}</td>


                                                                    <td><a href="{{ route('addToStock', $order->id) }}"
                                                                            class="btn btn-primary">Add</a></td>
                                                                    <td class="text-center">

                                                                    </td>

                                                                    @hasrole('admin')
                                                                        <td>


                                                                        </td>

                                                                    @endrole

                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <th colspan="6">
                                                                    {{-- <h2 class="text-center">No data found</h2> --}}
                                                                </th>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>



                                        </div>

                                    </div>
                                    {{-- {{$orders->links()}} --}}
                                    {{-- {{$orders->links('pagination::bootstrap-4')}} --}}

                                </div>

                            </div>
                        </div>
                        <!-- end row -->

                    </div> <!-- container-fluid -->

                </div> <!-- content -->
            </div>
        </body>
        <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
        <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
        {!! Toastr::message() !!}

        </html>
    @endif
@endsection()
