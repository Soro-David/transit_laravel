<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Étiquettes Colis AFT</title>
    {{-- Le CSS reste identique à celui que tu as fourni --}}
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        html, body {
             font-family: Arial, Helvetica, sans-serif; /* Utiliser une police safe comme DejaVu Sans si problèmes caractères */
             /* font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; */
             font-size: 9pt;
             line-height: 1.2;
        }
        @page {
            size: A6 landscape; /* Format et orientation */
            margin: 5mm; /* Marge uniforme simplifiée */
        }
        .etiquette-page {
            width: 138.5mm; /* Largeur A6 paysage - 2*5mm marge */
            height: 95mm;  /* Hauteur A6 paysage - 2*5mm marge */
            overflow: hidden;
            display: block;
            page-break-after: always;
            border: 1px dotted #ccc; /* Pour visualiser la zone pendant le dev, à enlever en prod */
            position: relative; /* Pour positionner les éléments si besoin */
        }
        .etiquette-page:last-child {
            page-break-after: avoid;
        }
        .etiquette-header {
            background-color: #000 !important; /* Important pour forcer le style dans le PDF */
            color: #fff !important;
            text-align: center;
            padding: 3px 0;
            font-weight: bold;
            font-size: 12pt;
            letter-spacing: 5px;
            margin-bottom: 4mm;
        }
        .etiquette-content {
            /* Pas besoin de flex ici si on positionne avec margin */
             padding: 0 5mm; /* Ajout padding latéral si besoin dans le contenu */
        }

        /* ... (le reste de ton CSS existant, vérifier les unités mm/pt) ... */
        .info-header-table,
        .details-table,
        .reference-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4mm;
            table-layout: fixed; /* Important pour le rendu PDF */
        }
         .reference-table {
             margin-bottom: 0;
             min-height: 20mm;
             /* Positionner en bas si nécessaire */
             /* position: absolute; */
             /* bottom: 5mm; */
             /* left: 5mm; */
             /* right: 5mm; */
         }
        /* Section 1: Logo / Adresse / QR */
        .info-header-table td {
            vertical-align: top; /* Top peut être mieux pour l'alignement */
            padding: 0 1mm;
            border: none;
        }
        .info-header-table .logo-cell { width: 33%; text-align: left; }
        .info-header-table .address-cell { width: 34%; text-align: center; }
        .info-header-table .qr-cell { width: 33%; text-align: right; }
         .custom-logo { max-width: 90%; height: 18mm; object-fit: contain;} /* Ajuster height/width */
         .address-details { font-size: 8pt; font-weight: normal; line-height: 1.2; }
         .address-details strong { font-size: 9pt; display: block; margin-bottom: 1mm; font-weight: bold;}
         .qr-code-img-header { max-width: 22mm; max-height: 22mm; display: block; margin-left: auto; }
         .qr-placeholder { font-size: 8pt; color: #666; text-align: center; padding: 5mm 0; border: 1px dashed #ccc; height: 22mm; display:flex; align-items:center; justify-content:center; }

        /* Section 2: Table Détails */
        .details-table { border: 1.5pt solid #000; }
        .details-table th {
            background-color: #E0E0E0 !important; /* Important pour PDF */
            color: #000 !important;
            font-weight: bold;
            font-size: 8pt; /* Plus petit pour les entêtes */
            padding: 1mm 1.5mm;
            border: 0.5pt solid #000;
            text-align: center;
        }
        .details-table td {
            border: 0.5pt solid #000;
            padding: 1.5mm 2mm; /* Augmenter légèrement le padding */
            vertical-align: top;
            font-size: 10pt; /* Un peu plus petit pour laisser de la place */
            font-weight: bold;
            line-height: 1.2; /* Augmenter interligne */
            word-wrap: break-word; /* Important */
        }
        .details-table .date-cell { width: 22%; text-align: center; font-size: 10pt;}
        .details-table .dest-cell { width: 48%; }
        .details-table .exp-cell { width: 30%; }
        .data-value { display: block; }
        .sub-info { font-size: 9pt; font-weight: normal; margin-top: 1mm; }

         /* Section 3: Référence / Compteur */
        .reference-table { border: 1.5pt solid #000; }
        .reference-table td {
            vertical-align: middle;
            padding: 1mm 2mm; /* Ajuster padding */
            font-size: 10pt; /* Taille de base */
            font-weight: bold;
            border: 0.5pt solid #000;
        }
        .reference-table .ref-cell { width: 70%; border-right: 1pt solid #000; text-align: left; }
        .reference-table .count-cell { width: 30%; text-align: center; vertical-align: middle;}
        .qr-code-img-ref {
            max-width: 12mm; max-height: 12mm;
            display: inline-block; vertical-align: middle; margin-right: 2mm;
        }
        .reference-number {
            font-size: 22pt; /* Ajuster taille si besoin */
            font-weight: bold; color: #000;
            display: inline-block; vertical-align: middle;
            line-height: 1;
            word-break: break-all; /* Casser les longues références */
        }
        .type-colis-info {
            font-size: 8pt; font-weight: normal; display: block;
            word-wrap: break-word; margin-top: 1mm; text-align: center; /* Centrer type colis */
        }
        .counter-text {
            font-size: 26pt; font-weight: bold; display: block;
            line-height: 1; margin-bottom: 1mm;
        }
        .destination-text { /* Pas utilisé dans le HTML fourni, mais défini */
            font-size: 9pt; font-weight: bold; display: block;
        }
        /* Utilitaires pour PDF */
        .text-center { text-align: center; }
        img { max-width: 100%; height: auto; } /* S'assurer que les images ne dépassent pas */

    </style>
</head>
<body>
    {{-- La variable $colis est maintenant la collection de clones passée par le contrôleur --}}
    {{-- La variable $totalEtiquettes est passée par le contrôleur --}}
    @if($colis && !$colis->isEmpty() && isset($totalEtiquettes))
        @php $currentIndex = 1; @endphp

        {{-- La boucle @foreach itère sur chaque clone, générant une page par clone --}}
        @foreach($colis as $colisItem)
            @php
                // Accéder aux relations directement depuis $colisItem (le clone)
                $destinataire = $colisItem->destinataire ?? null;
                $expediteur = $colisItem->expediteur ?? null;
                // $expediteurVille = optional($expediteur)->ville ?? 'ABIDJAN'; // Si besoin
                // $destinationVille = $colisItem->destination_ville ?? 'ABIDJAN'; // Si besoin
                $qrCodePath = $colisItem->qr_code_path ?? null; // Utilise le chemin enregistré
                $colisReference = $colisItem->reference_colis ?? 'N/A';
                $colisType = $colisItem->type_colis ?? 'N/A';
                // $quantite n'est plus nécessaire ici car on boucle déjà le bon nombre de fois
            @endphp

            {{-- Chaque itération de cette boucle génère une étiquette complète --}}
            <div class="etiquette-page">
                <div class="etiquette-header">
                    A F T   I M P O R T   E X P O R T {{-- Utiliser   pour forcer espace --}}
                </div>

                <div class="etiquette-content"> {{-- Pas besoin de with-padding si @page a des marges --}}

                    <table class="info-header-table">
                        <tr>
                            <td class="logo-cell">
                                {{-- Utiliser file_exists avec le chemin absolu --}}
                                @if(file_exists(public_path('images/LOGOAFT.png')))
                                    <img src="{{ public_path('images/LOGOAFT.png') }}" alt="Logo" class="custom-logo">
                                @else
                                    <p>Logo absent</p>
                                @endif
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
                                @else
                                    <div class="qr-placeholder">QR Code<br>Absent</div>
                                @endif
                            </td>
                        </tr>
                    </table>

                    <table class="details-table">
                        <thead>
                            <tr>
                                <th>DATE</th>
                                <th>DESTINATAIRE</th>
                                <th>EXPEDITEUR</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="date-cell">
                                    <span class="data-value">{{ $colisItem->created_at ? $colisItem->created_at->format('d/m/Y') : 'N/A' }}</span> {{-- Format Date FR --}}
                                    <span class="data-value">{{ $colisItem->created_at ? $colisItem->created_at->format('H:i') : '' }}</span> {{-- Heure H:i --}}
                                </td>
                                <td class="dest-cell">
                                    <span class="data-value">{{ optional($destinataire)->nom }} {{ optional($destinataire)->prenom }}</span>
                                    <span class="data-value">{{ optional($destinataire)->tel ? str_replace([' ', '-'], '', $destinataire->tel) : 'N/A' }}</span>
                                     {{-- Ajouter adresse si besoin --}}
                                     {{-- <span class="sub-info">{{ optional($destinataire)->adresse }}</span> --}}
                                </td>
                                <td class="exp-cell">
                                    <span class="data-value">{{ optional($expediteur)->nom }} {{ optional($expediteur)->prenom }}</span>
                                    <span class="sub-info">{{ optional($expediteur)->tel ? str_replace([' ', '-'], '', $expediteur->tel) : 'N/A' }}</span>
                                     {{-- Ajouter adresse si besoin --}}
                                     {{-- <span class="sub-info">{{ optional($expediteur)->adresse }}</span> --}}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="reference-table">
                        <tr>
                            <td class="ref-cell">
                                {{-- Conteneur pour QR et Référence --}}
                                <div style="display: block; text-align: center; margin-bottom: 2mm;"> {{-- Ajuster l'alignement si besoin --}}
                                    @if($qrCodePath && file_exists(public_path(ltrim($qrCodePath, '/'))))
                                        <img class="qr-code-img-ref" src="{{ public_path(ltrim($qrCodePath, '/')) }}" alt="QR Ref">
                                    @endif
                                    <span class="reference-number">{{ $colisReference }}</span>
                                </div>
                                <div class="type-colis-info">{{ $colisType }}</div>
                            </td>
                            <td class="count-cell">
                                <span class="counter-text">{{ $currentIndex }} / {{ $totalEtiquettes }}</span>
                                {{-- Ajouter destination si besoin --}}
                                {{-- <span class="destination-text">{{ optional($destinataire)->agence ?? 'N/A' }}</span> --}}
                            </td>
                        </tr>
                    </table>
                </div> {{-- Fin etiquette-content --}}
            </div> {{-- Fin etiquette-page --}}

            @php $currentIndex++; @endphp {{-- Incrémenter le compteur pour la prochaine étiquette --}}

        @endforeach

    @else
        {{-- Message si aucun colis ou totalEtiquettes non défini --}}
        <div class="etiquette-page">
            <p style="text-align: center; padding-top: 20mm; font-weight: bold;">
                Aucune étiquette à générer pour ce colis ou données manquantes.
            </p>
        </div>
    @endif
</body>
</html>