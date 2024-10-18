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

            <title> List</title>

        </head>

        <body>
            <div class="title d-flex justify-content-between">
                <h3 class="page-title"></h3>

                <p>
                    <a href="{{ route('levies.create') }}" class="btn btn-primary mr-3 my-3">Add </a>
                </p>

            </div>


            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tax and Levies Lists</h3>
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
                                <th>Description</th>
                                <th>Rate</th>
                                <th>Edit</th>
                                <th>Delete</th>

                            </tr>
                        </thead>
                        @forelse ($levies as $tx)
                            <tbody>
                                <tr>

                                    <td>{{ $tx->id }}</td>
                                    <td>{{ $tx->description ?? ''}}</td>
                                    <td>{{ $tx->rate ?? '' }}</td>
                                   @can('edit-levy')
                                        <td>
                                            <a href="{{ route('levies.edit', $tx->id) }}" class ="btn btn-success">Edit</a>

                                        </td>
                                        @endcan
                                        @can('delete-levy') 
                                        <td>

                                            <form action="{{ route('levies.destroy', $tx->id) }}" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Are you sure?')"
                                                    class="btn btn-danger">Delete</button>
                                            </form>
                                        </td>
                                        @endcan
                                  

                                @empty
                                <tr>
                                    <td class="text-center" colspan="12">Data Not Found!</td>
                                </tr>
                        @endforelse

                        </tr>
                        </tbody>

                    </table>
                </div>
                {{ $levies->links('pagination::bootstrap-4') }}
                <!-- /.card-body -->
            </div>


        </body>
        <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
        <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
        {!! Toastr::message() !!}

        </html>
   
@endsection
