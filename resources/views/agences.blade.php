<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Services - AFRIQUE FRET TRANSIT IMPORT EXPORT</title>
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
                h5, h6 {
                    font-family: 'Montserrat', sans-serif; /* Police pour les titres */
                    color: #2c3e50;
                }
                footer {
                    background-color: #343a40;
                    color: white;
                    text-align: center;
                    padding: 1rem 0;
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

                .container {
                max-width: 100% !important;
                padding-left: 100px;
                padding-right: 100px;
                }
                /* --- HEADER & NAVBAR --- */
                header {
                    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
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
                    text-align: center;
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
                    color: #ffc107 !important;
                    border-bottom: 3px solid #ffc107;
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
                    border-radius: 12px; 
                    padding: 3rem;
                    /* box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08); */
                    height: 100%;
                    margin-bottom: 2.5rem;
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                    border: none;
                }
                .card-custom:hover {
                    transform: translateY(-8px);
                    /* box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12); */
                }
                .card-custom h3 {
                    font-weight: 700;
                    color: #2c3e50;
                    margin-bottom: 1.8rem;
                    padding-bottom: 1rem;
                    border-bottom: 4px solid #ff8c00;
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
                    .container {
                        max-width: 100% !important;
                        padding-left: 100px;
                        padding-right: 100px;
                    }
                    .services-agencies-section{
                        max-width: 100% !important;
                        padding-left: 100px;
                        padding-right: 100px;
                    }
                    .image-container {
                        margin-top: 2rem;
                    }
                }

                /* @media (max-width: 767px) {
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
                } */

                @media (max-width: 767px) {
                    .navbar-brand img {
                        max-height: 50px;
                    }

                    /* Pour les écrans mobiles, le container prend toute la largeur */
                    .container {
                        padding-left: 15px; /* Réduit le padding sur les côtés */
                        padding-right: 15px; /* Réduit le padding sur les côtés */
                    }
                }

                h2,h3 {
                color: #28a745 !important;
                    font-weight: bold;
                    /* font-size: 1.2rem; */
                    text-align: center;
                }

                h4 {
                color: orange !important;
                    font-weight: bold;
                    /* font-size: 1.2rem; */
                    text-align: center;
                }

                    /* Styles personnalisés */
            .services-agencies-section {
                background-color: #f8f9fa; /* Couleur de fond légère */
                padding: 60px 0;
            }

            .services-agencies-section h2 {
                text-align: center;
                margin-bottom: 50px;
                font-weight: 700;
                color: #343a40;
                position: relative;
            }

            .services-agencies-section h2::after {
                content: '';
                position: absolute;
                left: 50%;
                bottom: -15px;
                transform: translateX(-50%);
                width: 80px;
                height: 4px;
                background-color: #ffae00; /* Couleur de la ligne sous le titre */
                border-radius: 2px;
            }

            /* .agency-list .card-agency {
                border: 1px solid #e9ecef;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                background-color: #ffffff;
            } */

            .agency-list .card-agency:hover {
                transform: translateY(-5px);
                box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important; /* Ombre plus prononcée au survol */
            }

            .card-agency .card-header {
                background-color: #f1f3f5;
                border-bottom: 1px solid #dee2e6;
                padding: 15px 20px;
                font-size: 1.25rem;
                color: #ffae00; /* Couleur du texte de l'en-tête */
                display: flex;
                align-items: center;
                border-radius: 5px 5px 0 0;
            }



            .agency-list .service-details li {
                padding: 8px 0;
                border-bottom: 1px dashed #e9ecef;
                font-size: 0.95rem;
                color: #495057;
            }

            .agency-list .service-details li:last-child {
                border-bottom: none;
            }

            .agency-list .service-details li i {
                margin-right: 10px;
                color: #ffae00; /* Couleur des icônes */
            }

            .agency-list .service-details li strong {
                color: #343a40;
            }
            .img-fluid {
                max-width: 100%;
                height: auto;
            }

           

            /* Médias queries pour la réactivité */
            @media (max-width: 991.98px) {
                .services-agencies-section .col-lg-5 {
                    margin-top: 40px;
                    order: -1; /* Place l'image au-dessus des agences sur les petits écrans */
                }
            }
                
            @media (min-width: 768px) {
            .container {
                padding-left: 100px;
                padding-right: 100px;
            }
            p {
                font-size: 1.25rem;
                line-height: 1.7;
            }
            h1 {
                font-size: 2.5rem;
            }
            h2 {
                font-size: 2.5rem;
            }
            h3 {
                font-size: 1.8rem;
            }
            .header-section {
                min-height: 300px;
                padding: 4rem 0;
                margin-bottom: 2rem;
            }
            .header-section p {
                font-size: 1.1rem;
            }
            .navbar-brand img {
                max-height: 70px;
            }
                .navbar-nav .nav-link.active {
                    color: #ffc107 !important;
                    border-bottom: 3px solid #ffc107;
                    background-color: rgba(255, 255, 255, 0.1);
                    border-radius: 4px 4px 0 0;
                }
            .about-intro {
                padding: 2rem;
                margin: 0 0 3rem 0;
            }
            .titre {
                font-size: 32px;
            }
            .slogan {
                font-size: 24px;
            }
            .mission-vision-section {
                margin-bottom: 3rem;
            }
            .mission-vision-section .row > div {
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }
            .mission-vision-section h3 {
                font-size: 1.6rem;
                margin-bottom: 1.5rem;
            }
            .mission-vision-section ul li {
                font-size: 1rem;
            }
            .mission-vision-section ul li i.fas, .mission-vision-section ul li span.bullet {
                font-size: 1.2em;
            }
            .team-section {
                margin-top: 4rem;
            }
            .team-member {
                margin-bottom: 2rem;
                padding: 1rem;
            }
            .team-member img {
                width: 150px;
                height: 150px;
            }
            .team-member h4 {
                font-size: 1.5rem;
            }
            .team-member p {
                font-size: 1rem;
            }
            .footer-dark {
                padding: 60px 0 30px 0;
            }
            .footer-dark h3 {
                font-size: 1.3rem;
                margin-bottom: 25px;
            }
            .footer-dark ul li a {
                font-size: 0.95rem;
            }
            .footer-dark .social a {
                font-size: 1.8rem;
            }
            .card-agency .card-header, .service-card .card-header {
                font-size: 1.2rem;
                padding: 12px 20px;
            }
            .service-icon {
                font-size: 3rem;
            }
            .service-title {
                font-size: 1.5rem;
            }
        }

        .services-agencies-section img {
            max-height: 400px !important;
            width: auto !important;
            object-fit: cover !important;
            }
        .navbar-collapse {
            justify-content: center;
        }
        

    </style>

</head>
<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand " href="/">
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
            <p> Par voie maritime ou aérienne, nous proposons des solutions globales garantissant le transport sécurisé et performant de vos marchandises vers toutes destinations internationales. Profitez de prestations personnalisées, adaptées à vos besoins spécifiques.</p>
        </div>
    </section>
        <!-- Section des Services -->
    <section class="services-agencies-section">
    <div class="container">
        <!-- Titre -->
        <h2 class="text-center mb-5">Nos Agences</h2>

        <!-- Agence France -->
        <div class="row align-items-center mb-5">
        <!-- Colonne texte -->
        <div class="col-lg-5">
            <div class="card-agency">
            <div class="card-header">
                <span class="country-flag">🇫🇷</span> FRANCE
            </div>
            <div class="card-body">
                <ul class="service-details list-unstyled m-0">
                <li><i class="fas fa-map-marker-alt"></i>
                    <strong>Adresse:</strong> 7, avenue Louis Blériot 93120 La Courneuve
                </li>
                <li><i class="fas fa-phone-alt"></i>
                    <strong>Contact:</strong> +33 1 86 78 69 67
                </li>
                <li><i class="fas fa-phone-alt"></i>
                    <strong>Contact:</strong> +33 7 66 78 54 61
                </li>
                <li><i class="fas fa-phone-alt"></i>
                    <strong>Contact:</strong> +33 6 52 98 35 19
                </li>
                </ul>
            </div>
            </div>
        </div>
        <!-- Colonne image -->
        <div class="col-lg-7 text-center">
            <img src="{{ asset('images/agencefrance.jpeg') }}" alt="Agence France" class="img-fluid rounded shadow-lg">
        </div>
        </div>

        <!-- Agence Chine -->
        <div class="row align-items-center mb-5">
        <div class="col-lg-5">
            <div class="card-agency">
            <div class="card-header">
                <span class="country-flag">🇨🇳</span> CHINE
            </div>
            <div class="card-body">
                <ul class="service-details list-unstyled m-0">
                <li><i class="fas fa-map-marker-alt"></i>
                    <strong>Maritime:</strong> ⼴东省佛⼭市南海区⾥⽔镇河塱沙路D2仓
                </li>
                <li><i class="fas fa-map-marker-alt"></i>
                    <strong>Maritime:</strong> Province du Guangdong, Foshan, district de Nanhai, ville de Lishui, route Helangsha, entrepôt D2.
                </li>
                <li><i class="fas fa-map-marker-alt"></i>
                    <strong>Aérien:</strong> ⼴州市环市中路205号恒⽣⼤厦B座室918
                </li>
                <li><i class="fas fa-map-marker-alt"></i>
                    <strong>Aérien:</strong> Guangzhou, 205 avenue Huanshi Zhong, immeuble Hengsheng, bloc B, bureau 918
                </li>
                <li><i class="fas fa-phone-alt"></i>
                    <strong>Contact:</strong> +86 13 67 89 15 049
                </li>
                </ul>
            </div>
            </div>
        </div>
        <div class="col-lg-7 text-center">
            <img src="{{ asset('images/agencechine.jpeg') }}" alt="Agence Chine" class="img-fluid rounded shadow-lg">
        </div>
        </div>

        <!-- Agence Côte d'Ivoire -->
        <div class="row align-items-center mb-5">
        <div class="col-lg-5">
            <div class="card-agency">
            <div class="card-header">
                <span class="country-flag">🇨🇮</span> CÔTE D'IVOIRE
            </div>
            <div class="card-body">
                <ul class="service-details list-unstyled m-0">
                <li><i class="fas fa-map-marker-alt"></i>
                    <strong>Maritime:</strong> Carrefour Angré
                </li>
                <li><i class="fas fa-phone-alt"></i>
                    <strong>Contact:</strong> +225 0758069896
                </li>
                <li><i class="fas fa-map-marker-alt"></i>
                    <strong>Aérien:</strong> Carrefour Nelson Mandela, Angré 8ème tranche
                </li>
                <li><i class="fas fa-phone-alt"></i>
                    <strong>Contact:</strong> +225 0758069896
                </li>
                </ul>
            </div>
            </div>
        </div>
        <div class="col-lg-7 text-center">
            <img src="{{ asset('images/dstranslog.jpeg') }}" alt="Agence Côte d'Ivoire" class="img-fluid rounded shadow-lg">
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
                <p><strong>Adresse :</strong>Bureau 918, Bâtiment B, Hengsheng Dasha, 205 Huanshi Middle Road, Ville de Guangzhou (Canton), Chine.</p>
                <p class="contact-info"><a href="mailto:douane@aft-app.com">douane@aft-app.com</a></p>
                <p>Votre porte d'entrée et de sortie Pour le colis maritime et Aérien.</p>
            </div>

            <!-- Agence 4 -->
            <div class="agency-card">
                <div class="icon-wrapper">
                    <i class="fas fa-building"></i>
                </div>
                <h4>Agence Louis Blériot</h4>
                <p><strong>Adresse :</strong> BP 45, Avenue des Affaires, Paris, France</p>
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
            text-align: center;
        }

        .agencies-horizontal-scroll .agency-card {
            flex: 0 0 auto; /* Empêche les cartes de rétrécir */
            width: 400px; /* Définir une largeur fixe pour chaque carte */
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