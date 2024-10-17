@extends('layouts.admin')
@section('content')
    <!doctype html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel CRUD With Multiple Image Upload</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
        <!-- Font-awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    </head>

    <body>
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>
            <p>
                <a href="{{ route('users.create') }}" class="btn btn-primary mr-3 my-3">Add New</a>
            </p>
            
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
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Email</th>
                          
                            <th>Role</th>
                            <th>Edit</th>
                            <th>Delete</th>

                        </tr>
                    </thead>
                    @forelse ($users as $us)
                        <tbody>



                            <tr>

                                <td>{{ $us->id }}</td>
                                <td>{{ $us->name }}</td>
                                <td>{{ $us->phone }}</td>
                                <td>{{ $us->address }}</td>
                                <td>{{ $us->email }}</td>
                               
                                <td>{{ $us->role->name ?? 'not found' }}</td>
                                <td>
                                    <a href="{{ route('users.edit', $us->id) }}" class="btn btn-success">Edit</a>

                                </td>
                                <td>

                                    <form action="{{ route('users.destroy', $us->id) }}" method="post">
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
            <!-- /.card-body -->


        </div>
       

    </body>
    <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
