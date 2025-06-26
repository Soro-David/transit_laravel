<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Services - Aft Import Export</title>
    <!-- Liens CSS existants -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
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

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand me-auto" href="/">
                    <img src="{{ asset('images/LOGOAFT.png') }}" alt="Logo Aft Import Export" class="img-fluid" style="max-height: 70px;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarButtonsExample" aria-controls="navbarButtonsExample" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarButtonsExample">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item"><a class="nav-link mx-3" aria-current="page" href="/">Accueil</a></li>
                        <li class="nav-item"><a class="nav-link mx-3" href="/a-propos">À propos de nous</a></li>
                        <li class="nav-item"><a class="nav-link active mx-3" href="/nos-servives">Nos services</a></li>
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

    <main>
       <section class="header-section" style="background-color: green; color: white; padding: 2em 0;">
            <div class="container">
                <h1>Nos Services</h1>
                <p>Découvrez l'ensemble de nos solutions de transport, de logistique et nos implantations stratégiques pour vous accompagner partout dans le monde.</p>
            </div>
        </section>

        
        <section class="services-agencies-section">
            <div class="container">
                <div class="row">
                    <!-- COLONNE AGENCES -->
                    <div class="col-lg-6 d-flex">
                        <div class="info-card w-100">
                            <h3>NOS AGENCES</h3>
                            <ul class="main-list">
                                <li>
                                    <span class="bullet">🇫🇷</span> <strong class="text-orange">FRANCE</strong>
                                    <ul class="details-list">
                                       <li><i class="fas fa-map-marker-alt"></i> 7, avenue Louis Blériot 93120 La Courneuve</li>
                                       <li><i class="fas fa-phone-alt"></i> <strong>Contact :</strong> +33 1 86 78 69 67</li>
                                       <li><i class="fas fa-phone-alt"></i> <strong>Contact :</strong> +33 7 66 78 54 61</li>
                                       <li><i class="fas fa-phone-alt"></i> <strong>Contact :</strong> +33 6 52 98 35 19</li>
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
                                         <li><i class="fas fa-phone-alt"></i> <strong>Contact :</strong> +86 13 67 89 15 049</li>
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
                    <div class="col-lg-6 d-flex">
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
            </div>
        </section>
    </main>

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
                © 2024 Aft Import Export. Tous droits réservés.
            </div>
        </div>
    </footer>

    <!-- Scripts JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Initialisation d'un slider si besoin sur une autre page
            // $('.slider').slick({ ... });
        });
    </script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>