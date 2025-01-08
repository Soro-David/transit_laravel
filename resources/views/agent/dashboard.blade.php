
@extends('agent.layouts.agent')
@section('content-header', '')

@section('content')
<section>
    <div class="container-fluid">

        {{-- <div class="card-box pd-20 height-100-p mb-30">
            <div class="row align-items-center">
                <!-- Image à gauche -->
                <div class="col-12 col-md-4" style="text-align: center;">
                    <img src="{{ asset('images/font_login2.jpg') }}" alt="Welcome Image" class="img-fluid custom-img">
                </div>
                <!-- Texte à droite -->
                <div class="col-12 col-md-8">
                    <h4 class="font-20 weight-500 mb-10 text-capitalize">
                        Welcome back <span class="weight-600 font-30 text-blue">Johnny Brown!</span>
                    </h4>
                    <p class="font-18 max-width-600">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Unde hic non repellendus debitis iure, doloremque assumenda. Autem modi, corrupti, nobis ea iure fugiat, veniam non quaerat mollitia animi error corporis.
                    </p>
                </div>
            </div>
        </div> --}}
        <div class="dashboard-bar  p-3 mb-4 bg-white rounded">
            <h2 class="text-center text-primary m-0">Tableau de bord</h2>
            <div class="scrolling-container">
                {{-- <h4 class="scrolling-agency">Agence: {{$agence->nom_agence}}</h4> --}}
                <h4 class="scrolling-agency">AFT Votre Intermediaire Credible</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-4">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3>{{$products_count}}</h3>
                        <h3>{{ __('Contenaire fermer') }}</h3>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dolly-flatbed"></i>
                    </div>
                    <a href="{{route('products.index')}}" class="small-box-footer">
                        {{ __('trans.more_info') }} <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-6 col-4">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{$orders_count}}</h3>
                        <h3>{{ __('Nombre de colis') }}</h3>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <a href="{{route('orders.index')}}" class="small-box-footer">
                        {{ __('trans.more_info') }} <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-6 col-4">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>{{ config('settings.currency_symbol') }} {{ number_format($income, 2) }}</h3>
                        <h3>{{ __('trans.total_income') }}</h3>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <a href="{{route('orders.index')}}" class="small-box-footer">
                        {{ __('trans.more_info') }} <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-6 col-4">
                <div class="small-box bg-teal">
                    <div class="inner">
                        <h3>{{ config('settings.currency_symbol') }} {{ number_format($income_today, 2) }}</h3>
                        <h3>{{ __('Bateau en transit') }}</h3>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                    <a href="{{route('orders.index')}}" class="small-box-footer">
                        {{ __('trans.more_info') }} <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-12 col-md-12">
                <div class="text-center" style="width: 90%; margin: 0 auto; margin-top: 20px;">
                    {{-- <h4 class="text-center" style="color: rgb(110, 110, 108)">Le Graphe</h4> --}}
                    <canvas id="myChart"></canvas>
                </div>
            </div>
                 
        </div>
    </div>
</section>
{{-- lien chart --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sélectionner l'élément canvas par son ID
    let contentChart = document.getElementById('myChart').getContext('2d');

    // Les données du graphique
    let data = {
        labels: ['Jan', 'Fév', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'],
        datasets: [
            {
                label: 'Grain de cette Année',
                data: [21, 12, 32, 45, 3, 65, 23, 45, 56, 64, 12, 2],
                backgroundColor: 'rgba(255, 165, 0, 1)', 
                borderColor: 'rgba(255, 165, 0, 0.5)',      
                borderWidth: 1                         
            },
            {
                label: "Grain de l'Année précédente", 
                data: [12, 20, 28, 45, 34, 30, 23, 45, 56, 64, 12, 80],
                backgroundColor: 'rgba(0, 128, 0, 0.5)', 
                borderColor: 'rgba(0, 128, 0, 1)',       
                borderWidth: 1                          
            }

            
        ]
    };

    let config = {
    type: 'line',
    data: data,
    options: {
        responsive: true,  // Important pour que le graphique soit réactif
        plugins: {
            title: {
                display: true,
                text: "Le Graphe d'Evolution des activités"
            },
            legend: {
                display: true, 
                position: 'top' 
            }
        },
        scales: {
            x: {
                beginAtZero: true 
            },
            y: {
                beginAtZero: true 
            }
        }
    }
};

    // Créer le graphique
    new Chart(contentChart, config);
</script>
 <!-- Ajoutez ce style pour personnaliser l'apparence -->
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
            /* box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); */
            padding: 10px !important;
            margin: 10px 0 !important;
        }

        .card-box img {
            max-width: 100%;
            border-radius: 10px;
            /* box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); */
        }

        .card-box h4 {
            font-size: 1.2rem;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .card-box .weight-600 {
            font-size: 1.5rem;
            color: #007bff;
            font-weight: 600;
        }

        .card-box p {
            font-size: 0.95rem;
            color: #666;
        }

        .small-box {
            border-radius: 10px;
            /* padding: 1px !important; */
            text-align: center;
            color: white;
            /* margin: 15px 0; */
        }

        .small-box a {
            display: block;
            margin-top: 10px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }

        .bg-red { background-color: #d9534f; }
        .bg-primary { background-color: #007bff; }
        .bg-green { background-color: #5cb85c; }
        .bg-teal { background-color: #20c997; }



        .dashboard-bar h2 {
            font-size: 1.8rem; /* Taille de la police */
            font-weight: 600; /* Épaisseur du texte */
        }
            .dashboard-bar {
            position: relative;
        }

        .scrolling-container {
            position: relative;
            width: 100%;
            overflow: hidden; /* Masquer le texte qui dépasse */
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
                color: green; /* Texte vert au début de l'animation */
            }
            50% {
                color: rgb(255, 128, 10); /* Retour à la couleur par défaut pendant que le texte défile */
            }
            100% {
                transform: translateX(-100%); /* Le texte sort complètement de l'écran */
                color: green; /* Texte vert lorsqu'il sort de la div */
            }
        }

</style>
@endsection
