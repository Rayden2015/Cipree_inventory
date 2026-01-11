@extends('layouts.admin')
@section('content')
    
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>Manage Users - {{ $tenant->name }}</title>
            <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
        </head>
    

        <body>
            <div class="title d-flex justify-content-between">
                <h3 class="page-title">Users for {{ $tenant->name }}</h3>
                <p>
                    <a href="{{ route('tenant-admin.users.create') }}" class="btn btn-primary mr-3 my-3">Create New User</a>
                </p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Users</h3>
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
                                <th>Email</th>
                                <th>Site</th>
                                <th>Roles</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        @forelse ($users as $user)
                            <tbody>
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->name ?? '' }}</td>
                                    <td>{{ $user->email ?? '' }}</td>
                                    <td>{{ $user->site->name ?? 'Tenant Admin' }}</td>
                                    <td>
                                        @forelse($user->getRoleNames() as $role)
                                            <span class="badge bg-primary">{{ $role }}</span>
                                        @empty
                                            <span class="text-muted">No roles</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $user->status === 'Active' ? 'success' : 'danger' }}">
                                            {{ $user->status }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at->format('Y-m-d') ?? '' }}</td>
                                    <td>
                                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-info btn-sm">View</a>
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-success btn-sm">Edit</a>
                                    </td>
                                </tr>
                            @empty
                            <tr>
                                <td class="text-center" colspan="8">No Users Found! <a href="{{ route('tenant-admin.users.create') }}">Create your first user</a></td>
                            </tr>
                        @endforelse

                        </tbody>

                    </table>
                </div>
                {{ $users->links('pagination::bootstrap-4') }}
                <!-- /.card-body -->
            </div>

        </body>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
        {!! Toastr::message() !!}

        </html>
  
@endsection
