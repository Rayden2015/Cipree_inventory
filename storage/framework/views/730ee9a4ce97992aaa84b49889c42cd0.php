<?php $__env->startSection('content'); ?>
   
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <link rel="stylesheet" href="https://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
            <link rel="stylesheet"
                href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

            <!-- Font Awesome -->
            <link rel="stylesheet" href="<?php echo e(asset('/assets/plugins/fontawesome-free/css/all.min.css')); ?>">
            <!-- DataTables -->
            <link rel="stylesheet" href="<?php echo e(asset('/assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')); ?>">
            <link rel="stylesheet"
                href="<?php echo e(asset('/assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')); ?>">
            <link rel="stylesheet" href="<?php echo e(asset('/assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')); ?>">
            <!-- Theme style -->
            <link rel="stylesheet" href="<?php echo e(asset('/assets/dist/css/adminlte.min.css')); ?>">

            <title>Document</title>

        </head>

        <body>
            <style>
                #rcorners1 {
                    border-radius: 25px;

                }
            </style>
            <?php
                $first_name = App\Http\Controllers\UserController::username();
                $logo = App\Http\Controllers\UserController::logo();
            ?>
         
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

                

                


                <!-- /.row (main row) -->
            </div>

            <div class="container-fluid">
                <!-- Small boxes (Stat box) -->
                <div class="row">

                    <!-- ./col -->


                    <!-- ./col -->

                </div>



                

                


                <!-- /.row (main row) -->
            </div>
        </body>
        <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
        <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
        <?php echo Toastr::message(); ?>

        <script>
            setTimeout(function() {
                window.location.reload();
            }, 60000);
        </script>
    

    </html>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/sappxaph/fastnet.sapphirehillsint.com/resources/views/dashboard/requester.blade.php ENDPATH**/ ?>