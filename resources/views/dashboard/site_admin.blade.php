@extends('layouts.admin')
@section('content')
    @if (Auth::user()->hasRole('site_admin'))
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
            <br>
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-6">

                    <div style="background-color: #19AF9D;" class="small-box" id="rcorners1">
                        <div class="inner">

                            <h3 style="color:white;">{{ $active_user_accounts }}</h3>

                            <p>Active User Accounts</p>
                        </div>
                        <div class="icon">
                            {{-- <i class="ion ion-bag"></i> --}}
                        </div>
                        <a href="{{ route('dashboard.active_user_accounts') }}" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">

                    <div style="background-color: #0e6258;" class="small-box" id="rcorners1">
                        <div class="inner">

                            <h3 style="color:white;">{{ $disabled_user_accounts }}</h3>

                            <p>Disabled User Accounts</p>
                        </div>
                        <div class="icon">
                            {{-- <i class="ion ion-stats-bars"></i> --}}
                        </div>
                        <a href="{{ route('dashboard.disabled_user_accounts') }}" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">

                    <div style="background-color: #19AF9D;" class="small-box" id="rcorners1">
                        <div class="inner">

                            <h3 style="color:white;">{{ $active_endusers }}</h3>

                            <p>Active Endusers</p>
                        </div>
                        <div class="icon">
                            {{-- <i class="ion ion-person-add"></i> --}}
                        </div>
                        <a href="{{ route('dashboard.active_endusers') }}" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">

                    <div style="background-color: #0e6258;" class="small-box" id="rcorners1">
                        <div class="inner">
                            <h3 style="color:white;">{{ $disabled_endusers }}</h3>

                            <p>Disabled Endusers</p>
                        </div>
                        <div class="icon">
                            {{-- <i class="ion ion-pie-graph"></i> --}}
                        </div>
                        <a href="{{ route('dashboard.disabled_endusers') }}" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
            </div>
            <div class="row">
                {{--  --}}
                <div class="col-lg-3 col-6">

                    <div style="background-color: #0e6258;" class="small-box" id="rcorners1">
                        <div class="inner">

                            <h3 style="color:white;">{{ $departments }}</h3>

                            <p>Departments</p>
                        </div>
                        <div class="icon">
                            {{-- <i class="ion ion-stats-bars"></i> --}}
                        </div>
                        <a href="{{ route('departmentslist.index') }}" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                {{--  --}}
                {{--  --}}
                <div class="col-lg-3 col-6">

                    <div style="background-color: #19AF9D;" class="small-box" id="rcorners1">
                        <div class="inner">

                            <h3 style="color:white;">{{ $sections }}</h3>

                            <p>Sections</p>
                        </div>
                        <div class="icon">
                            {{-- <i class="ion ion-stats-bars"></i> --}}
                        </div>
                        <a href="{{ route('sectionslist.index') }}" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                {{--  --}}
                {{--  --}}
                <div class="col-lg-3 col-6">

                    <div style="background-color: #0e6258;" class="small-box" id="rcorners1">
                        <div class="inner">

                            <h3 style="color:white;">{{ $user_complaints_open }}</h3>

                            <p>User Complaints - Open</p>
                        </div>
                        <div class="icon">
                            {{-- <i class="ion ion-stats-bars"></i> --}}
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                {{--  --}}

                {{--  --}}
                <div class="col-lg-3 col-6">

                    <div style="background-color:red;" class="small-box" id="rcorners1">
                        <div class="inner">

                            <h3 style="color:white;">0</h3>

                            <p>Ex</p>
                        </div>
                        <div class="icon">
                            {{-- <i class="ion ion-stats-bars"></i> --}}
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                {{--  --}}


                <!-- /.row (main row) -->
            </div>
        </div>
        <script>
            setTimeout(function() {
                window.location.reload();
            }, 60000);
        </script>
    @endif
@endsection
