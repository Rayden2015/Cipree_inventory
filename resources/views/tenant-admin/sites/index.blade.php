@extends('layouts.admin')

@php
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-sitemap"></i> Manage Sites</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tenant-admin.dashboard') }}">Tenant Admin</a></li>
                    <li class="breadcrumb-item active">Sites</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Tenant Info Card -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card card-info card-outline">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">Tenant: <strong>{{ $tenant->name }}</strong></h5>
                                <p class="text-muted mb-0">Manage sites for your tenant</p>
                            </div>
                            <div class="text-right">
                                <div class="badge badge-primary badge-lg">{{ $stats['total'] }} Sites</div>
                                <div class="badge badge-info badge-lg mt-1">{{ $stats['total_users'] }} Users</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list mr-1"></i> All Sites</h3>
                <div class="card-tools">
                    <a href="{{ route('tenant-admin.sites.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus-circle mr-1"></i> Create New Site
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Search -->
                <form method="GET" action="{{ route('tenant-admin.sites.index') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="search">Search</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       placeholder="Search by name or site code..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search mr-1"></i> Search
                                    </button>
                                    <a href="{{ route('tenant-admin.sites.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-redo mr-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Sites Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Site Name</th>
                                <th>Site Code</th>
                                <th class="text-center">Users</th>
                                <th>Created</th>
                                <th style="width: 120px;" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sites as $site)
                                <tr>
                                    <td>{{ ($sites->currentPage() - 1) * $sites->perPage() + $loop->iteration }}</td>
                                    <td><strong>{{ $site->name }}</strong></td>
                                    <td>
                                        @if($site->site_code)
                                            <code>{{ $site->site_code }}</code>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-primary badge-lg">{{ $site->users_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <small>{{ $site->created_at->format('M d, Y') }}</small>
                                        <br><small class="text-muted">{{ $site->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('sites.edit', $site->id) }}" 
                                               class="btn btn-success btn-sm" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-sitemap fa-3x text-muted mb-3"></i>
                                            <h5>No Sites Found</h5>
                                            <p class="text-muted">
                                                @if(request('search'))
                                                    No sites match your search criteria. 
                                                    <a href="{{ route('tenant-admin.sites.index') }}">Clear search</a> to see all sites.
                                                @else
                                                    Get started by creating your first site.
                                                @endif
                                            </p>
                                            @if(!request('search'))
                                                <a href="{{ route('tenant-admin.sites.create') }}" class="btn btn-primary mt-2">
                                                    <i class="fas fa-plus-circle mr-1"></i> Create New Site
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
                @if($sites->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing {{ $sites->firstItem() }} to {{ $sites->lastItem() }} of {{ $sites->total() }} sites
                        </div>
                        <div>
                            {{ $sites->links('pagination::bootstrap-4') }}
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
