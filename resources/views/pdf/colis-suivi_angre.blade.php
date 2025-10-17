<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Colis Validés</title>
    <style>
    @media print {
        @page {
            size: A4 portrait; /* ou 'landscape' si tu veux paysage */
            margin: 15mm;      /* marges de la page */
        }

        body {
            margin: 0;
            padding: 0;
            font-size: 10px;
        }

        .container {
            width: 100%;
            padding: 0;
            margin: 0;
            border: none;
            border-radius: 0;
        }

        .table-container table {
            font-size: 9px;
            page-break-inside: auto;
        }

        table, th, td {
            border: 1px solid #000 !important;
        }

        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }

        tr { page-break-inside: avoid; page-break-after: auto; }

        /* Cacher les éléments non utiles à l’impression */
        .no-print { display: none; }
    }
</style>

</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <h2 style="color: #667eea; margin: 0; font-size: 16px;">DS TRANSLOG Angré 8ème Tranche</h2>
            <h1>RAPPORT DES COLIS VALIDÉS</h1>
            <div class="subtitle">
                Généré le {{ $dateGeneration }}
            </div>
        </div>

        <!-- Filtres appliqués -->
        {{-- <div class="filtres-info">
            <strong>Filtres appliqués:</strong> 
            États: {{ $filtres['etat'] }} | 
            Agence: {{ $filtres['agence_destinataire'] }}
        </div> --}}

        <!-- Statistiques -->
        {{-- <div class="stats-container">
            <div class="stat-card stat-total">
                <div class="stat-number">{{ number_format($totalMontant, 0, ',', ' ') }} </div>
                <div class="stat-label">MONTANT TOTAL</div>
            </div>
            <div class="stat-card stat-paye">
                <div class="stat-number">{{ number_format($totalPaye, 0, ',', ' ') }} </div>
                <div class="stat-label">MONTANT PAYÉ</div>
            </div>
            <div class="stat-card stat-reste">
                <div class="stat-number">{{ number_format($totalReste, 0, ',', ' ') }} </div>
                <div class="stat-label">RESTE À PAYER</div>
            </div>
            <div class="stat-card stat-count">
                <div class="stat-number">{{ $totalColis }}</div>
                <div class="stat-label">REFERENCES</div>
            </div>
            <div class="stat-card" style="background: #8e44ad;">
                <div class="stat-number">{{ $totalColisCount }}</div>
                <div class="stat-label">TOTAL COLIS</div>
            </div>
        </div> --}}

        <!-- Tableau des colis -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="col-ref">Référence</th>
                        <th class="col-produit">Produit</th>
                        <th class="col-nb text-center">Nb. Colis</th>
                        <th class="col-montant text-right">Montant Total</th>
                        <th class="col-montant text-right">Montant Payé</th>
                        <th class="col-montant text-right">Reste à Payer</th>
                        <th class="col-statut">Statut Paiement</th>
                        <th class="col-expediteur">Expéditeur</th>
                        <th class="col-tel">Tél. Exp</th>
                        <th class="col-destinataire">Destinataire</th>
                        <th class="col-tel">Tél. Dest.</th>
                        <th class="col-etat">État</th>
                        <th class="col-date">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($colis as $item)
                    <tr>
                        <td class="text-bold">{{ $item['reference_colis'] }}</td>
                        <td>{{ $item['nom_produit'] ?? 'N/A' }}</td>
                        <td class="text-center">{{ $item['nombre_de_colis'] }}</td>
                        <td class="montant montant-total text-right">{{ number_format($item['montant_total'], 0, ',', ' ') }} </td>
                        <td class="montant montant-paye text-right">{{ number_format($item['montant_paye'], 0, ',', ' ') }} </td>
                        <td class="montant montant-reste text-right">{{ number_format($item['reste_a_payer'], 0, ',', ' ') }} </td>
                        <td class="text-center">
                            @php
                                $statusClass = match($item['payment_status']) {
                                    'paye' => 'status-paye',
                                    'partiel' => 'status-partiel',
                                    default => 'status-impaye'
                                };
                                $statusText = match($item['payment_status']) {
                                    'paye' => 'Payé',
                                    'partiel' => 'Partiel',
                                    default => 'Impayé'
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                        <td>{{ $item['expediteur_nom'] }} {{ $item['expediteur_prenom'] }}</td>
                        <td>{{ $item['expediteur_tel'] ?? 'N/A' }}</td>
                        <td>{{ $item['destinataire_nom'] }} {{ $item['destinataire_prenom'] }}</td>
                        <td>{{ $item['destinataire_tel'] ?? 'N/A' }}</td>
                        <td class="text-center">{{ $item['etat'] }}</td>
                        <td class="text-center">{{ $item['created_at'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="text-center">Aucun colis validé trouvé</td>
                    </tr>
                    @endforelse
                    
                    <!-- Ligne des totaux -->
                    @if($colis->count() > 0)
                    <tr class="totaux-row">
                        <td colspan="2" class="text-bold">TOTAUX</td>
                        <td class="text-center text-bold">{{ $totalColisCount }}</td>
                        <td class="montant text-right text-bold">{{ number_format($totalMontant, 0, ',', ' ') }} </td>
                        <td class="montant text-right text-bold">{{ number_format($totalPaye, 0, ',', ' ') }} </td>
                        <td class="montant text-right text-bold">{{ number_format($totalReste, 0, ',', ' ') }} </td>
                        <td colspan="7"></td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>