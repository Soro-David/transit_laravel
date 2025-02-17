@extends('AGENCE_CHINE.layouts.agentprint')

@section('content-header')
@endsection

@section('content')
    @csrf

    <div class="form-container text-center">
        <div class="titre_facture text-center text-white rounded p-2 my-4">
            <h2 class="m-0 fw-bold">Facture</h2>
        </div>               
        <table class="table border-0">
            <tr>
                <td class=" td_logo border-0 text-start">
                    <img src="{{ asset('images/LOGOAFT.png') }}" alt="Logo" class="img-fluid custom-logo">
                    <h3 class="mt-2">AFT IMPORT EXPORT</h3>
                    <p>7 Avenue Louis BLERIOT, 93120 LA COURNEUVE</p>
                    <p>Tel: 0186786967 | Email: contact@aft-import-export.net</p>
                    <p>Siret: 881916365 00011</p>
                </td>
                <td class="border-0"></td>
                <td class="td_droite border-0 text-start">
                    <h4>Facture Pour : {{$expediteur}}</h4>
                    <p><strong></strong></p>
                    <p>Téléphone :{{$tel_expediteur}}</p>
                    {{-- <h4 class="mt-4">Détails de la Facture</h4> --}}
                    <table class="table_1 table-bordered border">
                        <tr>
                            <td class="td_orange">Numéro Facture :</td>
                            <td>{{$numero_facture}}</td>
                        </tr>
                        <tr>
                            <td class="td_orange" >Date :</td>
                            <td>{{$date_facture}}</td>
                        </tr>
                        <tr>
                            <td class="td_orange" >Référence Dossier :</td>
                            <td>{{$reference_colis}}</td>
                        </tr>
                    </table>                          
                </td>
            </tr>
        </table>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="td_orange" >Description</th>
                    <th class="td_orange" >Type du colis</th>
                    <th class="td_orange" >Quantité</th>
                    <th class="td_orange" > Prix</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($colisData as $colis)
                    <tr>
                        <td>{{ $colis['description'] }}</td>
                        <td>{{ $colis['type_colis'] }}</td>
                        <td>{{ $colis['quantite'] }}</td>
                        <td>{{ $colis['prix_transit_colis']}}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <table class="table_2 table-bordered">
                    <tbody>
                        <tr>
                            <td class="td_orange td_total_2">Prix Total :</td>
                            <td>{{$prix_total}}</td>
                        </tr>
                        <tr>
                            <td class="td_orange td_total_2 ">Montant Payé :</td>
                            <td>{{$montant_paye}}</td>
                        </tr>
                        <tr>
                            <td class="td_orange td_total_2 ">Montant Restant :</td>
                            <td>{{$reste}}</td>
                        </tr>
                        <tr>
                            <td  class="td_orange td_total_2 ">Mode de paiement :</td>
                            <td>{{$mode_payement}}</td>
                        </tr>
                    </tbody>
                </table>
            </tfoot>               
        </table><br>
        <table class="table table-bordered border" style="width: 100%;">
            <tr >
                <td class="td_orange d_total_2">R.I.B</td>
                <td class="td_orange d_total_2">Expéditeur</td>
                <td class="td_orange d_total_2">Destinataire</td>
            </tr>
            <tr>
                <td>Ligne 2, Colonne 1</td>
                <td>{{$expediteur}}</td>
                <td>{{$destinataire}}</td>
            </tr>
        </table>
    
        <div class="mt-4">
            <h4 class="text-center">CONDITION DE VENTE</h4>
            <p class="text-center sous_titre" >
                * PÉNALITÉS DE RETARD (TAUX ANNUEL) : 10,00% - ESCOMPTE POUR PAIEMENT ANTICIPÉ (TAUX MENSUEL) : 1,50%. INDEMNITÉ FORFAITAIRE POUR FRAIS DE RECOUVREMENT EN CAS DE RETARD DE PAIEMENT : 40,00 EUROS. RÉSERVE DE PROPRIÉTÉ : NOUS NOUS RÉSERVONS LA PROPRIÉTÉ DES MARCHANDISES JUSQU'À LE PAIEMENT DU PRIX PAR L'ACHETEUR. NOTRE DROIT DE REVENDICATION PORTE AUSSI BIEN SUR LES MARCHANDISES QUE SUR LEUR PRIX SI ELLES ONT DÉJÀ ÉTÉ REVENDUES (Loi du 12 mai 1980).
            </p>
        </div>
    
        <div class="mt-4">
            <h4 class="text-center">AFT IMPORT EXPORT</h4>
            <p class="text-center sous_titre" >
                7 Avenue Louis BLERIOT 93120 LA COURNEUVE | Tel. 0186786967 | contact@aft-import-export.net | Siret: 881916365 00011 |.
            </p>
        </div>
    
        <div class="div_btn mt-4 no-print">
            <button class=" btn_print btn btn-primary" onclick="printAffiche()">Imprimer</button>
        </div>
    </div>
</div>

@endsection
<style>
    body {
        background-color: #fffbfb;
    }
    .form-container {
        max-width: 95%;
        margin: auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    .table{
        width: 100%;
    }
    .table th {
    padding: 50px 15px;
    }
    .sous_titre{
        font-size: 12px; 
        font-style: italic;
    }

    .table_2{
        width: 40%;
        padding: 20px;
        /* height: 20px; */
    }
    
    .table_1{
        width: 100%;
        padding: 20px;
        /* height: 20px; */
    }
    .custom-logo{
        max-height: 160px; 
        width: auto;
    }
    .td_orange{
        width: 30%;
        /* background-color: orange; */
        color: black;
        font-weight: bold;
    }
    .btn_print{
        width: 15%; 
        height: 50px; 
        font-size: 24px;
    }
    .td_total{
        padding: 20px;
        width: 200px; 
        border: 10px solid transparent;
        
    }
    .td_total_2{
        padding: 8px;
        width: 200px; 
        border: 10px solid transparent;
    }
    .titre_facture{
        background-color: rgb(218, 214, 207); 
        border: 2px solid #e0e0e0;
    }
    .td_logo{
        width: 45%; 
        vertical-align: top;
    }
    .td_droite{
        width: 45%; 
        vertical-align: top;
    }
    .tr_rib{
        background-color: orange;
    }
    .div_btn{
        display: flex; 
        justify-content: flex-end;
    }
</style>

<script>
    function printAffiche() {
        window.print();
    }
</script>