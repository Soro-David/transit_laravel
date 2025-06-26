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

                /* Styles personnalisés existants */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }
        header { color: white; padding: 1rem 0; }
        .navbar-brand { font-size: 1.5rem; font-weight: bold; }
        .navbar-nav .nav-link { color: white !important; padding: 0.5rem 1rem; }
        .navbar-nav .nav-link:hover { color: #ddd !important; }
        main { padding: 2rem 0; }
        h1 { font-size: 2.5rem; margin-bottom: 1rem; text-align: center; }
        p { font-size: 1.1rem; line-height: 1.6; }
        footer { background-color: #343a40; color: white; text-align: center; padding: 1rem 0; }
        .navbar-collapse { justify-content: center; }
        .navbar-nav .nav-link { color: white !important; font-weight: bold; padding: 0.5rem 1rem; }
        .navbar-nav .nav-link:hover { color: orange !important; }
        .navbar-nav .nav-link.active { text-decoration: underline; text-decoration-color: orange; }
        .demande-devis:hover { background-color: orange; color: white; }
        .contactez-nous:hover { background-color: white; color: red; }
        @media (max-width: 767px) { .navbar-brand img { max-height: 50px; } }

        /* --- STYLES POUR LA NOUVELLE SECTION SERVICES ET AGENCES --- */

        .header-section {
            padding: 3rem 0;
            background: #e9ecef; /* Un fond léger pour l'en-tête de page */
        }
        
        .header-section p {
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }

        .services-agencies-section {
            padding: 4rem 0;
        }

        .info-card {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 2.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            height: 100%; /* Assure que les colonnes ont la même hauteur */
            margin-bottom: 2rem;
        }

        .info-card h3 {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 3px solid orange;
            display: inline-block;
        }
        
        .main-list {
            list-style: none;
            padding-left: 0;
        }

        .main-list > li {
            margin-bottom: 2rem;
        }

        .main-list .bullet {
            font-size: 1.5rem;
            margin-right: 15px;
            vertical-align: top;
            display: inline-block;
            line-height: 1.2;
        }
        
        .main-list strong {
            display: inline-block;
            margin-bottom: 0.5rem;
        }

        .details-list {
            list-style: none;
            padding-left: 45px; /* Aligne avec le texte après le bullet */
            margin-top: 0.5rem;
        }

        .details-list li {
            margin-bottom: 0.8rem;
            color: #555;
            display: flex;
            align-items: flex-start;
        }
        
        .details-list i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
            margin-top: 4px; /* Petit ajustement vertical */
            color: orange;
        }

        .text-orange {
            color: orange !important;
        }

        /* Styles pour le Footer */
        .footer-dark { background-color: #343a40; color: #fff; padding: 40px 0; }
        .footer-dark h3 { font-size: 1.2rem; margin-bottom: 15px; }
        .footer-dark ul { list-style: none; padding: 0; }
        .footer-dark ul li a { color: #aaa; text-decoration: none; transition: color 0.3s; }
        .footer-dark ul li a:hover { color: orange; }
        .footer-dark .social a { color: #fff; font-size: 1.5rem; margin: 0 10px; transition: color 0.3s; }
        .footer-dark .social a:hover { color: orange; }
        .copyright { text-align: center; border-top: 1px solid #444; padding-top: 20px; margin-top: 20px; }

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
                               BIENVENUE A AFRIQUE FRET TRANSIT IMPORT EXPORT,
                                <span class="weight-600">{{ auth()->user()->getFullname() }}</span>
                            </h6>
                            <h4 class="text-center" style="font-28; weight-500; mb-10;">
                                AFT Emport Export Votre Intermédiaire Crédible
                            </h4>
                        </div>
                    </div>
            
                 <section class="services-agencies-section">
                <div class="row">
                    <!-- COLONNE AGENCES -->
                    <div class="col-lg-8 d-flex">
                        <div class="info-card w-100">
                            <h3>NOS AGENCES</h3>
                            <ul class="main-list">
                                <li>
                                    <span class="bullet">🇫🇷</span> <strong class="text-orange">FRANCE</strong>
                                    <ul class="details-list">
                                       <li><i class="fas fa-map-marker-alt"></i> 7, avenue Louis Blériot 93120 La Courneuve</li>
                                       <li><i class="fas fa-phone-alt"></i> <strong>Contact :</strong> +33 1 86 78 69 67</li>
                                    </ul>
                                </li>
                                <li>
                                    <span class="bullet">🇨🇳</span> <strong class="text-orange">CHINE</strong>
                                    <ul class="details-list">
                                         <li><i class="fas fa-ship"></i> <strong>Maritime :</strong> ⼴东省佛⼭市南海区⾥⽔镇河塱沙路D2仓。</li>
                                         <li><i class="fas fa-plane"></i> <strong>Aérien :</strong> ⼴州市环市中路205号恒⽣⼤厦B座室918</li>
                                         <li><i class="fas fa-phone-alt"></i> <strong>Contact :</strong> +86 13 67 89 15 049</li>
                                    </ul>
                                </li>
                                <li>
                                    <span class="bullet">🇨🇮</span> <strong class="text-orange">CÔTE D'IVOIRE, ABIDJAN</strong>
                                   <ul class="details-list">
                                         <li><i class="fas fa-ship"></i> <strong>Maritime :</strong> CARREFOUR ANGRE</li>
                                         <li><i class="fas fa-phone-alt"></i> <strong>Contact :</strong> +225 5 84 40 22 00</li>
                                         <li><i class="fas fa-plane"></i> <strong>Aérien :</strong> Carrefour NELSON MANDELA ANGRE 8eme tranche</li>
                                         <li><i class="fas fa-phone-alt"></i> <strong>Contact :</strong> +225 758069896</li>
                                   </ul>
                                </li>
                                <li>
                                    <span class="bullet">🇨🇮</span> <strong class="text-orange">CÔTE D'IVOIRE, SAN-PÉDRO</strong>
                                   <ul class="details-list">
                                         <li><i class="fas fa-map-marker-alt"></i> San Pedro au feu de la petite mairie coté voie pavée (UTE )</li>
                                         <li><i class="fas fa-phone-alt"></i> <strong>Contact :</strong> +225 27 33 74 95 19 / +225 74 940 74 02</li>
                                   </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- COLONNE SERVICES -->
                    <div class="col-lg-4 d-flex">
                        <div class="info-card w-100">
                            <h3>NOS SERVICES</h3>
                            <ul class="main-list">
                                <li>
                                    <span class="bullet text-orange">🔘</span> <strong class="text-orange">FRET MARITIME</strong>
                                    <ul class="details-list">
                                        <li>Transport de conteneurs (20’, 40’, 40’ HQ)</li>
                                        <li>Groupage (LCL) et plein chargement (FCL)</li>
                                        <li>Suivi personnalisé et sécurisé des marchandises</li>
                                    </ul>
                                </li>
                                <li>
                                    <span class="bullet text-orange">🔘</span> <strong class="text-orange">FRET AÉRIEN</strong>
                                    <ul class="details-list">
                                        <li>Livraison rapide et sécurisée pour les envois urgents</li>
                                        <li>Gestion des formalités douanières</li>
                                    </ul>
                                </li>
                                <li>
                                    <span class="bullet text-orange">🔘</span> <strong class="text-orange">SERVICES COMPLÉMENTAIRES</strong>
                                    <ul class="details-list">
                                        <li>Dédouanement et conseils en logistique</li>
                                        <li>Stockage et distribution locale</li>
                                        <li>Assurance des marchandises</li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
        </section>
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
