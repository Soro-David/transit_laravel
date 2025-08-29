<!DOCTYPE html>
<html>
<head>
    <title>Nouveau Devis Créé</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            color: #333; 
            margin: 0; 
            padding: 0; 
            background-color: #f4f4f4;
        }
        .container { 
            width: 100%; 
            max-width: 600px; 
            margin: 0 auto; 
            background-color: #ffffff; 
            padding: 20px; 
            border: 1px solid #ddd; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #0056b3;
            text-align: center;
            margin-bottom: 25px;
        }
        h2 {
            color: #0056b3;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        h3 {
            color: #0056b3;
            margin-top: 15px;
            margin-bottom: 8px;
        }
        p { 
            margin-bottom: 10px; 
            line-height: 1.6;
        }
        ul {
            list-style-type: none;
            padding: 0;
            margin-bottom: 20px;
        }
        ul li {
            background-color: #f9f9f9;
            margin-bottom: 5px;
            padding: 8px 10px;
            border-left: 3px solid #0056b3;
        }
        .footer { 
            margin-top: 30px; 
            text-align: center; 
            color: #777; 
            font-size: 14px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Un nouveau devis a été créé !</h1>
        <p>Bonjour,</p>
        <p>Un nouveau devis a été enregistré avec la référence principale : <strong>{{ $emailData['reference_colis_principale'] }}</strong>.</p>

        <h2>Détails de l'Expéditeur:</h2>
        <ul>
            <li><strong>Nom:</strong> {{ $emailData['expediteur']['nom'] ?? 'N/A' }} {{ $emailData['expediteur']['prenom'] ?? 'N/A' }}</li>
            <li><strong>Email:</strong> {{ $emailData['expediteur']['email'] ?? 'N/A' }}</li>
            <li><strong>Téléphone:</strong> {{ $emailData['expediteur']['tel'] ?? 'N/A' }}</li>
            <li><strong>Agence d'Expédition:</strong> {{ $emailData['expediteur']['agence'] ?? 'N/A' }}</li>
        </ul>

        <h2>Détails du Destinataire:</h2>
        <ul>
            <li><strong>Nom:</strong> {{ $emailData['destinataire']['nom'] ?? 'N/A' }} {{ $emailData['destinataire']['prenom'] ?? 'N/A' }}</li>
            <li><strong>Email:</strong> {{ $emailData['destinataire']['email'] ?? 'N/A' }}</li>
            <li><strong>Téléphone:</strong> {{ $emailData['destinataire']['tel'] ?? 'N/A' }}</li>
            <li><strong>Agence de Destination:</strong> {{ $emailData['destinataire']['agence'] ?? 'N/A' }}</li>
        </ul>

        <p>Nombre total de colis créés pour ce devis : <strong>{{ $emailData['nombre_colis'] }}</strong>.</p>

        @if(isset($emailData['premier_colis']))
            <h3>Détails du premier colis:</h3>
            <ul>
                <li><strong>Service:</strong> {{ $emailData['premier_colis']['service'] ?? 'N/A' }}</li>
                <li><strong>Prix Transit:</strong> {{ $emailData['premier_colis']['prix_transit_colis'] ?? 'N/A' }} F CFA</li>
                <li><strong>Poids:</strong> {{ $emailData['premier_colis']['poids_colis'] ?? 'N/A' }} kg</li>
                <li><strong>Description:</strong> {{ $emailData['premier_colis']['description_colis'] ?? 'N/A' }}</li>
            </ul>
        @endif

        <p>Veuillez consulter l'application pour plus de détails.</p>
        
        <div class="footer">
            <p>Cordialement,</p>
            <p>L'équipe {{ config('app.name') }}</p>
            <p><small>&copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</small></p>
        </div>
    </div>
</body>
</html>