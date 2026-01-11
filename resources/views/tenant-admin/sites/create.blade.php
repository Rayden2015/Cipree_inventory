@extends('layouts.admin')
@section('content')
    
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>Create Site - {{ $tenant->name }}</title>
            <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
        </head>
    

        <body>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New Site for {{ $tenant->name }}</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('tenant-admin.sites.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="name">Site Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="site_code">Site Code</label>
                            <input type="text" name="site_code" id="site_code" class="form-control @error('site_code') is-invalid @enderror" 
                                   value="{{ old('site_code') }}">
                            @error('site_code')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Optional unique identifier for the site</small>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Create Site</button>
                            <a href="{{ route('tenant-admin.sites.index') }}" class="btn btn-secondary">Cancel</a>
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
