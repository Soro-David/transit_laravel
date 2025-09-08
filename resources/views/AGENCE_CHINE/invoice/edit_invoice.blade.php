@extends('AGENCE_CHINE.layouts.agentprint')

@section('content-header')

@endsection

@section('content')
    @csrf

    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #333;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }

        .invoice-box-container {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .invoice-box {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            padding: 25px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            background-color: #fff;
            font-size: 14px;
            line-height: 1.6;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center; /* NOUVEAU/MODIFIÉ: Pour mieux aligner le logo et les détails */
            margin-bottom: 20px; /* NOUVEAU/MODIFIÉ */
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .logo img {
            max-width: 260px; /* NOUVEAU/MODIFIÉ: Logo plus grand */
            height: auto;
        }
        .company-details-header {
            text-align: right;
        }
        .company-details-header h2 {
            margin: 0 0 5px 0;
            font-size: 25px;
            font-weight: bold;
        }
        .company-details-header p {
            margin: 0;
            font-size: 13px;
        }

        .invoice-title-section {
            text-align: center;
            margin-bottom: 20px; /* NOUVEAU/MODIFIÉ */
        }
        .invoice-title-section h1 {
            font-size: 45px;
            font-weight: bold;
            margin: 0 0 8px 0;
            color: #000000505000;
            letter-spacing: 1px;
        }
        .simulated-barcode {
            height: 35px;
            background: linear-gradient(to right,
                #333 0%, #333 2px, transparent 2px, transparent 4px,
                #333 4px, #333 5px, transparent 5px, transparent 7px,
                #333 7px, #333 10px, transparent 10px, transparent 11px,
                #333 11px, #333 12px, transparent 12px, transparent 14px
            );
            background-repeat: repeat-x;
            background-size: 14px 100%;
            max-width: 220px;
            margin: 10px auto 0;
        }
        .simulated-barcode-small {
            height: 25px;
            background: linear-gradient(to right,
                #333 0%, #333 1.5px, transparent 1.5px, transparent 3px,
                #333 3px, #333 4px, transparent 4px, transparent 5.5px,
                #333 5.5px, #333 7.5px, transparent 7.5px, transparent 8.5px,
                #333 8.5px, #333 9.5px, transparent 9.5px, transparent 11px
            );
            background-repeat: repeat-x;
            background-size: 11px 100%;
            max-width: 180px;
            margin: 10px 0 0 auto;
        }


        .client-invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px; /* NOUVEAU/MODIFIÉ */
        }
        .client-details {
            max-width: 55%;
        }
        .client-details h3 {
            margin: 0 0 8px 0;
            font-size: 16px;
            font-weight: bold;
        }
        .client-details p {
            margin: 3px 0;
            font-size: 14px;
        }
        .invoice-meta {
             max-width: 40%;
        }
        .invoice-meta table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-meta td {
            padding: 6px 10px;
            font-size: 14px;
        }
        .invoice-meta td:first-child {
            text-align: left;
            font-weight: bold;
            background-color: #f9f9f9;
            border: 1px solid #eee;
            width: 45%;
        }
        .invoice-meta td:last-child {
            text-align: right;
            border: 1px solid #eee;
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .references-section {
            margin-bottom: 15px; /* NOUVEAU/MODIFIÉ: Réduit pour section souvent vide */
            border: 1px solid #eee;
            padding-top: 5px; /* NOUVEAU/MODIFIÉ */
            padding-bottom: 5px; /* NOUVEAU/MODIFIÉ */
        }
        .references-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .references-section th, .references-section td {
            border: 1px solid #eee;
            padding: 6px 8px; /* NOUVEAU/MODIFIÉ */
            font-size: 12px; /* NOUVEAU/MODIFIÉ */
            text-align: center;
        }
        .references-section th {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .references-section td {
            height: 20px; /* NOUVEAU/MODIFIÉ */
        }


        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }
        .items-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .items-table .col-qty, .items-table .col-price, .items-table .col-montant {
            text-align: right;
        }
        .items-table .col-produit { width: 55%; }
        .items-table .col-qty { width: 10%; }
        .items-table .col-price { width: 15%; }
        .items-table .col-montant { width: 20%; }

        .item-description {
            font-size: 12px;
            color: #666;
            padding-left: 10px;
            margin-top: 4px;
        }
        .item-main-service {
            font-weight: bold;
        }


        .totals-summary {
            margin-top: 20px; /* NOUVEAU/MODIFIÉ */
            padding-top: 15px;
            border-top: 2px solid #eee;
            margin-bottom: 20px; /* NOUVEAU/MODIFIÉ */
        }
        .totals-summary table {
            width: 45%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals-summary td {
            padding: 8px 10px;
            font-size: 14px;
        }
        .totals-summary td:first-child {
            text-align: right;
            font-weight: bold;
            width: 60%;
        }
        .totals-summary td:last-child {
            text-align: right;
            font-weight: bold;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            min-width: 130px;
        }
        .grand-total-header {
            background-color: #e0e0e0 !important;
            font-size: 15px !important;
        }

        .payment-notes-section {
            display: flex;
            justify-content: space-between;
            margin-top: 20px; /* NOUVEAU/MODIFIÉ */
            margin-bottom: 15px; /* NOUVEAU/MODIFIÉ */
            align-items: flex-start;
        }
        .payment-terms table {
            width: auto;
            border-collapse: collapse;
        }
        .payment-terms td {
            padding: 6px 10px;
            font-size: 14px;
            border: 1px solid #eee;
        }
        .payment-terms td:first-child {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .payment-terms td:last-child {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .notes-section {
            flex-grow: 1;
            margin-left: 25px;
        }
        .notes-section textarea {
            width: 100%;
            min-height: 60px; /* NOUVEAU/MODIFIÉ: Hauteur min réduite un peu */
            border: 1px solid #eee;
            padding: 8px;
            font-size: 13px;
            box-sizing: border-box;
            resize: vertical;
        }
        .notes-section p {
            margin: 0 0 5px 0;
            font-weight: bold;
            font-size: 14px;
        }


        .final-totals {
            margin-top: 15px;
            padding-top: 15px;
            margin-bottom: 15px;
        }
        .final-totals table {
            width: 45%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .final-totals td {
            padding: 10px;
            font-size: 15px;
            font-weight: bold;
        }
        .final-totals td:first-child {
            text-align: right;
        }
        .final-totals td:last-child {
            text-align: right;
            background-color: #e0e0e0;
            border: 1px solid #ccc;
            min-width: 130px;
        }
        .final-totals .reste-a-payer td:last-child {
             background-color: #d0d0d0;
        }



        .conditions {
            margin-top: 20px; /* NOUVEAU/MODIFIÉ */
            padding-top: 15px; /* NOUVEAU/MODIFIÉ */
            border-top: 1px solid #eee;
            margin-bottom: 20px; /* NOUVEAU/MODIFIÉ */
        }
        .conditions h4 {
            margin: 0 0 10px 0;
            font-size: 16px;
            font-weight: bold;
        }
        .conditions p {
            font-size: 12px; /* NOUVEAU/MODIFIÉ: Légère réduction si besoin de place */
            line-height: 1.4; /* NOUVEAU/MODIFIÉ */
            color: #555;
            text-align: justify;
        }

        .footer-section {
            margin-top: 25px; /* NOUVEAU/MODIFIÉ */
            padding-top: 15px;
            border-top: 2px solid #333;
            font-size: 11px;
            color: #555;
        }
        .footer-generation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            font-size: 12px;
        }
        .footer-company-details {
            text-align: center;
            line-height: 1.4;
            font-size: 12px;
        }
        .footer-company-details p {
            margin: 2px 0;
        }
        .footer-company-details strong {
            color: #333;
        }


        .text-bold { font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }


        @media print {
            @page {
                size: A4;
                margin: 0;
            }
            .items-table th, .items-table td {
                font-size: 10pt !important;
                padding: 6px !important;
            }
            body, html {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                height: auto !important;
                background-color: #fff !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                font-size: 10.5pt; /* NOUVEAU/MODIFIÉ: Légère réduction pour tout faire tenir */
            }

            body > footer,              /* Si le footer est un enfant direct de body */
            .main-footer,             /* Classe commune pour les footers (ex: AdminLTE) */
            #site-footer,             /* ID commun */
            #footer,                  /* Autre ID commun */
            [role="contentinfo"] {    /* Rôle ARIA souvent utilisé pour les footers */
                display: none !important;
            }
            .invoice-box-container {
                margin: 0 !important;
                padding: 0 !important;
                display: block !important;
            }
            .invoice-box {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 12mm !important; /* NOUVEAU/MODIFIÉ: Marges A4 un peu réduites si besoin */
                box-shadow: none !important;
                border: none !important; /* Si vous voulez un cadre visible à l'impression, changez pour ex: border: 1px solid #ccc !important; */
                page-break-inside: avoid;
                box-sizing: border-box !important;
            }

            .no-print, .no-print * {
                display: none !important;
            }

            .header-section { align-items: center !important; } /* Assurer l'alignement à l'impression */
            .logo img { max-width: 240px !important; } /* Taille du logo pour impression, un peu moins pour être sûr */


            .references-section {
                padding-top: 2mm !important;
                padding-bottom: 2mm !important;
                margin-bottom: 3mm !important; /* NOUVEAU/MODIFIÉ */
                margin-top: 2mm !important; /* NOUVEAU/MODIFIÉ */
            }
            .references-section th, .references-section td {
                padding: 3mm 4mm !important;
                font-size: 8pt !important; /* NOUVEAU/MODIFIÉ */
                height: auto !important;
            }


            .header-section, .invoice-title-section, .client-invoice-details,
            .items-table, .totals-summary,
            .payment-notes-section, .final-totals, .conditions {
                margin-bottom: 5mm !important; /* NOUVEAU/MODIFIÉ: Espacements verticaux réduits */
                margin-top: 3mm !important; /* NOUVEAU/MODIFIÉ */
                padding-top: 0 !important;
                padding-bottom: 0 !important;
            }
            .simulated-barcode-small {
                margin-top: 4mm !important; /* NOUVEAU/MODIFIÉ */
                margin-bottom: 4mm !important; /* NOUVEAU/MODIFIÉ */
            }
            .footer-section {
                margin-top: 6mm !important; /* NOUVEAU/MODIFIÉ */
                padding-top: 5mm !important; /* NOUVEAU/MODIFIÉ */
                margin-bottom: 0 !important;
                padding-bottom: 0 !important;
                page-break-before: auto;
                background-color: #fff !important;
            }
             /* Tailles de police spécifiques pour impression */
             .company-details-header h2 {
                margin: 0 0 5px 0;
                font-size: 25px;
                font-weight: bold;
            }
            .invoice-title-section h1 {
                    font-size: 45px;
                    font-weight: bold;
                    margin: 0 0 8px 0;
                    color: #000; /* Corrigé ici */
                    letter-spacing: 1px;
                }
 /* NOUVEAU/MODIFIÉ */
            .client-details h3 { font-size: 11.5pt !important; } /* NOUVEAU/MODIFIÉ */
            .conditions h4 { font-size: 11.5pt !important; } /* NOUVEAU/MODIFIÉ */
            .conditions p { font-size: 9pt !important; line-height: 1.3 !important; } /* NOUVEAU/MODIFIÉ */
            .footer-company-details { font-size: 8.5pt !important; } /* NOUVEAU/MODIFIÉ */
            .items-table th, .items-table td { font-size: 10pt !important; padding: 6px !important; } /* NOUVEAU/MODIFIÉ */
            .totals-summary td, .final-totals td { font-size: 10pt !important; padding: 5px 8px !important;}
            .grand-total-header { font-size: 11pt !important; }
            .payment-terms td { font-size: 10pt !important; }
            .notes-section p { font-size: 10pt !important; }
            .notes-section textarea { font-size: 9pt !important; min-height: 40px !important; }
        }
    </style>

    <div class="invoice-box-container">
        <div class="invoice-box">
            <!-- Header Section -->
            <div class="header-section">
                 <div class="logo">
                     <img src="{{ asset('images/LOGOAFT.png') }}" alt="Company Logo">
                 </div>
                 <div class="company-details-header">
                     <h2>AFT IMPORT EXPORT</h2>
                     <p>7 AVENUE LOUIS BLERIOT LA COURNEUVE</p>
                     <p>93120 France</p>
                     <p>Tel: +33171894351</p>
                 </div>
             </div>

             <!-- Invoice Title -->
             <div class="invoice-title-section">
                 <h1>FACTURE</h1>
                 <div class="simulated-barcode"></div>
             </div>

             <!-- Client and Invoice Meta -->
             <div class="client-invoice-details">
                 <div class="client-details">
                     <h3>{{ $expediteur ?? 'N/A Expediteur' }}</h3>
                     <p>Tel: {{ $tel_expediteur ?? 'N/A' }}</p>
                     <br>
                     <h3>À: {{ $destinataire ?? 'N/A Destinataire' }}</h3>
                     <p>Tel: {{ $tel_destinataire ?? 'N/A' }}</p>
                     <p>Adresse de Livraison: {{ $adresse_destinataire ?? 'N/A' }}</p>
                 </div>
                 <div class="invoice-meta">
                     <table>
                         <tr><td>Facture No.</td><td>{{ $numero_facture ?? 'N/A' }}</td></tr>
                         <tr><td>Date:</td><td>{{ isset($date_facture) ? $date_facture->format('d-m-Y') : 'N/A' }}</td></tr>
                         <tr><td>Référence Colis:</td><td>{{ $reference_colis ?? 'N/A' }}</td></tr>
                     </table>
                 </div>
             </div>

             <!-- Reference Section (Optional) -->
            <div class="references-section">
                <table>
                    <thead>
                        <tr>
                            <th>Devis</th><th>Bon de Commande</th><th>Bon de travail</th>
                            <th>Devis Réf.</th><th>Commande Réf.</th><th>Bon de trav Ref.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td> </td><td> </td><td> </td>
                            <td> </td><td> </td><td> </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Items Table -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="col-produit">Produit / Service</th>
                        <th class="col-qty">Qté</th>
                        <th class="col-price">P.U. ({{ $devise ?? ' ' }})</th>
                        <th class="col-montant">Montant ({{ $devise ?? ' ' }})</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoiceItems as $item)
                    <tr>
                        <td>
                            <span class="item-main-service">{{ $item['service'] ?? 'N/A' }}</span>
                        </td>
                        <td class="col-qty">{{ number_format($item['quantite_totale'] ?? 0, 0, ',', ' ') }}</td>
                        <td class="col-price">{{ number_format($item['prix_unitaire'] ?? 0, 0, ',', ' ') }}</td>
                        <td class="col-montant">{{ number_format($item['montant_total_ligne'] ?? 0, 0, ',', ' ') }}</td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center;">Aucun article trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="simulated-barcode-small"></div>


            <!-- Totals Summary -->
            <div class="totals-summary">
                <table>
                    <tr><td>Sous total</td><td>{{ number_format($prix_total ?? 0, 0, ',', ' ') }}</td></tr>
                    <tr><td>Montant total ({{ $devise ?? ' ' }})</td><td class="grand-total-header">{{ number_format($prix_total ?? 0, 0, ',', ' ') }}</td></tr>
                </table>
            </div>

            <!-- Payment and Notes -->
            <div class="payment-notes-section">
                <div class="payment-terms">
                    <table>
                        <tr><td>Terme de paiement</td><td>{{ $mode_payement ?? 'N/A' }}</td></tr>
                        <tr><td>Paiement dû le</td><td>{{ isset($date_facture) ? $date_facture->format('d-m-Y') : 'N/A' }}</td></tr>
                    </table>
                </div>
                <div class="notes-section">
                     <p>Notes</p>
                     <textarea readonly>{{ $notes_variable ?? '' }}</textarea>
                </div>
            </div>

            <!-- Final Totals -->
             <div class="final-totals">
                 <table>
                     <tr><td>Total ({{ $devise ?? ' ' }})</td><td>{{ number_format($prix_total ?? 0, 0, ',', ' ') }}</td></tr>
                     <tr><td>Total Payé ({{ $devise ?? ' ' }})</td><td>{{ number_format($totalMontantPaye ?? 0, 0, ',', ' ') }}</td></tr>
                     <tr class="reste-a-payer"><td>Reste à payer ({{ $devise ?? ' ' }})</td><td>{{ number_format($restePaye ?? 0, 0, ',', ' ') }}</td></tr>
                 </table>
                 <div class="simulated-barcode-small"></div>
             </div>

            <!-- Conditions de vente -->
            <div class="conditions">
                <h4 class="text-center">Conditions de vente</h4>
                <p>Les colis et marchandise transportées par AFRIQUE FRET TRANSIT IMPORT EXPORT de la France vers la cote d'ivoire et de la cote d'ivoire vers la France, le montant du transport, autres frais de douane et taxes doivent êtres solder avant livraison.les colis non soldés seront confisqué dans nos entrepôts jusqu'à la régularisation de la situation. passé délai 5 jours les frais de magasinage ainsi qu' une pénalité de 10% montant du seront appliqués et au delà 30 jours les colis et marchandises seront vendus pour remboursement des frais.</p>
            </div>

            <!-- Footer Section -->
             <div class="footer-section">
                 <div class="footer-generation">
                     <div>Généré le {{ now()->format('d-m-Y') }}<br>par {{ Auth::user()->first_name ?? 'Agent' }} {{ Auth::user()->last_name ?? '' }}</div>
                     <div>Page 1/1</div>
                 </div>
                 <div class="footer-company-details">
                     <p><strong>AFT IMPORT EXPORT</strong> 7 AVENUE LOUIS BLERIOT LA COURNEUVE 93120 France | Tel. +33978809389 | contacts.aft@gmail.com</p>
                     <p>IBAN FR03 1744 8000 01PO MQNE AER2 W45 | BIC: SFPEFRP2</p>
                     <p>N°TVA:FR96881916365 N°ORI FR88191636500011 SIRET:881916365 RCS Bobigny, EXO TVA, article 262 DU CGI</p>
                 </div>
             </div>
        </div>
    </div>

    <!-- Print Button Section -->
    <div class="no-print" style="text-align: center; margin: 20px;">
         <a href="javascript:history.back()" class="btn btn-secondary" style="padding: 10px 20px; font-size: 16px; margin-right: 10px; background-color: #6c757d; color:white; text-decoration: none; border-radius: 4px;">Retour</a>
        <button onclick="printAffiche()" style="padding: 10px 20px; font-size: 16px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
            🖨️ Imprimer la facture
        </button>
    </div>

    <script>
        function printAffiche() {
            window.print();
        }
    </script>
@endsection