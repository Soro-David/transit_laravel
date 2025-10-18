<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Colis Validés</title>
    <style>
        /* ------------------- Configuration Globale & Impression ------------------- */
        @page {
            size: A4 portrait;
            margin: 15mm 12mm 15mm 12mm;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 13px;
            color: #2c3e50;
            margin: 0;
            background-color: #fff;
            counter-reset: page;
        }

        /* Pied de page : numérotation */
        .page-footer {
            position: fixed;
            bottom: -12mm;
            left: 0;
            right: 0;
            text-align: right;
            font-size: 11px;
            color: #7f8c8d;
        }

        .page-footer .page-number::before {
            content: "Page " counter(page);
        }

        /* ------------------- Structure principale ------------------- */
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 0 5px;
        }

        /* ------------------- En-tête ------------------- */
        .header {
            text-align: center;
            border-bottom: 3px solid #4a6cf7;
            padding-bottom: 12px;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 28px;
            color: #2c3e50;
            margin: 8px 0;
        }

        .header h2 {
            font-size: 20px;
            color: #4a6cf7;
            margin: 0;
        }

        .header .subtitle {
            color: #7f8c8d;
            font-size: 12px;
            margin-top: 6px;
        }

        /* ------------------- Tableau ------------------- */
        .table-container {
            margin-top: 15px;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        thead {
            background-color: #34495e;
            color: white;
            display: table-header-group;
        }

        th, td {
            padding: 8px 6px;
            border: 1px solid #dcdcdc;
            word-wrap: break-word;
            text-align: left;
            vertical-align: middle;
        }

        th {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }

        tbody tr:nth-child(even) {
            background-color: #f9fafc;
        }

        tbody tr:hover {
            background-color: #eef3ff;
        }

        /* Alignements */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }

        /* Champs numériques */
        .montant { 
            white-space: nowrap; 
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
        }
        .montant-total { color: #2c3e50; }
        .montant-paye { color: #27ae60; font-weight: bold; }
        .montant-reste { color: #e74c3c; font-weight: bold; }

        /* ------------------- Badges de Statut ------------------- */
        .status-badge {
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: white;
            display: inline-block;
            text-align: center;
            min-width: 55px;
        }

        .status-paye { background-color: #27ae60; }
        .status-partiel { background-color: #f39c12; }
        .status-impaye { background-color: #e74c3c; }

        /* ------------------- Ligne des Totaux ------------------- */
        .totaux-row {
            background-color: #2c3e50 !important;
            color: white;
            font-weight: bold;
            font-size: 13px;
        }

        .totaux-row td {
            padding-top: 10px;
            padding-bottom: 10px;
        }

        /* ------------------- Pied de Page (info) ------------------- */
        .footer-info {
            margin-top: 25px;
            text-align: center;
            font-size: 11px;
            color: #7f8c8d;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <!-- Pied de page -->
    <div class="page-footer">
        <span class="page-number"></span>
    </div>

    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <h2>DS TRANSLOG Carrefour Angré</h2>
            <h1>RAPPORT DES COLIS VALIDÉS</h1>
            <div class="subtitle">
                Généré le {{ $dateGeneration }}
            </div>
        </div>

        <!-- Tableau -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%;">Référence</th>
                        <th style="width: 12%;">Produit</th>
                        <th class="text-center" style="width: 6%;">Nb. Colis</th>
                        <th class="text-right" style="width: 10%;">Montant Total</th>
                        <th class="text-right" style="width: 10%;">Montant Payé</th>
                        <th class="text-right" style="width: 10%;">Reste à Payer</th>
                        <th class="text-center" style="width: 8%;">Statut</th>
                        <th style="width: 12%;">Expéditeur / Tél.</th>
                        <th style="width: 12%;">Destinataire / Tél.</th>
                        <th class="text-center" style="width: 10%;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($colis as $item)
                    <tr>
                        <td class="text-bold">{{ $item['reference_colis'] }}</td>
                        <td>{{ $item['nom_produit'] ?? 'N/A' }}</td>
                        <td class="text-center">{{ $item['nombre_de_colis'] }}</td>
                        <td class="montant montant-total text-right">{{ number_format($item['montant_total'], 0, ',', ' ') }}</td>
                        <td class="montant montant-paye text-right">{{ number_format($item['montant_paye'], 0, ',', ' ') }}</td>
                        <td class="montant montant-reste text-right">{{ number_format($item['reste_a_payer'], 0, ',', ' ') }}</td>
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
                            <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                        </td>
                        <td>
                            {{ $item['expediteur_nom'] }} {{ $item['expediteur_prenom'] }}
                            <br><small style="color: #555;">{{ $item['expediteur_tel'] ?? 'N/A' }}</small>
                        </td>
                        <td>
                            {{ $item['destinataire_nom'] }} {{ $item['destinataire_prenom'] }}
                            <br><small style="color: #555;">{{ $item['destinataire_tel'] ?? 'N/A' }}</small>
                        </td>
                        <td class="text-center">{{ $item['created_at'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center" style="padding: 25px;">Aucun colis validé trouvé pour les filtres sélectionnés.</td>
                    </tr>
                    @endforelse
                    
                    @if($colis->count() > 0)
                    <tr class="totaux-row">
                        <td colspan="2" class="text-bold">TOTAUX</td>
                        <td class="text-center text-bold">{{ $totalColisCount }}</td>
                        <td class="montant text-right text-bold">{{ number_format($totalMontant, 0, ',', ' ') }}</td>
                        <td class="montant text-right text-bold">{{ number_format($totalPaye, 0, ',', ' ') }}</td>
                        <td class="montant text-right text-bold">{{ number_format($totalReste, 0, ',', ' ') }}</td>
                        <td colspan="4"></td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="footer-info">
            Rapport généré automatiquement par le système — DS TRANSLOG © {{ date('Y') }}
        </div>
    </div>
</body>
</html>
