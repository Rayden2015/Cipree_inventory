<?php $__env->startSection('content'); ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
        <link rel="stylesheet" href="https://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
            integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA=="
            crossorigin="anonymous" />
    </head>

    <body>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="float-start">
                            Add New User
                        </div>
                        <div class="float-end">
                            <a href="<?php echo e(route('users.index')); ?>" class="btn btn-primary btn-sm">&larr; Back</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo e(route('users.store')); ?>" method="post">
                            <?php echo csrf_field(); ?>

                           
                          
                            <div class="mb-3 row">
                                <label for="name" class="col-md-4 col-form-label text-md-end text-start">Name</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="name" name="name" value="<?php echo e(old('name')); ?>">
                                    <?php if($errors->has('name')): ?>
                                        <span class="text-danger"><?php echo e($errors->first('name')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="email" class="col-md-4 col-form-label text-md-end text-start">Email
                                    Address</label>
                                <div class="col-md-6">
                                    <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="email" name="email" value="<?php echo e(old('email')); ?>">
                                    <?php if($errors->has('email')): ?>
                                        <span class="text-danger"><?php echo e($errors->first('email')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="phone" class="col-md-4 col-form-label text-md-end text-start">Phone</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="phone" name="phone" value="<?php echo e(old('phone')); ?>">
                                    <?php if($errors->has('phone')): ?>
                                        <span class="text-danger"><?php echo e($errors->first('phone')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="address" class="col-md-4 col-form-label text-md-end text-start">Address</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="address" name="address" value="<?php echo e(old('address')); ?>">
                                    <?php if($errors->has('address')): ?>
                                        <span class="text-danger"><?php echo e($errors->first('address')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="dob" class="col-md-4 col-form-label text-md-end text-start">Date of
                                    Birth</label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control <?php $__errorArgs = ['dob'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="dob" name="dob" value="<?php echo e(old('dob')); ?>">
                                    <?php if($errors->has('dob')): ?>
                                        <span class="text-danger"><?php echo e($errors->first('dob')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>


                            <div class="mb-3 row">
                                <label for="site" class="col-md-4 col-form-label text-md-end text-start">Site
                                    Name</label>
                                <div class="col-md-6">
                                    <label>Site Name: </label>
                                    <select id="site_id" type="text"
                                        class="form-control <?php $__errorArgs = ['site_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="site_id"
                                        autocomplete="site_id" autofocus required>
                                        <option value="" selected hidden>Please Select</option>

                                        <?php $__currentLoopData = $sites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option <?php echo e(old('st') == $st->id ? 'selected' : ''); ?>

                                                value="<?php echo e($st->id); ?>">
                                                <?php echo e($st->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="status" class="col-md-4 col-form-label text-md-end text-start">Status</label>
                                <div class="col-md-6">

                                    <select class="select form-control" id="status" name="status" required data-fouc
                                        data-placeholder="Choose..">
                                        <option value=""></option>
                                        <option <?php echo e(old('status') == 'Active' ? 'selected' : ''); ?> value="Active">Active
                                        </option>
                                        <option <?php echo e(old('status') == 'Inactive' ? 'selected' : ''); ?> value="Inactive">
                                            Inactive</option>
                                    </select>
                                </div>
                            </div>

                            

                        <div class="mb-3 row">
                            <label for="roles" class="col-md-4 col-form-label text-md-end text-start">Roles</label>
                            <div class="col-md-6">
                                <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php if($role->name != 'Super Admin'): ?>
                                        <div class="form-check">
                                            <input class="form-check-input <?php $__errorArgs = ['roles'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                type="checkbox"
                                                name="roles[]"
                                                id="role_<?php echo e($role->id); ?>"
                                                value="<?php echo e($role->name); ?>"
                                                <?php echo e(in_array($role->name, old('roles') ?? []) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="role_<?php echo e($role->id); ?>">
                                                <?php echo e($role->name); ?>

                                            </label>
                                        </div>
                                    <?php else: ?>
                                        <?php if(Auth::user()->hasRole('Super Admin')): ?>
                                            <div class="form-check">
                                                <input class="form-check-input <?php $__errorArgs = ['roles'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    type="checkbox"
                                                    name="roles[]"
                                                    id="role_<?php echo e($role->id); ?>"
                                                    value="<?php echo e($role->name); ?>"
                                                    <?php echo e(in_array($role->name, old('roles') ?? []) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="role_<?php echo e($role->id); ?>">
                                                    <?php echo e($role->name); ?>

                                                </label>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div>No roles available</div>
                                <?php endif; ?>
                        
                                <?php if($errors->has('roles')): ?>
                                    <span class="text-danger"><?php echo e($errors->first('roles')); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        

                            <div class="mb-3 row">
                                <label for="staff_id" class="col-md-4 col-form-label text-md-end text-start">Staff
                                    ID</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control <?php $__errorArgs = ['staff_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="staff_id" name="staff_id" value="<?php echo e(old('staff_id')); ?>">
                                    <?php if($errors->has('staff_id')): ?>
                                        <span class="text-danger"><?php echo e($errors->first('staff_id')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>


                            <div class="mb-3 row">
                                <input type="submit" class="col-md-3 offset-md-5 btn btn-primary" value="Add User">
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
        </script>
    </body>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
        integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    <?php echo Toastr::message(); ?>



    </html>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Hackman_GH\Desktop\Zipped Projects\Laravel-10-roles-and-permissions-master\resources\views/users/create.blade.php ENDPATH**/ ?>