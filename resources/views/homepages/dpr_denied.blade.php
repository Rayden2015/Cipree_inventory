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
            <!-- /.card-header -->

            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Supplier</th>
                            <th data-priority="1">Request Number</th>
                           
                            <th>Date</th>

                            <th>Status</th>

                            {{-- <th>Taken At</th> --}}
                            <th>View Details</th>
                        


                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($dpr_denied as $rq)
                            {{-- expr --}}

                            <tr>
                                <td>{{ $rq->id }}</td>
                                <td>{{ $rq->supplier->name ?? '' }}</td>
                                <td>{{ $rq->request_number  ?? ''}}</td>
                                <td>{{ date('d-m-Y (H:i)', strtotime($rq->created_at))}}</td>
                                <td>
                                <label for="" style="color:red; font-weight:bold;">{{ $rq->approval_status ?? 'Pending' }}</label>
                                </td>
                                <td>
                                    <a href="{{ route('orders.show', $rq->id) }}" class="btn btn-secondary">View</a>

                                </td>
                             
                             

                              

                            @empty
                            <tr>
                                <td class="text-center" colspan="12">Data Not Found!</td>
                            </tr>
                        @endforelse


                    </tbody>
                </table>
            </div>
            {{ $dpr_denied->links('pagination::bootstrap-4') }}
            <!-- /.card-body -->
        </div>



    </body>
    <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
