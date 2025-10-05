@extends('AGENCE_CHINE.layouts.agent')

@section('content-header')

@section('content')
    <div class="container">
       

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

      <!-- Modal d'ajout de programme -->
<div class="modal fade" id="addProgrammeModal" tabindex="-1" role="dialog" aria-labelledby="addProgrammeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addProgrammeModalLabel">Créer un Programme</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('chine_programme.store') }}" novalidate>
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_programme" class="form-label">Date du Programme:</label>
                                <input type="date" name="date_programme" id="date_programme" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user_id" class="form-label">Chauffeur:</label>
                                <select name="user_id" id="user_id" class="form-control" required>
                                    <option value="">-- Sélectionner un Chauffeur --</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="programme-entries-container">
                        <!-- Premier bloc de programme -->
                        <div class="programme-entry card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="actions_a_faire_0" class="form-label">Actions à faire :</label>
                                            <select name="actions_a_faire[]" class="form-control required-field" required>
                                                <option value="">-- Sélectionner une action --</option>
                                                <option value="depot">Dépôt</option>
                                                <option value="recuperation">Récupération</option>
                                                <option value="livraison">Livraison</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="reference_colis_0" class="form-label">Référence Colis :</label>
                                            <input type="text" name="reference_colis[]" class="form-control reference_colis" 
                                                   data-index="0" id="reference_colis_0">
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="nature_du_colis_0" class="form-label">Nature du Colis :</label>
                                            <input type="text" name="nature_du_colis[]" class="form-control" id="nature_du_colis_0">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="border-bottom pb-2">Informations Expéditeur</h6>
                                        <div class="mb-3">
                                            <label for="nom_expediteur_0" class="form-label">Nom :</label>
                                            <input type="text" name="nom_expediteur[]" class="form-control" >
                                        </div>
                                        <div class="mb-3">
                                            <label for="Adresse_expedition_0" class="form-label">Adresse d'enlèvement :</label>
                                            <input type="text" name="Adresse_expedition[]" class="form-control" >
                                        </div>
                                        <div class="mb-3">
                                            <label for="tel_expediteur_0" class="form-label">Téléphone :</label>
                                            <input type="text" name="tel_expediteur[]" class="form-control" >
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h6 class="border-bottom pb-2">Informations Destinataire</h6>
                                        <div class="mb-3">
                                            <label for="nom_destinataire_0" class="form-label">Nom :</label>
                                            <input type="text" name="nom_destinataire[]" class="form-control" >
                                        </div>
                                        <div class="mb-3">
                                            <label for="tel_destinataire_0" class="form-label">Téléphone :</label>
                                            <input type="text" name="tel_destinataire[]" class="form-control" >
                                        </div>
                                        <div class="mb-3">
                                            <label for="Adresse_destination_0" class="form-label">Adresse Destination :</label>
                                            <input type="text" name="Adresse_destination[]" class="form-control" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-success" id="add-programme-entry">
                            <i class="fa fa-plus"></i> Ajouter un Colis
                        </button>
                        <button type="button" class="btn btn-danger" id="remove-programme-entry" style="margin-left: 10px;">
                            <i class="fa fa-minus"></i> Retirer le Dernier
                        </button>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">Enregistrer les Programmes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

        {{-- Modal d'édition de programme --}}
        <div class="modal fade" id="editProgrammeModal" tabindex="-1" role="dialog" aria-labelledby="editProgrammeModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editProgrammeModalLabel">Modifier le Programme</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editProgrammeForm" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="programme_id" id="edit_programme_id">
                            <div class="mb-3">
                                <label for="edit_date_programme" class="form-label">Date du Programme:</label>
                                <input type="date" name="date_programme" id="edit_date_programme" class="form-control" data-modified="false">
                            </div>
                            <div class="mb-3">
                                <label for="edit_user_id" class="form-label">Chauffeur:</label>
                                <select name="user_id" id="edit_user_id" class="form-control" data-modified="false">
                                    <option value="">-- Sélectionner un Chauffeur --</option>
                                </select>
                            </div>
                            <div class="mb-3">
                            <label for="edit_reference_colis">Référence Colis :</label>
                            <input type="text" name="reference_colis" class="form-control" id="edit_reference_colis">
                            </div>
                            <div class="mb-3">
                                <label for="edit_actions_a_faire" class="form-label">Actions à faire :</label>
                                <select name="actions_a_faire" id="edit_actions_a_faire" class="form-control" data-modified="false">
                                    <option value="">-- Sélectionner une action --</option>
                                    <option value="depot">Dépôt</option>
                                    <option value="recuperation">Récupération</option>
                                    <option value="livraison">Livraison</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_nature_du_colis">Nature du Colis :</label>
                                <input type="text" name="nature_du_colis" class="form-control" id="edit_nature_du_colis">
                            </div>
                            <div class="mb-3">
                                <label for="edit_etat_rdv" class="form-label">Etat du RDV:</label>
                                <input type="text" class="form-control" id="edit_etat_rdv" readonly>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
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
                            <th>Nature du Colis</th>
                            <th>Actions à faire</th>
                            <th>Nom Expéditeur</th>
                            <th>Adresse Expédition</th>
                            <th>Téléphone Expéditeur</th>
                            <th>Nom Destinataire</th>
                            <th>Téléphone Destinataire</th>
                            <th>Adresse Destination</th>
                            <th>Etat RDV</th>
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
    <datalist id="colis-list"></datalist>
    {{-- <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://kit.fontawesome.com/your-Font-Awesome-Kit-ID.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
        let programmesData = { programmes: [], colisValides: [] };
        let currentPage = 1;
        let itemsPerPage = 10;

        $('#remove-programme-entry').hide();

        function generateTable(programmes) {
            const tableBody = $('#programmes-table');
            tableBody.empty();

            if (programmes.length === 0) {
                tableBody.append('<tr><td colspan="13" class="text-center">Aucun programme trouvé.</td></tr>');
                return;
            }

            programmes.forEach(programme => {
                let rowClass = '';
                if (programme.etat_rdv === 'effectué') {
                    rowClass = 'table-success';
                } else if (programme.etat_rdv === 'à replanifié') {
                    rowClass = 'table-warning';
                }
                
                tableBody.append(`
                    <tr class="${rowClass}">
                        <td>${programme.date_programme}</td>
                        <td>${programme.user ? programme.user.first_name + ' ' + programme.user.last_name : 'N/A'}</td>
                        <td>${programme.reference_colis || 'N/A'}</td>
                        <td>${programme.nature_du_colis || 'N/A'}</td>
                        <td>${programme.actions_a_faire || 'N/A'}</td>
                        <td>${programme.nom_expediteur || 'N/A'}</td>
                        <td>${programme.Adresse_expedition || 'N/A'}</td>
                        <td>${programme.tel_expediteur || 'N/A'}</td>
                        <td>${programme.nom_destinataire || 'N/A'}</td>
                        <td>${programme.tel_destinataire || 'N/A'}</td>
                        <td>${programme.Adresse_destination || 'N/A'}</td>
                        <td>${programme.etat_rdv || 'N/A'}</td>
                        <td>
                            <button class="btn btn-sm btn-info edit-programme-btn" data-id="${programme.id}">Modifier</button>
                            <button class="btn btn-sm btn-danger delete-programme-btn" data-id="${programme.id}">Supprimer</button>
                        </td>
                    </tr>
                `);
            });
        }

        
        function generatePagination(totalItems) {
            const totalPages = Math.ceil(totalItems / itemsPerPage);
            const paginationContainer = $('#pagination');
            paginationContainer.empty();
            if (totalPages <= 1) return;

            const prevButton = $(`<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" aria-label="Précédent"><span aria-hidden="true">«</span></a></li>`);
            prevButton.on('click', () => { if (currentPage > 1) { currentPage--; updateTable(); } });
            paginationContainer.append(prevButton);

            for (let i = 1; i <= totalPages; i++) {
                const pageButton = $(`<li class="page-item ${currentPage === i ? 'active' : ''}"><a class="page-link" href="#">${i}</a></li>`);
                pageButton.on('click', () => { currentPage = i; updateTable(); });
                paginationContainer.append(pageButton);
            }

            const nextButton = $(`<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" aria-label="Suivant"><span aria-hidden="true">»</span></a></li>`);
            nextButton.on('click', () => { if (currentPage < totalPages) { currentPage++; updateTable(); } });
            paginationContainer.append(nextButton);
        }

        function updateTable() {
            const searchTerm = $('#search').val().toLowerCase();
            const filteredProgrammes = programmesData.programmes.filter(programme =>
                Object.values(programme).some(value => 
                    value && value.toString().toLowerCase().includes(searchTerm)
                ) ||
                (programme.user && 
                 (programme.user.first_name.toLowerCase().includes(searchTerm) ||
                  programme.user.last_name.toLowerCase().includes(searchTerm))
                )
            );
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const programmesToDisplay = filteredProgrammes.slice(startIndex, endIndex);
            generateTable(programmesToDisplay);
            generatePagination(filteredProgrammes.length);
        }

        axios.get("{{ route('chine_programme.data') }}")
            .then(function(response) {
                programmesData.programmes = response.data.programmes;
                programmesData.colisValides = response.data.colisValides;

                // Mettre à jour les selects des chauffeurs (users)
                var chauffeurs = response.data.chauffeurs;
                var selectChauffeurAdd = $('#user_id');
                var selectChauffeurEdit = $('#edit_user_id');
                
                selectChauffeurAdd.empty().append('<option value="">-- Sélectionner un Chauffeur --</option>');
                selectChauffeurEdit.empty().append('<option value="">-- Sélectionner un Chauffeur --</option>');
                
                chauffeurs.forEach(chauffeur => {
                    selectChauffeurAdd.append(`<option value="${chauffeur.id}">${chauffeur.first_name} ${chauffeur.last_name}</option>`);
                    selectChauffeurEdit.append(`<option value="${chauffeur.id}">${chauffeur.first_name} ${chauffeur.last_name}</option>`);
                });

                var colisList = $('#colis-list');
                colisList.empty();
                response.data.colisValides.forEach(colis => {
                    colisList.append(`<option value="${colis.reference_colis}">${colis.reference_colis}</option>`);
                });

                updateTable();
            })
            .catch(function(error) {
                console.error('Erreur de requête:', error);
            });

        // Recherche
        $('#search').on('input', function() {
            currentPage = 1;
            updateTable();
        });

        // Pagination
        $('.page-size-btn').on('click', function(e) {
            e.preventDefault();
            itemsPerPage = parseInt($(this).data('size'));
            currentPage = 1;
            $('#pageSizeDisplay').text(itemsPerPage);
            updateTable();
        });
        $('#add-programme-entry').on('click', function() {
            const index = $('.programme-entry').length;
            let newEntry = `
                <div class="programme-entry card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="actions_a_faire_${index}" class="form-label">Actions à faire :</label>
                                    <select name="actions_a_faire[]" class="form-control required-field" required>
                                        <option value="">-- Sélectionner une action --</option>
                                        <option value="depot">Dépôt</option>
                                        <option value="recuperation">Récupération</option>
                                        <option value="livraison">Livraison</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reference_colis_${index}" class="form-label">Référence Colis :</label>
                                    <input type="text" name="reference_colis[]" class="form-control reference_colis" 
                                           data-index="${index}" id="reference_colis_${index}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="nature_du_colis_${index}" class="form-label">Nature du Colis :</label>
                                    <input type="text" name="nature_du_colis[]" class="form-control" id="nature_du_colis_${index}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="border-bottom pb-2">Informations Expéditeur</h6>
                                <div class="mb-3">
                                    <label for="nom_expediteur_${index}" class="form-label">Nom :</label>
                                    <input type="text" name="nom_expediteur[]" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="Adresse_expedition_${index}" class="form-label">Adresse d'enlèvement :</label>
                                    <input type="text" name="Adresse_expedition[]" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="tel_expediteur_${index}" class="form-label">Téléphone :</label>
                                    <input type="text" name="tel_expediteur[]" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="border-bottom pb-2">Informations Destinataire</h6>
                                <div class="mb-3">
                                    <label for="nom_destinataire_${index}" class="form-label">Nom :</label>
                                    <input type="text" name="nom_destinataire[]" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="tel_destinataire_${index}" class="form-label">Téléphone :</label>
                                    <input type="text" name="tel_destinataire[]" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="Adresse_destination_${index}" class="form-label">Adresse Destination :</label>
                                    <input type="text" name="Adresse_destination[]" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('#programme-entries-container').append(newEntry);
            $('#remove-programme-entry').show();
        });
                 // Gestion de la suppression des entrées de programme
                 $('#remove-programme-entry').on('click', function() {
            const entries = $('.programme-entry');
            if (entries.length > 1) {
                entries.last().remove();
                if ($('.programme-entry').length === 1) {
                    $('#remove-programme-entry').hide();
                }
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Action impossible',
                    text: 'Vous ne pouvez pas supprimer la dernière entrée',
                    timer: 2000
                });
            }
        });
        $(document).on('input', '.reference_colis', function() {
            const entry = $(this).closest('.programme-entry');
            const selectedReference = $(this).val();
            const index = $(this).data('index');
            const action = entry.find('select[name="actions_a_faire[]"]').val();

            if (selectedReference.length >= 3 && action !== 'recuperation') {
        axios.get(`/admin/colis/getColisInfo/${selectedReference}`)
            .then(response => {
                const colis = response.data;
                if (colis) {
                    // Remplir les champs avec les données du colis
                    $(`input[name="nom_expediteur[]"]:eq(${index})`).val(colis.expediteur.nom + ' ' + colis.expediteur.prenom);
                    $(`input[name="Adresse_expedition[]"]:eq(${index})`).val(colis.expediteur.lieu_expedition);
                    $(`input[name="tel_expediteur[]"]:eq(${index})`).val(colis.expediteur.tel);
                    $(`input[name="nom_destinataire[]"]:eq(${index})`).val(colis.destinataire.nom + ' ' + colis.destinataire.prenom);
                    $(`input[name="tel_destinataire[]"]:eq(${index})`).val(colis.destinataire.tel);
                    $(`input[name="Adresse_destination[]"]:eq(${index})`).val(colis.destinataire.lieu_destination);

                    // Si c'est une récupération, rendre les champs éditables
                    if (action === 'recuperation') {
                        entry.find('input[name="nom_expediteur[]"]').prop('readonly', false);
                        entry.find('input[name="Adresse_expedition[]"]').prop('readonly', false);
                        entry.find('input[name="tel_expediteur[]"]').prop('readonly', false);
                    }
                } else {
                    // Effacer les champs si le colis n'est pas trouvé
                    $(`input[name="nom_expediteur[]"]:eq(${index})`).val('');
                    $(`input[name="Adresse_expedition[]"]:eq(${index})`).val('');
                    $(`input[name="tel_expediteur[]"]:eq(${index})`).val('');
                    $(`input[name="nom_destinataire[]"]:eq(${index})`).val('');
                    $(`input[name="tel_destinataire[]"]:eq(${index})`).val('');
                    $(`input[name="Adresse_destination[]"]:eq(${index})`).val('');
                }
            })
            .catch(error => {
                console.error('Erreur lors de la récupération des informations du colis:', error);
                // Effacer les champs en cas d'erreur
                $(`input[name="nom_expediteur[]"]:eq(${index})`).val('');
                $(`input[name="Adresse_expedition[]"]:eq(${index})`).val('');
                $(`input[name="tel_expediteur[]"]:eq(${index})`).val('');
                $(`input[name="nom_destinataire[]"]:eq(${index})`).val('');
                $(`input[name="tel_destinataire[]"]:eq(${index})`).val('');
                $(`input[name="Adresse_destination[]"]:eq(${index})`).val('');
            });
    } else {
        // Effacer les champs si la référence est trop courte
        $(`input[name="nom_expediteur[]"]:eq(${index})`).val('');
        $(`input[name="Adresse_expedition[]"]:eq(${index})`).val('');
        $(`input[name="tel_expediteur[]"]:eq(${index})`).val('');
        $(`input[name="nom_destinataire[]"]:eq(${index})`).val('');
        $(`input[name="tel_destinataire[]"]:eq(${index})`).val('');
        $(`input[name="Adresse_destination[]"]:eq(${index})`).val('');
    }
});

        // Remplir le modal d'édition et gérer la soumission
        $(document).on('click', '.edit-programme-btn', function() {
            const programmeId = $(this).data('id');
            const editModal = $('#editProgrammeModal');
            const editForm = $('#editProgrammeForm');

            // Définir l'URL de soumission du formulaire AVEC le préfixe /admin
            editForm.attr('action', `/AGENCE_CHINE/programmechine/update/${programmeId}`);
            // Modifier l'URL pour la requête GET AVEC le préfixe /admin
            axios.get(`/AGENCE_CHINE/programmechine/edit/${programmeId}`)
            .then(response => {
                    const programme = response.data.programme;
                    const chauffeurs = response.data.chauffeurs;

                    $('#edit_programme_id').val(programme.id);
                    $('#edit_date_programme').val(programme.date_programme);
                    $('#edit_nature_du_colis').val(programme.nature_du_colis);
                    
                    // Remplir le select des chauffeurs (users)
                    $('#edit_user_id').empty().append('<option value="">-- Sélectionner un Chauffeur --</option>');
                    chauffeurs.forEach(chauffeur => {
                        $('#edit_user_id').append(`<option value="${chauffeur.id}" ${programme.user_id == chauffeur.id ? 'selected' : ''}>${chauffeur.first_name} ${chauffeur.last_name}</option>`);
                    });

                    $('#edit_reference_colis').val(programme.reference_colis);
                    $('#edit_actions_a_faire').val(programme.actions_a_faire);
                    $('#edit_etat_rdv').val(programme.etat_rdv);

                    editModal.modal('show');

                    // Réinitialiser les indicateurs de modification
                    $('#editProgrammeModal input, #editProgrammeModal select').attr('data-modified', 'false');
                    
                    // Ajouter les écouteurs d'événements pour détecter les changements
                    $('#editProgrammeModal input, #editProgrammeModal select').off('change').on('change', function() {
                        $(this).attr('data-modified', 'true');
                    });
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des données du programme:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Impossible de charger les données du programme',
                    });
                });
        });
     // Gestion de la soumission du formulaire de modification
$('#editProgrammeForm').off('submit').on('submit', function(event) {
    event.preventDefault();
    const programmeId = $('#edit_programme_id').val();
    const formData = {};
    const formElements = $('#editProgrammeModal input, #editProgrammeModal select');

    formElements.each(function() {
        const element = $(this);
        if (element.attr('data-modified') === 'true') {
            formData[element.attr('name')] = element.val();
        }
    });

    // Utilisez la même URL que pour l'édition
    axios.put(`/AGENCE_CHINE/programmechine/update/${programmeId}`, formData)
        .then(response => {
            if (response.data.success) {
                // Recharger les données depuis le serveur
                return axios.get("{{ route('chine_programme.data') }}");
            } else {
                throw new Error(response.data.message);
            }
        })
        .then(response => {
            programmesData.programmes = response.data.programmes;
            updateTable();
            generatePagination(programmesData.programmes.length);
            Swal.fire({
                icon: 'success',
                title: 'Programme mis à jour avec succès!',
                showConfirmButton: false,
                timer: 2000
            });
            $('#editProgrammeModal').modal('hide');
        })
        .catch(error => {
            console.error('Erreur lors de la mise à jour du programme:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: error.response?.data?.message || error.message,
            });
        });
});
    // Gestion du clic sur la croix et le bouton Fermer
        $('#editProgrammeModal .close, #editProgrammeModal .btn-secondary').off('click').on('click', function() {
            $('#editProgrammeModal').modal('hide');
        });

         // Gestion de la soumission du formulaire d'ajout
        $('#addProgrammeModal form').off('submit').on('submit', function(event) {
            event.preventDefault();

            axios.post("{{ route('chine_programme.store') }}", $(this).serialize())
                .then(response => {
                    // recharger les données une fois le programme créé
                    return axios.get("{{ route('chine_programme.data') }}")
                })
                .then(response => {
                    programmesData.programmes = response.data.programmes;
                    programmesData.colisValides = response.data.colisValides; //Mise à jour des colis valide
                    updateTable();
                    generatePagination(programmesData.programmes.length);
                    Swal.fire({
                        icon: 'success',
                        title: 'Programme créé avec succès!',
                        showConfirmButton: false,
                        timer: 2000
                    });
                      $('#addProgrammeModal').modal('hide');
                })
                .catch(error => {
                    console.error('Erreur lors de la création du programme:', error);
                    if (error.response && error.response.data.errors) {

                    }
                });
        });
       
       // Validation du formulaire
 // Remplacer la validation existante par ceci :
 $('#addProgrammeModal form').off('submit').on('submit', function(event) {
    let isValid = true;
    let errorMessage = '';

    $('.programme-entry').each(function(index) {
        const action = $(this).find('select[name="actions_a_faire[]"]').val();
        const refInput = $(this).find('.reference_colis');
        
        // Vérifier que l'action est sélectionnée
        if (!action) {
            isValid = false;
            errorMessage = 'Veuillez sélectionner une action à faire pour chaque entrée.';
            return false; // Arrêter la boucle
        }
        
        // Pour les actions autres que la récupération, la référence colis est obligatoire
        if (action !== 'recuperation') {
            if (!refInput.val()) {
                isValid = false;
                errorMessage = 'La référence colis est obligatoire pour les actions de dépôt et livraison.';
                return false;
            }
        }
    });

    if (!isValid) {
        Swal.fire({
            icon: 'error',
            title: 'Champs obligatoires manquants',
            text: errorMessage,
        });
        event.preventDefault();
    }
});
$(document).on('change', 'select[name="actions_a_faire[]"]', function() {
    const entry = $(this).closest('.programme-entry');
    const refInput = entry.find('.reference_colis');
    const isRecuperation = $(this).val() === 'recuperation';

    if (isRecuperation) {
        // Pour une récupération, vider tous les champs
        refInput.val('');
        entry.find('input[name="nom_expediteur[]"]').val('');
        entry.find('input[name="Adresse_expedition[]"]').val('');
        entry.find('input[name="tel_expediteur[]"]').val('');
        entry.find('input[name="nom_destinataire[]"]').val('');
        entry.find('input[name="tel_destinataire[]"]').val('');
        entry.find('input[name="Adresse_destination[]"]').val('');
    }
});
// Désactiver la recherche AJAX pour les références en mode récupération
$(document).on('input', '.reference_colis', function() {
            
            const entry = $(this).closest('.programme-entry');
            const isRecuperation = entry.find('select[name="actions_a_faire[]"]').val() === 'recuperation';
            
            if (isRecuperation) return;
        });
        // Appliquer au chargement initial pour les entrées existantes
        $('select[name="actions_a_faire[]"]').each(function() {
            $(this).trigger('change');
        });
             // Gestion de la suppression
        $(document).on('click', '.delete-programme-btn', function() {
            const programmeId = $(this).data('id');
    if (confirm('Êtes-vous sûr de vouloir supprimer ce programme ?')) {
        axios.delete(`/AGENCE_CHINE/programmechine/delete/${programmeId}`)
            .then(response => {
                if (response.data.success) {
                    // Supprimer le programme localement
                    programmesData.programmes = programmesData.programmes.filter(p => p.id !== programmeId);
                    
                    // Actualiser l'affichage
                    updateTable();
                    
                    // Message de succès
                    $('.container').prepend('<div class="alert alert-success">' + response.data.message + '</div>');
                    setTimeout(() => $(".alert-success").remove(), 3000);
                } else {
                    throw new Error(response.data.message);
                }
            })
            .catch(error => {
                console.error('Erreur lors de la suppression:', error);
                $('.container').prepend('<div class="alert alert-danger">' + error.message + '</div>');
                setTimeout(() => $(".alert-danger").remove(), 3000);
            });
    }
});
    });
</script>
@endsection