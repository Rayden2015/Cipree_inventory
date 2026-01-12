@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-tachometer-alt"></i> Tenant Admin Dashboard</h1>
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
        @if($tenant)
            <!-- Tenant Info Card -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-building mr-1"></i>
                                {{ $tenant->name }}
                            </h3>
                            <div class="card-tools">
                                <span class="badge badge-{{ $tenant->status === 'Active' ? 'success' : ($tenant->status === 'Suspended' ? 'danger' : 'warning') }}">
                                    {{ $tenant->status }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Domain:</strong> {{ $tenant->domain ?? 'N/A' }}</p>
                                    <p><strong>Contact Email:</strong> {{ $tenant->contact_email ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Created:</strong> {{ $tenant->created_at->format('M d, Y') }}</p>
                                    <p><strong>Last Updated:</strong> {{ $tenant->updated_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row">
                <!-- Sites Card -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-gradient-info">
                        <div class="inner">
                            <h3>{{ $stats['sites_count'] ?? 0 }}</h3>
                            <p>Total Sites</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <a href="{{ route('sites.index') }}" class="small-box-footer">
                            View All Sites <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Users Card -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-gradient-success">
                        <div class="inner">
                            <h3>{{ $stats['users_count'] ?? 0 }}</h3>
                            <p>Total Users</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="{{ route('users.index') }}" class="small-box-footer">
                            View All Users <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Active Sites Card -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-gradient-warning">
                        <div class="inner">
                            <h3>{{ $stats['active_sites'] ?? 0 }}</h3>
                            <p>Active Sites</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="{{ route('sites.index') }}" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-gradient-primary">
                        <div class="inner">
                            <h3><i class="fas fa-bolt"></i></h3>
                            <p>Quick Actions</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <a href="#" class="small-box-footer" data-toggle="modal" data-target="#quickActionsModal">
                            More Info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Row -->
            <div class="row">
                <!-- Recent Sites -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-gradient-info">
                            <h3 class="card-title text-white">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                Recent Sites
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Site Name</th>
                                            <th>Created</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentSites ?? [] as $site)
                                            <tr>
                                                <td>
                                                    <strong>{{ $site->name }}</strong>
                                                </td>
                                                <td>
                                                    <small>{{ $site->created_at->diffForHumans() }}</small>
                                                </td>
                                                <td>
                                                    <a href="{{ route('sites.show', $site->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-3">
                                                    No sites found
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <a href="{{ route('sites.index') }}" class="btn btn-sm btn-info">
                                View All Sites
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-gradient-success">
                            <h3 class="card-title text-white">
                                <i class="fas fa-users mr-1"></i>
                                Recent Users
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>User Name</th>
                                            <th>Email</th>
                                            <th>Created</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentUsers ?? [] as $user)
                                            <tr>
                                                <td>
                                                    <strong>{{ $user->name }}</strong>
                                                </td>
                                                <td>
                                                    <small>{{ $user->email }}</small>
                                                </td>
                                                <td>
                                                    <small>{{ $user->created_at->diffForHumans() }}</small>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-3">
                                                    No users found
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <a href="{{ route('users.index') }}" class="btn btn-sm btn-success">
                                View All Users
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- No Tenant Assigned -->
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-warning">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> No Tenant Assigned</h5>
                        <p>You are not assigned to a tenant. Please contact your administrator.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Quick Actions Modal -->
<div class="modal fade" id="quickActionsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Actions</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="list-group">
                    <a href="{{ route('sites.create') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-plus-circle mr-2"></i> Create New Site
                    </a>
                    <a href="{{ route('users.create') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-user-plus mr-2"></i> Add New User
                    </a>
                    <a href="{{ route('sites.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-list mr-2"></i> Manage Sites
                    </a>
                    <a href="{{ route('users.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-users-cog mr-2"></i> Manage Users
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
