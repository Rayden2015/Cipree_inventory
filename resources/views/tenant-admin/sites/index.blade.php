@extends('layouts.admin')
@section('content')
    
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>Manage Sites - {{ $tenant->name }}</title>
            <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
        </head>
    

        <body>
            <div class="title d-flex justify-content-between">
                <h3 class="page-title">Sites for {{ $tenant->name }}</h3>
                <p>
                    <a href="{{ route('tenant-admin.sites.create') }}" class="btn btn-primary mr-3 my-3">Create New Site</a>
                </p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Sites</h3>
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
                                <th>Name</th>
                                <th>Site Code</th>
                                <th>Users</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        @forelse ($sites as $site)
                            <tbody>
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $site->name ?? '' }}</td>
                                    <td>{{ $site->site_code ?? 'N/A' }}</td>
                                    <td>{{ $site->users->count() ?? 0 }}</td>
                                    <td>{{ $site->created_at->format('Y-m-d') ?? '' }}</td>
                                    <td>
                                        <a href="{{ route('sites.edit', $site->id) }}" class="btn btn-success btn-sm">Edit</a>
                                    </td>
                                </tr>
                            @empty
                            <tr>
                                <td class="text-center" colspan="6">No Sites Found! <a href="{{ route('tenant-admin.sites.create') }}">Create your first site</a></td>
                            </tr>
                        @endforelse

                        </tbody>

                    </table>
                </div>
                {{ $sites->links('pagination::bootstrap-4') }}
                <!-- /.card-body -->
            </div>

        </body>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
        {!! Toastr::message() !!}

        </html>
  
@endsection
