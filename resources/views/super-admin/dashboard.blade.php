@extends('layouts.admin')

@php
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-tachometer-alt"></i> Super Admin Dashboard</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row">
            <!-- Total Tenants Card -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-info">
                    <div class="inner">
                        <h3>{{ number_format($stats['total_tenants']) }}</h3>
                        <p>Total Tenants</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <a href="{{ route('tenants.index') }}" class="small-box-footer">
                        Manage Tenants <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Active Tenants Card -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-success">
                    <div class="inner">
                        <h3>{{ number_format($stats['active_tenants']) }}</h3>
                        <p>Active Tenants</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <a href="{{ route('tenants.index', ['status' => 'Active']) }}" class="small-box-footer">
                        View Active <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Total Users Card -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-warning">
                    <div class="inner">
                        <h3>{{ number_format($stats['total_users']) }}</h3>
                        <p>Total Users</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                        View All Users <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Total Sites Card -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-primary">
                    <div class="inner">
                        <h3>{{ number_format($stats['total_sites']) }}</h3>
                        <p>Total Sites</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                        View All Sites <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Additional Statistics Row -->
        <div class="row">
            <!-- Tenant Admins Card -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-secondary">
                    <div class="inner">
                        <h3>{{ number_format($stats['total_tenant_admins']) }}</h3>
                        <p>Tenant Admins</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                        View Admins <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Inactive Tenants Card -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-danger">
                    <div class="inner">
                        <h3>{{ number_format($stats['inactive_tenants']) }}</h3>
                        <p>Inactive Tenants</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-ban"></i>
                    </div>
                    <a href="{{ route('tenants.index', ['status' => 'Inactive']) }}" class="small-box-footer">
                        View Inactive <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Suspended Tenants Card -->
            <div class="col-lg-3 col-6">
                <div class="small-box" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="inner">
                        <h3>{{ number_format($stats['suspended_tenants']) }}</h3>
                        <p>Suspended Tenants</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <a href="{{ route('tenants.index', ['status' => 'Suspended']) }}" class="small-box-footer">
                        View Suspended <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="col-lg-3 col-6">
                <div class="small-box" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="inner">
                        <h3><i class="fas fa-plus"></i></h3>
                        <p>Create New Tenant</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <a href="{{ route('tenants.create') }}" class="small-box-footer">
                        Create Tenant <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Row -->
        <div class="row">
            <!-- Recent Tenants -->
            <div class="col-lg-6">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clock mr-1"></i>
                            Recent Tenants
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('tenants.index') }}" class="btn btn-tool btn-sm">
                                <i class="fas fa-list"></i> View All
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Tenant Name</th>
                                        <th>Status</th>
                                        <th>Sites</th>
                                        <th>Users</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTenants as $tenant)
                                        <tr>
                                            <td>
                                                <strong>{{ $tenant->name }}</strong>
                                                @if($tenant->domain)
                                                    <br><small class="text-muted">{{ $tenant->domain }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($tenant->status === 'Active')
                                                    <span class="badge badge-success">{{ $tenant->status }}</span>
                                                @elseif($tenant->status === 'Inactive')
                                                    <span class="badge badge-danger">{{ $tenant->status }}</span>
                                                @else
                                                    <span class="badge badge-warning">{{ $tenant->status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $tenant->sites_count ?? 0 }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-primary">{{ $tenant->users_count ?? 0 }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('tenants.show', $tenant->id) }}" 
                                                   class="btn btn-sm btn-primary" 
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                                <p>No tenants found</p>
                                                <a href="{{ route('tenants.create') }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-plus"></i> Create First Tenant
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Tenants by Users -->
            <div class="col-lg-6">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-users mr-1"></i>
                            Top Tenants by Users
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Tenant Name</th>
                                        <th>Users</th>
                                        <th>Sites</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topTenantsByUsers as $tenant)
                                        <tr>
                                            <td>
                                                <strong>{{ $tenant->name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-warning badge-lg">{{ $tenant->users_count }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $tenant->sites_count }}</span>
                                            </td>
                                            <td>
                                                @if($tenant->status === 'Active')
                                                    <span class="badge badge-success">{{ $tenant->status }}</span>
                                                @elseif($tenant->status === 'Inactive')
                                                    <span class="badge badge-danger">{{ $tenant->status }}</span>
                                                @else
                                                    <span class="badge badge-warning">{{ $tenant->status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('tenants.show', $tenant->id) }}" 
                                                   class="btn btn-sm btn-success" 
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                                <p>No data available</p>
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

        <!-- Second Row: Top Tenants by Sites -->
        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            Top Tenants by Sites
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Tenant Name</th>
                                        <th>Sites</th>
                                        <th>Users</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topTenantsBySites as $index => $tenant)
                                        <tr>
                                            <td>
                                                <span class="badge badge-dark badge-lg">#{{ $index + 1 }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ $tenant->name }}</strong>
                                                @if($tenant->domain)
                                                    <br><small class="text-muted">{{ $tenant->domain }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-primary badge-lg">{{ $tenant->sites_count }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $tenant->users_count }}</span>
                                            </td>
                                            <td>
                                                @if($tenant->status === 'Active')
                                                    <span class="badge badge-success">{{ $tenant->status }}</span>
                                                @elseif($tenant->status === 'Inactive')
                                                    <span class="badge badge-danger">{{ $tenant->status }}</span>
                                                @else
                                                    <span class="badge badge-warning">{{ $tenant->status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('tenants.show', $tenant->id) }}" 
                                                   class="btn btn-sm btn-info" 
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                                <p>No data available</p>
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

        <!-- Growth Metrics Row -->
        <div class="row mt-3">
            <div class="col-lg-4">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Growth Metrics (Last 30 Days)
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-success"><i class="fas fa-building"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">New Tenants</span>
                                        <span class="info-box-number">{{ $stats['tenants_created_last_month'] }}</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" style="width: {{ min(100, ($stats['tenants_created_last_month'] / max(1, $stats['total_tenants'])) * 100) }}%"></div>
                                        </div>
                                        <span class="progress-description">
                                            {{ $stats['tenants_created_this_month'] }} this month
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">New Users</span>
                                        <span class="info-box-number">{{ $stats['users_created_last_month'] }}</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-info" style="width: {{ min(100, ($stats['users_created_last_month'] / max(1, $stats['total_users'])) * 100) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-primary"><i class="fas fa-map-marker-alt"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">New Sites</span>
                                        <span class="info-box-number">{{ $stats['sites_created_last_month'] }}</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-primary" style="width: {{ min(100, ($stats['sites_created_last_month'] / max(1, $stats['total_sites'])) * 100) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-tachometer-alt mr-1"></i>
                            System Health
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span><strong>Active Tenants</strong></span>
                                <span><strong>{{ $stats['active_percentage'] }}%</strong></span>
                            </div>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                                     role="progressbar" 
                                     style="width: {{ $stats['active_percentage'] }}%"
                                     aria-valuenow="{{ $stats['active_percentage'] }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ $stats['active_percentage'] }}%
                                </div>
                            </div>
                            <small class="text-muted">{{ $stats['active_tenants'] }} of {{ $stats['total_tenants'] }} tenants active</small>
                        </div>
                        
                        <div class="mt-4">
                            <h5><i class="fas fa-info-circle text-info"></i> Quick Stats</h5>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <strong>Active:</strong> {{ $stats['active_tenants'] }}
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-times-circle text-danger"></i>
                                    <strong>Inactive:</strong> {{ $stats['inactive_tenants'] }}
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-exclamation-triangle text-warning"></i>
                                    <strong>Suspended:</strong> {{ $stats['suspended_tenants'] }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clock mr-1"></i>
                            Recently Created (Last 7 Days)
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Tenant</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentlyCreatedTenants as $tenant)
                                        <tr>
                                            <td>
                                                <strong>{{ Str::limit($tenant->name, 20) }}</strong>
                                            </td>
                                            <td>
                                                @if($tenant->status === 'Active')
                                                    <span class="badge badge-success badge-sm">{{ $tenant->status }}</span>
                                                @else
                                                    <span class="badge badge-danger badge-sm">{{ $tenant->status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $tenant->created_at->diffForHumans() }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">
                                                <i class="fas fa-inbox"></i> No tenants created recently
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

        <!-- Quick Actions Row -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title text-white">
                            <i class="fas fa-bolt mr-1"></i>
                            Quick Actions
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="{{ route('tenants.create') }}" class="btn btn-block btn-primary btn-lg">
                                    <i class="fas fa-plus-circle mr-2"></i>
                                    Create New Tenant
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="{{ route('tenants.index') }}" class="btn btn-block btn-success btn-lg">
                                    <i class="fas fa-list mr-2"></i>
                                    Manage Tenants
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="{{ route('home') }}" class="btn btn-block btn-info btn-lg">
                                    <i class="fas fa-home mr-2"></i>
                                    Back to Home
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="#" class="btn btn-block btn-warning btn-lg">
                                    <i class="fas fa-chart-bar mr-2"></i>
                                    View Reports
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .small-box {
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .small-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .small-box .inner h3 {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0 0 10px 0;
        white-space: nowrap;
        padding: 0;
    }
    
    .small-box .inner p {
        font-size: 1.1rem;
        font-weight: 600;
    }
    
    .small-box .icon {
        color: rgba(0, 0, 0, 0.15);
        z-index: 0;
    }
    
    .small-box .icon > i {
        font-size: 70px;
        position: absolute;
        right: 15px;
        top: 15px;
        transition: transform 0.3s;
    }
    
    .small-box:hover .icon > i {
        transform: scale(1.1);
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: none;
        margin-bottom: 1.5rem;
    }
    
    .card-header {
        border-bottom: 2px solid rgba(0, 0, 0, 0.125);
        background-color: #fff;
    }
    
    .card-title {
        font-weight: 600;
        color: #495057;
    }
    
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border-top: none;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .badge-lg {
        font-size: 1rem;
        padding: 0.5em 0.75em;
    }
    
    .btn {
        border-radius: 0.375rem;
        transition: all 0.2s;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    }
    
    .bg-gradient-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
    }
    
    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%) !important;
    }
    
    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
    }
    
    .bg-gradient-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #545b62 100%) !important;
    }
    
    .bg-gradient-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
    }
    
    @media (max-width: 768px) {
        .small-box .inner h3 {
            font-size: 2rem;
        }
        
        .small-box .icon > i {
            font-size: 50px;
        }
    }
</style>
@endsection
