@extends('chauffeur.layouts.index')

@section('title', 'Tableau de Bord')

@push('styles')
<style>
    body {
        background-color: #f4f6f9;
        overflow-x: hidden; 
    }
    .content-wrapper {
        padding: 1.5rem; 
    }
    .stat-card {
        display: flex;
        align-items: center;
        background-color: #fff;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
        border: none;
        height: 100%;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .stat-card .icon {
        font-size: 2.5rem;
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-right: 20px;
        flex-shrink: 0;
    }
    .stat-card .info h4 {
        margin: 0;
        font-size: 0.9rem;
        font-weight: 500;
        color: #888;
        white-space: nowrap;
    }
    .stat-card .info p {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
        color: #333;
    }

    .icon-primary { background-color: rgba(0, 123, 255, 0.1); color: #007bff; }
    .icon-success { background-color: rgba(40, 167, 69, 0.1); color: #28a745; }
    .icon-warning { background-color: rgba(255, 193, 7, 0.1); color: #ffc107; }

    .chart-card {
        background-color: #fff;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        height: 100%;
    }
    .chart-card .card-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 1.5rem;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
</style>
@endpush

@section('content-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0">Tableau de Bord</h1>
            <p class="text-muted">Bienvenue, {{ auth()->user()->prenom }} ! Voici un résumé de votre activité.</p>
        </div>
    </div>
@endsection

@section('content')
<!-- Première rangée : Cartes de statistiques -->
<div class="row">
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="stat-card">
            <div class="icon icon-primary">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="info">
                <h4>Missions Aujourd'hui</h4>
                <p>{{ $missionsAujourdhui }}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="stat-card">
            <div class="icon icon-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="info">
                <h4>Total RDV Effectués</h4>
                <p>{{ $missionsEffectuees }}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="stat-card">
            <div class="icon icon-warning">
                <i class="fas fa-euro-sign"></i>
            </div>
            <div class="info">
                <h4>Total Encaissé</h4>
                <p>{{ number_format($totalEncaisse, 2, ',', ' ') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Deuxième rangée : Graphiques -->
<div class="row">
    <div class="col-lg-7 mb-4">
        <div class="chart-card">
            <h5 class="card-title"><i class="fas fa-chart-bar mr-2"></i>Activité des 7 derniers jours (RDV effectués)</h5>
            <div class="chart-container">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-5 mb-4">
        <div class="chart-card">
            <h5 class="card-title"><i class="fas fa-chart-pie mr-2"></i>Répartition des Statuts de RDV</h5>
            <div class="chart-container">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Charger jQuery d'abord -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- CDN de Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const barChartData = @json($barChartData);
        const pieChartData = @json($pieChartData);

        if (document.getElementById('barChart')) {
            const ctxBar = document.getElementById('barChart').getContext('2d');
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: barChartData.labels,
                    datasets: [{
                        label: 'RDV Effectués',
                        data: barChartData.data,
                        backgroundColor: 'rgba(0, 123, 255, 0.6)',
                        borderColor: 'rgba(0, 123, 255, 1)',
                        borderWidth: 1,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
                    plugins: { legend: { display: false } }
                }
            });
        }

        if (document.getElementById('pieChart')) {
            const ctxPie = document.getElementById('pieChart').getContext('2d');
            new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: pieChartData.labels,
                    datasets: [{
                        label: 'Statuts des RDV',
                        data: pieChartData.data,
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.7)',
                            'rgba(255, 193, 7, 0.7)',
                            'rgba(0, 123, 255, 0.7)',
                            'rgba(220, 53, 69, 0.7)'
                        ],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }
    });
</script>
@endpush