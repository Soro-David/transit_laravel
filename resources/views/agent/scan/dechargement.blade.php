@extends('agent.layouts.agent')
@section('content-header')

@endsection
@section('content')
<section class="py-3">
        <form action="" method="POST" class="mt-4">
            @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                            <h4 class="text-left mt-4">Colis dechargé</h4><br>
                            <div id="products-container">
                                <div class="table-responsive">
                                    <table id="productTable" class="table table-bordered table-striped display">
                                        <thead>
                                            <tr>
                                                <th>Reference colis</th>
                                                <th>Nom Expéditeur</th>
                                                <th>Contact Expéditeur</th>
                                                <th>Agence Expéditeur</th>
                                                <th>Nom Destinataire</th>
                                                <th>Contact Destinataire</th>
                                                <th>Agence Destinataire</th>
                                                <th>Date de Création</th>
                                                <th>Action</th>

                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </form>

    <!-- Modal for editing -->
    <div class="modal fade" id="showModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Valider le colis</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="/users/{id}/edit" method="GET">
                        <div class="modal-body">
                            <div class="container">

                                <div class="row">
                                    <h4>Information Expediteur</h4><hr>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Nom Expéditeur</label>
                                            <input type="text" name="nom " id="nom" value="" class="form-control" disabled required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Contact Expéditeur</label>
                                            <input type="text" name="telephone_expediteur" id="telephone_expediteur" value="" class="form-control" disabled required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="expediteur_email" class="form-label">Agence Expéditeur</label>
                                            <input type="text" name="agence_expediteur" id="agence_expediteur" value="" class="form-control" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <h4>Information Destinateur</h4><hr>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Nom Destinataire</label>
                                            <input type="text" name="nom_destinataire" id="nom_destinataire" value="" class="form-control" disabled required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Contact Destinataire</label>
                                            <input type="text" name="telephone_destinataire" id="telephone_destinataire" value="" class="form-control" disabled required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="agence_destinataire" class="form-label">Agence Destinataire</label>
                                            <input type="text" name="agence_destinataire" id="agence_destinataire" value="" class="form-control" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <h4>Information colis</h4><hr>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Quantité de colis</label>
                                            <input type="text" name="quantite_colis" id="quantite_colis" value="" class="form-control" disabled required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Valeur du Colis</label>
                                            <input type="text" name="valeur_colis" id="valeur_colis" value="" class="form-control" disabled  required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="mode_transit" class="form-label">Mode de transit</label>
                                            <input type="text" name="mode_transit" id="mode_transit" value="" class="form-control" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Poids du Colis</label>
                                            <input type="text" name="poids_colis" id="poids_colis" value="" class="form-control" disabled required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Prix du Colis</label>
                                            <input type="text" name="prix_colis" id="prix_colis" value="" class="form-control"  required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="agence_destinateur" class="form-label">Agence Destinataire</label>
                                            <input type="text" name="agence_destinateur" id="agence_destinateur" value="" class="form-control" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary">Valider</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for DataTable and Export -->
    <script>
$(document).ready(function () {
    // Initialisation de la table DataTable
    var table = $("#productTable").DataTable({
        responsive: true,
        language: {
                url: "{{ asset('js/fr-FR.json') }}" // Chemin local vers le fichier
            },
        ajax: '{{ route("agent_scan.get.colis.decharge") }}', // Récupération des données via AJAX
        columns: [
                { data: 'reference_colis' },
                {
                    data: null,
                    render: function (data, type, row) {
                        return row.nom_expediteur + ' ' + row.prenom_expediteur;
                    }
                },
                { data: 'tel_expediteur' },
                { data: 'agence_expedition' },
                {
                    data: null,
                    render: function (data, type, row) {
                        return row.nom_destinataire + ' ' + row.prenom_destinataire;
                    }
                },
                { data: 'tel_destinataire' },
                { data: 'agence_destination' },
                { data: 'created_at',
                    render: function(data, type, row) {
                        // Vérifiez si la date existe et la formater
                        if (data) {
                            var date = new Date(data);
                            // Retourne la date au format aa/mm/jj
                            var day = ('0' + date.getDate()).slice(-2);  // Ajoute un zéro si jour < 10
                            var month = ('0' + (date.getMonth() + 1)).slice(-2);  // +1 car les mois commencent à 0
                            var year = date.getFullYear().toString().slice(-2);  // On garde les deux derniers chiffres de l'année
                            return day + '/' + month + '/' + year;
                        }
                        return data;  // Si la date est vide, on retourne la donnée brute
                    }
                },
                { data: 'action', orderable: false, searchable: false }
            ],
            dom: 'Bfrtip', // Placement des boutons
            buttons: [
                // Bouton Excel
                {
                    extend: 'excelHtml5',
                    text: 'Exporter en Excel',
                    title: 'Liste des Colis en attente',
                    customize: function (xlsx) {
                        console.log("Exportation Excel réussie sans image.");
                    }
                },
                // Bouton PDF
                {
                    extend: 'pdfHtml5',
                    text: 'Exporter en PDF',
                    title: 'Liste des Colis en attente',
                    orientation: 'landscape', // Mode paysage
                    pageSize: 'A4', // Taille de la page
                    customize: function (doc) {
                        // Ajout du logo encodé en Base64 dans le PDF
                        var logoUrl = "{{ url('images/LOGOAFT.png') }}";
                        toDataURL(logoUrl, function (dataUrl) {
                            // Ajout de l'image au début du contenu PDF
                            console.log(dataUrl);
                            doc.content.unshift({
                                image: dataUrl,
                                width: 100, // Taille du logo
                                alignment: 'center',
                                margin: [0, 0, 0, 10] // Espacement
                            });
                        });
                    }
                },
                // Bouton Imprimer
                {
                    extend: 'print',
                    text: 'Imprimer',
                    title: 'Liste des Colis en attente',
                    customize: function (win) {
                        var logoUrl = "{{ url('images/LOGOAFT.png') }}";
                        var logo = '<img src="' + logoUrl + '" alt="Logo" style="position:relative; top:10px; left:20px; width:100px; height:auto;">';
                        $(win.document.body).find('h1')
                            .css('text-align', 'center')
                            .css('margin-top', '10px');
                        $(win.document.body).find('h1').after(logo);
                        $(win.document.body).find('table').css('margin-top', '30px');
                    }
                }
            ]
        });

    });

    </script>
    
    
</section>

<style>
    .btn {
        width: 15%;
        height: 40px;
        font-size: 18px;
    }

    .dataTable-wrapper {
        width: 80% !important;
        margin: 20px auto;
        padding: 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background: #f9f9f9;
    }

    .dt-button {
        padding: 10px 20px;
        margin: 5px;
        border: 1px solid transparent;
        border-radius: 5px;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
        text-transform: uppercase;
        transition: all 0.3s ease;
    }
</style>
@endsection
