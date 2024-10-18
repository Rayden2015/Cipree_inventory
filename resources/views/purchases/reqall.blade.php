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

        <title>All Requested Purchases</title>

    </head>

    <body>
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>

        </div>


        <div class="card">
            <div class="card-header">
                <a href="{{ route('orders.create') }}" class="btn btn-primary float-right">Add New</a>
                {{-- <h3 class="card-title">DataTable with default features</h3> --}}
            </div>
            <!-- /.card-header -->

            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
    
                            <th>Supplier</th>
                            <th>Request Number</th>
                            <th>Requested Date</th>
                            <th>Status</th>
                             <th>View</th>
                             {{-- @if(Auth::user()->role->name == 'purchasing_officer'|| Auth::user()->role->name == 'admin') --}}
                             @hasanyrole('purchasing_officer|admin')
                                 <th>Edit</th>
                             @endhasanyrole
                             <th>Delete</th>
                        </tr>
                    </thead>
                    @forelse ($req_all as $in)
                        <tbody>
                            <tr>
                                <td>{{ $in->id ?? ''}}</td>
                                <td>{{ $in->supplier->name ?? ''}}</td>
                                  <td>{{ $in->request_number ?? ''}}</td>
                                <td>{{ date('d-m-Y (H:i)', strtotime($in->request_date))}}</td>
                                <td>{{ $in->status ?? ''}}</td>
                                <td>
                                    <a href="{{ route('orders.show', $in->id) }}" class="btn btn-secondary">View</a>

                                </td>
                                @hasanyrole('purchasing_officer|admin')
                                <td>
                                    <a href="{{ route('orders.edit', $in->id) }}" class="btn btn-success">Edit</a>

                                </td>
                                @endhasanyrole

                                <td>

                                    <form action="{{ route('orders.destroy', $in->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are you sure?')"
                                            class="btn btn-danger">Delete</button>
                                    </form>
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
            {{ $req_all->links('pagination::bootstrap-4') }}
            <!-- /.card-body -->
            
        </div>



    </body>
    <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
