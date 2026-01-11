@extends('layouts.admin')
@section('content')
    
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>Tenants List</title>
            <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
                integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA=="
                crossorigin="anonymous" />
        </head>
    

        <body>
            <div class="title d-flex justify-content-between">
                <h3 class="page-title">Tenant Management</h3>
                <p>
                    <a href="{{ route('tenants.create') }}" class="btn btn-primary mr-3 my-3">Create New Tenant</a>
                </p>
            </div>


            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Tenants</h3>
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
                                <th>Slug</th>
                                <th>Domain</th>
                                <th>Status</th>
                                <th>Sites</th>
                                <th>Users</th>
                                <th>Contact</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tenants as $tenant)
                                <tr>
                                    <td>{{ ($tenants->currentPage() - 1) * $tenants->perPage() + $loop->iteration }}</td>
                                    <td>{{ $tenant->name ?? '' }}</td>
                                    <td>{{ $tenant->slug ?? '' }}</td>
                                    <td>{{ $tenant->domain ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $tenant->status === 'Active' ? 'success' : ($tenant->status === 'Inactive' ? 'warning' : 'danger') }}">
                                            {{ $tenant->status }}
                                        </span>
                                    </td>
                                    <td>{{ $tenant->sites->count() ?? 0 }}</td>
                                    <td>{{ $tenant->users->count() ?? 0 }}</td>
                                    <td>
                                        {{ $tenant->contact_name ?? 'N/A' }}<br>
                                        <small>{{ $tenant->contact_email ?? '' }}</small>
                                    </td>
                                    <td>{{ $tenant->created_at ? $tenant->created_at->format('Y-m-d') : 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('tenants.show', $tenant->id) }}" class="btn btn-info btn-sm">View</a>
                                        <a href="{{ route('tenants.edit', $tenant->id) }}" class="btn btn-success btn-sm">Edit</a>
                                        <form action="{{ route('tenants.destroy', $tenant->id) }}" method="post" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure you want to delete this tenant? This action cannot be undone.')"
                                                class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="10">No Tenants Found!</td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
                {{ $tenants->links('pagination::bootstrap-4') }}
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

        </html>
  
@endsection
