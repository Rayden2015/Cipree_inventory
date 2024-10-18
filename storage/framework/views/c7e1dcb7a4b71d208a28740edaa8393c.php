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

        <title>GRN Lists</title>

    </head>

    <body>
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add-grn')): ?>
                <p>
                    <a href="<?php echo e(route('inventories.create')); ?>" class="btn btn-primary mr-3 my-3">Add</a>
                </p>
            <?php endif; ?>
        </div>


        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Goods Received Notes </h3>
                <form action="<?php echo e(route('inventory_home_search')); ?>" method="GET">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Search GRN or Waybill "
                            aria-describedby="basic-addon2" name="search">
                        <div class="input-group-append">
                            <button class="btn btn-secondary" type="submit">Search</button>
                        </div>
                    </div>
                </form>
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
                           
                            <th>GRN Number</th>
                            <th>WB</th>
                            <th>Supplier </th>
                            <th>Invoice Number</th>
                            <th>Purchase Type</th>
                           <th>Modified By</th>
                            <th>Last Updated</th>


                            <th>View</th>
                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-grn')): ?>
                                <th>Edit</th>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete-grn')): ?>
                                <th>Delete</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <?php $__empty_1 = true; $__currentLoopData = $inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $in): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tbody>
                            <tr>
                                <td><?php echo e($in->id); ?></td>
                              
                                <td><?php echo e($in->grn_number ?? ''); ?></td>
                                <td><?php echo e($in->waybill ?? ''); ?></td>
                                <td><?php echo e($in->supplier->name ?? ''); ?></td>
                                <td><?php echo e($in->invoice_number ?? ''); ?></td>
                                <td><?php echo e($in->trans_type ?? ''); ?></td>
                              <td><?php echo e($in->editedby->name ?? ''); ?></td>

                                <td><?php echo e(date('d-m-Y (H:i)', strtotime($in->updated_at))); ?></td>
                                
                                <td>
                                    <a href="<?php echo e(route('inventories.show', $in->id)); ?>" class="btn btn-primary">View</a>

                                </td>
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-grn')): ?>
                                    <td>
                                        <a href="<?php echo e(route('inventories.edit', $in->id)); ?>" class="btn btn-success">Edit</a>

                                    </td>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete-grn')): ?>
                                    <td>

                                        <form action="<?php echo e(route('inventories.destroy', $in->id)); ?>" method="post">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" onclick="return confirm('Are you sure?')"
                                                class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                <?php endif; ?>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td class="text-center" colspan="12">Item not available!</td>
                            </tr>
                    <?php endif; ?>

                    </tr>
                    </tbody>

                </table>
            </div>
            <?php echo e($inventories->links('pagination::bootstrap-4')); ?>

            <!-- /.card-body -->
        </div>




    </body>
    <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    <?php echo Toastr::message(); ?>


    </html>
    
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/pensmqhz/test.cipree.com/resources/views/inventories/index.blade.php ENDPATH**/ ?>