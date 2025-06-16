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
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
                            <a class="nav-link  mx-3" aria-current="page" href="/">Accueil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-3" href="/a-propos">À propos de nous</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active mx-3" href="/nos-servives">Nos services</a>
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
            <h1>Nos Services</h1>
            <p>Des solutions de transport et de logistique adaptées à vos besoins.</p>
        </div>
    </section>
    
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="service-box">
                    <i class="fas fa-plane service-icon"></i>
                    <h3 class="service-title">Service de France vers Côte d'Ivoire</h3>
                    <p class="service-description">
                        Nous offrons un service de transport aérien rapide et fiable depuis la France vers la Côte d'Ivoire.
                        Profitez de nos solutions personnalisées pour vos envois urgents.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="service-box">
                    <i class="fas fa-ship service-icon"></i>
                    <h3 class="service-title">Service de Chine vers Côte d'Ivoire</h3>
                    <p class="service-description">
                        Importez vos marchandises depuis la Chine vers la Côte d'Ivoire grâce à notre service de transport maritime.
                        Nous gérons l'ensemble du processus, de la collecte à la livraison.
                    </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="service-box">
                    <i class="fas fa-truck service-icon"></i>
                    <h3 class="service-title">Fret Maritime</h3>
                    <p class="service-description">
                        Bénéficiez de notre service de fret maritime pour tous vos besoins de transport à l'international.
                        Nous vous garantissons un transport sécurisé et économique pour vos marchandises.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="service-box">
                    <i class="fas fa-rocket service-icon"></i>
                    <h3 class="service-title">Fret Aérien</h3>
                    <p class="service-description">
                        Optez pour notre service de fret aérien pour vos envois les plus urgents. Nous vous proposons des
                        solutions rapides et efficaces pour respecter vos délais.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <style>
 
    </style>

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