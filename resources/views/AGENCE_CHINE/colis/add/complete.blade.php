@extends('agent.layouts.agentprint')

@section('content-header')
@endsection

@section('content')
    @csrf
    <section style="background-color: #fff !important; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
        @foreach($colis as $index => $colisItem)
            <div id="affiche" style="width: 100%; max-width: 420mm; height: auto; padding: 20px; page-break-after: always;">
                <div class="header" style="background-color: black; color: white; text-align: center; padding: 20px; font-size: 36px; word-spacing: 50px; letter-spacing: 3px;">
                    AFT IMPORT EXPORT
                </div>

                <div class="content">
                    <!-- Tableau pour le logo, les informations et le QR code -->
                    <table class="table" style="width: 100%; margin: auto; font-weight: bold; text-align: center; border: 0;">
                        <tr>
                            <td rowspan="2" style="width: 30%;">
                                <img src="{{ asset('images/LOGOAFT.png') }}" alt="Logo" class="img-fluid custom-logo" style="max-height: 180px; width: auto;">
                            </td>
                            <td style="width: 40%; font-size: 28px;">
                                <strong>AFT IMPORT EXPORT<br>
                                    7 Avenue Louis BLERIOT, 93120 LA COURNEUVE<br>
                                    Phone: 0186786967
                                </strong>
                            </td>
                            <td class="qr" style="width: 30%;">
                                @if(!empty($colisItem->qr_code_path))
                                    <img class="imageqr" src="{{ asset($colisItem->qr_code_path) }}" alt="QR Code" style="max-height: 180px; width: auto;">
                                @else
                                    <p style="font-size: 24px;">QR Code non disponible</p>
                                @endif
                            </td>
                        </tr>
                    </table>

                    <!-- Tableau pour les informations du colis -->
                    <table class="table" style="width: 100%; margin: 30px auto; font-weight: bold; text-align: center; border: 2px solid black; border-collapse: collapse; font-size: 28px;">
                        <tr>
                            <th style="border: 2px solid black; padding: 20px;">Date</th>
                            <th style="border: 2px solid black; padding: 20px;">Destinataire</th>
                            <th style="border: 2px solid black; padding: 20px;">Expéditeur</th>
                        </tr>
                        <tr>
                            <td style="border: 2px solid black; padding: 20px;">{{ $colisItem->created_at }}</td>
                            <td style="border: 2px solid black; padding: 20px;">
                                {{ $colisItem->destinataire->nom }} {{ $colisItem->destinataire->prenom }} <br> {{ $colisItem->destinataire->tel }}
                            </td>
                            <td style="border: 2px solid black; padding: 20px;">
                                {{ $colisItem->expediteur->nom }} {{ $colisItem->expediteur->prenom }} <br> {{ $colisItem->expediteur->tel }}
                            </td>
                        </tr>
                    </table>

                    <!-- Tableau pour le QR code et les détails supplémentaires -->
                    <table class="table" style="width: 100%; margin: 40px auto; font-weight: bold; text-align: center; border: 2px solid black; border-collapse: collapse; font-size: 28px;">
                        <tr>
                            <td style="border: 2px solid black; padding: 20px;">
                                <img class="imageqr" src="{{ asset($colisItem->qr_code_path) }}" alt="QR Code" style="max-width: 250px; width: 100%; height: auto; margin: 0 auto; display: block;">
                                <span>{{ $colisItem->reference_colis }}</span>
                            </td>
                            <td style="border: 2px solid black; padding: 20px;">
                                {{-- @dd($colisItem->type_colis); --}}
                                <strong><span style="text-decoration: underline;">TYPE DE COLIS:</span> {{ $colisItem->type_colis }} </strong><br>
                                <strong><span style="text-decoration: underline;">DESCRIPTION DU COLIS:</span> {{ $colisItem->description_colis }}- {{ strtoupper($colisItem->expediteur_agence) }}</strong>
                            </td>
                            <td style="border: 2px solid black; padding: 20px;">{{ $index + 1 }} / {{ count($colis) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        @endforeach
    </section>

    <!-- Boutons pour retourner et imprimer -->
    <div class="mt-4 no-print" style="display: flex; justify-content: space-between;">
        <a href="{{ route('colis.create.step1') }}" class="btn btn-secondary" style="width: 15%; height: 50px; font-size: 24px;">Retour</a>
        <button class="btn btn-primary" onclick="printAffiche()" style="width: 15%; height: 50px; font-size: 24px;">Imprimer</button>
    </div>

    <!-- Styles pour l'impression et le responsive -->
    <style>
        body {
            background-color: #f7f7f7;
        }

        fieldset + fieldset {
            border-top: 2px solid #ccc;
            padding-top: 15px;
            margin-top: 15px;
        }

        .form-container {
            max-width: 95%;
            margin: auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .form-section {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        @media print {
            @page {
                size: A2 portrait;
                margin: 0;
            }
            .no-print {
                display: none;
            }
            .header {
                background-color: black !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            body {
                margin: 0;
                padding: 0;
            }
            section {
                box-shadow: none;
            }
            #affiche {
                page-break-after: always;
            }
        }

        /* Styles pour les écrans de petite taille */
        @media screen and (max-width: 768px) {
            .header {
                font-size: 15px !important;
                padding: 10px !important;
                word-spacing: 10px !important;
                letter-spacing: 1px !important;
            }

            .content table {
                font-size: 15px !important;
            }

            .content td, .content th {
                padding: 10px !important;
            }

            .btn {
                width: 100% !important;
                margin-bottom: 10px;
                font-size: 15px !important;
            }

            .img-fluid.custom-logo, .imageqr {
                max-height: 120px !important;
            }

            .table {
                width: 100% !important;
                margin: 10px auto !important;
            }

            .table td, .table th {
                padding: 10px !important;
            }
        }

        /* Styles pour les écrans de taille moyenne */
        @media screen and (min-width: 769px) and (max-width: 1024px) {
            .header {
                font-size: 15px !important;
                padding: 15px !important;
                word-spacing: 20px !important;
                letter-spacing: 2px !important;
            }

            .content table {
                font-size: 15px !important;
            }

            .content td, .content th {
                padding: 15px !important;
            }

            .btn {
                width: 20% !important;
                font-size: 15px !important;
            }

            .img-fluid.custom-logo, .imageqr {
                max-height: 150px !important;
            }
        }
    </style>

    <!-- Script pour l'impression -->
    <script>
        function printAffiche() {
            window.print();
        }
    </script>
@endsection