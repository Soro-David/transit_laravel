<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-purple elevation-4 position-fixed vh-100" >
    <div class="d-flex align-items-center justify-content-center">
        <div class="navbar-brand-box mx-2 py-4">
            <a href="{{ route('chauffeur.dashboard') }}" class="logo logo-light d-flex align-items-center">
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

                <li class="nav-item">
                    <a href="{{ route('chauffeur.dashboard') }}" class="nav-link {{ activeSegment('chauffeur/dashboard') }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Tableau de bord du chauffeur</p>
                    </a>
                 </li>

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link {{ activeSegment('colis') }}">
                        <i class="fas fa-concierge-bell"></i>
                        <p>Gestion des Colis</p>
                         <i class="right fas fa-angle-left"></i>
                    </a>
                </li>

                 <li class="nav-item has-treeview">
                    <a href="{{ route('chauffeur.programme.index') }}" class="nav-link {{ activeSegment('programme') }}">
                        <i class="fas fa-calendar-alt"></i>
                         <p>Programme De livraison</p>
                         <i class="right fas fa-angle-left"></i>
                    </a>
                 </li>


                <li class="nav-item">
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