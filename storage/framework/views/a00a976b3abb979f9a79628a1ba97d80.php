<DOCTYPE html>
    <html lang="en-US">

    <head>
        <meta charset="utf-8">
    </head>

    <body>
        <h3> Hi <?php echo e($data['name']); ?>, welcome to Cipree World! <br>
            </h3>
           <h3>  Please login to your new account with below details by clicking on the link provided. </h3>
            <h3>Email: <?php echo e($data['email']); ?> </h3>
            <h3>Password: <?php echo e($data['password']); ?></h3> <br>

            <p><a href="https://test.cipree.com/login">Login Page</a></p>
            <p id="p1"><a href="<?php echo e(route('password.request')); ?>" class="ml-auto">Note: You may be required to change your password after you first login. Click here.</a> </p>
            <p>We wish you the best of Cipree experience</p>

             <p>- Cipree Team</p>

    </body>

    </html>
<?php /**PATH C:\Users\Hackman_GH\Desktop\Zipped Projects\Laravel-10-roles-and-permissions-master\resources\views/emails/welcome.blade.php ENDPATH**/ ?>