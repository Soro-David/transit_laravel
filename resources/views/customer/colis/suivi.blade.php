@extends('customer.layouts.index')
@section('content-header')
@section('content')
<section class="py-3">
    <form action="" method="POST" class="mt-4">
        @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                        <h4 class="text-left mt-4">Suivi des colis</h4><br>
                        <div id="products-container">
                            <div id="products-container">
                                <table id="productTable" class="display">
                                    <thead>
                                        <tr>
                                            <th>Reference colis</th>
                                            <th>Nom Expéditeur</th>
                                            <th>Email Expéditeur</th>
                                            <th>Agence Expéditeur</th>
                                            <th>Nom Destinataire</th>
                                            <th>Email Destinataire</th>
                                            <th>Agence Destinataire</th>
                                            <th> Status</th>
                                            <th> Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="container">
                                <h6 class="text-right mt-4">Prix total : <span id="prix-total">0</span> FCFA</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </form>
</div>
<!-- Script JavaScript -->
<script>
    $(document).ready(function() {
    var table = $("#productTable").DataTable({
        responsive: true,
        language: {
                url: "{{ asset('js/fr-FR.json') }}" // Chemin local vers le fichier
            },
        ajax: {
            url: '{{ route("customer_colis.get.colis.suivi") }}',

        },
        columns: [
            { data: 'reference_colis' },
            {
                data: null,
                render: function (data, type, row) {
                    return row.expediteur_nom + ' ' + row.expediteur_prenom;
                }
            },
            { data: 'expediteur_email' },
            { data: 'expediteur_agence' },
            {
                data: null,
                render: function (data, type, row) {
                    return row.destinataire_nom + ' ' + row.destinataire_prenom;
                }
            },
            { data: 'destinataire_email' },
            { data: 'destinataire_agence' },
            { data: 'etat' },
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
    $(".add-product").on("click", function() {
        var description = $("#description").val();
        var quantite = $("#quantite").val();
        var dimension = $("#dimension").val();
        var prix = $("#prix").val();
        if (description && quantite && dimension && prix) {
            $.ajax({
                url: '{{ route("colis.store") }}',
                method: "POST",
                data: {
                    description: description,
                    quantite: quantite,
                    dimension: dimension,
                    prix: prix,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    table.row
                        .add([
                            response.description,
                            response.quantite,
                            response.dimension,
                            response.prix
                        ])
                        .draw(false);
                }
            });
        }
    });
});
</script>
@endsection
