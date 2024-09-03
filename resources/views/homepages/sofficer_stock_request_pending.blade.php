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

        <title>Stock Request Penidng</title>

    </head>

    <body>
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>

        </div>


        <div class="card">
            <div class="card-header">
                {{-- <h3 class="card-title">DataTable with default features</h3> --}}
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
                        <tr>
                            <th>ID</th>
                            <th>Request Number</th>
                            <th>Requested Date</th>
                            <th>Status</th>
                            <th>Enduser</th>
                            <th>Approval Status</th>
                            <th>View</th>
                            @if (Auth::user()->hasRole('store_officer'))
                                <th>Edit</th>
                            @endif

                        </tr>
                        </tr>
                    </thead>
                    @forelse ($sofficer_stock_request_pending as $in)
                        <tbody>
                            <tr>
                                <td>{{ $in->id }}</td>

                                <td>{{ $in->request_number ?? '' }}</td>
                                <td>{{ date('d-m-Y (H:i)', strtotime($in->request_date ?? '')) }}</td>
                                <td>{{ $in->status ?? '' }}</td>
                                <td>{{ $in->enduser->asset_staff_id ?? '' }}</td>
                                <td>{{ $in->approval_status ?? 'Pending' }}</td>

                                <td><a href="{{ route('sorders.store_list_view', $in->id) }}"
                                        class="btn btn-primary">View</a></td>
                                @if (Auth::user()->hasRole('store_officer'))
                                    @if (empty($in->approval_status))
                                       <td>Pending</td>
                                    @elseif($in->approval_status == 'Approved')
                                        <td><a href="{{ route('stores.store_officer_edit', $in->id) }}"
                                                class="btn btn-secondary">Edit</a></td>
                                                @elseif($in->approval_status == 'Denied')   
                                                <td>Denied</td>       
                                    @endif
                                @endif

                            @empty
                            <tr>
                                <td class="text-center" colspan="12">Data Not Found!</td>
                            </tr>
                    @endforelse

                    </tr>
                    </tbody>

                </table>
            </div>
            {{ $sofficer_stock_request_pending->links('pagination::bootstrap-4') }}
            <!-- /.card-body -->
        </div>



    </body>
    <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
