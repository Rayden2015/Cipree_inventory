@extends('layouts.admin')
@section('content')
    
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>Tenant Settings - {{ $tenant->name }}</title>
            <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
        </head>
    

        <body>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tenant Settings - {{ $tenant->name }}</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('tenant-admin.update-settings') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <h4>Tenant Information</h4>
                        <hr>
                        
                        <div class="form-group">
                            <label for="name">Tenant Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $tenant->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="3">{{ old('description', $tenant->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <h4 class="mt-4">Contact Information</h4>
                        <hr>

                        <div class="form-group">
                            <label for="contact_name">Contact Name</label>
                            <input type="text" name="contact_name" id="contact_name" class="form-control @error('contact_name') is-invalid @enderror" 
                                   value="{{ old('contact_name', $tenant->contact_name) }}">
                            @error('contact_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="contact_email">Contact Email</label>
                            <input type="email" name="contact_email" id="contact_email" class="form-control @error('contact_email') is-invalid @enderror" 
                                   value="{{ old('contact_email', $tenant->contact_email) }}">
                            @error('contact_email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="contact_phone">Contact Phone</label>
                            <input type="text" name="contact_phone" id="contact_phone" class="form-control @error('contact_phone') is-invalid @enderror" 
                                   value="{{ old('contact_phone', $tenant->contact_phone) }}">
                            @error('contact_phone')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <h4 class="mt-4">Branding</h4>
                        <hr>

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
                                    <input type="file" name="logo" id="logo" class="form-control-file @error('logo') is-invalid @enderror"
                                           accept="image/jpeg,image/png,image/jpg,image/gif,image/svg">
                                    <small class="form-text text-muted">Recommended: PNG or SVG, max 2MB. Leave empty to keep current logo.</small>
                                    @error('logo')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
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
                                        <span class="invalid-feedback">{{ $message }}</span>
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
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Update Settings</button>
                            <a href="{{ route('tenant-admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>

        </body>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
        {!! Toastr::message() !!}
        </html>
  
@endsection
