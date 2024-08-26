@extends('layouts.admin')
@section('content')
   
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>Document</title>
            <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
                integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA=="
                crossorigin="anonymous" />
        </head>

        <body>
            <div class="title d-flex justify-content-between">
                <h3 class="page-title"></h3>
                <p>
                    <a href="{{ route('categories.create') }}" class="btn btn-primary mr-3 my-3">Add</a>
                </p>
            </div>


            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Item Group</h3>
                </div>
                <!-- /.card-header -->

                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>

                                <th>Edit</th>
                                {{-- @if (Auth::user()->role->name == 'admin') --}}
                                    <th>Delete</th>
                                {{-- @endif --}}

                            </tr>
                        </thead>
                        @forelse ($categories as $ct)
                            <tbody>
                                <tr>
                                    <td>{{ $ct->id }}</td>
                                    <td>{{ $ct->name }}</td>
                                    <td>
                                        <a href="{{ route('categories.edit', $ct->id) }}" class="btn btn-success">Edit</a>

                                    </td>
                                    {{-- @if (Auth::user()->role->name == 'admin') --}}
                                        <td>
                                            <form action="{{ route('categories.destroy', $ct->id) }}" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Are you sure?')"
                                                    class="btn btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    {{-- @endif --}}

                                @empty
                                <tr>
                                    <td class="text-center" colspan="12">Data Not Found!</td>
                                </tr>
                        @endforelse

                        </tr>
                        </tbody>

                    </table>
                </div>
                {{ $categories->links('pagination::bootstrap-4') }}
                <!-- /.card-body -->
            </div>




        </body>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
        integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
        <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
        {!! Toastr::message() !!}

    {{-- @endif --}}
@endsection
