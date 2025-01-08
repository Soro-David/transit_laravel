@extends('agent.layouts.agent')
@section('content-header')
@endsection

@section('content')
<section class="py-3">
    <div class="form-container">
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
                                <div class="container">
                                    <h6 class="text-right mt-4">Prix total : <span id="prix-total">0</span> FCFA</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="border p-4 rounded shadow-sm">
                                            <div class="mb-3">
                                                <label for="commentaire1" class="form-label">Commentaire</label>
                                                <textarea name="commentaire1" id="commentaire1" rows="5" class="form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-8">
                                            <label for="reference_contenaire" class="form-label">Référence Contenaire</label>
                                            <input type="text" name="reference_contenaire" id="reference_contenaire" placeholder="Référence du contenaire" class="form-control">
                                        </div>
                                    </div>
                                    <div class="container text-right">
                                        <button type="submit" class="btn btn-danger mt-3">
                                            <i class="fas fa-times"></i> Fermer contenaire
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Script JavaScript -->
<script>
   $(document).ready(function () {
    var table = $("#productTable").DataTable({
        responsive: true, // Rend le tableau réactif
        language: {
            url: "//cdn.datatables.net/plug-ins/2.1.8/i18n/fr-FR.json" // Traduction en français
        },
        ajax: '{{ route("agent_colis.get.colis.hold") }}', // URL pour récupérer les données
        columns: [
            {
                data: null,
                name: 'nom_expediteur',
                render: function (data, type, row) {
                    return row.nom_expediteur + ' ' + row.prenom_expediteur; // Nom complet expéditeur
                }
            },
            { data: 'tel_expediteur', name: 'contact_expediteur' },
            { data: 'agence_expedition', name: 'agence_expedition' },
            {
                data: null,
                name: 'nom_destinataire',
                render: function (data, type, row) {
                    return row.nom_destinataire + ' ' + row.prenom_destinataire; // Nom complet destinataire
                }
            },
            { data: 'tel_destinataire', name: 'contact_destinataire' },
            { data: 'agence_destination', name: 'agence_destination' },
            { data: 'etat', name: 'etat' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        dom: 'Bfrtip', // Active les boutons et positionne les contrôles
        buttons: [
            {
                extend: 'excelHtml5', 
                text: 'Exporter en Excel', 
                title: 'Liste des devis en attente' 
            },
            {
                extend: 'pdfHtml5',   
                text: 'Exporter en PDF', 
                title: 'Liste des devis en attente', 
                orientation: 'landscape', 
                pageSize: 'A4' 
            },
            {
                extend: 'print', 
                text: 'Imprimer', 
                title: 'Liste des devis en attente',
                customize: function (win) {
                            var logoUrl = "{{ asset('images/LOGOAFT.png') }}";
                            var logo = '<img src="' + logoUrl + '" alt="Logo" style="position:relative; top:10px; left:20px; width:100px; height:auto;">';
                            $(win.document.body).find('h1').css('text-align', 'center').css('border', '2px solid #000').css('padding', '1px').css('margin-top', '10px');
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
    body {
        background-color: #f7f7f7;
    }

    .form-container {
        max-width: 97%;
        margin:auto;
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

    section {
        margin-top: 0px;
    }
</style>
@endsection
