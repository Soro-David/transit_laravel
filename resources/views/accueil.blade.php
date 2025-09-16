<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aft Import Export</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.css"
          integrity="sha512-wR4o5EQOVWL53c/bJtCQiRzE6C8zvcbMYiE20uRyVwPjxU5KmQ2SeZZ4yhJfQ8zhEXRzVFMkVVnLWZI6+m9SQg=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.css"
          integrity="sha512-6lLUqr3C5NW0ot4CEqtOUjNmdB0clNH5stMboo/uQDD0CLwLEoqk95Ktr9IwOAAvontuUFAWnzK8vJ0FJoaoQ=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* Vos styles existants */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }

        header {
            color: white;
            padding: 1rem 0;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .navbar-nav .nav-link {
            color: white !important;
            padding: 0.5rem 1rem;
        }

        .navbar-nav .nav-link:hover {
            color: #ddd !important;
        }

        .slider {
            margin-bottom: 2rem;
        }

        .slider img {
            width: 100%;
            height: auto;
            max-height: 450px;
            object-fit: cover;
        }

        main {
            padding: 2rem 0;
        }

        .container {
        max-width: 100% !important;
        padding-left: 100px;
        padding-right: 100px;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        p {
          
            line-height: 1.6;
            font-size: 1.25rem;
        }

        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 1rem 0;
        }

        .navbar-collapse {
            justify-content: center;
        }
        
        .navbar-nav .nav-link {
            color: white !important;
            font-weight: bold;
            padding: 0.5rem 1rem;
        }

        .navbar-nav .nav-link:hover {
            color: orange !important;
        }

        .navbar-nav .nav-link.active {
            color: #fff !important;
            border-bottom: 3px solid #ff7b00; /* Soulignement orange vif pour l'actif */
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 4px 4px 0 0;
        }

        @media (max-width: 767px) {
            .navbar-brand img {
                max-height: 50px;
            }
        }
        
        .carousel-image {
            filter: drop-shadow(10px 10px 10px rgba(0, 0, 0, 0.5));
        }

        .carousel-caption {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            text-align: center;
        }

        .carousel {
            height: 400px;
        }

        .carousel-item {
            height: 550px;
        }

        .carousel-item img {
            object-fit: cover;
            height: 700px;
        }

        .demande-devis:hover {
            background-color: orange;
            color: white;
        }

        .contactez-nous:hover {
            background-color: white;
            color: red;
        }

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
        
        .img-fluid {
            width: 100%;
            height: auto;
        }
        
        .rounded {
            border-radius: 10px;
        }
        
        .text-orange {
            color: orange !important;
        }

        .titre {
            color: green;
            font-weight: bold;
            font-size: 32px;
            text-shadow:
                -1px -1px 0 white,
                1px -1px 0 white,
                -1px  1px 0 white,
                1px  1px 0 white;
        }

        .slogan {
            color: orange;
            font-weight: bold;
            font-size: 24px;
            text-shadow:
                -1px -1px 0 white,
                1px -1px 0 white,
                -1px  1px 0 white,
                1px  1px 0 white;
        }
        
        /* NOUVEAUX STYLES POUR LA SECTION RÉSEAU LOGISTIQUE */
        
        .logistic-network {
            padding: 4rem 0;
            background: linear-gradient(to bottom, #ffffff, #f8f9fa);
        }
        
        .logistic-network h2 {
            text-align: center;
            margin-bottom: 3rem;
            color: #2c3e50;
            font-weight: 700;
            position: relative;
            padding-bottom: 15px;
        }
        
        .logistic-network h2:after {
            content: '';
            display: block;
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, #ff7b00, orange);
            margin: 15px auto 0;
            border-radius: 2px;
        }
        
        .logistic-intro {
            text-align: center;
            margin-bottom: 3rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            font-size: 1.1rem;
            color: #555;
        }
        
        .network-item {
            margin-bottom: 2.5rem;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .network-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .network-img {
            position: relative;
            overflow: hidden;
        }
        
        .network-img img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .network-item:hover .network-img img {
            transform: scale(1.05);
        }
        
        .network-content {
            padding: 1.5rem;
        }
        
        .network-content h3 {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 1.4rem;
        }
        
        .network-content p {
            color: #555;
            margin-bottom: 1rem;
            text-align: left;
        }
        
        .network-features {
            list-style-type: none;
            padding-left: 0;
            margin-bottom: 0;
        }
        
        .network-features li {
            position: relative;
            padding-left: 1.8rem;
            margin-bottom: 0.7rem;
            color: #444;
        }
        
        .network-features li:before {
            content: '\f00c';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            left: 0;
            color: #28a745;
        }
        
        .global-coverage {
            background-color: #f8f9fa;
            padding: 3rem 0;
            margin-top: 3rem;
            border-radius: 12px;
        }
        
        .global-coverage h3 {
            text-align: center;
            margin-bottom: 2rem;
            color: #2c3e50;
        }
        
        .coverage-stats {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            text-align: center;
        }
        
        .stat-item {
            flex: 0 0 30%;
            margin-bottom: 1.5rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: orange;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 1rem;
            color: #555;
        }
        
        @media (max-width: 768px) {
            .stat-item {
                flex: 0 0 100%;
            }
            
            .network-content h3 {
                font-size: 1.3rem;
            }
            
            .stat-number {
                font-size: 2rem;
            }
        }
        
        /* STYLES AMÉLIORÉS POUR LE MODAL */
        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #ff7b00, #ff5500);
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .modal-title {
            font-weight: 700;
            font-size: 1.4rem;
        }
        
        .modal-header .btn-close {
            filter: invert(1);
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }
        
        .modal-header .btn-close:hover {
            opacity: 1;
        }
        
        .modal-body {
            padding: 2rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #ff7b00;
            box-shadow: 0 0 0 0.25rem rgba(255, 123, 0, 0.25);
        }
        
        .form-text {
            color: #6c757d;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #ff7b00, #ff5500);
            border: none;
            border-radius: 8px;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #ff5500, #ff3c00);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 123, 0, 0.3);
        }
        
        .tracking-icon {
            display: inline-block;
            margin-right: 10px;
            font-size: 1.2em;
        }
        
        .modal-footer {
            border-top: 1px solid #eee;
            padding: 1rem 2rem;
            justify-content: center;
        }
        
        .modal-footer-text {
            font-size: 0.9rem;
            color: #6c757d;
            text-align: center;
        }
        
        .modal-footer-text a {
            color: #ff7b00;
            text-decoration: none;
            font-weight: 600;
        }
        
        .modal-footer-text a:hover {
            text-decoration: underline;
        }
        
        /* NOUVEAUX STYLES POUR LES RÉSULTATS DE RECHERCHE */
        #trackingResult {
            margin-top: 1.5rem;
            padding: 1rem;
            border-radius: 8px;
            background-color: #f8f9fa;
        }
        
        .tracking-details {
            background-color: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .tracking-details h5 {
            color: #ff7b00;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #ff7b00;
        }
        
        .tracking-details p {
            margin-bottom: 0.5rem;
        }
        
        .tracking-details .status-badge {
            display: inline-block;
            padding: 0.35rem 0.65rem;
            border-radius: 50rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }
        
        .status-active {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #664d03;
        }
        
        .status-delivered {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        
        .status-problem {
            background-color: #f8d7da;
            color: #842029;
        }
        
        /* Style pour la section des résultats sous le modal */
        .tracking-results-section {
            margin-top: 2rem;
            padding: 2rem;
            background-color: #f8f9fa;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .tracking-results-section h3 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            text-align: center;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .tracking-results-section h3:after {
            content: '';
            display: block;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #ff7b00, orange);
            margin: 10px auto 0;
            border-radius: 2px;
        }
            .text-orange {
        color: #ff6600; /* adapte à ta charte */
        }
        .video-wrapper {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .video-wrapper:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
    </style>
     <meta name="csrf-token" content="{{ csrf_token() }}"> 
</head>
<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand me-auto" href="/">
                    <img src="{{ asset('images/LOGOAFT.png') }}" alt="Logo Aft Import Export" class="img-fluid" style="max-height: 70px;">
                </a>
    
                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarButtonsExample"
                    aria-controls="navbarButtonsExample"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>
    
                <div class="collapse navbar-collapse" id="navbarButtonsExample">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item">
                            <a class="nav-link active mx-3" aria-current="page" href="/">Accueil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-3" href="/a-propos">À propos de nous</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-3" href="/nos-servives">Nos services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-3" href="/nos-agences">Nos Agences</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-3" href="/contact">Contact</a>
                        </li>
                    </ul>
    
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="nav-link text-white">
                                <i class="fas fa-user"></i> Se Connecter
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <section class="slider">
        <div id="carouselExampleDark" class="carousel carousel-dark slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="0" class="active"
                        aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="1"
                        aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="2"
                        aria-label="Slide 3"></button>
                <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="3"
                        aria-label="Slide 4"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active" data-bs-interval="5000">
                    <img src="{{ asset('images/slide4.jpg') }}" class="d-block w-100 carousel-image" alt="Import & Export Solutions">
                    <div class="carousel-caption d-md-block">
                        <h4 class="titre">AFRIQUE FRET TRANSIT IMPORT EXPORT</h4>
                        <p class="slogan">Aft import export, Votre intermédiaire crédible</p><br>
                        <div>
                            <a href="#" class="btn btn-danger demande-devis" data-bs-toggle="modal" data-bs-target="#trackingModal">Suivre Mon colis</a>
                            <a href="#" class="btn btn-outline-light contactez-nous">Contactez nous</a>
                        </div>
                    </div>
                </div>
                <div class="carousel-item" data-bs-interval="5000">
                    <img src="{{ asset('images/slide2.jpg') }}" class="d-block w-100 carousel-image" alt="Reliable Logistics">
                    <div class="carousel-caption d-md-block">
                       <h4 class="titre">AFRIQUE FRET TRANSIT IMPORT EXPORT</h4>
                        <p class="slogan">Aft import export, Votre intermédiaire crédible</p><br>
                        <div>
                            <a href="#" class="btn btn-danger demande-devis" data-bs-toggle="modal" data-bs-target="#trackingModal">Suivre Mon colis</a>
                            <a href="#" class="btn btn-outline-light contactez-nous">Contactez nous</a>
                        </div>
                    </div>
                </div>
                <div class="carousel-item" data-bs-interval="5000">
                    <img src="{{ asset('images/slide3.jpg') }}" class="d-block w-100 carousel-image" alt="Customs Clearance">
                    <div class="carousel-caption d-md-block">
                        <h4 class="titre">AFRIQUE FRET TRANSIT IMPORT EXPORT</h4>
                        <p class="slogan">Aft import export, Votre intermédiaire crédible</p><br>
                        <div>
                            <a href="#" class="btn btn-danger demande-devis" data-bs-toggle="modal" data-bs-target="#trackingModal">Suivre Mon colis</a>
                            <a href="#" class="btn btn-outline-light contactez-nous">Contactez nous</a>
                        </div>
                    </div>
                </div>
                <div class="carousel-item" data-bs-interval="5000">
                    <img src="{{ asset('images/slide01.jpg') }}" class="d-block w-100 carousel-image" alt="Global Network">
                    <div class="carousel-caption d-md-block">
                         <h5 class="titre">AFRIQUE FRET TRANSIT IMPORT EXPORT</h5><br>
                        <p class="slogan">Aft import export, Votre intermédiaire crédible</p><br>
                        <div>
                            <a href="#" class="btn btn-danger demande-devis" data-bs-toggle="modal" data-bs-target="#trackingModal">Suivre Mon colis</a>
                            <a href="#" class="btn btn-outline-light contactez-nous">Contactez nous</a>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark"
                    data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark"
                    data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section><br>

    <section class="container my-5 py-5">
        <div class="row align-items-center">
            <!-- Texte -->
            <div class="col-md-6 mb-4 mb-md-0">
                <h2 class="text-orange fw-bold mb-3">Pourquoi nous choisir ?</h2>
                <p class="text-muted mb-4">
                    Nous offrons des solutions <span class="fw-semibold">innovantes</span> et adaptées à vos besoins spécifiques. 
                    Notre équipe d'experts est dédiée à <span class="fw-semibold">votre succès</span>.
                </p>
                <ul class="list-unstyled">
                    <li class="d-flex align-items-center mb-2">
                        <i class="fas fa-check-circle text-orange me-2"></i> Expertise reconnue dans le secteur
                    </li>
                    <li class="d-flex align-items-center mb-2">
                        <i class="fas fa-check-circle text-orange me-2"></i> Solutions personnalisées et flexibles
                    </li>
                    <li class="d-flex align-items-center mb-2">
                        <i class="fas fa-check-circle text-orange me-2"></i> Support client réactif et dédié
                    </li>
                    <li class="d-flex align-items-center">
                        <i class="fas fa-check-circle text-orange me-2"></i> Résultats mesurables et concrets
                    </li>
                </ul>
            </div>

            <!-- Vidéo -->
            <div class="col-md-6">
                <div class="video-wrapper border rounded-4 shadow-lg overflow-hidden">
                    <video class="w-100 rounded-4" autoplay muted loop playsinline controls>
                        <source src="{{ asset('video/video1.mp4') }}" type="video/mp4">
                        Votre navigateur ne supporte pas la lecture vidéo.
                    </video>
                </div>
            </div>
        </div>
    </section>




    <!-- SECTION RÉSEAU LOGISTIQUE AMÉLIORÉE -->
    <section class="logistic-network">
        <div class="container">
            <h2>Découvrez notre réseau logistique international</h2>
            
            <p class="logistic-intro">
                Notre organisation repose sur un réseau logistique mondial parfaitement maîtrisé, 
                garantissant une coordination optimale à chaque étape de votre chaîne d'approvisionnement. 
                Découvrez comment notre structure nous permet de vous offrir un service fiable et efficace.
            </p>
            
            <div class="row">
                <!-- Point 1 -->
                <div class="col-md-6">
                    <div class="network-item">
                        <div class="network-img">
                            <img src="{{ asset('images/ac6.jpeg') }}" alt="Centres logistiques stratégiques">
                        </div>
                        <div class="network-content">
                            <h3>Centres logistiques stratégiques</h3>
                            <p>
                                Nos plateformes logistiques situées aux points névralgiques du commerce international 
                                permettent une gestion optimale des flux de marchandises et une réduction significative 
                                des délais de transit.
                            </p>
                            <ul class="network-features">
                                <li>Hubs en Europe, Asie et Afrique</li>
                                <li>Entrepôts sécurisés et modernes</li>
                                <li>Equipements de manutention de pointe</li>
                                <li>Zones de stockage tempéré et frigorifique</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Point 2 -->
                <div class="col-md-6">
                    <div class="network-item">
                        <div class="network-img">
                            <img src="{{ asset('images/stra2.jpg') }}" alt="Transport multimodal intégré">
                        </div>
                        <div class="network-content">
                            <h3>Transport multimodal intégré</h3>
                            <p>
                                Nous maîtrisons l'ensemble des modes de transport pour vous offrir des solutions 
                                sur mesure, en optimisant le rapport coût/délai pour chaque envoi.
                            </p>
                            <ul class="network-features">
                                <li>Solutions maritimes (FCL et LCL)</li>
                                <li>Transport aérien express</li>
                                <li>Solutions terrestres et ferroviaires</li>
                                <li>Coordination fluide entre les modes</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Point 3 -->
                <div class="col-md-6">
                    <div class="network-item">
                        <div class="network-img">
                            <img src="{{ asset('images/ac3.jpeg') }}" alt="Gestion douanière expertise">
                        </div>
                        <div class="network-content">
                            <h3>Expertise douanière internationale</h3>
                            <p>
                                Notre équipe de spécialistes en procedures douanières assure la conformité 
                                de vos envois et anticipe les formalités pour un dédouanement fluide et sans surprise.
                            </p>
                            <ul class="network-features">
                                <li>Conseil en réglementation douanière</li>
                                <li>Gestion des certificats et licences</li>
                                <li>Optimisation des coûts et taxes</li>
                                <li>Veille réglementaire permanente</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Point 4 -->
                <div class="col-md-6">
                    <div class="network-item">
                        <div class="network-img">
                            <img src="{{ asset('images/stra4.jpg') }}" alt="Technologie et traçabilité">
                        </div>
                        <div class="network-content">
                            <h3>Technologie et traçabilité avancée</h3>
                            <p>
                                Notre plateforme digitale vous offre une visibilité totale sur vos envois 
                                en temps réel, avec des outils performants de suivi et de reporting.
                            </p>
                            <ul class="network-features">
                                <li>Suivi en temps réel 24/7</li>
                                <li>Alertes proactives et notifications</li>
                                <li>Reporting personnalisé et analytics</li>
                                <li>Interface intuitive et mobile</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Section de couverture mondiale -->
            <div class="global-coverage">
                <h3>Notre couverture mondiale</h3>
                <div class="coverage-stats">
                    <div class="stat-item">
                        <div class="stat-number">10+</div>
                        <div class="stat-label">Pays desservis</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">15+</div>
                        <div class="stat-label">Partenaires internationaux</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Satisfaction client</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION POUR AFFICHER LES RÉSULTATS DE RECHERCHE -->
   

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
                        <li><a href="mailto:contact@aft-import-export.net"><i class="fas fa-envelope"></i> contact@aft-import-export.net</a></li>
                    </ul>
                    
                    <h4><i class="fas fa-building"></i> Bureaux en Côte d'Ivoire</h4>
                    <p><i class="fas fa-map-pin"></i> Angré & Cocody, Abidjan</p>
                    <ul>
                        <li><a href="#"><i class="fas fa-phone"></i> +225 XX XX XX XX</a></li>
                        <li><a href="mailto:contact.ci@aft-import-export.net"><i class="fas fa-envelope"></i> contact.ci@aft-import-export.net</a></li>
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

<!-- Votre modal existant -->
<div class="modal fade" id="trackingModal" tabindex="-1" aria-labelledby="trackingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="trackingModalLabel"><i class="fas fa-box tracking-icon"></i> Suivre votre colis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="trackingForm" action="{{ route('colis.tracking') }}" method="post">
                    @csrf
                    <div class="mb-4">
                        <label for="reference" class="form-label">Référence du colis</label>
                        <input type="text" class="form-control" id="reference" name="reference" placeholder="Ex: AFT-123456789" required>
                        <div class="form-text">Entrez le numéro de référence qui vous a été communiqué</div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search me-2"></i> Suivre mon colis</button>
                    </div>
                </form>
                <div id="trackingResult" class="mt-4"></div>
            </div>
            <div class="modal-footer">
                {{-- <p class="modal-footer-text">
                    Vous n'avez pas votre référence ? <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#contactModal">Contactez-nous</a>
                </p> --}}
            </div>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"
            integrity="sha512-XtmMtDEcNz2j7ekrtHvOVR4iwwMD6yrwpJy8UZjRseKbslzvcNFaz6AY9dlK8xjKYGgOB/mpiVNixFD4iHdq8Q=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const trackingForm = document.getElementById("trackingForm");
            const trackingResult = document.getElementById("trackingResult");

            if (trackingForm) {
                trackingForm.addEventListener("submit", function (e) {
                    e.preventDefault(); // Empêche l'envoi standard du formulaire

                    const formData = new FormData(trackingForm);
                    const reference = document.getElementById("reference").value;

                    console.log("Soumission du formulaire pour référence :", reference);

                    fetch(trackingForm.action, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                            "Accept": "application/json"
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw new Error(errorData.message || 'Erreur réseau ou du serveur');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log("Données reçues du serveur :", data);

                        // Afficher les résultats dans le modal
                        if (data.success) {
                            // Déterminer la classe CSS en fonction du statut
                            let statusClass = 'status-pending';
                            if (data.colis.status) {
                                const lowerCaseStatus = data.colis.status.toLowerCase();
                                if (lowerCaseStatus.includes('livré') || lowerCaseStatus.includes('delivered')) {
                                    statusClass = 'status-delivered';
                                } else if (lowerCaseStatus.includes('en cours') || lowerCaseStatus.includes('transit')) {
                                    statusClass = 'status-active';
                                } else if (lowerCaseStatus.includes('problème') || lowerCaseStatus.includes('issue')) {
                                    statusClass = 'status-problem';
                                }
                            }
                            
                            const referenceColis = data.colis.reference || 'N/A';
                            const statusColis = data.colis.status || 'N/A';
                            const etatColis = data.colis.etat || 'N/A';
                            const transitColis = data.colis.transit || 'N/A';
                            const lastUpdateDate = data.colis.updated_at ? new Date(data.colis.updated_at).toLocaleDateString('fr-FR') : new Date().toLocaleDateString('fr-FR');

                            trackingResult.innerHTML = `
                                <div class="tracking-details">
                                    <h5>Détails du colis <span class="status-badge ${statusClass}"></span></h5>
                                    <p><strong>Référence :</strong> ${referenceColis}</p>
                                    <p><strong>État du colis :</strong> ${etatColis}</p>
                                    <p><strong>Mode de transit :</strong> ${transitColis}</p>
                                    <p><strong>Dernière mise à jour :</strong> ${lastUpdateDate}</p>
                                </div>
                                
                                <div class="mt-4 text-center">
                                    <button class="btn btn-outline-primary" onclick="resetTrackingForm()">
                                        <i class="fas fa-search me-2"></i>Nouvelle recherche
                                    </button>
                                </div>
                            `;
                        } else {
                            trackingResult.innerHTML = `
                                <div class="alert alert-danger text-center">
                                    <h5>Aucun résultat trouvé</h5>
                                    <p>${data.message || "Vérifiez votre référence ou contactez-nous pour obtenir de l'aide."}</p>
                                </div>
                                
                                <div class="mt-4 text-center">
                                    <button class="btn btn-outline-primary" onclick="resetTrackingForm()">
                                        <i class="fas fa-search me-2"></i>Réessayer
                                    </button>
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error("Erreur lors de la requête Fetch :", error);
                        
                        // Afficher l'erreur dans le modal
                        trackingResult.innerHTML = `
                            <div class="alert alert-danger text-center">
                                <h5>Erreur de connexion</h5>
                                <p> ${error.message}</p>
                                <p>Veuillez réessayer ultérieurement.</p>
                            </div>
                            
                            <div class="mt-4 text-center">
                                <button class="btn btn-outline-primary" onclick="resetTrackingForm()">
                                    <i class="fas fa-search me-2"></i>Réessayer
                                </button>
                            </div>
                        `;
                    });
                });
            } else {
                console.error("Formulaire 'trackingForm' non trouvé.");
            }
            
            // Réinitialiser le formulaire et les résultats lorsque le modal est fermé
            const trackingModalElement = document.getElementById('trackingModal');
            if (trackingModalElement) {
                trackingModalElement.addEventListener('hidden.bs.modal', function () {
                    trackingForm.reset();
                    trackingResult.innerHTML = '';
                });
            }
        });
        
        // Fonction pour réinitialiser le formulaire de suivi
        function resetTrackingForm() {
            const trackingForm = document.getElementById("trackingForm");
            const trackingResult = document.getElementById("trackingResult");
            
            if (trackingForm && trackingResult) {
                trackingForm.reset();
                trackingResult.innerHTML = '';
                document.getElementById("reference").focus();
            }
        }
    </script>

    <script>
        $(document).ready(function() {
            // Animation pour les éléments du réseau logistique
            $('.network-item').each(function(i) {
                $(this).delay(i * 200).animate({
                    opacity: 1
                }, 800);
            });
            
            // Focus automatique sur le champ de référence lorsque le modal s'ouvre
            $('#trackingModal').on('shown.bs.modal', function () {
                $('#reference').focus();
            });
        });
    </script>
</body>
</html>