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

        <title>Processed Requests</title>

    </head>

    <body>
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>

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



        <div class="card">
            <div class="card-header">
                {{-- <h3 class="card-title">DataTable with default features</h3> --}}
            </div>
            <!-- /.card-header -->

            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th data-priority="1">Request Number</th>
                            <th>Request By</th>
                            <th>Type of Purchase</th>

                            <th>Status</th>

                            {{-- <th>Taken At</th> --}}
                            <th>View Details</th>
                            <th>Edit</th>
                            {{-- <th>Pay</th> --}}


                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($processed_request as $order)
                            {{-- expr --}}

                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->request_number ?? 'not set' }}</td>
                                <td>{{ $order->request_by->name ?? '' }}</td>
                                <td>{{ $order->type_of_purchase ?? 'not set ' }}</td>

                                <td>{{ $order->status ?? '' }}</td>
                                <td><a href="{{ route('sorders.store_list_view', $order->id) }}"
                                        class="btn btn-primary">View</a></td>

                                        <td><a href="{{ route('stores.store_officer_edit',$order->id) }}"
                                            class="btn btn-secondary">Edit</a></td>
    

                            </tr>
                        @endforeach


                    </tbody>
                </table>
            </div>
            {{ $processed_request->links('pagination::bootstrap-4') }}
            <!-- /.card-body -->
        </div>



    </body>
    <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
