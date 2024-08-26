<?php $__env->startSection('content'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
        integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA=="
        crossorigin="anonymous" />
</head>

<body>
        <div class="card">
            <div class="card-header">Manage Users</div>

            <div class="card-body">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-user')): ?>
                    <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary float-right btn-sm my-2"><i
                            class="bi bi-plus-circle"></i> Add New User</a>
                <?php endif; ?>
                <input type="text" id="search" class="form-control" placeholder="Search by name or email">
                <br>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">S#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Roles</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody id="user-list">
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <th scope="row"><?php echo e($loop->iteration); ?></th>
                                <td><?php echo e($user->name); ?></td>
                                <td><?php echo e($user->email); ?></td>
                                <td>
                                    <?php $__empty_2 = true; $__currentLoopData = $user->getRoleNames(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                        <span class="badge bg-primary"><?php echo e($role); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form action="<?php echo e(route('users.destroy', $user->id)); ?>" method="post">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>

                                        <a href="<?php echo e(route('users.show', $user->id)); ?>" class="btn btn-warning btn-sm"><i
                                                class="bi bi-eye"></i> Show</a>

                                        <?php if(in_array('Super Admin', $user->getRoleNames()->toArray() ?? [])): ?>
                                            <?php if(Auth::user()->hasRole('Super Admin')): ?>
                                                <a href="<?php echo e(route('users.edit', $user->id)); ?>"
                                                    class="btn btn-primary btn-sm"><i class="bi bi-pencil-square"></i>
                                                    Edit</a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-user')): ?>
                                                <a href="<?php echo e(route('users.edit', $user->id)); ?>"
                                                    class="btn btn-primary btn-sm"><i class="bi bi-pencil-square"></i> Edit</a>
                                            <?php endif; ?>

                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete-user')): ?>
                                                <?php if(Auth::user()->id != $user->id): ?>
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Do you want to delete this user?');"><i
                                                            class="bi bi-trash"></i> Delete</button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <td colspan="5">
                                <span class="text-danger">
                                    <strong>No User Found!</strong>
                                </span>
                            </td>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <script>
            $(document).ready(function() {
                $('#search').on('keyup', function() {
                    let query = $(this).val();
                    if (query.length > 2) { // start searching after 3 characters
                        $.ajax({
                            url: "<?php echo e(route('search.users')); ?>",
                            type: "GET",
                            data: {
                                query: query
                            },
                            success: function(data) {
                                $('#user-list').html('');
                                if (data.length > 0) {
                                    data.forEach(user => {
                                        $('#user-list').append(`
                                            <tr>
                                                <td>${user.id}</td>
                                                <td>${user.name}</td>
                                                   <td>${user.email}</td>
                                               
                                             
                                               <td>
                        <?php $__empty_1 = true; $__currentLoopData = $user->getRoleNames(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <span class="badge bg-primary"><?php echo e($role); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <?php endif; ?>
                    </td>
                                                <td>
                                                    <a href="/users/${user.id}/edit" class="btn btn-success">Edit</a>
                                                </td>
                                                <td>
                                                    <form action="/users/${user.id}" method="post">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        `);
                                    });
                                } else {
                                    $('#user-list').append(
                                        '<tr><td class="text-center" colspan="8">Data Not Found!</td></tr>'
                                    );
                                }
                            }
                        });
                    } else {
                        // Optionally reset the list or show initial users
                        $('#user-list').html(`<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $us): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($us->id); ?></td>
                                <td><?php echo e($us->name); ?></td>
                            <td><?php echo e($us->email); ?></td>
                              <td>
                        <?php $__empty_1 = true; $__currentLoopData = $user->getRoleNames(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <span class="badge bg-primary"><?php echo e($role); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <?php endif; ?>
                    </td>
                                <td>
                                    <a href="<?php echo e(route('users.edit', $us->id)); ?>" class="btn btn-success">Edit</a>
                                </td>
                                <td>
                                    <form action="<?php echo e(route('users.destroy', $us->id)); ?>" method="post">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>`);
                    }
                });
            });
        </script>
  </body>
  <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
  <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
  <?php echo Toastr::message(); ?>


  </html>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Hackman_GH\Desktop\Zipped Projects\Laravel-10-roles-and-permissions-master\resources\views/users/index.blade.php ENDPATH**/ ?>