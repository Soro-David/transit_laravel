<!-- Main Sidebar Container -->
<aside class="main-sidebar elevation-4 position-fixed vh-100">
    <div class="d-flex align-items-center justify-content-center">
        <div class="navbar-brand-box mx-2 py-4">
            <a href="{{ route('AGENCE_CHINE.dashboard') }}" class="logo logo-light d-flex align-items-center">
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
                    <a href="{{ route('AGENCE_CHINE.dashboard') }}" class="nav-link {{ activeSegment('') }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>{{ __('trans.Dashboard') }}</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link {{ activeSegment('gestion') }}">
                        <i class="fas fa-list-alt"></i>
                        <p>
                            {{ __('Gestion des dévis') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('chine_colis.hold')}}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Dévis en attente') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('chine_colis.devis.confirme') }}" class="nav-link">
                                <i class="far fa-check-circle nav-icon"></i>
                                <p>{{ __('Devis Confirmés Chine') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('chine_colis.devis.hold')}}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Suivi des dévis') }}</p>
                            </a>
                        </li>
                        
                    </ul>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link {{ activeSegment('products') }}">
                        <i class="fas fa-concierge-bell"></i>
                        <p>{{ __('Gestion des colis') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('chine_colis.create.colis') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Ajouter un colis') }}</p>
                            </a>
                        </li>
                       
                        <li class="nav-item">
                            <a href="{{route('chine_colis.colis.valide')}}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Voir les colis validés') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('chine_colis.tout.colis') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Historique des colis') }}</p>
                            </a>
                        </li>

                    </ul>
                </li>
                {{-- gestion ces cargaison --}}
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link {{ activeSegment('products') }}">
                        <i class="fas fa-shipping-fast"></i>
                        <p>{{ __('Gestion des Conteneur') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('chine_colis.liste.contenaire') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Colis en conteneur') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('chine_colis.historique.contenaire') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Historique') }}</p>
                            </a>
                        </li>
                    
                    </ul>
                </li>

                {{-- gestion ces Vol --}}
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link {{ activeSegment('products') }}">
                        <i class="fas fa-shipping-fast"></i>
                        <p>{{ __('Gestion des Vol') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('chine_colis.liste.vol') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Vol') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('chine_colis.historique.vol') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Historique') }}</p>
                            </a>
                        </li>
                        
                    </ul>
                </li>
                    {{-- Gestion des Ballon --}}
                <li class="nav-item has-treeview">
                    <a href="{{ route('chine_colis.cargaison.ferme') }}" class="nav-link">
                        <i class="fas fa-ship"></i>
                        <p>{{ __('Bateau et Avion') }}</p>
                    </a>

                {{-- Scan --}}
                <li class="nav-item has-treeview">
                    <a href="" class="nav-link {{ activeSegment('products') }}">
                        <i class="fas fa-qrcode"></i>
                        <p>{{ __('Vérification au scanner') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('chine_scan.entrepot')}}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Mise en Entrépot') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('chine_scan.chargement')}}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Chargement') }}</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('chine_scan.dechargement')}}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Dechargement') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('chine_scan.livre')}}" class="nav-link">
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
                            <a href="{{ route('rdv_chine.rdv.index') }}" class="nav-link">
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
                    <a href="{{ route('chine_message.index') }}" class="nav-link {{ activeSegment('customers') }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>{{ __('Clients') }}</p>
                    </a>
                </li>
               {{-- programme de transport --}}
<li class="nav-item has-treeview">
    <a href="#" class="nav-link {{ activeSegment('products') }}">
        <i class="fas fa-car"></i>
        <p>{{ __('Transport') }}</p>
        <i class="right fas fa-angle-left"></i>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('chine_transport.chauffeurs.show') }}" class="nav-link">
                <i class="far nav-icon"></i>
                <p>{{ __('Chauffeurs') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('chine_programme.programme.depot') }}" class="nav-link">
                <i class="far fa-circle nav-icon text-success"></i>
                <p>{{ __('Créer un Dépôt') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('chine_programme.programme.recuperation') }}" class="nav-link">
                <i class="far fa-circle nav-icon text-warning"></i>
                <p>{{ __('Créer une Récupération') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('chine_programme.ajoutDevis') }}" class="nav-link">
                <i class="far nav-icon"></i>
                <p>{{ __('Programmer via Devis') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('chine_programme.planing.index') }}" class="nav-link">
                <i class="far nav-icon"></i>
                <p>{{ __('Liste des Programmes') }}</p>
            </a>
        </li>
    </ul>
</li>
                <li class="nav-item">
            <a href="{{ route('bilan.chine') }}" class="nav-link">
                <i class="fas fa-flag-checkered mr-2"></i>
                <p>Bilan Agence Chine</p>
            </a>
        </li>
            </ul>
        </nav>
    </div>
</aside>

<style>
    .main-sidebar {
        background-color: #b7bfdb; /* Fond blanc */
        color: #000000;           /* Texte noir */
        top: 0;
        left: 0;
        height: 100vh;
        position: fixed;
    }
    /* Appliquer la couleur sur les liens de la sidebar */
    .main-sidebar a.nav-link {
        color: #000000; /* Texte noir */
        transition: transform 0.2s ease, color 0.2s ease;
    }
    /* Effet de survol : décalage vers la droite pour simuler un enfoncement */
    .main-sidebar a.nav-link:hover {
        transform: translateX(5px);
    }


    .main-sidebar {
    background-color: #b7bfdb; /* Fond */
    color: #000000;           /* Texte */
    top: 0;
    left: 0;
    display: flex;
    flex-direction: column;
}

.main-sidebar > .d-flex.align-items-center.justify-content-center {
    flex-shrink: 0; 
}


.sidebar {
    flex-grow: 1;
    overflow-y: auto;
    min-height: 0;
}

/* Styles existants pour les liens, etc. (gardez-les) */
.main-sidebar a.nav-link {
    color: #000000; /* Texte noir */
    transition: transform 0.2s ease, color 0.2s ease;
}

.main-sidebar a.nav-link:hover {
    transform: translateX(5px);
}

.custom-logo {
    max-height: 90px;
    padding: 1px;
    margin: 0 auto;
}

/* Si vous souhaitez personnaliser la barre de défilement (optionnel) */
.sidebar::-webkit-scrollbar {
    width: 8px;
}

.sidebar::-webkit-scrollbar-track {
    background: rgba(0,0,0,0.1);
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.3);
    border-radius: 4px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(0,0,0,0.5);
}
</style>
