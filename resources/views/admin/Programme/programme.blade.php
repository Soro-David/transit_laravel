@extends('admin.layouts.admin')

@section('content-header')
@endsection

@section('content')
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <h1 class="mb-4">Gestion des Programmes</h1>

        <div class="mb-3">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addProgrammeModal">
                Ajouter Programme
            </button>
        </div>

        <div class="modal fade" id="addProgrammeModal" tabindex="-1" role="dialog" aria-labelledby="addProgrammeModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="addProgrammeModalLabel">Créer un Programme</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="{{ route('programme.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="date_programme" class="form-label">Date du Programme:</label>
                                <input type="date" name="date_programme" id="date_programme" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="chauffeur_id" class="form-label">Chauffeur:</label>
                                <select name="chauffeur_id" id="chauffeur_id" class="form-control" required>
                                    <option value="">-- Sélectionner un Chauffeur --</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="reference_colis" class="form-label">Référence Colis :</label>
                                <input type="text" name="reference_colis" id="reference_colis" class="form-control" list="colis-list">
                                <datalist id="colis-list">
                                </datalist>
                            </div>
                            <div class="mb-3">
                                <label for="nom_expediteur" class="form-label">Nom Expéditeur :</label>
                                <input type="text" name="nom_expediteur" id="nom_expediteur" class="form-control" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="nom_destinataire" class="form-label">Nom Destinataire :</label>
                                <input type="text" name="nom_destinataire" id="nom_destinataire" class="form-control" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="lieu_destinataire" class="form-label">Lieu Destinataire :</label>
                                <input type="text" name="lieu_destinataire" id="lieu_destinataire" class="form-control" readonly>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                                <button type="submit" class="btn btn-primary">Enregistrer le Programme</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h2 class="card-title mb-0">Liste des Programmes</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="programmes-table" class="table table-bordered table-striped">
                        <thead class="bg-light">
                        <tr>
                            <th>Date Programme</th>
                            <th>Chauffeur</th>
                            <th>Référence Colis</th>
                            <th>Nom Expéditeur</th>
                            <th>Nom Destinataire</th>
                            <th>Lieu Destinataire</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                         <tbody>
                         <!-- DataTables will load data here -->
                         </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

    <script>
        $(document).ready(function() {
            let dataTable;

            axios.get("{{ route('programme.data') }}")
                .then(function(response) {
                    console.log(response.data);

                    // Remplir le select de chauffeurs
                    var chauffeurs = response.data.chauffeurs;
                    var selectChauffeur = $('#chauffeur_id');
                    selectChauffeur.empty().append(`<option value="">-- Sélectionner un Chauffeur --</option>`);
                    if(Array.isArray(chauffeurs)) {
                        chauffeurs.forEach(chauffeur => {
                            selectChauffeur.append(`<option value="${chauffeur.id}">${chauffeur.nom}</option>`);
                        });
                    } else {
                        console.error('Erreur: chauffeurs n\'est pas un tableau:', chauffeurs);
                    }

                    // Remplir la liste des colis
                    var colisValides = response.data.colisValides;
                    var colisList = $('#colis-list');
                    colisList.empty();
                    if(Array.isArray(colisValides)){
                        colisValides.forEach(colis =>{
                            colisList.append(`<option value="${colis.reference_colis}">${colis.reference_colis}</option>`);
                        });
                    } else {
                        console.error('Erreur: colisValides n\'est pas un tableau:', colisValides);
                    }

                    // Initialiser DataTables avec les données
                    dataTable = $('#programmes-table').DataTable({
                        data: response.data.programmes,
                        columns: [
                            { data: 'date_programme' },
                            { data: 'chauffeur.nom' },
                            { data: 'reference_colis', defaultContent: 'N/A' },
                            { data: 'nom_expediteur', defaultContent: 'N/A' },
                            { data: 'nom_destinataire', defaultContent: 'N/A' },
                            { data: 'lieu_destinataire', defaultContent: 'N/A' },
                            {
                                data: null,
                                render: function(data, type, row) {
                                    return `
                                     <button class="btn btn-sm btn-info">Modifier</button>
                                     <button class="btn btn-sm btn-danger">Supprimer</button>
                                    `;
                                }
                            }
                        ],
                         "language": {
                             "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
                         }
                    });
                  // Gestion de l'événement "input" sur le champ référence colis
                    $('#reference_colis').on('input', function () {
                         var selectedReference = $(this).val();
                         var selectedColis = colisValides.find(colis => colis.reference_colis === selectedReference);

                         if (selectedColis) {
                             $('#nom_expediteur').val(selectedColis.expediteur.nom);
                             $('#nom_destinataire').val(selectedColis.destinataire.nom);
                             $('#lieu_destinataire').val(selectedColis.destinataire.lieu_destination);
                         } else {
                            $('#nom_expediteur').val('');
                            $('#nom_destinataire').val('');
                            $('#lieu_destinataire').val('');
                          }
                   });

                })
                .catch(function(error) {
                    console.error('Erreur de requete:', error);
                });
        });
    </script>
@endsection