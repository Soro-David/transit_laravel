<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Étiquette - {{ $referenceAffichee }}</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
            size: 80mm 200mm;
        }
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 10px;
            width: 80mm;
            height: 200mm;
            font-size: 12px;
            line-height: 1.2;
        }
        .etiquette {
            page-break-after: always;
            border: 1px solid #000;
            padding: 8px;
            height: 190mm;
            display: flex;
            flex-direction: column;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }
        .reference {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .service {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .date {
            font-size: 12px;
            margin-bottom: 10px;
        }
        .operation {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0;
            padding: 5px;
            background-color: #f0f0f0;
            border-radius: 5px;
        }
        .section {
            margin: 8px 0;
        }
        .section-title {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 3px;
        }
        .section-content {
            font-size: 11px;
            padding-left: 5px;
        }
        .qr-code {
            text-align: center;
            margin: 10px 0;
        }
        .qr-code img {
            width: 120px;
            height: 120px;
        }
        .footer {
            margin-top: auto;
            text-align: center;
            font-size: 10px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        .colis-info {
            background-color: #f8f9fa;
            padding: 5px;
            border-radius: 3px;
            margin: 5px 0;
            font-size: 10px;
        }
    </style>
</head>
<body>
    @foreach($etiquettes as $etiquette)
    <div class="etiquette">
        <div class="header">
            <div class="reference">{{ $etiquette['reference'] }}</div>
            <div class="service">{{ $etiquette['service'] }}</div>
            <div class="date">{{ date('d/m/Y', strtotime($etiquette['date_programme'])) }}</div>
        </div>

        <div class="operation">
            {{ strtoupper($etiquette['actions_a_faire']) }}
        </div>

        <div class="section">
            <div class="section-title">CLIENT</div>
            <div class="section-content">{{ $etiquette['nom_expediteur'] }}</div>
        </div>

        <div class="section">
            <div class="section-title">TÉLÉPHONE</div>
            <div class="section-content">{{ $etiquette['tel_expediteur'] }}</div>
        </div>

        <div class="section">
            <div class="section-title">NATURE DU COLIS</div>
            <div class="section-content">{{ $etiquette['nature_colis'] }}</div>
        </div>

        <div class="section">
            <div class="section-title">ADRESSE</div>
            <div class="section-content">{{ $etiquette['lieu_expedition'] }}</div>
        </div>

        <div class="qr-code">
            <!-- MODIFICATION ICI: data:image/png devient data:image/svg+xml -->
            <img src="data:image/svg+xml;base64,{{ $etiquette['qrCodeBase64'] }}" alt="QR Code">
        </div>
        <div class="colis-info">
            Colis: {{ $etiquette['compteur'] }}/{{ $quantiteTotale }} | 
            Poids: {{ $etiquette['poids'] }} kg
        </div>

        <div class="section">
            <div class="section-title">CHAUFFEUR</div>
            <div class="section-content">{{ $etiquette['chauffeurName'] }}</div>
        </div>

        <div class="footer">
            AFT Logistics - {{ date('d/m/Y H:i') }}
        </div>
    </div>
    @endforeach
</body>
</html>