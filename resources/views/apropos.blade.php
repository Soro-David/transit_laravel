<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À Propos - Aft Import Export</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.css"
          integrity="sha512-wR4o5EQOVWL53c/bJtCQiRzE6C8zvcbMYiE20uRyVwPjxU5KmQ2SeZZ4yhJfQ8zhEXRzVFMkVVnLWZI6+m9SQg=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.css"
          integrity="sha512-6lLUqr3C5NW0ot4CEqtOUeNmdB0clNH5stMboo/uQDD0CLwLEoqk95Ktr9IwOAAvontuUFAWnzK8vJ0FJoaoQ=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <style>
        .header-section {
            background-image: url('{{ asset('images/equip1.png') }}'); /* Utilisation de l'image de fond */
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
            margin-bottom: 20px;
            color: rgb(255, 153, 0);
        }
        
        /* CORRECTION: Titres h2 en vert */
        h2 {
            color: #388b00 !important; /* Vert */
            margin-bottom: 20px;
        }
        
        /* CORRECTION: Titres h3 en orange */
        h3 {
            color: orange !important; /* Orange */
            margin-bottom: 20px;
        }

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
            /* padding: 0.5rem 1rem; */
            font-weight: bold;
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

        /* Ajustement de la taille du logo */
        @media (max-width: 767px) {
            .navbar-brand img {
                max-height: 50px; /* Réduire la taille du logo sur les petits écrans */
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
            /* padding: 60px 0 30px 0; */
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

        .container {
        max-width: 100% !important;
        padding-left: 100px;
        padding-right: 100px;
        }

        p {
            font-size: 1.25rem;
            line-height: 1.7;
            color: #555;;
        }

        ul {
            list-style-type: disc;
            padding-left: 20px;
        }

        li {
            margin-bottom: 10px;
        }

        .img-fluid {
            width: 100%;
            height: auto;
        }

        .rounded-image { /* Nouveau style pour les images encadrées */
            border-radius: 10px;
            /* box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); */
            transition: transform 0.3s ease-in-out;
        }
        .rounded-image:hover {
            transform: scale(1.02);
        }

       .about-intro {
            width: 100%;                 /* prend toute la largeur */
            margin: 0 0 3rem 0;          /* marge en bas uniquement */
            padding: 2rem;               /* espace intérieur */
            background-color: #ffffff;   /* fond blanc */
            border-radius: 0;            /* si tu veux vraiment bord à bord, sinon laisse 8px */
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .mission-vision-section {
            margin-bottom: 3rem;
        }
        .mission-vision-section .row > div {
            padding: 1.5rem;
            background-color: #ffffff;
            /* border-radius: 8px; */
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }
        .mission-vision-section h3 {
            margin-bottom: 1rem;
            font-weight: bold;
        }
        .mission-vision-section ul {
            list-style: none;
            padding-left: 0;
        }
        .mission-vision-section ul li {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: flex-start;
        }
        .mission-vision-section ul li i.fas, .mission-vision-section ul li span.bullet {
            margin-right: 10px;
            color: orange;
            font-size: 1.2em;
        }
        .mission-vision-section ul li strong {
            color: #333;
        }

        .team-section {
            margin-top: 4rem;
            text-align: center;
        }
        .team-member {
            margin-bottom: 2rem;
            padding: 1rem;
            /* background-color: #ffffff; */
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .team-member img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 1rem;
            border: 3px solid orange;
        }
        .team-member h4 {
            color: #333;
            margin-bottom: 0.5rem;
        }
        .team-member p {
            color: orange;
            font-weight: bold;
        }

        .text-orange {
            color: orange !important;
        }
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
            font-size: 2rem;
            font-weight: bold;
            color: #333;
            position: relative;
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

                .mission-vision-section {
            /* padding: 4rem 0; */
            background-color: var(--light-bg);
        }
        
        .mission-vision-section h3 {
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 3px solid var(--primary-color);
            display: inline-block;
        }
        
        .mission-vision-section h3 i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .services-list {
            list-style: none;
            padding: 0;
        }
        
        .services-list > li {
            margin-bottom: 2rem;
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }
        
        .services-list > li:hover {
            transform: translateY(-5px);
        }
        
        .bullet {
            display: inline-block;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .text-orange {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .service-details {
            list-style: none;
            padding-left: 0;
            margin-top: 0.8rem;
        }
        
        .service-details li {
            /* padding: 0.5rem 0; */
            display: flex;
            align-items: flex-start;
        }
        
        .service-details i {
            margin-right: 10px;
            color: var(--primary-color);
            min-width: 20px;
            margin-top: 4px;
        }
        
        .rounded-image {
            border-radius: 8px;
            /* box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); */
            transition: transform 0.3s ease;
        }
        
        .rounded-image:hover {
            transform: scale(1.02);
        }
        
        @media (max-width: 768px) {
            .mission-vision-section {
                padding: 2rem 0;
            }
            
            .services-list > li {
                padding: 1rem;
            }
        }
        .service-title {
            margin-top: 1rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;

        }
        .card-header {
            /* background-color: orange; */
            color: rgb(0, 0, 0);
            font-weight: bold;
            font-size: 1.2rem;
            text-align: center;
        }
    </style>
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
                            <a class="nav-link mx-3" href="/">Accueil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active mx-3" aria-current="page" href="/a-propos">À propos de nous</a>
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

    <section class="header-section">
        <div class="container">
            <h1>A propos de nous</h1>
            <p>Des solutions de transport et de logistique adaptées à vos besoins.</p>
        </div>
    </section>

    <section class="about-page">
        <div class="container">
            <section class="about-intro text-center">
                <h2 class="titre">Notre Histoire et Notre Engagement</h2>
                <p class="about-text">
                    <strong style="color: green;">Afrique Fret Transit Import Export</strong> est une entreprise
                    spécialisée dans le fret maritime et aérien, avec <strong class="text-orange">10 ans
                    d’expérience</strong> dans le domaine du transport
                    international. Nous offrons des solutions logistiques
                    sur mesure pour répondre aux besoins de nos clients en
                    matière d’import et d’export. Notre mission est de simplifier vos opérations logistiques et d'assurer une livraison rapide et sécurisée de vos marchandises partout dans le monde.
                </p>
                {{-- <div class="row justify-content-center mt-4">
                    <div class="col-md-8">
                        <!-- Image introductive -->
                        <img src="{{ asset('images/slide3.jpg') }}" alt="Vue d'ensemble des opérations AFT" class="img-fluid rounded-image">
                        <!-- Ici, tu pourrais générer une image du type: "A modern logistics hub with diverse transportation methods (ships, planes, trucks) operating efficiently, illustrating global import and export." -->
                        
                    </div>
                </div> --}}
            </section>

            <section class="mission-vision-section">
                <div class="row">
                    <div class="col-md-6 zt">
                        <h3><i class="fas fa-route text-orange"></i> NOTRE EXPERTISE COUVRE LES AXES SUIVANTS :</h3>
                        <ul>
                            <li><i class="fas fa-globe-americas"></i> Fret maritime et aérien de l’Europe vers la Côte d’Ivoire et l’Afrique de l’Ouest.</li>
                            <li><i class="fas fa-plane-departure"></i> Fret maritime et aérien de la Chine vers la Côte d’Ivoire.</li>
                            <li><i class="fas fa-ship"></i> Fret maritime et aérien de la Chine vers la France.</li>
                        </ul>
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <!-- Image pour l'expertise -->
                                <img src="{{ asset('images/atout.jpeg') }}" alt="Routes logistiques mondiales" class="img-fluid rounded-image">
                                <!-- Ici, tu pourrais générer une image du type: "A stylized world map highlighting major shipping routes between Europe, China, Ivory Coast, and West Africa, with icons for sea and air transport." -->
                                
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 zt">
                        <h3><i class="fas fa-award text-orange"></i> NOS ATOUTS</h3>
                        <ul style="list-style-type: disc; padding-left: 20px;">
                            <li><i class="fas fa-certificate text-orange"></i> <strong class="text-orange">Expérience :</strong> 10 ans d’expertise dans le fret international.</li>
                            <li><i class="fas fa-network-wired text-orange"></i> <strong class="text-orange">Réseau :</strong> Partenaires fiables en Europe, en Chine et en Afrique de l’Ouest.</li>
                            <li><i class="fas fa-handshake text-orange"></i> <strong class="text-orange">Flexibilité :</strong> Solutions adaptées aux besoins spécifiques de chaque client.</li>
                            <li><i class="fas fa-chart-line text-orange"></i> <strong class="text-orange">Transparence :</strong> Suivi en temps réel des expéditions.</li>
                        </ul>
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <!-- Image pour les atouts -->
                                <img src="{{ asset('images/slide3.jpg') }}" alt="Illustration des atouts AFT" class="img-fluid rounded-image">
                                <!-- Ici, tu pourrais générer une image du type: "A dynamic infographic showing four key strengths: 'Experience' with a clock icon, 'Network' with connected nodes, 'Flexibility' with adaptive gears, and 'Transparency' with a magnifying glass over a package." -->
                                
                            </div>
                        </div>
                    </div>
                </div>
            </section>

    <section class="mission-vision-section">
        <div class="container">
            <h2 class="section-title">Nos Agences et Services</h2>
            
            <div class="row">
                <!-- Agences -->
                <div class="col-lg-6 mb-5">
                    <h3 class="zt text-center"><i class="fas fa-building me-2"></i>NOS AGENCES</h3>
                    
                    <div class="row">
                        <!-- France -->
                        <div class="col-md-12 zt">
                            <div class="card-agency">
                                <div class="card-header">
                                    <span class="country-flag">🇫🇷</span> FRANCE
                                </div>
                                <div class="card-body">
                                    <ul class="service-details">
                                        <li><i class="fas fa-map-marker-alt"></i> <strong>Adresse:</strong> 7, avenue Louis Blériot 93120 La Courneuve</li>
                                        <li><i class="fas fa-phone-alt"></i> <strong>Contact:</strong> +33 1 86 78 69 67</li>
                                        <li><i class="fas fa-phone-alt"></i> <strong>Contact:</strong> +33 7 66 78 54 61</li>
                                        <li><i class="fas fa-phone-alt"></i> <strong>Contact:</strong> +33 6 52 98 35 19</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Chine -->
                        <div class="col-md-12 zt">
                            <div class="card-agency">
                                <div class="card-header">
                                    <span class="country-flag">🇨🇳</span> CHINE
                                </div>
                                <div class="card-body">
                                    <ul class="service-details">
                                        <li><i class="fas fa-map-marker-alt"></i> <strong>Maritime:</strong> ⼴东省佛⼭市南海区⾥⽔镇河塱沙路D2仓</li>
                                        <li><i class="fas fa-map-marker-alt"></i> <strong>Aérien:</strong> ⼴州市环市中路205号恒⽣⼤厦B座室918</li>
                                        <li><i class="fas fa-phone-alt"></i> <strong>Contact:</strong> +86 13 67 89 15 049</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Côte d'Ivoire -->
                        <div class="col-md-12 zt">
                            <div class="card-agency">
                                <div class="card-header">
                                    <span class="country-flag">🇨🇮</span> CÔTE D'IVOIRE
                                </div>
                                <div class="card-body">
                                    <ul class="service-details">
                                        <li><i class="fas fa-map-marker-alt"></i> <strong>Maritime:</strong> CARREFOUR ANGRE</li>
                                        <li><i class="fas fa-phone-alt"></i> <strong>Contact:</strong>+225 0758069896</li>
                                        <li><i class="fas fa-map-marker-alt"></i> <strong>Aérien:</strong> Carrefour NELSON MANDELA ANGRE 8eme tranche</li>
                                        <li><i class="fas fa-phone-alt"></i> <strong>Contact:</strong> +225 0758069896</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Services -->
                <div class="col-lg-6 mb-5">
                    <h3 class="zt text-center"><i class="fas fa-box-open me-2"></i>NOS SERVICES</h3>
                    
                    <div class="row">
                        <!-- Fret Maritime -->
                        <div class="col-md-12 zt">
                            <div class="service-card">
                                <div class="card-body text-center">
                                    <div class="service-icon">
                                        <i class="fas fa-ship"></i>
                                    </div>
                                    <h4 class="service-title">FRET MARITIME</h4>
                                    <ul class="service-details">
                                        <li><i class="fas fa-pallet"></i> Transport de conteneurs (20', 40', 40' HQ)</li>
                                        <li><i class="fas fa-people-carry"></i> Groupage (LCL) et plein chargement (FCL)</li>
                                        <li><i class="fas fa-shipping-fast"></i> Suivi personnalisé et sécurisé des marchandises</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Fret Aérien -->
                        <div class="col-md-12 zt">
                            <div class="service-card">
                                <div class="card-body text-center">
                                    <div class="service-icon">
                                        <i class="fas fa-plane"></i>
                                    </div>
                                    <h4 class="service-title">FRET AÉRIEN</h4>
                                    <ul class="service-details">
                                        <li><i class="fas fa-plane"></i> Livraison rapide et sécurisée pour les envois urgents</li>
                                        <li><i class="fas fa-file-invoice"></i> Gestion des formalités douanières</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Services Complémentaires -->
                        <div class="col-md-12 zt">
                            <div class="service-card">
                                <div class="card-body text-center">
                                    <div class="service-icon">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                    <h4 class="service-title">SERVICES COMPLÉMENTAIRES</h4>
                                    <ul class="service-details">
                                        <li><i class="fas fa-truck-loading"></i> Dédouanement et conseils en logistique</li>
                                        <li><i class="fas fa-warehouse"></i> Stockage et distribution locale</li>
                                        <li><i class="fas fa-shield-alt"></i> Assurance des marchandises</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           
        </div>
    </section>
            <section class="mission-vision-section">
                <div class="row">
                    <div class="col-md-6 zt">
                        <h3><i class="fas fa-map-marked-alt text-orange"></i> ZONES D’INTERVENTION :</h3>
                        <ul>
                            <li><i class="fas fa-euro-sign text-orange"></i> <strong class="text-orange">Europe : </strong>France, Belgique, Allemagne, etc.</li>
                            <li><i class="fas fa-yen-sign text-orange"></i> <strong class="text-orange">Asie : </strong>Chine (principalement)</li>
                            <li><i class="fas fa-dollar-sign text-orange"></i> <strong class="text-orange">Afrique : </strong>Côte d’Ivoire, Sénégal, Mali, Burkina Faso, etc.</li>
                        </ul>
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <!-- Image pour les zones d'intervention -->
                                <img src="{{ asset('images/atout.jpg') }}" alt="Zones d'Intervention AFT" class="img-fluid rounded-image">
                                <!-- Ici, tu pourrais générer une image du type: "A world map with highlighted regions for Europe, Asia (China), and West Africa, with lines connecting them, symbolizing global reach." -->
                                
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 zt">
                        <h3><i class="fas fa-question-circle text-orange"></i> POURQUOI NOUS CHOISIR ?</h3>
                            <ul style="list-style-type: disc; padding-left: 20px;">
                                <li>
                                    <i class="fas fa-piggy-bank text-orange"></i> 
                                    <strong class="text-orange">Compétitivité :</strong>
                                    Tarifs attractifs et sur mesure.
                                </li>
                                <li>
                                    <i class="fas fa-lightbulb text-orange"></i> 
                                    <strong class="text-orange">Expertise :</strong> 
                                    Connaissance approfondie des marchés européens, africains et asiatiques.
                                </li>
                               
                                <li>
                                    <i class="fas fa-shield-alt text-orange"></i> 
                                    <strong class="text-orange">Fiabilité :</strong> 
                                    Nous garantissons un service sécurisé et constant, basé sur la confiance.
                                </li>
                                <li>
                                    <i class="fas fa-sync-alt text-orange"></i> 
                                    <strong class="text-orange">Régularité :</strong> 
                                    Nos opérations sont planifiées et suivies de manière rigoureuse.
                                </li>
                                <li>
                                    <i class="fas fa-bolt text-orange"></i> 
                                    <strong class="text-orange">Rapidité :</strong> 
                                    Nous privilégions l’efficacité et la réactivité afin de réduire les délais.
                            </ul>

                        <div class="row justify-content-center">
                            <div class="col-12">
                                <!-- Image pour "Pourquoi nous choisir" -->
                                <img src="{{ asset('images/contact-us.jpg') }}" alt="Avantages AFT" class="img-fluid rounded-image">
                                <!-- Ici, tu pourrais générer une image du type: "An image representing value and knowledge: a scales balancing cost-effectiveness and a brain icon with a map inside, symbolizing market knowledge." -->
                                
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="team-section">
                <h2 class="titre">Notre Équipe Dédiée</h2>

                <div class="row justify-content-center">
                    <div class="col-md-4 team-member">
                        <!-- Image pour M. Sylla Adama -->
                        <img src="{{ asset('images/profil4.jpg') }}" alt="M. Sylla Adama" class="rounded-image">
                        <!-- Ici, tu pourrais générer une image du type: "A professional portrait of a smiling African man in a business suit, representing a General Director." -->
                        
                        <h4>M. Sylla Adama</h4>
                        <p class="text-orange">Directeur Général</p>
                    </div>
                    <div class="col-md-4 team-member">
                        <!-- Image pour M. Sylla Ousmane -->
                        <img src="{{ asset('images/profil2.jpg') }}" alt="M. Sylla Ousmane" class="rounded-image">
                        <!-- Ici, tu pourrais générer une image du type: "A professional portrait of a focused African man in a smart-casual outfit, representing a Logistics Manager." -->
                        
                        <h4>M. Sylla Ousmane</h4>
                        <p class="text-orange">Responsable Logistique</p>
                    </div>
                    <div class="col-md-4 team-member">
                        <!-- Image pour M. Bakary -->
                        <img src="{{ asset('images/profil5.jpg') }}" alt="M. Bakary" class="rounded-image">
                        <!-- Ici, tu pourrais générer une image du type: "A professional portrait of a friendly African man with a confident smile, representing a Sales Manager." -->
                        
                        <h4>M. Bakary</h4>
                        <p class="text-orange">Responsable Commercial</p>
                    </div>
                </div>
            </section>

        </div>
    </section>

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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"
            integrity="sha512-XtmMtDEcNz2j7ekrtHvOVR4iwwMD6yrwpJy8UZjRseKbslzvcNFaz6AY9dlK8xjKYGgOB/mpiVNixFD4iHdq8Q=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.slider').slick({
                autoplay: true,
                dots: true,
                arrows: false
            });
        });
    </script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>