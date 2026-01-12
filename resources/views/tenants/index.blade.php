@extends('layouts.admin')

@php
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-building"></i> Tenant Management</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('super-admin.dashboard') }}">Super Admin</a></li>
                    <li class="breadcrumb-item active">Tenants</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $stats['total'] }}</h3>
                        <p>Total Tenants</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $stats['active'] }}</h3>
                        <p>Active Tenants</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $stats['inactive'] }}</h3>
                        <p>Inactive Tenants</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-pause-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $stats['suspended'] }}</h3>
                        <p>Suspended Tenants</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list mr-1"></i> All Tenants</h3>
                <div class="card-tools">
                    <a href="{{ route('tenants.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus-circle mr-1"></i> Create New Tenant
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters and Search -->
                <form method="GET" action="{{ route('tenants.index') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search">Search</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       placeholder="Search by name, slug, domain, contact..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>All Statuses</option>
                                    <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="Suspended" {{ request('status') === 'Suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search mr-1"></i> Filter
                                    </button>
                                    <a href="{{ route('tenants.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-redo mr-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Tenants Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Tenant Name</th>
                                <th>Slug</th>
                                <th>Domain</th>
                                <th>Status</th>
                                <th class="text-center">Sites</th>
                                <th class="text-center">Users</th>
                                <th>Contact</th>
                                <th>Created</th>
                                <th style="width: 180px;" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tenants as $tenant)
                                <tr>
                                    <td>{{ ($tenants->currentPage() - 1) * $tenants->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $tenant->name }}</strong>
                                        @if($tenant->description)
                                            <br><small class="text-muted">{{ Str::limit($tenant->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <code>{{ $tenant->slug ?? 'N/A' }}</code>
                                    </td>
                                    <td>
                                        @if($tenant->domain)
                                            <span class="badge badge-info">{{ $tenant->domain }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tenant->status === 'Active')
                                            <span class="badge badge-success badge-lg">{{ $tenant->status }}</span>
                                        @elseif($tenant->status === 'Inactive')
                                            <span class="badge badge-warning badge-lg">{{ $tenant->status }}</span>
                                        @else
                                            <span class="badge badge-danger badge-lg">{{ $tenant->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-primary badge-lg">{{ $tenant->sites_count ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info badge-lg">{{ $tenant->users_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        @if($tenant->contact_name || $tenant->contact_email)
                                            <strong>{{ $tenant->contact_name ?? 'N/A' }}</strong>
                                            @if($tenant->contact_email)
                                                <br><small class="text-muted"><i class="fas fa-envelope"></i> {{ $tenant->contact_email }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $tenant->created_at->format('M d, Y') }}</small>
                                        <br><small class="text-muted">{{ $tenant->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('tenants.show', $tenant->id) }}" 
                                               class="btn btn-info btn-sm" 
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('tenants.edit', $tenant->id) }}" 
                                               class="btn btn-success btn-sm" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('tenants.destroy', $tenant->id) }}" 
                                                  method="POST" 
                                                  style="display: inline-block;"
                                                  onsubmit="return confirm('Are you sure you want to delete this tenant? This action cannot be undone and will delete all associated data.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-danger btn-sm" 
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                                            <h5>No Tenants Found</h5>
                                            <p class="text-muted">
                                                @if(request('search') || request('status'))
                                                    No tenants match your search criteria. 
                                                    <a href="{{ route('tenants.index') }}">Clear filters</a> to see all tenants.
                                                @else
                                                    Get started by creating your first tenant.
                                                @endif
                                            </p>
                                            @if(!request('search') && !request('status'))
                                                <a href="{{ route('tenants.create') }}" class="btn btn-primary mt-2">
                                                    <i class="fas fa-plus-circle mr-1"></i> Create New Tenant
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
                @if($tenants->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing {{ $tenants->firstItem() }} to {{ $tenants->lastItem() }} of {{ $tenants->total() }} tenants
                        </div>
                        <div>
                            {{ $tenants->links('pagination::bootstrap-4') }}
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

