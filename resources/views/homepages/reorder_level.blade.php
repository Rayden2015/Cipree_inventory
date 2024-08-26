@extends('layouts.admin')
@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
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
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>

        </div>


        <div class="card">
            <div class="card-header">
                {{-- <h3 class="card-title">DataTable with default features</h3> --}}
            </div>
              <h2>Add Items to Replenish Inventory  <p style="float:right;"><i class="fa fa-shopping-cart"
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
            <!-- Add this code wherever you want to display the search bar -->
            <form action="{{ route('reorder_level_search') }}" method="GET">
                <div class="input-group mb-3">
                    <input type="text" name="search" class="form-control"
                        placeholder="Search by description, part number, or stock code" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a href="{{ route('dashboard.reorder_level') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>

            <!-- /.card-header -->

            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th> Description</th>
                            <th>Part Number</th>
                            <th>Stock Code</th>
                            <th>Stock Quantity</th>
                            <th>Reorder Level</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    @forelse ($reorder_level as $rq)
                        <tbody>
                            <tr>
                                <td>{{ $rq->id }}</td>
                                <td>{{ $rq->item->item_description ?? '' }}</td>
                                <td>{{ $rq->item->item_part_number ?? '' }}</td>
                                <td>{{ $rq->item->item_stock_code ?? '' }}</td>
                                <td>{{ $rq->item->stock_quantity ?? '' }}</td>
                                <td>{{ $rq->item->reorder_level ?? '' }}</td>
                                {{-- <td> <a href="{{ route('dashboard.low_stock_view', $rq->id) }}"
                                        class="btn btn-primary">View</a> </td> --}}

                                          <td> <a href="{{ route('addToStock', $rq->id) }}"
                                                                            class="btn btn-primary">Add</a></td>
                            @empty
                            <tr>
                                <td class="text-center" colspan="12">Data Not Found!</td>
                            </tr>
                    @endforelse

                    </tr>
                    </tbody>

                </table>
            </div>
            <tr>
                <td>Count:</td>
                <td>{{ $reorder_level->pluck('id')->count() }}</td>
            </tr>
            {{-- {{ $reorder_level->links('pagination::bootstrap-4') }} --}}
            <!-- /.card-body -->
        </div>



    </body>
    <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
