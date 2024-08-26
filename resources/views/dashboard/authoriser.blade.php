@extends('layouts.admin')
@section('content')
@if(Auth::user()->hasRole('authoriser'))
<style>
    #rcorners1 {
  border-radius: 25px;
 
}
</style>
@php
    $first_name = App\Http\Controllers\UserController::username();
    $logo = App\Http\Controllers\UserController::logo();
@endphp
   
<div class="container-fluid">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        
        <!-- ./col -->
        <div class="col-lg-3 col-6">

            <div class="small-box bg-danger">
                <div class="inner">

                    <h3>{{ $new_approvals }}</h3>

                    <p>New Approvals</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
                <a href="" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">

            <div class="small-box bg-primary">
                <div class="inner">

                    <h3>{{ $pending_request_approvals }}</h3>

                    <p>Pending Request Approvals</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="{{ route('dashboard.pending_request_approvals') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
       
        <div class="col-lg-3 col-6">

            <div class="small-box bg-warning">
                <div class="inner">

                    <h3>{{ $pending_po_approvals }}</h3>

                    <p>Pending Purchase Order Approvals</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="{{ route('dashboard.pending_po_approvals') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">

            <div class="small-box bg-success">
                <div class="inner">

                    <h3>{{ $pending_stock_approvals }}</h3>

                    <p>Pending Stock Approvals</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="{{ route('dashboard.pending_stock_approvals') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

       
        <div class="col-lg-3 col-6">

            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $approved_request }}</h3>

                    <p>Approved Requests </p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
                <a href="{{ route('dashboard.approved_request') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">

            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $approved_pos }}</h3>

                    <p>Approved Purchased Orders</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
                <a href="{{ route('dashboard.approved_pos') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">

            <div class="small-box bg-dark">
                <div class="inner">
                    <h3>{{ $processed_pos }}</h3>

                    <p>Processed Purchased Orders</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
                <a href="{{ route('dashboard.processed_pos') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">

            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $processed_request }}</h3>

                    <p>Processed Requests</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
                <a href="{{ route('dashboard.processed_request') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
    </div>

   {{--  --}}

    {{--  --}}


    <!-- /.row (main row) -->
</div>

<div class="container-fluid">
    <!-- Small boxes (Stat box) -->
   

   {{--  --}}

    {{--  --}}


    <!-- /.row (main row) -->
</div>
<script>
    setTimeout(function(){
window.location.reload();
}, 60000);
</script>
@endif
@endsection
