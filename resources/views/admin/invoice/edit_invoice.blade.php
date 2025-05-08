{{-- resources/views/admin/colis/add/edit_invoice.blade.php --}}
@extends('admin.layouts.adminprint')

@section('content-header')

@endsection

@section('content')
    @csrf

    <style>
        /* ... Your existing CSS ... */
         body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #333;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }

        .invoice-box {
            max-width: 1000px;
            margin: 15px auto;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            background-color: #fff;
            font-size: 12px;
            line-height: 1.6;
        }

        /* --- Rest of your CSS --- */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .logo img {
            max-width: 180px;
            height: auto;
        }
        .company-details-header {
            text-align: right;
        }
        .company-details-header h2 {
            margin: 0 0 5px 0;
            font-size: 18px;
            font-weight: bold;
        }
        .company-details-header p {
            margin: 0;
            font-size: 11px;
        }

        .invoice-title-section {
            text-align: center;
            margin-bottom: 30px;
        }
        .invoice-title-section h1 {
            font-size: 28px;
            font-weight: bold;
            margin: 0 0 5px 0;
            color: #555;
            letter-spacing: 1px;
        }
        .simulated-barcode {
            height: 30px;
            background: linear-gradient(to right,
                #333 0%, #333 2px, transparent 2px, transparent 4px,
                #333 4px, #333 5px, transparent 5px, transparent 7px,
                #333 7px, #333 10px, transparent 10px, transparent 11px,
                #333 11px, #333 12px, transparent 12px, transparent 14px
            );
            background-repeat: repeat-x;
            background-size: 14px 100%;
            max-width: 200px;
            margin: 5px auto 0;
        }
        .simulated-barcode-small {
            height: 20px;
            background: linear-gradient(to right,
                #333 0%, #333 1.5px, transparent 1.5px, transparent 3px,
                #333 3px, #333 4px, transparent 4px, transparent 5.5px,
                #333 5.5px, #333 7.5px, transparent 7.5px, transparent 8.5px,
                #333 8.5px, #333 9.5px, transparent 9.5px, transparent 11px
            );
            background-repeat: repeat-x;
            background-size: 11px 100%;
            max-width: 150px;
            margin: 5px 0 0 auto;
        }


        .client-invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .client-details {
            max-width: 50%;
        }
        .client-details h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
            font-weight: bold;
        }
        .client-details p {
            margin: 2px 0;
            font-size: 12px;
        }
        .invoice-meta table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-meta td {
            padding: 5px 8px;
            font-size: 12px;
        }
        .invoice-meta td:first-child {
            text-align: left;
            font-weight: bold;
            background-color: #f9f9f9;
            border: 1px solid #eee;
            width: 40%;
        }
        .invoice-meta td:last-child {
            text-align: right;
            border: 1px solid #eee;
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .references-section {
            margin-bottom: 30px;
            border: 1px solid #eee;
        }
        .references-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .references-section th, .references-section td {
            border: 1px solid #eee;
            padding: 6px 8px;
            font-size: 11px;
            text-align: center;
        }
        .references-section th {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .references-section td {
            height: 20px;
        }


        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
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
            font-size: 10px;
            color: #666;
            padding-left: 10px;
            margin-top: 3px;
        }
        .item-main-service {
            font-weight: bold;
        }


        .totals-summary {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #eee;
            margin-bottom: 30px;
        }
        .totals-summary table {
            width: 40%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals-summary td {
            padding: 6px 8px;
            font-size: 12px;
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
            min-width: 120px;
        }
        .grand-total-header {
            background-color: #e0e0e0 !important;
            font-size: 13px !important;
        }

        .payment-notes-section {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            margin-bottom: 20px;
            align-items: flex-start;
        }
        .payment-terms table {
            width: auto;
            border-collapse: collapse;
        }
        .payment-terms td {
            padding: 5px 8px;
            font-size: 12px;
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
            margin-left: 20px;
        }
        .notes-section textarea {
            width: 100%;
            min-height: 60px;
            border: 1px solid #eee;
            padding: 5px;
            font-size: 11px;
            box-sizing: border-box;
            resize: vertical;
        }
        .notes-section p {
            margin: 0 0 5px 0;
            font-weight: bold;
        }


        .final-totals {
            margin-top: 10px;
            padding-top: 10px;
            margin-bottom: 10px;
        }
        .final-totals table {
            width: 40%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .final-totals td {
            padding: 8px;
            font-size: 13px;
            font-weight: bold;
        }
        .final-totals td:first-child {
            text-align: right;
        }
        .final-totals td:last-child {
            text-align: right;
            background-color: #e0e0e0;
            border: 1px solid #ccc;
            min-width: 120px;
        }
        .final-totals .reste-a-payer td:last-child {
             background-color: #d0d0d0;
        }



        .conditions {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            margin-bottom: 30px;
        }
        .conditions h4 {
            margin: 0 0 8px 0;
            font-size: 13px;
            font-weight: bold;
        }
        .conditions p {
            font-size: 10px;
            line-height: 1.4;
            color: #555;
            text-align: justify;
        }

        .footer-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #333;
            font-size: 9px;
            color: #555;
        }
        .footer-generation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .footer-company-details {
            text-align: center;
            line-height: 1.3;
        }
        .footer-company-details p {
            margin: 1px 0;
        }
        .footer-company-details strong {
            color: #333;
        }


        .text-bold { font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }


        @media print {
            @page {
                margin: 0;
            }
            body, html {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                height: auto !important;
                background-color: #fff !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                font-size: 10pt;
            }
            .invoice-box-container {
                margin: 0 !important;
                padding: 0 !important;
            }
            .invoice-box {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 10mm !important;
                box-shadow: none !important;
                border: none !important;
                page-break-inside: avoid;
                box-sizing: border-box !important;
            }

            .no-print, .no-print * {
                display: none !important;
            }

            .header-section, .invoice-title-section, .client-invoice-details,
            .references-section, .items-table, .totals-summary,
            .payment-notes-section, .final-totals, .conditions {
                margin-bottom: 10px !important;
                margin-top: 5px !important;
                padding-top: 0 !important;
                padding-bottom: 0 !important;
            }
            .simulated-barcode-small {
                margin-top: 5px !important;
                margin-bottom: 5px !important;
            }
            .footer-section {
                margin-top: 15px !important;
                padding-top: 10px !important;
                margin-bottom: 0 !important;
                padding-bottom: 0 !important;
                page-break-before: auto;
                background-color: #fff !important;
            }
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
                     <h2 style="font-size: 25px;">AFT IMPORT EXPORT</h2>
                     <p>7 AVENUE LOUIS BLERIOT LA COURNEUVE</p>
                     <p>93120 France</p>
                     <p>Tel. +33171894351</p>
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
                        <th class="col-produit">Description Colis / Service</th>
                        <th class="col-qty">Qté</th>
                        <th class="col-price">P.U. (FCFA)</th>
                        <th class="col-montant">Montant (FCFA)</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Use the grouped data ($invoiceItems) passed from controller --}}
                    @forelse ($invoiceItems as $item)
                    <tr>
                        <td>
                            {{-- Display the Service/Description from the grouped item --}}
                            <span class="item-main-service">{{ $item['service'] ?? 'N/A' }}</span>
                             {{-- Optionally display type colis if relevant and stored in group --}}
                             {{-- <div class="item-description">Type: {{ $item['type_colis'] ?? 'N/A' }}</div> --}}
                        </td>
                        {{-- Display aggregated quantity --}}
                        <td class="col-qty">{{ number_format($item['quantite_totale'] ?? 0, 0, ',', ' ') }}</td>
                        {{-- Display unit price for the group --}}
                        <td class="col-price">{{ number_format($item['prix_unitaire'] ?? 0, 0, ',', ' ') }}</td>
                        {{-- Display aggregated total amount for the line --}}
                        <td class="col-montant">{{ number_format($item['montant_total_ligne'] ?? 0, 0, ',', ' ') }}</td>
                    </tr>
                    @empty
                         {{-- Fallback if no items - consider showing a message or leaving empty --}}
                        <tr>
                            <td colspan="4" style="text-align: center;">Aucun article trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="simulated-barcode-small" style="max-width: 300px; margin: 15px 0 15px auto;"></div>


            <!-- Totals Summary -->
            <div class="totals-summary">
                <table>
                    {{-- Use $prix_total which is the sum of all lines calculated in controller --}}
                    <tr><td>Sous total</td><td>{{ number_format($prix_total ?? 0, 0, ',', ' ') }}</td></tr>
                    <tr><td>Montant total (FCFA)</td><td class="grand-total-header">{{ number_format($prix_total ?? 0, 0, ',', ' ') }}</td></tr>
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
                     <textarea readonly>{{-- $notes_variable ?? '' --}}</textarea>
                </div>
            </div>

            <!-- Final Totals -->
             <div class="final-totals">
                 <table>
                     <tr><td>Total (FCFA)</td><td>{{ number_format($prix_total ?? 0, 0, ',', ' ') }}</td></tr>
                     <tr><td>Total Payé (FCFA)</td><td>{{ number_format($totalMontantPaye ?? 0, 0, ',', ' ') }}</td></tr>
                     <tr class="reste-a-payer"><td>Reste à payer (FCFA)</td><td>{{ number_format($restePaye ?? 0, 0, ',', ' ') }}</td></tr>
                 </table>
                 <div class="simulated-barcode-small" style="max-width: 300px; margin: 10px 0 0 auto;"></div>
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
            console.log('printAffiche function called');
            window.print();
        }
        console.log('Print script for invoice loaded.');
    </script>
@endsection