
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

        <title>Pending Request Approvals</title>

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
                            <th>ID</th>
    
                            <th>Supplier</th>
                            <th>Request Number</th>
                            <th>Requested Date</th>
                            <th>Enduser</th>
                            <th>Status</th>
                             <th>View</th>
                             <?php if(Auth::user()->hasRole('purchasing_officer') || Auth::user()->hasRole('admin')): ?>
                                 <th>Edit</th>
                             <?php endif; ?>
                        </tr>
                    </thead>
                    <?php $__empty_1 = true; $__currentLoopData = $pending_request_approvals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ap): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tbody>
                            <tr>
                                <td><?php echo e($ap->id ?? ''); ?></td>
                                <td><?php echo e($ap->supplier->name ?? ''); ?></td>
                                  <td><?php echo e($ap->request_number ?? ''); ?></td>
                                  <td><?php echo e(date('d-m-Y (H:i)', strtotime($ap->request_date))); ?></td>
                                  <td><?php echo e($ap->enduser->asset_staff_id ?? ''); ?></td>
                                <td><?php echo e($ap->status ?? ''); ?></td>

                                <td>
                                    <a href="<?php echo e(route('orders.show', $ap->id)); ?>" class="btn btn-secondary">View</a>

                                </td>
                                <?php if(Auth::user()->hasRole('purchasing_officer') || Auth::user()->hasRole('admin')): ?>
                                <td>
                                    <a href="<?php echo e(route('orders.edit', $ap->id)); ?>" class="btn btn-success">Edit</a>

                                </td>
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
            <?php echo e($pending_request_approvals->links('pagination::bootstrap-4')); ?>

            <!-- /.card-body -->
        </div>



    </body>
    <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    <?php echo Toastr::message(); ?>


    </html>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/pensmqhz/test.cipree.com/resources/views/homepages/pending_request_approvals.blade.php ENDPATH**/ ?>