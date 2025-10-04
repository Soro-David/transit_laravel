@extends('AFT_LOUIS_BLERIOT.layouts.agent')

@section('content-header')
<div class="content-header">
<div class="container-fluid">
<div class="row mb-2">
<div class="col-sm-6">
<h1 class="m-0">Gestion des Programmes</h1>
</div>
</div>
</div>
</div>
@endsection

@section('content')
<div class="container-fluid">
<div class="card">
<div class="card-header bg-primary text-white">
<h3 class="card-title mb-0">PROGRAMMEUR</h3>
</div>
<div class="card-body">
<!-- Boutons d'actions principales -->
<div class="row mb-4">
<div class="col-md-4">
<button type="button" class="btn btn-success btn-lg btn-block" data-toggle="modal" data-target="#depotModal">
<i class="fas fa-box"></i> UN DÉPÔT
</button>
</div>
<div class="col-md-4">
<button type="button" class="btn btn-warning btn-lg btn-block" data-toggle="modal" data-target="#recuperationModal">
<i class="fas fa-truck-loading"></i> UNE RÉCUPÉRATION
</button>
</div>
<div class="col-md-4">
<button type="button" class="btn btn-info btn-lg btn-block" data-toggle="modal" data-target="#livraisonModal" disabled>
<i class="fas fa-shipping-fast"></i> UNE LIVRAISON
</button>
</div>
</div>


    
<!-- Filtres et recherche -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-inline">
                        <input type="text" id="search" class="form-control mr-2" placeholder="Rechercher...">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="pageSizeDropdown" data-toggle="dropdown">
                                <span id="pageSizeDisplay">10</span>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item page-size-btn" href="#" data-size="10">10</a>
                                <a class="dropdown-item page-size-btn" href="#" data-size="50">50</a>
                                <a class="dropdown-item page-size-btn" href="#" data-size="100">100</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableau des programmes -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="bg-light">
                        <tr>
                            <th>Qantité</th>
                            <th>Action à Faire</th>
                            <th>Référence</th>
                            <th>Nature Colis</th>
                            <th>Nom du client</th>
                            <th>Télephone</th>
                            <th>Addresse de depot ou recuperation</th>
                            <th>Chauffeur</th>
                            <th>État</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="programmes-table">
                        <!-- Données chargées via JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3 d-flex justify-content-center">
                <nav aria-label="Page navigation">
                    <ul id="pagination" class="pagination">
                        <!-- Généré par JavaScript -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour Dépôt -->
<div class="modal fade" id="depotModal" tabindex="-1" role="dialog" aria-labelledby="depotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="depotModalLabel">Programmer un Dépôt</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="depotForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="quantite">Quantité *</label>
                                <input type="number" class="form-control" id="quantite" name="quantite" required min="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_programme_depot">Date du Programme *</label>
                                <input type="date" class="form-control" id="date_programme_depot" name="date_programme" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user_id_depot">Chauffeur *</label>
                                <select class="form-control" id="user_id_depot" name="user_id" required>
                                    <option value="">-- Sélectionner un Chauffeur --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nature_du_colis_depot">Nature de l'objet a deposé *</label>
                                <input type="text" class="form-control" id="nature_du_colis_depot" name="nature_du_colis" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nom_expediteur_depot">Nom du Concerné *</label>
                        <input type="text" class="form-control" id="nom_expediteur_depot" name="nom_expediteur" required>
                    </div>

                    <div class="form-group">
                        <label for="lieu_expedition_depot">Adresse de Dépôt *</label>
                        <textarea class="form-control" id="lieu_expedition_depot" name="lieu_expedition" rows="2" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="tel_expediteur_depot">Numéro de Téléphone *</label>
                        <input type="text" class="form-control" id="tel_expediteur_depot" name="tel_expediteur" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-success">Enregistrer le Dépôt</button>
                </div>
            </form>
        </div>
    </div>
</div>

  

 <!-- Modal pour Récupération -->
<div class="modal fade" id="recuperationModal" tabindex="-1" role="dialog" aria-labelledby="recuperationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-black">
                <h5 class="modal-title text-black" id="recuperationModalLabel">Programmer une Récupération</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="recuperationForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type_reference">Type de récupération <span style="color: brown">*</span></label>
                                <select class="form-control" id="type_reference" name="type_reference">
                                    <option value="">-- Sélectionner le type --</option>
                                    <option value="devis">Devis confirmé</option>
                                    <option value="depot">Dépôt effectué</option>
                                    <option value="manuel">Référence manuelle</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reference_input">Référence <span style="color: brown">*</span></label>
                                <input type="text" class="form-control" id="reference_input" name="reference_input" 
                                       placeholder="Entrez la référence du devis, dépôt ou une référence manuelle">
                                <small class="form-text text-center" style="font-size: 10px; color:brown" id="reference_help">
                                    Référence devis confirmé, dépôt effectué ou référence personnalisée
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="quantite_recup">Quantité totale <span style="color: brown">*</span></label>
                                <input type="number" class="form-control" id="quantite_recup" name="quantite" min="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="user_id_recup">Chauffeur <span style="color: brown">*</span></label>
                                <select class="form-control" id="user_id_recup" name="user_id">
                                    <option value="">-- Sélectionner un Chauffeur --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date_programme_recup">Date du Programme <span style="color: brown">*</span></label>
                                <input type="date" class="form-control" id="date_programme_recup" name="date_programme">
                            </div>
                        </div>
                    </div>

                    <!-- NOUVEAU: Section pour afficher et modifier les informations des devis_items -->
                    <div id="devis_items_section" style="display: none;">
                        <div class="card mb-3">
                            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Informations détaillées du colis</h6>
                                <button type="button" class="btn btn-sm btn-light" id="toggleEditMode">
                                    <i class="fas fa-edit"></i> Modifier
                                </button>
                            </div>
                            <div class="card-body" id="devis_items_content">
                                <!-- Les informations des devis_items seront injectées ici -->
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info" id="reference_info" style="display: none;">
                        <i class="fas fa-info-circle"></i> <span id="reference_message"></span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nature_du_colis_recup">Nature du Colis <span style="color: brown">*</span></label>
                                <input type="text" class="form-control" id="nature_du_colis_recup" name="nature_du_colis">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nom_expediteur_recup">Nom du Client <span style="color: brown">*</span></label>
                                <input type="text" class="form-control" id="nom_expediteur_recup" name="nom_expediteur">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tel_expediteur_recup">Numéro de Téléphone <span style="color: brown">*</span></label>
                                <input type="text" class="form-control" id="tel_expediteur_recup" name="tel_expediteur">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="lieu_expedition_recup">Adresse de Récupération <span style="color: brown">*</span></label>
                                <textarea class="form-control" id="lieu_expedition_recup" name="lieu_expedition" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-warning">Enregistrer la Récupération</button>
                </div>
            </form>
        </div>
    </div>
</div>
  

</div>

    
<!-- Modal pour Livraison (à implémenter plus tard) -->
<div class="modal fade" id="livraisonModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Programmer une Livraison</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                <p class="text-muted">Fonctionnalité à venir...</p>
            </div>
        </div>
    </div>
</div>

  

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    let programmesData = { programmes: [] };
    let currentPage = 1;
    let itemsPerPage = 10;

    // Chargement initial des données - VERSION AMÉLIORÉE
    function loadData() {
        console.log("=== DÉBUT loadData ===");
        
        axios.get("{{ route('aftlb_transport.programme.data') }}")
            .then(function(response) {
                console.log("✅ DONNÉES REÇUES");
                
                if (!response.data) {
                    throw new Error('Réponse vide du serveur');
                }
                
                programmesData.programmes = response.data.programmes || [];
                programmesData.chauffeurs = response.data.chauffeurs || [];
                
                console.log("📊 Programmes chargés:", programmesData.programmes.length);
                console.log("📊 Chauffeurs chargés:", programmesData.chauffeurs.length);
                
                // Mettre à jour LES DEUX selects des chauffeurs
                var selectChauffeurDepot = $('#user_id_depot');
                var selectChauffeurRecup = $('#user_id_recup'); // Cible le select de récupération
                
                selectChauffeurDepot.empty().append('<option value="">-- Sélectionner un Chauffeur --</option>');
                selectChauffeurRecup.empty().append('<option value="">-- Sélectionner un Chauffeur --</option>'); // Vide aussi celui de récupération
                
                if (programmesData.chauffeurs && programmesData.chauffeurs.length > 0) {
                    programmesData.chauffeurs.forEach(function(chauffeur) {
                        if (chauffeur && chauffeur.id) {
                            const nomComplet = `${chauffeur.first_name || ''} ${chauffeur.last_name || ''}`.trim();
                            const option = `<option value="${chauffeur.id}">${nomComplet}</option>`;
                            selectChauffeurDepot.append(option);
                            selectChauffeurRecup.append(option); // Ajoute l'option aux deux selects
                        }
                    });
                }

                updateTable();
                console.log("=== FIN loadData ===");
            })
            .catch(function(error) {
                console.error('❌ Erreur de chargement des données:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Impossible de charger les données: ' + (error.response?.data?.message || error.message)
                });
            });
    }

    // Génération du tableau
    function generateTable(programmes) {
    const tableBody = $('#programmes-table');
    tableBody.empty();

    // CORRECTION: Vérifier que programmes est valide
    if (!programmes || !Array.isArray(programmes) || programmes.length === 0) {
        tableBody.append('<tr><td colspan="13" class="text-center">Aucun programme trouvé.</td></tr>');
        return;
    }

    programmes.forEach(programme => {
        // CORRECTION: Vérifier que programme n'est pas null
        if (!programme) return;
        
        let rowClass = '';
        if (programme.etat_rdv === 'effectué') {
            rowClass = 'table-success';
        } else if (programme.etat_rdv === 'à replanifié') {
            rowClass = 'table-warning';
        }
        
        // CORRECTION: Utiliser des valeurs par défaut sécurisées
        const userDisplayName = programme.user ? 
            `${programme.user.first_name || ''} ${programme.user.last_name || ''}`.trim() : 
            'N/A';
        
        tableBody.append(`
            <tr class="${rowClass}">
                <td>${programme.quantite || '1'}</td>
                <td>
                    <span class="badge ${getActionBadgeClass(programme.actions_a_faire)}">
                        ${getActionText(programme.actions_a_faire)}
                    </span>
                </td>
                <td>${programme.reference_a_afficher || 'N/A'}</td>
                <td>${programme.nature_du_colis || 'N/A'}</td>
                <td>${programme.nom_expediteur || 'N/A'}</td>
                <td>${programme.tel_expediteur || 'N/A'}</td>
                <td>${programme.lieu_expedition || 'N/A'}</td>
               
                <td>${userDisplayName}</td>
                <td>
                    <span class="badge ${getEtatBadgeClass(programme.etat_rdv)}">
                        ${programme.etat_rdv || 'N/A'}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-info edit-programme-btn" data-id="${programme.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-programme-btn" data-id="${programme.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `);
    });
}

    function getActionBadgeClass(action) {
        switch(action) {
            case 'depot': return 'badge-success';
            case 'recuperation': return 'badge-warning';
            case 'livraison': return 'badge-info';
            default: return 'badge-secondary';
        }
    }

    function getActionText(action) {
        switch(action) {
            case 'depot': return 'DÉPÔT';
            case 'recuperation': return 'RÉCUPÉRATION';
            case 'livraison': return 'LIVRAISON';
            default: return action;
        }
    }

    function getEtatBadgeClass(etat) {
        switch(etat) {
            case 'effectué': return 'badge-success';
            case 'en attente': return 'badge-warning';
            case 'à replanifié': return 'badge-danger';
            default: return 'badge-secondary';
        }
    }

    // Gestion de la pagination
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
    
    // CORRECTION: Vérifier que programmesData.programmes existe et est un tableau
    if (!programmesData.programmes || !Array.isArray(programmesData.programmes)) {
        console.error('❌ programmesData.programmes est invalide:', programmesData.programmes);
        const tableBody = $('#programmes-table');
        tableBody.empty();
        tableBody.append('<tr><td colspan="13" class="text-center text-danger">Erreur: Données invalides</td></tr>');
        return;
    }

    const filteredProgrammes = programmesData.programmes.filter(programme => {
        // CORRECTION: Vérifier que programme n'est pas null/undefined
        if (!programme) return false;
        
        // Vérifier les valeurs du programme
        const programmeValues = Object.values(programme);
        const hasMatchInProgramme = programmeValues.some(value => 
            value !== null && value !== undefined && value.toString().toLowerCase().includes(searchTerm)
        );
        
        // Vérifier les valeurs de l'utilisateur
        const hasMatchInUser = programme.user && 
            ((programme.user.first_name && programme.user.first_name.toLowerCase().includes(searchTerm)) ||
             (programme.user.last_name && programme.user.last_name.toLowerCase().includes(searchTerm)));
        
        return hasMatchInProgramme || hasMatchInUser;
    });
    
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const programmesToDisplay = filteredProgrammes.slice(startIndex, endIndex);
    generateTable(programmesToDisplay);
    generatePagination(filteredProgrammes.length);
}
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

    // Soumission du formulaire de dépôt - CORRECTION ICI
    $('#depotForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        
        axios.post("{{ route('aftlb_transport.programme.createDepot') }}", formData)
            .then(response => {
                if (response.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès!',
                        text: response.data.message,
                        timer: 2000
                    });
                    $('#depotModal').modal('hide');
                    $('#depotForm')[0].reset();
                    loadData(); // Recharger les données
                } else {
                    throw new Error(response.data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: error.response?.data?.message || error.message
                });
            });
    });
    function loadChauffeursRecup() {
    console.log("🔄 Chargement des chauffeurs pour récupération...");
    
    var selectChauffeur = $('#user_id_recup');
    selectChauffeur.empty().append('<option value="">-- Sélectionner un Chauffeur --</option>');
    
    // Vérification plus robuste des données
    if (programmesData.chauffeurs && Array.isArray(programmesData.chauffeurs) && programmesData.chauffeurs.length > 0) {
        console.log(`✅ ${programmesData.chauffeurs.length} chauffeurs disponibles dans programmesData`);
        
        programmesData.chauffeurs.forEach(function(chauffeur) {
            if (chauffeur && chauffeur.id && chauffeur.first_name) {
                const nomComplet = `${chauffeur.first_name || ''} ${chauffeur.last_name || ''}`.trim();
                selectChauffeur.append(`<option value="${chauffeur.id}">${nomComplet}</option>`);
            }
        });
        
        console.log("✅ Select récupération mis à jour. Options:", $('#user_id_recup option').length);
    } else {
        console.warn("⚠️ Aucun chauffeur disponible dans programmesData, rechargement...");
        
        // Recharger les données complètes
        axios.get("{{ route('aftlb_transport.programme.data') }}")
            .then(function(response) {
                programmesData.chauffeurs = response.data.chauffeurs;
                programmesData.programmes = response.data.programmes;
                
                if (programmesData.chauffeurs && Array.isArray(programmesData.chauffeurs) && programmesData.chauffeurs.length > 0) {
                    programmesData.chauffeurs.forEach(function(chauffeur) {
                        if (chauffeur && chauffeur.id && chauffeur.first_name) {
                            const nomComplet = `${chauffeur.first_name || ''} ${chauffeur.last_name || ''}`.trim();
                            selectChauffeur.append(`<option value="${chauffeur.id}">${nomComplet}</option>`);
                        }
                    });
                    console.log("✅ Chauffeurs chargés après rechargement:", programmesData.chauffeurs.length);
                } else {
                    console.error("❌ Aucun chauffeur trouvé même après rechargement");
                    selectChauffeur.append('<option value="">Aucun chauffeur disponible</option>');
                }
            })
            .catch(function(error) {
                console.error('❌ Erreur chargement chauffeurs fallback:', error);
                selectChauffeur.append('<option value="">Erreur de chargement</option>');
            });
    }
}
// Recherche automatique des informations de référence
$('#reference_input').on('input', function() {
    const reference = $(this).val();
    const typeReference = $('#type_reference').val();
    let urlTemplate = "{{ route('aftlb_transport.programme.referenceInfo', ['reference' => 'PLACEHOLDER']) }}";
    let finalUrl = urlTemplate.replace('PLACEHOLDER', reference);
    

    if (reference.length >= 3 && typeReference) {
        console.log("🔍 Recherche référence:", reference, "Type:", typeReference);
        
        axios.get(finalUrl)
            .then(function(response) {
                const data = response.data;
                const infoDiv = $('#reference_info');
                const messageSpan = $('#reference_message');
                const devisItemsSection = $('#devis_items_section');
                const devisItemsContent = $('#devis_items_content');
                
                console.log("Réponse référence:", data);
                
                if (data.existe) {
                    // Auto-remplissage des champs de base
                    $('#nom_expediteur_recup').val(data.data.nom_expediteur || '');
                    $('#lieu_expedition_recup').val(data.data.lieu_expedition || '');
                    $('#tel_expediteur_recup').val(data.data.tel_expediteur || '');
                    $('#nature_du_colis_recup').val(data.data.nature_du_colis || '');
                    
                    // CORRECTION: Toujours remplir la quantité avec la valeur calculée
                    const quantiteTotale = data.data.quantite || 1;
                    $('#quantite_recup').val(quantiteTotale);
                    console.log("✅ Quantité totale remplie:", quantiteTotale);
                    
                    // NOUVEAU: Afficher les informations des devis_items si disponibles
                    if (data.data.items && data.data.items.length > 0) {
                        console.log("📦 Items du devis:", data.data.items);
                        
                        // Générer le HTML avec possibilité d'édition
                        generateEditableItemsHTML(data.data.items, devisItemsContent);
                        devisItemsSection.show();
                        
                        // Stocker les items pour modification éventuelle
                        $('#recuperationForm').data('devis-items', data.data.items);
                        $('#recuperationForm').data('devis-info', {
                            mode_transit: data.data.mode_transit,
                            agence_destination: data.data.agence_destination,
                            montant: data.data.montant,
                            devise: data.data.devise,
                            quantite_originale: data.data.quantite
                        });
                        
                        // Initialiser le mode édition
                        initEditMode();
                    } else {
                        devisItemsSection.hide();
                        $('#recuperationForm').removeData('devis-items');
                    }

                    // LOGIQUE EXISTANTE POUR LES TYPES DE RÉFÉRENCES
                    if (data.type === 'reference_re') {
                        infoDiv.removeClass('alert-danger alert-warning').addClass('alert-success');
                        messageSpan.html(`
                            <strong>✅ Référence -RE trouvée!</strong><br>
                            <small>Remplissage automatique effectué à partir de la table programmes</small>
                        `);
                    }
                    else if (data.type === 'devis') {
                        if (data.etat_devis === 'confirmé') {
                            infoDiv.removeClass('alert-danger alert-warning').addClass('alert-success');
                            messageSpan.html(`
                                <strong>✅ Devis confirmé trouvé!</strong><br>
                                <small>
                                    • Mode transit: ${data.data.mode_transit || 'N/A'}<br>
                                    • Agence destination: ${data.data.agence_destination || 'N/A'}<br>
                                    • Montant: ${data.data.montant || 'N/A'} ${data.data.devise || ''}<br>
                                    • Quantité totale: ${quantiteTotale} article(s)
                                </small>
                            `);
                        } 
                        else if (data.etat_devis === 'validé') {
                            infoDiv.removeClass('alert-danger alert-warning').addClass('alert-info');
                            messageSpan.html(`
                                <strong>✅ Devis validé trouvé</strong><br>
                                <small>Remplissage automatique effectué - Quantité: ${quantiteTotale}</small>
                            `);
                        }
                    } 
                    else if (data.type === 'depot') {
                        if (data.valide) {
                            infoDiv.removeClass('alert-danger alert-warning').addClass('alert-success');
                            messageSpan.text('✅ Dépôt effectué trouvé - Remplissage automatique effectué - Quantité: ' + quantiteTotale);
                        } else {
                            infoDiv.removeClass('alert-success alert-danger').addClass('alert-warning');
                            messageSpan.text('⚠️ Dépôt trouvé mais pas encore effectué - Remplissage automatique effectué - Quantité: ' + quantiteTotale);
                        }
                    }
                } else {
                    // Référence manuelle
                    infoDiv.removeClass('alert-success alert-danger').addClass('alert-info');
                    messageSpan.text('ℹ️ Référence manuelle - Veuillez remplir les informations manuellement');
                    
                    // Vider les champs
                    $('#nom_expediteur_recup').val('');
                    $('#lieu_expedition_recup').val('');
                    $('#tel_expediteur_recup').val('');
                    $('#nature_du_colis_recup').val('');
                    $('#quantite_recup').val(1); // Valeur par défaut
                    
                    // Masquer la section devis_items
                    devisItemsSection.hide();
                    
                    // Nettoyer les données stockées
                    $('#recuperationForm').removeData('devis-items');
                    $('#recuperationForm').removeData('devis-info');
                }
                
                infoDiv.show();
            })
            .catch(function(error) {
                console.error('Erreur recherche référence:', error);
                $('#reference_info').hide();
                $('#devis_items_section').hide();
            });
    } else {
        $('#reference_info').hide();
        $('#devis_items_section').hide();
    }
});

// Modification de la validation selon le type de référence sélectionné
$('#type_reference').on('change', function() {
    const typeRef = $(this).val();
    const referenceInput = $('#reference_input');
    
    // Adapter le placeholder selon le type
    if (typeRef === 'devis') {
        referenceInput.attr('placeholder', 'Entrez la référence du devis confirmé');
    } else if (typeRef === 'depot') {
        referenceInput.attr('placeholder', 'Entrez la référence du dépôt effectué');
    } else if (typeRef === 'manuel') {
        referenceInput.attr('placeholder', 'Entrez une référence personnalisée ou laissez vide pour génération automatique');
    }
    
    // Déclencher la recherche si une référence est déjà saisie
    if (referenceInput.val().length >= 3) {
        referenceInput.trigger('input');
    }
});
function generateEditableItemsHTML(items, container) {
    let itemsHTML = '';
    
    items.forEach((item, index) => {
        itemsHTML += `
            <div class="item-details mb-3 p-3 border rounded ${index > 0 ? 'mt-2' : ''}" data-item-index="${index}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-primary mb-0">Article ${index + 1}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn" data-index="${index}" style="display: none;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label class="small"><strong>Produit ou service:</strong></label>
                            <input type="text" class="form-control form-control-sm editable-field" value="${item.service || ''}" 
                                   data-field="service" data-original="${item.service || ''}" readonly>
                        </div>
                        <div class="form-group mb-2">
                            <label class="small"><strong>Type de colis:</strong></label>
                            <input type="text" class="form-control form-control-sm editable-field" value="${item.type_colis || 'Colis divers'}" 
                                   data-field="type_colis" data-original="${item.type_colis || 'Colis divers'}" readonly>
                        </div>
                        <div class="form-group mb-2">
                            <label class="small"><strong>Valeur (FCFA):</strong></label>
                            <input type="number" class="form-control form-control-sm editable-field" value="${item.valeur_colis || ''}" 
                                   data-field="valeur_colis" data-original="${item.valeur_colis || ''}" readonly step="0.01">
                        </div>
                        <div class="form-group mb-2">
                            <label class="small"><strong>Description:</strong></label>
                            <textarea class="form-control form-control-sm editable-field" data-field="description_colis" 
                                      data-original="${item.description_colis || ''}" readonly rows="2">${item.description_colis || ''}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label class="small"><strong>Poids (kg):</strong></label>
                            <input type="number" class="form-control form-control-sm editable-field" value="${item.poids || ''}" 
                                   data-field="poids" data-original="${item.poids || ''}" readonly step="0.01">
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group mb-2">
                                    <label class="small"><strong>Longueur:</strong></label>
                                    <input type="number" class="form-control form-control-sm editable-field" value="${item.longueur || ''}" 
                                           data-field="longueur" data-original="${item.longueur || ''}" readonly>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group mb-2">
                                    <label class="small"><strong>Largeur:</strong></label>
                                    <input type="number" class="form-control form-control-sm editable-field" value="${item.largeur || ''}" 
                                           data-field="largeur" data-original="${item.largeur || ''}" readonly>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group mb-2">
                                    <label class="small"><strong>Hauteur:</strong></label>
                                    <input type="number" class="form-control form-control-sm editable-field" value="${item.hauteur || ''}" 
                                           data-field="hauteur" data-original="${item.hauteur || ''}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <label class="small"><strong>Quantité:</strong></label>
                            <input type="number" class="form-control form-control-sm editable-field" value="${item.quantite_colis || '1'}" 
                                   data-field="quantite_colis" data-original="${item.quantite_colis || '1'}" readonly min="1">
                        </div>
                    </div>
                </div>
                <div class="item-actions mt-2" style="display: none;">
                    <button type="button" class="btn btn-sm btn-success save-item-btn" data-index="${index}">
                        <i class="fas fa-check"></i> Sauvegarder
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary cancel-edit-btn" data-index="${index}">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                </div>
            </div>
        `;
    });
    
    // Ajouter un bouton pour ajouter un nouvel item
    itemsHTML += `
        <div class="text-center mt-3" id="add-item-section" style="display: none;">
            <button type="button" class="btn btn-sm btn-primary" id="add-new-item">
                <i class="fas fa-plus"></i> Ajouter un nouvel article
            </button>
        </div>
    `;
    
    container.html(itemsHTML);
    
    // Mettre à jour la quantité totale
    updateTotalQuantity();
}

// NOUVELLE FONCTION: Initialiser le mode édition
function initEditMode() {
    let isEditMode = false;
    
    $('#toggleEditMode').on('click', function() {
        isEditMode = !isEditMode;
        
        if (isEditMode) {
            // Activer le mode édition
            $(this).html('<i class="fas fa-eye"></i> Visualiser').removeClass('btn-light').addClass('btn-warning');
            $('.editable-field').prop('readonly', false).addClass('border-primary');
            $('.remove-item-btn').show();
            $('#add-item-section').show();
            $('.item-actions').show();
        } else {
            // Désactiver le mode édition
            $(this).html('<i class="fas fa-edit"></i> Modifier').removeClass('btn-warning').addClass('btn-light');
            $('.editable-field').prop('readonly', true).removeClass('border-primary');
            $('.remove-item-btn').hide();
            $('#add-item-section').hide();
            $('.item-actions').hide();
            
            // Annuler les modifications non sauvegardées
            $('.editable-field').each(function() {
                const original = $(this).data('original');
                $(this).val(original);
            });
            
            updateTotalQuantity();
        }
    });
    
   // Sauvegarder les modifications d'un item
   $(document).on('click', '.save-item-btn', function() {
        const index = $(this).data('index');
        const itemElement = $(`.item-details[data-item-index="${index}"]`);
        
        // Récupérer les données modifiées
        const updatedItem = {};
        let hasErrors = false;
        
        itemElement.find('.editable-field').each(function() {
            const field = $(this).data('field');
            let value = $(this).val();
            
            // Validation basique
            if (field === 'service' && !value.trim()) {
                $(this).addClass('is-invalid');
                hasErrors = true;
                return;
            } else {
                $(this).removeClass('is-invalid');
            }
            
            // Convertir les valeurs numériques
            if (field === 'quantite_colis' || field === 'longueur' || field === 'largeur' || field === 'hauteur') {
                value = parseInt(value) || 0;
                if (value < 0) value = 0;
            } else if (field === 'valeur_colis' || field === 'poids') {
                value = parseFloat(value) || 0;
                if (value < 0) value = 0;
            }
            
            updatedItem[field] = value;
            // Mettre à jour la valeur originale
            $(this).data('original', value);
        });
        
        if (hasErrors) {
            showTempMessage('Veuillez remplir tous les champs obligatoires', 'error');
            return;
        }
        
        // Mettre à jour les données stockées
        const currentItems = $('#recuperationForm').data('devis-items');
        
        if (currentItems && currentItems[index]) {
            // Conserver les métadonnées existantes
            updatedItem.is_new = currentItems[index].is_new || false;
            updatedItem.id = currentItems[index].id || `item_${index}`;
            
            currentItems[index] = { ...currentItems[index], ...updatedItem };
            $('#recuperationForm').data('devis-items', currentItems);
            
            // Mettre à jour la quantité totale
            updateTotalQuantity();
            
            // Afficher un message de confirmation
            showTempMessage('Article ' + (index + 1) + ' sauvegardé', 'success');
            
            console.log("💾 Article sauvegardé:", currentItems[index]);
            
            // Désactiver le mode édition pour cet item
            itemElement.find('.editable-field').prop('readonly', true).removeClass('border-primary');
            itemElement.find('.item-actions').hide();
            itemElement.find('.remove-item-btn').hide();
        }
    });
    
     // Annuler les modifications d'un item
     $(document).on('click', '.cancel-edit-btn', function() {
        const index = $(this).data('index');
        const itemElement = $(`.item-details[data-item-index="${index}"]`);
        
        // Restaurer les valeurs originales
        itemElement.find('.editable-field').each(function() {
            const original = $(this).data('original');
            $(this).val(original);
        });
    });
    
    // Supprimer un item
    $(document).on('click', '.remove-item-btn', function() {
        const index = $(this).data('index');
        const currentItems = $('#recuperationForm').data('devis-items');
        
        if (currentItems.length <= 1) {
            Swal.fire('Attention', 'Vous ne pouvez pas supprimer le dernier article', 'warning');
            return;
        }
        
        Swal.fire({
            title: 'Êtes-vous sûr?',
            text: "Cette action ne peut pas être annulée!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer!',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                currentItems.splice(index, 1);
                $('#recuperationForm').data('devis-items', currentItems);
                
                // Regénérer l'affichage
                generateEditableItemsHTML(currentItems, $('#devis_items_content'));
                initEditMode();
                
                // Mettre à jour la quantité totale
                updateTotalQuantity();
                
                Swal.fire('Supprimé!', 'L\'article a été supprimé.', 'success');
            }
        });
    });
    
    // Ajouter un nouvel item
    $(document).on('click', '#add-new-item', function() {
        const currentItems = $('#recuperationForm').data('devis-items') || [];
        const newItem = {
            quantite_colis: 1,
            service: 'Nouveau service',
            valeur_colis: 0,
            type_colis: 'Colis divers',
            description_colis: 'Description du nouvel article',
            poids: 0,
            longueur: 0,
            largeur: 0,
            hauteur: 0,
            is_new: true,
            id: 'new_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9)
        };
        
        currentItems.push(newItem);
        $('#recuperationForm').data('devis-items', currentItems);
        
        console.log("➕ Nouvel article ajouté:", newItem);
        
        // Regénérer l'affichage
        generateEditableItemsHTML(currentItems, $('#devis_items_content'));
        initEditMode();
        
        // Activer automatiquement le mode édition pour le nouvel item
        if (!$('#toggleEditMode').hasClass('btn-warning')) {
            $('#toggleEditMode').click(); // Activer le mode édition si pas déjà activé
        }
        
        // Mettre le focus sur le premier champ du nouvel article
        setTimeout(() => {
            const lastIndex = currentItems.length - 1;
            $(`.item-details[data-item-index="${lastIndex}"] .editable-field`).first().focus();
        }, 100);
    });
}

function updateTotalQuantity() {
    const currentItems = $('#recuperationForm').data('devis-items') || [];
    const totalQuantity = currentItems.reduce((total, item) => {
        return total + parseInt(item.quantite_colis || 1);
    }, 0);
    
    $('#quantite_recup').val(totalQuantity);
}

// NOUVELLE FONCTION: Afficher un message temporaire
function showTempMessage(message, type = 'info') {
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-danger' : 'alert-info';
    const tempAlert = $(`<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>`);
    
    $('#reference_info').after(tempAlert);
    
    setTimeout(() => {
        tempAlert.alert('close');
    }, 3000);
}

// Soumission du formulaire de récupération
$('#recuperationForm').on('submit', function(e) {
    e.preventDefault();
    
    // Sauvegarder tous les articles non sauvegardés avant la soumission
    $('.save-item-btn').each(function() {
        const index = $(this).data('index');
        const itemElement = $(`.item-details[data-item-index="${index}"]`);
        
        // Vérifier si l'article a des modifications non sauvegardées
        let hasUnsavedChanges = false;
        itemElement.find('.editable-field').each(function() {
            const currentValue = $(this).val();
            const originalValue = $(this).data('original');
            if (currentValue !== originalValue) {
                hasUnsavedChanges = true;
                return false; // Sortir de la boucle
            }
        });
        
        if (hasUnsavedChanges) {
            $(this).click(); // Sauvegarder les modifications
        }
    });
    
    // Attendre un peu que toutes les sauvegardes soient faites
    setTimeout(() => {
        submitRecuperationForm();
    }, 500);
});
// NOUVELLE FONCTION: Soumission réelle du formulaire
// NOUVELLE FONCTION: Soumission réelle du formulaire
function submitRecuperationForm() {
    // FORCER la sauvegarde de TOUS les articles avant envoi
    const currentItems = $('#recuperationForm').data('devis-items') || [];
    console.log("🔄 Pré-sauvegarde des articles:", currentItems);
    
    // Sauvegarder tous les articles (y compris les nouveaux)
    let allItemsSaved = true;
    $('.item-details').each(function() {
        const index = $(this).data('item-index');
        const saveButton = $(`.save-item-btn[data-index="${index}"]`);
        
        // Si le bouton de sauvegarde est visible, c'est qu'il y a des modifications non sauvegardées
        if (saveButton.is(':visible')) {
            console.log(`💾 Sauvegarde forcée de l'article ${index}`);
            saveButton.click();
            allItemsSaved = false;
        }
    });
    
    // Attendre que toutes les sauvegardes soient faites
    const waitForSave = () => {
        setTimeout(() => {
            const hasUnsavedChanges = $('.save-item-btn:visible').length > 0;
            
            if (hasUnsavedChanges) {
                console.log("⏳ Attente sauvegarde...");
                waitForSave();
            } else {
                console.log("✅ Tous les articles sauvegardés, envoi au serveur");
                sendFormToServer();
            }
        }, 300);
    };
    
    waitForSave();
}

// Fonction séparée pour l'envoi au serveur
function sendFormToServer() {
    let formData = $('#recuperationForm').serializeArray();
    
    // Récupérer les items FINAUX après toutes les sauvegardes
    const devisItems = $('#recuperationForm').data('devis-items') || [];
    console.log("📦 Articles finaux à envoyer:", devisItems);
    
    if (devisItems && devisItems.length > 0) {
        // Préparer les données pour l'envoi
        const itemsToSend = devisItems.map(item => ({
            quantite_colis: parseInt(item.quantite_colis) || 1,
            service: item.service || 'Service non spécifié',
            valeur_colis: parseFloat(item.valeur_colis) || 0,
            type_colis: item.type_colis || 'Colis divers',
            description_colis: item.description_colis || '',
            poids: parseFloat(item.poids) || 0,
            longueur: parseFloat(item.longueur) || 0,
            largeur: parseFloat(item.largeur) || 0,
            hauteur: parseFloat(item.hauteur) || 0,
            // Inclure l'identifiant pour le tracking
            temp_id: item.id || null,
            is_new: item.is_new || false
        }));
        
        console.log("📤 Données préparées pour envoi:", itemsToSend);
        
        // Ajouter les items au formData
        formData.push({ 
            name: 'devis_items', 
            value: JSON.stringify(itemsToSend) 
        });
        
        // TOUJOURS envoyer l'indicateur de modifications
        formData.push({ 
            name: 'modifications_apportees', 
            value: 'true' 
        });
    }

    // Afficher un indicateur de chargement
    Swal.fire({
        title: 'Création en cours...',
        text: 'Veuillez patienter',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    axios.post("{{ route('aftlb_transport.programme.createRecuperation') }}", $.param(formData))
        .then(function(response) {
            Swal.close();
            
            if (response.data.success) {
                let successMessage = response.data.message;
                if (response.data.items_created) {
                    successMessage += ' Les articles ont été liés au programme.';
                }
                
                Swal.fire({
                    icon: 'success',
                    title: 'Succès!',
                    html: successMessage + '<br><small>Référence: ' + (response.data.reference_generee || '') + '</small>',
                    timer: 4000
                });
                
                $('#recuperationModal').modal('hide');
                $('#recuperationForm')[0].reset();
                $('#recuperationForm').removeData('devis-items devis-info');
                $('#devis_items_section').hide();
                loadData();
            } else {
                throw new Error(response.data.message);
            }
        })
        .catch(function(error) {
            Swal.close();
            console.error('❌ Erreur:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: error.response?.data?.message || error.message,
                footer: 'Vérifiez les logs pour plus de détails'
            });
        });
}

// Charger les chauffeurs quand le modal récupération s'ouvre
$('#recuperationModal').on('show.bs.modal', function () {
    loadChauffeursRecup();
    $('#reference_info').hide();
    $('#devis_items_section').hide();
});

// MODIFICATION: Masquer la section devis_items quand le modal se ferme
$('#recuperationModal').on('hidden.bs.modal', function () {
    $('#devis_items_section').hide();
    $('#devis_items_content').empty();
    // Réinitialiser le bouton d'édition
    $('#toggleEditMode').html('<i class="fas fa-edit"></i> Modifier').removeClass('btn-warning').addClass('btn-light');
});
    // Édition d'un programme - CORRECTION ICI
    $(document).on('click', '.edit-programme-btn', function() {
        const programmeId = $(this).data('id');
        
        axios.get("/aftlb_transport/programme-edit/" + programmeId + "-aft-louis-b")
            .then(response => {
                const programme = response.data.programme;
                // Ouvrir un modal d'édition ou utiliser SweetAlert pour l'édition
                Swal.fire({
                    title: 'Modifier le Programme',
                    html: `
                        <form id="editProgrammeForm">
                            <div class="form-group">
                                <label for="edit_quantite">Quantité</label>
                                <input type="number" class="form-control" id="edit_quantite" value="${programme.quantite || 1}" min="1">
                            </div>
                            <div class="form-group">
                                <label for="edit_date_programme">Date du Programme</label>
                                <input type="date" class="form-control" id="edit_date_programme" value="${programme.date_programme}">
                            </div>
                            <div class="form-group">
                                <label for="edit_actions_a_faire">Action à faire</label>
                                <select class="form-control" id="edit_actions_a_faire">
                                    <option value="depot" ${programme.actions_a_faire === 'depot' ? 'selected' : ''}>Dépôt</option>
                                    <option value="recuperation" ${programme.actions_a_faire === 'recuperation' ? 'selected' : ''}>Récupération</option>
                                    <option value="livraison" ${programme.actions_a_faire === 'livraison' ? 'selected' : ''}>Livraison</option>
                                </select>
                            </div>
                        </form>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Enregistrer',
                    cancelButtonText: 'Annuler',
                    preConfirm: () => {
                        return {
                            quantite: $('#edit_quantite').val(),
                            date_programme: $('#edit_date_programme').val(),
                            actions_a_faire: $('#edit_actions_a_faire').val()
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = result.value;
                        axios.put("/aftlb_transport/programme-update/" + programmeId + "-aft-louis-b", formData)
                            .then(response => {
                                if (response.data.success) {
                                    Swal.fire('Succès!', response.data.message, 'success');
                                    loadData(); // Recharger les données
                                }
                            })
                            .catch(error => {
                                Swal.fire('Erreur!', error.response?.data?.message || 'Erreur lors de la modification', 'error');
                            });
                    }
                });
            })
            .catch(error => {
                console.error('Erreur:', error);
                Swal.fire('Erreur', 'Impossible de charger les données du programme', 'error');
            });
    });

    // Suppression d'un programme - CORRECTION ICI
    $(document).on('click', '.delete-programme-btn', function() {
        const programmeId = $(this).data('id');
        
        Swal.fire({
            title: 'Êtes-vous sûr?',
            text: "Vous ne pourrez pas annuler cette action!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer!',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete("/aftlb_transport/programme-delete/" + programmeId + "-aft-louis-b")
                    .then(response => {
                        if (response.data.success) {
                            programmesData.programmes = programmesData.programmes.filter(p => p.id !== programmeId);
                            updateTable();
                            Swal.fire({
                                icon: 'success',
                                title: 'Supprimé!',
                                text: response.data.message,
                                timer: 2000
                            });
                        } else {
                            throw new Error(response.data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la suppression:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: error.message,
                        });
                    });
            }
        });
    });

    // Chargement initial
    loadData();
});
</script>
<style>
    .item-details {
        background-color: #f8f9fa;
        border-left: 4px solid #007bff !important;
    }
    
    .item-details h6 {
        margin-bottom: 10px;
        font-size: 14px;
    }
    
    .item-details small {
        font-size: 12px;
    }
    
    #devis_items_section .card-header {
        padding: 10px 15px;
        font-size: 14px;
    }
    .item-details {
        background-color: #f8f9fa;
        border-left: 4px solid #007bff !important;
    }
    
    .item-details h6 {
        margin-bottom: 10px;
        font-size: 14px;
    }
    
    .item-details small {
        font-size: 12px;
    }
    
    #devis_items_section .card-header {
        padding: 10px 15px;
        font-size: 14px;
    }
    
    /* Style pour les champs en erreur */
    .editable-field.is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    
    /* Style pour les nouveaux articles */
    .item-details.new-item {
        border-left: 4px solid #28a745 !important;
        background-color: #f8fff9;
    }
    </style>
@endsection