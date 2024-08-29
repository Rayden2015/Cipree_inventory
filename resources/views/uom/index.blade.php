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
                <a href="{{ route('uom.create') }}" class="btn btn-primary mr-3 my-3">Add </a>
            </p>

        </div>


        <div class="card">
            <div class="card-header">
                <h3 class="card-title">UoM  Lists</h3>
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
                            {{-- <th>Rate</th> --}}
                            @can('edit-uom')
                                <th>Edit</th>
                            @endcan
                            @can('delete-uom')
                                <th>Delete</th>
                            @endcan
                        </tr>
                    </thead>
                    @forelse ($uom as $um)
                        <tbody>
                            <tr>
                                <td>{{ $um->id }}</td>
                                <td>{{ $um->name ?? '' }}</td>
                                {{-- <td>{{ $um->rate ?? '' }}</td> --}}
                                @can('edit-uom')
                                    <td>
                                        <a href="{{ route('uom.edit', $um->id) }}" class ="btn btn-success">Edit</a>

                                    </td>
                                @endcan
                                @can('uom-tax')
                                    <td>

                                        <form action="{{ route('uom.destroy', $um->id) }}" method="post">
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
            {{ $uom->links('pagination::bootstrap-4') }}
            <!-- /.card-body -->
        </div>


    </body>
    <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
