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
