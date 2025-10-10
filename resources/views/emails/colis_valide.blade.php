<!DOCTYPE html>
<html>
<head>
    <title>Confirmation de votre colis</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .header {
            background-color: #2c3e50;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .footer {
            background-color: #f5f5f5;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #666666;
        }
        h2 {
            color: #2c3e50;
            margin-top: 0;
        }
        h3 {
            color: #3498db;
            border-bottom: 2px solid #f1f1f1;
            padding-bottom: 10px;
            margin-top: 25px;
        }
        h4 {
            color: #e74c3c;
            margin-bottom: 10px;
            padding-left: 10px;
            border-left: 3px solid #e74c3c;
        }
        ul {
            padding-left: 20px;
        }
        li {
            margin-bottom: 8px;
        }
        .service-group {
            margin-bottom: 20px;
            border: 1px solid #e1e1e1;
            border-radius: 5px;
            padding: 15px;
            background-color: #f9f9f9;
        }
        .colis-detail {
            background-color: #ffffff;
            border-left: 4px solid #3498db;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 0 4px 4px 0;
        }
        .colis-detail p {
            margin: 5px 0;
            font-size: 14px;
        }
        .service-summary {
            background-color: #e8f4fc;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .highlight {
            color: #e74c3c;
            font-weight: bold;
        }
        .logo {
            color: #ffffff;
            font-size: 28px;
            font-weight: bold;
        }
        .signature {
            margin-top: 30px;
            border-top: 1px solid #eeeeee;
            padding-top: 20px;
        }
        .total-service {
            font-weight: bold;
            text-align: right;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">AFT IMPORT EXPORT</div>
        </div>
        
        <div class="content">
            <h2>Bonjour {{ $expediteur_nom }},</h2>
            
            <p>Votre dossier de colis a été créé avec succès et est maintenant pris en charge par nos services.</p>
            
            <h3>Détails de l'expédition</h3>
            <ul>
                <li><strong>Référence :</strong> <span class="highlight">{{ $reference_colis }}</span></li>
                <li><strong>Agence d'expédition :</strong> {{ $agence_expedition }}</li>
                <li><strong>Agence de destination :</strong> {{ $agence_destination }}</li>
                <li><strong>Mode de transit :</strong> {{ $mode_transit }}</li>
                <li><strong>Statut de paiement :</strong> {{ $paiement->statut_paiement }}</li>
                <li><strong>Montant total :</strong> {{ number_format($paiement->montant, 0, ',', ' ') }} {{ $paiement->devise ?? 'FCFA' }}</li>
                <li><strong>Montant payé :</strong> {{ number_format($paiement->montant_paye, 0, ',', ' ') }} {{ $paiement->devise ?? 'FCFA' }}</li>
            </ul>

            <h3>Détails des colis par service</h3>
            
            @php
                // Regrouper les colis par service
                $services = [];
                foreach($colisDetails as $colis) {
                    $service = $colis->service ?? 'Autre service';
                    if (!isset($services[$service])) {
                        $services[$service] = [
                            'colis' => [],
                            'total_poids' => 0,
                            'count' => 0
                        ];
                    }
                    $services[$service]['colis'][] = $colis;
                    $services[$service]['total_poids'] += $colis->poids_colis;
                    $services[$service]['count']++;
                }
            @endphp

            @foreach($services as $serviceName => $serviceData)
                <div class="service-group">
                    <h4>Service: {{ $serviceName }}</h4>
                    
                    <div class="service-summary">
                        Nombre de colis: {{ $serviceData['count'] }} | 
                        Poids total: {{ $serviceData['total_poids'] }} kg
                    </div>
                    
                    @foreach($serviceData['colis'] as $colis)
                        <div class="colis-detail">
                            <p><strong>Référence colis :</strong> {{ $colis->reference_colis }}</p>
                            <p><strong>Type :</strong> {{ $colis->type_colis }}</p>
                            <p><strong>Poids :</strong> {{ $colis->poids_colis }} kg</p>
                            <p><strong>Description :</strong> {{ $colis->description_colis }}</p>
                        </div>
                    @endforeach
                </div>
            @endforeach

            <p>Vous pouvez suivre l'état de votre expédition à tout moment en utilisant votre référence sur notre plateforme.</p>
            
            <div class="signature">
                <p>Merci de votre confiance !</p>
                <p>Cordialement,<br><strong>L'équipe AFT</strong></p>
            </div>
        </div>
        
        <div class="footer">
            <p>© 2023 AFT IMPORT EXPORT. Tous droits réservés.</p>
            <p>Si vous avez des questions, contactez-nous à contact@aft-app.com</p>
        </div>
    </div>
</body>
</html>