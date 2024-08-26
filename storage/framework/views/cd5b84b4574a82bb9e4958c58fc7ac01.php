<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1>Dashboard</h1>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'Super Admin')): ?>
                    <?php echo $__env->make('dashboard.superadmin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'Admin')): ?>
                    <?php echo $__env->make('dashboard.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'requester')): ?>
                <?php echo $__env->make('dashboard.requester', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'store_officer')): ?>
                    <?php echo $__env->make('dashboard.store_officer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'store_assistant')): ?>
                    <?php echo $__env->make('dashboard.store_officer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'purchasing_officer')): ?>
                    <?php echo $__env->make('dashboard.purchasing_officer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'authoriser')): ?>
                    <?php echo $__env->make('dashboard.authoriser', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'site_admin')): ?>
                    <?php echo $__env->make('dashboard.site_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'finance_officer')): ?>
                    <?php echo $__env->make('dashboard.finance_officer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>


            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/pensmqhz/test.cipree.com/resources/views/home.blade.php ENDPATH**/ ?>