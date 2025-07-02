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
                .header-section {
            background-color:rgb(255, 149, 0);
            color: white;
            padding: 50px 0;
            text-align: center;
            margin-bottom: 50px;
        }

        .header-section h1 {
            font-size: 3em;
            font-weight: bold;
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
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
        }

        .about-section {
            padding: 80px 0;
            background-color: #ffffff;
        }

        .about-title {
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 30px;
            color: #007bff;
        }

        .about-text {
            font-size: 1.1em;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        .mission-vision {
            margin-bottom: 40px;
        }

        .mission-vision h3 {
            color: #007bff;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .team-member {
            margin-bottom: 30px;
            text-align: center;
        }

        .team-member img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .team-member h4 {
            font-weight: bold;
            color: #343a40;
            margin-bottom: 5px;
        }

        .team-member p {
            color: #6c757d;
        }

        .container {
            max-width: 960px;
        }
    </style>
</head>
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
                            <a class="nav-link mx-3" aria-current="page" href="/">Accueil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active mx-3" href="/a-propos">À propos de nous</a>
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
</head>
<body>
    <section class="header-section">
        <div class="container">
            <h1>A propos de nous</h1>
            <p>Des solutions de transport et de logistique adaptées à vos besoins.</p>
        </div>
    </section>
    <section class="about-page">
        <div class="container">
            <section class="about-intro">
                {{-- <h2 class="about-title text-center">À Propos de Nous</h2> --}}
                <h4 class="about-text" style="color: green; font-weight: bold;">
                    Afrique Fret Transit Import Export est une entreprise
                    spécialisée dans le fret maritime et aérien, avec 10 ans
                    d’expérience dans le domaine du transport
                    international. Nous offrons des solutions logistiques
                    sur mesure pour répondre aux besoins de nos clients en
                    matière d’import et d’export.
                </h4>

            </section>
    
            <section class="mission-vision-section">
                <div class="row mission-vision">
                    <div class="col-md-6">
                        <h3>NOTRE EXPERTISE COUVRE LES AXES SUIVANTS :</h3>
                        <ul>
                            <li>Fret maritime et aérien de l’Europe vers la Côte d’Ivoire et l’Afrique de l’Ouest.</li>
                            <li>Fret maritime et aérien de la Chine vers la Côte d’Ivoire.</li>
                            <li>Fret maritime et aérien de la Chine vers la France.</li>
                        </ul>

                    </div>
                    <div class="col-md-6">
                        <h3>NOS ATOUTS</h3>
                    <ul style="list-style-type: disc; padding-left: 20px;">
                        <li>✅ <strong style="color: orange;">Expérience :</strong> 10 ans d’expertise dans le fret international.</li>
                        <li>✅ <strong style="color: orange;">Réseau :</strong> Partenaires fiables en Europe, en Chine et en Afrique de l’Ouest.</li>
                        <li>✅ <strong style="color: orange;">Flexibilité :</strong> Solutions adaptées aux besoins spécifiques de chaque client.</li>
                        <li>✅ <strong style="color: orange;">Transparence :</strong> Suivi en temps réel des expéditions.</li>
                    </ul>

                    </div>
                </div>
            </section>
            {{-- <section class="mission-vision-section">
                <div class="row mission-vision">
                    <div class="col-md-6">
                        <h3><strong>NOS AGENCES</strong></h3>
                        <ul class="services-list">
                            <li>
                                <span class="bullet">🇫🇷</span> <strong style="color: orange;">FRANCE</strong>
                                <ul class="service-details">
                                   <li><i class="fas fa-map-marker-alt" style="color: orange;"></i> 7, avenue Louis Blériot 93120 La Courneuve</li>
                                   <li><i class="fas fa-phone-alt" style="color: orange;"></i></i> <strong >Contact :</strong> +33 1 86 78 69 67</li>
                                   <li><i class="fas fa-phone-alt" style="color: orange;"></i></i> <strong >Contact :</strong> +33 7 66 78 54 61</li>
                                   <li><i class="fas fa-phone-alt" style="color: orange;"></i></i> <strong >Contact :</strong> +33 6 52 98 35 19</li>
                                </ul>
                            </li>
                            <li>
                                <span class="bullet">🇨🇳</span> <strong style="color: orange;">Chine</strong>
                                <ul class="service-details">
                                     <li><i class="fas fa-map-marker-alt" style="color: orange;"></i> <strong style="color: orange;">Maritime :</strong> ⼴东省佛⼭市南海区⾥⽔镇河塱沙路D2仓。</li>
                                     <li><i class="fas fa-map-marker-alt" style="color: orange;"></i> <strong style="color: orange;">Aérien :</strong> ⼴州市环市中路205号恒⽣⼤厦B座室918</li>
                                     <li><i class="fas fa-phone-alt" style="color: orange;"></i> <strong >Contact :</strong> +86 13 67 89 15 049</li>
                                </ul>
                            </li>
                            <li>
                                <span class="bullet">🇨🇮</span> <strong style="color: orange;">Côte d'Ivoire, Abidjan</strong>

                               <ul class="service-details">
                                     <li><i class="fas fa-map-marker-alt" style="color: orange;"></i> <strong style="color: orange;">Maritime :</strong> CARREFOUR ANGRE</li>
                                     <li><i class="fas fa-phone-alt" style="color: orange;"></i> <strong >Contact :</strong> +86 13 67 89 15 049</li>
                                     <li><i class="fas fa-map-marker-alt" style="color: orange;"></i> <strong style="color: orange;">Aérien :</strong>Carrefour NELSON MANDELA ANGRE 8eme tranche</li>
                                     <li><i class="fas fa-phone-alt" style="color: orange;"></i> <strong >Contact :</strong>+225 758069896</li>
                                </ul>
                            </li>

                            <li>
                                <span class="bullet">🇨🇮</span> <strong style="color: orange;">Côte d'Ivoire, San-pédro</strong>

                               <ul class="service-details">
                                     <li><i class="fas fa-map-marker-alt" style="color: orange;"></i> San Pedro au feu de la petite mairie coté voie pavée (UTE )</li>
                                     <li><i class="fas fa-phone-alt" style="color: orange;"></i> <strong >Contact :</strong> +225 27 33 74 95 19 / +225 74 940 74 02</li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h3><strong>NOS SERVICES</strong></h3>
                        <ul class="services-list">
                            <li>
                                <span class="bullet" >🔘</span> <strong style="color: orange;">FRET MARITIME</strong>
                                <ul class="service-details">
                                    <li>Transport de conteneurs (20’, 40’, 40’ HQ)</li>
                                    <li>Groupage (LCL) et plein chargement (FCL)</li>
                                    <li>Suivi personnalisé et sécurisé des marchandises</li>
                                </ul>
                            </li>
                            <li>
                                <span class="bullet" style="color: orange;">🔘</span> <strong style="color: orange;">FRET AÉRIEN</strong>
                                <ul class="service-details">
                                    <li>Livraison rapide et sécurisée pour les envois urgents</li>
                                    <li>Gestion des formalités douanières</li>
                                </ul>
                            </li>
                            <li>
                                <span class="bullet" style="color: orange;">🔘</span> <strong style="color: orange;">SERVICES COMPLÉMENTAIRES</strong>
                                <ul class="service-details">
                                    <li>Dédouanement et conseils en logistique</li>
                                    <li>Stockage et distribution locale</li>
                                    <li>Assurance des marchandises</li>
                                </ul>
                            </li>
                        </ul>
                    </div>
            </section> --}}
                <section class="mission-vision-section">
                <div class="row mission-vision">
                    <div class="col-md-6">
                        <h3>ZONES D’INTERVENTION :</h3>
                        <ul>
                        <li>✅ <strong style="color: orange;">Europe : </strong>France, Belgique, Allemagne, etc.</li>
                        <li>✅ <strong style="color: orange;">Asie : </strong>Chine (principalement)</li>
                        <li>✅ <strong style="color: orange;">Afrique : </strong>Côte d’Ivoire, Sénégal, Mali, Burkina Faso, etc.</li>
                        </ul>

                    </div>
                    <div class="col-md-6">
                        <h3>POURQUOI NOUS CHOISIR ?</h3>
                    <ul style="list-style-type: disc; padding-left: 20px;">
                        <li>✅ <strong style="color: orange;">Compétitivité :</strong>  Tarifs attractifs et sur mesure.</li>
                        <li>✅ <strong style="color: orange;">Expertise :</strong> Connaissance approfondie des marchés européens, africains et asiatiques</li>
                    </ul>

                    </div>
                </div>
            </section>
            <section class="team-section">
                <h2 class="about-title text-center">Notre Équipe</h2>
    
                <div class="row">
                    <div class="col-md-4 team-member">
                        <img src="https://via.placeholder.com/150" alt="Membre de l'équipe">
                        <h4>M. Sylla  Adama</h4>
                        <p style="color: orange;">Directeur Général</p>
                    </div>
                    <div class="col-md-4 team-member">
                        <img src="https://via.placeholder.com/150" alt="Membre de l'équipe">
                        <h4>M.Sylla Ousmane</h4>
                        <p style="color: orange;">Responsable Logistique</p>
                    </div>
                    <div class="col-md-4 team-member">
                        <img src="https://via.placeholder.com/150" alt="Membre de l'équipe">
                        <h4>M. Bakary</h4>
                        <p style="color: orange;">Responsable Commercial</p>
                    </div>
                </div>
            </section>

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
            © {{ date('Y') }} AFT IMPORT EXPORT. Tous droits réservés.
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