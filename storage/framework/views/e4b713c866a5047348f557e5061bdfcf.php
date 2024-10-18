<title>Permissions</title>
<?php $__env->startSection('content'); ?>
    <div class="bg-light p-4 rounded">
        <h2>Permissions</h2>
        <div class="lead">
            Manage your permissions here.
            <a href="<?php echo e(route('permissions.create')); ?>" class="btn btn-primary btn-sm float-right">Add permissions</a>
        </div>

        <div class="mt-2">
            
        </div>
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

        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col" width="15%">Name</th>
                    <th scope="col">Guard</th>
                    <th scope="col" colspan="3" width="1%"></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($permission->name); ?></td>
                        <td><?php echo e($permission->guard_name); ?></td>
                        <td><a href="<?php echo e(route('permissions.edit', $permission->id)); ?>" class="btn btn-info btn-sm">Edit</a>
                        </td>
                        <td>
                            <form action="<?php echo e(route('permissions.destroy', $permission->id)); ?>" method="post">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" onclick="return confirm('Are you sure?')"
                                    class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/pensmqhz/test.cipree.com/resources/views/permissions/index.blade.php ENDPATH**/ ?>