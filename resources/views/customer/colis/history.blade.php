@extends('customer.layouts.index')

@section('content-header')
    <h4 class="text-center">Effectuer le paiement pour le colis</h4>
@endsection

@section('content')
<section class="py-3">
    <form action="" method="POST" class="mt-4">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                    <h4 class="text-left mt-4">Historique des colis</h4><br>
                    <div id="products-container">
                        <div class="table-responsive">
                            <table id="productTable" class="display">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Expéditeur</th>
                                        <th>Email</th>
                                        <th>Agence Expéditeur</th>
                                        <th>Destinataire</th>
                                        <th>Email</th>
                                        <th>Agence Destinataire</th>
                                        <th>Prix</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

{{-- Les Modals --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Les informations du colis validé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" action="/users/{id}/edit" method="GET">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom destinataire</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </form>
            </div>
        </div>
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
            url: '{{ route("customer_colis.get.colis.valide") }}',
        },
        columns: [
            { data: 'reference_colis' },
            {
                data: null,
                render: function(data, type, row) {
                    return row.expediteur_nom + ' ' + row.expediteur_prenom;
                }
            },
            { data: 'expediteur_email' },
            { data: 'expediteur_agence' },
            {
                data: null,
                render: function(data, type, row) {
                    return row.destinataire_nom + ' ' + row.destinataire_prenom;
                }
            },
            { data: 'destinataire_email' },
            { data: 'destinataire_agence' },
            { data: 'prix_transit_colis' },
            { data: 'etat' }, // Assurez-vous que ce champ existe dans votre réponse
            { 
                data: 'updated_at',
                render: function(data, type, row) {
                    if (data) {
                        var date = new Date(data);
                        var day = ('0' + date.getDate()).slice(-2);
                        var month = ('0' + (date.getMonth() + 1)).slice(-2);
                        var year = date.getFullYear();
                        var hours = ('0' + date.getHours()).slice(-2);
                        var minutes = ('0' + date.getMinutes()).slice(-2);
                        
                        return day + '/' + month + '/' + year + ' ' + hours + ':' + minutes;
                    }
                    return data;
                }
            },
            { data: 'action', orderable: false, searchable: false }
        ],
    });
});
</script>

{{-- CSS Personnalisé --}}
<style>
    body {
        background-color: #f7f7f7;
    }

    .form-container {
        max-width: 95%;
        margin: auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection