@extends('IPMS_SIMEXCI_ANGRE.layouts.agent')
@section('content-header', '')

@section('content')
<section>
    <div class="container-fluid">
        <!-- Dashboard Header -->
        <div class="dashboard-bar p-3 mb-4 bg-white rounded">
            <h2 class="text-center text-primary m-0">Tableau de bord</h2>
            <div class="scrolling-container">
                <h4 class="scrolling-agency">IPMS-SIMEX-CI Angré 8ème Tranche</h4>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3 id="volCargaisonCount">{{ $volCargaisonCount }}</h3>
                        <p>{{ __('Vol de cargaison') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-plane"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="small-box bg-teal">
                    <div class="inner">
                        <h3 id="conteneurCount">{{ $conteneurCount }}</h3>
                        <p>{{ __('Conteneur') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-ship"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3 id="colisCount">{{ $colisCount }}</h3>
                        <p>{{ __('Nombre de colis validé') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-box"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3 id="totalPrixTransit">{{ number_format($totalPrixTransit, 2) }}</h3>
                        <p>{{ __('Total des revenus') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="col-12">
                <div class="text-center" style="width: 90%; margin: 20px auto 0;">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Including Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Fetch data for colis counts
    function fetchColisCounts() {
        fetch('{{ route('ipms_angre_colis.vol-cargaison-count') }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('volCargaisonCount').textContent = data.count;
            })
            .catch(error => console.error('Erreur lors de la récupération des données de vol de cargaison:', error));

        fetch('{{ route('chine_colis.conteneur-count') }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('conteneurCount').textContent = data.count;
            })
            .catch(error => console.error('Erreur lors de la récupération des données de conteneur:', error));
    }

    // Call function every 10 seconds
    setInterval(fetchColisCounts, 10000);
    // Call function on page load
    fetchColisCounts();

    // Fetch total price transit data
    function fetchTotalPrixTransit() {
        fetch('{{ route('ipms_angre_colis.prix-total') }}')
            .then(response => response.json())
            .then(data => {
                const totalPrixTransitElement = document.getElementById('totalPrixTransit');
                totalPrixTransitElement.textContent = data.totalPrixTransit.toFixed(2);
            })
            .catch(error => console.error('Erreur lors de la récupération des données:', error));
    }

    // Call function every 10 seconds
    setInterval(fetchTotalPrixTransit, 10000);

    // Fetch colis count
    function fetchColisCount() {
        fetch('{{ route('chine_colis.count') }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('colisCount').textContent = data.colisCount;
            })
            .catch(error => console.error('Erreur lors de la récupération du compteur:', error));
    }

    // Update colis count every 10 seconds
    setInterval(fetchColisCount, 10000);
    // Initial load
    document.addEventListener('DOMContentLoaded', fetchColisCount);

    // Chart initialization
    // Chart initialization
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('myChart').getContext('2d');
    const moisNoms = ['Jan', 'Fév', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'];

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: moisNoms,
            datasets: [{
                label: 'Colis Arrivés',
                data: @json($colisData), // Inject the $colisData directly into the JavaScript
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Fetch data and update chart (not needed if using the above approach)
    async function fetchDataAndUpdateChart() {
        try {
            const response = await fetch('{{ route('ipms_angre_colis.valides-par-mois') }}');
            const data = await response.json();
            chart.data.datasets[0].data = data;
            chart.update();
        } catch (error) {
            console.error('Erreur lors de la récupération des données:', error);
        }
    }

    // Call this function initially and setInterval is unnecessary since data is being passed in view
    fetchDataAndUpdateChart();
});

</script>

<!-- Custom Styles -->
<style>
body {
    font-family: 'Poppins', sans-serif;
    background-color: #f5f5f5;
    margin: 0;
    padding: 0;
}

.content-wrapper {
    padding: 20px;
}

.card-box {
    background-color: #fff;
    border-radius: 15px;
    padding: 10px !important;
    margin: 10px 0 !important;
}

.card-box img {
    max-width: 100%;
    border-radius: 10px;
}

.card-box h4 {
    font-size: 1.2rem;
    font-weight: 500;
    margin-bottom: 10px;
}

.card-box p {
    font-size: 0.95rem;
    color: #666;
}

.small-box {
    border-radius: 10px;
    text-align: center;
    color: white;
    margin: 15px 0;
    padding: 15px;
    transition: transform 0.3s ease;
}

.small-box:hover {
    transform: scale(1.05);
}

.small-box .inner h3 {
    font-size: 1.5rem;
    margin: 0;
}

.small-box .inner p {
    font-size: 1rem;
    margin: 0;
    font-weight: 300;
}

.small-box a {
    display: block;
    margin-top: 10px;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
}

/* Background Colors */
.bg-red { background-color: #d9534f; }
.bg-primary { background-color: #007bff; }
.bg-green { background-color: #5cb85c; }
.bg-teal { background-color: #20c997; }

/* Dashboard Header */
.dashboard-bar h2 {
    font-size: 1.8rem;
    font-weight: 600;
    text-align: center;
    color: #007bff;
}

.scrolling-container {
    width: 100%;
    overflow: hidden;
}

.scrolling-agency {
    white-space: nowrap;
    display: inline-block;
    font-size: 40px;
    animation: scroll-left 20s linear infinite;
}

@keyframes scroll-left {
    0% {
        transform: translateX(100%);
        color: green;
    }
    50% {
        color: rgb(255, 128, 10);
    }
    100% {
        transform: translateX(-100%);
        color: green;
    }
}

/* Responsive Design */
@media (max-width: 1200px) {
    .small-box {
        margin-bottom: 20px;
    }
}

@media (max-width: 992px) {
    .dashboard-bar h2 {
        font-size: 1.5rem;
    }
    .scrolling-agency {
        font-size: 30px;
    }
}

@media (max-width: 768px) {
    .dashboard-bar h2 {
        font-size: 1.2rem;
    }
    .scrolling-agency {
        font-size: 25px;
    }
    .small-box .inner h3 {
        font-size: 1.5rem;
    }
    .small-box .inner {
        padding: 10px;
    }
}

@media (max-width: 576px) {
    .scrolling-agency {
        font-size: 20px;
    }
    .dashboard-bar h2 {
        font-size: 1rem;
    }
    .small-box .inner h3 {
        font-size: 1.2rem;
    }
    .small-box {
        padding: 15px;
    }
}
</style>
@endsection
