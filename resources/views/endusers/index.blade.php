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
                <a href="{{ route('endusers.create') }}" class="btn btn-primary mr-3 my-3">Add</a>
            </p>
        </div>


        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Endusers List</h3> <br>
                <form action="{{ route('endusers.search') }}" method="get" class="form">
                    {{-- @csrf --}}
                    <div class="input-group">
                        <input type="text" name="query" placeholder="Search..." class="form-control"
                            style="width:50%;">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('endusers.index') }}" class="btn btn-danger">Reset</a>
                        </div>
                    </div>
                </form>
                <br>
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


                <div style="float:right">
                    <form action="{{ route('endusersort') }}" method="get">
                        <div class="input-group">
                            <select id="enduser_category_id" class="form-control" name="enduser_category_id">
                                <option value="" selected hidden>Please Select</option>
                                <option value="all">All</option> <!-- Add this line -->
                                @foreach ($endusercategories as $ect)
                                    <option value="{{ $ect }}">{{ $ect }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary">Sort</button> <!-- Move this line -->
                        </div>
                    </form>

                </div>



            </div>
            <!-- /.card-header -->

            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Asset/StaffID</th>
                            <th>Description</th>
                            <th>Depart.</th>
                            <th>Section</th>
                            <th>Model</th>
                            <th>Serial No.</th>
                            <th>Category</th>
                            <th>Manuf.</th>
                            <th>Designation</th>
                            <th>Edit</th>
                            <th>View</th>
                            @can('delete-enduser')
                                <th>Delete</th>
                            @endcan


                        </tr>
                    </thead>
                    @forelse ($endusers as $ed)
                        <tbody>
                            <tr>
                                <td>{{ $ed->id }}</td>
                                <td>{{ $ed->asset_staff_id ?? '' }}</td>
                                <td>{{ $ed->name_description ?? '' }}</td>
                                <td>{{ $ed->departmente->name ?? 'Not Set' }}</td>
                                <td>{{ $ed->sectione->name ?? 'Not Set' }}</td>
                                <td>{{ $ed->model ?? '' }}</td>
                                <td>{{ $ed->serial_number ?? '' }}</td>
                                <td>{{ $ed->type ?? '' }}</td>
                                <td>{{ $ed->manufacturer ?? '' }}</td>
                                <td>{{ $ed->designation ?? '' }}</td>

                                @can('edit-enduser')
                                    <td>
                                        <a href="{{ route('endusers.edit', $ed->id) }}" class="btn btn-success">Edit</a>

                                    </td>
                                @endcan
                                <td>
                                    <a href="{{ route('endusers.show', $ed->id) }}" class="btn btn-secondary">Show</a>

                                </td>
                                @can('delete-enduser')
                                    <td>

                                        <form action="{{ route('endusers.destroy', $ed->id) }}" method="post">
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
                {{ $endusers->links('pagination::bootstrap-4') }}
            </div>
            <!-- /.card-body -->
        </div>


    </body>
    <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
