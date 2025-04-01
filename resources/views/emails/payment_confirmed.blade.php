<!DOCTYPE html>
<html>
<head>
    <title>Confirmation de Paiement de votre Colis</title>
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
            <p>Nous vous confirmons que votre paiement pour le colis référencé <strong>{{ $reference_colis }}</strong> a été reçu avec succès.</p>
            <p>Votre colis est maintenant validé et en préparation pour l'expédition.</p>
        </div>

        <div class="details">
            <p><strong>Référence du Colis:</strong> {{ $reference_colis }}</p>
            <p><strong>Mode de Paiement:</strong> {{ $methode_paiement ?? 'N/A' }}</p>
            <p><strong>Montant Payé:</strong> {{ $montant_paye ?? 'N/A' }} CFA</p>
            <p><strong>Date de Paiement:</strong> {{ $date_validation ?? 'N/A' }}</p>
        </div>


        <div class="footer">
            <p>Merci de votre confiance.</p>
            <p>Cordialement,<br>L'équipe AFT Louis Blériot</p>
        </div>
    </div>
</body>
</html>