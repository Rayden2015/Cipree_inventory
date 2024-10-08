@extends('layouts.admin')
@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Item History</title>
        <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
            integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA=="
            crossorigin="anonymous" />
    </head>

    <body>
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>
            {{-- <p>
                    <a href="{{ route('categories.create') }}" class="btn btn-primary mr-3 my-3">Add</a>
                </p> --}}
        </div>


        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Items</h3>
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
                <form action="{{ route('product_history') }}" method="GET" class="form-inline w-100">
                    <div class="input-group w-100">
                        <input type="text" name="search" class="form-control" 
                            placeholder="Search by description, part number or stock code" value="{{ request()->input('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="{{ route('product_history') }}" class="btn btn-secondary ml-2">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
            

            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            {{-- <th>ID</th> --}}
                            <th>Description</th>
                            <th>Part Number</th>
                            <th>Stock Code</th>
                            <th>View</th>
                            {{-- @if (Auth::user()->role->name == 'admin') --}}
                            {{-- <th>Delete</th> --}}
                            {{-- @endif --}}

                        </tr>
                    </thead>
                    @forelse ($product_history   as $ct)
                        <tbody>
                            <tr>
                                {{-- <td>{{ $ct->id }}</td> --}}
                                <td>{{ $ct->item_description }}</td>
                                <td>{{ $ct->item_part_number }}</td>
                                <td>{{ $ct->item_stock_code }}</td>
                                {{-- <td>{{ $ct->item_description }}</td>
                                    <td>{{ $ct->item_description }}</td>
                                    <td>{{ $ct->item_description }}</td> --}}
                                <td>
                                    <a href="{{ route('product_history_show', $ct->id) }}" class="btn btn-success">Show</a>

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
            {{ $product_history->links('pagination::bootstrap-4') }}
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
