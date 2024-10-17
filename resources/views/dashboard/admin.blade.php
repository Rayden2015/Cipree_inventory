<h1>Admin Dashboard</h1>
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        {{-- @php($students = \App\Models\Student::all()) --}}
                        <h3>{{ $total_properties }}</h3>

                        <p>Total Properties</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>

            </div>

            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-secondary">
                    <div class="inner">
                        {{-- @php($students = \App\Models\Student::all()) --}}
                        <h3>{{ $sale_properties }}</h3>

                        <p style="text-align:">Sale Properties</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>

            </div>

            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        {{-- @php($students = \App\Models\Student::all()) --}}
                        <h3>{{ $rent_properties }}</h3>

                        <p style="text-align:">Rent Properties</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>

            </div>



        </div>



        <!-- ./col -->


    {{--  --}}

    </div>
    {{--  --}}


    <!-- /.row (main row) -->
    </div>