<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Services - AFT Import Export</title>
    <!-- Liens CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts Poppins pour un look moderne -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* ===================================== */
        /*          Styles Généraux              */
        /* ===================================== */
        body {
            font-family: 'Poppins', sans-serif; /* Utilisation d'une police plus moderne */
            margin: 0;
            padding: 0;
            background-color: #f4f7f6; /* Fond légèrement plus doux */
            color: #333;
            line-height: 1.6;
            overflow-x: hidden; /* Empêche le défilement horizontal indésirable */
        }

                header {
            color: white;
            padding: 1rem 0;
        }
        .container {
            max-width: 1200px; /* Largeur max du contenu */
        }

        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
            color: #2c3e50;
        }

        h2 {
            color: #28a745 !important; /* Vert pour les titres de section */
            font-weight: bold;
            text-align: center;
        }

        h4 {
            color: orange !important; /* Orange pour certains sous-titres */
            font-weight: bold;
            text-align: center;
        }

        p {
            font-size: 1.05rem;
            line-height: 1.7;
            color: #ffffff !important;
        }

        /* ===================================== */
        /*           Header Section              */
        /* ===================================== */
        .navbar {
            background-color: #0a0a0ae1; /* Couleur sombre pour la barre de navigation */
            padding: 1rem 0;
        }

        .navbar-brand img {
            max-height: 60px; /* Taille du logo */
            transition: transform 0.3s ease;
        }
        .navbar-brand img:hover {
            transform: scale(1.05);
        }

        .navbar-nav .nav-link {
            color: #fff !important;
            font-weight: 500;
            padding: 0.75rem 1.2rem;
            transition: color 0.3s ease, background-color 0.3s ease, border-bottom 0.3s ease;
            border-bottom: 3px solid transparent;
        }

        .navbar-nav .nav-link:hover {
            color: #ffc107 !important; /* Jaune/orange clair au survol */
            border-bottom: 3px solid #ffc107;
        }

        .navbar-nav .nav-link {
            color: white !important;
            padding: 0.5rem 1rem !important; /* Assure une zone de contact suffisante */
            font-weight: bold;
            text-align: center;
        }
        .navbar-nav .nav-link.active {
            color: #ffc107 !important;
            border-bottom: 3px solid #ffc107;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 4px 4px 0 0;
        }

        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.2);
        }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Styles spécifiques pour le header des services */
        .header-section {
            background-image: url('{{ asset('images/aft.jpg') }}'); /* Utilisation de l'image de fond */
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
        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
            color: #faa200;
        }

        p {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
            color: #21c400;
        }


        /* ===================================== */
        /*       Section des Services Clés       */
        /* ===================================== */
        .services-main {
            padding: 4rem 0;
            background-color: #fff;
        }

        .main-title {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 3.5rem;
            color: #2c3e50; /* Couleur pour le titre principal */
            text-transform: uppercase;
            letter-spacing: 1.5px;
            position: relative;
            padding-bottom: 15px;
        }
        
        .main-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, #28a745, orange); /* Dégradé pour le soulignement */
            border-radius: 2px;
        }
        
        .service-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            height: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
            border-top: 5px solid transparent;
            border-image: linear-gradient(90deg, #28a745, orange) 1;
        }
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .service-card .icon-wrapper {
            background: linear-gradient(45deg, #28a745, #8bc34a); /* Dégradé vert */
            color: white;
            border-radius: 50%;
            width: 80px;
            height: 80px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2.5rem;
            margin: 0 auto 1.5rem auto;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
        .service-card.orange .icon-wrapper {
            background: linear-gradient(45deg, #ff7b00, #f39c12); /* Dégradé orange */
            box-shadow: 0 4px 15px rgba(255, 123, 0, 0.3);
        }

        .service-card h3 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #333;
        }

        .service-card p {
            font-size: 1rem;
            color: #666;
        }

        /* ===================================== */
        /*   Section des Services Complémentaires   */
        /* ===================================== */
        .complementary-services-section {
            padding: 4rem 0;
            background-color: #f8f9fa;
        }

        .complementary-services-section h2 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 3.5rem;
            color: #2c3e50;
            position: relative;
            padding-bottom: 15px;
        }
        
        .complementary-services-section h2:after {
            content: '';
            display: block;
            width: 90px;
            height: 4px;
            background: linear-gradient(90deg, #ff7b00, #f39c12);
            margin: 15px auto 0;
            border-radius: 2px;
        }

        /* Styles pour les cartes dans le carousel (ou grille) */
        .service-detail-card {
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            display: flex;
            flex-direction: column;
            border-top: 6px solid transparent; /* Bordure au-dessus */
            border-image: linear-gradient(90deg, #ff7b00, #f39c12) 1;
            height: 100%; /* S'assurer que les cartes ont la même hauteur dans une grille */
        }
        
        .service-detail-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.15);
        }
        
        .service-detail-card .icon-wrapper {
            width: 70px;
            height: 70px;
            background: linear-gradient(45deg, #ffcc80, #ffb347); /* Dégradé doux pour l'icône */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            box-shadow: 0 3px 10px rgba(255, 165, 0, 0.2);
            margin-left: auto;
            margin-right: auto;
        }
        
        .service-detail-card .icon-wrapper i {
            font-size: 2.2rem;
            color: #fff;
        }
        
        .service-detail-card h4 {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        
        .service-detail-card .service-description {
            color: #666;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        .service-features, .service-advantages {
            margin-bottom: 1rem;
        }
        
        .service-features h5, .service-advantages h5 {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.8rem;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
        }
        
        .service-features h5:before {
            content: '\f0a9'; /* Icône de main droite */
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            margin-right: 0.8rem;
            color: #ff7b00;
            font-size: 1.1em;
        }
        .service-advantages h5:before {
            content: '\f058'; /* Icône de coche cerclée */
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            margin-right: 0.8rem;
            color: #28a745; /* Vert pour les avantages */
            font-size: 1.25em;
        }
        
        .features-list, .advantages-list {
            list-style-type: none;
            padding-left: 0;
            margin-bottom: 0;
        }
        
        .features-list li, .advantages-list li {
            position: relative;
            padding-left: 2rem;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: #555;
        }
        
        .features-list li:before {
            content: '\f00c'; /* Icône de coche */
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            left: 0;
            color: #28a745;
        }
        
        .advantages-list li:before {
            content: '\f05a'; /* Icône d'information cerclée */
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            left: 0;
            color: #17a2b8; /* Bleu pour les avantages (différent de feature) */
        }
        
        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 1rem 0;
        }
        /* ===================================== */
        /*              Footer Section           */
        /* ===================================== */
        .footer-dark { 
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 60px 0 30px 0;
            border-top: 5px solid #ff7b00;
        }
        .footer-dark h3 { 
            font-size: 1.4rem; 
            margin-bottom: 25px; 
            color: #ff7b00;
            font-weight: 700;
        }
        .footer-dark ul { 
            list-style: none; 
            padding: 0; 
            margin-bottom: 20px;
        }
        .footer-dark ul li {
            margin-bottom: 10px;
        }
        .footer-dark ul li a { 
            color: #bdc3c7; 
            text-decoration: none; 
            transition: color 0.3s ease, padding-left 0.3s ease; 
            font-size: 0.95rem;
        }
        .footer-dark ul li a:hover { 
            color: #ff7b00;
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
        .footer-dark .contact-info p, .footer-dark .contact-info h4, .footer-dark .contact-info ul li {
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }
        .footer-dark .contact-info i {
            margin-right: 8px;
            color: #ff7b00;
        }
        .footer-dark .input-group input {
            background-color: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
        }
        .footer-dark .input-group input::placeholder {
            color: #ccc;
        }
        .footer-dark .input-group .btn {
            background-color: #ff7b00;
            border-color: #ff7b00;
            color: #fff;
            transition: background-color 0.3s ease;
        }
        .footer-dark .input-group .btn:hover {
            background-color: #e66e00;
            border-color: #e66e00;
        }

        /* ===================================== */
        /*         Responsive Adjustments        */
        /* ===================================== */
        @media (max-width: 1200px) {
            .header-section h1 { font-size: 3rem; }
        }

        @media (max-width: 991px) {
            .navbar-brand img { max-height: 50px; }
            .header-section { padding: 3rem 0; min-height: 250px; }
            .header-section h1 { font-size: 2.5rem; }
            .header-section p { font-size: 1rem; }

            .main-title { font-size: 2.2rem; margin-bottom: 2.5rem; }
            .service-card { padding: 2rem; }
            .service-card .icon-wrapper { width: 70px; height: 70px; font-size: 2rem; }
            .service-card h3 { font-size: 1.6rem; }

            .complementary-services-section h2 { font-size: 1.8rem; margin-bottom: 2.5rem; }
            .service-detail-card { padding: 2rem; }
            .service-detail-card .icon-wrapper { width: 60px; height: 60px; font-size: 1.8rem; }
            .service-detail-card h4 { font-size: 1.3rem; }
            .service-detail-card .service-description { font-size: 0.9rem; }
            .features-list li, .advantages-list li { font-size: 0.85rem; }

            .footer-dark h3 { font-size: 1.2rem; }
            .footer-dark ul li a { font-size: 0.85rem; }
            .footer-dark .social a { font-size: 1.5rem; }
        }
        
        @media (max-width: 767px) {
            .container { padding-left: 15px; padding-right: 15px; }
            .header-section h1 { font-size: 2rem; }
            .header-section p { font-size: 0.9rem; }

            .main-title { font-size: 1.8rem; }
            .service-card { margin-bottom: 1.5rem; }

            .complementary-services-section h2 { font-size: 1.6rem; }
            .service-detail-card { margin-bottom: 1.5rem; }
            .footer-dark .col-md-3 { margin-bottom: 30px; }
            .footer-dark .social { text-align: center; }
        }

        /* Styles spécifiques pour les icônes colorées dans les cartes de services complémentaires */
        .complementary-services-section .card .icon-wrapper .fa-globe {
            color: #28a745;
        }
        .complementary-services-section .card .icon-wrapper .fa-truck-moving {
            color: #ff7b00;
        }
        .complementary-services-section .card .icon-wrapper .fa-ship {
            color: #28a745;
        }
        .complementary-services-section .card .icon-wrapper .fa-plane-departure {
            color: #ff7b00;
        }
        /* Ajustements pour que les icônes multiples soient bien alignées */
        .service-detail-card .icon-wrapper.two-icons {
            display: flex;
            gap: 10px; /* Espace entre les icônes */
        }
        .service-detail-card .icon-wrapper.two-icons i {
            font-size: 1.8rem;
        }
        .service-detail-card .icon-wrapper.two-icons .fa-truck-moving,
        .service-detail-card .icon-wrapper.two-icons .fa-ship,
        .service-detail-card .icon-wrapper.two-icons .fa-plane-departure {
            color: white; /* Les icônes individuelles doivent rester blanches dans le wrapper */
        }

        /* ==========================================================================
        1. Variables et Configuration de Base
        ========================================================================== */
        :root {
            --primary-color: orange;
            --secondary-color: #388b00; /* Vert */
            --dark-bg: #2c3e50;
            --light-bg: #f8f9fa;
            --text-color-dark: #333;
            --text-color-medium: #555;
            --text-color-light: #ecf0f1;
            --footer-link-color: #bdc3c7;
            --border-radius-base: 8px;
            --box-shadow-base: 0 2px 5px rgba(0,0,0,0.05);
            --box-shadow-hover: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        /* ==========================================================================
        2. Styles Généraux et Typographie
        ========================================================================== */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--light-bg);
            color: var(--text-color-dark);
        }

        .container {
            max-width: 100% !important; /* Permet un contrôle total du padding */
            padding-left: 15px; /* Mobile par défaut */
            padding-right: 15px; /* Mobile par défaut */
        }

        p {
            font-size: 1.1rem; /* Ajusté pour mobile */
            line-height: 1.6;
            color: var(--text-color-medium);
        }

        h1 {
            font-size: 2rem; /* Ajusté pour mobile */
            margin-bottom: 20px;
            color: var(--primary-color);
        }

        h2 {
            color: var(--secondary-color) !important;
            margin-bottom: 20px;
            font-size: 1.8rem; /* Ajusté pour mobile */
        }

        h3 {
            color: var(--primary-color) !important;
            margin-bottom: 20px;
            font-size: 1.5rem; /* Ajusté pour mobile */
        }

        ul {
            list-style-type: disc;
            padding-left: 20px;
        }

        li {
            margin-bottom: 10px;
        }

        /* Styles pour les titres spécifiques */
        .titre {
            color: var(--secondary-color); /* Vert */
            font-weight: bold;
            font-size: 28px; /* Ajusté pour mobile */
            text-shadow: -1px -1px 0 white, 1px -1px 0 white, -1px 1px 0 white, 1px 1px 0 white;
        }

        .slogan {
            color: var(--primary-color); /* Orange */
            font-weight: bold;
            font-size: 20px; /* Ajusté pour mobile */
            text-shadow: -1px -1px 0 white, 1px -1px 0 white, -1px 1px 0 white, 1px 1px 0 white;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
            font-size: 2rem;
            font-weight: bold;
            color: var(--text-color-dark);
            position: relative;
        }

        /* Utilitaires */
        .text-orange {
            color: var(--primary-color) !important;
        }

        .img-fluid {
            max-width: 100%;
            height: auto;
        }

        .rounded-image { /* Nouveau style pour les images encadrées */
            border-radius: var(--border-radius-base);
            transition: transform 0.3s ease-in-out;
        }
        .rounded-image:hover {
            transform: scale(1.02);
        }

        /* ==========================================================================
        3. Header et Navigation
        ========================================================================== */
        /* Section d'entête avec image de fond */
        .header-section {
            background-image: url('{{ asset('images/equip1.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 250px; /* Réduit pour mobile */
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            color: white;
            padding: 3rem 0; /* Ajusté pour mobile */
            margin-bottom: 1.5rem; /* Ajusté pour mobile */
        }
        /* Overlay semi-transparent */
        .header-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }
        .header-section h1, .header-section p {
            position: relative;
            z-index: 2;
        }
        .header-section p {
            font-size: 1rem; /* Texte de paragraphe plus petit sur l'entête pour mobile */
        }

        /* Barre de navigation */
        header {
            padding: 0.5rem 0; /* Padding plus petit pour le header sur mobile */
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .navbar-brand img {
            max-height: 60px; /* Taille du logo ajustée pour mobile */
        }
        .navbar-nav .nav-link {
            color: white !important;
            padding: 0.5rem 1rem !important; /* Assure une zone de contact suffisante */
            font-weight: bold;
            text-align: center;
        }
        .navbar-nav .nav-link:hover {
            color: var(--primary-color) !important;
        }
                .navbar-nav .nav-link.active {
                    color: #ffc107 !important;
                    border-bottom: 3px solid #ffc107;
                    background-color: rgba(255, 255, 255, 0.1);
                    border-radius: 4px 4px 0 0;
                }
        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.1);
        }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.5%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* ==========================================================================
        4. Carousel
        ========================================================================== */
        .carousel {
            height: 400px;
        }
        .carousel-item {
            height: 400px; /* Ajusté pour mobile */
        }
        .carousel-item img {
            object-fit: cover;
            height: 100%; /* S'assure que l'image remplit le conteneur */
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
            font-size: 0.8rem; /* Texte de légende plus petit */
        }

        /* ==========================================================================
        5. Sections Spécifiques (À Propos, Mission/Vision, Équipe)
        ========================================================================== */

        /* Section "À propos" introductive */
        .about-intro {
            width: 100%;
            margin: 0 0 2rem 0; /* Ajusté pour mobile */
            padding: 1.5rem; /* Ajusté pour mobile */
            background-color: #ffffff;
            border-radius: 0; /* Bords à bords */
            box-shadow: var(--box-shadow-base);
        }

        /* Section Mission/Vision */
        .mission-vision-section {
            margin-bottom: 2rem; /* Ajusté pour mobile */
        }
        .mission-vision-section .row > div {
            padding: 1rem; /* Padding plus petit pour les cartes */
            background-color: #ffffff;
            border-radius: var(--border-radius-base);
            box-shadow: var(--box-shadow-base);
            margin-bottom: 1rem; /* Marge plus petite entre les cartes */
        }
        .mission-vision-section h3 {
            font-weight: 700;
            margin-bottom: 1rem; /* Ajusté pour mobile */
            padding-bottom: 0.75rem;
            border-bottom: 3px solid var(--primary-color);
            display: inline-block;
        }
        .mission-vision-section h3 i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        .mission-vision-section ul {
            list-style: none;
            padding-left: 0;
        }
        .mission-vision-section ul li {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: flex-start;
            font-size: 0.95rem; /* Texte des éléments de liste plus petit */
        }
        .mission-vision-section ul li i.fas, .mission-vision-section ul li span.bullet {
            margin-right: 10px;
            color: var(--primary-color);
            font-size: 1.1em; /* Ajusté pour mobile */
        }
        .mission-vision-section ul li strong {
            color: var(--text-color-dark);
        }

        /* Section Équipe */
        .team-section {
            margin-top: 2rem; /* Ajusté pour mobile */
            text-align: center;
        }
        .team-member {
            margin-bottom: 1.5rem; /* Ajusté pour mobile */
            padding: 0.8rem; /* Ajusté pour mobile */
            /* background-color: #ffffff; */ /* Retiré si background-color n'est pas nécessaire */
            border-radius: var(--border-radius-base);
            box-shadow: var(--box-shadow-base);
        }
        .team-member img {
            width: 120px; /* Taille d'image plus petite pour les membres de l'équipe */
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 0.8rem; /* Ajusté pour mobile */
            border: 3px solid var(--primary-color);
        }
        .team-member h4 {
            color: var(--text-color-dark);
            margin-bottom: 0.5rem;
            font-size: 1.3rem;
        }
        .team-member p {
            color: var(--primary-color);
            font-weight: bold;
            font-size: 0.95rem;
        }

        /* ==========================================================================
        6. Sections de Services / Cartes d'Agence
        ========================================================================== */
        .services-list {
            list-style: none;
            padding: 0;
        }
        .services-list > li {
            margin-bottom: 2rem;
            background: white;
            border-radius: var(--border-radius-base);
            padding: 1.5rem;
            box-shadow: var(--box-shadow-hover);
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

        .service-details {
            list-style: none;
            padding-left: 0;
            margin-top: 0.8rem;
        }
        .service-details li {
            display: flex;
            align-items: flex-start;
        }
        .service-details i {
            margin-right: 10px;
            color: var(--primary-color);
            min-width: 20px;
            margin-top: 4px;
        }
        .service-title {
            margin-top: 1rem;
            font-size: 1.3rem; /* Ajusté pour mobile */
            font-weight: 700;
            color: var(--primary-color);
        }
        .service-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        /* Cartes générales (pour agence ou autres services) */
        .card-agency, .service-card {
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: var(--border-radius-base);
            overflow: hidden;
            background-color: #fff;
            box-shadow: var(--box-shadow-base);
        }
        .card-agency .card-header, .service-card .card-header {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 15px;
            font-size: 1.1rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: none;
        }
        .card-agency .card-header .country-flag {
            font-size: 1.5rem;
            margin-right: 10px;
        }
        .card-agency .card-body, .service-card .card-body {
            padding: 15px;
        }


        /* ==========================================================================
        7. Footer
        ========================================================================== */
        .footer-dark {
            background-color: var(--dark-bg); /* Couleur sombre plus profonde */
            color: var(--text-color-light); /* Texte gris clair */
            padding: 40px 0 20px 0; /* Ajusté pour mobile */
            border-top: 5px solid var(--primary-color); /* Ligne orange en haut du footer */
        }
        .footer-dark h3 {
            font-size: 1.2rem; /* Ajusté pour mobile */
            margin-bottom: 15px; /* Ajusté pour mobile */
            color: var(--primary-color); /* Titres orange */
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
            color: var(--footer-link-color);
            text-decoration: none;
            transition: color 0.3s ease;
            font-size: 0.9rem; /* Ajusté pour mobile */
        }
        .footer-dark ul li a:hover {
            color: var(--primary-color); /* Orange au survol */
            padding-left: 5px;
        }
        .footer-dark .social a {
            color: var(--text-color-light);
            font-size: 1.5rem; /* Ajusté pour mobile */
            margin: 0 8px; /* Ajusté pour mobile */
            transition: color 0.3s ease, transform 0.3s ease;
        }
        .footer-dark .social a:hover {
            color: var(--primary-color);
            transform: translateY(-3px);
        }
        .footer-dark .copyright {
            font-size: 0.85rem;
            text-align: center;
            margin-top: 20px;
        }

        /* ==========================================================================
        8. Media Queries pour les écrans plus larges (Desktop)
        ========================================================================== */
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
    </style>
</head>
<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark">
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
                        <li class="nav-item"><a class="nav-link active mx-3" href="/nos-services">Nos services</a></li>
                        <li class="nav-item"><a class="nav-link mx-3" href="/nos-agences">Nos Agences</a></li>
                        <li class="nav-item"><a class="nav-link mx-3" href="/contact">Contact</a></li>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="nav-link text-white">
                                <i class="fas fa-user me-2"></i> Se Connecter
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
            <h1>Nos solutions d’envoi ou Nos solutions d’acheminement </h1>
            <p>Découvrez l'ensemble de nos solutions de transport et de logistique intégrées, conçues pour optimiser vos flux et soutenir votre expansion globale.</p>
        </div>
    </section>
       
    <section class="services-main py-5">
    <div class="container">
        <!-- Titre section -->
        <h2 class="main-title text-center mb-5">NOS SERVICES PRINCIPAUX</h2>

        <div class="row g-4">
        <!-- FRET MARITIME INTERNATIONAL -->
        <div class="col-md-4">
            <div class="service-card h-100">
            <div class="icon-wrapper">
                <i class="fas fa-ship"></i>
            </div>
            <h3 class="service-title">Fret Maritime International</h3>
            <p>Solutions de transport maritime optimisées pour conteneurs complets (FCL) ou groupage (LCL).</p>
            <ul class="list-unstyled text-start mt-3">
                <li><i class="fas fa-check-circle text-success me-2"></i> Transport de conteneurs standards</li>
                <li><i class="fas fa-check-circle text-success me-2"></i> Services LCL et FCL</li>
                <li><i class="fas fa-check-circle text-success me-2"></i> Suivi personnalisé et sécurisé</li>
                <li><i class="fas fa-check-circle text-success me-2"></i> Optimisation des routes et des coûts</li>
            </ul>
            <div class="service-image mt-3">
                <img src="{{ asset('images/bateau_service.jpg') }}" alt="Fret maritime international - Conteneurs" class="img-fluid rounded shadow-sm">
            </div>
            </div>
        </div>

        <!-- FRET AÉRIEN EXPRESS -->
        <div class="col-md-4">
            <div class="service-card orange h-100">
            <div class="icon-wrapper">
                <i class="fas fa-plane-departure"></i>
            </div>
            <h3 class="service-title">Fret Aérien Express</h3>
            <p>Transport aérien rapide et sécurisé pour vos envois urgents à l'échelle mondiale.</p>
            <ul class="list-unstyled text-start mt-3">
                <li><i class="fas fa-check-circle text-success me-2"></i> Livraison rapide et sécurisée</li>
                <li><i class="fas fa-check-circle text-success me-2"></i> Gestion complète des formalités douanières</li>
                <li><i class="fas fa-check-circle text-success me-2"></i> Suivi en temps réel des expéditions</li>
                <li><i class="fas fa-check-circle text-success me-2"></i> Connexions mondiales</li>
            </ul>
            <div class="service-image mt-3">
                <img src="{{ asset('images/avion_service.jpg') }}" alt="Fret aérien express - Avion cargo" class="img-fluid rounded shadow-sm">
            </div>
            </div>
        </div>

        <!-- SERVICES LOGISTIQUES INTÉGRÉS -->
        <div class="col-md-4">
            <div class="service-card h-100">
            <div class="icon-wrapper">
                <i class="fas fa-boxes"></i>
            </div>
            <h3 class="service-title">Logistique Intégrée</h3>
            <p>Simplifiez votre chaîne logistique avec nos services de dédouanement et de stockage.</p>
            <ul class="list-unstyled text-start mt-3">
                <li><i class="fas fa-check-circle text-success me-2"></i> Dédouanement et conseils experts</li>
                <li><i class="fas fa-check-circle text-success me-2"></i> Solutions de stockage sécurisé</li>
                <li><i class="fas fa-check-circle text-success me-2"></i> Assurance complète des marchandises</li>
                <li><i class="fas fa-check-circle text-success me-2"></i> Optimisation de la chaîne d'approvisionnement</li>
            </ul>
            <div class="service-image mt-3">
                <img src="{{ asset('images/ac4.jpeg') }}" alt="Services logistiques intégrés - Entrepôt" class="img-fluid rounded shadow-sm">
            </div>
            </div>
        </div>
        </div>
    </div>
    </section>

    <section class="complementary-services-section py-5">
        <div class="container">
            <h2 class="text-center mb-5">Nos Services Complémentaires</h2>

            <div class="row g-4">
                <!-- Service 1: Déménagement International -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="service-detail-card">
                        <div class="icon-wrapper two-icons">
                            <i class="fas fa-truck-moving"></i>
                            <i class="fas fa-globe"></i>
                        </div>
                        <h4 class="text-center">Déménagement International</h4>
                        <p class="service-description text-center">Transfert de vos biens avec soin, de l'emballage à l'installation.</p>
                        
                        <div class="service-features">
                            <h5>Ce que nous offrons :</h5>
                            <ul class="features-list">
                                <li>Emballage professionnel de vos biens</li>
                                <li>Transport sécurisé et suivi rigoureux</li>
                                <li>Stockage temporaire si nécessaire</li>
                            </ul>
                        </div>
                        
                        <div class="service-advantages">
                            <h5>Nos avantages :</h5>
                            <ul class="advantages-list">
                                <li>Une tranquillité d'esprit garantie</li>
                                <li>Service clé en main, de bout en bout</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Service 2: Achat International -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="service-detail-card">
                        <div class="icon-wrapper">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h4 class="text-center">Achat International</h4>
                        <p class="service-description text-center">Facilitez vos achats et importations, notamment depuis la Chine vers la Côte d'Ivoire.</p>
                        
                        <div class="service-features">
                            <h5>Ce que nous offrons :</h5>
                            <ul class="features-list">
                                <li>Accompagnement dans le sourcing</li>
                                <li>Gestion des commandes et paiements</li>
                                <li>Inspection qualité</li>
                            </ul>
                        </div>
                        
                        <div class="service-advantages">
                            <h5>Nos avantages :</h5>
                            <ul class="advantages-list">
                                <li>Accès à un large éventail de fournisseurs</li>
                                <li>Processus d'achat simplifié et sécurisé</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Service 3: Service Maritime de Groupage -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="service-detail-card">
                        <div class="icon-wrapper two-icons">
                            <i class="fas fa-box-open"></i>
                            <i class="fas fa-ship"></i>
                        </div>
                        <h4 class="text-center">Service Maritime (Groupage)</h4>
                        <p class="service-description text-center">Groupage maritime professionnel entre la France et la Côte d'Ivoire.</p>
                        
                        <div class="service-features">
                            <h5>Ce que nous offrons :</h5>
                            <ul class="features-list">
                                <li>Enlèvement à domicile en France</li>
                                <li>Conteneur dédié et sécurisé</li>
                                <li>Délai de 2-3 semaines</li>
                            </ul>
                        </div>
                        
                        <div class="service-advantages">
                            <h5>Nos avantages :</h5>
                            <ul class="advantages-list">
                                <li>Économies sur les coûts de transport</li>
                                <li>Réduction des délais de livraison</li>
                                <li>Sécurité accrue des marchandises</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Service 4: Service Aérien -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card h-100 p-3 shadow-sm">
                    <div class="icon-wrapper text-center mb-3">
                        <i class="fas fa-plane-departure fa-2x"></i>
                    </div>
                    <h4 class="text-center mb-3">Service Aérien</h4>
                    <p class="text-center">Transport aérien rapide et sécurisé de vos colis</p>
                    
                    <div class="service-features mb-2">
                        <h5>Ce que nous offrons :</h5>
                        <ul class="features-list ps-3">
                        <li>Rapidité d'acheminement</li>
                        <li>Fiabilité maximale</li>
                        </ul>
                    </div>
                    
                    <div class="service-advantages">
                        <h5>Nos avantages :</h5>
                        <ul class="advantages-list ps-3">
                        <li>Livraison express</li>
                        <li>Suivi en temps réel</li>
                        <li>Service personnalisé</li>
                        <li>Couverture mondiale</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div> <!-- row -->
    </div> <!-- container -->
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Animation pour les cartes de services
            $('.service-card').each(function(i) {
                $(this).delay(i * 200).animate({
                    opacity: 1
                }, 800);
            });
        });
    </script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>