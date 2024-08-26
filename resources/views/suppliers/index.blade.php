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

        <title>Document</title>

    </head>

    <body>
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>
            <p>
                <a href="{{ route('suppliers.create') }}" class="btn btn-primary mr-3 my-3">Add </a>
            </p>

        </div>
        <form action="{{ route('supplier_search') }}" method="GET">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Search name or phone"
                    aria-describedby="basic-addon2" name="search">
                <div class="input-group-append">
                    <button class="btn btn-secondary" type="submit">Search</button>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Suppliers List</h3>
                
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
                            <th>Name</th>
                            <th>Email</th>
                            <th>Address</th>

                            <th>Location</th>
                            <th>Telephone</th>
                            <th>Phone</th>
                            @can('edit-supplier')
                            <th>Edit</th>
                            @endcan
                            @can('delete-supplier')
                            <th>Delete</th>
                            @endcan
                        </tr>
                    </thead>
                    @forelse ($suppliers as $sp)
                        <tbody>



                            <tr>

                                <td>{{ $sp->id }}</td>
                                <td>{{ $sp->name }}</td>
                                <td>{{ $sp->email }}</td>
                                <td>{{ $sp->address }}</td>
                             
                                <td>{{ $sp->location }}</td>
                                <td>{{ $sp->tel }}</td>

                                <td>{{ $sp->phone }}</td>
                                @can('edit-supplier')
                                <td>
                                    <a href="{{ route('suppliers.edit', $sp->id) }}" class="btn btn-success">Edit</a>

                                </td>
                                @endcan
                                @can('delete-supplier')
                                <td>

                                    <form action="{{ route('suppliers.destroy', $sp->id) }}" method="post">
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
            {{ $suppliers->links('pagination::bootstrap-4') }}
            <!-- /.card-body -->
        </div>





    </body>
    <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
