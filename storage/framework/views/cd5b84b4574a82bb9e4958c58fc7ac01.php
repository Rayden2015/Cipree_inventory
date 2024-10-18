<?php $__env->startSection('content'); ?>
    <style>
        #rcorners1 {
            border-radius: 25px;

        }
    </style>
    <?php
        $first_name = App\Http\Controllers\UserController::username();
        $logo = App\Http\Controllers\UserController::logo();
    ?>
    <br>
    
    <?php echo $__env->make('dashboard.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('dashboard.authoriser', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('dashboard.finance_officer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('dashboard.purchasing_officer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('dashboard.requester', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('dashboard.site_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('dashboard.store_officer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('dashboard.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <script>
        setTimeout(function() {
            window.location.reload();
        }, 60000);
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/pensmqhz/test.cipree.com/resources/views/home.blade.php ENDPATH**/ ?>