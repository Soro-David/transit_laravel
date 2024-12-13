@extends('admin.layouts.admin')
@section('content-header')
@endsection
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
                                <div class="table-responsive">
                                    <table id="productTable" class="table table-bordered table-striped display">
                                        <thead>
                                            <tr>
                                                <th>Nom Expéditeur</th>
                                                <th>Contact Expéditeur</th>
                                                <th>Agence Expéditeur</th>
                                                <th>Nom Destinataire</th>
                                                <th>Contact Destinataire</th>
                                                <th>Agence Destinataire</th>
                                                <th>Etat du Colis</th>
                                                <th>Date de Création</th>
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
            </div>
        </form>
    </div>
    {{-- modal  --}}
    <div class="modal fade" id="showModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Valider le colis</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="/users/{id}/edit" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="prix_colis" class="form-label">Prix du Colis</label>
                                    <input type="number" name="prix_colis" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="montant_paye" class="form-label">Montant Payé</label>
                                    <input type="number" name="montant_paye" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="reste" class="form-label">Reste</label>
                                    <input type="number" name="reste" placeholder="-Reste-" class="form-control" disabled required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="referrence" class="form-label">Reférence du colis</label>
                                    <input type="text" name="referrence_colis" class="form-control" disabled required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="reference_contenaire" class="form-label">Reférence du Contenaire</label>
                                    <input type="text" name="reference_contenaire" class="form-control" disabled required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="mode_transit" class="form-label">Mode de Transit</label>
                                    <input type="text" name="mode_transit" class="form-control" required disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="poids" class="form-label">Le Poids du Colis en Kg</label>
                                    <input type="number" name="poids" class="form-control" required disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="valeur_colis" class="form-label">Valeur du Colis</label>
                                    <input type="number" name="valeur_colis" class="form-control" required disabled>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                <button type="submit" class="btn btn-primary">Valider</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script JavaScript -->
<script>
 $(document).ready(function () {
    var table = $("#productTable").DataTable({
        responsive: true, // Active le design réactif pour le tableau
        language: {
            url: "//cdn.datatables.net/plug-ins/2.1.8/i18n/fr-FR.json" // Traduction en français
        },
        ajax: '{{ route("colis.get.colis.hold") }}', // URL pour récupérer les données via AJAX
        columns: [
            { 
                data: null, // Données combinées
                name: 'nom_expediteur',
                render: function (data, type, row) {
                    // Combinaison nom + prénom de l'expéditeur
                    return row.nom_expediteur + ' ' + row.prenom_expediteur;
                }
            },
            { 
                data: 'tel_expediteur', 
                name: 'tel_expediteur' // Correction du nom de la colonne
            },
            { 
                data: 'agence_expedition', 
                name: 'agence_expedition' 
            },
            { 
                data: null, // Données combinées
                name: 'nom_destinataire',
                render: function (data, type, row) {
                    // Combinaison nom + prénom du destinataire
                    return row.nom_destinataire + ' ' + row.prenom_destinataire;
                }
            },
            { 
                data: 'tel_destinataire', 
                name: 'tel_destinataire' // Correction du nom de la colonne
            },
            { 
                data: 'agence_destination', 
                name: 'agence_destination' 
            },
            { 
                data: 'etat', 
                name: 'etat' 
            },
            { 
                data: 'created_at', 
                name: 'created_at' 
            },
            { 
                data: 'action', // Colonne des actions
                name: 'action', 
                orderable: false, // Ne pas permettre de tri sur cette colonne
                searchable: false // Ne pas permettre la recherche sur cette colonne
            }
        ],
        dom: 'Bfrtip', // Barre de boutons pour les fonctionnalités supplémentaires
        buttons: [
            {
                extend: 'excelHtml5', // Export en Excel
                text: 'Exporter en Excel', // Texte du bouton
                title: 'Liste des Colis en attente' // Titre du fichier exporté
            },
            {
                extend: 'pdfHtml5',   // Export en PDF
                text: 'Exporter en PDF', // Texte du bouton
                title: 'Liste des Colis en attente', // Titre du fichier exporté
                orientation: 'landscape', // Orientation du document
                pageSize: 'A4' // Taille de la page
            },
            {
                extend: 'print', // Impression
                text: 'Imprimer', // Texte du bouton
                title: 'Liste des Colis en attente' // Titre de l'impression
            }
        ]
    });
});

</script>
</section>
<style>
    /* ajuster la taille du btn */
    .btn {
        width: 15%;  
        height: 40px;  
        font-size: 18px; 
    }
   /* Centrer le DataTable et ajuster la largeur */
.dataTable-wrapper {
    width: 80% !important;
    margin: 20px auto; 
    padding: 15px; 
    border: 1px solid #ccc; 
    border-radius: 8px; 
    background: #f9f9f9; 
}

/* Style commun pour tous les boutons */
.dt-button {
    padding: 10px 20px; $
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
