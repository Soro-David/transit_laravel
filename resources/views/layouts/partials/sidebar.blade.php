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
                <li class="nav-item has-treeview">
                    <a href="{{route('home')}}" class="nav-link {{ activeSegment('') }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>{{ __('trans.Dashboard') }}</p>
                    </a>
                </li>
                {{--  --}}
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="fas fa-user-plus"></i>
                        <p>{{ __('trans.provider') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('trans.add_provider') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('trans.provider_list') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                {{--  --}}

                <li class="nav-item has-treeview">
                    <a href="{{ route('products.index') }}" class="nav-link {{ activeSegment('products') }}">
                        <i class="fas fa-concierge-bell"></i>
                        <p>{{ __('trans.consult_the_services') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('trans.on_old') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('trans.history') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview">
                    <a href="{{ route('customers.index') }}" class="nav-link {{ activeSegment('customers') }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Clients</p>
                    </a>
                </li>
                {{-- <li class="nav-item has-treeview">
                    <a href="{{ route('cart.index') }}" class="nav-link {{ activeSegment('cart') }}">
                        <i class="nav-icon fas fa-cash-register"></i>
                        <p>{{ __('trans.pos_system') }}</p>
                    </a>
                </li> --}}
                {{-- <li class="nav-item has-treeview">
                    <a href="{{ route('orders.index') }}" class="nav-link {{ activeSegment('orders') }}">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>{{ __('trans.orders') }}</p>
                    </a>
                </li> --}}
                {{--  --}}
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link {{ activeSegment('gestion') }}">
                        <i class="fas fa-history"></i>
                        <p>
                            {{ __('trans.quote_management') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('trans.add_quote') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('trans.quote_list') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('trans.payment_tracking') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                
                {{--  --}}
                <li class="nav-item has-treeview">
                    <a href="{{ route('settings.index') }}" class="nav-link {{ activeSegment('settings') }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>{{ __('trans.settings') }}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="document.getElementById('logout-form').submit()">
                        <i class="nav-icon fas fa-power-off"></i>
                        <p>{{ __('trans.logout') }}</p>
                        <form action="{{route('logout')}}" method="POST" id="logout-form">
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
