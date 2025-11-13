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

                                {{-- @if (Auth::user()->role->name == 'purchasing_officer' ||
                                        Auth::user()->role->name == 'Super Authoriser' ||
                                        Auth::user()->role->name == 'store_officer') --}}
                                        @hasanyrole('purchasing_officer|Super Authoriser|store_officer')
                                    <th>Edit</th>
                                @endhasanyrole
                                @if (Auth::user()->hasRole('purchasing_officer'))
                                    <th>Delete</th>
                                @endif
                                {{-- <th>Export</th> --}}

                            </tr>
                        </thead>
                        @forelse ($store_requests as $rq)
                            <tbody>
                                <tr>
                                    <td>{{ $rq->id }}</td>
                                    <td>{{ $rq->request_by->name ?? '' }}</td>
                                    <td>{{ $rq->enduser->asset_staff_id ?? 'Not Set' }}</td>
                                    <td>{{ $rq->request_number ?? '' }}</td>
                                    <td>{{ $rq->work_order_number ?? 'Not Set' }}</td>


                                    <td>{{ date('d-m-Y (H:i)', strtotime($rq->request_date)) }}</td>
                                    <td>{{ $rq->status ?? '' }}</td>
                                    <td>
                                        {{-- @if ($rq->approval_status == '')
                                            <a href="{{ route('stores.approved_status', $rq->id) }}"
                                                class="btn btn-success">Approve</a>
                                        @elseif ($rq->approval_status == 'Approved')
                                            <a href="{{ route('stores.denied_status', $rq->id) }}"
                                                class="btn btn-danger">Deny</a>
                                        @elseif ($rq->approval_status == 'Denied')
                                            <a href="{{ route('stores.approved_status', $rq->id) }}"
                                                class="btn btn-success">Approve</a>
                                        @else
                                            <a href="{{ route('stores.denied_status', $rq->id) }}"
                                                class="btn btn-danger">Deny</a>
                                        @endif --}}
                                        {{ $rq->approval_status ?? 'Pending' }}
                                    </td>
                                    <td>
                                        <a href="{{ route('sorders.store_list_view', $rq->id) }}"
                                            class="btn btn-secondary">View</a>

                                    </td>

{{-- 
                                    @if (Auth::user()->role->name == 'purchasing_officer' ||
                                            Auth::user()->role->name == 'admin' ||
                                            Auth::user()->role->name == 'store_officer' ||
                                            Auth::user()->role->name == 'Super Authoriser') --}}
                                            @hasanyrole('purchasing_officer|Super Authoriser|store_officer')
                                        <td>
                                            @if($rq->status != 'Supplied')
                                                
                                           
                                            <a href="{{ route('sorders.store_list_edit', $rq->id) }}"
                                                class="btn btn-success">Edit</a>
                                                @else
                                                Transaction Completed
                                                @endif

                                        </td>
                                    @endhasanyrole


                                    {{-- @if (Auth::user()->role->name == 'purchasing_officer' || Auth::user()->role->name == 'admin') --}}
                                    @hasanyrole('purchasing_officer|Admin')
                                        <td>

                                            <form action="{{ route('purchases.purchase_destroy', $rq->id) }}"
                                                method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Are you sure?')"
                                                    class="btn btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    @endhasanyrole
                                    {{-- <td>
                                    <a href="{{ route('purchases.generatePurchaseOrderPDF', $rq->id) }}"
                                        class="btn btn-primary">Export</a>

                                </td> --}}

                                @empty
                                <tr>
                                    <td class="text-center" colspan="12">Data Not Found!</td>
                                </tr>
                        @endforelse

                        </tr>
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
