<?php $__env->startSection('content'); ?>

    <head>
        <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.2/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://unpkg.com/bs-brain@2.0.2/components/logins/login-5/assets/css/login-5.css">
    </head>
    <style>
        #webname div {
            /* width: 560px; */

        }

        @media (min-width:900px) {
            .myimage {
                padding-left: 140px;
                padding-right: 120px;
                width: 600px;
                height: 300px;
            }


        }
    </style>

    <body style="background-color:white; font-family: 'Segoe UI Light';">
        <!-- Login 5 - Bootstrap Brain Component -->

        <div class="container">
            <div class="card border-light-subtle shadow-sm">
                <div class="row g-0">

                    
                    <div class="col-12 col-md-6">
                        <div class="d-flex align-items-center justify-content-center h-100"
                            style="background-color:#0e6258">
                            <div class=" gy-md-4 overflow-hidden">

                                <h2 class="h1 mb-4" style="color:#A9D18E; width:100%; text-align:center;">WELCOME <span
                                        style="font-family: 'Brush Script MT', cursive;"> to</span>
                                    CIPREE</h2>
                                <img class="img-fluid rounded mb-4 myimage" loading="lazy"
                                    src="<?php echo e(asset('assets/images/icons/test_green.png')); ?>" width="390" height="80"
                                    alt="BootstrapBrain Logo">
                                
                                <p style="font-size: 16px; color:#C5E0B4; text-align:center;">Website: www.cipree.com
                                    Email:info@cipree.com / service@cipree.com</p>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6" style="background-color: #F0F8FB">
                        <div class="card-body p-3 p-md-4 p-xl-5">
                            <div class="row">
                                <?php if(Session::has('errors')): ?>
                                    <div class="alert alert-danger"style="color:red;">
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php echo e($error); ?><br />
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="col-12">
                                    <div class="mb-5">
                                        <h3 style="font-weight:none; text-align:center;">Sign in</h3>
                                    </div>
                                </div>
                            </div>
                            <form method="POST" action="<?php echo e(route('login')); ?>">
                                <?php echo csrf_field(); ?>
                                <div class="row gy-3 gy-md-4 overflow-hidden">
                                    <div class="col-12">
                                        <label for="email" class="form-label" style="font-weight: bold;">Email <span
                                                class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="email" id="email"
                                            placeholder="name@example.com" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="password" class="form-label" style="font-weight: bold;">Password
                                            <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" name="password" id="password"
                                            value="" required>
                                    </div>
                                    <div class="col-12">
                                        
                                    </div>
                                    <div class="col-12">
                                        <div class="d-grid">
                                            <button style="background-color:#0e6258" class="btn bsb-btn-xl btn-primary"
                                                type="submit">Sign In</button>
                                        </div>
                                        <br>
                                        <p id="p1"><a href="<?php echo e(route('password.request')); ?>" class="ml-auto">Forgot
                                                password?</a> </p>
                                    </div>
                                </div>
                            </form>
                            <div class="row">
                                <div class="col-12">
                                    <hr class="mt-5 mb-4 border-secondary-subtle">
                                    <div class="d-flex gap-2 gap-md-4 flex-column flex-md-row justify-content-md-end">

                                    </div>
                                </div>
                            </div>
                            <div class="row">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </body>


    </html>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app2', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/pensmqhz/test.cipree.com/resources/views/auth/login.blade.php ENDPATH**/ ?>