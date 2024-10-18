
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">

                <!-- ./col -->

                <!-- ./col -->
                <div class="col-lg-3 col-6">

                    <div style="background-color: #0e6258" class="small-box" id="rcorners1">
                        <div class="inner">
                            <h4 style="color: white; font-family: 'Segoe UI Light';">{{ $rfi_pending_approval }}</h4>

                            <p style="color: white; font-family: 'Segoe UI Light';">
                                RFI Pending Approval</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="{{ route('dashboard.rfi_pending_approval') }}" class="small-box-footer">More info
                            <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">

                    <div style="background-color: #19AF9D;" class="small-box " id="rcorners1">
                        <div class="inner">

                            <h4 style="color: white; font-family: 'Segoe UI Light';">{{ $rfi_approved_requests }}</h4>


                            <p style="color: white; font-family: 'Segoe UI Light';">RFI Approved</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="{{ route('dashboard.rfi_approved_requests') }}" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">

                    <div style="background-color:#0e6258" class="small-box" id="rcorners1">
                        <div class="inner">

                            <h4 style="color: white; font-family: 'Segoe UI Light';">{{ $rfi_processed_requests }}</h4>

                            <p style="color: white; font-family: 'Segoe UI Light';">RFI Processed</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="{{ route('dashboard.rfi_processed_requests') }}" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <!-- ./col -->


                <div class="col-lg-3 col-6">

                    <div class="small-box bg-danger" id="rcorners1">
                        <div class="inner">

                            <h4 style="color: white; font-family: 'Segoe UI Light';">{{ $rfi_denied }}</h4>

                            <p style="color: white; font-family: 'Segoe UI Light';">RFI Denied</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="{{ route('dashboard.rfi_denied') }}" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                {{-- dpr --}}

                <div class="col-lg-3 col-6">

                    <div style="background-color: #0e6258" class="small-box" id="rcorners1">
                        <div class="inner">
                            <h4 style="color: white; font-family: 'Segoe UI Light';">{{ $dpr_pending_approval }}</h4>

                            <p style="color: white; font-family: 'Segoe UI Light';">
                                DPR Pending Approval</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="{{ route('dashboard.dpr_pending_approval') }}" class="small-box-footer">More info
                            <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">

                    <div style="background-color: #19AF9D;" class="small-box " id="rcorners1">
                        <div class="inner">

                            <h4 style="color: white; font-family: 'Segoe UI Light';">{{ $dpr_approved }}</h4>


                            <p style="color: white; font-family: 'Segoe UI Light';">DPR Approved</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="{{ route('dashboard.dpr_approved') }}" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">

                    <div style="background-color:#0e6258" class="small-box" id="rcorners1">
                        <div class="inner">

                            <h4 style="color: white; font-family: 'Segoe UI Light';">{{ $dpr_processed }}</h4>

                            <p style="color: white; font-family: 'Segoe UI Light';">DPR Processed</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="{{ route('dashboard.dpr_processed') }}" class="small-box-footer">More info
                            <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger" id="rcorners1">
                        <div class="inner">

                            <h4 style="color: white; font-family: 'Segoe UI Light';">{{ $dpr_denied }}</h4>

                            <p style="color: white; font-family: 'Segoe UI Light';">DPR Denied</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="{{ route('dashboard.dpr_denied') }}" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                {{-- end of dpr  --}}

                <div class="col-lg-3 col-6">
                    <div style="background-color: #19AF9D;" class="small-box " id="rcorners1">
                        <div class="inner">

                            <h4 style="color: white; font-family: 'Segoe UI Light';"></h4>
                            @include('purchases.requesterhomesearch')

                        </div>
                        <div class="icon">
                            {{-- <i class="ion ion-person-add"></i> --}}
                        </div>
                        {{-- <a href="{{ route('stores.request_search') }}"  type="submit" class="small-box-footer">Check Item Availability <i class="fas fa-arrow-circle-right"></i></a>
     --}}
                    </div>
                </div>
                <!-- ./col -->
            </div>

        </div>
   
    {{-- end of requester dashboard --}}