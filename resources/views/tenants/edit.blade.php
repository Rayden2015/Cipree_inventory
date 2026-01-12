@extends('layouts.admin')

@php
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-building"></i> Edit Tenant</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('super-admin.dashboard') }}">Super Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tenants.index') }}">Tenants</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tenants.show', $tenant->id) }}">{{ Str::limit($tenant->name, 20) }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-edit mr-2"></i>Edit Tenant: {{ $tenant->name }}</h3>
                    </div>
                    <form action="{{ route('tenants.update', $tenant->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <!-- Tenant Information Section -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="mb-3"><i class="fas fa-info-circle mr-2"></i>Tenant Information</h5>
                                    <hr>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Tenant Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               value="{{ old('name', $tenant->name) }}" required 
                                               placeholder="Enter tenant name">
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="slug">Slug</label>
                                        <input type="text" name="slug" id="slug" 
                                               class="form-control @error('slug') is-invalid @enderror" 
                                               value="{{ old('slug', $tenant->slug) }}"
                                               placeholder="URL-friendly identifier">
                                        @error('slug')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="domain">Domain</label>
                                        <input type="text" name="domain" id="domain" 
                                               class="form-control @error('domain') is-invalid @enderror" 
                                               value="{{ old('domain', $tenant->domain) }}"
                                               placeholder="example.com">
                                        @error('domain')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select name="status" id="status" 
                                                class="form-control @error('status') is-invalid @enderror" required>
                                            <option value="Active" {{ old('status', $tenant->status) === 'Active' ? 'selected' : '' }}>Active</option>
                                            <option value="Inactive" {{ old('status', $tenant->status) === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                            <option value="Suspended" {{ old('status', $tenant->status) === 'Suspended' ? 'selected' : '' }}>Suspended</option>
                                        </select>
                                        @error('status')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea name="description" id="description" 
                                                  class="form-control @error('description') is-invalid @enderror" 
                                                  rows="3" placeholder="Enter tenant description">{{ old('description', $tenant->description) }}</textarea>
                                        @error('description')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information Section -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h5 class="mb-3"><i class="fas fa-address-card mr-2"></i>Contact Information</h5>
                                    <hr>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="contact_name">Contact Name</label>
                                        <input type="text" name="contact_name" id="contact_name" 
                                               class="form-control @error('contact_name') is-invalid @enderror" 
                                               value="{{ old('contact_name', $tenant->contact_name) }}"
                                               placeholder="Contact person name">
                                        @error('contact_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="contact_email">Contact Email</label>
                                        <input type="email" name="contact_email" id="contact_email" 
                                               class="form-control @error('contact_email') is-invalid @enderror" 
                                               value="{{ old('contact_email', $tenant->contact_email) }}"
                                               placeholder="contact@example.com">
                                        @error('contact_email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="contact_phone">Contact Phone</label>
                                        <input type="text" name="contact_phone" id="contact_phone" 
                                               class="form-control @error('contact_phone') is-invalid @enderror" 
                                               value="{{ old('contact_phone', $tenant->contact_phone) }}"
                                               placeholder="+1234567890">
                                        @error('contact_phone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Branding Section -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h5 class="mb-3"><i class="fas fa-palette mr-2"></i>Branding</h5>
                                    <hr>
                                    <p class="text-muted">Customize the tenant's logo and color scheme.</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="logo">Logo</label>
                                        @if($tenant->logo_path)
                                            <div class="mb-2">
                                                <img src="{{ asset($tenant->logo_path) }}" alt="{{ $tenant->name }} Logo" 
                                                     style="max-width: 200px; max-height: 100px; border: 1px solid #ddd; padding: 5px;">
                                                <p class="text-muted small mt-1">Current logo</p>
                                            </div>
                                        @endif
                                        <input type="file" name="logo" id="logo" 
                                               class="form-control-file @error('logo') is-invalid @enderror"
                                               accept="image/jpeg,image/png,image/jpg,image/gif,image/svg">
                                        <small class="form-text text-muted">Recommended: PNG or SVG, max 2MB. Leave empty to keep current logo.</small>
                                        @error('logo')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="primary_color">Primary Color</label>
                                        <input type="color" name="primary_color" id="primary_color" 
                                               class="form-control @error('primary_color') is-invalid @enderror" 
                                               value="{{ old('primary_color', $tenant->primary_color ?? '#007bff') }}"
                                               style="height: 38px;">
                                        <small class="form-text text-muted">Main theme color</small>
                                        @error('primary_color')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="secondary_color">Secondary Color</label>
                                        <input type="color" name="secondary_color" id="secondary_color" 
                                               class="form-control @error('secondary_color') is-invalid @enderror" 
                                               value="{{ old('secondary_color', $tenant->secondary_color ?? '#6c757d') }}">
                                        <small class="form-text text-muted">Secondary theme color (optional)</small>
                                        @error('secondary_color')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Update Tenant
                            </button>
                            <a href="{{ route('tenants.show', $tenant->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
