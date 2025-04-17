@extends('AGENCE_CHINE.layouts.agent')

@section('content')
<div class="container-fluid">
    <!-- Statistiques principales -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5><i class="fas fa-users"></i> Expéditeurs</h5>
                    <h2>{{ $customers_count }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5><i class="fas fa-cube"></i> Produits</h5>
                    <h2>{{ $products_count }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5><i class="fas fa-boxes"></i> Colis Validés</h5>
                    <h2>{{ $colisCount }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5><i class="fas fa-coins"></i> Total Transit</h5>
                    <h2>{{ number_format($montantBilan, 2) }} €</h2>
                </div>
            </div>
        </div>
    </div>

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
                    <!-- <div class="mt-3">
                        <canvas id="volChart" height="150"></canvas>
                    </div> -->
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
                    <!-- <div class="mt-3">
                        <canvas id="conteneurChart" height="150"></canvas>
                    </div> -->
                </div>
            </div>
        </div>
    </div>

   <!-- Graphique mensuel -->
   <!-- <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="far fa-chart-bar"></i>
                            Colis par Mois (Année {{ now()->year }})
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="chart">
                            <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
         <!--  AJOUT DU CODE JAVASCRIPT ET DE L'INCLUDE CHART.JS ICI -->
         <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
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
            });
        </script> -->
        <!-- <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Répartition transport</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        Vols de cargaison: <strong>{{ $volCargaisonCount }}</strong>
                    </div>
                    <div class="alert alert-secondary">
                        Conteneurs maritimes: <strong>{{ $conteneurCount }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

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
                            <input type="text" class="form-control" id="montant_bilan" value="{{ number_format($montantBilan, 2) }} €" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-3"><i class="fas fa-calculator mr-2"></i> Saisir une opération comptable</h5>
                        <p class="text-muted">! Les Opérations d'entrées ou de sorties d'argents seront appliquées au montant de votre bilan actuel !</p>
                        <form method="POST" action="{{ route('enregistrer.operation.chine') }}">
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
                <i class="fas fa-list-alt mr-2"></i> Liste des Opérations comptables depuis le dernier encaissement du :
            </h3>
        </div>
        <div id="listeOperationsCollapse" class="collapse" aria-labelledby="listeOperationsHeader">
        <div class="card-body">
                <!-- Formulaire de filtrage -->
                <div class="card mb-3">
                <div class="card-body">
    </div>

    <!-- **Link for Export (GET Request)** -->
    <a href="{{ route('export.operations.comptables.chine') }}" class="btn btn-success btn-sm ml-2">Exporter En Excel</a>
</div>
                </div>

                <!-- Boutons de filtre rapides (à implémenter la logique) -->
                <div class="mb-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm">DATES</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm ml-1">TYPES</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm ml-1">BÉNÉFICIAIRES</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm ml-1">OBJETS</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm ml-1">MONTANTS</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm ml-1">AGENTS</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm ml-1">CONTENEUR</button>
                </div>

                <!-- Tableau des opérations comptables -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Bénéficiaire / Fournisseur</th>
                                <th>Objet</th>
                                <th>Montant (€)</th>
                                <th>Conteneur / Frais</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($operationsComptables as $operation)
                                <tr>
                                    <td>{{ $operation->date_operation }}</td>
                                    <td>{{ $operation->type_operation }}</td>
                                    <td>{{ $operation->beneficiaire_fournisseur }}</td>
                                    <td>{{ $operation->objet }}</td>
                                    <td class="text-right">{{ number_format($operation->montant, 2) }}</td>
                                    <td>{{ $operation->conteneur_frais_fonction }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Vous n'avez pas encore d'encaissement enregistré</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Section Agent -->
    <div class="card mb-4"> <!-- Ajout de mb-4 pour l'espacement -->
        <div class="card-header d-flex justify-content-between align-items-center" id="agentHeader" data-toggle="collapse" data-target="#agentCollapse" aria-expanded="true" aria-controls="agentCollapse" style="cursor: pointer;">
            <h3 class="card-title">Colis par agent</h3>
        </div>

        <div id="agentCollapse" class="collapse show" aria-labelledby="agentHeader"> <!-- Ajout de id="agentCollapse" et class="collapse show pour ouvrir par défaut -->
            @if($agentColis)  {{-- Changed condition to check for agentColis --}}
            <div class="card-body">
                <!-- Bouton Export Excel -->
                <div class="mb-4">
                    <form action="{{ route('export.agent.colis.chine') }}" method="POST">
                        @csrf
                        <input type="hidden" name="agent_id" value="{{ $agentId }}"> {{-- Use $agentId passed from controller --}}
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
                                <td class="text-right">{{ $colis['prix_colis'] }}</td>
                                <td class="text-right">{{ $colis['montant_paye'] }}</td>
                                <td class="text-right">{{ $colis['reste_a_payer'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Aucun colis trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="3">Totaux</th>
                            <th class="text-right">{{ $agentTotals['totalPrix'] }}</th>
                            <th class="text-right">{{ $agentTotals['totalPaye'] }}</th>
                            <th class="text-right">{{ $agentTotals['totalResteAPayer'] }}</th>
                        </tr>
                    </tfoot>
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

        // Initialisation de Select2 (Removed as no longer needed)
        // $('.select2').select2({
        //     placeholder: "Sélectionner un agent",
        //     allowClear: true
        // });
    });
</script>
@endsection

@section('styles')
<style>
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