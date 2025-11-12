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

        <title>Stock Lists</title>

    </head>

    <body>
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>

        </div>


        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lists </h3>
                <form action="{{ route('stores.store_officer_list_search') }}" method="GET">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Search Requester"
                            aria-describedby="basic-addon2" name="search">
                        <div class="input-group-append">
                            <button class="btn btn-secondary" type="submit">Search</button>
                           <a class="btn btn-primary" href="{{ route('stores.store_officer_lists') }}">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
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

        @if (!empty($missingSite) && $missingSite)
            <div class="alert alert-warning m-3">
                Your account is not linked to a site. Showing all approved stock requests.
            </div>
        @endif

            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th data-priority="1">Request Number</th>
                            <th>Requester</th>
                            <th>Approval Status</th>
                            <th>Supply Status</th>
                            <th>Enduser</th>
                            <th>View Details</th>
                            {{-- <th>Edit</th> --}}
                            {{-- <th>Pay</th> --}}
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($officer_lists as $order)
                            {{-- expr --}}
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->request_number ?? 'not set' }}</td>
                                <td>{{ $order->request_by->name ?? '' }}</td>
                                <td>{{ $order->approval_status ?? 'not set ' }}</td>

                                <td>{{ $order->status ?? '' }}</td>
                                <td>{{ $order->enduser->asset_staff_id ?? ''}}</td>
                                <td><a href="{{ route('sorders.store_list_view', $order->id) }}"
                                        class="btn btn-primary">View</a></td>

                                      
    

                            </tr>
                        @endforeach


                    </tbody>
                </table>
                {{$officer_lists->links('pagination::bootstrap-4')}}
            </div>
            <!-- /.card-body -->
        </div>



    </body>
    <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
