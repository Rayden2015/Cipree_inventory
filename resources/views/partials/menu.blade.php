@php
    $first_name = App\Http\Controllers\UserController::username();
    $logo = App\Http\Controllers\UserController::logo();
    $lastlogin = App\Http\Controllers\UserController::lastlogin();
    $user = auth()->user();
@endphp
<aside class="main-sidebar sidebar-light-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link tenant-branding">
        <img
            src="{{ $tenantBranding['logo'] ?? asset('images/branding/cipree.png') }}"
            alt="{{ $tenantBranding['name'] ?? 'CIPREE' }} Logo"
            class="tenant-logo"
            style="opacity:.9;"
        />

        <div class="tenant-name">
            {{ $tenantBranding['name'] ?? 'CIPREE' }}
        </div>

        <div class="tenant-user-roles">
            <div style="font-weight:700;">{{ $first_name }}</div>
            @if ($user)
                @foreach ($user->getRoleNames() as $role)
                    <span class="role-pill">{{ $role }}</span>
                @endforeach
            @endif
        </div>

        <div class="tenant-last-login">
            Last Login: {{ $lastlogin ? \Carbon\Carbon::parse($lastlogin->created_at)->format('d-M-Y H:i') : 'First Login' }}
        </div>
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
                <li class="nav-item {{ request()->routeIs('home') ? 'menu-open' : '' }}">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" style="{{ request()->routeIs('home') ? 'background-color: #0e6258' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                {{-- Tenant Management (Super Admin only) --}}
                @if(Auth::user()->isSuperAdmin())
                <li class="nav-item {{ request()->routeIs('super-admin.*', 'tenants.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('super-admin.*', 'tenants.*') ? 'active' : '' }}" style="{{ request()->routeIs('super-admin.*', 'tenants.*') ? 'background-color: #0e6258' : '' }}">
                        <i class="nav-icon fas fa-building"></i>
                        <p>
                            Super Admin
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('super-admin.dashboard') }}" class="nav-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}" style="{{ request()->routeIs('super-admin.dashboard') ? 'background-color: #0e6258' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('tenants.index') }}" class="nav-link {{ request()->routeIs('tenants.index', 'tenants.create', 'tenants.store') ? 'active' : '' }}" style="{{ request()->routeIs('tenants.index', 'tenants.create', 'tenants.store') ? 'background-color: #0e6258' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Tenants</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('tenants.create') }}" class="nav-link {{ request()->routeIs('tenants.create') ? 'active' : '' }}" style="{{ request()->routeIs('tenants.create') ? 'background-color: #0e6258' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Create Tenant</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                {{-- Tenant Admin navigation is consolidated under Organization Setup to avoid duplication --}}

                {{-- Organization Setup: tenant-wide setup (Company + Org + People + Assets + Roles/Perms) --}}
                @if(Auth::user()->canAny([
                    // Company/setup
                    'company-module', 'info', 'account', 'reviews', 'view-site', 'bulk-mails', 'view-uom', 'view-role', 'view-permission',
                    'view-user', 'add-user', 'edit-user', 'delete-user',
                    // Org master data
                    'view-department', 'add-department', 'view-section', 'add-section',
                    // Employees
                    'view-employee', 'add-employee',
                    // End users split
                    'view-asset', 'view-personnel', 'endusers-module'
                ]))
                    <li class="nav-item {{ request()->routeIs('company.*', 'users.*', 'reviews.*', 'sites.*', 'roles.*', 'permissions.*', 'uom.*', 'send.bulk.email', 'departmentslist.*', 'sectionslist.*', 'employees.*', 'endusers.*') || request()->is('endusersearch*', 'endusersort*', 'enduser_show*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('company.*', 'users.*', 'reviews.*', 'sites.*', 'roles.*', 'permissions.*', 'uom.*', 'send.bulk.email', 'departmentslist.*', 'sectionslist.*', 'employees.*', 'endusers.*') || request()->is('endusersearch*', 'endusersort*', 'enduser_show*') ? 'active' : '' }}"
                            style="{{ request()->routeIs('company.*', 'users.*', 'reviews.*', 'sites.*', 'roles.*', 'permissions.*', 'uom.*', 'send.bulk.email', 'departmentslist.*', 'sectionslist.*', 'employees.*', 'endusers.*') || request()->is('endusersearch*', 'endusersort*', 'enduser_show*') ? 'background-color: #0e6258' : '' }}">
                            <i class="nav-icon fas fa-sitemap"></i>
                            <p>
                                Organization Setup
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            {{-- Tenant Admin quick links (tenant-scoped) --}}
                            @if(Auth::user()->isTenantAdmin() && !Auth::user()->isSuperAdmin())
                                <li class="nav-item">
                                    <a href="{{ route('tenant-admin.dashboard') }}"
                                       class="nav-link {{ request()->routeIs('tenant-admin.dashboard') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Tenant Dashboard</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('tenant-admin.settings') }}"
                                       class="nav-link {{ request()->routeIs('tenant-admin.settings', 'tenant-admin.update-settings') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Tenant Settings (Branding)</p>
                                    </a>
                                </li>
                            @endif

                            {{-- Company --}}
                            @can('info')
                                <li class="nav-item">
                                    <a href="{{ (Auth::user()->isTenantAdmin() && !Auth::user()->isSuperAdmin()) ? route('tenant-admin.settings') : route('company.index') }}"
                                       class="nav-link {{ request()->routeIs('company.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Company Info</p>
                                    </a>
                                </li>
                            @endcan
                            @canany(['account', 'view-user'])
                                <li class="nav-item">
                                    <a href="{{ (Auth::user()->isTenantAdmin() && !Auth::user()->isSuperAdmin()) ? route('tenant-admin.users.index') : route('users.index') }}"
                                       class="nav-link {{ request()->routeIs('users.*', 'tenant-admin.users.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Users</p>
                                    </a>
                                </li>
                            @endcanany
                            @can('view-site')
                                <li class="nav-item">
                                    <a href="{{ (Auth::user()->isTenantAdmin() && !Auth::user()->isSuperAdmin()) ? route('tenant-admin.sites.index') : route('sites.index') }}"
                                       class="nav-link {{ request()->routeIs('sites.*', 'tenant-admin.sites.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Sites</p>
                                    </a>
                                </li>
                            @endcan
                            @can('bulk-mails')
                                <li class="nav-item">
                                    <a href="{{ route('send.bulk.email') }}"
                                       class="nav-link {{ request()->routeIs('send.bulk.email*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Bulk Emails</p>
                                    </a>
                                </li>
                            @endcan
                            @can('reviews')
                                <li class="nav-item">
                                    <a href="{{ route('reviews.index') }}"
                                       class="nav-link {{ request()->routeIs('reviews.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Reviews</p>
                                    </a>
                                </li>
                            @endcan
                            @can('view-uom')
                                <li class="nav-item">
                                    <a href="{{ route('uom.index') }}"
                                       class="nav-link {{ request()->routeIs('uom.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>UoM</p>
                                    </a>
                                </li>
                            @endcan
                            @can('view-role')
                                <li class="nav-item">
                                    <a href="{{ route('roles.index') }}"
                                       class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Roles</p>
                                    </a>
                                </li>
                            @endcan
                            @can('view-permission')
                                <li class="nav-item">
                                    <a href="{{ route('permissions.index') }}"
                                       class="nav-link {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Permissions</p>
                                    </a>
                                </li>
                            @endcan

                            <li class="nav-header">ORGANIZATION</li>
                            @can('view-department')
                                <li class="nav-item">
                                    <a href="{{ route('departmentslist.index') }}"
                                        class="nav-link {{ request()->routeIs('departmentslist.index', 'departmentslist.edit', 'departmentslist.update') ? 'active' : '' }}"
                                        style="{{ request()->routeIs('departmentslist.index', 'departmentslist.edit', 'departmentslist.update') ? 'background-color: #0e6258' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Departments</p>
                                    </a>
                                </li>
                            @endcan
                            @can('add-department')
                                <li class="nav-item">
                                    <a href="{{ route('departmentslist.create') }}"
                                        class="nav-link {{ request()->routeIs('departmentslist.create', 'departmentslist.store') ? 'active' : '' }}"
                                        style="{{ request()->routeIs('departmentslist.create', 'departmentslist.store') ? 'background-color: #0e6258' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Create Department</p>
                                    </a>
                                </li>
                            @endcan

                            @can('view-section')
                                <li class="nav-item">
                                    <a href="{{ route('sectionslist.index') }}"
                                        class="nav-link {{ request()->routeIs('sectionslist.index', 'sectionslist.edit', 'sectionslist.update') ? 'active' : '' }}"
                                        style="{{ request()->routeIs('sectionslist.index', 'sectionslist.edit', 'sectionslist.update') ? 'background-color: #0e6258' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Sections</p>
                                    </a>
                                </li>
                            @endcan
                            @can('add-section')
                                <li class="nav-item">
                                    <a href="{{ route('sectionslist.create') }}"
                                        class="nav-link {{ request()->routeIs('sectionslist.create', 'sectionslist.store') ? 'active' : '' }}"
                                        style="{{ request()->routeIs('sectionslist.create', 'sectionslist.store') ? 'background-color: #0e6258' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Create Section</p>
                                    </a>
                                </li>
                            @endcan

                            {{-- Employees --}}
                            @can('view-employee')
                                <li class="nav-item">
                                    <a href="{{ route('employees.index') }}"
                                       class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Employees</p>
                                    </a>
                                </li>
                            @endcan
                            @can('add-employee')
                                <li class="nav-item">
                                    <a href="{{ route('employees.create') }}"
                                       class="nav-link {{ request()->routeIs('employees.create', 'employees.store') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Create Employee</p>
                                    </a>
                                </li>
                            @endcan

                            {{-- End Users --}}
                            @can('view-asset')
                                <li class="nav-item">
                                    <a href="{{ route('endusers.assets') }}"
                                       class="nav-link {{ request()->routeIs('endusers.assets') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Assets</p>
                                    </a>
                                </li>
                            @endcan
                            @can('endusers-module')
                                <li class="nav-item">
                                    <a href="{{ route('endusers.index') }}"
                                       class="nav-link {{ request()->routeIs('endusers.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>All Endusers</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif
                {{-- end of organization setup --}}

                {{-- suppliers tab --}}
                {{-- @if (Auth::user()->role->name == 'admin') --}}
                @if(Auth::user()->canAny(['suppliers-module', 'view-supplier', 'add-supplier', 'edit-supplier', 'delete-supplier']))
                    <li class="nav-item {{ request()->routeIs('suppliers.*') || request()->is('supplier_search*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('suppliers.*') || request()->is('supplier_search*') ? 'active' : '' }}" style="{{ request()->routeIs('suppliers.*') || request()->is('supplier_search*') ? 'background-color: #0e6258' : '' }}">
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
                                    class="nav-link {{ request()->routeIs('suppliers.*') || request()->is('supplier_search*') ? 'active' : '' }}" style="{{ request()->routeIs('suppliers.*') || request()->is('supplier_search*') ? 'background-color: #0e6258' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Suppliers</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                {{-- @endif --}}
                {{-- end if suppliers tab --}}

                {{-- inventory management tab --}}
                {{-- @if (Auth::user()->role->name == 'store_officer' || Auth::user()->role->name == 'store_assistant' || Auth::user()->role->name == 'site_admin') --}}
                @if(Auth::user()->canAny([
                    'inventory-management-module',
                    'view-item', 'add-item', 'edit-item', 'delete-item',
                    'view-location', 'add-location', 'edit-location', 'delete-location',
                    'view-item-group', 'add-item-group', 'edit-item-group', 'delete-item-group',
                    'view-grn', 'add-grn', 'edit-grn', 'delete-grn',
                    'stock-request-lists', 'stock-purchase-requests',
                    'approve-inventory-correction', 'view-inventory-audit-log'
                ]))
                    <li
                        class="nav-item {{ request()->is('items*', 'locations*', 'stores*', 'inventories*', 'categories*', 'spr_*', 'auth_spr_*', 'itemspersite*', 'product_history*', 'store_officer_*', 'store_list_*', 'item_search*', 'inventory_*') || request()->routeIs('items.*', 'locations.*', 'stores.*', 'inventories.*', 'categories.*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->is('items*', 'locations*', 'stores*', 'inventories*', 'categories*', 'spr_*', 'auth_spr_*', 'itemspersite*', 'product_history*', 'store_officer_*', 'store_list_*', 'item_search*', 'inventory_*') || request()->routeIs('items.*', 'locations.*', 'stores.*', 'inventories.*', 'categories.*') ? 'active' : '' }}" style="{{ request()->is('items*', 'locations*', 'stores*', 'inventories*', 'categories*', 'spr_*', 'auth_spr_*', 'itemspersite*', 'product_history*', 'store_officer_*', 'store_list_*', 'item_search*', 'inventory_*') || request()->routeIs('items.*', 'locations.*', 'stores.*', 'inventories.*', 'categories.*') ? 'background-color: #0e6258' : '' }}">
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
                                        class="nav-link {{ request()->routeIs('items.*') || request()->is('item_search*', 'product_history*', 'itemspersite*') ? 'active' : '' }}" style="{{ request()->routeIs('items.*') || request()->is('item_search*', 'product_history*', 'itemspersite*') ? 'background-color: #0e6258' : '' }}">
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
                                    class="nav-link {{ request()->routeIs('product_history*') || request()->is('product_history*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Item History</p>
                                </a>
                            </li>
                        @endcan


                            {{-- @if (Auth::user()->role->name == 'store_officer' || Auth::user()->role->name == 'store_assistant') --}}
                            @can('view-location')
                                <li class="nav-item">
                                    <a href="{{ route('locations.index') }}"
                                        class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}" style="{{ request()->routeIs('locations.*') ? 'background-color: #0e6258' : '' }}">
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
                                        class="nav-link {{ request()->routeIs('stores.store_officer_lists') || request()->is('store_officer_edit*', 'store_list_edit*', 'store_list_view*') ? 'active' : '' }}" style="{{ request()->routeIs('stores.store_officer_lists') || request()->is('store_officer_edit*', 'store_list_edit*', 'store_list_view*') ? 'background-color: #0e6258' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Stock Request Lists</p>
                                    </a>
                                </li>
                            @endcan
                            @can('add-grn')
                                <li class="nav-item">
                                    <a href="{{ route('inventories.create') }}"
                                        class="nav-link {{ request()->routeIs('inventories.create', 'inventories.store') ? 'active' : '' }}">
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
                                        class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" style="{{ request()->routeIs('categories.*') ? 'background-color: #0e6258' : '' }}">
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
                                        class="nav-link {{ request()->routeIs('spr_lists') || request()->is('spr_*', 'store_officer_spr_*', 'so_spr_*') ? 'active' : '' }}">
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
                                        class="nav-link {{ request()->routeIs('auth_spr_lists') || request()->is('auth_spr_*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Stock Purchase Requests</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @endif --}}
                        </ul>
                    </li>
                @endif

                {{-- end of inventory management tab --}}
                {{-- @endif --}}
                {{-- navigate --}}
                @if(Auth::user()->canAny([
                    'navigate-module',
                    'authoriser-request-lists', 'authoriser-purchase-lists', 'authoriser-stock-requests', 'authoriser-stock-purchase-requests',
                    'request-lists', 'purchase-lists', 'purchase-requests', 'requester-stock-requests',
                    'received-history', 'supply-history',
                    'direct-purchase-requests', 'direct-purchase-orders',
                    'stock-purchase-orders', 'draft-purchase-orders', 'stock-purchase-request-pos',
                    'view-grn',
                    'approve-inventory-correction', 'view-inventory-audit-log'
                ]))
                    <li
                        class="nav-item {{ request()->routeIs('purchases.*', 'sorders.*', 'auth_spr_lists', 'authorise.*', 'stores.*', 'po_spr_lists', 'spr_pos*') || request()->is('purchase_*', 'all_requests*', 'req_all*', 'inventory_history*', 'supply_history*', 'requester_store_*', 'store_officer_*', 'drafts*', 'po_spr_*', 'spr_pos*', 'generatePDF*', 'generatesorderPDF*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->routeIs('purchases.*', 'sorders.*', 'auth_spr_lists', 'authorise.*', 'stores.*', 'po_spr_lists', 'spr_pos*') || request()->is('purchase_*', 'all_requests*', 'req_all*', 'inventory_history*', 'supply_history*', 'requester_store_*', 'store_officer_*', 'drafts*', 'po_spr_*', 'spr_pos*', 'generatePDF*', 'generatesorderPDF*') ? 'active' : '' }}" style="{{ request()->routeIs('purchases.*', 'sorders.*', 'auth_spr_lists', 'authorise.*', 'stores.*', 'po_spr_lists', 'spr_pos*') || request()->is('purchase_*', 'all_requests*', 'req_all*', 'inventory_history*', 'supply_history*', 'requester_store_*', 'store_officer_*', 'drafts*', 'po_spr_*', 'spr_pos*', 'generatePDF*', 'generatesorderPDF*') ? 'background-color: #0e6258' : '' }}">
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
                                        class="nav-link {{ request()->routeIs('authorise.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Request Lists</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- authoriser-purchase-lists --}}
                            @can('authoriser-purchase-lists')
                                <li class="nav-item">
                                    <a href="{{ route('purchases.purchase_list') }}"
                                        class="nav-link {{ request()->routeIs('purchases.purchase_list') || request()->is('purchase_list*', 'purchase_edit*', 'showlist*', 'editlist*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Purchase Lists</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- authoriser-stock-requests --}}
                            @can('authoriser-stock-requests')
                                <li class="nav-item">
                                    <a href="{{ route('sorders.store_lists') }}"
                                        class="nav-link {{ request()->routeIs('sorders.*') || request()->is('stores/approved_status*', 'stores/denied_status*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Stock Requests</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- authoriser-stock-purchase-requests --}}
                            @can('authoriser-stock-purchase-requests')
                                <li class="nav-item">
                                    <a href="{{ route('auth_spr_lists') }}"
                                        class="nav-link {{ request()->routeIs('auth_spr_lists') || request()->is('auth_spr_*') ? 'active' : '' }}">
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
                                        class="nav-link {{ request()->routeIs('purchases.all_requests', 'purchases.req_all', 'purchases.requested', 'purchases.initiated', 'purchases.approved', 'purchases.ordered', 'purchases.delivered') || request()->is('req_all*', 'all_requests*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p> Request Lists</p>
                                    </a>
                                </li>
                            @endcan
                            @can('purchase-lists')
                                <li class="nav-item">
                                    <a href="{{ route('purchases.purchase_list') }}"
                                        class="nav-link {{ request()->routeIs('purchases.purchase_list') || request()->is('purchase_list*', 'purchase_edit*', 'showlist*', 'editlist*', 'generatePDF*') ? 'active' : '' }}">
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
                                        class="nav-link {{ request()->routeIs('purchases.req_all', 'purchases.requested', 'purchases.initiated', 'purchases.approved', 'purchases.ordered', 'purchases.delivered') || request()->is('req_all*') ? 'active' : '' }}">
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
                                        class="nav-link {{ request()->routeIs('inventories.inventory_item_history') || request()->is('inventory_history*', 'inventory_item_history*') ? 'active' : '' }}" style="{{ request()->routeIs('inventories.inventory_item_history') || request()->is('inventory_history*', 'inventory_item_history*') ? 'background-color: #0e6258' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p> Received History</p>
                                    </a>
                                </li>
                            @endcan
                            @can('supply-history')
                                <li class="nav-item">
                                    <a href="{{ route('stores.supply_history') }}"
                                        class="nav-link {{ request()->routeIs('stores.supply_history') || request()->is('supply_history*') ? 'active' : '' }}" style="{{ request()->routeIs('stores.supply_history') || request()->is('supply_history*') ? 'background-color: #0e6258' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p> Supply History</p>
                                    </a>
                                </li>
                            @endcan
                            @can('requester-stock-requests')
                                <li class="nav-item">
                                    <a href="{{ route('stores.requester_store_lists') }}"
                                        class="nav-link {{ request()->routeIs('stores.requester_store_lists') || request()->is('requester_store_*', 'requester_edit*') ? 'active' : '' }}" style="{{ request()->routeIs('stores.requester_store_lists') || request()->is('requester_store_*', 'requester_edit*') ? 'background-color: #0e6258' : '' }}">
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
                                        class="nav-link {{ request()->routeIs('authorise.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p> Direct Purchase Requests</p>
                                    </a>
                                </li>
                            @endcan

                            {{-- @if (Auth::user()->role->name == 'purchasing_officer') --}}
                            @can('direct-purchase-requests')
                                <li class="nav-item">
                                    <a href="{{ route('purchases.purchase_list') }}"
                                        class="nav-link {{ request()->routeIs('purchases.purchase_list') || request()->is('purchase_list*', 'purchase_edit*', 'showlist*', 'editlist*', 'generatePDF*', 'generatePurchaseOrderPDF*') ? 'active' : '' }}">
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
                                        class="nav-link {{ request()->routeIs('po_spr_lists') || request()->is('po_spr_*') ? 'active' : '' }}">
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
                                        class="nav-link {{ request()->routeIs('purchases.purchase_list') || request()->is('purchase_list*', 'purchase_edit*', 'showlist*', 'editlist*') ? 'active' : '' }}">
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
                                        class="nav-link {{ request()->routeIs('purchases.drafts') || request()->is('drafts*', 'save_draft*', 'purchase_order_draft*') ? 'active' : '' }}">
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
                                        class="nav-link {{ request()->routeIs('spr_pos*') || request()->is('spr_pos*') ? 'active' : '' }}">
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
                                        class="nav-link {{ request()->routeIs('inventories.index', 'inventories.show', 'inventories.edit') || request()->is('generateinventoryPDF*') ? 'active' : '' }}" style="{{ request()->routeIs('inventories.index', 'inventories.show', 'inventories.edit') || request()->is('generateinventoryPDF*') ? 'background-color: #0e6258' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>GRN</p>
                                    </a>
                                </li>
                            @endcan
                            @can('approve-inventory-correction')
                                <li class="nav-item">
                                    <a href="{{ route('inventory-corrections.index') }}"
                                        class="nav-link {{ request()->routeIs('inventory-corrections.*') ? 'active' : '' }}" style="{{ request()->routeIs('inventory-corrections.index', 'inventory-corrections.show') ? 'background-color: #0e6258' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Correction Requests</p>
                                    </a>
                                </li>
                            @endcan
                            @can('view-inventory-audit-log')
                                <li class="nav-item">
                                    <a href="{{ route('inventory-corrections.audit-log') }}"
                                        class="nav-link {{ request()->routeIs('inventory-corrections.audit-log') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Inventory Audit Log</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @endif --}}

                        </ul>
                    </li>
                @endif
                {{-- end of navigate module --}}
                {{-- purchase model --}}
                {{-- @if (Auth::user()->role->name == 'site_admin' || Auth::user()->role->name == 'purchasing_officer') --}}
                @if(Auth::user()->canAny(['purchase-management-module', 'view-tax', 'add-tax', 'edit-tax', 'delete-tax', 'view-levy', 'add-levy', 'edit-levy', 'delete-levy', 'view-uom', 'add-uom', 'edit-uom', 'delete-uom']))
                    <li
                        class="nav-item {{ request()->routeIs('taxes.*', 'levies.*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->routeIs('taxes.*', 'levies.*') ? 'active' : '' }}" style="{{ request()->routeIs('taxes.*', 'levies.*') ? 'background-color: #0e6258' : '' }}">
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
                            {{-- Note: Suppliers is in standalone menu, not duplicated here --}}
                            {{-- Removed duplicate Items/Parts - use "Items" under Inventory Management instead --}}
                            
                            {{-- Levies --}}
                            @can('view-levy')
                                <li class="nav-item">
                                    <a href="{{ route('taxes.index') }}"
                                        class="nav-link {{ request()->routeIs('taxes.*') ? 'active' : '' }}" style="{{ request()->routeIs('taxes.*') ? 'background-color: #0e6258' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Levies</p>
                                    </a>
                                </li>
                            @endcan
                            
                            {{-- Taxes --}}
                            @can('view-tax')
                                <li class="nav-item">
                                    <a href="{{ route('levies.index') }}"
                                        class="nav-link {{ request()->routeIs('levies.*') ? 'active' : '' }}" style="{{ request()->routeIs('levies.*') ? 'background-color: #0e6258' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Taxes</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>

                    </li>
                    {{-- @endif --}}
                @endcan
                {{-- end purchase model  --}}
                {{-- reports --}}
                @can('reports-module')
                    <li class="nav-item {{ request()->routeIs('monthlyreport*') || request()->is('monthlyreport*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('monthlyreport*') || request()->is('monthlyreport*') ? 'active' : '' }}">
                            <i>
                                <img src="{{ asset('assets/images/icons/reports.jpg') }}" width="26" height="26"
                                    alt="" />
                            </i>
                            <p>
                                Reports
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                            {{-- @if (Auth::user()->role->name == 'store_officer') --}}
                            @can('monthly-reports')
                                <li class="nav-item">
                                    <a href="{{ route('monthlyreport') }}"
                                        class="nav-link {{ request()->routeIs('monthlyreport*') || request()->is('monthlyreport*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Monthly Reports</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @endif --}}

                        </ul>
                    </li>
                    {{-- end of reports module --}}

                @endcan

                {{-- Work Orders module --}}
                @can('view-work-order')
                    <li class="nav-item {{ request()->routeIs('work-orders.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('work-orders.*') ? 'active' : '' }}">
                            <i>
                                <img src="{{ asset('assets/images/icons/reports.jpg') }}" width="26" height="26"
                                     alt="" />
                            </i>
                            <p>
                                Work Orders
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('maintenance-planner-dashboard')
                                <li class="nav-item">
                                    <a href="{{ route('work-orders.dashboard') }}"
                                       class="nav-link {{ request()->routeIs('work-orders.dashboard') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>WO Dashboard</p>
                                    </a>
                                </li>
                            @endcan
                            @can('view-work-order')
                                <li class="nav-item">
                                    <a href="{{ route('work-orders.index') }}"
                                       class="nav-link {{ request()->routeIs('work-orders.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Work Orders (List)</p>
                                    </a>
                                </li>
                            @endcan
                            @can('add-work-order')
                                <li class="nav-item">
                                    <a href="{{ route('work-orders.create') }}"
                                       class="nav-link {{ request()->routeIs('work-orders.create') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Create Work Order</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif

            </ul>
            {{--  --}}

            <a href="{{ route('myaccounts.index') }}"
                class="nav-link {{ request()->routeIs('myaccounts.*') || request()->is('myaccounts*') ? 'active' : '' }}">
                <i>
                    <img src="{{ asset('assets/images/icons/myaccount.png') }}" width="26" height="26"
                        alt="" />
                </i>
                <span>My Account</span>
            </a>

            <a href="{{ route('logout') }}" class="nav-link">
                <i>
                    <img src="{{ asset('assets/images/icons/logout.jpg') }}" width="26" height="26"
                        alt="" />
                </i>
                <span>Logout</span>
            </a>
            {{--  --}}
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
