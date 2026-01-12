@extends('layouts.admin')

@php
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-building"></i> Tenant Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('super-admin.dashboard') }}">Super Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tenants.index') }}">Tenants</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($tenant->name, 20) }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Header Actions -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-1">{{ $tenant->name }}</h3>
                                @if($tenant->description)
                                    <p class="text-muted mb-0">{{ $tenant->description }}</p>
                                @endif
                            </div>
                            <div>
                                @if($tenant->status === 'Active')
                                    <span class="badge badge-success badge-lg mr-2">{{ $tenant->status }}</span>
                                @elseif($tenant->status === 'Inactive')
                                    <span class="badge badge-warning badge-lg mr-2">{{ $tenant->status }}</span>
                                @else
                                    <span class="badge badge-danger badge-lg mr-2">{{ $tenant->status }}</span>
                                @endif
                                <a href="{{ route('tenants.edit', $tenant->id) }}" class="btn btn-success">
                                    <i class="fas fa-edit mr-1"></i> Edit Tenant
                                </a>
                                <a href="{{ route('tenants.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $tenant->sites->count() }}</h3>
                        <p>Sites</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-sitemap"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $tenant->users->count() }}</h3>
                        <p>Users</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $tenant->tenantAdmins->count() }}</h3>
                        <p>Tenant Admins</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $tenant->created_at->format('M Y') }}</h3>
                        <p>Created</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Tenant Information -->
            <div class="col-md-6">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle mr-1"></i> Tenant Information</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 40%;">Name:</th>
                                <td><strong>{{ $tenant->name }}</strong></td>
                            </tr>
                            <tr>
                                <th>Slug:</th>
                                <td><code>{{ $tenant->slug }}</code></td>
                            </tr>
                            <tr>
                                <th>Domain:</th>
                                <td>
                                    @if($tenant->domain)
                                        <span class="badge badge-info">{{ $tenant->domain }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if($tenant->status === 'Active')
                                        <span class="badge badge-success badge-lg">{{ $tenant->status }}</span>
                                    @elseif($tenant->status === 'Inactive')
                                        <span class="badge badge-warning badge-lg">{{ $tenant->status }}</span>
                                    @else
                                        <span class="badge badge-danger badge-lg">{{ $tenant->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            @if($tenant->description)
                            <tr>
                                <th>Description:</th>
                                <td>{{ $tenant->description }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Created At:</th>
                                <td>
                                    {{ $tenant->created_at->format('F d, Y \a\t g:i A') }}
                                    <br><small class="text-muted">{{ $tenant->created_at->diffForHumans() }}</small>
                                </td>
                            </tr>
                            <tr>
                                <th>Last Updated:</th>
                                <td>
                                    {{ $tenant->updated_at->format('F d, Y \a\t g:i A') }}
                                    <br><small class="text-muted">{{ $tenant->updated_at->diffForHumans() }}</small>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="col-md-6">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-address-book mr-1"></i> Contact Information</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 40%;">Contact Name:</th>
                                <td>{{ $tenant->contact_name ?? '<span class="text-muted">N/A</span>' }}</td>
                            </tr>
                            <tr>
                                <th>Contact Email:</th>
                                <td>
                                    @if($tenant->contact_email)
                                        <a href="mailto:{{ $tenant->contact_email }}">
                                            <i class="fas fa-envelope mr-1"></i>{{ $tenant->contact_email }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Contact Phone:</th>
                                <td>
                                    @if($tenant->contact_phone)
                                        <a href="tel:{{ $tenant->contact_phone }}">
                                            <i class="fas fa-phone mr-1"></i>{{ $tenant->contact_phone }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tenant Admins -->
        <div class="row">
            <div class="col-12">
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-shield mr-1"></i> Tenant Admins ({{ $tenant->tenantAdmins->count() }})</h3>
                        <div class="card-tools">
                            <a href="{{ route('tenants.create-admin', $tenant->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus-circle mr-1"></i> Add Tenant Admin
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tenant->tenantAdmins as $admin)
                                        <tr>
                                            <td><strong>{{ $admin->name }}</strong></td>
                                            <td>
                                                <a href="mailto:{{ $admin->email }}">
                                                    <i class="fas fa-envelope mr-1"></i>{{ $admin->email }}
                                                </a>
                                            </td>
                                            <td>
                                                @if($admin->status === 'Active')
                                                    <span class="badge badge-success">{{ $admin->status }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ $admin->status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $admin->created_at->format('M d, Y') }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <div class="empty-state">
                                                    <i class="fas fa-user-shield fa-2x text-muted mb-2"></i>
                                                    <p class="text-muted mb-0">No Tenant Admins found</p>
                                                    <a href="{{ route('tenants.create-admin', $tenant->id) }}" class="btn btn-primary btn-sm mt-2">
                                                        <i class="fas fa-plus-circle mr-1"></i> Add Tenant Admin
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sites -->
        <div class="row">
            <div class="col-12">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-sitemap mr-1"></i> Sites ({{ $tenant->sites->count() }})</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Site Code</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tenant->sites as $site)
                                        <tr>
                                            <td><strong>{{ $site->name }}</strong></td>
                                            <td>
                                                @if($site->site_code)
                                                    <code>{{ $site->site_code }}</code>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $site->created_at->format('M d, Y') }}</small>
                                                <br><small class="text-muted">{{ $site->created_at->diffForHumans() }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">
                                                <div class="empty-state">
                                                    <i class="fas fa-sitemap fa-2x text-muted mb-2"></i>
                                                    <p class="text-muted mb-0">No Sites found for this tenant</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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
        padding: 1rem;
    }
    .table-borderless td,
    .table-borderless th {
        border: none;
        padding: 0.75rem 0.5rem;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@endsection
