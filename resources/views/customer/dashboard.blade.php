<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mon Tableau de Bord - {{ config('app.name') }}</title>

    <!-- Fonts and Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- App Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <style>
        /* --- STYLES GÉNÉRAUX --- */
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .dashboard-container { padding: 2rem; }

        /* --- HEADER DU DASHBOARD --- */
        .dashboard-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .dashboard-header h1 { font-weight: 600; color: #2c3e50; font-size: 2rem; }
        .btn-devis {
            background-color: orange; color: white; font-weight: 500;
            padding: 10px 20px; border-radius: 8px; text-decoration: none;
            transition: all 0.3s ease; display: inline-flex; align-items: center;
        }
        .btn-devis i { margin-right: 8px; }
        .btn-devis:hover { background-color: #e69500; transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }

        /* --- CARTES DE STATISTIQUES --- */
        .stats-card { background: white; border-radius: 12px; padding: 1.5rem; display: flex; align-items: center; box-shadow: 0 4px 20px rgba(0,0,0,0.05); transition: all 0.3s ease; }
        .stats-card:hover { transform: translateY(-5px); box-shadow: 0 6px 25px rgba(0,0,0,0.08); }
        .stats-card .icon { font-size: 1.8rem; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1.5rem; }
        .stats-card .info h3 { font-size: 2.2rem; font-weight: 700; margin: 0; color: #2c3e50; }
        .stats-card .info p { margin: 0; color: #7f8c8d; }
        .icon-attente { background-color: #fef0e7; color: #f2994a; }
        .icon-valide { background-color: #e8f5e9; color: #27ae60; }
        /* CORRECTION : Renommage de la classe de l'icône "livré" */
        .icon-livre { background-color: #e0f2f1; color: #009688; }

        /* --- SECTION SUIVI DE COLIS --- */
        .suivi-section { margin-top: 3rem; }
        .suivi-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .suivi-header h2 { font-weight: 600; color: #2c3e50; font-size: 1.8rem; margin: 0; }
        
        /* --- NOUVEAUX STYLES POUR LES BOUTONS DE FILTRE --- */
        .filter-buttons .btn-filter {
            background-color: #fff; color: #495057; border: 1px solid #dee2e6;
            padding: 8px 18px; border-radius: 8px; font-weight: 500;
            text-decoration: none; margin-left: 10px; transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .filter-buttons .btn-filter.active, .filter-buttons .btn-filter:hover {
            background-color: #0d6efd; /* Bleu Bootstrap */
            color: white; border-color: #0d6efd;
        }

        /* --- Styles du tracker (inchangés) --- */
        .tracker-card { background: white; border-radius: 12px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .tracker-header { font-weight: 600; font-size: 1.2rem; color: #34495e; margin-bottom: 2rem; }
        .tracker-timeline { display: flex; align-items: center; justify-content: space-between; }
        .tracker-step { text-align: center; position: relative; flex-basis: 20%; }
        .tracker-icon { width: 50px; height: 50px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background-color: #ecf0f1; color: #bdc3c7; font-size: 1.5rem; border: 3px solid #ecf0f1; transition: all 0.4s ease; z-index: 2; }
        .tracker-label { margin-top: 0.8rem; font-size: 0.8rem; font-weight: 500; color: #7f8c8d; }
        .tracker-line { position: absolute; top: 25px; left: 50%; width: 100%; height: 4px; background-color: #ecf0f1; z-index: 1; transition: background-color 0.4s ease; }
        .tracker-step.completed .tracker-icon { background-color: #2ecc71; border-color: #2ecc71; color: white; }
        .tracker-step.completed .tracker-label { color: #2ecc71; }
        .tracker-step.completed .tracker-line { background-color: #2ecc71; }
        .tracker-step.active .tracker-icon { background-color: #f1c40f; border-color: #f1c40f; color: white; }
        .tracker-step.active .tracker-label { color: #f1c40f; }
        .tracker-step:last-child .tracker-line { display: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        @include('customer.layouts.partials.navbar')
        @include('customer.layouts.partials.sidebar')

        <div class="content-wrapper">
            <div class="dashboard-container">
                <!-- EN-TÊTE -->
                <div class="dashboard-header">
                    <h1>Mon Tableau de Bord</h1>
                    <a href="{{ route('customer_colis.create.colis') }}" class="btn-devis">
                        <i class="fas fa-plus-circle"></i> Demander un devis
                    </a>
                </div>

               <!-- CARTES DE STATISTIQUES (MODIFIÉES) -->
               <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="stats-card">
                        <div class="icon icon-attente"><i class="fas fa-hourglass-half"></i></div>
                        <div class="info">
                            <h3>{{ $devisEnAttenteCount }}</h3>
                            <p>Devis en attente</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="stats-card">
                        <div class="icon icon-valide"><i class="fas fa-shipping-fast"></i></div>
                        <div class="info">
                            <h3>{{ $colisEnCoursCount }}</h3>
                            <p>Colis en cours</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="stats-card">
                        {{-- CORRECTION : L'icône est maintenant bien "fas fa-check-double" --}}
                        <div class="icon icon-livre"><i class="fas fa-check-double"></i></div>
                        <div class="info">
                            <h3>{{ $colisLivresCount }}</h3>
                            <p>Colis livrés</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION SUIVI DE COLIS (MODIFIÉE AVEC FILTRES) -->
            <div class="suivi-section">
                <div class="suivi-header">
                    <h2>Suivi de vos colis</h2>
                    <div class="filter-buttons">
                        {{-- On utilise la classe "btn-devis" adaptée pour les filtres --}}
                        <a href="{{ route('customer.dashboard', ['filter' => 'en_cours']) }}" 
                           class="btn-filter {{ $filter == 'en_cours' ? 'active' : '' }}">
                           <i class="fas fa-stream"></i> Colis en cours
                        </a>
                        <a href="{{ route('customer.dashboard', ['filter' => 'livre']) }}" 
                           class="btn-filter {{ $filter == 'livre' ? 'active' : '' }}">
                           <i class="fas fa-archive"></i> Colis livrés
                        </a>
                    </div>
                </div>

                @forelse($colisPourSuivi as $colis)
                    @php
                        // La logique de progression reste la même
                        $etats = [
                            'En attente' => 1, 'Validé' => 1, 'En entrepot' => 2, 'Chargé' => 3,
                            'En transit' => 4, 'Dechargé' => 5, 'Livré' => 6, 'Fermé' => 6
                        ];
                        $progressLevel = $etats[$colis['etat']] ?? 0;
                    @endphp
                    <div class="tracker-card">
                        <p class="tracker-header">Référence : {{ $colis['reference'] }}</p>
                        <div class="tracker-timeline">
                            <div class="tracker-step {{ $progressLevel >= 1 ? 'completed' : '' }}">
                                <div class="tracker-icon"><i class="fas fa-truck" style="z-index: 9;"></i></div>
                                <p class="tracker-label">EN ATTENTE</p>
                                <div class="tracker-line"></div>
                            </div>
                            <div class="tracker-step {{ $progressLevel >= 3 ? 'completed' : '' }} {{ $progressLevel == 2 ? 'active' : '' }}">
                                <div class="tracker-icon"><i class="fas fa-warehouse" style="z-index: 9;"></i></div>
                                <p class="tracker-label">EN ENTREPÔT / CHARGÉ</p>
                                <div class="tracker-line"></div>
                            </div>
                            <div class="tracker-step {{ $progressLevel >= 4 ? 'completed' : '' }} {{ $progressLevel == 4 ? 'active' : '' }}">
                                <div class="tracker-icon">
                                    @if($colis['mode_transit'] == 'maritime') <i class="fas fa-ship" style="z-index: 9;"></i> @else <i class="fas fa-plane"></i> @endif
                                </div>
                                <p class="tracker-label">EN TRANSIT</p>
                                <div class="tracker-line"></div>
                            </div>
                            <div class="tracker-step {{ $progressLevel >= 6 ? 'completed' : '' }} {{ $progressLevel == 5 ? 'active' : '' }}">
                                <div class="tracker-icon"><i class="fas fa-motorcycle" style="z-index: 9;"></i></div>
                                <p class="tracker-label">DÉCHARGÉ / LIVRÉ</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="tracker-card text-center">
                        @if($filter == 'livre')
                            <p>Vous n'avez aucun colis livré pour le moment.</p>
                        @else
                            <p>Vous n'avez aucun colis en cours de suivi.</p>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    @include('admin.layouts.partials.footer')
    </div>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('js')
</body>
</html>