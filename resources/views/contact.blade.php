<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Aft Import Export</title>
    <!-- Liens CSS existants -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        /* Styles de base et barre de navigation */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        .navbar-nav .nav-link { color: white !important; font-weight: bold; }
        .navbar-nav .nav-link:hover { color: orange !important; }
        .navbar-nav .nav-link.active { text-decoration: underline; text-decoration-color: orange; }
        @media (max-width: 767px) { .navbar-brand img { max-height: 50px; } }

        /* --- STYLES POUR LA PAGE CONTACT --- */

        /* En-tête de la page */
        .header-section {
            background-color: #ff9500; /* Orange vif */
            color: white;
            padding: 50px 0;
            text-align: center;
        }
        .header-section h1 { font-size: 3em; font-weight: bold; }
        .header-section p { font-size: 1.2em; opacity: 0.9; }

        /* Section principale du contenu */
        .contact-content-section {
            padding: 60px 0;
        }
        
        .section-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            color: #343a40;
            margin-bottom: 50px;
        }

        /* Cartes des agences */
        .agency-card {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
            /* La hauteur sera gérée par flexbox pour un alignement parfait */
        }
        .agency-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #343a40;
            margin-bottom: 20px;
        }
        .agency-title .flag-icon {
            font-size: 1.3rem;
            margin-right: 10px;
        }
        .contact-list {
            list-style: none;
            padding-left: 0;
        }
        .contact-list li {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            color: #555;
            line-height: 1.6;
        }
        .contact-list i {
            width: 20px;
            text-align: center;
            margin-right: 15px;
            margin-top: 5px;
            color: #ff9500;
        }

        /* Styles du pied de page */
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
                        <li class="nav-item"><a class="nav-link mx-3" href="/">Accueil</a></li>
                        <li class="nav-item"><a class="nav-link mx-3" href="/a-propos">À propos de nous</a></li>
                        <li class="nav-item"><a class="nav-link mx-3" href="/nos-servives">Nos services</a></li>
                        <li class="nav-item"><a class="nav-link active mx-3" href="/contact">Contact</a></li>
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
        <section class="header-section">
            <div class="container">
                <h1>Contactez-nous</h1>
                <p>Retrouvez ici les coordonnées de toutes nos agences.</p>
            </div>
        </section>

        <section class="contact-content-section">
            <div class="container">
                <h2 class="section-title">Nos Agences à Votre Service</h2>
                
                <!-- Grille des agences -->
                <div class="row g-4">
                    <!-- Agence France -->
                    <div class="col-lg-6 col-md-6 d-flex">
                        <div class="agency-card w-100">
                            <h4 class="agency-title"><span class="flag-icon">🇫🇷</span> France</h4>
                            <ul class="contact-list">
                                <li><i class="fas fa-map-marker-alt"></i> 7, avenue Louis Blériot 93120 La Courneuve</li>
                                <li><i class="fas fa-phone-alt"></i> +33 1 86 78 69 67</li>
                                <li><i class="fas fa-phone-alt"></i> +33 7 66 78 54 61</li>
                                <li><i class="fas fa-phone-alt"></i> +33 6 52 98 35 19</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Agence Chine -->
                    <div class="col-lg-6 col-md-6 d-flex">
                        <div class="agency-card w-100">
                            <h4 class="agency-title"><span class="flag-icon">🇨🇳</span> Chine</h4>
                            <ul class="contact-list">
                                <li><i class="fas fa-ship"></i> <strong>Maritime:</strong> ⼴东省佛⼭市南海区⾥⽔镇河塱沙路D2仓。</li>
                                <li><i class="fas fa-plane"></i> <strong>Aérien:</strong> ⼴州市环市中路205号恒⽣⼤厦B座室918</li>
                                <li><i class="fas fa-phone-alt"></i> +86 13 67 89 15 049</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Agence Côte d'Ivoire - Abidjan -->
                    <div class="col-lg-6 col-md-6 d-flex">
                        <div class="agency-card w-100">
                            <h4 class="agency-title"><span class="flag-icon">🇨🇮</span> Côte d'Ivoire - Abidjan</h4>
                             <ul class="contact-list">
                                <li><i class="fas fa-ship"></i> <strong>Maritime:</strong> CARREFOUR ANGRE</li>
                                <li><i class="fas fa-plane"></i> <strong>Aérien:</strong> Carrefour NELSON MANDELA ANGRE 8eme tranche</li>
                                <li><i class="fas fa-phone-alt"></i> +225 75 80 69 896</li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Agence Côte d'Ivoire - San-Pédro -->
                    <div class="col-lg-6 col-md-6 d-flex">
                         <div class="agency-card w-100">
                            <h4 class="agency-title"><span class="flag-icon">🇨🇮</span> Côte d'Ivoire - San-Pédro</h4>
                             <ul class="contact-list">
                                <li><i class="fas fa-map-marker-alt"></i> San Pedro au feu de la petite mairie coté voie pavée (UTE)</li>
                                <li><i class="fas fa-phone-alt"></i> +225 27 33 74 95 19</li>
                                <li><i class="fas fa-phone-alt"></i> +225 74 940 74 02</li>
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
                © {{ date('Y') }} AFT IMPORT EXPORT. Tous droits réservés.
            </div>
        </div>
    </footer>

    <!-- Scripts JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>