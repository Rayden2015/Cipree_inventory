@extends('layouts.admin')
@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Tax List</title>
        <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
            integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA=="
            crossorigin="anonymous" />
    </head>

    <body>
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>

            <p>
                <a href="{{ route('taxes.create') }}" class="btn btn-primary mr-3 my-3">Add </a>
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
                            @can('edit-tax')
                                <th>Edit</th>
                            @endcan
                            @can('delete-tax')
                                <th>Delete</th>
                            @endcan
                        </tr>
                    </thead>
                    @forelse ($taxes as $tx)
                        <tbody>
                            <tr>
                                <td>{{ $tx->id }}</td>
                                <td>{{ $tx->description ?? '' }}</td>
                                <td>{{ $tx->rate ?? '' }}</td>
                                @can('edit-tax')
                                    <td>
                                        <a href="{{ route('taxes.edit', $tx->id) }}" class ="btn btn-success">Edit</a>

                                    </td>
                                @endcan
                                @can('delete-tax')
                                    <td>

                                        <form action="{{ route('taxes.destroy', $tx->id) }}" method="post">
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
            {{ $taxes->links('pagination::bootstrap-4') }}
            <!-- /.card-body -->
        </div>


    </body>
    <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
