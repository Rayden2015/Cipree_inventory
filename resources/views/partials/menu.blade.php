<aside class="main-sidebar sidebar-light-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link">
        <img src="{{ asset('assets/images/icons/infinixel.png') }}" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-bold">Mur De Pierre</span>
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
                    <a href="{{ route('home') }}" class="nav-link active">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-item">

                    {{-- @role('admin')

                @endrole --}}
                <li class="nav-item">
                    <a href="{{route('property.allproperties')}}" class="nav-link">
                        <i>
                            <img src="{{ asset('assets/images/icons/property.jpg') }}" width="26" height="26"
                                alt="" />
                        </i>
                        <p>Properties</p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{ route('blog.allblogs') }}" class="nav-link">
                        <i>
                            <img src="{{ asset('assets/images/icons/blog.png') }}" width="26" height="26"
                                alt="" />
                        </i>
                        <p>Blogs</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link">
                        <i>
                            <img src="{{ asset('assets/images/icons/users.png') }}" width="26" height="26"
                                alt="" />
                        </i>
                        <p>Users</p>
                    </a>
                </li>

<<<<<<< HEAD


                        </ul>
                    </li>
                @endcan
                {{-- @endif --}}
                {{-- end of employees management tab --}}

                {{-- endusers tab --}}
                {{-- @if (Auth::user()->role->name == 'admin' || Auth::user()->role->name == 'site_admin') --}}
                @can('endusers-module')
                    <li class="nav-item {{ request()->routeIs('endusers.index') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('endusers.index') ? 'active' : '' }}">
                            <i>
                                <img src="{{ asset('assets/images/icons/enduser.jpg') }}"width="26" height="26"
                                    alt="" />
                            </i>
                            <p>
                                Endusers
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                            <li class="nav-item">
                                <a href="{{ route('endusers.index') }}"
                                    class="nav-link {{ request()->routeIs('endusers.index') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Endusers</p>
                                </a>
                            </li>



                        </ul>
                    </li>
                @endcan
                {{-- @endif --}}
                {{-- end of endusers tab --}}

                {{-- suppliers tab --}}
                {{-- @if (Auth::user()->role->name == 'admin') --}}
                @can('suppliers-module')
                    <li class="nav-item {{ request()->routeIs('suppliers.index') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('suppliers.index') ? 'active' : '' }}">
                            <i>
                                <img src="{{ asset('assets/images/icons/supplier.png') }}" width="26" height="26"
                                    alt="" />
                            </i>
                            <p>
                                Suppliers
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('suppliers.index') }}"
                                    class="nav-link {{ request()->routeIs('suppliers.index') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Suppliers</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan
                {{-- @endif --}}
                {{-- end if suppliers tab --}}

                {{-- inventory management tab --}}
                {{-- @if (Auth::user()->role->name == 'store_officer' || Auth::user()->role->name == 'store_assistant' || Auth::user()->role->name == 'site_admin') --}}
                @can('inventory-management-module')
                    <li
                        class="nav-item {{ request()->is('items*', 'locations*', 'stores*', 'inventories*', 'categories*', 'spr_lists*', 'auth_spr_lists*','itemspersite','product_history','product_history_show*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->is('items*', 'locations*', 'stores*', 'inventories*', 'categories*', 'spr_lists*', 'auth_spr_lists*','itemspersite','product_history','product_history_show*') ? 'active' : '' }}">
                            <i>
                                <img src="{{ asset('assets/images/icons/invmanagement.png') }}" width="26"
                                    height="26" alt="" />
                            </i>
                            <p>
                                Inventory Management
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            {{-- @if (Auth::user()->role->name == 'store_officer' || Auth::user()->role->name == 'store_assistant') --}}
                            @can('view-item')
                                <li class="nav-item">
                                    <a href="{{ route('items.index') }}"
                                        class="nav-link {{ request()->routeIs('items.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Items</p>
                                    </a>
                                </li>
                            @endcan

                            {{-- @can('view-item')
                                <li class="nav-item">
                                    <a href="{{ route('itemspersite') }}"
                                        class="nav-link {{ request()->routeIs('itemspersite') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Items Per Site</p>
                                    </a>
                                </li>
                            @endcan --}}


                            @can('item-history')
                            <li class="nav-item">
                                <a href="{{ route('product_history') }}"
                                    class="nav-link {{ request()->routeIs('product_history') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Item History</p>
                                </a>
                            </li>
                        @endcan


                            {{-- @if (Auth::user()->role->name == 'store_officer' || Auth::user()->role->name == 'store_assistant') --}}
                            @can('view-location')
                                <li class="nav-item">
                                    <a href="{{ route('locations.index') }}"
                                        class="nav-link {{ request()->routeIs('locations.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Location</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @endif --}}
                            {{-- @if (Auth::user()->role->name == 'store_officer' || Auth::user()->role->name == 'store_assistant') --}}
                            @can('stock-request-lists')
                                <li class="nav-item">
                                    <a href="{{ route('stores.store_officer_lists') }}"
                                        class="nav-link {{ request()->routeIs('stores.store_officer_lists') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Stock Request Lists</p>
                                    </a>
                                </li>
                            @endcan
                            @can('add-grn')
                                <li class="nav-item">
                                    <a href="{{ route('inventories.create') }}"
                                        class="nav-link {{ request()->routeIs('inventories.create') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Add GRN</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @endif --}}
                            {{-- @if (Auth::user()->role->name == 'store_officer' || Auth::user()->role->name == 'store_assistant') --}}
                            @can('view-item-group')
                                <li class="nav-item">
                                    <a href="{{ route('categories.index') }}"
                                        class="nav-link {{ request()->routeIs('categories.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Item Group</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @endif --}}
                            {{-- @if (Auth::user()->role->name == 'store_assistant' || Auth::user()->role->name == 'store_officer' || Auth::user()->role->name == 'authoriser') --}}
                            @can('stock-purchase-requests')
                                <li class="nav-item">
                                    <a href="{{ route('spr_lists') }}"
                                        class="nav-link {{ request()->routeIs('spr_lists') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Stock Purchase Requests</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @endif --}}
                            {{-- @if (Auth::user()->role->name == 'authoriser') --}}
                            @can('authoriser-stock-purchase-requests')
                                <li class="nav-item">
                                    <a href="{{ route('auth_spr_lists') }}"
                                        class="nav-link {{ request()->routeIs('auth_spr_lists') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Stock Purchase Requests</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @endif --}}
                        </ul>
                    </li>
                @endcan

                {{-- end of inventory management tab --}}
                {{-- @endif --}}
                {{-- navigate --}}
                @can('navigate-module')
                    <li
                        class="nav-item {{ request()->routeIs('purchases.purchase_list', 'sorders.store_lists', 'auth_spr_lists', 'purchases.all_requests', 'purchases.req_all', 'inventories.inventory_item_history', 'stores.supply_history', 'authorise.all_requests', 'inventories.inventory_item_history', 'stores.supply_history', 'stores.requester_store_lists', 'stores.store_officer_lists', 'purchases.drafts', 'inventories.index', 'po_spr_lists', 'spr_pos') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->routeIs('sorders.store_lists', 'auth_spr_lists', 'purchases.all_requests', 'purchases.purchase_list', 'purchases.req_all', 'inventories.inventory_item_history', 'stores.supply_history', 'authorise.all_requests', 'inventories.inventory_item_history', 'stores.supply_history', 'stores.requester_store_lists', 'stores.store_officer_lists', 'purchases.drafts', 'inventories.index', 'po_spr_lists', 'spr_pos') ? 'active' : '' }}">
                            <i>
                                <img src="{{ asset('assets/images/icons/purchasing.png') }}" width="26"
                                    height="26" alt="" />
                            </i>
                            <p>
                                Navigate
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            {{-- @if (Auth::user()->role->name == 'authoriser') --}}
                            {{-- authoriser-request-lists --}}
                            @can('authoriser-request-lists')
                                <li class="nav-item">
                                    <a href="{{ route('authorise.all_requests') }}"
                                        class="nav-link {{ request()->routeIs('authorise.all_requests') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Request Lists</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- authoriser-purchase-lists --}}
                            @can('authoriser-purchase-lists')
                                <li class="nav-item">
                                    <a href="{{ route('purchases.purchase_list') }}"
                                        class="nav-link {{ request()->routeIs('purchases.purchase_list') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Purchase Lists</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- authoriser-stock-requests --}}
                            @can('authoriser-stock-requests')
                                <li class="nav-item">
                                    <a href="{{ route('sorders.store_lists') }}"
                                        class="nav-link {{ request()->routeIs('sorders.store_lists') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Stock Requests</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- authoriser-stock-purchase-requests --}}
                            @can('authoriser-stock-purchase-requests')
                                <li class="nav-item">
                                    <a href="{{ route('auth_spr_lists') }}"
                                        class="nav-link {{ request()->routeIs('auth_spr_lists') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Stock Purchase Requests</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @endif
                        @if (Auth::user()->role->name == 'store_officer' || Auth::user()->role->name == 'store_assistant') --}}
                            @can('request-lists')
                                <li class="nav-item">
                                    <a href="{{ route('purchases.all_requests') }}"
                                        class="nav-link {{ request()->routeIs('purchases.all_requests') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p> Request Lists</p>
                                    </a>
                                </li>
                            @endcan
                            @can('purchase-lists')
                                <li class="nav-item">
                                    <a href="{{ route('purchases.purchase_list') }}"
                                        class="nav-link {{ request()->routeIs('purchases.purchase_list') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p> Purchase Lists</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @endif

                        @if (Auth::user()->role->name == 'requester') --}}
                            @can('purchase-requests')
                                <li class="nav-item">
                                    <a href="{{ route('purchases.req_all') }}"
                                        class="nav-link {{ request()->routeIs('purchases.req_all') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p> Purchase Requests</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @endif
                        @if (Auth::user()->role->name == 'store_officer' || Auth::user()->role->name == 'store_assistant') --}}
                            @can('received-history')
                                <li class="nav-item">
                                    <a href="{{ route('inventories.inventory_item_history') }}"
                                        class="nav-link {{ request()->routeIs('inventories.inventory_item_history') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p> Received History</p>
                                    </a>
                                </li>
                            @endcan
                            @can('supply-history')
                                <li class="nav-item">
                                    <a href="{{ route('stores.supply_history') }}"
                                        class="nav-link {{ request()->routeIs('stores.supply_history') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p> Supply History</p>
                                    </a>
                                </li>
                            @endcan

                          
                            @can('requester-stock-requests')
                                <li class="nav-item">
                                    <a href="{{ route('stores.requester_store_lists') }}"
                                        class="nav-link {{ request()->routeIs('stores.requester_store_lists') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Stock Requests</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @endif



                        @if (Auth::user()->role->name == 'requester') --}}
                            @can('purchase-orders')
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Purchase Orders</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @endif

                        @if (Auth::user()->role->name == 'requester') --}}
                            @can('transfer-requests')
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Transfer Request</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @endif

                        @if (Auth::user()->role->name == 'site_admin') --}}
                            @can('transfer-requests')
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Transfer Requests</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @endif
                        @if (Auth::user()->role->name == 'purchasing_officer') --}}
                            @can('direct-purchase-requests')
                                <li class="nav-item">
                                    <a href="{{ route('authorise.all_requests') }}"
                                        class="nav-link {{ request()->routeIs('authorise.all_requests') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p> Direct Purchase Requests</p>
                                    </a>
                                </li>
                            @endcan

                            {{-- @if (Auth::user()->role->name == 'purchasing_officer') --}}
                            @can('direct-purchase-requests')
                                <li class="nav-item">
                                    <a href="{{ route('purchases.purchase_list') }}"
                                        class="nav-link {{ request()->routeIs('purchases.purchase_list') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Direct Purchase Orders</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @endif --}}
                            {{-- @if (Auth::user()->role->name == 'purchasing_officer') --}}
                            @can('stock-purchase-requests')
                                <li class="nav-item">
                                    <a href="{{ route('po_spr_lists') }}"
                                        class="nav-link {{ request()->routeIs('po_spr_lists') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Stock Purchase Requests</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @endif --}}

                            {{-- @if (Auth::user()->role->name == 'purchasing_officer') --}}
                            @can('stock-purchase-orders')
                                <li class="nav-item">
                                    <a href="{{ route('purchases.purchase_list') }}"
                                        class="nav-link {{ request()->routeIs('purchases.purchase_list') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Stock Purchase Orders</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @endif --}}

                            {{-- @if (Auth::user()->role->name == 'purchasing_officer') --}}
                            @can('draft-purchase-orders')
                                <li class="nav-item">
                                    <a href="{{ route('purchases.drafts') }}"
                                        class="nav-link {{ request()->routeIs('purchases.drafts') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Draft Purchase Orders</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @endif --}}

                            {{-- @if (Auth::user()->role->name == 'purchasing_officer') --}}
                            @can('stock-purchase-request-pos')
                                <li class="nav-item">
                                    <a href="{{ route('spr_pos') }}"
                                        class="nav-link {{ request()->routeIs('spr_pos') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Stock Purchase Request POs</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @endif

                        @if (Auth::user()->role->name == 'store_officer' || Auth::user()->role->name == 'store_assistant') --}}
                            @can('view-grn')
                                <li class="nav-item">
                                    <a href="{{ route('inventories.index') }}"
                                        class="nav-link {{ request()->routeIs('inventories.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>GRN</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @endif --}}

                        </ul>
                    </li>
                @endcan
                {{-- end of navigate module --}}
                {{-- purchase model --}}
                {{-- @if (Auth::user()->role->name == 'site_admin' || Auth::user()->role->name == 'purchasing_officer') --}}
                @can('purchase-management-module')
                    <li
                        class="nav-item  {{ request()->routeIs('suppliers.index', 'taxes.index', 'levies.index', 'items.index') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->routeIs('suppliers.index', 'taxes.index', 'levies.index', 'items.index') ? 'active' : '' }}">
                            <i>
                                <img src="{{ asset('assets/images/icons/pm.jpg') }}" width="26" height="26"
                                    alt="" />
                            </i>
                            <p>
                                Purchase Management
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            {{-- authoriser module --}}
                            @can('view-supplier')
                                <li class="nav-item">
                                    <a href="{{ route('suppliers.index') }}"
                                        class="nav-link {{ request()->routeIs('suppliers.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Suppliers/Vendors</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                        {{-- @if (Auth::user()->role->name == 'purchasing_officer') --}}
                        <ul class="nav nav-treeview">
                            {{-- authoriser module --}}
                            @can('view-item')
                                <li class="nav-item">
                                    <a href="{{ route('items.index') }}"
                                        class="nav-link {{ request()->routeIs('items.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Items/Parts</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                        {{-- @endif --}}
                        {{-- @if (Auth::user()->role->name == 'purchasing_officer') --}}
                        <ul class="nav nav-treeview">
                            {{-- authoriser module --}}
                            @can('view-levy')
                                <li class="nav-item">
                                    <a href="{{ route('taxes.index') }}"
                                        class="nav-link {{ request()->routeIs('taxes.index') ? ' active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Levies </p>
                                    </a>
                                </li>
                            @endcan
                            @can('view-tax')
                                <li class="nav-item">
                                    <a href="{{ route('levies.index') }}"
                                        class="nav-link {{ request()->routeIs('levies.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p> Taxes</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                        {{-- @endif --}}

                    </li>
                    {{-- @endif --}}
                @endcan
                {{-- end purchase model  --}}
=======
>>>>>>> d29d2b411f82256fddca149984e6cef765ac5ec9
                {{-- reports --}}



            </ul>
            </li>

            {{-- end of reports --}}



            </ul>
            {{--  --}}

            {{-- <a href="{{ route('myaccounts.index') }}" class="nav-link"> <i>
                    <img src="{{ asset('assets/images/icons/myaccount.png') }}" width="26" height="26"
                        alt="" />
                </i>
                <span>My Account</span></a> --}}



            <a href="{{ route('logout') }}" class="nav-link"> <i>
                    <img src="{{ asset('assets/images/icons/logout.jpg') }}" width="26" height="26"
                        alt="" />
                </i>
                <span>Logout</span></a>

            {{--  --}}
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
