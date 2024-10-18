
<?php $__env->startSection('content'); ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
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

        <title>Stock Request Penidng</title>

    </head>

    <body>
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>

        </div>


        <div class="card">
            <div class="card-header">
                
            </div>
            <!-- /.card-header -->
            <?php if(session('success')): ?>
            <div class="alert alert-success">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>


            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                        <tr>
                            <th>ID</th>
                            <th>Request Number</th>
                            <th>Requested Date</th>
                            <th>Status</th>
                            <th>Enduser</th>
                            <th>Approval Status</th>
                            <th>View</th>
                            <?php if(Auth::user()->hasRole('store_officer')): ?>
                                <th>Edit</th>
                            <?php endif; ?>

                        </tr>
                        </tr>
                    </thead>
                    <?php $__empty_1 = true; $__currentLoopData = $sofficer_stock_request_pending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $in): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tbody>
                            <tr>
                                <td><?php echo e($in->id); ?></td>

                                <td><?php echo e($in->request_number ?? ''); ?></td>
                                <td><?php echo e(date('d-m-Y (H:i)', strtotime($in->request_date ?? ''))); ?></td>
                                <td><?php echo e($in->status ?? ''); ?></td>
                                <td><?php echo e($in->enduser->asset_staff_id ?? ''); ?></td>
                                <td><?php echo e($in->approval_status ?? 'Pending'); ?></td>

                                <td><a href="<?php echo e(route('sorders.store_list_view', $in->id)); ?>"
                                        class="btn btn-primary">View</a></td>
                                <?php if(Auth::user()->hasRole('store_officer')): ?>
                                    <?php if(empty($in->approval_status)): ?>
                                       <td>Pending</td>
                                    <?php elseif($in->approval_status == 'Approved'): ?>
                                        <td><a href="<?php echo e(route('stores.store_officer_edit', $in->id)); ?>"
                                                class="btn btn-secondary">Edit</a></td>
                                                <?php elseif($in->approval_status == 'Denied'): ?>   
                                                <td>Denied</td>       
                                    <?php endif; ?>
                                <?php endif; ?>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td class="text-center" colspan="12">Data Not Found!</td>
                            </tr>
                    <?php endif; ?>

                    </tr>
                    </tbody>

                </table>
            </div>
            <?php echo e($sofficer_stock_request_pending->links('pagination::bootstrap-4')); ?>

            <!-- /.card-body -->
        </div>



    </body>
    <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    <?php echo Toastr::message(); ?>


    </html>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/pensmqhz/test.cipree.com/resources/views/homepages/sofficer_stock_request_pending.blade.php ENDPATH**/ ?>