@extends('customer.layouts.index')
@section('content-header')
@section('content')
<section class="py-3">
<div class="container">
    <form action="" method="POST" class="mt-4">
        @csrf
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                        <h4 class="text-left mt-4">Liste des colis en attente</h4><br>
                        <div id="products-container">
                            <table id="productTable" class="display">
                                <thead>
                                    <tr>
                                        <th>Nom Expéditeur</th>
                                        <th>Prenom Expéditeur</th>
                                        <th>Email Expediteur</th>
                                        <th>Agence Expéditeur</th>
                                        <th>Agence Destinataire</th>
                                        <th> Status</th>
                                        <th> Etat</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <div class="container">
                                <h6 class="text-right mt-4">Prix total : <span id="prix-total">0</span> FCFA</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    </div>
</div>
<!-- Script JavaScript -->
<script>
   $(document).ready(function() {
    var table = $("#productTable").DataTable({
        responsive: true, // Rend la table responsive
        language: {
            url: "//cdn.datatables.net/plug-ins/1.12.1/i18n/fr-FR.json"
        },
        ajax: {
            url: '{{ route("customer_colis.get.colis") }}',
            // type: 'GET', // Méthode HTTP pour récupérer les données
            // dataType: 'json' // Type de données attendu
        },
        columns: [
            { data: 'nom_expediteur', name: 'nom_expediteur' },
            { data: 'prenom_expediteur', name: 'prenom_expediteur' },
            { data: 'email_expediteur', name: 'email_expediteur' },
            { data: 'agence_expedition', name: 'agence_expedition' },
            { data: 'agence_destination', name: 'agence_destination' },
            { data: 'status', name: 'status' },
            { data: 'etat', name: 'etat' },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false // Actions non triables et non recherchables
            }
        ]
    });

    // $(".add-product").on("click", function() {
    //     var description = $("#description").val();
    //     var quantite = $("#quantite").val();
    //     var dimension = $("#dimension").val();
    //     var prix = $("#prix").val();
    //     if (description && quantite && dimension && prix) {
    //         $.ajax({
    //             url: '{{ route("customer_colis.get.colis") }}',
    //             method: "POST",
    //             data: {
    //                 description: description,
    //                 quantite: quantite,
    //                 dimension: dimension,
    //                 prix: prix,
    //                 _token: "{{ csrf_token() }}"
    //             },
    //             success: function(response) {
    //                 table.row
    //                     .add([
    //                         response.description,
    //                         response.quantite,
    //                         response.dimension,
    //                         response.prix
    //                     ])
    //                     .draw(false);
    //             }
    //         });
    //     }
    // });
});
</script>
@endsection
