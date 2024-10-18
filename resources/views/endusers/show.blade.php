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

        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ asset('/assets/plugins/fontawesome-free/css/all.min.css') }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('/assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet"
            href="{{ asset('/assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('/assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{ asset('/assets/dist/css/adminlte.min.css') }}">

        <title>Endusers View</title>

        <style>
            .active{
                font-weight: bold;
            }
        </style>
    </head>

    <body>
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>
            <p>
                <a href="{{ route('endusers.index') }}" class="btn btn-primary mr-3 my-3">Back</a>
            </p>
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
            <div class="container">
                <p></p>
                <h4>Enduser Detail</h4>
                {{-- <p>Iki mung detail singkat wae soale seko jenenge wae wis short detail dadi yo ojo dowo-dowo.</p> --}}
                <table class="table table-th-block">
                    <tbody>
                        <tr><td class="active">Asset/StaffID:</td><td>{{ $enduser->asset_staff_id ?? ''}}</td></tr>
                        <tr><td class="active">Description:</td><td>{{ $enduser->name_description ?? ''}}</td></tr>
                        <tr><td class="active">Department:</td><td>{{ $enduser->departmente->name ?? 'Not Set' }}</td></tr>
                        <tr><td class="active">Section:</td><td>{{ $enduser->sectione->name ?? 'Not Set' }}</td></tr>
                        <tr><td class="active">Model:</td><td>{{ $enduser->model ?? '' }}</td></tr>
                        <tr><td class="active">Serial Number:</td><td>{{ $enduser->serial_number ?? '' }}</td></tr>
                        <tr><td class="active">Category:</td><td>{{ $enduser->type ?? '' }}</td></tr>
                        <tr><td class="active">Manufacturer:</td><td>{{ $enduser->manufacturer ?? '' }}</td></tr>
                        <tr><td class="active">Designation:</td><td>{{ $enduser->designation ?? '' }}</td></tr>
                        <tr><td class="active">Site:</td><td>{{ $enduser->site->name ?? 'Not Set' }}</td></tr>
                        <tr><td class="active">Status:</td><td>{{ $enduser->status ?? ''}}</td></tr>
        
                    </tbody>
                </table>
            </div>
           
        </div>
     

    </body>
    <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
