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
                .navbar-nav .nav-link.active {
            color: #fff !important;
            border-bottom: 3px solid #ff7b00; /* Soulignement orange vif pour l'actif */
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 4px 4px 0 0;
        }
        @media (max-width: 767px) { .navbar-brand img { max-height: 50px; } }

        /* --- STYLES POUR LA PAGE CONTACT --- */

        /* En-tête de la page */
        .header-section {
            background-image: url('{{ asset('images/equip4.png') }}'); /* Utilisation de l'image de fond */
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
        p {
           font-size: 1.25rem;
            line-height: 1.6;
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
            color: #faa200;
        }
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
                        <li class="nav-item"><a class="nav-link mx-3" href="/nos-agences">Nos Agences</a></li>
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
                {{-- <p>Retrouvez ici les coordonnées de toutes nos agences.</p> --}}
        </div>
    </section>

<section class="contact-content-section">
    <div class="container">
        <h2 class="section-title">Formulaire de contact             
            <div class="contact-form-image ">
                <img src="{{ asset('images/LOGOAFT.png') }}" alt="Logo de l'entreprise">
            </div>
        </h2>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        @if(session('success'))
                            <div class="alert alert-success text-center">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('contact.store') }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-md-6">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                            <div class="col-md-6">
                                <label for="contact" class="form-label">Contact</label>
                                <input type="text" class="form-control" id="contact" name="contact" required>
                            </div>
                            <div class="col-md-12">
                                <label for="email" class="form-label">Adresse Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="col-md-12">
                                <label for="sujet" class="form-label">Objet</label>
                                <input type="text" class="form-control" id="sujet" name="sujet" required>
                            </div>
                            <div class="col-md-12">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>
                            <div class="col-12 text-center mt-4">
                                <button type="submit" class="btn btn-warning px-5 py-2 fw-bold text-white">
                                    <i class="fas fa-paper-plane"></i> Envoyer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>