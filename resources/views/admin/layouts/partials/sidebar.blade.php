<!-- Main Sidebar Container -->
<aside class="main-sidebar elevation-4 position-fixed vh-100">
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
                    <a href="{{ route('home') }}" class="nav-link {{ activeSegment('') }}">
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
                            <a href="{{ route('agence.agent') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Agents') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('managers.agence') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Agences') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                {{-- Gestion des devis --}}
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
                            <a href="{{ route('colis.hold') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Dévis en attente') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('colis.devis.hold') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Suivi des dévis ') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                {{-- Gestion des colis --}}
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link {{ activeSegment('products') }}">
                        <i class="fas fa-concierge-bell"></i>
                        <p>{{ __('Gestion des colis') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('colis.create.colis') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Ajouter un colis') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('colis.colis.valide') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Voir les colis validés') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('colis.tout.colis') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Historique des colis') }}</p>
                            </a>
                        </li>
                        {{-- <li class="nav-item">
                            <a href="{{ route('colis.dump') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Voir les colis arrivés') }}</p>
                            </a>
                        </li> --}}
                    </ul>
                </li>
                {{-- Gestion des facture --}}
                {{-- <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="fas fa-file-invoice"></i>
                        <p>{{ __('Gestion des factures') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('invoice.create.invoice') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Nouvelle Facture') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('invoice.historique.invoice') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Historique') }}</p>
                            </a>
                        </li>
                    </ul>
                </li> --}}
                {{-- Gestion des Cargaison --}}
                {{-- <li class="nav-item has-treeview">
                    <a href="#" class="nav-link {{ activeSegment('products') }}">
                        <i class="fas fa-shipping-fast"></i>
                        <p>{{ __('Gestion des cargaisons') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('colis.liste.contenaire') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Colis en conteneurs') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('colis.liste.vol') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Vol') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('colis.cargaison.ferme') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Bateaux & Avions') }}</p>
                            </a>
                        </li>
                    </ul>
                </li> --}}

                                {{-- gestion ces cargaison --}}
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link {{ activeSegment('products') }}">
                        <i class="fas fa-shipping-fast"></i>
                        <p>{{ __('Gestion des Conteneur') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('colis.liste.contenaire') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Colis en conteneur') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('cargaison.historique.contenaire') }}" class="nav-link">
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
                            <a href="{{ route('colis.liste.vol') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Vol') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('cargaison.historique.vol') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Historique') }}</p>
                            </a>
                        </li>
                        
                    </ul>
                </li>
                    {{-- Gestion des Ballon --}}
                <li class="nav-item has-treeview">
                    <a href="{{ route('colis.cargaison.ferme') }}" class="nav-link">
                        <i class="fas fa-ship"></i>
                        <p>{{ __('Bateau et Avion') }}</p>
                    </a>
                </li>
                     {{-- Gestion des Ballon --}}
               {{-- <li class="nav-item has-treeview">
                    <a href="{{ route('colis.liste_ballon') }}" class="nav-link">
                        <i class="fas fa-ship"></i>
                        <p>{{ __('Avion & Bateau Arrivés') }}</p>
                    </a>
                </li> --}}
                {{-- Scan --}}
                <li class="nav-item has-treeview">
                    <a href="" class="nav-link {{ activeSegment('products') }}">
                        <i class="fas fa-qrcode"></i>
                        <p>{{ __('Vérification au scanner') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('scan.entrepot') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Mise en Entrépot') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('scan.chargement') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Chargement') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('scan.dechargement') }}" class="nav-link">
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
                            <a href="{{ route('rdv.index') }}" class="nav-link">
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
                    <a href="{{ route('message.index') }}" class="nav-link {{ activeSegment('customers') }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>{{ __('Clients') }}</p>
                    </a>
                </li>

                 <li class="nav-item has-treeview">
                    <a href="{{ route('prospects.index') }}" class="nav-link {{ activeSegment('customers') }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>{{ __('Prospect') }}</p>
                    </a>
                </li>
                {{-- Transport --}}
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
                            <a href="{{ route('programme.index') }}" class="nav-link">
                                <i class="far nav-icon"></i>
                                <p>{{ __('Programme') }}</p>
                            </a>
                        </li>

                        <li class="nav-item has-treeview">
                    </ul>
                </li>
                <a href="{{ route('bilan.bilan') }}" class="nav-link {{ activeSegment('bilan') }}">
        <i class="nav-icon fas fa-chart-bar"></i> <p>Bilan Import/Export</p>
    </a>
</li>

                {{-- <li class="nav-item has-treeview">
                    <a href="{{ route('setting.index') }}" class="nav-link {{ activeSegment('settings') }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>{{ __('Paramètre') }}</p>
                    </a>
                </li> --}}
            </ul>
        </nav>
    </div>
</aside>

<style>
 .main-sidebar {
    background-color: #ffffff; /* Fond blanc */
    color: #000000;           /* Texte noir */
    top: 0;
    left: 0;
    height: 100vh; /* Assure que la sidebar prend toute la hauteur de la fenêtre */
    position: fixed; /* Fixe la sidebar par rapport à la fenêtre */

    /* AJOUTÉ : Indispensable pour que flex-grow et flex-shrink fonctionnent sur les enfants */
    display: flex;
    flex-direction: column; /* Les enfants (logo, menu) s'empileront verticalement */
}

/* Zone du logo : s'assure qu'elle ne rétrécit pas */
.main-sidebar > .d-flex.align-items-center.justify-content-center {
    flex-shrink: 0; /* Empêche cette zone de rétrécir si le contenu du menu est grand */
}

/* Zone du menu scrollable */
.sidebar {
    flex-grow: 1; /* Permet à cette section de prendre tout l'espace vertical restant */
    overflow-y: auto; /* Active le défilement vertical si le contenu dépasse */
    min-height: 0; /* Important pour que overflow fonctionne correctement dans un parent flex */
}

/* Styles pour les liens dans la sidebar */
.main-sidebar a.nav-link {
    color: #000000; /* Texte noir */
    transition: transform 0.2s ease, color 0.2s ease;
}

/* Effet de survol pour les liens */
.main-sidebar a.nav-link:hover {
    transform: translateX(5px); /* Petit décalage vers la droite */
}

/* Style pour le logo personnalisé */
.custom-logo {
    max-height: 90px;
    padding: 1px;
    margin: 0 auto;
}

/* Styles optionnels pour personnaliser la barre de défilement (pour les navigateurs WebKit) */
.sidebar::-webkit-scrollbar {
    width: 8px;
}

.sidebar::-webkit-scrollbar-track {
    background: rgba(0,0,0,0.1); /* Couleur de fond de la piste */
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.3); /* Couleur du curseur de la barre de défilement */
    border-radius: 4px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(0,0,0,0.5); /* Couleur du curseur au survol */
}
</style>
