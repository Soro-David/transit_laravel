<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Facture - {{ $devis->reference ?? 'N/A' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <style>
        /* Police DejaVu (recommandée pour DOMPDF) */
        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
            src: url('{{ storage_path('fonts/DejaVuSans.ttf') }}') format('truetype');
        }

        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: bold;
            src: url('{{ storage_path('fonts/DejaVuSans-Bold.ttf') }}') format('truetype');
        }

        body {
            font-family: "DejaVu Sans", Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
            line-height: 1.4;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid #667eea;
        }

        .logo-section {
            flex: 1;
        }

        .logo {
            margin-bottom: 10px;
        }

        .logo img {
            max-height: 60px;
        }

        .company-info {
            flex: 2;
            text-align: center;
        }

        .invoice-info {
            flex: 1;
            text-align: right;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            margin: 0 0 5px 0;
        }

        .subtitle {
            font-size: 14px;
            color: #666;
            margin: 0;
        }

        .reference {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #28a745;
            color: white;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            margin-top: 5px;
        }

        .client-company {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            gap: 30px;
        }

        .client-info, .company-details {
            flex: 1;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #dee2e6;
        }

        .info-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 20px;
        }

        .info-item {
            flex: 1;
        }

        .info-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .info-value {
            font-size: 12px;
            font-weight: 600;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
        }

        th {
            background: #667eea;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        td {
            padding: 10px 8px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: top;
        }

        tr:nth-child(even) {
            background: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .total-section {
            margin-top: 20px;
            text-align: right;
        }

        .total-line {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .notes {
            margin-top: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            font-size: 11px;
        }

        .notes-title {
            font-weight: bold;
            color: #667eea;
            margin-bottom: 8px;
        }

        /* Ajustements pour éviter débordement sur PDF */
        thead { 
            display: table-header-group; 
        }
        tfoot { 
            display: table-footer-group; 
        }
        tr { 
            page-break-inside: avoid; 
        }

        .page-break {
            page-break-before: always;
        }

        .dimensions {
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <div class="logo-section">
            @php
                $logoPath = public_path('images/LOGOAFT.png');
            @endphp

            @if(file_exists($logoPath))
                <div class="logo">
                    <img src="{{ $logoPath }}" alt="Logo">
                </div>
            @endif
            <div class="company-name">
                <strong>{{ config('app.name', 'Mon Entreprise') }}</strong>
            </div>
        </div>

        <div class="company-info">
            <div class="title">FACTURE</div>
            <div class="subtitle">Document commercial</div>
        </div>

        <div class="invoice-info">
            <div class="reference">Réf: {{ $devis->reference ?? '-' }}</div>
            <div>Date: {{ $devis->created_at ? $devis->created_at->format('d/m/Y') : '-' }}</div>
            <div class="status-badge">{{ ucfirst($devis->etat ?? 'confirmé') }}</div>
        </div>
    </div>

    <!-- Informations client et entreprise -->
    <div class="client-company">
        <div class="client-info">
            <div class="section-title">CLIENT</div>
            <div class="text-bold">{{ $devis->nom_expediteur ?? '-' }} {{ $devis->prenom_expediteur ?? '' }}</div>
            <div>{{ $devis->adresse_expediteur ?? '' }}</div>
            <div>Tél: {{ $devis->tel_expediteur ?? '-' }}</div>
            <div>Email: {{ $devis->email_expediteur ?? '-' }}</div>
        </div>

        <div class="company-details">
            <div class="section-title">{{ config('app.name', 'Mon Entreprise') }}</div>
            <div>Tél: +225 00 00 00 00</div>
            <div>Email: contact@exemple.com</div>
            <div>Site: www.votreentreprise.com</div>
        </div>
    </div>

    <!-- Informations supplémentaires -->
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Mode de Transit</div>
            <div class="info-value">{{ $devis->mode_transit ?? '-' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Mode de Retrait</div>
            <div class="info-value">{{ $devis->mode_de_retrait ?? '-' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Pays</div>
            <div class="info-value">{{ $devis->pays_expedition ?? '-' }}</div>
        </div>
    </div>

    <!-- Détails des articles -->
    <div class="section-title">DÉTAILS DES ARTICLES</div>
    
    @php
        $items = $devis->devisItems ?? $devis->items ?? collect();
    @endphp

    <table>
        <thead>
            <tr>
                <th style="width: 40%">Description</th>
                <th style="width: 10%" class="text-center">Quantité</th>
                <th style="width: 10%" class="text-center">Poids</th>
                <th style="width: 15%" class="text-center">Dimensions</th>
                <th style="width: 12%" class="text-right">Valeur</th>
                <th style="width: 13%" class="text-right">Montant</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td>
                        <div class="text-bold">{{ $item->service ?? $item->description_colis ?? '—' }}</div>
                        @if($item->type_colis)
                            <div class="dimensions">{{ $item->type_colis }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantite_colis ?? 1 }}</td>
                    <td class="text-center">{{ $item->poids ?? '-' }}</td>
                    <td class="text-center">
                        @if($item->longueur || $item->largeur || $item->hauteur)
                            {{ $item->longueur ?? '-' }}×{{ $item->largeur ?? '-' }}×{{ $item->hauteur ?? '-' }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">{{ $devis->devise ?? 'XOF' }} {{ number_format($item->valeur_colis ?? 0, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ $devis->devise ?? 'XOF' }} {{ number_format($item->montant ?? 0, 0, ',', ' ') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px;">
                        Aucun article pour ce devis
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($items->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right text-bold" style="border: none; padding-top: 15px;">
                        TOTAL {{ $devis->devise ?? 'XOF' }}
                    </td>
                    <td class="text-right text-bold" style="border: none; padding-top: 15px; font-size: 14px;">
                        {{ number_format($devis->montant ?? ($items->sum('montant') ?? 0), 0, ',', ' ') }}
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>

    <!-- Notes -->
    <div class="notes">
        <div class="notes-title">INFORMATIONS COMPLÉMENTAIRES</div>
        <div>Mode de transit : {{ $devis->mode_transit ?? '-' }} — Mode de retrait : {{ $devis->mode_de_retrait ?? '-' }}</div>
        <div style="margin-top: 8px;">
            Merci pour votre confiance. Pour toute question relative à cette facture, contactez-nous à contact@exemple.com ou au +225 00 00 00 00.
        </div>
    </div>

    <!-- Pied de page -->
    <div class="footer">
        {{ config('app.name', 'Mon Entreprise') }} — {{ now()->format('Y') }} • Adresse : aft-import-export
        <br>
        Document généré le {{ now()->format('d/m/Y à H:i') }}
    </div>
</body>
</html>