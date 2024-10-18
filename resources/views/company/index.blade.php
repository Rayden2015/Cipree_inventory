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

        <title>Company</title>
       
    </head>

    <body>
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>
            {{-- <p>
                <a href="{{ route('company.create') }}" class="btn btn-primary mr-3 my-3">Add New</a>
            </p> --}}
        </div>
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



        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Company Info</h3>
            </div>
            <!-- /.card-header -->

            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Phone</th>
                            <th>VAT No</th>
                            <th>Email</th>
                            <th>Website</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    @forelse ($company as $cp)
                        <tbody>
 <tr>
                                <td>{{ $cp->id }}</td>
                                <td>{{ $cp->name }}</td>
                                <td>{{ $cp->address }}</td>
                                <td>{{ $cp->phone }}</td>
                                <td>{{ $cp->vat_no }}</td>
                                <td>{{ $cp->email }}</td>
                                <td>{{ $cp->website }}</td>
                                <td>
                                    <a href="{{route('company.edit',$cp->id)}}" class ="btn btn-success">Edit</a>

                                </td>
                                <td>

                                    <form action="{{ route('company.destroy', $cp->id) }}"
                                        method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are you sure?')"
                                            class="btn btn-danger">Delete</button>
                                    </form></td>

                            @empty
                            <tr>
                                <td class="text-center" colspan="12">Data Not Found!</td>
                            </tr>
                    @endforelse

                    </tr>
                    </tbody>

                </table>
            </div>
            <!-- /.card-body -->
        </div>


    </body>
    <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
