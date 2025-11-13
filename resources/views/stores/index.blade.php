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

            <title>Stock Requisitions</title>

        </head>

        <body>
            <div class="title d-flex justify-content-between">
                <h3 class="page-title"></h3>

            </div>


            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Store Requests</h3>
                    <form action="{{ route('sorders.store_lists') }}" method="GET">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" 
                                   placeholder="Search by Item Number, Request Reference Number, Work Order Number, or End User" 
                                   aria-describedby="basic-addon2" 
                                   name="search"
                                   value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-secondary" type="submit">Search</button>
                                <a href="{{ route('sorders.store_lists') }}" class="btn btn-primary ml-2">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.card-header -->
                @if (!empty($missingDepartment) && $missingDepartment)
                    <div class="alert alert-warning m-3">
                        Your account is not linked to a department. Showing all stock requests for {{ Auth::user()->site->name ?? 'this site' }}.
                    </div>
                @endif
                @if (!empty($missingSite) && $missingSite)
                    <div class="alert alert-warning m-3">
                        Your account is not linked to a site. Showing stock requests across all sites.
                    </div>
                @endif

                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Requested By</th>
                                <th>EndUser</th>
                                <th>SR Number</th>
                                <th>Work Order #</th>
                                <th>Requested Date</th>
                                <th>Status</th>
                                <th>Approval Status</th>
                                <th>View</th>
                                <th class="text-nowrap">Actions</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($store_requests as $rq)
                            <tr>
                                <td>{{ $rq->id }}</td>
                                <td>{{ $rq->request_by->name ?? '' }}</td>
                                <td>{{ $rq->enduser->asset_staff_id ?? 'Not Set' }}</td>
                                <td>{{ $rq->request_number ?? '' }}</td>
                                <td>{{ $rq->work_order_number ?? 'Not Set' }}</td>
                                <td>{{ date('d-m-Y (H:i)', strtotime($rq->request_date)) }}</td>
                                <td>{{ $rq->status ?? '' }}</td>
                                <td>{{ $rq->approval_status ?? 'Pending' }}</td>
                                <td>
                                    <a href="{{ route('sorders.store_list_view', $rq->id) }}"
                                        class="btn btn-secondary btn-sm">View</a>
                                </td>
                                <td class="text-nowrap">
                                    @if ($rq->status === 'Supplied')
                                        <span class="badge badge-success">Processed</span>
                                    @elseif ($rq->approval_status !== 'Approved' || $rq->depart_auth_approval_status !== 'Approved')
                                        <span class="badge badge-warning">Awaiting Approval</span>
                                    @else
                                        @if (Auth::user()->hasRole('store_officer') || Auth::user()->hasRole('store_assistant'))
                                            <a href="{{ route('sorders.store_list_view', $rq->id) }}#process" class="btn btn-outline-success btn-sm">Process</a>
                                        @else
                                            <a href="{{ route('sorders.store_list_edit', $rq->id) }}" class="btn btn-success btn-sm">Edit</a>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @hasanyrole('purchasing_officer|Admin')
                                        <form action="{{ route('purchases.purchase_destroy', $rq->id) }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure?')"
                                                class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    @else
                                        <span class="text-muted">--</span>
                                    @endhasanyrole
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="11">Data Not Found!</td>
                            </tr>
                        @endforelse
                        </tbody>

                    </table>
                </div>
                {{ $store_requests->links('pagination::bootstrap-4') }}
                <!-- /.card-body -->
            </div>



        </body>
        <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
        <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
        {!! Toastr::message() !!}

        </html>

@endsection
