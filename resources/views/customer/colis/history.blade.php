@extends('customer.layouts.index')
@section('content-header')
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
                                            <th>Reference colis</th>
                                            <th>Nom Expéditeur</th>
                                            <th>Email Expéditeur</th>
                                            <th>Agence Expéditeur</th>
                                            <th>Nom Destinataire</th>
                                            <th>Email Destinataire</th>
                                            <th>Agence Destinataire</th>
                                            {{-- <th> Status</th> --}}
                                            <th> Etat</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        <h6 class="text-right mt-4">Prix total : <span id="prix-total">0</span> FCFA</h6>
                        </div>
                    </div>
                </div>
            </div>
    </form>
    </div>
</div>

{{-- les Modals --}}
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
            // type: 'GET', // Méthode HTTP pour récupérer les données
            // dataType: 'json' // Type de données attendu
        },
        columns: [
            { data: 'reference_colis' },
            {
                data: null,
                render: function (data, type, row) {
                    return row.nom_expediteur + ' ' + row.prenom_expediteur;
                }
            },
            { data: 'expediteur_email' },
            { data: 'expediteur_agence' },
            {
                data: null,
                render: function (data, type, row) {
                    return row.nom_destinataire + ' ' + row.prenom_destinataire;
                }
            },
            { data: 'destinataire_email' },
            { data: 'destinataire_agence' },
            { data: 'created_at' },
            { data: 'action', orderable: false, searchable: false }
        ],
    })
    });

        
</script>


@endsection
