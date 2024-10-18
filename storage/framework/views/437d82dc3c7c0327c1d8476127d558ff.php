
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

        <title>All Requested Purchases</title>

    </head>

    <body>
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>

        </div>


        <div class="card">
            <div class="card-header">
                <a href="<?php echo e(route('orders.create')); ?>" class="btn btn-primary float-right">Add New</a>
                
            </div>
            <!-- /.card-header -->

            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
    
                            <th>Supplier</th>
                            <th>Request Number</th>
                            <th>Requested Date</th>
                            <th>Status</th>
                             <th>View</th>
                             
                             <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'purchasing_officer|admin')): ?>
                                 <th>Edit</th>
                             <?php endif; ?>
                             <th>Delete</th>
                        </tr>
                    </thead>
                    <?php $__empty_1 = true; $__currentLoopData = $req_all; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $in): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tbody>
                            <tr>
                                <td><?php echo e($in->id ?? ''); ?></td>
                                <td><?php echo e($in->supplier->name ?? ''); ?></td>
                                  <td><?php echo e($in->request_number ?? ''); ?></td>
                                <td><?php echo e(date('d-m-Y (H:i)', strtotime($in->request_date))); ?></td>
                                <td><?php echo e($in->status ?? ''); ?></td>
                                <td>
                                    <a href="<?php echo e(route('orders.show', $in->id)); ?>" class="btn btn-secondary">View</a>

                                </td>
                                <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'purchasing_officer|admin')): ?>
                                <td>
                                    <a href="<?php echo e(route('orders.edit', $in->id)); ?>" class="btn btn-success">Edit</a>

                                </td>
                                <?php endif; ?>

                                <td>

                                    <form action="<?php echo e(route('orders.destroy', $in->id)); ?>" method="post">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" onclick="return confirm('Are you sure?')"
                                            class="btn btn-danger">Delete</button>
                                    </form>
                                </td>


                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td class="text-center" colspan="12">Data Not Found!</td>
                            </tr>
                    <?php endif; ?>

                    </tr>
                    </tbody>

                </table>
            </div>
            <?php echo e($req_all->links('pagination::bootstrap-4')); ?>

            <!-- /.card-body -->
            
        </div>



    </body>
    <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    <?php echo Toastr::message(); ?>


    </html>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Hackman_GH\Desktop\Projects\inventory-v2\resources\views/purchases/reqall.blade.php ENDPATH**/ ?>