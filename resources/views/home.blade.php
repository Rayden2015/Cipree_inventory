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
        $dashboard_included = false; // To ensure only one dashboard is shown
    @endphp

    <br>

    {{-- Dashboard Inclusions --}}
    @if (!$dashboard_included && Gate::allows('admin-dashboard'))
        @include('dashboard.admin')
        @php $dashboard_included = true; @endphp
    @endif

    @if (!$dashboard_included && Gate::allows('super-authoriser-dashboard'))
        @include('dashboard.authoriser')
        @php $dashboard_included = true; @endphp
    @endif

    @if (!$dashboard_included && Gate::allows('finance-officer-dashboard'))
        @include('dashboard.finance_officer')
        @php $dashboard_included = true; @endphp
    @endif

    @if (!$dashboard_included && Gate::allows('purchasing-officer-dashboard'))
        @include('dashboard.purchasing_officer')
        @php $dashboard_included = true; @endphp
    @endif

    @if (!$dashboard_included && Gate::allows('requester-dashboard'))
        @include('dashboard.requester')
        @php $dashboard_included = true; @endphp
    @endif

    @if (!$dashboard_included && Gate::allows('site-admin-dashboard'))
        @include('dashboard.site_admin')
        @php $dashboard_included = true; @endphp
    @endif

    @if (!$dashboard_included && Gate::allows('store-officer-dashboard'))
        @include('dashboard.store_officer')
        @php $dashboard_included = true; @endphp
    @endif

    @if (!$dashboard_included && Gate::allows('super-admin-dashboard'))
        @include('dashboard.super_admin')
        @php $dashboard_included = true; @endphp
    @endif

    @if (!$dashboard_included && Gate::allows('department-authoriser-dashboard'))
        @include('dashboard.department_authoriser')
        @php $dashboard_included = true; @endphp
    @endif

    @if (!$dashboard_included)
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Welcome to Cipree Inventory</h1>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <p class="text-center">You don't have access to any dashboard yet. Please contact your administrator to assign appropriate permissions.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <script>
        setTimeout(function() {
            window.location.reload();
        }, 60000);
    </script>
@endsection
