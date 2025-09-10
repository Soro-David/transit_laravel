<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Services - AFT Import Export</title>
    <!-- Liens CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        /* ===================================== */
        /*          Styles Généraux              */
        /* ===================================== */
        body {
            font-family: 'Poppins', sans-serif; /* Utilisation d'une police plus moderne */
            margin: 0;
            padding: 0;
            background-color: #f4f7f6; /* Fond légèrement plus doux */
            color: #333;
            line-height: 1.6;
            overflow-x: hidden; /* Empêche le défilement horizontal indésirable */
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
            color: #2c3e50;
        }

        /* ===================================== */
        /*           Header Section              */
        /* ===================================== */


        .navbar-brand img {
            max-height: 70px;
            transition: transform 0.3s ease;
        }
        .navbar-brand img:hover {
            transform: scale(1.05);
        }

        .navbar-nav .nav-link {
            color: #fff !important;
            font-weight: 600;
            padding: 0.75rem 1.2rem;
            transition: color 0.3s ease, background-color 0.3s ease, border-bottom 0.3s ease;
            border-bottom: 3px solid transparent;
        }

        .navbar-nav .nav-link:hover {
            color: #ffe0b2 !important; /* Couleur plus claire au survol */
            border-bottom: 3px solid #fff;
        }

        .navbar-nav .nav-link.active {
            color: #fff !important;
            border-bottom: 3px solid #ff7b00; /* Soulignement orange vif pour l'actif */
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 4px 4px 0 0;
        }

        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.2);
        }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* ===================================== */
        /*       Hero Section (Nos Services)     */
        /* ===================================== */
        .hero-section {
            padding: 5rem 0;
            background: linear-gradient(135deg, #ff9933, #e67e22);
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
            /* border-bottom-left-radius: 50% 20px; */
            /* border-bottom-right-radius: 50% 20px; */
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            animation: moveTopLeft 15s infinite alternate;
        }
        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            animation: moveBottomRight 15s infinite alternate;
        }
        @keyframes moveTopLeft {
            from { transform: translate(0, 0); }
            to { transform: translate(100px, 100px); }
        }
        @keyframes moveBottomRight {
            from { transform: translate(0, 0); }
            to { transform: translate(-100px, -100px); }
        }

        .hero-section h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .hero-section {
            max-width: 700px;
            margin: 0 auto 2rem auto;
            font-size: 1.15rem;
            opacity: 0.9;
        }

        /* ===================================== */
        /*    Core Services & Image Section      */
        /* ===================================== */
        .core-services-section {
            padding: 4rem 0;
            background-color: #fff;
        }

        .core-services-section h2 {
            text-align: center;
            margin-bottom: 3rem;
            color: #2c3e50;
            font-weight: 700;
            position: relative;
            padding-bottom: 15px;
        }
        
        .core-services-section h2:after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #ff7b00, orange);
            margin: 15px auto 0;
            border-radius: 2px;
        }

        .service-list-card {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 3rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            height: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .service-list-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        }

        .service-list-card h3 {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 4px solid #ff7b00; /* Ligne de séparation plus épaisse */
            display: inline-block;
            font-size: 1.8rem;
        }
        
        .service-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 2.5rem; /* Espacement accru */
        }

        .service-item:last-child {
            margin-bottom: 0;
        }

        .service-item .icon-wrapper {
            background: linear-gradient(45deg, #ff7b00, #f39c12); /* Dégradé pour l'icône */
            color: white;
            border-radius: 50%;
            width: 65px; /* Taille légèrement plus grande */
            height: 65px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.8rem; /* Icône plus grande */
            margin-right: 1.8rem;
            flex-shrink: 0;
            box-shadow: 0 4px 15px rgba(255, 123, 0, 0.3);
        }
        
        .service-item .content h4 {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.6rem;
            font-size: 1.3rem;
        }

        .service-item .content {
            font-size: 1rem;
            color: #555;
            line-height: 1.6;
            margin-bottom: 0;
        }
        p {
            font-size: 1.25rem;
            line-height: 1.7;
            color: #fffefc;
        }

        .image-container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }
        .image-container img {
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
        }
        .image-container img:hover {
            transform: scale(1.02);
        }

        /* ===================================== */
        /*   Complimentary Services Section      */
        /* ===================================== */
        .complementary-services-section {
            padding: 4rem 0;
            background-color: #f8f9fa; /* Fond légèrement différent */
        }
        
        .complementary-services-section h2 {
            text-align: center;
            margin-bottom: 3.5rem;
            color: #2c3e50;
            font-weight: 700;
            position: relative;
            padding-bottom: 15px;
        }
        
        .complementary-services-section h2:after {
            content: '';
            display: block;
            width: 90px;
            height: 4px;
            background: linear-gradient(90deg, #ff7b00, #f39c12);
            margin: 15px auto 0;
            border-radius: 2px;
        }
        
        .services-carousel {
            display: flex;
            overflow-x: auto;
            padding: 1.5rem 0.5rem;
            scrollbar-width: thin;
            scrollbar-color: orange #f1f1f1;
            gap: 2rem; /* Espacement entre les cartes */
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Styles pour la barre de défilement */
        .services-carousel::-webkit-scrollbar {
            height: 10px;
        }
        
        .services-carousel::-webkit-scrollbar-track {
            background: #e0e0e0;
            border-radius: 10px;
        }
        
        .services-carousel::-webkit-scrollbar-thumb {
            background: #ff7b00;
            border-radius: 10px;
            border: 2px solid #e0e0e0; /* Bordure pour un meilleur contraste */
        }
        
        .service-detail-card {
            flex: 0 0 350px; /* Largeur fixe pour les cartes */
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            scroll-snap-align: start;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            border-top: 6px solid transparent; /* Bordure au-dessus */
            border-image: linear-gradient(90deg, #ff7b00, #f39c12) 1;
        }
        
        .service-detail-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.15);
        }
        
        .service-detail-card .icon-wrapper {
            width: 75px;
            height: 75px;
            background: linear-gradient(45deg, #ffcc80, #ffb347); /* Dégradé doux pour l'icône */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            box-shadow: 0 3px 10px rgba(255, 165, 0, 0.2);
        }
        
        .service-detail-card .icon-wrapper i {
            font-size: 2.2rem;
            color: #fff; /* Icône blanche */
        }
        
        .service-detail-card h4 {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1.2rem;
            font-size: 1.5rem;
        }
        
        .service-detail-card .service-description {
            color: #fffafa;
            margin-bottom: 1.8rem;
            font-size: 1rem;
            line-height: 1.6;
        }
        
        .service-features, .service-advantages {
            margin-bottom: 1.5rem;
        }
        
        .service-features h5, .service-advantages h5 {
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
            font-size: 1.15rem;
            display: flex;
            align-items: center;
        }
        
        .service-features h5:before {
            content: '\f0a9'; /* Icône de main droite */
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            margin-right: 0.8rem;
            color: #ff7b00;
            font-size: 1.1em;
        }
        .service-advantages h5:before {
            content: '\f058'; /* Icône de coche cerclée */
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            margin-right: 0.8rem;
            color: #28a745; /* Vert pour les avantages */
            font-size: 1.1em;
        }
        
        .features-list, .advantages-list {
            list-style-type: none;
            padding-left: 0;
            margin-bottom: 0;
        }
        
        .features-list li, .advantages-list li {
            position: relative;
            padding-left: 2rem; /* Plus d'espace pour l'icône */
            margin-bottom: 0.6rem;
            font-size: 0.98rem;
            color: #555;
        }
        
        .features-list li:before {
            content: '\f00c'; /* Icône de coche */
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            left: 0;
            color: #28a745;
        }
        
        .advantages-list li:before {
            content: '\f05a'; /* Icône d'information cerclée */
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            left: 0;
            color: #17a2b8; /* Bleu pour les avantages (différent de feature) */
        }
        
        /* Indicateurs de défilement */
        .scroll-indicator {
            text-align: center;
            margin-top: 2.5rem;
            color: #777;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        
        .scroll-indicator i {
            color: #ff7b00;
            animation: bounceScroll 2s infinite ease-in-out;
            font-size: 1.2em;
        }
        
        @keyframes bounceScroll {
            0%, 100% {transform: translateX(0);}
            50% {transform: translateX(-8px);}
        }
        
        /* ===================================== */
        /*              Footer Section           */
        /* ===================================== */
        .footer-dark { 
            background-color: #2c3e50; /* Couleur sombre plus profonde */
            color: #ecf0f1; /* Texte gris clair */
            padding: 60px 0 30px 0;
            border-top: 5px solid #ff7b00; /* Ligne orange en haut du footer */
        }
        .footer-dark h3 { 
            font-size: 1.3rem; 
            margin-bottom: 25px; 
            color: #ff7b00; /* Titres orange */
            font-weight: 700;
        }
        .footer-dark ul { 
            list-style: none; 
            padding: 0; 
            margin-bottom: 30px;
        }
        .footer-dark ul li {
            margin-bottom: 10px;
        }
        .footer-dark ul li a { 
            color: #bdc3c7; 
            text-decoration: none; 
            transition: color 0.3s ease; 
            font-size: 0.95rem;
        }
        .footer-dark ul li a:hover { 
            color: #ff7b00; /* Orange au survol */
            padding-left: 5px;
        }
        .footer-dark .social a { 
            color: #ecf0f1; 
            font-size: 1.8rem; 
            margin: 0 12px; 
            transition: color 0.3s ease, transform 0.3s ease;
        }
        .footer-dark .social a:hover { 
            color: #ff7b00; 
            transform: translateY(-3px);
        }
        .copyright { 
            text-align: center; 
            border-top: 1px solid rgba(255, 255, 255, 0.1); 
            padding-top: 25px; 
            margin-top: 40px; 
            font-size: 0.9rem;
            color: #bdc3c7;
        }

        /* ===================================== */
        /*         Responsive Adjustments        */
        /* ===================================== */
        @media (max-width: 1200px) {
            .hero-section h1 { font-size: 3rem; }
        }

        @media (min-width: 992px) {
            .services-carousel {
                justify-content: center;
                overflow-x: hidden;
                flex-wrap: wrap;
                gap: 2rem;
            }
            
            .service-detail-card {
                flex: 0 0 calc(50% - 2rem);
                max-width: calc(50% - 2rem);
            }
            
            .scroll-indicator {
                display: none;
            }
        }
        
        @media (max-width: 991px) {
            .hero-section { padding: 4rem 0; }
            .hero-section h1 { font-size: 2.5rem; }
            .hero-section p { font-size: 1rem; }

            .service-list-card { padding: 2rem; }
            .service-list-card h3 { font-size: 1.5rem; }
            .service-item .icon-wrapper { width: 55px; height: 55px; font-size: 1.5rem; margin-right: 1.2rem; }
            .service-item .content h4 { font-size: 1.2rem; }
            .service-item .content p { font-size: 0.95rem; }

            .complementary-services-section h2, .agences-section h2 { font-size: 2rem; }
            .service-detail-card {
                flex: 0 0 320px;
                padding: 2rem;
            }
            .service-detail-card .icon-wrapper { width: 65px; height: 65px; font-size: 1.8rem; }
            .service-detail-card h4 { font-size: 1.3rem; }
            .service-detail-card .service-description { font-size: 0.95rem; }
            .features-list li, .advantages-list li { font-size: 0.9rem; }
            .scroll-indicator { font-size: 0.9rem; gap: 0.5rem; }

            .footer-dark h3 { font-size: 1.1rem; }
            .footer-dark ul li a { font-size: 0.85rem; }
            .footer-dark .social a { font-size: 1.5rem; }
        }
        
        @media (max-width: 767px) {
            .navbar-brand img { max-height: 60px; }
            .navbar-nav { text-align: center; margin-top: 1rem; }
            .navbar-nav .nav-link { margin: 0.5rem 0; }

            .hero-section { padding: 3rem 0; }
            .hero-section h1 { font-size: 2rem; }
            .hero-section p { font-size: 0.9rem; max-width: 90%; }

            .core-services-section { padding: 3rem 0; }
            .service-list-card { padding: 1.5rem; margin-bottom: 1.5rem; }
            .service-list-card h3 { font-size: 1.3rem; margin-bottom: 1.5rem; padding-bottom: 0.75rem; }
            .service-item { margin-bottom: 1.8rem; }
            .service-item .icon-wrapper { width: 50px; height: 50px; font-size: 1.4rem; margin-right: 1rem; }
            .service-item .content h4 { font-size: 1.1rem; }
            .service-item .content p { font-size: 0.85rem; }

            .image-container { margin-top: 2rem; }
            
            .complementary-services-section { padding: 3rem 0; }
            .complementary-services-section h2 { font-size: 1.8rem; margin-bottom: 2.5rem; }
            .services-carousel { padding: 1rem 0; gap: 1rem; }
            .service-detail-card {
                flex: 0 0 280px;
                padding: 1.8rem;
            }
            .service-detail-card .icon-wrapper { width: 60px; height: 60px; font-size: 1.6rem; margin-bottom: 1.5rem; }
            .service-detail-card h4 { font-size: 1.2rem; margin-bottom: 1rem; }
            .service-detail-card .service-description { font-size: 0.9rem; margin-bottom: 1.5rem; }
            .features-list li, .advantages-list li { font-size: 0.85rem; padding-left: 1.8rem; }
            .scroll-indicator { font-size: 0.85rem; gap: 0.4rem; }
            
            .footer-dark { padding: 40px 0 20px 0; }
            .footer-dark .col-md-3 { margin-bottom: 20px; }
            .footer-dark h3 { margin-bottom: 15px; }
            .footer-dark .social { margin-top: 15px; }
            .footer-dark .social a { margin: 0 8px; }
            .copyright { padding-top: 15px; margin-top: 25px; font-size: 0.8rem; }
        }

        .header-section {
            background-image: url('{{ asset('images/aft.jpg') }}'); /* Utilisation de l'image de fond */
            background-size: cover;        /* L’image couvre toute la section */
            background-position: center;   /* Centrage de l’image */
            background-repeat: no-repeat;  /* Pas de répétition */
            min-height: 300px;             /* Hauteur minimum */
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative; /* Ajouté pour que l'overlay se positionne correctement */
            color: white; /* Pour que le texte soit visible sur l'image */
            padding: 4rem 0;
            margin-bottom: 2rem;
        }
        /* Overlay semi-transparent */
        .header-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5); /* Noir à 50% d'opacité */
            z-index: 1;
        }
        .header-section h1, .header-section {
            position: relative;
            z-index: 2; /* S'assurer que le texte est au-dessus de l'overlay */
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
            color: #faa200;
        }
        
    </style>
</head>
<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand me-auto" href="/">
                    <img src="{{ asset('images/LOGOAFT.png') }}" alt="Logo Aft Import Export" class="img-fluid">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarButtonsExample" aria-controls="navbarButtonsExample" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarButtonsExample">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item"><a class="nav-link mx-3" aria-current="page" href="/">Accueil</a></li>
                        <li class="nav-item"><a class="nav-link mx-3" href="/a-propos">À propos de nous</a></li>
                        <li class="nav-item"><a class="nav-link active mx-3" href="/nos-servives">Nos services</a></li>
                        <li class="nav-item"><a class="nav-link mx-3" href="/nos-agences">Nos Agences</a></li>
                        <li class="nav-item"><a class="nav-link mx-3" href="/contact">Contact</a></li>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="nav-link text-white">
                                <i class="fas fa-user me-2"></i> Se Connecter
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main>
    <section class="header-section">
        <div class="container">
            <h1>Nos Solutions D'import et Export</h1>
                {{-- <p>Découvrez l'ensemble de nos solutions de transport et de logistique intégrées, conçues pour optimiser vos flux et soutenir votre expansion globale.</p> --}}
        </div>
    </section>
       

        <section class="core-services-section">
            <div class="container">
                <h2>Nos Services Principaux</h2>
                <div class="row justify-content-center align-items-center">
                    <!-- Colonne pour les services -->
                    <div class="col-lg-7 mb-4">
                        <div class="service-list-card">
                            <h3>AFT Import Export propose :</h3>
                            
                            <!-- FRET MARITIME -->
                            <div class="service-item">
                                <div class="icon-wrapper">
                                    <i class="fas fa-ship"></i>
                                </div>
                                <div class="content">
                                    <h4>FRET MARITIME</h4>
                                    <p>Transport de conteneurs (20', 40', 40' HQ) adapté à vos besoins. Solutions de groupage (LCL) et de plein chargement (FCL) avec un suivi personnalisé et sécurisé de vos marchandises, du port de départ à l'arrivée.</p>
                                </div>
                            </div>

                            <!-- FRET AÉRIEN -->
                            <div class="service-item">
                                <div class="icon-wrapper">
                                    <i class="fas fa-plane"></i>
                                </div>
                                <div class="content">
                                    <h4>FRET AÉRIEN</h4>
                                    <p>Livraison express et sécurisée pour tous vos envois urgents. Nous prenons en charge l'ensemble des formalités douanières pour une expédition rapide et sans tracas, garantissant l'arrivée à temps de vos colis.</p>
                                </div>
                            </div>

                            <!-- SERVICES COMPLÉMENTAIRES -->
                            <div class="service-item">
                                <div class="icon-wrapper">
                                    <i class="fas fa-boxes"></i>
                                </div>
                                <div class="content">
                                    <h4>SERVICES COMPLÉMENTAIRES</h4>
                                    <p>Profitez de notre expertise en dédouanement et de nos conseils logistiques. Nous offrons des solutions de stockage, de distribution locale et d'assurance complète des marchandises pour une tranquillité d'esprit totale.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Colonne pour l'image -->
                    <div class="col-lg-5 mb-4">
                        <div class="image-container">
                            <img src="{{ asset('images/container.jpg') }}" alt="Services AFT Import Export" class="img-fluid rounded shadow-lg">
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <section class="complementary-services-section">
        <div class="container">
            <h2>Nos Services Complémentaires</h2>
            <div class="services-carousel">
                <!-- Service 1: Déménagement International -->
                <div class="service-detail-card">
                    <div class="icon-wrapper">
                        <i class="fas fa-truck-moving"></i>
                    </div>
                    <h4>Déménagement International</h4>
                    {{-- <p class="service-description">Des solutions complètes et sans stress pour votre déménagement entre la France et la Côte d'Ivoire.</p> --}}
                    
                    <div class="service-features">
                        <h5>Ce que nous offrons :</h5>
                        <ul class="features-list">
                            <li>Emballage professionnel de vos biens</li>
                            <li>Transport sécurisé et suivi rigoureux</li>
                            <li>Stockage temporaire si nécessaire</li>
                            <li>Assurance complète de vos marchandises</li>
                            <li>Gestion des formalités douanières</li>
                            <li>Livraison et assistance à l'installation à destination</li>
                        </ul>
                    </div>
                    
                    <div class="service-advantages">
                        <h5>Nos avantages pour vous :</h5>
                        <ul class="advantages-list">
                            <li>Une tranquillité d'esprit garantie</li>
                            <li>Un service clé en main, de bout en bout</li>
                            <li>Notre expertise reconnue à l'international</li>
                            <li>Un accompagnement personnalisé</li>
                        </ul>
                    </div>
                </div>

                <div class="service-detail-card">
                    <div class="icon-wrapper">
                        <i class="fas fa-truck-moving"></i>
                    </div>
                    <h4>Achat International</h4>
                    <p class="service-description">Service d'achat et d'importation depuis la Chine vers la Côte d'Ivoire</p>
                    
                    <div class="service-features">
                        <h5>Ce que nous offrons :</h5>
                        <ul class="features-list">
                            <li>Emballage professionnel</li>
                            <li>Transport sécurisé</li>
                            <li>Stockage temporaire</li>
                            <li>Assurance complète</li>
                            <li>Gestion douanière</li>
                            <li>Installation à destination</li>
                        </ul>
                    </div>
                    
                    <div class="service-advantages">
                        <h5>Nos avantages pour vous :</h5>
                        <ul class="advantages-list">
                             <li>Tranquillité d'esprit</li>
                            <li>Service clé en main</li>
                            <li>Expertise internationale</li>
                            <li>Accompagnement personnalisé</li>
                        </ul>
                    </div>
                </div>
                <!-- Service 3 -->
                <div class="service-detail-card">
                    <div class="icon-wrapper">
                        <i class="fas fa-truck-moving"></i>
                    </div>
                    <h4>Service Maritime</h4>
                    <p class="service-description">Service de groupage maritime professionnel entre la France et la Côte d'Ivoire</p>
                    
                    <div class="service-features">
                        <h5>Ce que nous offrons :</h5>
                        <ul class="features-list">
                            <li>Enlèvement à domicile en France</li>
                            <li>Conteneur dédié et sécurisé</li>
                            <li>Délai de 2-3 semaines</li>
                            <li>Livraison à Abidjan</li>
                            <li>Service porte-à-porte</li>
                            <li>Suivi en temps réel</li>
                        </ul>
                    </div>
                    
                    <div class="service-advantages">
                        <h5>Nos avantages pour vous :</h5>
                        <ul class="advantages-list">
                            <li>Économies sur les coûts de transport</li>
                            <li>Réduction des délais de livraison</li>
                            <li>Sécurité accrue des marchandises</li>
                            <li>Flexibilité dans les options de livraison</li>
                            <li>Gestion complète de A à Z</li>
                            <li>Service personnalisé</li>
                        </ul>
                    </div>
                </div>
                <!-- Service 4 -->
                <div class="service-detail-card">
                    <div class="icon-wrapper">
                        <i class="fas fa-truck-moving"></i>
                    </div>
                    <h4>Service Aérien</h4>
                    <p class="service-description">Transport aérien rapide et sécurisé de vos colis</p>
                    
                    <div class="service-features">
                        <h5>Ce que nous offrons :</h5>
                        <ul class="features-list">
                            <li>Rapidité d'acheminement</li>
                            <li>Fiabilité maximale</li>
                            <li>Flexibilité des options</li>
                            <li>Support dédié</li>
                        </ul>
                    </div>
                    
                    <div class="service-advantages">
                        <h5>Nos avantages pour vous :</h5>
                        <ul class="advantages-list">
                            <li>Livraison express</li>
                            <li>Suivi en temps réel</li>
                            <li>Service personnalisé</li>
                            <li>Couverture mondiale</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="scroll-indicator">
                <i class="fas fa-arrow-left"></i>
                Faites glisser pour voir plus de services
                <i class="fas fa-arrow-right"></i>
            </div>
        </div>
    </section>

    </main>

    <footer class="footer-dark">
        <div class="container">
            <div class="row">
                <!-- Colonne Contact -->
                <div class="col-md-3">
                    <h3>Nous contacter</h3>
                    
                    <div class="contact-info">
                        <p><i class="fas fa-map-marker-alt"></i> 7 AVENUE LOUIS BLERIOT, 93120 LA COURNEUVE</p>
                    </div>
                    
                    <h4><i class="fas fa-building"></i> Bureau en France</h4>
                    <ul>
                        <li><a href="tel:+33652983519"><i class="fas fa-phone"></i> +33 6 52 98 35 19</a></li>
                        <li><a href="mailto:entrepot.paris@aft-app.com"><i class="fas fa-envelope"></i> entrepot.paris@aft-app.com</a></li>
                    </ul>
                    
                    <h4><i class="fas fa-building"></i> Bureaux en Côte d'Ivoire</h4>
                    <p><i class="fas fa-map-pin"></i> Angré & Cocody, Abidjan</p>
                    <ul>
                        <li><a href="#"><i class="fas fa-phone"></i> +225 05 84 40 22 00</a></li>
                        <li><a href="entrepot.abidjan@aft-app.com"><i class="fas fa-envelope"></i> entrepot.abidjan@aft-app.com</a></li>
                    </ul>
                </div>
                
                <!-- Colonne Services -->
                <div class="col-md-3">
                    <h3><i class="fas fa-services"></i> Services</h3>
                    <ul>
                        <li><a href="#"><i class="fas fa-ship"></i> Fret maritime</a></li>
                        <li><a href="#"><i class="fas fa-plane"></i> Fret aérien</a></li>
                        <li><a href="#"><i class="fas fa-truck"></i> Logistique</a></li>
                        <li><a href="#"><i class="fas fa-box"></i> Groupage</a></li>
                        <li><a href="#"><i class="fas fa-passport"></i> Dédouanement</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h3><i class="fas fa-info-circle"></i> Informations</h3>
                    <ul>
                        <li><a href="#"><i class="fas fa-address-card"></i> À propos</a></li>
                        <li><a href="#"><i class="fas fa-map-marker-alt"></i> Nos agences</a></li>
                        <li><a href="#"><i class="fas fa-question-circle"></i> FAQ</a></li>
                        <li><a href="#"><i class="fas fa-newspaper"></i> Actualités</a></li>
                        <li><a href="#"><i class="fas fa-briefcase"></i> Carrières</a></li>
                    </ul>
                </div>
                
                <!-- Colonne Réseaux sociaux -->
                <div class="col-md-3">
                    <h3><i class="fas fa-share-alt"></i> Suivez-nous</h3>
                    <p>Restez connecté avec nous sur les réseaux sociaux pour suivre nos actualités.</p>
                    
                    <div class="social">
                        <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <h4><i class="fas fa-newsletter"></i> Newsletter</h4>
                        <p>Abonnez-vous à notre newsletter pour recevoir nos actualités.</p>
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" placeholder="Votre email">
                            <button class="btn btn-warning" type="button">S'abonner</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="copyright">
                © 2023 AFT IMPORT EXPORT. Tous droits réservés. | <a href="#" style="color: #aaa;">Mentions légales</a> | <a href="#" style="color: #aaa;">Politique de confidentialité</a>
            </div>
        </div>
    </footer>
    <!-- Scripts JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Animation pour les cartes de services
            $('.service-card').each(function(i) {
                $(this).delay(i * 200).animate({
                    opacity: 1
                }, 800);
            });
        });
    </script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>