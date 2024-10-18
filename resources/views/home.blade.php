@extends('layouts.admin')

@section('content')
    <style>
        #rcorners1 {
            border-radius: 25px;
        }
    </style>
    
    @php
        $first_name = App\Http\Controllers\UserController::username();
        $logo = App\Http\Controllers\UserController::logo();
    @endphp
    
    <br>

    {{-- Dashboard Inclusions --}}
    @can('view-admin-dashboard')
        @include('dashboard.admin')
    @endcan

    @can('view-authoriser-dashboard')
        @include('dashboard.authoriser')
    @endcan

    @can('view-finance-officer-dashboard')
        @include('dashboard.finance_officer')
    @endcan

    @can('view-purchasing-officer-dashboard')
        @include('dashboard.purchasing_officer')
    @endcan

    @can('view-requester-dashboard')
        @include('dashboard.requester')
    @endcan

    @can('view-site-admin-dashboard')
        @include('dashboard.site_admin')
    @endcan

    @can('view-store-officer-dashboard')
        @include('dashboard.store_officer')
    @endcan

    @can('view-super-admin-dashboard')
        @include('dashboard.super_admin')
    @endcan

    @can('department-authoriser-dashboard')
        @include('dashboard.department_authoriser')
    @endcan

    <script>
        setTimeout(function() {
            window.location.reload();
        }, 60000);
    </script>
@endsection
