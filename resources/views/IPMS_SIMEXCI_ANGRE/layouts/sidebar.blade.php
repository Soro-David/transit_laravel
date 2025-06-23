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
                <li class="nav-item">
                    <a href="{{ route('IPMS_SIMEXCI_ANGRE.dashboard') }}" class="nav-link {{ isActiveRoute('IPMS_SIMEXCI_ANGRE.dashboard') }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>{{ __('trans.Dashboard') }}</p>
                    </a>
                </li>

                <li class="nav-item has-treeview {{ isMenuOpen(['ipms_angre_colis.create.colis', 'ipms_angre_colis.colis.valide']) }}">
                    <a href="#" class="nav-link {{ isActiveRoute(['ipms_angre_colis.create.colis', 'ipms_angre_colis.colis.valide']) }}">
                        <i class="fas fa-concierge-bell"></i>
                        <p>{{ __("Gestion des colis d'Exp") }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('ipms_angre_colis.create.colis') }}" class="nav-link {{ isActiveRoute('ipms_angre_colis.create.colis') }}">
                                <p>{{ __('Ajouter un colis') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('ipms_angre_colis.colis.valide')}}" class="nav-link {{ isActiveRoute('ipms_angre_colis.colis.valide') }}">
                                <p>{{ __('Voir les colis validés') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item has-treeview {{ isMenuOpen(['ipms_angre_colis.suivi', 'ipms_angre_colis.dump']) }}">
                    <a href="#" class="nav-link {{ isActiveRoute(['ipms_angre_colis.suivi', 'ipms_angre_colis.dump']) }}">
                        <i class="fas fa-concierge-bell"></i> <!-- Peut-être un autre icône ici pour différencier ? ex: fa-truck-loading -->
                        <p>{{ __('Gestion des colis arrivés') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('ipms_angre_colis.suivi')}}" class="nav-link {{ isActiveRoute('ipms_angre_colis.suivi') }}">
                                <p>{{ __('Suivi des colis') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('ipms_angre_colis.dump')}}" class="nav-link {{ isActiveRoute('ipms_angre_colis.dump') }}">
                                <p>{{ __('Voir les colis arrivé') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item has-treeview {{ isMenuOpen(['ipms_angre_colis.liste.vol', 'ipms_angre_colis.cargaison.ferme']) }}">
                    <a href="#" class="nav-link {{ isActiveRoute(['ipms_angre_colis.liste.vol', 'ipms_angre_colis.cargaison.ferme']) }}">
                        <i class="fas fa-shipping-fast"></i>
                        <p>{{ __('Gestion des cargaisons') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('ipms_angre_colis.liste.vol') }}" class="nav-link {{ isActiveRoute('ipms_angre_colis.liste.vol') }}">
                                <p>{{ __('Vol') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('ipms_angre_colis.cargaison.ferme') }}" class="nav-link {{ isActiveRoute('ipms_angre_colis.cargaison.ferme') }}">
                                <p>{{ __('Cargaisons fermées') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item"> <!-- Supprimé has-treeview car c'est un lien direct -->
                    <a href="{{ route('ipms_angre_colis.liste_ballon') }}" class="nav-link {{ isActiveRoute('ipms_angre_colis.liste_ballon') }}">
                        <i class="fas fa-ship"></i>
                        <p>{{ __('Ballon Arrivés') }}</p>
                    </a>
                </li>

                <li class="nav-item has-treeview {{ isMenuOpen(['ipms_angre_scan.entrepot', 'ipms_angre_scan.chargement', 'ipms_angre_scan.dechargement', 'ipms_angre_scan.livre']) }}">
                    <a href="#" class="nav-link {{ isActiveRoute(['ipms_angre_scan.entrepot', 'ipms_angre_scan.chargement', 'ipms_angre_scan.dechargement', 'ipms_angre_scan.livre']) }}">
                        <i class="fas fa-qrcode"></i>
                        <p>{{ __('Vérification au scanner') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('ipms_angre_scan.entrepot')}}" class="nav-link {{ isActiveRoute('ipms_angre_scan.entrepot') }}">
                                <p>{{ __('Mise en Entrépot') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('ipms_angre_scan.chargement')}}" class="nav-link {{ isActiveRoute('ipms_angre_scan.chargement') }}">
                                <p>{{ __('Chargement') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('ipms_angre_scan.dechargement')}}" class="nav-link {{ isActiveRoute('ipms_angre_scan.dechargement') }}">
                                <p>{{ __('Dechargement') }}</p>
                            </a>
                        </li>
                         <li class="nav-item">
                            <a href="{{route('ipms_angre_scan.livre')}}" class="nav-link {{ isActiveRoute('ipms_angre_scan.livre') }}">
                                <p>{{ __('Livré') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item has-treeview {{ isMenuOpen(['ipmx_rdv.rdv.index', 'ipmx_rdv.historique']) }}"> <!-- Assurez-vous que 'ipmx_rdv.historique' est un nom de route valide -->
                    <a href="#" class="nav-link {{ isActiveRoute(['ipmx_rdv.rdv.index', 'ipmx_rdv.historique']) }}">
                        <i class="fas fa-calendar-alt"></i>
                        <p>{{ __('RDV') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('ipmx_rdv.rdv.index') }}" class="nav-link {{ isActiveRoute('ipmx_rdv.rdv.index') }}">
                                <p>{{ __('Liste RDV') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <!-- Assurez-vous d'avoir une route nommée 'ipmx_rdv.historique' ou ajustez -->
                            <a href="{{-- route('ipmx_rdv.historique') --}}" class="nav-link {{-- isActiveRoute('ipmx_rdv.historique') --}}">
                                <p>{{ __('Historique des RDV') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item"> <!-- Supprimé has-treeview si c'est un lien direct -->
                    <!-- Définissez une route pour les clients, ex: 'clients.index' -->
                    <a href="{{-- route('clients.index') --}}" class="nav-link {{-- isActiveRoute('clients.index') --}} {{-- isActiveRoute('clients.*') --}}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Clients</p>
                    </a>
                </li>

                <li class="nav-item has-treeview {{ isMenuOpen(['IPMSANGRE_transport.chauffeurs.show', 'IPMSANGRE_transport.planification.show']) }}">
                    <a href="#" class="nav-link {{ isActiveRoute(['IPMSANGRE_transport.chauffeurs.show', 'IPMSANGRE_transport.planification.show']) }}">
                        <i class="fas fa-car"></i>
                        <p>{{ __('Transport') }}</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('IPMSANGRE_transport.chauffeurs.show') }}" class="nav-link {{ isActiveRoute('IPMSANGRE_transport.chauffeurs.show') }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('Chauffeurs') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('IPMSANGRE_transport.planification.show') }}" class="nav-link {{ isActiveRoute('IPMSANGRE_transport.planification.show') }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('Planifier') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>