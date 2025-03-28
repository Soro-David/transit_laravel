<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>

    <!-- Fonts and Icons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- App Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('css')

    <script>
        window.APP = @json([
            'currency_symbol' => config('settings.currency_symbol'),
            'warning_quantity' => config('settings.warning_quantity')
        ]);
    </script>

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
            }

            .small-box .inner h3 {
                font-size: 1.5rem;
            }

            .small-box a {
                display: block;
                margin-top: 10px;
                color: rgba(255, 255, 255, 0.8);
                text-decoration: none;
            }

            /* Couleurs de fond centralisées */
            .bg-red { background-color: #d9534f; }
            .bg-primary { background-color: #007bff; }
            .bg-green { background-color: #5cb85c; }
            .bg-teal { background-color: #20c997; }

            /* Dashboard bar */
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
                animation: scroll-left 100s linear infinite;
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

            /* Media Queries */
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

                .card-box img {
                    max-width: 80%;
                }

                .row > [class^="col-"] {
                    margin-bottom: 20px;
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
</head>
<body>
    <div class="wrapper">
        @include('customer.layouts.partials.navbar')
        @include('customer.layouts.partials.sidebar')

        <div class="content-wrapper">
            <!-- Content Header -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>@yield('content-header')</h1>
                        </div>
                        <div class="col-sm-6 text-right">
                            @yield('content-actions')
                        </div>
                    </div>
                </div>
            </section>

            @include('admin.layouts.partials.alert.success')
            @include('admin.layouts.partials.alert.error')

            <!-- Main Content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Dashboard Header -->
                    <div class="dashboard-bar p-3 mb-4 bg-white rounded">
                        <h2 class="text-center text-primary m-0">Tableau de bord</h2>
                        <div class="scrolling-container">
                            <h6 class="scrolling-agency">
                               BIENVENUE A AFT IMPORT/EXPORT,
                                <span class="weight-600">{{ auth()->user()->getFullname() }}</span>
                            </h6>
                            <h4 class="text-center" style="font-28; weight-500; mb-10;">
                                Votre partenaire de confiance dans l'import-export.
                            </h4>
                        </div>
                    </div>
            
                    <!-- Statistics Section -->
                    {{-- <div class="row">
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            <div class="small-box bg-red">
                                <div class="inner">
                                    <h3 id="volCargaisonCount"></h3>
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
                                    <h3 id="conteneurCount"></h3>
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
                                    <h3 id="colisCount"></h3>
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
                                    <h3 id="totalPrixTransit"></h3>
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
                    </div> --}}
                </div>
            </section>
        </div>
        @include('admin.layouts.partials.footer')
    </div>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('js')
</body>
</html>
