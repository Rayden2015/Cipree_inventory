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

        <title>Supply History</title>

    </head>

    <body>
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>
            <p>
                <a href="<?php echo e(route('inventories.create')); ?>" class="btn btn-primary mr-3 my-3">Add</a>
            </p>
        </div>


        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Supply History</h3>
                <br>
                <form action="<?php echo e(route('stores.supply_history_search_item')); ?>" method="GET">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control"
                            placeholder="Search Description or Part Number, Enduser or Stock Code"
                            aria-describedby="basic-addon2" name="search">
                    </div>
                    <div class="input-group mb-3">
                        <input type="date" class="form-control" name="start_date">
                        <input type="date" class="form-control" name="end_date">
                    </div>
                    <div class="input-group mb-3">
                        <button class="btn btn-secondary" type="submit">Search</button>
                        <a href="<?php echo e(route('stores.supply_history')); ?>" class="btn btn-primary pull-left ml-2">
                            <h6>Reset</h6>
                        </a>
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
                            <th>Supply Date</th>
                            <th>SR Number</th>
                            <th>GRN Number</th>
                            <th>Description</th>
                            <th>Part Number</th>
                            <th>Stock Code</th>
                            <th>Quantity</th>

                            <th>Cost</th>
                            <th>End User</th>
                            <th>Location</th>
                            <?php if(Auth::user()->hasRole('Super Admin')): ?>
                                <th>Delete</th>
                            <?php endif; ?>

                        </tr>
                    </thead>
                    <?php $__empty_1 = true; $__currentLoopData = $total_cost_of_parts_within_the_month; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $in): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tbody>
                            <tr>
                                <td><?php echo e($in->id); ?></td>
                                <td><?php echo e(date('d-m-Y (H:i)', strtotime($in->sorder->delivered_on ?? ''))); ?></td>
                                <!-- Accessing delivered_on from sorder -->
                                <td><?php echo e($in->sorder->delivery_reference_number ?? ''); ?></td>
                                <!-- Accessing delivery_reference_number from sorder -->
                                <td><?php echo e($in->inventoryItem->inventory->grn_number ?? 'Not Found or Deleted'); ?></td>
                                <!-- Accessing GRN Number -->
                                <td><?php echo e($in->item->item_description ?? ' '); ?></td>
                                <!-- Accessing item_description from item -->
                                <td><?php echo e($in->item->item_part_number ?? ' '); ?></td>
                                <!-- Accessing item_part_number from item -->
                                <td><?php echo e($in->item->item_stock_code ?? ' '); ?></td>
                                <!-- Accessing item_stock_code from item -->
                                <td><?php echo e($in->qty_supplied ?? ''); ?></td>
                                <td><?php echo e($in->sub_total ?? ''); ?></td>
                                <td><?php echo e($in->sorder->enduser->asset_staff_id ?? 'Not Set'); ?></td>
                                <!-- Adjust as necessary -->
                                <td><?php echo e($in->inventoryItem->location->name ?? 'Not Set'); ?></td> <!-- Adjust as necessary -->

                                <?php if(Auth::user()->hasRole('Super Admin')): ?>
                                    <td>
                                        <form action="<?php echo e(route('sorderpart_delete', $in->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this item?')">Delete</button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td class="text-center" colspan="12">Item not available!</td>
                            </tr>
                    <?php endif; ?>


                    </tr>

                    </tbody>

                </table>
            </div>
            <?php echo e($total_cost_of_parts_within_the_month->links('pagination::bootstrap-4')); ?>

            <!-- /.card-body -->
        </div>

        </div>

        <script>
            document.getElementById('exportBtn').addEventListener('click', function(event) {
                event.preventDefault(); // Prevent the default form submission

                // Change the action attribute of the form
                document.getElementById('supplyHistoryForm').action = this.getAttribute('href');

                // Submit the form
                document.getElementById('supplyHistoryForm').submit();
            });
        </script>

    </body>
    <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    <?php echo Toastr::message(); ?>


    </html>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/pensmqhz/test.cipree.com/resources/views/stores/supply_history.blade.php ENDPATH**/ ?>