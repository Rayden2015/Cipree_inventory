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
    {{-- admin dashboard --}}
    @include('dashboard.admin')

    {{-- authoriser dashboard --}}
    @include('dashboard.authoriser')

    {{-- finance officer dashboard --}}
    @include('dashboard.finance_officer')

    {{-- purchasing officer dashboard --}}
    @include('dashboard.purchasing_officer')

    {{-- requester dashboard --}}
    @include('dashboard.requester')

    {{-- site admin dashboard --}}
    @include('dashboard.site_admin')

    {{-- store officer dashboard --}}
    @include('dashboard.store_officer')

    {{-- super admin dashboard --}}
    @include('dashboard.super_admin')

    <script>
        setTimeout(function() {
            window.location.reload();
        }, 60000);
    </script>
@endsection
