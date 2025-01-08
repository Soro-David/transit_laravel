<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-purple elevation-4 position-fixed vh-100">
    <div class="d-flex align-items-center justify-content-center">
        <div class="navbar-brand-box mx-2 py-4">
            <a href="{{ route('home') }}" class="logo logo-light d-flex align-items-center">
                <span class="logo-sm">
                    <img src="{{ asset('images/LOGOAFT.png') }}" alt="Logo" class="img-fluid custom-logo" style="max-height: 90px;">
                </span>
            </a>
        </div>
    </div>
    <div class="sidebar">
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
                    <a href="" class="nav-link">
                        <i class="fas fa-user-plus"></i>
                        <p>{{ __('Agences & Agents') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('managers.agent')}}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Agents') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('managers.agence')}}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Agences') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                {{--  --}}

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link {{ activeSegment('products') }}">
                        <i class="fas fa-concierge-bell"></i>
                        <p>{{ __('trans.view_packages') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('colis.create.step1') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('trans.add') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('colis.hold') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('trans.on_old') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('colis.dump') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Arrivé') }}</p>
                            </a>
                        </li>
                        {{-- <li class="nav-item">
                            <a href="{{ route('colis.history') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('trans.package_tracking') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('colis.history') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('trans.history') }}</p>
                            </a>
                        </li> --}}
                    </ul>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link {{ activeSegment('gestion') }}">
                        <i class="fas fa-list-alt"></i>
                        <p>
                            {{ __('trans.quote_management') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        {{-- <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('trans.add_quote') }}</p>
                            </a>
                        </li> --}}
                        <li class="nav-item">
                            <a href="{{ route('colis.devis.hold') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Suivi') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('colis.liste.contenaire') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Contenaire') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                {{-- Scan --}}
                <li class="nav-item has-treeview">
                    <a href="" class="nav-link {{ activeSegment('products') }}">
                        <i class="fas fa-qrcode"></i>
                        <p>{{ __('Scan') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('scan.entrepot')}}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Mise en Entrépot') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('scan.chargement')}}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Chargement') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('scan.dechargement')}}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Dechargement') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                {{-- StartRDV --}}
                <li class="nav-item has-treeview">
                    <a href="{{ route('products.index') }}" class="nav-link {{ activeSegment('products') }}">
                        <i class="fas fa-calendar-alt"></i>
                        <p>{{ __('RDV') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Liste RDV') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Historique des RDV') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                {{-- endRDV --}}
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
                {{-- progammz de transport --}}
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link {{ activeSegment('products') }}">
                        <i class="fas fa-car"></i>
                        <p>{{ __('Transport') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('transport.show.chauffeur') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Chauffeurs') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('transport.planing.chauffeur') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Planifier') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview">
                    <a href="{{ route('settings.index') }}" class="nav-link {{ activeSegment('settings') }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>{{ __('trans.settings') }}</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<style>
    .custom-logo {
    max-height: 10px;
    padding: 1px; /* Ajout d'un léger padding interne si nécessaire */
    margin: 10 auto; /* Centrer l'image */
}
</style>
