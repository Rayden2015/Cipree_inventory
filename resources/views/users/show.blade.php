@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="float-start">
                    User Information
                </div>
                <div class="float-end">
                    <a href="{{ route('users.index') }}" class="btn btn-primary btn-sm">&larr; Back</a>
                </div>
            </div>
            <div class="card-body">
                @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
    
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

                    <div class="mb-3 row">
                        <label for="name" class="col-md-4 col-form-label text-md-end text-start"><strong>Name:</strong></label>
                        <div class="col-md-6" style="line-height: 35px;">
                            {{ $user->name }}
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="email" class="col-md-4 col-form-label text-md-end text-start"><strong>Email Address:</strong></label>
                        <div class="col-md-6" style="line-height: 35px;">
                            {{ $user->email }}
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="address" class="col-md-4 col-form-label text-md-end text-start"><strong>Address:</strong></label>
                        <div class="col-md-6" style="line-height: 35px;">
                            {{ $user->address }}
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="dob" class="col-md-4 col-form-label text-md-end text-start"><strong>Date of Birth:</strong></label>
                        <div class="col-md-6" style="line-height: 35px;">
                            {{ $user->dob }}
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="phone" class="col-md-4 col-form-label text-md-end text-start"><strong>Phone:</strong></label>
                        <div class="col-md-6" style="line-height: 35px;">
                            {{ $user->phone }}
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="status" class="col-md-4 col-form-label text-md-end text-start"><strong>Status:</strong></label>
                        <div class="col-md-6" style="line-height: 35px;">
                            {{ $user->status }}
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="site_id" class="col-md-4 col-form-label text-md-end text-start"><strong>Site Name:</strong></label>
                        <div class="col-md-6" style="line-height: 35px;">
                            {{ $user->site->name }}
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="staff_id" class="col-md-4 col-form-label text-md-end text-start"><strong>Staff ID:</strong></label>
                        <div class="col-md-6" style="line-height: 35px;">
                            {{ $user->staff_id }}
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="roles" class="col-md-4 col-form-label text-md-end text-start"><strong>Roles:</strong></label>
                        <div class="col-md-6" style="line-height: 35px;">
                            @forelse ($user->getRoleNames() as $role)
                                <span class="badge bg-primary">{{ $role }}</span>
                            @empty
                            @endforelse
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>    
@endsection