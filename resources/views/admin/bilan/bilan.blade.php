@extends('admin.layouts.admin')

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
                    <h2>{{ number_format($totalPrixTransit, 2) }} €</h2>
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
                    <div class="mt-3">
                        <canvas id="volChart" height="150"></canvas>
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
                    <div class="mt-3">
                        <canvas id="conteneurChart" height="150"></canvas>
                    </div>
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