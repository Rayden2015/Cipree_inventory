@extends('layouts.admin')
@section('content')
    
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>Tenant Admin Dashboard</title>
            <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
        </head>
    

        <body>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tenant Admin Dashboard - {{ $tenant->name ?? 'Unknown Tenant' }}</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-building"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Sites</span>
                                    <span class="info-box-number">{{ $stats['sites_count'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Users</span>
                                    <span class="info-box-number">{{ $stats['users_count'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Active Sites</span>
                                    <span class="info-box-number">{{ $stats['active_sites'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title">Quick Actions</h3>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('tenant-admin.sites.index') }}" class="btn btn-primary mr-2">Manage Sites</a>
                                    <a href="{{ route('tenant-admin.users.index') }}" class="btn btn-success mr-2">Manage Users</a>
                                    <a href="{{ route('tenant-admin.settings') }}" class="btn btn-info">Tenant Settings</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Recent Sites</h3>
                                </div>
                                <div class="card-body">
                                    @forelse($recentSites as $site)
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <strong>{{ $site->name }}</strong><br>
                                                <small class="text-muted">{{ $site->site_code ?? 'N/A' }}</small>
                                            </div>
                                            <small class="text-muted">{{ $site->created_at->diffForHumans() }}</small>
                                        </div>
                                    @empty
                                        <p class="text-muted">No sites yet. <a href="{{ route('tenant-admin.sites.create') }}">Create your first site</a></p>
                                    @endforelse
                                    @if($recentSites->count() > 0)
                                        <a href="{{ route('tenant-admin.sites.index') }}" class="btn btn-sm btn-primary mt-2">View All Sites</a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Recent Users</h3>
                                </div>
                                <div class="card-body">
                                    @forelse($recentUsers as $user)
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <strong>{{ $user->name }}</strong><br>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                            <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                        </div>
                                    @empty
                                        <p class="text-muted">No users yet. <a href="{{ route('tenant-admin.users.create') }}">Create your first user</a></p>
                                    @endforelse
                                    @if($recentUsers->count() > 0)
                                        <a href="{{ route('tenant-admin.users.index') }}" class="btn btn-sm btn-success mt-2">View All Users</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </body>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
        {!! Toastr::message() !!}
        </html>
  
@endsection
