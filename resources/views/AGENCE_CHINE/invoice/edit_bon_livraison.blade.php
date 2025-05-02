@extends('AGENCE_CHINE.layouts.agentprint')

@section('content-header')
@endsection

@section('content')
    @csrf 

    <div class="form-container">
        <div class="header-section text-center mb-4">
             <img src="{{ asset('images/LOGOAFT.png') }}" alt="Logo AFT IMPORT EXPORT" style="max-height: 80px; width: auto;" class="mb-2">
            <h3>AFT IMPORT EXPORT</h3>
            <p class="mb-0" style="font-size: 0.9em;">VOTRE INTERMÉDIAIRE CRÉDIBLE</p>
            <p class="mb-0" style="font-size: 0.8em;">7 Avenue Louis BLERIOT, 93120 LA COURNEUVE</p>
            <p style="font-size: 0.8em;">Tel: 0186786967</p>
        </div><br><br><br>

        <div class="text-center p-2 mb-4" style="background-color: #ADD8E6; border: 1px solid #ccc;">
            <h4 class="mb-0">BON DE LIVRAISON</h4>
        </div>

        {{-- Recipient Details --}}
        <div class="recipient-details mb-4" style="line-height: 1.6;">
            <table class="table table-borderless table-sm" style="width: 50%;">
                <tr>
                    <td style="width: 40%; font-weight: bold;">Reference dossier:</td>
                    <td>{{ $reference_colis ?? 'vide' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">NOM :</td>
                    <td>{{ $expediteur ?? 'vide' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">TELEPHONE :</td>
                    <td>{{ $tel_expediteur ?? '00000000' }}</td>
                </tr>
            </table>
        </div><br><br><br>

        <table class="table table-bordered mb-4">
            <thead class="text-center" style="background-color: #f2f2f2;">
                <tr>
                    <th style="width: 5%;">QUANTITE</th>
                    <th style="width: 35%;">DESCRIPTION PRODUITS</th>
                    <th style="width: 40%;">COMMENTAIRE</th>
                    <th style="width: 20%;">STATUS PAYEMENT</th>
                    <th style="width: 15%;">LIVRÉ ?</th>
                </tr>
            </thead>
            <tbody>
                @if($colisCollection->count() > 0)
                    @foreach ($colisCollection as $colisItem)
                        <tr>
                            <td>{{ $colisItem->quantite_colis ?? ' ' }}</td>
                            <td>{{ $colisItem->service ?? 'Non spécifiée' }}</td>
                            <td>{{ $colisItem->description_colis ?? '' }}</td>
                            <td>{{ $colisItem->status ?? 'COLIS EN RÉCEPTION' }}</td>
                            <td>{{ $colisItem->livre ? 'Oui' : 'Non' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="text-center">Aucun colis trouvé pour cette référence.</td>
                    </tr>
                @endif
            </tbody>
        </table>
        

        <div class="footer-section mt-5">
             <div class="mb-5">
                {{-- <strong>DATE DE LIVRAISON :</strong> le {{ $date_livraison ?? '... / .... / .......' }} Use variable or placeholder --}}
            </div>

            <table class="table table-borderless" style="width: 100%;">
                 <tr>
                    <td style="width: 50%; vertical-align: bottom;" class="text-start">
                        Fait à {{ $lieu_fait ?? 'Abidjan' }}, le {{ $date_facture ?? '... / .... / .......' }} {{-- Use variable or default/placeholder --}}
                         <br><br><br>
                        <p><strong>Signature client</strong></p>
                    </td>
                    <td style="width: 50%; vertical-align: bottom;" class="text-end">
                        <br><br><br> {{-- Align vertically --}}
                         <p><strong>Signature</strong></p> {{-- Assuming company signature --}}
                    </td>
                </tr>
            </table>
        </div>
        <div class="mt-4 no-print" style="display: flex; justify-content: space-between;">
            <a href="javascript:history.back()" class="btn btn-secondary" style="width: 15%; height: 50px; font-size: 20px; line-height: 30px;">Retour</a>
            <button class="btn_print btn btn-primary" onclick="printAffiche()" style="width: 15%; height: 50px; font-size: 20px;">Imprimer</button>
        </div>
    </div>

@endsection

<style>
    body {
         background-color: #fff;
         font-family: Arial, sans-serif; /* Common font */
    }
    .form-container {
        max-width: 800px; /* Standard A4-like width */
        margin: auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 5px; /* Subtle rounding */
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* Softer shadow */
    }
    .table {
        width: 100%;
        margin-bottom: 1rem; /* Consistent spacing */
        color: #212529; /* Standard text color */
        border-collapse: collapse; /* Clean borders */
    }

    .table th,
    .table td {
        padding: 0.5rem; /* Adjust padding */
        vertical-align: top;
        border-top: 1px solid #dee2e6; /* Standard bootstrap border color */
    }

    .table thead th {
        vertical-align: bottom;
        border-bottom: 2px solid #dee2e6;
         background-color: #e9ecef; /* Light grey header */
         font-weight: bold;
    }

     .table-bordered th,
    .table-bordered td {
        border: 1px solid #dee2e6;
    }

    .table-borderless th,
    .table-borderless td {
        border: 0;
    }
     .table-sm th,
    .table-sm td {
        padding: 0.3rem; /* Smaller padding for compact tables */
    }

    .sous_titre {
        font-size: 10px; /* Smaller font size for footer notes */
        font-style: italic;
        color: #6c757d; /* Grey color for less emphasis */
    }

    .custom-logo {
        max-height: 80px; /* Adjusted logo size */
        width: auto;
    }

     .btn_print {
        /* width: 15%; */ /* Let button size naturally */
        padding: 10px 20px;
        height: auto;
        font-size: 16px; /* Standard button font size */
    }
     .btn-secondary{
         padding: 10px 20px;
         height: auto;
         font-size: 16px;
     }

    .titre_facture { /* Renamed from original, but reusing style concept */
        background-color: #e0e0e0; /* Light grey background */
        border: 1px solid #ccc;
    }

    /* Ensure styles are applied for printing */
    @media print {
        body {
            background-color: #fff; /* Ensure white background for print */
        }
        .form-container {
            box-shadow: none; /* Remove shadow for print */
            border-radius: 0;
            padding: 0; /* Remove padding if margins are handled by browser */
            max-width: 100%; /* Use full width */
        }
        .no-print {
            display: none !important; /* Hide buttons when printing */
        }
         /* Add more print-specific styles if needed */
        table {
             page-break-inside: auto; /* Allow tables to break across pages if necessary */
        }
         tr {
             page-break-inside: avoid;
             page-break-after: auto;
        }
         thead {
             display: table-header-group; /* Repeat table headers on each page */
        }
         .table td, .table th {
            font-size: 9pt; /* Smaller font for print if needed */
         }
         h3, h4{
            font-size: 12pt;
         }
         p{
            font-size: 9pt;
         }
    }

</style>

{{-- Keep the existing script block --}}
<script>
    function printAffiche() {
        window.print();
    }
</script>