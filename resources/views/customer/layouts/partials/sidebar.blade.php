<aside class="main-sidebar sidebar-dark-purple elevation-4 position-fixed vh-100">
    <div class="d-flex align-items-center justify-content-center">
        <div class="navbar-brand-box mx-2 py-4">
            <a href="{{ route('customer.dashboard') }}" class="logo logo-light d-flex align-items-center">
                <span class="logo-sm">
                    <img src="{{ asset('images/LOGOAFT.png') }}" alt="Logo" class="img-fluid custom-logo" style="max-height: 90px;">
                </span>
            </a>
        </div>
    </div>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item has-treeview mb-3">
                    <a href="{{ route('customer.dashboard') }}" class="nav-link {{ activeSegment('') }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>{{ __('trans.Dashboard') }}</p>
                    </a>
                </li>
                <li class="nav-item has-treeview mb-3">
                    <a href="{{ route('customer_colis.create.step1') }}" class="nav-link">
                        <i class="fas fa-plus"></i>
                        <p>{{ __('Demander un dévis') }}</p>
                    </a>
                </li>
                <li class="nav-item has-treeview mb-3">
                    <a href="{{ route('customer_colis.hold') }}" class="nav-link">
                        <i class="fas fa-clock"></i>
                        <p>{{ __('Demande en attente') }}</p>
                    </a>
                </li>
                <li class="nav-item has-treeview mb-3">
                    <a href="{{ route('customer_colis.history') }}" class="nav-link" >
                        <i class="fas fa-box"></i> 
                        <i class="fas fa-check text-success"></i>
                        <p>{{ __('Colis validé') }}</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="{{ route('customer_colis.suivi') }}" class="nav-link">
                        <i class="fas fa-map-marker-alt"></i>
                        <p>{{ __('Suivi de colis') }}</p>
                    </a>
                </li>
                 <li class="nav-item has-treeview">
                    <a href="{{ route('customer_colis.facture') }}" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        <p>{{ __('Facture') }}</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<style>
    .custom-logo {
        max-height: 90px;
        padding: 5px;
        margin: auto;
    }

    .nav-item {
        margin-bottom: 15px;
    }
</style>
