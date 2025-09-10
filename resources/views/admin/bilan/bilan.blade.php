@extends('admin.layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Statistiques principales -->
     <!-- Carte Principale Mise en Évidence -->
     <div class="row mb-4">
        <div class="col-12">
            {{-- Note: La classe "text-white" est redondante car gérée par le CSS ci-dessous --}}
            <div class="card text-dark text-center p-3" style="background-color: #ffb300;
                <h5 class="card-title" style="font-size: 1.1rem; font-weight: 300;">
                    <i class="fas fa-coins"></i> Total Transit Global
                </h5>
                <h2 class="card-text display-4" style="font-weight: 600;">
                    {{ number_format($totalPrixTransitColis, 0, ',', '.') }}€
                </h2>
            </div>
        </div>
    </div>

    <!-- Statistiques secondaires -->
    <div class="row mb-4">
        <div class="col-md-4 col-sm-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5><i class="fas fa-users"></i> Expéditeurs</h5>
                    <h2>{{ $customers_count }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5><i class="fas fa-cube"></i> Produits</h5>
                    <h2>{{ $products_count }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5><i class="fas fa-boxes"></i> Colis Validés</h5>
                    <h2>{{ $colisCount }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- LIGNE POUR LES STATISTIQUES PAR AGENCE -->
    <div class="row mb-4">
        <div class="col-md-6 col-sm-6">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <h5><i class="fas fa-coins"></i> Total Transit Louis Bleriot</h5>
                    <h2>{{ number_format($totalTransitLouisBleriot, 0, ',', '.') }}€</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6">
            <div class="card" style="background-color: #C70039; color: white;">
                <div class="card-body">
                    <h5><i class="fas fa-coins"></i> Total Transit Chine</h5>
                    <h2>{{ number_format($totalTransitChine, 0, ',', '.') }}Fcfa</h2>
                    <h6 class="mt-2" style="font-weight: 300;">Soit env. {{ number_format($totalTransitChineEnEuros, 2, ',', '.') }}€</h6>
                </div>
            </div>
        </div>
    </div>
    <!-- FIN DE LA LIGNE -->

    <!-- Section Transport -->
    <div class="row mb-4">
        <!-- Vols de cargaison -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title">
                        <i class="fas fa-plane"></i> Vols de Cargaison
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box bg-gradient-light">
                                <span class="info-box-icon"><i class="fas fa-plane-departure"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Vols</span>
                                    <span class="info-box-number">{{ $volCargaisonCount }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box bg-gradient-light">
                                <span class="info-box-icon"><i class="fas fa-box-open"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Colis Aériens</span>
                                    <span class="info-box-number">{{ $colisAerienCount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conteneurs maritimes -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h3 class="card-title">
                        <i class="fas fa-ship"></i> Conteneurs Maritimes
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box bg-gradient-light">
                                <span class="info-box-icon"><i class="fas fa-ship"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Conteneurs</span>
                                    <span class="info-box-number">{{ $conteneurCount }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box bg-gradient-light">
                                <span class="info-box-icon"><i class="fas fa-boxes"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Colis Maritimes</span>
                                    <span class="info-box-number">{{ $colisMaritimeCount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Opération Comptable et Montant Bilan (Collapsible) -->
    <div class="card mb-4">
        <div class="card-header bg-lightblue" id="operationBilanHeader" data-toggle="collapse" data-target="#operationBilanCollapse" aria-expanded="true" aria-controls="operationBilanCollapse" style="cursor: pointer;">
            <h3 class="card-title d-flex align-items-center">
                <i class="fas fa-calculator mr-2"></i> Opération Comptable & Bilan
            </h3>
        </div>
        <div id="operationBilanCollapse" class="collapse show" aria-labelledby="operationBilanHeader">
            <div class="card-body bg-light">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3"><i class="fas fa-money-bill-wave mr-2"></i> Montant Total Présent dans Votre Bilan actuel</h5>
                        <div class="form-group">
                            <label for="montant_bilan">Montant de votre Bilan:</label>
                            <input type="text" class="form-control" id="montant_bilan" value="{{ number_format($montantBilan, 0, ',', '.') }}€" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-3"><i class="fas fa-calculator mr-2"></i> Saisir une opération comptable</h5>
                        <p class="text-muted">! Les Opérations d'entrées ou de sorties d'argents seront appliquées au montant de votre bilan actuel !</p>
                        <form method="POST" action="{{ route('bilan.enregistrerOperation') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_operation">Date de l'opération:</label>
                                        <input type="date" class="form-control" id="date_operation" name="date_operation" value="{{ now()->format('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="type_operation">Type d'opération:</label>
                                        <select class="form-control" id="type_operation" name="type_operation">
                                            <option value="SORTIE D'ARGENT" selected>SORTIE D'ARGENT</option>
                                            <option value="ENTREE D'ARGENT">ENTREE D'ARGENT</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="beneficiaire_fournisseur">Bénéficiaire / Fournisseur:</label>
                                <input type="text" class="form-control" id="beneficiaire_fournisseur" name="beneficiaire_fournisseur">
                            </div>
                            <div class="form-group">
                                <label for="objet">Objet:</label>
                                <input type="text" class="form-control" id="objet" name="objet">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="montant">Montant en Euros*:</label>
                                        <input type="number" class="form-control" id="montant" name="montant" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="conteneur_frais_fonction">Conteneur / Frais fonction.. *</label>
                                        <select class="form-control" id="conteneur_frais_fonction" name="conteneur_frais_fonction">
                                            <option value="FRAIS DE FONCTIONNEMENT">FRAIS DE FONCTIONNEMENT</option>
                                            @foreach($conteneursDisponibles as $conteneur)
                                                <option value="{{ $conteneur }}">{{ $conteneur }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Enregistrer l'opération</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Liste des Opérations Comptables (Collapsible) -->
    <div class="card mb-4">
        <div class="card-header bg-lightblue" id="listeOperationsHeader" data-toggle="collapse" data-target="#listeOperationsCollapse" aria-expanded="false" aria-controls="listeOperationsCollapse" style="cursor: pointer;">
            <h3 class="card-title d-flex align-items-center">
                <i class="fas fa-list-alt mr-2"></i> Liste des Opérations comptables
            </h3>
        </div>
        <div id="listeOperationsCollapse" class="collapse" aria-labelledby="listeOperationsHeader">
            <div class="card-body">
                <a href="{{ route('export.operations.comptables.bilan') }}" class="btn btn-success btn-sm mb-3">Exporter En Excel</a>

                <!-- Tableau des opérations comptables -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Date</th>
                                <th>Agent</th>
                                <th>Type</th>
                                <th>Bénéficiaire / Fournisseur</th>
                                <th>Objet</th>
                                <th>Montant</th>
                                <th>Conteneur / Frais</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($operationsComptables as $operation)
                                <tr>
                                    <td>{{ $operation->date_operation }}</td>
                                    <td>{{ optional($operation->agent)->nom }}</td>
                                    <td>{{ $operation->type_operation }}</td>
                                    <td>{{ $operation->beneficiaire_fournisseur }}</td>
                                    <td>{{ $operation->objet }}</td>
                                    <td class="text-right">{{ number_format($operation->montant, 0, ',', '.') }}€</td>
                                    <td>{{ $operation->conteneur_frais_fonction }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Aucune opération enregistrée</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Agent -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Colis par agent</h3>
            <form method="GET" action="{{ route('bilan.bilan') }}" class="form-inline">
                <select 
                    name="agent_id" 
                    class="form-control mr-2 select2"
                    onchange="this.form.submit()"
                >
                    <option value="">Sélectionner un agent</option>
                    @foreach($agents as $agent)
                        <option 
                            value="{{ $agent->id }}" 
                            {{ $selectedAgentId == $agent->id ? 'selected' : '' }}
                        >
                            {{ $agent->nom }} ({{ $agent->colis_valides_count }} colis)
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        @if($selectedAgentId)
        <div class="card-body">
            <!-- Bouton Export Excel -->
            <div class="mb-4">
                <form action="{{ route('bilan.export') }}" method="POST">
                    @csrf
                    <input type="hidden" name="agent_id" value="{{ $selectedAgentId }}">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Exporter Excel
                    </button>
                </form>
            </div>

            <!-- Tableau des colis -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>Référence</th>
                            <th>Date Paiement</th>
                            <th>Mode Transport</th>
                            <th>Prix Total (€)</th>
                            <th>Montant Payé (€)</th>
                            <th>Reste à Payer (€)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agentColis as $colis)
                            <tr>
                                <td>{{ $colis['reference_colis'] }}</td>
                                <td>{{ $colis['date_paiement'] }}</td>
                                <td>
                                    @if($colis['mode_transit'] == 'aérien')
                                        <span class="badge bg-info">Aérien</span>
                                    @else
                                        <span class="badge bg-secondary">Maritime</span>
                                    @endif
                                </td>
                                <!-- NOTE: (float) est utilisé pour s'assurer que le re-formatage fonctionne -->
                                <td class="text-right">{{ number_format((float)$colis['prix_colis'], 0, ',', '.') }}€</td>
                                <td class="text-right">{{ number_format((float)$colis['montant_paye'], 0, ',', '.') }}€</td>
                                <td class="text-right">{{ number_format((float)$colis['reste_a_payer'], 0, ',', '.') }}€</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Aucun colis trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
<!-- Scripts -->
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Graphique des colis par mois
        var areaChartData = {
            labels: @json($moisNoms),
            datasets: [{
                label: 'Colis',
                backgroundColor: 'rgba(60,141,188,0.9)',
                borderColor: 'rgba(60,141,188,0.8)',
                pointRadius: false,
                pointColor: '#3b8bba',
                pointStrokeColor: 'rgba(60,141,188,1)',
                pointHighlightFill: '#fff',
                pointHighlightStroke: 'rgba(60,141,188,1)',
                data: @json($colisData)
            }]
        };

        var barChartCanvas = document.getElementById('barChart').getContext('2d');
        new Chart(barChartCanvas, {
            type: 'bar',
            data: areaChartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Graphique des vols
        var volChart = new Chart(
            document.getElementById('volChart').getContext('2d'),
            {
                type: 'doughnut',
                data: {
                    labels: ['Validés', 'En cours', 'Annulés'],
                    datasets: [{
                        data: [@json($volValideCount), @json($volEnCoursCount), @json($volAnnuleCount)],
                        backgroundColor: [
                            '#28a745',
                            '#17a2b8',
                            '#dc3545'
                        ]
                    }]
                }
            }
        );

        // Graphique des conteneurs
        var conteneurChart = new Chart(
            document.getElementById('conteneurChart').getContext('2d'),
            {
                type: 'doughnut',
                data: {
                    labels: ['Validés', 'En cours', 'Annulés'],
                    datasets: [{
                        data: [@json($conteneurValideCount), @json($conteneurEnCoursCount), @json($conteneurAnnuleCount)],
                        backgroundColor: [
                            '#6c757d',
                            '#17a2b8',
                            '#dc3545'
                        ]
                    }]
                }
            }
        );

        // Initialisation de Select2
        $('.select2').select2({
            placeholder: "Sélectionner un agent",
            allowClear: true
        });
    });
</script>
@endsection

@section('styles')
<style>
    /* Style pour la carte principale mise en évidence */
    #total-transit-global-card {
        /* NOUVEAU DÉGRADÉ ET AJOUT DE !important POUR FORCER LE STYLE */
        background: linear-gradient(45deg, #0f2027, #203a43, #2c5364) !important;
        border: none !important;
        color: white !important; /* Force la couleur du texte en blanc */
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        transition: transform 0.3s ease-in-out;
    }

    #total-transit-global-card:hover {
        transform: translateY(-5px);
    }
    
    /* Assure que le texte à l'intérieur hérite de la couleur blanche forcée */
    #total-transit-global-card h5, #total-transit-global-card h2 {
        color: white !important;
    }

    .select2-container--default .select2-selection--single {
        height: 38px;
        padding-top: 4px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .table th {
        white-space: nowrap;
    }
    .text-right {
        text-align: right;
    }
    .info-box {
        cursor: pointer;
        transition: all 0.3s;
    }
    .info-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .card-header {
        font-weight: bold;
    }
</style>
@endsection
@endsection