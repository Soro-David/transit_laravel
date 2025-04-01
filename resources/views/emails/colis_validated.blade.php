<!DOCTYPE html>
<html>
<head>
    <title>Confirmation de validation de votre colis</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 150px;
        }
        .message {
            margin-bottom: 20px;
        }
        .details {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #eee;
            background-color: #f9f9f9;
        }
        .steps {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .step {
            text-align: center;
        }
        .step-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #4CAF50; /* Green */
            color: white;
            line-height: 30px;
            margin: 0 auto 5px;
        }
        .step-icon.incomplete {
            background-color: #ddd; /* Grey for incomplete steps */
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
        <img src="{{ asset('images/LOGOAFT.png')}}" alt="AFT Louis Blériot Logo"> 
        </div>
        <div class="message">
            <p>Cher/Chère {{ $expediteur_nom }},</p>
            <p>Nous avons le plaisir de vous confirmer que votre demande de devis référencé <strong>{{ $reference_colis }}</strong> a été validé.</p>
            <p>Votre demande de devis a été accepté et votre colis est en cours de préparation pour l'expédition.</p>
            <p>Ainsi veuillez effectuez le paiement pour la prise en charge global et pour l'expedition du colis.</p>
        </div>

        <div class="details">
            <p><strong>Référence du Colis:</strong> {{ $reference_colis }}</p>
            <p><strong>Agence d'expédition:</strong> {{ $agence_expedition }}</p>
            <p><strong>Agence de destination:</strong> {{ $agence_destination }}</p>
            <p><strong>Mode de transit:</strong> {{ $mode_transit }}</p>
            <p><strong>Description du colis:</strong> {{ $description_colis }}</p>
            <p><strong>Prix du Colis:</strong> {{ $prix_transit_colis }} CFA</p>
        </div>

        <div class="steps">
            <div class="step">
                <div class="step-icon">✔</div>
                <div>Demande Devis Validé</div>
            </div>
            <div class="step">
                <div class="step-icon incomplete"></div>
                <div>En préparation</div>
            </div>
            <div class="step">
                <div class="step-icon incomplete"></div>
                <div>Expédié</div>
            </div>
            <div class="step">
                <div class="step-icon incomplete"></div>
                <div>En Transit</div>
            </div>
            <div class="step">
                <div class="step-icon incomplete"></div>
                <div>Livré</div>
            </div>
        </div>

        <div class="footer">
            <p>Merci de votre confiance.</p>
            <p>Cordialement,<br>L'équipe AFT Louis Blériot</p>
        </div>
    </div>
</body>
</html>