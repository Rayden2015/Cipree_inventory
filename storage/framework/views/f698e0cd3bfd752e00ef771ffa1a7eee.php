<?php
    $first_name = App\Http\Controllers\UserController::username();
    $logo = App\Http\Controllers\UserController::logo();
    $lastlogin = App\Http\Controllers\UserController::lastlogin();
    $user = auth()->user();
?>
<aside class="main-sidebar sidebar-light-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo e(route('home')); ?>" class="brand-link">
        <img src="<?php echo e(asset('images/company/' . $logo)); ?>" alt="AdminLTE Logo" class="brand-image"
            style="opacity: .8; width:210px; heigh:100px;"><br>
        <span style="text-align:center; font-weight:bold; padding-left:80px;">CIPREE</span> <br>
        <h6 style="text-align:center; font-weight:bold; padding-top:3px;"><?php echo e($first_name . ', '); ?>

            
            <?php if($user): ?>

                <?php $__currentLoopData = $user->getRoleNames(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span><?php echo e($role); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </h6>
        <h6 style="text-align:center;">Last Login: <?php echo e($lastlogin ? ' ' . $lastlogin->created_at : ''); ?></h6>

        
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <!-- SidebarSearch Form -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-item menu-open">
                    <a style="background-color: #0e6258" href="<?php echo e(route('home')); ?>" class="nav-link active">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-item">

                    
                    
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('company-module')): ?>
                    <li
                        class="nav-item <?php echo e(request()->routeIs('company.index', 'users.index', 'reviews.index', 'sites*','roles*','permissions*') ? 'menu-open' : ''); ?>">
                        <a href="#"
                            class="nav-link <?php echo e(request()->routeIs('company.index', 'users.index', 'reviews.index', 'sites*','roles*','permissions*') ? 'active' : ''); ?>">
                            <i>
                                <img src="<?php echo e(asset('assets/images/icons/comp.png')); ?>"width="26" height="26"
                                    alt="" />
                            </i>
                            <p>
                                Company
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('info')): ?>
                                <li class="nav-item">
                                    <a href="<?php echo e(route('company.index')); ?>"
                                        class="nav-link <?php echo e(request()->routeIs('company.index') ? 'active' : ''); ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Info</p>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('account')): ?>
                                <li class="nav-item">
                                    <a href="<?php echo e(route('users.index')); ?>"
                                        class="nav-link <?php echo e(request()->routeIs('users.index') ? 'active' : ''); ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Account</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reviews')): ?>
                                <li class="nav-item">
                                    <a href="<?php echo e(route('reviews.index')); ?>"
                                        class="nav-link <?php echo e(request()->routeIs('reviews.index') ? 'active' : ''); ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Reviews</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-site')): ?>
                                <li class="nav-item">
                                    <a href="<?php echo e(route('sites.index')); ?>"
                                        class="nav-link <?php echo e(request()->routeIs('sites.index') ? 'active' : ''); ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Sites</p>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-role')): ?>
                                <li class="nav-item">
                                    <a href="<?php echo e(route('roles.index')); ?>"
                                        class="nav-link <?php echo e(request()->routeIs('roles.index') ? 'active' : ''); ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Roles</p>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-permission')): ?>
                                <li class="nav-item">
                                    <a href="<?php echo e(route('permissions.index')); ?>"
                                        class="nav-link <?php echo e(request()->routeIs('permissions.index') ? 'active' : ''); ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Permissions</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            

                        </ul>
                    </li>
                <?php endif; ?>
                
                

                
                
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('endusers-module')): ?>
                    <li class="nav-item <?php echo e(request()->routeIs('endusers.index') ? 'menu-open' : ''); ?>">
                        <a href="#" class="nav-link <?php echo e(request()->routeIs('endusers.index') ? 'active' : ''); ?>">
                            <i>
                                <img src="<?php echo e(asset('assets/images/icons/enduser.jpg')); ?>"width="26" height="26"
                                    alt="" />
                            </i>
                            <p>
                                Endusers
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                            <li class="nav-item">
                                <a href="<?php echo e(route('endusers.index')); ?>"
                                    class="nav-link <?php echo e(request()->routeIs('endusers.index') ? 'active' : ''); ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Endusers</p>
                                </a>
                            </li>



                        </ul>
                    </li>
                <?php endif; ?>
                
                

                
                
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('suppliers-module')): ?>
                    <li class="nav-item <?php echo e(request()->routeIs('suppliers.index') ? 'menu-open' : ''); ?>">
                        <a href="#" class="nav-link <?php echo e(request()->routeIs('suppliers.index') ? 'active' : ''); ?>">
                            <i>
                                <img src="<?php echo e(asset('assets/images/icons/supplier.png')); ?>" width="26" height="26"
                                    alt="" />
                            </i>
                            <p>
                                Suppliers
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?php echo e(route('suppliers.index')); ?>"
                                    class="nav-link <?php echo e(request()->routeIs('suppliers.index') ? 'active' : ''); ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Suppliers</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                
                

                
                
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('inventory-management-module')): ?>
                    <li
                        class="nav-item <?php echo e(request()->is('items*', 'locations*', 'stores*', 'inventories*', 'categories*', 'spr_lists*', 'auth_spr_lists*') ? 'menu-open' : ''); ?>">
                        <a href="#"
                            class="nav-link <?php echo e(request()->is('items*', 'locations*', 'stores*', 'inventories*', 'categories*', 'spr_lists*', 'auth_spr_lists*') ? 'active' : ''); ?>">
                            <i>
                                <img src="<?php echo e(asset('assets/images/icons/invmanagement.png')); ?>" width="26"
                                    height="26" alt="" />
                            </i>
                            <p>
                                Inventory Management
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-item')): ?>

                                <li class="nav-item">
                                    <a href="<?php echo e(route('items.index')); ?>"
                                        class="nav-link <?php echo e(request()->routeIs('items.index') ? 'active' : ''); ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Items</p>
                                    </a>
                                </li>
                                <?php endif; ?>

                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-location')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('locations.index')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('locations.index') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Location</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('stock-request-lists')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('stores.store_officer_lists')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('stores.store_officer_lists') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Stock Request Lists</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add-grn')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('inventories.create')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('inventories.create') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Add GRN</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-item-group')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('categories.index')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('categories.index') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Item Group</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('stock-purchase-requests')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('spr_lists')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('spr_lists') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Stock Purchase Requests</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('authoriser-stock-purchase-requests')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('auth_spr_lists')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('auth_spr_lists') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Stock Purchase Requests</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                            </ul>
                        </li>
                    <?php endif; ?>

                    
                    
                    
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('navigate-module')): ?>
                        <li
                            class="nav-item <?php echo e(request()->routeIs('purchases.purchase_list', 'sorders.store_lists', 'auth_spr_lists', 'purchases.all_requests', 'purchases.req_all', 'inventories.inventory_item_history', 'stores.supply_history', 'authorise.all_requests', 'inventories.inventory_item_history', 'stores.supply_history', 'stores.requester_store_lists', 'stores.store_officer_lists', 'purchases.drafts', 'inventories.index', 'po_spr_lists', 'spr_pos') ? 'menu-open' : ''); ?>">
                            <a href="#"
                                class="nav-link <?php echo e(request()->routeIs('sorders.store_lists', 'auth_spr_lists', 'purchases.all_requests', 'purchases.purchase_list', 'purchases.req_all', 'inventories.inventory_item_history', 'stores.supply_history', 'authorise.all_requests', 'inventories.inventory_item_history', 'stores.supply_history', 'stores.requester_store_lists', 'stores.store_officer_lists', 'purchases.drafts', 'inventories.index', 'po_spr_lists', 'spr_pos') ? 'active' : ''); ?>">
                                <i>
                                    <img src="<?php echo e(asset('assets/images/icons/purchasing.png')); ?>" width="26"
                                        height="26" alt="" />
                                </i>
                                <p>
                                    Navigate
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('authoriser-request-lists')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('authorise.all_requests')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('authorise.all_requests') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Request Lists</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('authoriser-purchase-lists')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('purchases.purchase_list')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('purchases.purchase_list') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Purchase Lists</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('authoriser-stock-requests')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('sorders.store_lists')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('sorders.store_lists') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Stock Requests</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('authoriser-stock-purchase-requests')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('auth_spr_lists')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('auth_spr_lists') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Stock Purchase Requests</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('request-lists')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('purchases.all_requests')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('purchases.all_requests') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p> Request Lists</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('purchase-lists')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('purchases.purchase_list')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('purchases.purchase_list') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p> Purchase Lists</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('purchase-requests')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('purchases.req_all')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('purchases.req_all') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p> Purchase Requests</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('received-history')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('inventories.inventory_item_history')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('inventories.inventory_item_history') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p> Received History</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('supply-history')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('stores.supply_history')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('stores.supply_history') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p> Supply History</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('requester-stock-requests')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('stores.requester_store_lists')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('stores.requester_store_lists') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Stock Requests</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('purchase-orders')): ?>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Purchase Orders</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('transfer-requests')): ?>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Transfer Request</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('transfer-requests')): ?>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Transfer Requests</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('direct-purchase-requests')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('authorise.all_requests')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('authorise.all_requests') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p> Direct Purchase Requests</p>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('direct-purchase-requests')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('purchases.purchase_list')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('purchases.purchase_list') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Direct Purchase Orders</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('stock-purchase-requests')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('po_spr_lists')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('po_spr_lists') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Stock Purchase Requests</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                

                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('stock-purchase-orders')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('purchases.purchase_list')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('purchases.purchase_list') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Stock Purchase Orders</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                

                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('draft-purchase-orders')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('purchases.drafts')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('purchases.drafts') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Draft Purchase Orders</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                

                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('stock-purchase-request-pos')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('spr_pos')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('spr_pos') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Stock Purchase Request POs</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-grn')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('inventories.index')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('inventories.index') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>GRN</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                

                            </ul>
                        </li>
                    <?php endif; ?>
                    
                    
                    
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('purchase-management-module')): ?>
                        <li
                            class="nav-item  <?php echo e(request()->routeIs('suppliers.index', 'taxes.index', 'levies.index', 'items.index') ? 'menu-open' : ''); ?>">
                            <a href="#"
                                class="nav-link <?php echo e(request()->routeIs('suppliers.index', 'taxes.index', 'levies.index', 'items.index') ? 'active' : ''); ?>">
                                <i>
                                    <img src="<?php echo e(asset('assets/images/icons/pm.jpg')); ?>" width="26" height="26"
                                        alt="" />
                                </i>
                                <p>
                                    Purchase Management
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-supplier')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('suppliers.index')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('suppliers.index') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Suppliers/Vendors</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                            
                            <ul class="nav nav-treeview">
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-item')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('items.index')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('items.index') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Items/Parts</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                            
                            
                            <ul class="nav nav-treeview">
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-levy')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('taxes.index')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('taxes.index') ? ' active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Levies </p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-tax')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('levies.index')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('levies.index') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p> Taxes</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                            

                        </li>
                        
                    <?php endif; ?>
                    
                    
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reports-module')): ?>
                        <li class="nav-item <?php echo e(request()->routeIs('monthlyreport') ? 'menu-open' : ''); ?>">
                            <a href="#" class="nav-link <?php echo e(request()->routeIs('monthlyreport') ? 'active' : ''); ?>">
                                <i>
                                    <img src="<?php echo e(asset('assets/images/icons/reports.jpg')); ?>" width="26" height="26"
                                        alt="" />
                                </i>
                                <p>
                                    Reports
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">

                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('monthly-reports')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('monthlyreport')); ?>"
                                            class="nav-link <?php echo e(request()->routeIs('monthlyreport') ? 'active' : ''); ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Monthly Reports</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                

                            </ul>
                        </li>
                        

<?php endif; ?>

                    </ul>
                    

                    <a href="<?php echo e(route('myaccounts.index')); ?>"
                        class="nav-link <?php echo e(request()->routeIs('myaccounts.index') ? 'active' : ''); ?>">
                        <i>
                            <img src="<?php echo e(asset('assets/images/icons/myaccount.png')); ?>" width="26" height="26"
                                alt="" />
                        </i>
                        <span>My Account</span>
                    </a>

                    <a href="<?php echo e(route('logout')); ?>" class="nav-link <?php echo e(request()->routeIs('logout') ? 'active' : ''); ?>">
                        <i>
                            <img src="<?php echo e(asset('assets/images/icons/logout.jpg')); ?>" width="26" height="26"
                                alt="" />
                        </i>
                        <span>Logout</span>
                    </a>
                    
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>
<?php /**PATH /home/pensmqhz/test.cipree.com/resources/views/partials/menu.blade.php ENDPATH**/ ?>