<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('site-admin-dashboard')): ?>
        <div class="container-fluid">
            <br>
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-6">

                    <div style="background-color: #19AF9D;" class="small-box" id="rcorners1">
                        <div class="inner">

                            <h3 style="color:white;"><?php echo e($active_user_accounts); ?></h3>

                            <p>Active User Accounts</p>
                        </div>
                        <div class="icon">
                            
                        </div>
                        <a href="<?php echo e(route('dashboard.active_user_accounts')); ?>" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">

                    <div style="background-color: #0e6258;" class="small-box" id="rcorners1">
                        <div class="inner">

                            <h3 style="color:white;"><?php echo e($disabled_user_accounts); ?></h3>

                            <p>Disabled User Accounts</p>
                        </div>
                        <div class="icon">
                            
                        </div>
                        <a href="<?php echo e(route('dashboard.disabled_user_accounts')); ?>" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">

                    <div style="background-color: #19AF9D;" class="small-box" id="rcorners1">
                        <div class="inner">

                            <h3 style="color:white;"><?php echo e($active_endusers); ?></h3>

                            <p>Active Endusers</p>
                        </div>
                        <div class="icon">
                            
                        </div>
                        <a href="<?php echo e(route('dashboard.active_endusers')); ?>" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">

                    <div style="background-color: #0e6258;" class="small-box" id="rcorners1">
                        <div class="inner">
                            <h3 style="color:white;"><?php echo e($disabled_endusers); ?></h3>

                            <p>Disabled Endusers</p>
                        </div>
                        <div class="icon">
                            
                        </div>
                        <a href="<?php echo e(route('dashboard.disabled_endusers')); ?>" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
            </div>
            <div class="row">
                
                <div class="col-lg-3 col-6">

                    <div style="background-color: #0e6258;" class="small-box" id="rcorners1">
                        <div class="inner">

                            <h3 style="color:white;"><?php echo e($departments); ?></h3>

                            <p>Departments</p>
                        </div>
                        <div class="icon">
                            
                        </div>
                        <a href="<?php echo e(route('departmentslist.index')); ?>" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                
                
                <div class="col-lg-3 col-6">

                    <div style="background-color: #19AF9D;" class="small-box" id="rcorners1">
                        <div class="inner">

                            <h3 style="color:white;"><?php echo e($sections); ?></h3>

                            <p>Sections</p>
                        </div>
                        <div class="icon">
                            
                        </div>
                        <a href="<?php echo e(route('sectionslist.index')); ?>" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                
                
                <div class="col-lg-3 col-6">

                    <div style="background-color: #0e6258;" class="small-box" id="rcorners1">
                        <div class="inner">

                            <h3 style="color:white;"><?php echo e($user_complaints_open); ?></h3>

                            <p>User Complaints - Open</p>
                        </div>
                        <div class="icon">
                            
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                

                
                <div class="col-lg-3 col-6">

                    <div style="background-color:red;" class="small-box" id="rcorners1">
                        <div class="inner">

                            <h3 style="color:white;">0</h3>

                            <p>Ex</p>
                        </div>
                        <div class="icon">
                            
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                


                <!-- /.row (main row) -->
            </div>
        </div>
    <?php endif; ?>
    <?php /**PATH C:\Users\Hackman_GH\Desktop\Projects\inventory-v2\resources\views/dashboard/site_admin.blade.php ENDPATH**/ ?>