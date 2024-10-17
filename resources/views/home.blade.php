@extends('layouts.admin')
@section('content')
    @if(Auth::user()->role_id == '1')
    @include('dashboard.admin')
    @endif
    @if(Auth::user()->role_id == '2')
    @include('dashboard.user')
        
    @endif
@endsection
