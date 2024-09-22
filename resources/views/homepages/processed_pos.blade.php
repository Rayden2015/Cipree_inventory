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

        <title>Processed POs</title>

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
                            <th>ID</th>
                            <th>POD</th>
                           <th>Supplier</th>
                        
                           <th>Requested Date</th>
                           <th>Status</th>
                            <th>View</th>
                          
                                 @if (Auth::user()->hasRole('store_officer') || Auth::user()->hasRole('authoriser') || Auth::user()->hasRole('purchasing_officer'))
                            
                                <th>Edit</th>
                            @endif
                           
                            @if (Auth::user()->hasRole('purchasing_officer') || Auth::user()->hasRole('admin'))
                            <th>Delete</th>
                        @endif
                        <th>Export</th>

                        </tr>
                    </thead>
                    @forelse ($processed_pos as $rq)
                        <tbody>
                            <tr>
                                <td>{{ $rq->id }}</td>
                                <td>{{ $rq->purchasing_order_number  ?? ''}}</td>
                                <td>{{ $rq->supplier->name  ?? ''}}</td>
                               
                                <td>{{ date('d-m-Y (H:i)', strtotime($rq->request_date))}}</td>
                                <td>{{ $rq->status ?? '' }}</td>
                                <td>
                                    <a href="{{ route('purchases.showlist', $rq->id) }}" class="btn btn-secondary">View</a>
                                </td>
                                @if (Auth::user()->hasRole('store_officer') || Auth::user()->hasRole('authoriser') || Auth::user()->hasRole('purchasing_officer'))
                                <td>
                                    <a href="{{ route('purchases.editlist', $rq->id) }}" class="btn btn-success">Edit</a>

                                </td>
                                @endif

                               
                                @if (Auth::user()->hasRole('purchasing_officer') || Auth::user()->hasRole('admin'))
                                <td>

                                    <form action="{{ route('purchases.purchase_destroy', $rq->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are you sure?')"
                                            class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                                @endif
                                <td>
                                    <a href="{{ route('purchases.generatePurchaseOrderPDF', $rq->id) }}"
                                        class="btn btn-primary">Export</a>

                                </td>

                            @empty
                            <tr>
                                <td class="text-center" colspan="12">Data Not Found!</td>
                            </tr>
                    @endforelse

                    </tr>
                    </tbody>

                </table>
            </div>
            {{$processed_pos->links('pagination::bootstrap-4') }}
            <!-- /.card-body -->
        </div>



    </body>
    <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
