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

        <title>Pending Stock Approvals</title>

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
                            <th>Requested By</th>
                            <th>EndUser</th>
                            <th>SOD</th>
                            <th>Requested Date</th>
                            <th>Status</th>
                            <th>Approval Status</th>
                            <th>View</th>
                            
                            <?php if(Auth::user()->hasRole('purchasing_officer') ||
                            Auth::user()->hasRole('Super Authoriser') ||
                            Auth::user()->hasRole('store_officer')): ?>
                        <th>Edit</th>
                    <?php endif; ?>
                     <?php if(Auth::user()->hasRole('purchasing_officer') || Auth::user()->hasRole('admin')): ?>
                        <th>Delete</th>
                    <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>

                        <?php $__empty_1 = true; $__currentLoopData = $pending_stock_approvals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            

                            <tr>
                                <td><?php echo e($rq->id); ?></td>
                                <td><?php echo e($rq->request_by->name ?? ''); ?></td>
                                <td><?php echo e($rq->enduser->asset_staff_id ?? 'Not Set'); ?></td>
                                <td><?php echo e($rq->request_number ?? ''); ?></td>
                                <td><?php echo e(date('d-m-Y (H:i)', strtotime($rq->request_date))); ?></td>
                                <td><?php echo e($rq->status ?? ''); ?></td>
                                
                                <td>
                                   <?php echo e($rq->approval_status ?? 'Pending'); ?>

                                </td>
                                <td>
                                    <a href="<?php echo e(route('sorders.authoriser_store_list_view_dash', $rq->id)); ?>" class="btn btn-secondary">View</a>

                                </td>
                             
                               
                                <?php if(Auth::user()->hasRole('purchasing_officer') ||
                            Auth::user()->hasRole('Super Authoriser') ||
                            Auth::user()->hasRole('store_officer')): ?>
                            <td>
                                <a href="<?php echo e(route('sorders.store_list_edit', $rq->id)); ?>"
                                    class="btn btn-success">Edit</a>

                            </td>
                        <?php endif; ?>


                        <?php if(Auth::user()->hasRole('purchasing_officer') || Auth::user()->hasRole('admin')): ?>
                            <td>

                                <form action="<?php echo e(route('purchases.purchase_destroy', $rq->id)); ?>"
                                    method="post">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" onclick="return confirm('Are you sure?')"
                                        class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        <?php endif; ?>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td class="text-center" colspan="12">Data Not Found!</td>
                            </tr>
                        <?php endif; ?>


                    </tbody>
                </table>
            </div>
            <?php echo e($pending_stock_approvals->links('pagination::bootstrap-4')); ?>

            <!-- /.card-body -->
        </div>



    </body>
    <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    <?php echo Toastr::message(); ?>


    </html>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/pensmqhz/test.cipree.com/resources/views/homepages/pending_stock_approvals.blade.php ENDPATH**/ ?>