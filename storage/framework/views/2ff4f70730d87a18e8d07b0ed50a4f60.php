

<?php $__env->startSection('content'); ?>
    <?php if(Auth::user()->hasRole('requester') ||
            Auth::user()->hasRole('store_officer') ||
            Auth::user()->hasRole('purchasing_officer')): ?>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->
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

            <title>Stock Request</title>

        </head>

        <body>
            <div class="content-page">
                <div class="content">
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

                    <!-- Start Content-->
                    <div class="container-fluid">

                        <div class="row">
                            <div class="col-12">
                                <div class="card-box">

                                    <h2>Check Item Availability <p style="float:right;"><i class="fa fa-shopping-cart"
                                                aria-hidden="true"></i> <span class="badge badge-pill badge-danger">
                                                <?php if(count((array) session('cart')) == '0'): ?>
                                                    <a href="<?php echo e(route('stores.request_search')); ?>">
                                                        <?php echo e(count((array) session('cart'))); ?>

                                                    </a>
                                                <?php elseif(session('cart') > '0'): ?>
                                                    <a href="<?php echo e(route('cart')); ?>">
                                                        <?php echo e(count((array) session('cart'))); ?>

                                                    </a>
                                                <?php endif; ?>
                                            </span> </p>
                                    </h2>

                                    <form action="" method="GET">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control"
                                                placeholder="Enter Description or Part number or Stock Code"
                                                aria-describedby="basic-addon2" name="search">
                                            <div class="input-group-append">
                                                <button class="btn btn-secondary" type="submit">Search</button>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="responsive-table-plugin" style="padding-bottom: 15px;">
                                        <?php if(Session::has('success')): ?>
                                            <div class="alert alert-success">
                                                <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">&times;</button>
                                                <strong>Success!</strong> <?php echo e(Session::get('success')); ?>

                                            </div>
                                        <?php endif; ?>

                                        <div class="table-rep-plugin">
                                            <div class="table-responsive" data-pattern="priority-columns">
                                                <table id="tech-companies-1" class="table table-striped mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            
                                                            <?php if(!Auth::user()->hasRole('requester')): ?>
                                                                <th data-priority="1">Location</th>
                                                            <?php endif; ?>
                                                            <th>Description</th>
                                                            <th>Part Number</th>
                                                            <th>Stock Code</th>
                                                            <th>Stock Quantity</th>
                                                            <?php if(!Auth::user()->hasRole('requester')): ?>
                                                                <th>Age</th>
                                                            <?php endif; ?>
                                                            
                                                            <th>Site</th>
                                                            <th>Action</th>



                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if(isset($inventory)): ?>
                                                            <?php $__currentLoopData = $inventory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                

                                                                <tr>
                                                                    <td><?php echo e($order->id); ?></td>
                                                                    
                                                                    <?php if(!Auth::user()->hasRole('requester')): ?>
                                                                        <td><?php echo e($order->location->name ?? 'not set'); ?></td>
                                                                    <?php endif; ?>
                                                                    <th><?php echo e($order->item_description ?? 'not set '); ?></th>
                                                                    <th><?php echo e($order->item_part_number ?? 'not set '); ?></th>
                                                                    <th><?php echo e($order->item_stock_code ?? 'not set '); ?></th>
                                                                    <td><?php echo e($order->quantity ?? ''); ?></td>

                                                                    <?php
                                                                    $date = \Carbon\Carbon::parse($order->created_at);
                                                                    $difference = $date->diffInDays(\Carbon\Carbon::now());
                                                                    ?>
                                                                    
                                                                    <?php if(!Auth::user()->hasRole('requester')): ?>
                                                                        <td><?php echo e($difference); ?></td>
                                                                    <?php endif; ?>

                                                                    <td><?php echo e($order->site->name ?? ''); ?></td>
                                                                    
                                                                    <?php if(Auth::user()->site->id != $order->site_id): ?>
                                                                        <td>N/A</td>
                                                                    <?php else: ?>
                                                                        <td><a href="<?php echo e(route('add.to.cart', $order->id)); ?>"
                                                                                class="btn btn-primary">Add</a></td>
                                                                    <?php endif; ?>
                                                                
                                                                </tr>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php else: ?>
                                                            <tr>
                                                                <th colspan="6">
                                                                    
                                                                </th>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>



                                        </div>

                                    </div>
                                    
                                    

                                </div>

                            </div>
                        </div>
                        <!-- end row -->

                    </div> <!-- container-fluid -->

                </div> <!-- content -->
            </div>
        </body>
        <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
        <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
        <?php echo Toastr::message(); ?>


        </html>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/pensmqhz/test.cipree.com/resources/views/purchases/request_search.blade.php ENDPATH**/ ?>