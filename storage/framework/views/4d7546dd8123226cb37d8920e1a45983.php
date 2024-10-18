
<?php $__env->startSection('content'); ?>
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

        <title>Stock Lists</title>

    </head>

    <body>
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>

        </div>


        <div class="card">
            <div class="card-header">
                
                <a href="<?php echo e(route('stores.request_search')); ?>" class="btn btn-primary float-right">Add New</a>
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
                            <th data-priority="1">Request Number</th>
                            <th>Requester</th>
                            <th>Date</th>
                            <th>Approval Status</th>
                            <th>Supply Status</th>
                            <th>Enduser</th>
                            <th>Edit</th>
                            
                            <th>View Details</th>
                            <?php if(Auth::user()->hasRole('store_officer')): ?>
                                <th>Edit</th>
                            <?php endif; ?>
                            <th>Delete</th>


                        </tr>
                    </thead>
                    <tbody>

                        <?php $__currentLoopData = $requester_store_lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($order->id); ?></td>
                                <td><?php echo e($order->request_number ?? ''); ?></td>
                                <td><?php echo e($order->request_by->name ?? ''); ?></td>
                                <td><?php echo e(date('d-m-Y (H:i)', strtotime($order->request_date))); ?></td>
                                <td><?php echo e($order->approval_status ?? 'Pending'); ?></td>
                                <td><?php echo e($order->status ?? ''); ?></td>
                                <td><?php echo e($order->enduser->asset_staff_id ?? ''); ?></td>
                                <?php if($order->approval_status == 'Denied' || $order->approval_status == 'Approved'): ?>
                                    <td>N/A </td>
                                <?php elseif($order->approval_status === null): ?>
                                    <td><a class="btn btn-secondary"
                                            href="<?php echo e(route('stores.requester_edit', $order->id)); ?>">Edit</a></td>
                                <?php endif; ?>

                                <td><a href="<?php echo e(route('sorders.requester_store_list_view', $order->id)); ?>"
                                        class="btn btn-primary">View</a></td>
                                        <?php if(Auth::user()->hasRole('store_officer')): ?>
                                    <td><a href="<?php echo e(route('stores.store_officer_edit', $order->id)); ?>"
                                            class="btn btn-secondary">Edit</a></td>
                                <?php endif; ?>
                                <?php if($order->status == 'Supplied'): ?>
                                    <td>Items Already Supplied</td>
                                <?php else: ?>
                                    <td>
                                        <form action="<?php echo e(route('sorders.destroy', $order->id)); ?>" method="post">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" onclick="return confirm('Are you sure?')"
                                                class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                <?php endif; ?>



                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                    </tbody>
                </table>
                <?php echo $requester_store_lists->links(); ?>

            </div>
            <!-- /.card-body -->
        </div>



    </body>
    <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    <?php echo Toastr::message(); ?>


    </html>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Hackman_GH\Desktop\Projects\inventory-v2\resources\views/stores/requester_store_lists.blade.php ENDPATH**/ ?>