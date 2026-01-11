@extends('layouts.admin')
@section('content')
    
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>Tenant Details</title>
            <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
        </head>
    

        <body>
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Tenant Details: {{ $tenant->name }}</h3>
                    <div>
                        <a href="{{ route('tenants.edit', $tenant->id) }}" class="btn btn-success">Edit Tenant</a>
                        <a href="{{ route('tenants.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Tenant Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Name:</th>
                                    <td>{{ $tenant->name }}</td>
                                </tr>
                                <tr>
                                    <th>Slug:</th>
                                    <td>{{ $tenant->slug }}</td>
                                </tr>
                                <tr>
                                    <th>Domain:</th>
                                    <td>{{ $tenant->domain ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge badge-{{ $tenant->status === 'Active' ? 'success' : ($tenant->status === 'Inactive' ? 'warning' : 'danger') }}">
                                            {{ $tenant->status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Description:</th>
                                    <td>{{ $tenant->description ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Created At:</th>
                                    <td>{{ $tenant->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h4>Contact Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Contact Name:</th>
                                    <td>{{ $tenant->contact_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Contact Email:</th>
                                    <td>{{ $tenant->contact_email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Contact Phone:</th>
                                    <td>{{ $tenant->contact_phone ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h4>Statistics</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-building"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Sites</span>
                                            <span class="info-box-number">{{ $tenant->sites->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="fas fa-users"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Users</span>
                                            <span class="info-box-number">{{ $tenant->users->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>Tenant Admins</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tenant->tenantAdmins as $admin)
                                        <tr>
                                            <td>{{ $admin->name }}</td>
                                            <td>{{ $admin->email }}</td>
                                            <td>
                                                <span class="badge badge-{{ $admin->status === 'Active' ? 'success' : 'danger' }}">
                                                    {{ $admin->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No Tenant Admins Found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <a href="{{ route('tenants.create-admin', $tenant->id) }}" class="btn btn-primary btn-sm">Add Tenant Admin</a>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>Sites ({{ $tenant->sites->count() }})</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Site Code</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tenant->sites as $site)
                                        <tr>
                                            <td>{{ $site->name }}</td>
                                            <td>{{ $site->site_code ?? 'N/A' }}</td>
                                            <td>{{ $site->created_at->format('Y-m-d') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No Sites Found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
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
