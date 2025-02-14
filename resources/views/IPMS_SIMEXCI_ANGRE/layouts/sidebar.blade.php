<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-purple elevation-4 position-fixed vh-100">
    <div class="d-flex align-items-center justify-content-center">
        <div class="navbar-brand-box mx-2 py-4">
            <a href="{{ route('IPMS_SIMEXCI_ANGRE.dashboard') }}" class="logo logo-light d-flex align-items-center">
                <span class="logo-sm">
                    <img src="{{ asset('images/LOGOAFT.png')}}" alt="Logo" class="img-fluid custom-logo" style="max-height: 90px;">
                </span>
            </a>
        </div>
    </div>
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item has-treeview">
                    <a href="" class="nav-link {{ activeSegment('') }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>{{ __('trans.Dashboard') }}</p>
                    </a>
                </li>
                
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link {{ activeSegment('products') }}">
                        <i class="fas fa-concierge-bell"></i>
                        <p>{{ __('Gestion des colis') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        
                        <li class="nav-item">
                            <a href="{{route('ipms_angre_colis.suivi')}}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Suivi des colis') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('ipms_angre_colis.dump')}}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Voir les colis arrivé') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                {{-- Gestion des facture --}}
                <li class="nav-item has-treeview">
                    <a href="{{ route('ipms_angre_invoice.create.invoice') }}" class="nav-link">
                        <i class="fas fa-file-invoice"></i>
                        <p>{{ __('Gestion des factures') }}</p>
                    </a>
                </li>
                {{-- Scan --}}
                <li class="nav-item has-treeview">
                    <a href="" class="nav-link {{ activeSegment('products') }}">
                        <i class="fas fa-qrcode"></i>
                        <p>{{ __('Vérification au scanner') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                       
                        <li class="nav-item">
                            <a href="{{route('ipms_angre_scan.dechargement')}}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Dechargement') }}</p>
                            </a>
                        </li>
                         <li class="nav-item">
                            <a href="{{route('ipms_angre_scan.livre')}}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Livré') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                {{-- StartRDV --}}
                <li class="nav-item has-treeview">
                    <a href="" class="nav-link {{ activeSegment('products') }}">
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
                    <a href="" class="nav-link {{ activeSegment('customers') }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Clients</p>
                    </a>
                </li>
                {{-- progammz de transport --}}
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link {{ activeSegment('products') }}">
                        <i class="fas fa-car"></i>
                        <p>{{ __('Transport') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('agent_transport.show.chauffeur') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Chauffeurs') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('agent_transport.planing.chauffeur') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Planifier') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<style>
    .main-sidebar {
        top: 0;
        left: 0;
        /* z-index: 1030; Assurez-vous qu'il soit au-dessus d'autres éléments */
        height: 100vh; /* Assurez que la sidebar occupe toute la hauteur de la fenêtre */
        position: fixed; /* Fixée pour qu'elle ne bouge pas avec le contenu principal */
    }
    .custom-logo {
        max-height: 90px;
        padding: 1px;
        margin: 0 auto;
    }

.sidebar {
    max-height: calc(100vh - 160px); /* Ajustez cette valeur en fonction de l'en-tête ou d'autres marges */
    overflow-y: auto; /* Assurez-vous que le scroll est bien activé */
}
</style>
