<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('purchasing-officer-dashboard')): ?>
<div class="container-fluid">
    <!-- Small boxes (Stat box) -->
    <div class="row">
       
        <!-- ./col -->
        <div class="col-lg-3 col-6">

            <div style="background-color: #19AF9D;" class="small-box" id="rcorners1">
                <div class="inner">

                    <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e($po_total_number_of_requests_mtd); ?></h4>

                    <p style="color: white; font-family: 'Segoe UI Light';">Total Number of Requests - MTD</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
                <a href="" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">

            <div style="background-color: #0e6258;" class="small-box" id="rcorners1">
                <div class="inner">

                    <h4 style="color: white; font-family: 'Segoe UI Light';">
                        <?php echo e('$ ' . $po_total_value_of_approved_pos_mtd); ?></h4>


                    <p style="color: white; font-family: 'Segoe UI Light';">Total Value of Approved POs - MTD</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="<?php echo e(route('dashboard.po_total_value_of_approved_pos_mtd')); ?>" class="small-box-footer">More info
                    <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">

            <div style="background-color: #19AF9D" class="small-box" id="rcorners1">
                <div class="inner">
                    <h4 style="color: white; font-family: 'Segoe UI Light';">
                        <?php echo e('$ ' . $po_total_value_of_supplied_pos_mtd); ?></h4>

                    <p style="color: white; font-family: 'Segoe UI Light';">
                        Total Value of Supplied POs - MTD</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
                <a href="<?php echo e(route('dashboard.po_total_value_of_supplied_pos_mtd')); ?>" class="small-box-footer">More info
                    <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">

            <div style="background-color: #0e6258;" class="small-box " id="rcorners1">
                <div class="inner">

                    <h4 style="color: white; font-family: 'Segoe UI Light';">
                        <?php echo e('$ ' . $po_total_value_of_pending_pos_mtd); ?></h4>


                    <p style="color: white; font-family: 'Segoe UI Light';">Total Value of Pending POs - MTD</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="<?php echo e(route('dashboard.po_total_value_of_pending_pos_mtd')); ?>" class="small-box-footer">More info
                    <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->

        <!-- ./col -->
    </div>

    

    


    <!-- /.row (main row) -->
</div>

<div class="container-fluid">
    <!-- Small boxes (Stat box) -->
    <div class="row">

        <!-- ./col -->
        <div class="col-lg-3 col-6">

            <div style="background-color: #0e6258;" class="small-box" id="rcorners1">
                <div class="inner">

                    <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e($po_approved_stock_requests); ?></h4>

                    <p style="color: white; font-family: 'Segoe UI Light';">Approved Stock Requests</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
                <a href="<?php echo e(route('dashboard.po_approved_stock_requests')); ?>" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- ./col -->
        <div class="col-lg-3 col-6">

            <div style="background-color:#19AF9D" class="small-box" id="rcorners1">
                <div class="inner">

                    <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e($po_approved_direct_requests); ?></h4>

                    <p style="color: white; font-family: 'Segoe UI Light';">Approved Direct Requests</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
                <a href="<?php echo e(route('dashboard.po_approved_direct_requests')); ?>" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- ./col -->

        <!-- ./col -->
        <div class="col-lg-3 col-6">

            <div style="background-color: #0e6258;" class="small-box" id="rcorners1">
                <div class="inner">

                    <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e($po_approved_pos); ?></h4>

                    <p style="color: white; font-family: 'Segoe UI Light';">Approved POs</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
                <a href="<?php echo e(route('dashboard.po_approved_pos')); ?>" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- ./col -->

        <div class="col-lg-3 col-6">

            <div class="small-box bg-danger" id="rcorners1">
                <div class="inner">

                    <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e($po_denied_requests); ?></h4>

                    <p style="color: white; font-family: 'Segoe UI Light';">Denied Requests </p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
                <a href="<?php echo e(route('dashboard.po_denied_requests')); ?>" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php /**PATH C:\Users\Hackman_GH\Desktop\New folder (2)\resources\views/dashboard/purchasing_officer.blade.php ENDPATH**/ ?>