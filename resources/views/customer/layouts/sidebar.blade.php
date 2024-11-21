<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-purple elevation-4">

    <div class="d-flex align-items-center">
        <div class="navbar-brand-box">
            <a href="{{ route('home') }}" class="logo logo-light">
                <span class="logo-sm">
                    <img src="{{ asset('images/LOGOAFT.png') }}" alt="Logo" class="img-fluid custom-logo">
                </span>
            </a>
        </div>
    </div>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ auth()->user()->getAvatar() }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->getFullname() }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                
                <!-- Dashboard -->
                <li class="nav-item has-treeview">
                    <a href="{{ route('home') }}" class="nav-link {{ activeSegment('') }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>{{ __('trans.Dashboard') }}</p>
                    </a>
                </li>
                <!-- Request for Service -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>{{ __('trans.request_for_service') }}</p>
                    </a>
                    <ul class="submenu">
                        <li><a href="#" class="submenu-item">{{ __('Lease Property') }}</a></li>
                        <li><a href="#" class="submenu-item">{{ __('Lease Property') }}</a></li>
                        <li><a href="#" class="submenu-item">{{ __('Lease Property') }}</a></li>
                    </ul>
                </li>
        
                <!-- Clients -->
                <li class="nav-item has-treeview">
                    <a href="{{ route('customers.index') }}" class="nav-link {{ activeSegment('customers') }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>{{ __('Clients') }}</p>
                    </a>
                </li>
        
                <!-- Logout -->
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="document.getElementById('logout-form').submit()">
                        <i class="nav-icon fas fa-power-off"></i>
                        <p>{{ __('trans.logout') }}</p>
                        <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display: none;">
                            @csrf
                        </form>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- /.sidebar-menu -->
    </div><!-- Log on to codeastro.com for more projects -->
    <!-- /.sidebar -->
</aside>
