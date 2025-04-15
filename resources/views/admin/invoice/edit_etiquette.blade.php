<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Étiquettes Colis AFT</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
             font-family: Arial, Helvetica, sans-serif;
             font-size: 9pt;
             line-height: 1.2;
        }

        @page {
            size: A6 landscape; /* Format et orientation */
            margin: 0mm 5mm;
        }

        .with-padding {
            padding: 5mm 5mm 0 5mm; /* haut droite bas gauche */
        }

        .etiquette-page {
            overflow: hidden;
            display: block; /* éviter flex ici si inutile */
            page-break-after: always;
            padding: 5mm; 
        }

       

        .etiquette-header {
            background-color: #000 !important;
            color: #fff !important;
            text-align: center;
            padding: 3px 0;
            font-weight: bold;
            font-size: 12pt;
            letter-spacing: 5px;
            margin-bottom: 4mm; /* Espace réduit */
            flex-shrink: 0;
        }

        .etiquette-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Répartit l'espace vertical */
        }

        .info-header-table,
        .details-table,
        .reference-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4mm; /* Espace cohérent */
            flex-shrink: 0; /* Empêche le rétrécissement */
        }
        .reference-table {
            margin-bottom: 0; /* Pas de marge après le dernier élément */
            /* flex-grow: 1; */ /* Peut poser problème */
            min-height: 20mm; /* Hauteur minimale */
        }


        /* Section 1: Logo / Adresse / QR */
        .info-header-table {
            border: none;
            min-height: 20mm;
        }
        .info-header-table td {
            vertical-align: middle;
            /* padding: 0 1mm; */
            border: none;
        }
        .info-header-table .logo-cell { width: 33%; text-align: left;}
        .info-header-table .address-cell { width: 34%; text-align: center; }
        .info-header-table .qr-cell { width: 33%; text-align: right;}

        .custom-logo {
            max-width: 100%;
            max-height: 18mm;
            display: block;
            margin-right: auto; /* Aligne à gauche */
        }
        .address-details {
            font-size: 8pt;
            font-weight: bold;
            line-height: 1.3;
        }
        .address-details strong {
            font-size: 9pt;
            display: block;
            margin-bottom: 1mm;
        }
        .qr-code-img-header {
            max-width: 100%;
            max-height: 22mm;
            display: block;
            margin-left: auto; /* Aligne à droite */
        }
        .qr-placeholder { font-size: 8pt; color: #666; text-align: center; padding: 5mm 0; }


        /* Section 2: Table Détails */
        .details-table {
            border: 1.5pt solid #000;
        }
        .details-table th {
            background-color: #E0E0E0 !important;
            font-weight: bold;
            font-size: 8.5pt;
            /* padding: 1.5mm 2mm; */
            border: 0.5pt solid #000;
            text-align: center;
        }
        .details-table td {
            border: 0.5pt solid #000;
            /* padding: 1.5mm 2mm; */
            vertical-align: top;
            font-size: 12pt;
            font-weight: bold;
            line-height: 1.1;
        }
        .details-table .date-cell {
            width: 22%;
            text-align: center;
            font-size: 11pt;
        }
        .details-table .dest-cell { width: 48%; }
        .details-table .exp-cell { width: 30%; }

        .data-value { display: block; }
        .sub-info {
            font-size: 10pt;
            font-weight: normal;
            margin-top: 1mm;
         }

        /* Section 3: Référence / Compteur */
        .reference-table {
            border: 1.5pt solid #000;
            display: table; /* Utiliser table pour dompdf est parfois plus stable */
            table-layout: fixed; /* Largeurs fixes */
        }

        .reference-table td {
            vertical-align: middle;
            /* padding: 1.5mm 2.5mm; */
            font-size: 12pt; /* Base pour cette table */
            font-weight: bold;
            border: 0.5pt solid #000; /* Bordures internes */
        }

        .reference-table .ref-cell {
            width: 70%;
            border-right: 1pt solid #000;
            text-align: left;
        }
        .reference-table .count-cell {
            width: 30%;
            text-align: center;
        }

        .qr-code-img-ref {
            max-width: 12mm;
            max-height: 12mm;
            display: inline-block;
            vertical-align: middle;
            margin-right: 2mm;
        }
        .reference-number {
            font-size: 26pt;
            font-weight: bold;
            color: #000;
            display: inline-block; /* Pour aligner avec QR */
            vertical-align: middle;
            line-height: 1;
            word-wrap: break-word;
        }
        .type-colis-info {
            font-size: 8pt;
            font-weight: normal;
            display: block;
            word-wrap: break-word;
            margin-top: 1mm; /* Espace après la référence */
        }

        .counter-text {
            font-size: 26pt;
            font-weight: bold;
            display: block;
            line-height: 1;
            margin-bottom: 1mm; /* Espace avant destination */
        }
        .destination-text {
            font-size: 9pt;
            font-weight: bold;
            display: block;
        }

    </style>
</head>
<body>
    @if($colis && !$colis->isEmpty())
        @foreach($colis as $index => $colisItem)
            @php
                $destinataire = $colisItem->destinataire ?? null;
                $expediteur = $colisItem->expediteur ?? null;
                $expediteurVille = $expediteur->ville ?? 'ABIDJAN';
                $destinationVille = $colisItem->destination_ville ?? 'ABIDJAN';
                $typeColisDetail = $colisItem->type_colis_detail ?? ($colisItem->type_colis ?? 'Type N/A');
                $qrCodePath = $colisItem->qr_code_path ?? null;
                $qrRefText = 'SA-' . ($colisItem->reference_colis ?? 'REF') . '_1_' . ($colisItem->id ?? '0');
                $colisReference = $colisItem->reference_colis ?? 'N/A';
                $colisType = $colisItem->type_colis ?? 'N/A';
                $isLast = $loop->last;
            @endphp

            <div class="etiquette-page" @if($isLast) style="page-break-after: avoid;" @endif>
                <div class="etiquette-header">
                    A F T   I M P O R T   E X P O R T
                </div>

                <div class="etiquette-content with-padding">

                    <table class="info-header-table">
                         <tr>
                            <td class="logo-cell">
                                 <img src="{{ public_path('images/LOGOAFT.png') }}" alt="Logo" class="custom-logo">
                            </td>
                            <td class="address-cell">
                                <div class="address-details">
                                    <strong>AFT IMPORT EXPORT</strong>
                                    7 Avenue Louis BLERIOT<br>
                                    93120 LA COURNEUVE<br>
                                    Phone: 0186786967
                                </div>
                            </td>
                            <td class="qr-cell">
                                @if($qrCodePath && file_exists(public_path(ltrim($qrCodePath, '/'))))
                                    <img class="qr-code-img-header" src="{{ public_path(ltrim($qrCodePath, '/')) }}" alt="QR Code">
                                    {{-- Texte sous QR retiré selon dernier code HTML fourni --}}
                                @else
                                    <div class="qr-placeholder">QR Code<br>Indisponible</div>
                                @endif
                            </td>
                        </tr>
                    </table>

                    <table class="details-table">
                        <thead>
                            <tr>
                                <th style="width: 22%;">DATE</th>
                                <th style="width: 48%;">DESTINATAIRE</th>
                                <th style="width: 30%;">EXPEDITEUR</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="date-cell">
                                    <span class="data-value">{{ $colisItem->created_at ? $colisItem->created_at->format('Y-m-d') : 'N/A' }}</span>
                                    <span class="data-value">{{ $colisItem->created_at ? $colisItem->created_at->format('H:i:s') : '' }}</span>
                                </td>
                                <td class="dest-cell">
                                    <span class="data-value">{{ $destinataire->nom ?? 'N/A' }} {{ $destinataire->prenom ?? '' }}</span>
                                    <span class="data-value">{{ $destinataire->tel ? str_replace([' ', '-'], '', $destinataire->tel) : 'N/A' }}</span>
                                </td>
                                <td class="exp-cell">
                                    <span class="data-value">{{ $expediteur->nom ?? 'N/A' }} {{ $expediteur->prenom ?? '' }}</span>
                                    <span class="data-value sub-info">{{ $expediteur->tel ? str_replace([' ', '-'], '', $expediteur->tel) : 'N/A' }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="reference-table">
                        <tr>
                            <td class="ref-cell"><br>
                                <div style="display: flex; align-items: center;">
                                    @if($qrCodePath && file_exists(public_path(ltrim($qrCodePath, '/'))))
                                        <img class="qr-code-img-ref text-center" src="{{ public_path(ltrim($qrCodePath, '/')) }}" alt="QR Ref">
                                    @endif
                                    <span class="reference-number">{{ $colisReference }}</span>
                                </div>                                
                                <div style="text-align: center;">
                                    <span class="type-colis-info">{{ $colisType }}</span>
                                </div>
                            </td>
                            <td class="count-cell">
                                <span class="counter-text">{{ $index + 1 }} / {{ count($colis) }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        @endforeach
    @else
        <div class="etiquette-page">
             <p style="text-align: center; padding-top: 20mm;">Aucun colis à afficher.</p>
         </div>
    @endif
</body>
</html>