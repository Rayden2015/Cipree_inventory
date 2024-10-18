<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('super-authoriser-dashboard')): ?>
<div class="container-fluid">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <div style="background-color: #19AF9D;" class="small-box" id="rcorners1">
                <div class="inner">
                    <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e($new_approvals); ?></h4>
                    <p style="color: white; font-family: 'Segoe UI Light';">New Approvals</p>
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
                    <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e($pending_request_approvals); ?></h4>
                    <p style="color: white; font-family: 'Segoe UI Light';">Pending Request Approvals</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="<?php echo e(route('dashboard.pending_request_approvals')); ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div style="background-color: #19AF9D;" class="small-box" id="rcorners1">
                <div class="inner">
                    <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e($pending_po_approvals); ?></h4>
                    <p style="color: white; font-family: 'Segoe UI Light';">Pending Purchase Order Approvals</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="<?php echo e(route('dashboard.pending_po_approvals')); ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div style="background-color: #0e6258;" class="small-box" id="rcorners1">
                <div class="inner">
                    <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e($pending_stock_approvals); ?></h4>
                    <p style="color: white; font-family: 'Segoe UI Light';">Pending Stock Approvals</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="<?php echo e(route('dashboard.pending_stock_approvals')); ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div style="background-color: #19AF9D;" class="small-box" id="rcorners1">
                <div class="inner">
                    <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e($approved_request); ?></h4>
                    <p style="color: white; font-family: 'Segoe UI Light';">Approved Requests</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
                <a href="<?php echo e(route('dashboard.approved_request')); ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div style="background-color: #0e6258;" class="small-box" id="rcorners1">
                <div class="inner">
                    <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e($approved_pos); ?></h4>
                    <p style="color: white; font-family: 'Segoe UI Light';">Approved Purchase Orders</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
                <a href="<?php echo e(route('dashboard.approved_pos')); ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div style="background-color: #19AF9D;" class="small-box" id="rcorners1">
                <div class="inner">
                    <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e($processed_pos); ?></h4>
                    <p style="color: white; font-family: 'Segoe UI Light';">Processed Purchase Orders</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
                <a href="<?php echo e(route('dashboard.processed_pos')); ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div style="background-color: #0e6258;" class="small-box" id="rcorners1">
                <div class="inner">
                    <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e($processed_request); ?></h4>
                    <p style="color: white; font-family: 'Segoe UI Light';">Processed Requests</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
                <a href="<?php echo e(route('dashboard.processed_request')); ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

    </div>
</div>

<?php endif; ?>
<?php /**PATH /home/pensmqhz/test.cipree.com/resources/views/dashboard/authoriser.blade.php ENDPATH**/ ?>