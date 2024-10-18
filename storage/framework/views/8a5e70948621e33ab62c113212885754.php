<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('requester-dashboard')): ?>
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">

                <!-- ./col -->

                <!-- ./col -->
                <div class="col-lg-3 col-6">

                    <div style="background-color: #0e6258" class="small-box" id="rcorners1">
                        <div class="inner">
                            <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e($rfi_pending_approval); ?></h4>

                            <p style="color: white; font-family: 'Segoe UI Light';">
                                RFI Pending Approval</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="<?php echo e(route('dashboard.rfi_pending_approval')); ?>" class="small-box-footer">More info
                            <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">

                    <div style="background-color: #19AF9D;" class="small-box " id="rcorners1">
                        <div class="inner">

                            <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e($rfi_approved_requests); ?></h4>


                            <p style="color: white; font-family: 'Segoe UI Light';">RFI Approved</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="<?php echo e(route('dashboard.rfi_approved_requests')); ?>" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">

                    <div style="background-color:#0e6258" class="small-box" id="rcorners1">
                        <div class="inner">

                            <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e($rfi_processed_requests); ?></h4>

                            <p style="color: white; font-family: 'Segoe UI Light';">RFI Processed</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="<?php echo e(route('dashboard.rfi_processed_requests')); ?>" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <!-- ./col -->


                <div class="col-lg-3 col-6">

                    <div class="small-box bg-danger" id="rcorners1">
                        <div class="inner">

                            <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e($rfi_denied); ?></h4>

                            <p style="color: white; font-family: 'Segoe UI Light';">RFI Denied</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="<?php echo e(route('dashboard.rfi_denied')); ?>" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                

                <div class="col-lg-3 col-6">

                    <div style="background-color: #0e6258" class="small-box" id="rcorners1">
                        <div class="inner">
                            <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e($dpr_pending_approval); ?></h4>

                            <p style="color: white; font-family: 'Segoe UI Light';">
                                DPR Pending Approval</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="<?php echo e(route('dashboard.dpr_pending_approval')); ?>" class="small-box-footer">More info
                            <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">

                    <div style="background-color: #19AF9D;" class="small-box " id="rcorners1">
                        <div class="inner">

                            <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e($dpr_approved); ?></h4>


                            <p style="color: white; font-family: 'Segoe UI Light';">DPR Approved</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="<?php echo e(route('dashboard.dpr_approved')); ?>" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">

                    <div style="background-color:#0e6258" class="small-box" id="rcorners1">
                        <div class="inner">

                            <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e($dpr_processed); ?></h4>

                            <p style="color: white; font-family: 'Segoe UI Light';">DPR Processed</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="<?php echo e(route('dashboard.dpr_processed')); ?>" class="small-box-footer">More info
                            <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger" id="rcorners1">
                        <div class="inner">

                            <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e($dpr_denied); ?></h4>

                            <p style="color: white; font-family: 'Segoe UI Light';">DPR Denied</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="<?php echo e(route('dashboard.dpr_denied')); ?>" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                

                <div class="col-lg-3 col-6">
                    <div style="background-color: #19AF9D;" class="small-box " id="rcorners1">
                        <div class="inner">

                            <h4 style="color: white; font-family: 'Segoe UI Light';"></h4>
                            <?php echo $__env->make('purchases.requesterhomesearch', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                        </div>
                        <div class="icon">
                            
                        </div>
                        
                    </div>
                </div>
                <!-- ./col -->
            </div>

        </div>
    <?php endif; ?>
    <?php /**PATH /home/pensmqhz/test.cipree.com/resources/views/dashboard/requester.blade.php ENDPATH**/ ?>