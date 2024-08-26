@extends('layouts.admin')

@section('content')
@if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('purchasing_officer'))
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
            @if (Auth::user()->hasRole('purchasing_officer'))
                <p>
                    <a href="{{ route('total_taxes.create') }}" class="btn btn-primary mr-3 my-3">Add</a>
                </p>
            @endif
        </div>


        <div class="card">
            

            <!-- /.card-header -->

            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                           <th>Modified By</th>
                            <th>Last Updated</th>


                            <th>View</th>
                           
                            @if (Auth::user()->hasRole('purchasing_officer'))
                                <th>Edit</th>
                                <th>Delete</th>
                            @endif
                        </tr>
                    </thead>
                    @forelse ($total_taxes as $in)
                        <tbody>
                            <tr>
                                <td>{{ $in->id }}</td>
                              <td>{{ $in->editedby->name ?? '' }}</td>

                                <td>{{ date('d-m-Y (H:i)', strtotime($in->updated_at)) }}</td>
                                {{-- <td>{{ $in->date ?? '' }}</td>                              --}}
                                <td>
                                    <a href="{{ route('total_taxes.show', $in->id) }}" class="btn btn-primary">View</a>

                                </td>
                               
                                @if (Auth::user()->hasRole('purchasing_officer'))
                                    <td>
                                        <a href="{{ route('total_taxes.edit', $in->id) }}" class="btn btn-success">Edit</a>

                                    </td>
                                    <td>

                                        <form action="{{ route('total_taxes.destroy', $in->id) }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure?')"
                                                class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                @endif

                            @empty
                            <tr>
                                <td class="text-center" colspan="12">Item not available!</td>
                            </tr>
                    @endforelse

                    </tr>
                    </tbody>

                </table>
            </div>
            {{ $total_taxes->links('pagination::bootstrap-4') }}
            <!-- /.card-body -->
        </div>




    </body>
    <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
    @endif
@endsection
