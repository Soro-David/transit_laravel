<!DOCTYPE html>
<html>
<head>
    <title>Confirmation de validation de votre dossier</title>
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
            max-width: 650px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #2c3e50;
            padding: 25px;
            text-align: center;
            color: #ffffff;
        }
        .logo-container {
            text-align: center;
            padding: 20px 0;
            background-color: #ffffff;
        }
        .logo {
            max-width: 180px;
            height: auto;
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
            border-top: 1px solid #eeeeee;
        }
        h2 {
            color: #2c3e50;
            margin-top: 0;
            font-size: 24px;
        }
        h3 {
            color: #3498db;
            border-bottom: 2px solid #f1f1f1;
            padding-bottom: 10px;
            margin-top: 25px;
        }
        .details-box {
            background-color: #f9f9f9;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 25px;
            border-left: 4px solid #3498db;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 14px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        th {
            background-color: #f2f8ff;
            font-weight: 600;
            color: #2c3e50;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .highlight {
            color: #e74c3c;
            font-weight: bold;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-paid {
            background-color: #e7f6e9;
            color: #2ecc71;
        }
        .status-pending {
            background-color: #fef5e6;
            color: #f39c12;
        }
        .payment-summary {
            background-color: #f9f9f9;
            border-radius: 6px;
            padding: 20px;
            margin-top: 20px;
        }
        .payment-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eeeeee;
        }
        .payment-item:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 16px;
            color: #e74c3c;
        }
        .signature {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eeeeee;
        }
        .alert-info {
            background-color: #e8f4fd;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Confirmation de Validation de Dossier</h2>
        </div>
        
        <div class="logo-container">
            <img class="logo" src="{{ $message->embed(public_path('images/LOGOAFT.png')) }}" alt="AFT Louis Blériot Logo">
        </div>
        
        <div class="content">
            <p>Cher/Chère <strong>{{ $expediteur_nom }}</strong>,</p>
            <p>Nous avons le plaisir de vous confirmer que votre dossier référencé <strong class="highlight">{{ $reference_colis }}</strong> a été validé avec succès.</p>
            
            @if($paiement->statut_paiement != 'payé')
                <div class="alert-info">
                    <p>Vos colis sont en cours de préparation. Pour finaliser l'expédition, veuillez vous assurer que le paiement complet soit effectué.</p>
                </div>
            @else
                <div class="alert-info">
                    <p>Le paiement a été intégralement reçu et vos colis sont en cours de préparation pour l'expédition.</p>
                </div>
            @endif

            <div class="details-box">
                <h3>Informations sur l'expédition</h3>
                <p><strong>Référence Principale:</strong> <span class="highlight">{{ $reference_colis }}</span></p>
                <p><strong>Agence d'expédition:</strong> {{ $agence_expedition }}</p>
                <p><strong>Agence de destination:</strong> {{ $agence_destination }}</p>
                <p><strong>Mode de transit:</strong> {{ $mode_transit }}</p>
            </div>

            <h3>Détails des Colis ({{ $colisDetails->count() }} au total)</h3>
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Description</th>
                        <th class="text-right">Prix</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($colisDetails as $colis)
                    <tr>
                        <td>{{ $colis->type_colis ?? 'N/A' }}</td>
                        <td>{{ $colis->description_colis ?? 'N/A' }}</td>
                        <td class="text-right">{{ number_format($colis->prix_transit_colis, 0, ',', ' ') }} F CFA</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="payment-summary">
                <h3>Récapitulatif du Paiement</h3>
                <div class="payment-item">
                    <span>Prix Total:</span>
                    <span>{{ number_format($paiement->montant, 0, ',', ' ') }} F CFA</span>
                </div>
                <div class="payment-item">
                    <span>Montant Payé:</span>
                    <span>{{ number_format($paiement->montant_paye, 0, ',', ' ') }} F CFA</span>
                </div>
                <div class="payment-item">
                    <span>Reste à Payer:</span>
                    <span>{{ number_format($paiement->montant - $paiement->montant_paye, 0, ',', ' ') }} F CFA</span>
                </div>
                <div class="payment-item">
                    <span>Statut du Paiement:</span>
                    <span class="status-badge {{ $paiement->statut_paiement == 'payé' ? 'status-paid' : 'status-pending' }}">
                        {{ ucfirst($paiement->statut_paiement) }}
                    </span>
                </div>
            </div>

            <div class="signature">
                <p>Merci de votre confiance.</p>
                <p>Cordialement,<br><strong>L'équipe AFT Louis Blériot</strong></p>
            </div>
        </div>
        
        <div class="footer">
            <p>© 2023 AFT Louis Blériot. Tous droits réservés.</p>
            <p>Si vous avez des questions, contactez-nous à contact@aft-app.com</p>
        </div>
    </div>
</body>
</html>