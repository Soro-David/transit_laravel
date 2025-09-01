<!DOCTYPE html>
<html>
<head>
    <title>Confirmation de validation de votre dossier</title>
    <style>
        /* ... votre CSS reste inchangé ... */
        body { font-family: Arial, sans-serif; color: #333; }
        .container { width: 80%; margin: 0 auto; padding: 20px; border: 1px solid #ddd; }
        .logo { text-align: center; margin-bottom: 20px; }
        .logo img { max-width: 150px; }
        .details { margin-bottom: 20px; padding: 10px; border: 1px solid #eee; background-color: #f9f9f9; }
        table { width:100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .footer { margin-top: 20px; text-align: center; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="{{ $message->embed(public_path('images/LOGOAFT.png')) }}" alt="AFT Louis Blériot Logo">
        </div>
        <div class="message">
            <p>Cher/Chère {{ $expediteur_nom }},</p>
            <p>Nous avons le plaisir de vous confirmer que votre dossier référencé <strong>{{ $reference_colis }}</strong> a été validé avec succès.</p>
            @if($paiement->statut_paiement != 'payé')
                <p>Vos colis sont en cours de préparation. Pour finaliser l'expédition, veuillez vous assurer que le paiement complet soit effectué.</p>
            @else
                <p>Le paiement a été reçu et vos colis sont en cours de préparation pour l'expédition.</p>
            @endif
        </div>

        <div class="details">
            <h3>Récapitulatif du Dossier</h3>
            <p><strong>Référence Principale:</strong> {{ $reference_colis }}</p>
            <p><strong>Agence d'expédition:</strong> {{ $agence_expedition }}</p>
            <p><strong>Agence de destination:</strong> {{ $agence_destination }}</p>
            <p><strong>Mode de transit:</strong> {{ $mode_transit }}</p>
            
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

            <h3>Récapitulatif du Paiement</h3>
            <p><strong>Prix Total:</strong> {{ number_format($paiement->montant, 0, ',', ' ') }} F CFA</p>
            <p><strong>Montant Payé:</strong> {{ number_format($paiement->montant_paye, 0, ',', ' ') }} F CFA</p>
            <p><strong>Reste à Payer:</strong> {{ number_format($paiement->montant - $paiement->montant_paye, 0, ',', ' ') }} F CFA</p>
            <p><strong>Statut du Paiement:</strong> {{ ucfirst($paiement->statut_paiement) }}</p>
        </div>

        <div class="footer">
            <p>Merci de votre confiance.</p>
            <p>Cordialement,<br>L'équipe AFT Louis Blériot</p>
        </div>
    </div>
</body>
</html>