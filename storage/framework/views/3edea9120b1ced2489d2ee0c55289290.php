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

            <title>Items List</title>

        </head>

        <body>
            <div class="title d-flex justify-content-between">
                <h3 class="page-title"></h3>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add-item')): ?>
                    <p>
                        <a href="<?php echo e(route('items.create')); ?>" class="btn btn-primary mr-3 my-3">Add</a>
                    </p>
              <?php endif; ?>
            </div>


            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Items </h3>
                    <form action="<?php echo e(route('item_search')); ?>" method="GET">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control"
                                placeholder="Search Description, Part Number, Stock Code" aria-describedby="basic-addon2"
                                name="search">
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
                                <th>Description</th>
                                <th>Stock Code</th>
                                <th>Part Number</th>
                                <th>Created By</th>
                                <th>Qty In Stock</th>
                                
                                <th>View</th>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-item')): ?>
                                    <th>Edit</th>
                              <?php endif; ?>
                              

                            </tr>
                        </thead>
                        <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tbody>
                                <tr>
                                    <td><?php echo e($ct->id); ?></td>
                                    <td><?php echo e($ct->item_description ?? ''); ?></td>
                                    <td><?php echo e($ct->item_stock_code ?? ''); ?></td>
                                    <td><?php echo e($ct->item_part_number ?? ''); ?></td>
                                    <td><?php echo e($ct->user->name ?? ''); ?></td>

                                    <td><?php echo e($ct->stock_quantity ?? ''); ?></td>

                                    
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-item')): ?>
                                    <td><a href="<?php echo e(route('items.show', $ct->id)); ?>" class="btn btn-primary">View</a></td>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-item')): ?>
                                        <td>
                                            <a href="<?php echo e(route('items.edit', $ct->id)); ?>" class="btn btn-success">Edit</a>

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
                <?php echo e($items->links('pagination::bootstrap-4')); ?>

                <!-- /.card-body -->
            </div>




        </body>
        <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
        <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
        <?php echo Toastr::message(); ?>


        </html>
  
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/pensmqhz/test.cipree.com/resources/views/items/index.blade.php ENDPATH**/ ?>