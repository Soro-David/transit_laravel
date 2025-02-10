@extends('customer.layouts.index')
@section('content-header')
@section('content')
<section class="py-3">
    <form action="" method="POST" class="mt-4">
        @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                        <h4 class="text-left mt-4">Liste des colis en attente</h4><br>
                        <div id="products-container">
                            <div class="table-responsive">
                                <table id="productTable" class="display">
                                    <thead>
                                        <tr>
                                            <th>Référence</th>
                                            {{-- <th>Nom Expéditeur</th>
                                            <th>Email Expéditeur</th> --}}
                                            <th>Agence Expédition</th>
                                            <th>Destinataire</th>
                                            {{-- <th>Email Destinataire</th> --}}
                                            <th>Téléphone</th>
                                            <th>Agence Destination</th>
                                            <th> Status</th>
                                            {{-- <th> Etat du colis</th> --}}
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            {{-- <div class="container">
                                <h6 class="text-right mt-4">Prix total : <span id="prix-total">0</span> FCFA</h6>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
    </form>
</div>
{{-- CSS Personnalisé --}}
<style>
      .btn-delete {
    background-color: #f44336; /* Couleur rouge */
    color: white; /* Texte blanc */
    border: none; /* Pas de bordure */
    padding: 5px 20px; /* Espacement */
    border-radius: 5px; /* Coins arrondis */
    text-decoration: none; /* Pas de soulignement */
}

.btn-delete:hover {
    background-color: #ec0909; /* Couleur rouge plus foncé au survol */
}
    .form-container {
        max-width: 850px;
        margin: auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    .form-section {
        margin-bottom: 20px;
    }
    .payment-section {
        border: 1px solid #ddd;
        padding: 15px;
        border-radius: 5px;
        margin-top: 10px;
        display: none;
    }
</style>


<!-- Script JavaScript -->
<script>
   $(document).ready(function() {
    var table = $("#productTable").DataTable({
        responsive: true, // Rend la table responsive
        language: {
                url: "{{ asset('js/fr-FR.json') }}" // Chemin local vers le fichier de traduction
            },
        ajax: {
            url: '{{ route("customer_colis.get.colis") }}',

        },
        columns: [
            { data: 'reference_colis' },
            // {
            //     data: null,
            //     render: function (data, type, row) {
            //         return row.expediteur_nom + ' ' + row.expediteur_prenom;
            //     }
            // },
            // { data: 'expediteur_email' },
            { data: 'expediteur_agence' },
            {
                data: null,
                render: function (data, type, row) {
                    return row.destinataire_nom + ' ' + row.destinataire_prenom;
                }
            },
            { data: 'destinataire_tel' },
            { data: 'destinataire_agence' },
            // { data: 'etat' },
            { 
                data: 'updated_at',
                render: function(data, type, row) {
                    // Vérifiez si la date existe et la formater
                    if (data) {
                        var date = new Date(data);
                        // Retourne la date au format jj/mm/aa hh:mm
                        var day = ('0' + date.getDate()).slice(-2);  // Ajoute un zéro si jour < 10
                        var month = ('0' + (date.getMonth() + 1)).slice(-2);  // +1 car les mois commencent à 0
                        var year = date.getFullYear();  // On garde l'année complète
                        var hours = ('0' + date.getHours()).slice(-2);  // Ajoute un zéro si heure < 10
                        var minutes = ('0' + date.getMinutes()).slice(-2);  // Ajoute un zéro si minute < 10
                        
                        return day + '/' + month + '/' + year + ' ' + hours + ':' + minutes;  // Format final
                    }
                    return data;  // Si la date est vide, on retourne la donnée brute
                }
            },
            { data: 'action', orderable: false, searchable: false }
        ],
    });
});
</script>
@endsection
