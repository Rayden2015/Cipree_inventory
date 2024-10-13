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

        <title>SPR Lists</title>

    </head>

    <body>
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>

        </div>


        <div class="card">
            <div class="card-header">
                {{-- <h3 class="card-title">DataTable with default features</h3> --}}
                {{-- <a href="{{ route('spr_create') }}" class="btn btn-primary float-right">Add New</a> --}}
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
            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th data-priority="1">Request Number</th>
                            <th>Requester</th>
                            <th>Date</th>
                            <th>Approval Status</th>
                            <th>Supply Status</th>
                            <th>Enduser</th>

                            {{-- <th>Taken At</th> --}}
                            <th>View Details</th>
                           {{-- @if(Auth::user()->role->name == 'Super Authoriser') --}}
                           @if(Auth::user()->hasRole('store_officer'))
                                <th>Edit</th>
                            @endif
                            <th>Delete</th>


                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($spr_lists as $order)
                         
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->request_number ?? '' }}</td>
                                <td>{{ $order->request_by->name ?? '' }}</td>
                                <td>{{ date('d-m-Y (H:i)', strtotime($order->request_date))}}</td>
                                <td>{{ $order->approval_status ?? 'Pending' }}</td>
                                <td>{{ $order->status ?? '' }}</td>
                                <td>{{ $order->enduser->asset_staff_id ?? '' }}</td>
                                
                                {{-- @if($order->approval_status != 'Approved' || $order->approval_status != 'Denied' && $order->status != 'Supplied')
                                <td><a class="btn btn-secondary" href="{{ route('stores.requester_edit', $order->id) }}">Edit</a></td>
                                @else
                                <td>N/A </td>
                                @endif --}}
                                <td><a href="{{ route('auth_spr_list_view', $order->id) }}"
                                        class="btn btn-primary">View</a></td>
                             
                              
                                        @if(Auth::user()->hasRole('store_officer'))
                                    <td><a href="{{ route('auth_spr_list_edit', $order->id) }}"
                                            class="btn btn-secondary">Edit</a></td>
                                            @endif
                              
                                @if ($order->status == 'Supplied')
                                    
                                    <td>Items Already Supplied</td>

                                    
                                @else
                                    
                                   
                                            <td>
                                                <form action="" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Are you sure?')"
                                                        class="btn btn-danger">Delete</button>
                                                </form>
                                            </td>
                                    
                                @endif



                            </tr>
                        @endforeach


                    </tbody>
                </table>
                {!! $spr_lists->links() !!}
            </div>
            <!-- /.card-body -->
        </div>



    </body>
    <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
