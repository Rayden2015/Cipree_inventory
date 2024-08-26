@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1>Dashboard</h1>
                @hasrole('Super Admin')
                    @include('dashboard.superadmin')
                @endhasrole

                @hasrole('Admin')
                    @include('dashboard.admin')
                @endhasrole

                @hasrole('requester')
                @include('dashboard.requester')
            @endhasrole

                @hasrole('store_officer')
                    @include('dashboard.store_officer')
                @endhasrole

                @hasrole('store_assistant')
                    @include('dashboard.store_officer')
                @endhasrole

                @hasrole('purchasing_officer')
                    @include('dashboard.purchasing_officer')
                @endhasrole

                @hasrole('authoriser')
                    @include('dashboard.authoriser')
                @endhasrole
                @hasrole('site_admin')
                    @include('dashboard.site_admin')
                @endhasrole

                @hasrole('finance_officer')
                    @include('dashboard.finance_officer')
                @endhasrole


            </div>
        </div>
    </div>
@endsection
