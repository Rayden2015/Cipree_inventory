@extends('layouts.admin')
@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    </head>

    <body>
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>
            <p>
                <a href="{{ route('blog.create') }}" class="btn btn-primary mr-3 my-3">Add News</a>
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
                                <th>Headline</th>
                                <th>Image</th>

                                <th>Edit</th>
                                <th>Delete</th>

                            </tr>
                        </thead>
                        @forelse ($allblogs as $sp)
                            <tbody>



                                <tr>

                                    <td>{{ $sp->id }}</td>
                                    <td>{{ $sp->headline }}</td>
                                    <td><img class="rounded-circle" style="height: 80px; width: 70px;" src="{{asset('images/blog/'. $sp->image) }}" alt="photo"></td>
                                    {{-- <td> <img src="{{ asset('images/' . $sp->image) }}" alt=""></td> --}}
                                    <td>
                                        <a href="{{route('blog.edit',$sp->id)}}" class ="btn btn-success">Edit</a>

                                    </td>
                                    <td>

                                        <form action="{{ route('blog.destroy', $sp->id) }}"
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
    <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
