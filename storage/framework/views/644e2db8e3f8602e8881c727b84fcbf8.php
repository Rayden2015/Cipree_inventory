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

        <title>Document</title>
       
    </head>

    <body>
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>
            
        </div>


        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Company Info</h3>
            </div>
            <!-- /.card-header -->

            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Phone</th>
                            <th>VAT No</th>
                            <th>Email</th>
                            <th>Website</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <?php $__empty_1 = true; $__currentLoopData = $company; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tbody>
 <tr>
                                <td><?php echo e($cp->id); ?></td>
                                <td><?php echo e($cp->name); ?></td>
                                <td><?php echo e($cp->address); ?></td>
                                <td><?php echo e($cp->phone); ?></td>
                                <td><?php echo e($cp->vat_no); ?></td>
                                <td><?php echo e($cp->email); ?></td>
                                <td><?php echo e($cp->website); ?></td>
                                <td>
                                    <a href="<?php echo e(route('company.edit',$cp->id)); ?>" class ="btn btn-success">Edit</a>

                                </td>
                                <td>

                                    <form action="<?php echo e(route('company.destroy', $cp->id)); ?>"
                                        method="post">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" onclick="return confirm('Are you sure?')"
                                            class="btn btn-danger">Delete</button>
                                    </form></td>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td class="text-center" colspan="12">Data Not Found!</td>
                            </tr>
                    <?php endif; ?>

                    </tr>
                    </tbody>

                </table>
            </div>
            <!-- /.card-body -->
        </div>


    </body>
    <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    <?php echo Toastr::message(); ?>


    </html>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Hackman_GH\Desktop\Zipped Projects\Laravel-10-roles-and-permissions-master\resources\views/company/index.blade.php ENDPATH**/ ?>