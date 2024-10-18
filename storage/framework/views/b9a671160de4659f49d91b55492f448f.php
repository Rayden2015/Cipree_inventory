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

<h5 style="color:white;">Check Item Availability <p style="float:right;"><i class="fa fa-shopping-cart" aria-hidden="true"></i> <span
            class="badge badge-pill badge-danger">
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
</h5>

<form action="<?php echo e(route('stores.requester_search')); ?>" method="GET">
    <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Enter Description or Part number or Stock Code"
            aria-describedby="basic-addon2" required name="search">
        <div class="input-group-append">
            <button class="btn btn-secondary" type="submit">Search</button>
        </div>
    </div>
</form>
<?php /**PATH C:\Users\Hackman_GH\Desktop\New folder (2)\resources\views/purchases/requesterhomesearch.blade.php ENDPATH**/ ?>