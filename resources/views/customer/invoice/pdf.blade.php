<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Facture - {{ $devis->reference ?? 'N/A' }}</title>
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
            color: #333;
            background-color: #fff;
            margin: 0;
            padding: 0;
            font-size: 10px; /* <-- Nouvelle réduction de la police de base */
        }

        .invoice-box {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            padding: 15px; /* <-- Espacement principal réduit */
            background-color: #fff;
            line-height: 1.4; /* <-- Interligne resserré */
            box-sizing: border-box;
        }

        .layout-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px; /* <-- Marge réduite */
        }
        .layout-table td {
            padding: 0;
            vertical-align: top;
        }

        .logo img {
            max-width: 180px; /* <-- Taille du logo de nouveau réduite */
            height: auto;
        }
        .company-details-header {
            text-align: right;
        }
        .company-details-header h2 {
            margin: 0 0 4px 0;
            font-size: 16px; /* <-- Titre de l'entreprise réduit */
            font-weight: bold;
        }
        .company-details-header p {
            margin: 0;
            font-size: 10px;
        }

        .invoice-title-section {
            text-align: center;
            margin-bottom: 20px;
            margin-top: 10px;
            padding: 8px 0; /* <-- Espacement réduit */
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }
        .invoice-title-section h1 {
            font-size: 30px; /* <-- Titre "FACTURE" réduit */
            font-weight: bold;
            margin: 0;
            color: #000000;
        }

        .client-details h3 {
            margin: 0 0 5px 0;
            font-size: 12px; /* <-- Police réduite */
            font-weight: bold;
        }
        .client-details p {
            margin: 1px 0;
            font-size: 10px;
        }

        .invoice-meta table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-meta td {
            padding: 4px 6px; /* <-- Espacement des cellules réduit */
            font-size: 10px;
            border: 1px solid #eee;
        }
        .invoice-meta td:first-child {
            text-align: left;
            font-weight: bold;
            background-color: #f9f9f9;
            width: 40%;
        }
        .invoice-meta td:last-child {
            text-align: right;
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            margin-top: 20px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 6px; /* <-- Espacement des cellules réduit */
            text-align: left;
            font-size: 10px;
        }
        .items-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .col-produit { width: 55%; }
        .items-table .col-qty { width: 10%; }
        .items-table .col-price { width: 15%; }
        .items-table .col-montant { width: 20%; }

        .totals-summary {
            margin-top: 12px;
        }
        .totals-summary table {
            width: 45%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals-summary td {
            padding: 5px 7px; /* <-- Espacement réduit */
            font-size: 10px;
        }
        .totals-summary td:first-child {
            text-align: right;
            font-weight: bold;
        }
        .totals-summary td:last-child {
            text-align: right;
            font-weight: bold;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
        }
        .grand-total-header {
            background-color: #e0e0e0 !important;
            font-size: 12px !important; /* <-- Police réduite */
        }

        .conditions {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #eee;
        }
        .conditions h4 {
            margin: 0 0 6px 0;
            font-size: 12px; /* <-- Police réduite */
            font-weight: bold;
            text-align: center;
        }
        .conditions p {
            font-size: 9px; /* <-- Police réduite */
            line-height: 1.3;
            color: #555;
            text-align: justify;
        }

        .footer-section {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #333;
            color: #555;
        }
        .footer-generation-table {
            width: 100%;
            margin-bottom: 8px;
        }
        .footer-generation-table td {
            font-size: 9px;
        }
        .footer-company-details {
            text-align: center;
            line-height: 1.3;
            font-size: 9px;
        }
        .footer-company-details p {
            margin: 1px 0;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- Header Section -->
        <table class="layout-table">
            <tr>
                <td>
                    <div class="logo">
                        @php
                            $logoPath = public_path('images/LOGOAFT.png');
                        @endphp
                        @if(file_exists($logoPath))
                            <img src="{{ $logoPath }}" alt="Company Logo">
                        @else
                            <h2>AFT IMPORT EXPORT</h2>
                        @endif
                    </div>
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    <div class="company-details-header">
                        <h2>AFT IMPORT EXPORT</h2>
                        <p>7 AVENUE LOUIS BLERIOT LA COURNEUVE</p>
                        <p>93120 France</p>
                        <p>Tel. +33171894351</p>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Invoice Title -->
        <div class="invoice-title-section">
            <h1>FACTURE</h1>
        </div>

        <!-- Client and Invoice Meta -->
        <table class="layout-table">
            <tr>
                <td style="width: 55%; vertical-align: top;">
                    <div class="client-details">
                        @php
                            $expediteur = trim(($devis->nom_expediteur ?? '') . ' ' . ($devis->prenom_expediteur ?? ''));
                            $destinataire = trim(($devis->nom_destinataire ?? '') . ' ' . ($devis->prenom_destinataire ?? ''));
                        @endphp
                        <h3>De: {{ $expediteur ?: 'N/A Expediteur' }}</h3>
                        <p>Tel: {{ $devis->tel_expediteur ?? 'N/A' }}</p>
                        <p>Adresse: {{ $devis->lieu_expedition ?? 'Non spécifié' }}</p>
                        <br>
                        <h3>À: {{ $destinataire ?: 'N/A Destinataire' }}</h3>
                        <p>Tel: {{ $devis->tel_destinataire ?? 'N/A' }}</p>
                        <p>Adresse: {{ $devis->lieu_destination ?? 'Non spécifié' }}</p>
                    </div>
                </td>
                <td style="width: 45%; vertical-align: top;">
                    <div class="invoice-meta">
                        <table>
                            <tr><td>Facture n°</td><td>{{ $devis->reference ?? 'N/A' }}</td></tr>
                            <tr><td>Date</td><td>{{ $devis->created_at ? $devis->created_at->format('d-m-Y') : 'N/A' }}</td></tr>
                            <tr><td>Référence</td><td>{{ $devis->reference ?? 'N/A' }}</td></tr>
                            <tr><td>Colis</td><td></td></tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>


        <!-- Items Table -->
        @php
        $items = $devis->devisItems ?? $devis->items ?? collect();
        $devise = $devis->devise ?? '';
        $montantTotal = $devis->montant ?? ($items->sum('montant') ?? 0);
    @endphp

<table class="items-table">
    <thead>
        <tr>
            <th class="col-produit">Produit / Service</th>
            <th class="col-qty text-right">Qté</th>
            <th class="col-price text-right">P.U. ({{ $devise }})</th>
            <th class="col-montant text-right">Montant ({{ $devise }})</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $item)
            <tr>
                <td>
                    {{ $item->service ?? $item->description_colis ?? '—' }}
                    @if($item->type_colis)
                        <br><small><strong>Type:</strong> {{ $item->type_colis }}</small>
                    @endif
                    @if($item->poids)
                        <br><small><strong>Poids:</strong> {{ $item->poids }} kg</small>
                    @endif
                    @if($item->longueur || $item->largeur || $item->hauteur)
                        <br><small><strong>Dimensions:</strong> 
                            {{ $item->longueur ?? '-' }} × {{ $item->largeur ?? '-' }} × {{ $item->hauteur ?? '-' }}
                        </small>
                    @endif
                </td>
                <td class="text-right">{{ $item->quantite_colis ?? 1 }}</td>
                <td class="text-right">{{ number_format($item->montant ?? 0, 2, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($item->montant ?? 0, 2, ',', ' ') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align: center;">Aucun article trouvé.</td>
            </tr>
        @endforelse
    </tbody>
</table>

        <!-- Totals Summary -->
        <div class="totals-summary">
            <table>
                <tr><td>Sous-total Produits</td><td>{{ number_format($montantTotal, 2, ',', ' ') }}</td></tr>
                <tr><td>Montant total ({{ $devise }})</td><td class="grand-total-header">{{ number_format($montantTotal, 2, ',', ' ') }}</td></tr>
            </table>
        </div>

        <!-- Conditions de vente -->
        <div class="conditions">
            <h4>Conditions de vente</h4>
            <p>
                Les colis et marchandises transportés par AFRIQUE FRET TRANSIT IMPORT EXPORT, de la France vers la Côte d'Ivoire et de la Côte d'Ivoire vers la France, doivent faire l'objet du règlement intégral des frais de transport, des droits de douane et des taxes avant toute livraison. Les colis non solides seront conservés dans nos entrepôts en attendant la régularisation de la situation. Passé un délai de 5 jours, des frais de magasinage ainsi qu'une pénalité de 10 % du montant total seront appliqués. Au-delà de 30 jours, les colis et marchandises non réclamés seront vendus afin de couvrir les frais engagés.
            </p>
        </div>

        <!-- Footer Section -->
        <div class="footer-section">
            <table class="footer-generation-table">
                <tr>
                    <td>Généré le {{ now()->format('d-m-Y') }}<br>par aft chine</td>
                    <td style="text-align: right;">Page 1/1</td>
                </tr>
            </table>
            <div class="footer-company-details">
                <p><strong>AFT IMPORT EXPORT</strong> 7 AVENUE LOUIS BLERIOT LA COURNEUVE 93120 France | Tel. +33978809389 | contacts.aft@gmail.com</p>
                <p>IBAN FR03 1744 8000 01PO MONE AERZ W45 | BIC: SFPEFRP2</p>
                <p>N°TVA:FR96881916365 N°ORI FR88191636500011 SIRET881916365 RCS Bobigny, EXO TVA, article 262 DU CGI</p>
            </div>
        </div>
    </div>
</body>
</html>