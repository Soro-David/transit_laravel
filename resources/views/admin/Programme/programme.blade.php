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

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addProgrammeModal">
                Ajouter Programme
            </button>

            <div class="form-inline">
                <input type="text" id="search" class="form-control mr-2" placeholder="Rechercher...">
            </div>
        </div>
       <div class="mb-3 d-flex justify-content-start align-items-center">
            <div class="mr-2">Afficher par page:</div>
            <div class="dropdown">
              <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="pageSizeDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span id="pageSizeDisplay">10</span>
              </button>
               <div class="dropdown-menu" aria-labelledby="pageSizeDropdown">
                    <a class="dropdown-item page-size-btn" href="#" data-size="10">10</a>
                    <a class="dropdown-item page-size-btn" href="#" data-size="50">50</a>
                   <a class="dropdown-item page-size-btn" href="#" data-size="100">100</a>
                </div>
            </div>
        </div>

        <!-- Modal d'ajout de programme (inchangé) -->
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
                     <table class="table table-bordered table-striped">
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
                         <tbody id="programmes-table">
                           <!-- Programmes générés par js -->
                         </tbody>
                    </table>
                 </div>
                <div class="mt-3 d-flex justify-content-center">
                    <nav aria-label="Page navigation">
                        <ul id="pagination" class="pagination">
                           <!-- Pagination générée par js -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

 <script>
    $(document).ready(function() {

         let programmesData = [];
         let currentPage = 1;
         let itemsPerPage = 10;

        // Fonction pour générer le tableau HTML
        function generateTable(programmes) {
            const tableBody = $('#programmes-table');
            tableBody.empty(); // Vider le tableau avant de le remplir

            if (programmes.length === 0) {
                tableBody.append('<tr><td colspan="7" class="text-center">Aucun programme trouvé.</td></tr>');
                return;
            }
             programmes.forEach(programme => {
                    tableBody.append(`
                        <tr>
                            <td>${programme.date_programme}</td>
                            <td>${programme.chauffeur.nom}</td>
                            <td>${programme.reference_colis || 'N/A'}</td>
                            <td>${programme.nom_expediteur || 'N/A'}</td>
                            <td>${programme.nom_destinataire || 'N/A'}</td>
                            <td>${programme.lieu_destinataire || 'N/A'}</td>
                             <td>
                                 <button class="btn btn-sm btn-info">Modifier</button>
                                 <button class="btn btn-sm btn-danger">Supprimer</button>
                             </td>
                         </tr>
                    `);
                 });
        }
        // Fonction pour générer la pagination
          function generatePagination(totalItems) {
            const totalPages = Math.ceil(totalItems / itemsPerPage);
            const paginationContainer = $('#pagination');
            paginationContainer.empty();
             if (totalPages <= 1) {
                return;
            }

             // Bouton "Précédent"
            const prevButton = $(`<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" aria-label="Précédent">
                        <span aria-hidden="true">«</span>
                    </a>
                </li>`);
            prevButton.on('click', function(e) {
                    e.preventDefault();
                    if (currentPage > 1) {
                        currentPage--;
                        updateTable();
                    }
            });
             paginationContainer.append(prevButton);


            // Afficher les numéros de page
            for (let i = 1; i <= totalPages; i++) {
                const pageButton = $(`<li class="page-item ${currentPage === i ? 'active' : ''}">
                    <a class="page-link" href="#">${i}</a>
                    </li>`);
                pageButton.on('click', function(e) {
                    e.preventDefault();
                    currentPage = i;
                    updateTable();
                });
                paginationContainer.append(pageButton);
             }


            // Bouton "Suivant"
            const nextButton = $(`<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" aria-label="Suivant">
                        <span aria-hidden="true">»</span>
                    </a>
                </li>`);
                nextButton.on('click', function(e) {
                    e.preventDefault();
                    if (currentPage < totalPages) {
                        currentPage++;
                        updateTable();
                    }
                });
                paginationContainer.append(nextButton);
         }

          // Fonction pour mettre à jour le tableau en fonction de la page actuelle et du terme de recherche
          function updateTable() {
            const searchTerm = $('#search').val().toLowerCase();
            let filteredProgrammes = programmesData;

            if (searchTerm) {
                filteredProgrammes = programmesData.filter(programme =>
                   programme.date_programme.toLowerCase().includes(searchTerm) ||
                   programme.chauffeur.nom.toLowerCase().includes(searchTerm) ||
                   (programme.reference_colis && programme.reference_colis.toLowerCase().includes(searchTerm)) ||
                   (programme.nom_expediteur && programme.nom_expediteur.toLowerCase().includes(searchTerm)) ||
                   (programme.nom_destinataire && programme.nom_destinataire.toLowerCase().includes(searchTerm)) ||
                   (programme.lieu_destinataire && programme.lieu_destinataire.toLowerCase().includes(searchTerm))
                );
           }
           const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
             const programmesToDisplay = filteredProgrammes.slice(startIndex, endIndex);
            generateTable(programmesToDisplay);
            generatePagination(filteredProgrammes.length);
        }

        // Chargement initial des données
         axios.get("{{ route('programme.data') }}")
            .then(function(response) {
                console.log(response.data);
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

                // mise à jour de la liste des colis
                var colisValides = response.data.colisValides;
                var colisList = $('#colis-list');
                colisList.empty(); // Vider la datalist avant de la remplir
                if(Array.isArray(colisValides)){
                    colisValides.forEach(colis =>{
                        colisList.append(`<option value="${colis.reference_colis}">${colis.reference_colis}</option>`);
                    });
                } else {
                    console.error('Erreur: colisValides n\'est pas un tableau:', colisValides);
                }
                 programmesData = response.data.programmes; // Stockez toutes les données
                 updateTable();

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
            .catch(function(error){
                console.error('Erreur de requete:',error);
            });

        // Recherche en temps réel
        $('#search').on('input', function() {
            currentPage = 1;
            updateTable();
        });


        // Gestion du dropdown de la taille de la page
        $('.page-size-btn').on('click', function(e) {
             e.preventDefault();
            itemsPerPage = parseInt($(this).data('size'));
            currentPage = 1;
            $('#pageSizeDisplay').text(itemsPerPage);
            updateTable();
          });
    });
</script>
@endsection