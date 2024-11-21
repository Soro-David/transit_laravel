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
            <div class="profile">
                <div class="profile-photo">
                    <!-- Formulaire pour changer la photo -->
                    <form action="#" method="" enctype="multipart/form-data">
                        @csrf
                        <label for="photo-upload" style="cursor: pointer;">
                            <img src="{{ auth()->user()->photo_url ?: asset('images/default-avatar.png') }}" 
                                 alt="Photo de {{ auth()->user()->getFullname() }}" 
                                 class="img-fluid rounded-circle elevation-2">
                        </label>
                        <input type="file" name="photo" id="photo-upload" accept="image/*" 
                               style="display: none;" onchange="this.form.submit()">
                    </form>
                </div>
                <div class="info">
                    <a href="#" class="d-block">{{ auth()->user()->getFullname() }}</a>
                </div>
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
{{--  --}}
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link {{ activeSegment('gestion') }}">
                        <i class="fas fa-history"></i>
                        <p>
                            {{ __('trans.my_quote') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
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
{{--  --}}
                <!-- Request for Service -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>{{ __('trans.request_a_service') }}</p>
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

<style>
 

</style>