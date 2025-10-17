<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Colis Déchargés</title>
    <style>
        /* ------------------- Global ------------------- */
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;  /* texte principal agrandi */
            color: #333;
            margin: 0;
            padding: 15px;
        }

        .container {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin: 0 auto;
        }

        /* ------------------- Header ------------------- */
        .header {
            text-align: center;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 22px;  /* agrandi */
            margin: 5px 0;
        }

        .header h2 {
            font-size: 18px;  /* agrandi */
            color: #667eea;
            margin: 0;
        }

        .header .subtitle {
            color: #7f8c8d;
            font-size: 11px;  /* agrandi */
            margin-top: 3px;
        }

        /* ------------------- Filtres ------------------- */
        .filtres-info {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 6px;
            margin-bottom: 12px;
            border-radius: 4px;
            font-size: 10px; /* agrandi */
        }

        /* ------------------- Statistiques ------------------- */
        .stats-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 15px;
            gap: 8px;
        }

        .stat-card {
            flex: 1;
            min-width: 100px;
            padding: 8px;
            border-radius: 6px;
            text-align: center;
            color: white;
            font-weight: bold;
        }

        .stat-total { background: #667eea; }
        .stat-paye { background: #27ae60; }
        .stat-reste { background: #e74c3c; }
        .stat-count { background: #f39c12; }

        .stat-number {
            font-size: 13px; /* agrandi */
            font-weight: bold;
        }

        .stat-label {
            font-size: 9px;  /* agrandi */
            opacity: 0.9;
        }

        /* ------------------- Tableau ------------------- */
        .table-container {
            margin-top: 10px;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 10px; /* agrandi */
        }

        thead {
            background: #34495e;
            color: white;
        }

        th, td {
            padding: 4px 3px; /* un peu plus confortable */
            border: 1px solid #ddd;
            word-wrap: break-word;
            vertical-align: middle;
        }

        th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 9px;  /* agrandi */
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }

        .montant { white-space: nowrap; }
        .montant-total { color: #2c3e50; }
        .montant-paye { color: #27ae60; }
        .montant-reste { color: #e74c3c; }

        .status-badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 8px; /* agrandi */
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
            min-width: 40px;
            text-align: center;
        }

        .status-paye { background: #d4edda; color: #155724; }
        .status-partiel { background: #fff3cd; color: #856404; }
        .status-impaye { background: #f8d7da; color: #721c24; }

        tbody tr { page-break-inside: avoid; }

        /* ------------------- Totaux ------------------- */
        .totaux-row {
            background: #2c3e50 !important;
            color: white;
            font-weight: bold;
        }

        .totaux-row td {
            border: 1px solid #1a252f;
        }

        /* ------------------- Footer ------------------- */
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 9px;  /* agrandi */
            color: #7f8c8d;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }

        /* ------------------- Largeurs des colonnes ------------------- */
        .col-ref { width: 60px; }
        .col-produit { width: 50px; }
        .col-nb { width: 30px; }
        .col-montant { width: 45px; }
        .col-statut { width: 40px; }
        .col-expediteur { width: 60px; }
        .col-tel { width: 40px; }
        .col-destinataire { width: 60px; }
        .col-etat { width: 40px; }
        .col-date { width: 40px; }

    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <h2 style="color: #667eea; margin: 0;">DS TRANSLOG Angré 8ème Tranche</h2>
            <h1>RAPPORT DES COLIS DÉCHARGÉS</h1>
            <div class="subtitle">
                Généré le {{ $dateGeneration }}
            </div>
        </div>

        <!-- Tableau des colis -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Produit</th>
                        <th class="text-center">Nb. Colis</th>
                        <th class="text-right">Montant Total</th>
                        <th class="text-right">Montant Payé</th>
                        <th class="text-right">Reste à Payer</th>
                        <th>Statut Paiement</th>
                        <th>Expéditeur</th>
                        <th>Tél. Exp</th>
                        <th>Destinataire</th>
                        <th>Tél. Dest.</th>
                        <th>État</th>
                        <th>Date</th>
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
                        <td>
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
                        <td>{{ $item['etat'] }}</td>
                        <td>{{ $item['created_at'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="text-center">Aucun colis déchargé trouvé</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pied de page -->
        <div class="footer">
        </div>
    </div>
</body>
</html>