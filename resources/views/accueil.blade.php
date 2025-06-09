<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aft Import Export</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.css"
          integrity="sha512-wR4o5EQOVWL53c/bJtCQiRzE6C8zvcbMYiE20uRyVwPjxU5KmQ2SeZZ4yhJfQ8zhEXRzVFMkVVnLWZI6+m9SQg=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.css"
          integrity="sha512-6lLUqr3C5NW0ot4CEqtOUeNmdB0clNH5stMboo/uQDD0CLwLEoqk95Ktr9IwOAAvontuUFAWnzK8vJ0FJoaoQ=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <style>
        /* Styles personnalisés */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }

        header {
            /* background-color: #007bff; */
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
            max-height: 400px; /* Ajustez selon vos besoins */
            object-fit: cover; /* Pour que les images remplissent l'espace sans déformation */
        }

        main {
            padding: 2rem 0;
        }

        .container {
            max-width: 960px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        p {
            font-size: 1.1rem;
            line-height: 1.6;
            text-align: center;
        }

        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 1rem 0;
        }

        /* Centrer le menu */
        .navbar-collapse {
            justify-content: center;
        }
        .navbar-nav .nav-link {
                color: white !important;
                font-weight: bold;
                padding: 0.5rem 1rem; /* Ajustez l'espacement vertical et horizontal */
            }
    
            .navbar-nav .nav-link:hover {
                color: orange !important;
            }
    
            .navbar-nav .nav-link.active {
                text-decoration: underline;
                text-decoration-color: orange;
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
            position: absolute; /* Place le caption par-dessus l'image */
            top: 50%; /* Centre verticalement */
            left: 50%; /* Centre horizontalement */
            transform: translate(-50%, -50%); /* Ajuste le positionnement pour un centrage parfait */
            /* background-color: rgb(255, 255, 255); Fond transparent */
            padding: 20px;
            border-radius: 10px;
            width: 80%; /* Ajustez la largeur selon vos besoins */
            text-align: center; /* Centre le texte */
        }
    
        .carousel {
            height: 400px; /* Ajustez la hauteur selon vos besoins */
        }
    
        .carousel-item {
            height: 550px; /* Ajustez la hauteur selon vos besoins */
        }
    
        .carousel-item img {
            object-fit: cover; /* Remplir l'espace, peut rogner l'image */
            height: 700px; /* Ajustez la hauteur selon vos besoins */
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
            background-color: #343a40; /* Couleur de fond sombre */
            color: #fff; /* Couleur du texte clair */
            padding: 50px 0;
        }

        .footer-dark h3 {
            margin-top: 0;
            margin-bottom: 12px;
            font-weight: bold;
            font-size: 1.2em;
        }

        .footer-dark ul {
            padding: 0;
            list-style: none;
            line-height: 1.6;
            font-size: 14px;
        }

        .footer-dark ul a {
            color: inherit;
            text-decoration: none;
            opacity: 0.8;
        }

        .footer-dark ul a:hover {
            opacity: 1;
        }

        .footer-dark .social {
            text-align: center;
        }

        .footer-dark .social > a {
            font-size: 24px;
            width: 40px;
            height: 40px;
            line-height: 40px;
            display: inline-block;
            text-align: center;
            border-radius: 50%;
            border: 1px solid #fff;
            margin: 0 8px;
            color: inherit;
            opacity: 0.75;
        }

        .footer-dark .social > a:hover {
            opacity: 0.9;
        }

        .footer-dark .copyright {
            text-align: center;
            padding-top: 24px;
            opacity: 0.7;
            font-size: 13px;
        }
        .container {
            max-width: 1200px; /* Ajustez la largeur maximale selon vos besoins */
        }
        
        h2 {
            color: #333; /* Couleur du titre */
            margin-bottom: 20px;
        }
        
        p {
            color: #666; /* Couleur du texte */
            line-height: 1.6;
        }
        
        ul {
            list-style-type: disc; /* Style des puces */
            padding-left: 20px;
        }
        
        li {
            margin-bottom: 10px;
        }
        
        .img-fluid {
            width: 100%; /* L'image prend toute la largeur de son conteneur */
            height: auto; /* La hauteur s'ajuste automatiquement pour conserver les proportions */
        }
        
        .rounded {
            border-radius: 10px; /* Arrondit les coins de l'image */
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
                            <a class="nav-link active mx-3" aria-current="page" href="/">Accueil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-3" href="/a-propos">À propos de nous</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-3" href="/nos-servives">Nos services</a>
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
                    <img src="{{ asset('images/slide1.jpeg') }}" class="d-block w-100 carousel-image" alt="Import & Export Solutions">
                    <div class="carousel-caption d-md-block">
                        <h5 class="text-white fw-bold"></h5>
                        <p class="text-white fw-bold"></p>
                        <div>
                            <a href="{{ route('customer_colis.create.colis') }}" class="btn btn-danger demande-devis">Demande de devis</a>
                            <a href="#" class="btn btn-outline-light contactez-nous">Contactez nous</a>
                        </div>
                    </div>
                </div>
                <div class="carousel-item" data-bs-interval="5000">
                    <img src="{{ asset('images/slide2.jpg') }}" class="d-block w-100 carousel-image" alt="Reliable Logistics">
                    <div class="carousel-caption d-md-block">
                        <h5 class="text-white fw-bold"></h5>
                        <p class="text-white fw-bold"></p>
                        <div>
                            <a href="{{ route('customer_colis.create.colis') }}" class="btn btn-danger demande-devis">Demande de devis</a>
                            <a href="#" class="btn btn-outline-light contactez-nous">Contactez nous</a>
                        </div>
                    </div>
                </div>
                <div class="carousel-item" data-bs-interval="5000">
                    <img src="{{ asset('images/slide3.jpg') }}" class="d-block w-100 carousel-image" alt="Customs Clearance">
                    <div class="carousel-caption d-md-block">
                        <h5 class="text-white fw-bold"></h5>
                        <p class="text-white fw-bold"></p>
                        <div>
                            <a href="{{ route('customer_colis.create.colis') }}" class="btn btn-danger demande-devis">Demande de devis</a>
                            <a href="#" class="btn btn-outline-light contactez-nous">Contactez nous</a>
                        </div>
                    </div>
                </div>
                <div class="carousel-item" data-bs-interval="5000">
                    <img src="{{ asset('images/slide4.jpg') }}" class="d-block w-100 carousel-image" alt="Global Network">
                    <div class="carousel-caption d-md-block">
                        <h5 class="text-white fw-bold"></h5>
                        <p class="text-white fw-bold"></p>
                        <div>
                            <a href="#" class="btn btn-danger demande-devis">Demande de devis</a>
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
    </section>

    <section class="container my-5">
        <div class="row">
            <div class="col-md-6">
                <h2>Pourquoi nous choisir ?</h2>
                <p>Nous offrons des solutions innovantes et adaptées à vos besoins spécifiques. Notre équipe d'experts est dédiée à votre succès.</p>
                <ul>
                    <li>Expertise reconnue dans le secteur</li>
                    <li>Solutions personnalisées et flexibles</li>
                    <li>Support client réactif et dédié</li>
                    <li>Résultats mesurables et concrets</li>
                </ul>
            </div>
            <div class="col-md-6">
                <img src="{{ asset('images/slide3.jpg') }}" alt="Pourquoi nous choisir ?" class="img-fluid rounded">
            </div>
        </div>
    </section>

<footer class="footer-dark">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <h3>À propos de nous</h3>
                <ul>
                    <li><a href="#">Notre histoire</a></li>
                    <li><a href="#">Notre équipe</a></li>
                    <li><a href="#">Carrières</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h3>Services</h3>
                <ul>
                    <li><a href="#">Fret maritime</a></li>
                    <li><a href="#">Fret aérien</a></li>
                    <li><a href="#">Logistique</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h3>Informations</h3>
                <ul>
                    <li><a href="#">Contactez-nous</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Blog</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h3>Suivez-nous</h3>
                <div class="social">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <div class="copyright">
            © 2024 Mon Entreprise. Tous droits réservés.
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