<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', config('app.name'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @yield('css')
    <script>
        window.APP = @json([
            'currency_symbol' => config('settings.currency_symbol'),
            'warning_quantity' => config('settings.warning_quantity')
        ]);
    </script>
    <style>
        /* Global styles */
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
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px 0;
        }

        .card-box img {
            max-width: 100%;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
            padding: 20px;
            text-align: center;
            color: white;
            margin: 15px 0;
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .card-box .row {
                flex-direction: column;
                text-align: center;
            }

            .card-box img {
                margin-bottom: 15px;
            }

            .small-box {
                font-size: 14px;
            }
            
        }
        .scrolling-agency {
    white-space: nowrap;
    display: inline-block;
    font-size: 20px;
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

            <!-- Main Content -->
            <section class="content">
                @include('admin.layouts.partials.alert.success')
                @include('admin.layouts.partials.alert.error')
                @yield('content')
                
                <!-- Custom Section -->
                <div class="container">
                    <div class="card-box">
                        <div class="row align-items-center">
                            <div class="container">
                                <div class="col-md-8">
                                    <h6 class="scrolling-agency text-center">
                                        Bienvenue chez AFT import/export, 
                                        <span class="weight-600">{{ auth()->user()->getFullname() }}</span>
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <h4 class="text-center font-20 weight-500 mb-10 ">
                            Votre partenaire de confiance dans l'import-export.
                        </h4>
                    </div>

                    <!-- Small Boxes -->
                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <div class="small-box bg-red">
                                <div class="inner">
                                    <h3>{{ __('Mes Colis') }}</h3>
                                </div>
                                <div class="icon"><i class="fas fa-dolly-flatbed"></i></div>
                                <a href="{{ route('products.index') }}" class="small-box-footer">
                                    {{ __('trans.more_info') }} <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-12">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3>{{ __('Nombre de colis') }}</h3>
                                </div>
                                <div class="icon"><i class="fas fa-chart-line"></i></div>
                                <a href="{{ route('orders.index') }}" class="small-box-footer">
                                    {{ __('trans.more_info') }} <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-12">
                            <div class="small-box bg-green">
                                <div class="inner">
                                    <h3>{{ config('settings.currency_symbol') }} </h3>
                                    <h3>{{ __('trans.total_income') }}</h3>
                                </div>
                                <div class="icon"><i class="fas fa-dollar-sign"></i></div>
                                <a href="{{ route('orders.index') }}" class="small-box-footer">
                                    {{ __('trans.more_info') }} <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-12">
                            <div class="small-box bg-teal">
                                <div class="inner">
                                    <h3>{{ config('settings.currency_symbol') }}</h3>
                                    <h3>{{ __('Bateau en transit') }}</h3>
                                </div>
                                <div class="icon"><i class="fas fa-money-check-alt"></i></div>
                                <a href="{{ route('orders.index') }}" class="small-box-footer">
                                    {{ __('trans.more_info') }} <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        @include('admin.layouts.partials.footer')
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    
    @yield('js')
</body>
</html>
