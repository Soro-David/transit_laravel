<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Services - AFT Import Export</title>
    <!-- Google Fonts pour une typographie plus moderne -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <!-- Liens CSS existants -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        /* --- STYLES GÉNÉRAUX & RESET --- */
        body {
            font-family: 'Open Sans', sans-serif; /* Police de texte principale */
            margin: 0;
            padding: 0;
            background-color: #f8f9fa; /* Arrière-plan plus clair */
            color: #34495e; /* Couleur de texte plus douce */
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Montserrat', sans-serif; /* Police pour les titres */
            color: #2c3e50;
        }
        p {
            font-size: 1.25rem;
            line-height: 1.7;
            color: #ffffff;
        }
        a {
            text-decoration: none;
            color: inherit;
        }

        /* --- HEADER & NAVBAR --- */
        header {
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); /* Légère ombre pour le header */
        }
        .navbar-brand img {
            max-height: 70px;
            transition: transform 0.3s ease;
        }
        .navbar-brand img:hover {
            transform: scale(1.05);
        }
        .navbar-dark .navbar-nav .nav-link {
            color: white !important;
            font-weight: 600;
            padding: 0.75rem 1.2rem;
            position: relative;
            transition: color 0.3s ease;
        }
        .navbar-dark .navbar-nav .nav-link:hover {
            color: #ff8c00 !important; /* Orange plus vif au survol */
        }
        .navbar-dark .navbar-nav .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 5px;
            left: 50%;
            transform: translateX(-50%);
            width: 40%;
            height: 3px;
            /* background-color: #ff8c00; */
            border-radius: 2px;
        }
        .navbar-nav .nav-link.active {
            color: #fff !important;
            border-bottom: 3px solid #ff7b00; /* Soulignement orange vif pour l'actif */
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 4px 4px 0 0;
        }
        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.3);
        }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.5%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        
        /* --- SECTION HEADER PRINCIPALE (BANNER) --- */
        .header-section {
            background-image: url('{{ asset('images/equip3.png') }}'); /* Utilisation de l'image de fond */
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
        .header-section h1, .header-section p {
            position: relative;
            z-index: 2; /* S'assurer que le texte est au-dessus de l'overlay */
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
            color: #faa200;
        }
        /* --- SECTION SERVICES & AGENCES GÉNÉRALE --- */
        .services-agencies-section {
            padding: 5rem 0;
            background-color: #f0f2f5; /* Fond légèrement grisé pour la section */
        }

        /* --- CARTES DE SERVICES / AGENCES --- */
        .card-custom {
            background-color: #ffffff;
            border-radius: 12px; /* Coins plus arrondis */
            padding: 3rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08); /* Ombre plus prononcée */
            height: 100%;
            margin-bottom: 2.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Animation au survol */
            border: none;
        }
        .card-custom:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
        }
        .card-custom h3 {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1.8rem;
            padding-bottom: 1rem;
            border-bottom: 4px solid #ff8c00; /* Bordure orange plus épaisse */
            display: inline-block;
            font-size: 2rem;
            text-transform: uppercase;
        }
        
        /* --- ÉLÉMENTS INDIVIDUELS DE SERVICE --- */
        .service-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 2.5rem;
        }
        .service-item:last-child {
            margin-bottom: 0; /* Pas de marge après le dernier élément */
        }

        .service-item .icon-wrapper {
            background-color: #ff8c00;
            color: white;
            border-radius: 50%;
            width: 60px; /* Icône plus grande */
            height: 60px;
            min-width: 60px; /* Empêche le rétrécissement */
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.8rem; /* Taille de l'icône */
            margin-right: 1.8rem;
            box-shadow: 0 4px 10px rgba(255, 140, 0, 0.3); /* Ombre pour l'icône */
        }
        
        .service-item .content {
            flex-grow: 1;
        }
        .service-item h4 {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.6rem;
            font-size: 1.4rem;
        }
        .service-item p {
            font-size: 1.25rem;
            color: #555;
            line-height: 1.6;
        }

        /* --- IMAGE DANS LA COLONNE --- */
        .image-container {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .image-container img {
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            object-fit: cover; /* Assure que l'image couvre bien l'espace */
            max-height: 100%; /* S'adapte à la hauteur de la colonne */
        }

        /* --- SECTION NOS AGENCES --- */
        .agences-section {
            background-color: #ffffff; /* Fond blanc pour les agences */
            padding: 5rem 0;
        }
        .agences-section h2 {
            text-align: center;
            margin-bottom: 3rem;
            font-size: 2.8rem;
            color: #2c3e50;
            position: relative;
            padding-bottom: 1rem;
        }
        .agences-section h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: #ff8c00;
            border-radius: 2px;
        }
        
        /* Styles pour les cartes d'agence, similaires aux services mais avec un focus sur la localisation */
        .agency-card {
            background-color: #f8f9fa; /* Fond légèrement différent */
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            height: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center; /* Centrer le contenu des cartes d'agence */
            border: 1px solid #eee;
        }
        .agency-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        .agency-card .icon-wrapper {
            background-color: #ff8c00;
            color: white;
            border-radius: 50%;
            width: 70px; /* Plus grande icône pour l'agence */
            height: 70px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem auto; /* Centrer l'icône */
            box-shadow: 0 4px 10px rgba(255, 140, 0, 0.3);
        }
        .agency-card h4 {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1rem;
            font-size: 1.6rem;
        }
        .agency-card p {
            font-size: 1rem;
            color: #666;
            margin-bottom: 0.5rem;
        }
        .agency-card .contact-info a {
            color: #ff8c00;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .agency-card .contact-info a:hover {
            color: #e67e00;
        }

        /* --- FOOTER --- */
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
            border-top: 1px solid #34495e; /* Bordure plus foncée */
            padding-top: 25px;
            margin-top: 40px;
            font-size: 0.9rem;
            color: #95a5a6;
        }

        /* --- RESPONSIVITÉ --- */
        @media (max-width: 991px) {
            .header-section h1 {
                font-size: 2.8rem;
            }
            .header-section p {
                font-size: 1.1rem;
            }
            .card-custom {
                padding: 2rem;
            }
            .card-custom h3 {
                font-size: 1.8rem;
                margin-bottom: 1.5rem;
            }
            .service-item .icon-wrapper {
                width: 50px;
                height: 50px;
                min-width: 50px;
                font-size: 1.4rem;
                margin-right: 1.2rem;
            }
            .service-item h4 {
                font-size: 1.2rem;
            }
            .image-container {
                margin-top: 2rem;
            }
        }

        @media (max-width: 767px) {
            .navbar-brand img {
                max-height: 60px;
            }
            .header-section {
                padding: 4rem 0;
            }
            .header-section h1 {
                font-size: 2.2rem;
            }
            .header-section p {
                font-size: 1rem;
            }
            .services-agencies-section, .agences-section {
                padding: 3rem 0;
            }
            .card-custom {
                padding: 1.5rem;
            }
            .card-custom h3 {
                font-size: 1.6rem;
                padding-bottom: 0.8rem;
            }
            .service-item {
                margin-bottom: 1.8rem;
            }
            .footer-dark h3 {
                margin-top: 30px;
            }
            .footer-dark .social {
                text-align: center;
                margin-top: 30px;
            }
        }
    </style>

</head>
<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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
                        <li class="nav-item"><a class="nav-link  mx-3" href="/nos-servives">Nos services</a></li>
                        <li class="nav-item"><a class="nav-link active mx-3" href="/nos-agences">Nos Agences</a></li>
                        <li class="nav-item"><a class="nav-link mx-3" href="/contact">Contact</a></li>
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

    <main><br><br>
       <!-- Section d'en-tête de la page des services -->
    <section class="header-section">
        <div class="container">
            <h1>Nos Solutions de Transport et Logistique</h1>
            <p>Que ce soit par mer ou par air, nous offrons des solutions complètes pour l'acheminement sécurisé et efficace de vos marchandises partout dans le monde. Découvrez nos services sur mesure.</p>
        </div>
    </section>
        <!-- Section des Services -->
<section class="services-agencies-section">
    <div class="container">
        <div class="row justify-content-center">
            <!-- Colonne pour les services détaillés -->
            <div class="col-lg-7 mb-4">
                <div class="card-custom">
                    <h3>Nos Services</h3>
                    
                    <!-- FRET MARITIME -->
                    <div class="service-item">
                        <div class="icon-wrapper">
                            <i class="fas fa-ship"></i>
                        </div>
                        <div class="content">
                            <h4>Fret Maritime International</h4>
                            <p>Profitez de nos solutions de transport par voie maritime pour vos marchandises. Que ce soit pour des conteneurs complets (FCL) ou du groupage (LCL), nous garantissons une gestion optimale et un suivi rigoureux de vos expéditions. Nos services incluent :</p>
                            <ul>
                                <li>Transport de conteneurs standards (20’, 40’, 40’ HQ)</li>
                                <li>Services de Groupage (LCL) et de Plein Chargement (FCL)</li>
                                <li>Suivi personnalisé et sécurisé des marchandises en temps réel</li>
                                <li>Optimisation des routes et des coûts</li>
                            </ul>
                        </div>
                    </div>

                    <!-- FRET AÉRIEN -->
                    <div class="service-item">
                        <div class="icon-wrapper">
                            <i class="fas fa-plane"></i>
                        </div>
                        <div class="content">
                            <h4>Fret Aérien Express</h4>
                            <p>Pour vos envois urgents et à haute valeur ajoutée, notre service de fret aérien assure une livraison rapide et sécurisée à travers le monde. Nous prenons en charge toutes les formalités pour une efficacité maximale :</p>
                            <ul>
                                <li>Livraison rapide et sécurisée pour les envois urgents</li>
                                <li>Gestion complète des formalités douanières et documents nécessaires</li>
                                <li>Suivi en temps réel de vos expéditions aériennes</li>
                                <li>Solutions adaptées aux marchandises périssables ou sensibles</li>
                            </ul>
                        </div>
                    </div>

                    <!-- SERVICES COMPLÉMENTAIRES -->
                    <div class="service-item">
                        <div class="icon-wrapper">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div class="content">
                            <h4>Services Logistiques Intégrés</h4>
                            <p>Au-delà du transport, nous proposons une gamme complète de services complémentaires pour simplifier votre chaîne logistique et assurer une fluidité totale de vos opérations :</p>
                            <ul>
                                <li>Dédouanement et conseils experts en logistique internationale</li>
                                <li>Solutions de stockage sécurisé et distribution locale</li>
                                <li>Assurance complète des marchandises pour une tranquillité d'esprit</li>
                                <li>Optimisation de la chaîne d'approvisionnement et gestion des stocks</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne pour les images illustrant les services -->
            <div class="col-lg-5 mb-4">
                <div class="image-grid">
                    <div class="row g-2"> <!-- g-2 ajoute un petit espacement entre les colonnes et les lignes -->
                        <div class="col-12 col-md-12 col-lg-12">
                            <img src="{{ asset('images/get.jpg') }}" alt="Fret Maritime" class="img-fluid rounded shadow-sm">
                        </div>
                        <div class="col-12 col-md-12 col-lg-12">
                            <img src="{{ asset('images/boeing.jpg') }}" alt="Fret Aérien" class="img-fluid rounded shadow-sm">
                        </div>
                        <div class="col-12 col-md-4 col-lg-12">
                            <img src="{{ asset('images/freight.jpg') }}" alt="Services Logistiques" class="img-fluid rounded shadow-sm">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

        <!-- Nouvelle section "Nos Agences" -->
<section class="agences-section">
    <div class="container">
        <h2>Nos Agences Stratégiquement Implémentées</h2>
        <div class="agencies-horizontal-scroll"> <!-- Nouveau conteneur pour le défilement horizontal -->
            <!-- Agence 1 -->
            <div class="agency-card">
                <div class="icon-wrapper">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h4>DS Translog Carrefour Angré</h4>
                <p><strong>Adresse :</strong>Abidjan Carrefour Angré</p>
                <p class="contact-info"><a href="mailto:entrepot-abidjan@aft-app.com">entrepot-abidjan@aft-app.com</a></p>
                <p>Votre porte d'entrée et de sortie Pour le colis maritime..</p>
            </div>

            <!-- Agence 2 -->
            <div class="agency-card">
                <div class="icon-wrapper">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h4>DS Translog Angré 8ème Tranche</h4>
                <p><strong>Adresse :</strong>Abidjan, Angré 8ème Tranche </p>
                <p class="contact-info"><a href="mailto:entrepot-abidjan@aft-app.com">entrepot-abidjan@aft-app.com</a></p>
                <p>Votre porte d'entrée et de sortie Pour le colis Aérien..</p>
            </div>

            <!-- Agence 3 -->
            <div class="agency-card">
                <div class="icon-wrapper">
                    <i class="fas fa-globe-africa"></i>
                </div>
                <h4>Agence de Chine</h4>
                <p><strong>Adresse :</strong> Chine</p>
                <p class="contact-info"><a href="mailto:douane@aftèapp.com">douane@aftèapp.com</a></p>
                <p>Votre porte d'entrée et de sortie Pour le colis maritime et Aérien.</p>
            </div>

            <!-- Agence 4 -->
            <div class="agency-card">
                <div class="icon-wrapper">
                    <i class="fas fa-building"></i>
                </div>
                <h4>Agence Louis Blériot</h4>
                <p><strong>Adresse :</strong> 45, Avenue des Affaires, Paris, France</p>
                <p class="contact-info"><a href="mailto:entrepot-paris@aft-app.com">entrepot-paris@aft-app.com</a></p>
                <p>Notre hub européen pour des connexions internationales.</p>
            </div>
        </div>
    </div>
</section>

<style>
        /* CSS pour le défilement horizontal */
        .agencies-horizontal-scroll {
            display: flex; /* Utilise Flexbox */
            overflow-x: auto; /* Permet le défilement horizontal si le contenu dépasse */
            -webkit-overflow-scrolling: touch; /* Améliore le défilement sur iOS */
            padding-bottom: 15px; /* Pour éviter que la barre de défilement ne masque le contenu */
            gap: 20px; /* Espace entre les cartes */
        }

        .agencies-horizontal-scroll .agency-card {
            flex: 0 0 auto; /* Empêche les cartes de rétrécir */
            width: 300px; /* Définir une largeur fixe pour chaque carte */
            /* Assurez-vous que les styles de votre .agency-card sont adaptés */
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* Styles pour les autres éléments de la carte (icon-wrapper, etc.) */
        .agency-card .icon-wrapper {
            font-size: 3rem;
            color: #007bff; /* Ou votre couleur d'accent */
            margin-bottom: 15px;
        }
</style>
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
            // Pas de slider sur cette page pour l'instant, mais la dépendance est là si besoin
        });
    </script>
    <!-- Assurez-vous que app.js contient les scripts globaux ou des interactions spécifiques si nécessaire -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>