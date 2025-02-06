@extends('AFT_LOUIS_BLERIOT.layouts.agentprint')

@section('content-header')
@endsection

@section('content')
    @csrf
    <section style="background-color: #fff !important; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
        <div id="affiche">
            <div class="header" style="background-color: black; color: white; text-align: center; padding: 10px; font-size: 18px; word-spacing: 30px; letter-spacing: 2px;">
                AFT IMPORT EXPORT
            </div>

            <div class="content">
                <table class="table" style="margin: auto; font-weight: bold; text-align: center; border: 0;">
                    <tr>
                        <td rowspan="2">
                            <img src="{{ asset('images/LOGOAFT.png') }}" alt="Logo" class="img-fluid custom-logo" style="max-height: 90px;">
                        </td>
                        <td>
                            <strong>AFT IMPORT EXPORT<br>
                                7 Avenue Louis BLERIOT, 93120 LA COURNEUVE<br>
                                Phone: 0186786967
                            </strong>
                        </td>
                        <td class="qr">
                            <img class="imageqr" src="{{ asset($filePath) }}" alt="QR Code" style="max-width: 200px; margin: 0 auto; display: block;">
                        </td>
                    </tr>
                </table>

                <!-- Informations sur l'expédition -->
                <table class="table" style="margin: auto; font-weight: bold; text-align: center; border: 1px solid black; width: 100%; border-collapse: collapse;">
                    <tr>
                        <th style="border: 1px solid black; padding: 10px;">Date</th>
                        <th style="border: 1px solid black; padding: 10px;">Destinataire</th>
                        <th style="border: 1px solid black; padding: 10px;">Expéditeur</th>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; padding: 10px;">{{ $colis->created_at }}</td>
                        <td style="border: 1px solid black; padding: 10px;">
                            {{ $colis->destinataire->nom }} {{ $colis->destinataire->prenom }} <br> {{ $colis->destinataire->tel }}
                        </td>
                        <td style="border: 1px solid black; padding: 10px;">
                            {{ $colis->expediteur->nom }} {{ $colis->expediteur->prenom }} <br> {{ $colis->expediteur->tel }}
                        </td>
                    </tr>
                </table>

                <!-- Détails du colis -->
                <table class="table" style="margin: 10px auto; font-weight: bold; text-align: center; border: 1px solid black; width: 100%; border-collapse: collapse;">
                    <tr>
                        <td class="qr" style="border: 1px solid black; padding: 10px;">
                            <img class="imageqr" src="{{ asset($filePath) }}" alt="QR Code" style="max-width: 150px; margin: 0 auto; display: block;">
                            <span>{{ $colis->reference_colis }}</span>
                        </td>
                        <td style="border: 1px solid black; padding: 10px;">
                            <strong>PALETTE ACCESSOIRES MÉDICAUX - {{ strtoupper($colis->expediteur_agence) }}</strong>
                        </td>
                        <td class="bold" style="border: 1px solid black; padding: 10px;">1 / 2</td>
                    </tr>
                </table>
            </div>
        </div>

            <!-- Les boutons sont en dehors de #affiche pour éviter leur impression -->
        <div class="mt-4 no-print" style="display: flex; justify-content: space-between;">
            <a href="javascript:history.back()" class="btn btn-secondary" style="width: 15%; height: 40px; font-size: 18px;">Retour</a>
            <button class="btn btn-primary" onclick="printAffiche()" style="width: 15%; height: 40px; font-size: 18px;">Imprimer</button>
        </div>

    </section>

    <style>
        @media print {
            /* Cache tous les éléments qui ne doivent pas être imprimés */
            .no-print {
                display: none;
            }
            /* Assurer que le fond du header est imprimé */
            .header {
                background-color: black !important;
                color: white !important;
                -webkit-print-color-adjust: exact; /* Pour Chrome */
                print-color-adjust: exact; /* Pour Firefox */
            }
            /* Réduire les marges et autres styles d'impression */
            body {
                margin: 0;
                padding: 0;
            }
            section {
                box-shadow: none;
            }
        }
    </style>

    <script>
        function printAffiche() {
            window.print();
        }
    </script>
@endsection
