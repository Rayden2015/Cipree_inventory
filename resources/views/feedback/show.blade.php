@extends('layouts.admin')
@section('content')
    @if (Auth::user()->hasRole('admin') ||
            Auth::user()->hasRole('store_officer') ||
            Auth::user()->hasRole('store_assistant'))
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

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">View Review </h3>
                    <a href="{{ route('reviews.index') }}" class="btn btn-primary float-right">Back</a>
                </div>

                <!-- /.card-header -->

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-11">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label> Type: <span class="text-danger"></span></label>
                                    <input value="{{ $feedback1->type }}" readonly type="text" name="type"
                                        class="form-control">
                                </div>
                            </div>
                            <br>
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label> Logged By: <span class="text-danger"></span></label>
                                    <input value="{{ $user }}" readonly type="text" name=""
                                        class="form-control">
                                </div>
                            </div>
                            <br>
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label> Message: <span class="text-danger"></span></label>
                                    <textarea class="form-control" name="" value="{{ $feedback1->message }}" id="" readonly cols="10"
                                        rows="4" style="text-align:left;">
                                    {{ $feedback1->message }}  </textarea>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label> User Info: <span class="text-danger"></span></label>
                                    <textarea class="form-control" name="" id="" readonly cols="10" rows="5"
                                        style="align-content:left;">
                                    {{-- {{serialize($feedback->user_info)}} --}}
                                    @foreach ($feedback as $d)
{{ $d }}
@endforeach
                                
                                  </textarea>
                                </div>
                            </div>
                            <form action="{{ route('reviews.markAsReviewed', $feedback[0]) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success">Mark as resolved</button>
                            </form>

                        </div>
                    </div>
                </div>

                <!-- /.card-body -->
            </div>

        </body>
        <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
        <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
        {!! Toastr::message() !!}

        </html>
    @endif
@endsection
