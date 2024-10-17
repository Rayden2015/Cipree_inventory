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

        <div class="container">
            <div class="card-header">
                <h3 class="card-title" style="font-weight:bold">Properties</h3>
                <a href="{{ route('property.create') }}" class="btn btn-primary float-right">Add Property</a>
            </div>

            {{-- <a href="{{ route('property.create') }}" class="btn btn-outline-primary">Add New Post</a> --}}

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Location</th>
                        <th>Price</th>
                        <th>Added By</th>
                        {{-- <th>Description</th> --}}
                        <th>Cover</th>
                        <th>Update</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>


                    @foreach ($properties as $post)
                        <tr>
                            <th scope="row">{{ $post->id }}</th>
                            <td>{{ $post->location  ?? ''}}</td>
                            <td>{{ $post->price ?? '' }}</td>
                            <td>{{ $post->user->name ?? '' }}</td>
                            {{-- <td>{{ $post->body }}</td> --}}
                            <td><img src="cover/{{ $post->cover }}" class="img-responsive"
                                    style="max-height:100px; max-width:100px" alt="" srcset=""></td>
                            <td><a href="{{ route('property.edit', $post->id) }}" class="btn btn-outline-primary">Update</a>
                            </td>
                            <td>
                                <form action="{{ route('property.destroy', $post->id) }}" method="post">
                                    <button class="btn btn-outline-danger" onclick="return confirm('Are you sure?');"
                                        type="submit">Delete</button>
                                    @csrf
                                    @method('delete')
                                </form>
                            </td>

                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
        {{$properties->links('pagination::bootstrap-4')}}



    </body>

    </html>
@endsection
