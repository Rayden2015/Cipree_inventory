@extends('layouts.admin')
@section('content')
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

        <title>Items List</title>

    </head>

    <body>
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>

        </div>


        <div class="card">
            <a href="{{ route('export.items_per_site') }}" class="btn btn-success">Export to Excel</a>

            {{-- <div class="card-header">
                    <h3 class="card-title">Items </h3>
                    <form action="{{ route('item_search') }}" method="GET">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control"
                                placeholder="Search Description, Part Number, Stock Code" aria-describedby="basic-addon2"
                                name="search">
                            <div class="input-group-append">
                                <button class="btn btn-secondary" type="submit">Search</button>
                            </div>
                        </div>
                    </form>
                </div> --}}
            <!-- /.card-header -->
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
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>

                            <th>Item ID</th>
                            <th>Item Description</th>
                            <th>Site Name</th>
                            <th>Total Received</th>
                            <th>Total Supplied</th>
                            <th>Updated Stock Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td>{{ $item->item_id }}</td>
                                <td>{{ $item->item_description }}</td>
                                <td>{{ $item->site_name }}</td>
                                <td>{{ $item->total_received }}</td>
                                <td>{{ $item->total_supplied }}</td>
                                <td>{{ $item->updated_stock_quantity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- {{ $items->links('pagination::bootstrap-4') }} --}}
            <!-- /.card-body -->
        </div>




    </body>
    <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
