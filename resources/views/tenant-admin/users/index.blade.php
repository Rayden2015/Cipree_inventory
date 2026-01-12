@extends('layouts.admin')

@php
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-users"></i> Manage Users</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tenant-admin.dashboard') }}">Tenant Admin</a></li>
                    <li class="breadcrumb-item active">Users</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Tenant Info & Statistics -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $stats['total'] }}</h3>
                        <p>Total Users</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $stats['active'] }}</h3>
                        <p>Active Users</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $stats['inactive'] }}</h3>
                        <p>Inactive Users</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-times"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $tenant->name }}</h3>
                        <p>Tenant</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list mr-1"></i> All Users</h3>
                <div class="card-tools">
                    <a href="{{ route('tenant-admin.users.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus-circle mr-1"></i> Create New User
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters and Search -->
                <form method="GET" action="{{ route('tenant-admin.users.index') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search">Search</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       placeholder="Search by name or email..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="site_id">Site</label>
                                <select class="form-control" id="site_id" name="site_id">
                                    <option value="all" {{ request('site_id') === 'all' || !request('site_id') ? 'selected' : '' }}>All Sites</option>
                                    @foreach($sites as $site)
                                        <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>
                                            {{ $site->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>All Statuses</option>
                                    <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search mr-1"></i> Filter
                                    </button>
                                    <a href="{{ route('tenant-admin.users.index') }}" class="btn btn-secondary btn-block">
                                        <i class="fas fa-redo mr-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Users Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Site</th>
                                <th>Roles</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th style="width: 150px;" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                                    <td><strong>{{ $user->name }}</strong></td>
                                    <td>
                                        <a href="mailto:{{ $user->email }}">
                                            <i class="fas fa-envelope mr-1"></i>{{ $user->email }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($user->site)
                                            <span class="badge badge-info">{{ $user->site->name }}</span>
                                        @else
                                            <span class="badge badge-secondary">Tenant Admin</span>
                                        @endif
                                    </td>
                                    <td>
                                        @forelse($user->getRoleNames() as $role)
                                            <span class="badge badge-primary badge-sm">{{ $role }}</span>
                                        @empty
                                            <span class="text-muted">No roles</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        @if($user->status === 'Active')
                                            <span class="badge badge-success badge-lg">{{ $user->status }}</span>
                                        @else
                                            <span class="badge badge-danger badge-lg">{{ $user->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $user->created_at->format('M d, Y') }}</small>
                                        <br><small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('users.show', $user->id) }}" 
                                               class="btn btn-info btn-sm" 
                                               title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('users.edit', $user->id) }}" 
                                               class="btn btn-success btn-sm" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <h5>No Users Found</h5>
                                            <p class="text-muted">
                                                @if(request('search') || request('site_id') || request('status'))
                                                    No users match your search criteria. 
                                                    <a href="{{ route('tenant-admin.users.index') }}">Clear filters</a> to see all users.
                                                @else
                                                    Get started by creating your first user.
                                                @endif
                                            </p>
                                            @if(!request('search') && !request('site_id') && !request('status'))
                                                <a href="{{ route('tenant-admin.users.create') }}" class="btn btn-primary mt-2">
                                                    <i class="fas fa-plus-circle mr-1"></i> Create New User
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                        </div>
                        <div>
                            {{ $users->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    .badge-lg {
        font-size: 0.9em;
        padding: 0.35em 0.65em;
    }
    .badge-sm {
        font-size: 0.75em;
        padding: 0.25em 0.5em;
    }
    .empty-state {
        padding: 2rem;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    .thead-light {
        background-color: #f8f9fa;
    }
</style>
@endpush

@endsection
