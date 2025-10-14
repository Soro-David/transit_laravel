{{-- views/AFT_LOUIS_BLERIOT/transport/planing.blade.php --}}
@extends('AFT_LOUIS_BLERIOT.layouts.agent')

@section('content-header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
<<<<<<< HEAD
                <h1 class="m-0">Gestion des Programmes</h1>
=======
                <h1 class="m-0 text-primary">
                    <i class="fas fa-calendar-alt mr-2"></i>Programmes de Transport
                </h1>
            </div>
            <div class="col-sm-6 text-right">
                <button class="btn btn-success" id="refreshBtn">
                    <i class="fas fa-sync-alt mr-1"></i>Actualiser
                </button>
>>>>>>> 1aedc3d299d0a565bfd7b5a51b18f7bff19131ee
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Statistiques Rapides -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="totalProgrammes">0</h3>
                    <p>Total Programmes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tasks"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="programmesAttente">0</h3>
                    <p>En Attente</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="programmesEffectue">0</h3>
                    <p>Effectués</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3 id="programmesPlanifie">0</h3>
                    <p>À Planifier</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-plus"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light text-dark">
            <h3 class="card-title mb-0 font-weight-bold">
                <i class="fas fa-road mr-2 text-primary"></i>PLANIFICATION DES TRANSPORTS
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filtres Avancés -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card bg-light">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="card-title font-weight-bold text-dark">
                                <i class="fas fa-filter mr-1 text-primary"></i>Filtres et Recherche
                            </h5>
                        </div>
                        <div class="card-body bg-light">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0">
                                                <i class="fas fa-search text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="text" id="search" class="form-control border-left-0" placeholder="Référence, nom, téléphone, adresse...">
                                    </div>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <select id="actionFilter" class="form-control select2">
                                        <option value="">Toutes les actions</option>
                                        <option value="depot">Dépôt</option>
                                        <option value="recuperation">Récupération</option>
                                        <option value="livraison">Livraison</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <select id="etatFilter" class="form-control select2">
                                        <option value="">Tous les états</option>
                                        <option value="à planifié">À planifié</option>
                                        <option value="en attente">En attente</option>
                                        <option value="programmé">Programmé</option>
                                        <option value="effectué">Effectué</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <select id="chauffeurFilter" class="form-control select2">
                                        <option value="">Tous chauffeurs</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <div class="btn-group w-100">
                                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="pageSizeDropdown" data-toggle="dropdown">
                                            <i class="fas fa-list-ol mr-1"></i><span id="pageSizeDisplay">10</span>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item page-size-btn" href="#" data-size="10">
                                                <i class="fas fa-list mr-1"></i>10 éléments
                                            </a>
                                            <a class="dropdown-item page-size-btn" href="#" data-size="25">
                                                <i class="fas fa-list-alt mr-1"></i>25 éléments
                                            </a>
                                            <a class="dropdown-item page-size-btn" href="#" data-size="50">
                                                <i class="fas fa-th-list mr-1"></i>50 éléments
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableau des programmes -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 80px;">
                                <i class="fas fa-hashtag text-muted"></i> Qté
                            </th>
                            <th class="text-center" style="width: 120px;">
                                <i class="fas fa-bolt text-muted"></i> Action
                            </th>
                            <th style="width: 150px;">
                                <i class="fas fa-barcode text-muted"></i> Référence
                            </th>
                            <th style="width: 120px;">
                                <i class="fas fa-box text-muted"></i> Nature
                            </th>
                            <th>
                                <i class="fas fa-user text-muted"></i> Client
                            </th>
                            <th style="width: 130px;">
                                <i class="fas fa-phone text-muted"></i> Téléphone
                            </th>
                            <th>
                                <i class="fas fa-map-marker-alt text-muted"></i> Adresse
                            </th>
                            <th style="width: 150px;">
                                <i class="fas fa-id-card text-muted"></i> Chauffeur
                            </th>
                            <th class="text-center" style="width: 130px;">
                                <i class="fas fa-flag text-muted"></i> État
                            </th>
                            <th class="text-center" style="width: 100px;">
                                <i class="fas fa-cogs text-muted"></i> Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="programmes-table">
                        <!-- Données chargées via JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Indicateur de chargement -->
            <div id="loadingIndicator" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Chargement...</span>
                </div>
                <p class="mt-2 text-muted">Chargement des programmes...</p>
            </div>

            <!-- Pagination -->
            <div class="mt-4 d-flex justify-content-between align-items-center">
                <div class="text-muted" id="paginationInfo">
                    Affichage de 0 à 0 sur 0 éléments
                </div>
                <nav aria-label="Page navigation">
                    <ul id="pagination" class="pagination pagination-sm">
                        <!-- Généré par JavaScript -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
<<<<<<< HEAD

<!-- Modal pour Dépôt Multiple -->
<div class="modal fade" id="depotModal" tabindex="-1" role="dialog" aria-labelledby="depotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="depotModalLabel">Programmer des Dépôts</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="depotForm" action="{{ route('aftlb_transport.programme.createMultipleDepot') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Section pour programmes multiples -->
                    <div class="card mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Programmes de dépôt</h6>
                            <button type="button" class="btn btn-sm btn-success" id="add-depot-programme-btn">
                                <i class="fas fa-plus"></i> Ajouter un autre dépôt
                            </button>
                        </div>
                        <div class="card-body" id="depot-programmes-container">
                            <!-- Premier programme (toujours présent) -->
                            <div class="depot-programme-item border rounded p-3 mb-3" data-depot-programme-index="0">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="text-primary mb-0">Dépôt #1</h6>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-depot-programme-btn" style="display: none;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="quantite_depot_0">Quantité *</label>
                                            <input type="number" class="form-control" id="quantite_depot_0" name="programmes[0][quantite]" required min="1">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nature_du_colis_depot_0">Nature de l'objet à déposer *</label>
                                            <input type="text" class="form-control" id="nature_du_colis_depot_0" name="programmes[0][nature_du_colis]" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nom_expediteur_depot_0">Nom du Concerné *</label>
                                            <input type="text" class="form-control" id="nom_expediteur_depot_0" name="programmes[0][nom_expediteur]" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tel_expediteur_depot_0">Numéro de Téléphone *</label>
                                            <input type="text" class="form-control" id="tel_expediteur_depot_0" name="programmes[0][tel_expediteur]" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lieu_expedition_depot_0">Adresse de Dépôt *</label>
                                            <textarea class="form-control" id="lieu_expedition_depot_0" name="programmes[0][lieu_expedition]" rows="2" required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations communes à tous les programmes -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Informations communes</h6>
                        </div>
                        <div class="card-body">
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
                                        <label for="date_programme_depot">Date du Programme *</label>
                                        <input type="date" class="form-control" id="date_programme_depot" name="date_programme" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-success">Enregistrer les Dépôts</button>
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
            <form id="recuperationForm" action="{{ route('aftlb_transport.programme.createMultipleRecuperation') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Section pour programmes multiples -->
                    <div class="card mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Programmes de récupération</h6>
                            <button type="button" class="btn btn-sm btn-success" id="add-programme-btn">
                                <i class="fas fa-plus"></i> Ajouter un autre programme
                            </button>
                        </div>
                        <div class="card-body" id="programmes-container">
                            <!-- Premier programme (toujours présent) -->
                            <div class="programme-item border rounded p-3 mb-3" data-programme-index="0">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="text-primary mb-0">Programme #1</h6>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-programme-btn" style="display: none;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type_reference_0">Type de récupération <span style="color: brown">*</span></label>
                                            <select class="form-control type-reference" id="type_reference_0" name="programmes[0][type_reference]" data-index="0">
                                                <option value="">-- Sélectionner le type --</option>
                                                <option value="devis">Devis confirmé</option>
                                                <option value="depot">Dépôt effectué</option>
                                                <option value="manuel">Référence manuelle</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="reference_input_0">Référence <span style="color: brown"></span></label>
                                            <input type="text" class="form-control reference-input" id="reference_input_0" name="programmes[0][reference_input]" 
                                                   placeholder="Entrez la référence du devis, dépôt ou une référence manuelle" data-index="0">
                                            <small class="form-text text-center" style="font-size: 10px; color:brown">
                                                Référence devis confirmé, dépôt effectué ou référence personnalisée
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="quantite_recup_0">Quantité totale <span style="color: brown">*</span></label>
                                            <input type="number" class="form-control" id="quantite_recup_0" name="programmes[0][quantite]" min="1">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nature_du_colis_recup_0">Nature du Colis <span style="color: brown">*</span></label>
                                            <input type="text" class="form-control" id="nature_du_colis_recup_0" name="programmes[0][nature_du_colis]">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nom_expediteur_recup_0">Nom du Client <span style="color: brown">*</span></label>
                                            <input type="text" class="form-control" id="nom_expediteur_recup_0" name="programmes[0][nom_expediteur]">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tel_expediteur_recup_0">Numéro de Téléphone <span style="color: brown">*</span></label>
                                            <input type="text" class="form-control" id="tel_expediteur_recup_0" name="programmes[0][tel_expediteur]">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lieu_expedition_recup_0">Adresse de Récupération <span style="color: brown">*</span></label>
                                            <textarea class="form-control" id="lieu_expedition_recup_0" name="programmes[0][lieu_expedition]" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section devis_items pour ce programme -->
                                <div class="devis-items-section" id="devis_items_section_0" style="display: none;">
                                    <div class="card mt-3">
                                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">Informations détaillées du colis</h6>
                                            <button type="button" class="btn btn-sm btn-light toggle-edit-mode" data-index="0">
                                                <i class="fas fa-edit"></i> Modifier
                                            </button>
                                        </div>
                                        <div class="card-body devis-items-content" id="devis_items_content_0">
                                            <!-- Les informations des devis_items seront injectées ici -->
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info reference-info" id="reference_info_0" style="display: none;">
                                    <i class="fas fa-info-circle"></i> <span class="reference-message"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations communes à tous les programmes -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Informations communes</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="user_id_recup">Chauffeur <span style="color: brown">*</span></label>
                                        <select class="form-control" id="user_id_recup" name="user_id">
                                            <option value="">-- Sélectionner un Chauffeur --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_programme_recup">Date du Programme <span style="color: brown">*</span></label>
                                        <input type="date" class="form-control" id="date_programme_recup" name="date_programme">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-warning">Enregistrer les Récupérations</button>
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

  

=======
>>>>>>> 1aedc3d299d0a565bfd7b5a51b18f7bff19131ee
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">

<script>
<<<<<<< HEAD
        $(document).ready(function() {

            // =========================================================================
            // 1. VARIABLES GLOBALES ET INITIALISATION
            // =========================================================================

            let programmesData = { programmes: [], chauffeurs: [] };
            let currentPage = 1;
            let itemsPerPage = 10;
            let programmeCount = 1;

            // Lancement de l'application
            initApp();
=======
$(document).ready(function() {
    let programmesData = [];
    let currentPage = 1;
    let itemsPerPage = 10;
    let totalItems = 0;

    function initApp() {
        // Initialiser Select2
        $('.select2').select2({
            width: '100%',
            placeholder: 'Sélectionnez...'
        });
>>>>>>> 1aedc3d299d0a565bfd7b5a51b18f7bff19131ee

        loadData();
        
        // Événements de filtrage
        $('#search').on('input', debounce(function() {
            currentPage = 1;
            updateTable();
        }, 300));

<<<<<<< HEAD
            // =========================================================================
            // 2. FONCTIONS DE GESTION DU TABLEAU PRINCIPAL
            // =========================================================================

            function loadData() {
                axios.get("{{ route('aftlb_transport.programme.data') }}")
                    .then(function(response) {
                        if (!response.data) throw new Error('Réponse vide du serveur');
                        programmesData.programmes = response.data.programmes || [];
                        programmesData.chauffeurs = response.data.chauffeurs || [];
                        updateChauffeurSelects();
                        updateTable();
                    })
                    .catch(function(error) {
                        console.error('❌ Erreur de chargement des données:', error);
                        Swal.fire('Erreur', 'Impossible de charger les données du serveur.', 'error');
                    });
            }

            function updateChauffeurSelects() {
                const selects = $('#user_id_depot, #user_id_recup');
                selects.empty().append('<option value="">-- Sélectionner un Chauffeur --</option>');
                
                if (programmesData.chauffeurs && programmesData.chauffeurs.length > 0) {
                    programmesData.chauffeurs.forEach(function(chauffeur) {
                        if (chauffeur && chauffeur.id) {
                            const nomComplet = `${chauffeur.first_name || ''} ${chauffeur.last_name || ''}`.trim();
                            selects.append(`<option value="${chauffeur.id}">${nomComplet}</option>`);
                        }
                    });
                }
            }

            function generateTable(programmes) {
            const tableBody = $('#programmes-table');
            tableBody.empty();
            
            if (!programmes || programmes.length === 0) {
                tableBody.append('<tr><td colspan="10" class="text-center">Aucun programme trouvé.</td></tr>');
                return;
            }
            
            programmes.forEach(p => {
                // VÉRIFICATION DE SÉCURITÉ - éviter les erreurs si p est null/undefined
                if (!p) return;
                
                const user = p.user ? `${p.user.first_name || ''} ${p.user.last_name || ''}`.trim() : 'N/A';
                const reference = p.reference_a_afficher || p.reference_generee || p.reference_colis || 'N/A';
                const quantite = p.quantite || 1;
                const nature = p.nature_du_colis || 'N/A';
                const expediteur = p.nom_expediteur || 'N/A';
                const tel = p.tel_expediteur || 'N/A';
                const lieu = p.lieu_expedition || 'N/A';
                const etat = p.etat_rdv || 'N/A';
                
                tableBody.append(`
                    <tr class="${etat === 'effectué' ? 'table-success' : etat === 'à planifié' ? 'table-warning' : ''}">
                        <td>${quantite}</td>
                        <td>
                            <span class="badge ${getActionBadgeClass(p.actions_a_faire)}">
                                ${getActionText(p.actions_a_faire)}
                            </span>
                        </td>
                        <td>${reference}</td>
                        <td>${nature}</td>
                        <td>${expediteur}</td>
                        <td>${tel}</td>
                        <td>${lieu}</td>
                        <td>${user}</td>
                        <td>
                            <span class="badge ${getEtatBadgeClass(etat)}">
                                ${etat}
                            </span>
                        </td>
                        <td>
                            ${etat !== 'effectué' ? 
                            `<a href="{{ route('aftlb_transport.programme.edit.page', '') }}/${p.id}-aft-louis-b" class="btn btn-sm btn-info edit-programme-btn" data-id="${p.id}">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                <button class="btn btn-sm btn-danger delete-programme-btn" data-id="${p.id}">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>` 
                                : 
                                `<button class="btn btn-sm btn-secondary" disabled title="Programme déjà effectué">
                                    <i class="fas fa-edit"></i> Modifier
                                </button>
                                <button class="btn btn-sm btn-secondary" disabled title="Programme déjà effectué">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>`
                            }
                        </td>
                    </tr>
                `);
            });
        }  
        function updateTable() {
                const filtered = filterProgrammes();
                const paginated = filtered.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);
                generateTable(paginated);
                generatePagination(filtered.length);
                
                // Mettre à jour le compteur de résultats
                updateResultsCount(filtered.length);
            }
            function updateResultsCount(count) {
                const totalCount = programmesData.programmes.length;
                const filteredCount = count;
                
                let countText = `Affichage de ${filteredCount} programme(s)`;
                if (filteredCount !== totalCount) {
                    countText += ` (filtrés sur ${totalCount} au total)`;
                }
                
                // Créer ou mettre à jour l'élément de compteur
                let countElement = $('#resultsCount');
                if (countElement.length === 0) {
                    $('.table-responsive').before(`<div id="resultsCount" class="mb-2 text-muted small">${countText}</div>`);
                } else {
                    countElement.text(countText);
                }
            }
            function generatePagination(totalItems) {
                const totalPages = Math.ceil(totalItems / itemsPerPage);
                $('#pagination').empty();
                if (totalPages <= 1) return;
                for (let i = 1; i <= totalPages; i++) {
                    $('#pagination').append(`<li class="page-item ${currentPage === i ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`);
                }
            }


            // =========================================================================
            // 3. FONCTIONS DE GESTION DES ARTICLES (LOGIQUE DE L'ANCIEN CODE)
            // =========================================================================

            function generateEditableItemsHTML(items, container, programmeIndex) {
            let html = '';
            
            // Récupérer le mode transit depuis les données stockées
            const pItem = $(`.programme-item[data-programme-index="${programmeIndex}"]`);
            const itemsSection = pItem.find('.devis-items-section');
            const devisInfo = itemsSection.data('devis-info') || {};
            const modeTransit = devisInfo.mode_transit || 'aerien';
            
            console.log(`🚚 Mode transit détecté pour programme ${programmeIndex}:`, modeTransit);
            
            items.forEach((item, index) => {
                const itemClass = item.is_new ? 'new-item' : '';
                
                // Déterminer quels champs afficher selon le mode transit
                const showDimensions = modeTransit === 'maritime';
                const showPoids = modeTransit === 'aerien';
                const dimensionsClass = showDimensions ? '' : 'd-none';
                const poidsClass = showPoids ? '' : 'd-none';
                
                html += `
                    <div class="item-details mb-3 p-3 border rounded ${itemClass} ${modeTransit === 'maritime' ? 'maritime-item' : 'aerien-item'}" data-item-index="${index}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="text-primary mb-0">Article ${index + 1} ${item.is_new ? '<span class="badge badge-success ml-2">Nouveau</span>' : ''}</h6>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn" data-index="${index}" style="display: none;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        
                        <!-- Section MODE TRANSIT modifiable -->
                        <div class="mb-3">
                            <div class="form-group">
                                <label class="small"><strong>Mode de transit:</strong></label>
                                <select class="form-control form-control-sm editable-field mode-transit-select" 
                                        data-field="mode_transit" data-original="${modeTransit}" readonly>
                                    <option value="aerien" ${modeTransit === 'aerien' ? 'selected' : ''}>Aérien</option>
                                    <option value="maritime" ${modeTransit === 'maritime' ? 'selected' : ''}>Maritime</option>
                                </select>
                            </div>
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
                                    <select class="form-control form-control-sm editable-field type-colis-select" 
                                            data-field="type_colis" data-original="${item.type_colis || 'standard'}" readonly>
                                        <option value="standard" ${(item.type_colis || 'standard') === 'standard' ? 'selected' : ''}>Standard</option>
                                        <option value="fragile" ${(item.type_colis || 'standard') === 'fragile' ? 'selected' : ''}>Fragile</option>
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label class="small"><strong>Valeur (FCFA):</strong></label>
                                    <input type="number" class="form-control form-control-sm editable-field" value="${item.valeur_colis || ''}" 
                                        data-field="valeur_colis" data-original="${item.valeur_colis || ''}" readonly step="0.01">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- POIDS - Visible uniquement pour AÉRIEN -->
                                <div class="form-group mb-2 ${poidsClass}">
                                    <label class="small"><strong>Poids (kg):</strong></label>
                                    <input type="number" class="form-control form-control-sm editable-field" value="${item.poids || ''}" 
                                        data-field="poids" data-original="${item.poids || ''}" readonly step="0.01">
                                </div>
                                
                                <!-- DIMENSIONS - Visibles uniquement pour MARITIME -->
                                <div class="dimensions-section ${dimensionsClass}">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-group mb-2">
                                                <label class="small"><strong>Longueur (cm):</strong></label>
                                                <input type="number" class="form-control form-control-sm editable-field" value="${item.longueur || ''}" 
                                                    data-field="longueur" data-original="${item.longueur || ''}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group mb-2">
                                                <label class="small"><strong>Largeur (cm):</strong></label>
                                                <input type="number" class="form-control form-control-sm editable-field" value="${item.largeur || ''}" 
                                                    data-field="largeur" data-original="${item.largeur || ''}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group mb-2">
                                                <label class="small"><strong>Hauteur (cm):</strong></label>
                                                <input type="number" class="form-control form-control-sm editable-field" value="${item.hauteur || ''}" 
                                                    data-field="hauteur" data-original="${item.hauteur || ''}" readonly>
                                            </div>
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
                        
                        <div class="form-group mb-2">
                            <label class="small"><strong>Description:</strong></label>
                            <textarea class="form-control form-control-sm editable-field" data-field="description_colis" 
                                    data-original="${item.description_colis || ''}" readonly rows="2">${item.description_colis || ''}</textarea>
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
            
        // CORRECTION : Toujours afficher le bouton d'ajout
        html += `
                <div class="text-center mt-3" id="add-item-section-${programmeIndex}">
                    <button type="button" class="btn btn-sm btn-primary add-new-item-btn" data-programme-index="${programmeIndex}">
                        <i class="fas fa-plus"></i> Ajouter un nouvel article
                    </button>
                </div>
            `;
            
            container.html(html);
        }
        function generateItemFields(item, index, modeTransit, dimensionsClass, poidsClass) {
            return `
                <div class="mb-3">
                    <div class="form-group">
                        <label class="small"><strong>Mode de transit:</strong></label>
                        <select class="form-control form-control-sm editable-field mode-transit-select" 
                                data-field="mode_transit" data-original="${modeTransit}" readonly>
                            <option value="aerien" ${modeTransit === 'aerien' ? 'selected' : ''}>Aérien</option>
                            <option value="maritime" ${modeTransit === 'maritime' ? 'selected' : ''}>Maritime</option>
                        </select>
                    </div>
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
                            <select class="form-control form-control-sm editable-field type-colis-select" 
                                    data-field="type_colis" data-original="${item.type_colis || 'standard'}" readonly>
                                <option value="standard" ${(item.type_colis || 'standard') === 'standard' ? 'selected' : ''}>Standard</option>
                                <option value="fragile" ${(item.type_colis || 'standard') === 'fragile' ? 'selected' : ''}>Fragile</option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label class="small"><strong>Valeur (FCFA):</strong></label>
                            <input type="number" class="form-control form-control-sm editable-field" value="${item.valeur_colis || ''}" 
                                data-field="valeur_colis" data-original="${item.valeur_colis || ''}" readonly step="0.01">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-2 ${poidsClass}">
                            <label class="small"><strong>Poids (kg):</strong></label>
                            <input type="number" class="form-control form-control-sm editable-field" value="${item.poids || ''}" 
                                data-field="poids" data-original="${item.poids || ''}" readonly step="0.01">
                        </div>
                        
                        <div class="dimensions-section ${dimensionsClass}">
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group mb-2">
                                        <label class="small"><strong>Longueur (cm):</strong></label>
                                        <input type="number" class="form-control form-control-sm editable-field" value="${item.longueur || ''}" 
                                            data-field="longueur" data-original="${item.longueur || ''}" readonly>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group mb-2">
                                        <label class="small"><strong>Largeur (cm):</strong></label>
                                        <input type="number" class="form-control form-control-sm editable-field" value="${item.largeur || ''}" 
                                            data-field="largeur" data-original="${item.largeur || ''}" readonly>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group mb-2">
                                        <label class="small"><strong>Hauteur (cm):</strong></label>
                                        <input type="number" class="form-control form-control-sm editable-field" value="${item.hauteur || ''}" 
                                            data-field="hauteur" data-original="${item.hauteur || ''}" readonly>
                                    </div>
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
                
                <div class="form-group mb-2">
                    <label class="small"><strong>Description:</strong></label>
                    <textarea class="form-control form-control-sm editable-field" data-field="description_colis" 
                            data-original="${item.description_colis || ''}" readonly rows="2">${item.description_colis || ''}</textarea>
                </div>
            `;
        }
        function toggleEditMode(programmeIndex, forceOn) {
            if (forceOn === undefined) forceOn = false;
            const pItem = $(`.programme-item[data-programme-index="${programmeIndex}"]`);
            // VÉRIFICATION DE SÉCURITÉ
            if (pItem.length === 0) {
                console.error(`❌ Programme item avec index ${programmeIndex} non trouvé`);
                // Tentative de fallback
                const fallbackItem = $('.programme-item').first();
                if (fallbackItem.length > 0) {
                    const fallbackIndex = fallbackItem.data('programme-index');
                    console.log(`🔄 Utilisation du fallback index: ${fallbackIndex}`);
                    pItem = fallbackItem;
                    programmeIndex = fallbackIndex;
                } else {
                    console.error('❌ Aucun programme item trouvé');
                    return;
                }
            }
            const btn = pItem.find('.toggle-edit-mode');
            const content = pItem.find('.devis-items-content');
            const isEdit = btn.hasClass('btn-warning');

            console.log("🔄 toggleEditMode appelé pour programme", programmeIndex, "forceOn:", forceOn, "isEdit:", isEdit);

            if (!isEdit || forceOn) {
                // Activer le mode édition
                btn.html('<i class="fas fa-eye"></i> Visualiser').removeClass('btn-light').addClass('btn-warning');
                
                // Activer tous les champs éditables
                content.find('.editable-field').prop('readonly', false).addClass('border-primary');
                content.find('.mode-transit-select').prop('readonly', false);
                content.find('.type-colis-select').prop('readonly', false);
                
                // Afficher les boutons d'action
                content.find('.remove-item-btn').show();
                content.find('.item-actions').show();
                $(`#add-item-section-${programmeIndex}`).show();
                
                console.log("✅ Mode édition activé pour le programme", programmeIndex);
            } else {
                // Désactiver le mode édition
                btn.html('<i class="fas fa-edit"></i> Modifier').removeClass('btn-warning').addClass('btn-light');
                
                // Désactiver tous les champs
                content.find('.editable-field').prop('readonly', true).removeClass('border-primary');
                content.find('.mode-transit-select').prop('readonly', true);
                content.find('.type-colis-select').prop('readonly', true);
                
                // Cacher les boutons d'action
                content.find('.remove-item-btn').hide();
                content.find('.item-actions').hide();
                $(`#add-item-section-${programmeIndex}`).hide();
                
                // Annuler les modifications non sauvegardées
                content.find('.editable-field').each(function() {
                    const original = $(this).data('original');
                    $(this).val(original);
                });
                
                updateTotalQuantity(programmeIndex);
                console.log("🔒 Mode édition désactivé pour le programme", programmeIndex);
            }
        }
        function updateTotalQuantity(programmeIndex) {
            const pItem = $(`.programme-item[data-programme-index="${programmeIndex}"]`);
            const itemsSection = pItem.find('.devis-items-section');
            const currentItems = itemsSection.data('devis-items') || [];
            
            const totalQuantity = currentItems.reduce((total, item) => {
                return total + parseInt(item.quantite_colis || 1);
            }, 0);
            
            pItem.find('[name$="[quantite]"]').val(totalQuantity);
        }

        function showTempMessage(message, type = 'info', programmeIndex = 0) {
            const alertClass = type === 'success' ? 'alert-success' : 
                            type === 'error' ? 'alert-danger' : 'alert-info';
            const tempAlert = $(`<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>`);
            
            $(`.programme-item[data-programme-index="${programmeIndex}"] .reference-info`).after(tempAlert);
            
            setTimeout(() => {
                tempAlert.alert('close');
            }, 3000);
        }
        function initAddItemButtons() {
            $('.programme-item').each(function() {
                const programmeIndex = $(this).data('programme-index');
                const itemsSection = $(this).find('.devis-items-section');
                const currentItems = itemsSection.data('devis-items') || [];
                
                // Afficher le bouton seulement s'il y a des articles
                if (currentItems.length > 0) {
                    $(`#add-item-section-${programmeIndex}`).show();
                }
            });
        }

            // =========================================================================
            // 4. GESTION DES PROGRAMMES MULTIPLES
            // =========================================================================

            function searchReferenceInfo(reference, typeReference, programmeIndex) {
            if (reference.length < 3 || !typeReference) return;
            
            let url = `{{ route('aftlb_transport.programme.referenceInfo', ['reference' => 'PLACEHOLDER']) }}`.replace('PLACEHOLDER', reference);
            
            axios.get(url)
                .then(res => {
                    const { data } = res;
                    const pItem = $(`.programme-item[data-programme-index="${programmeIndex}"]`);
                    const itemsSection = pItem.find('.devis-items-section');
                    
                    if (data.existe) {
                        // Auto-remplissage des champs de base
                        pItem.find('[name$="[nom_expediteur]"]').val(data.data.nom_expediteur || '');
                        pItem.find('[name$="[lieu_expedition]"]').val(data.data.lieu_expedition || '');
                        pItem.find('[name$="[tel_expediteur]"]').val(data.data.tel_expediteur || '');
                        pItem.find('[name$="[nature_du_colis]"]').val(data.data.nature_du_colis || '');
                        
                        // Quantité totale
                        const quantiteTotale = data.data.quantite || 1;
                        pItem.find('[name$="[quantite]"]').val(quantiteTotale);
                        
                        // Gestion des items
                        if (data.data.items && data.data.items.length > 0) {
                            // Stocker les items ET les informations du devis (dont mode_transit)
                            itemsSection.data('devis-items', JSON.parse(JSON.stringify(data.data.items)));
                            itemsSection.data('devis-info', {
                                mode_transit: data.data.mode_transit || 'aerien',
                                agence_destination: data.data.agence_destination,
                                montant: data.data.montant,
                                devise: data.data.devise,
                                quantite_originale: data.data.quantite
                            });
                            
                            console.log(`📦 Mode transit récupéré: ${data.data.mode_transit || 'aerien'} pour programme ${programmeIndex}`);
                            
                            // Générer l'affichage des items avec le bon mode transit
                            generateEditableItemsHTML(data.data.items, itemsSection.find('.devis-items-content'), programmeIndex);
                            itemsSection.show();
                            
                            // Initialiser les événements d'édition
                            initEditModeForProgramme(programmeIndex);
                        } else {
                            itemsSection.hide();
                            itemsSection.removeData('devis-items');
                            itemsSection.removeData('devis-info');
                        }

                        // Afficher les informations de référence
                        const infoDiv = pItem.find('.reference-info');
                        const messageSpan = pItem.find('.reference-message');
                        
                        if (data.type === 'devis') {
                            if (data.etat_devis === 'confirmé') {
                                infoDiv.removeClass('alert-danger alert-warning').addClass('alert-success');
                                messageSpan.html(`
                                    <strong>✅ Devis confirmé trouvé!</strong><br>
                                    <small>• Mode transit: ${data.data.mode_transit || 'N/A'}<br>
                                    • Agence destination: ${data.data.agence_destination || 'N/A'}<br>
                                    • Montant: ${data.data.montant || 'N/A'} ${data.data.devise || ''}<br>
                                    • Quantité totale: ${quantiteTotale} article(s)</small>
                                `);
                            }
                        } else if (data.type === 'depot') {
                            infoDiv.removeClass('alert-danger alert-warning').addClass('alert-success');
                            messageSpan.text('✅ Dépôt effectué trouvé - Remplissage automatique effectué - Quantité: ' + quantiteTotale);
                        }
                    } else {
                        // Référence manuelle - par défaut mode aérien
                        const infoDiv = pItem.find('.reference-info');
                        infoDiv.removeClass('alert-success alert-danger').addClass('alert-info');
                        pItem.find('.reference-message').text('ℹ️ Référence manuelle - Veuillez remplir les informations manuellement');
                        
                        // Pour les références manuelles, définir le mode transit par défaut (aérien)
                        itemsSection.data('devis-info', {
                            mode_transit: 'aerien',
                            quantite_originale: 1
                        });
                        
                        // Masquer la section devis_items
                        itemsSection.hide();
                        itemsSection.removeData('devis-items');
                    }
                    
                    pItem.find('.reference-info').show();
                })
                .catch(err => {
                    console.error('Erreur recherche référence:', err);
                    $(`.programme-item[data-programme-index="${programmeIndex}"] .reference-info`).hide();
                    $(`.programme-item[data-programme-index="${programmeIndex}"] .devis-items-section`).hide();
                });
        }

        function initEditModeForProgramme(programmeIndex) {
            const pItem = $(`.programme-item[data-programme-index="${programmeIndex}"]`);
            
            if (pItem.length === 0) {
                console.warn(`⚠️ Programme item ${programmeIndex} non trouvé pour initEditModeForProgramme`);
                return;
            }
            const itemsSection = pItem.find('.devis-items-section');
            
            // Gestion du changement de mode transit
            pItem.off('change.modeTransit').on('change.modeTransit', '.mode-transit-select', function(e) {
                e.stopImmediatePropagation();
                const itemIndex = $(this).closest('.item-details').data('item-index');
                const newModeTransit = $(this).val();
                
                console.log(`🔄 Changement mode transit pour item ${itemIndex}:`, newModeTransit);
                
                // Mettre à jour l'affichage des champs selon le mode transit
                const itemElement = $(this).closest('.item-details');
                
                if (newModeTransit === 'maritime') {
                    // Afficher dimensions, masquer poids
                    itemElement.find('.dimensions-section').removeClass('d-none');
                    itemElement.find('.form-group.mb-2:has(input[data-field="poids"])').addClass('d-none');
                } else {
                    // Afficher poids, masquer dimensions
                    itemElement.find('.dimensions-section').addClass('d-none');
                    itemElement.find('.form-group.mb-2:has(input[data-field="poids"])').removeClass('d-none');
                }
                
                // Mettre à jour les données stockées
                const currentItems = itemsSection.data('devis-items');
                if (currentItems && currentItems[itemIndex]) {
                    currentItems[itemIndex].mode_transit = newModeTransit;
                    itemsSection.data('devis-items', currentItems);
                }
            });
            
            // Sauvegarde d'un item
            pItem.off('click.saveItem').on('click.saveItem', '.save-item-btn', function(e) {
                e.stopImmediatePropagation();
                const index = $(this).data('index');
                const itemElement = pItem.find(`.item-details[data-item-index="${index}"]`);
                
                // Récupérer les données modifiées
                const updatedItem = {};
                let hasErrors = false;
                
                itemElement.find('.editable-field').each(function() {
                    const field = $(this).data('field');
                    let value = $(this).val();
                    
                    // Validation
                    if (field === 'service' && !value.trim()) {
                        $(this).addClass('is-invalid');
                        hasErrors = true;
                        return;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                    
                    // Convertir les valeurs numériques
                    if (field === 'quantite_colis') {
                        value = parseInt(value) || 1;
                        if (value < 1) value = 1;
                    } else if (field === 'valeur_colis' || field === 'poids' || field === 'longueur' || field === 'largeur' || field === 'hauteur') {
                        value = parseFloat(value) || 0;
                        if (value < 0) value = 0;
                    }
                    
                    updatedItem[field] = value;
                    $(this).data('original', value);
                });
                
                if (hasErrors) {
                    showTempMessage('Veuillez remplir tous les champs obligatoires', 'error', programmeIndex);
                    return;
                }
                
                // Mettre à jour les données stockées
                const currentItems = itemsSection.data('devis-items');
                
                if (currentItems && currentItems[index]) {
                    updatedItem.is_new = currentItems[index].is_new || false;
                    updatedItem.id = currentItems[index].id || `item_${index}`;
                    
                    currentItems[index] = { ...currentItems[index], ...updatedItem };
                    itemsSection.data('devis-items', currentItems);
                    
                    updateTotalQuantity(programmeIndex);
                    showTempMessage('Article ' + (index + 1) + ' sauvegardé', 'success', programmeIndex);
                    
                    // Désactiver le mode édition pour cet item
                    itemElement.find('.editable-field').prop('readonly', true).removeClass('border-primary');
                    itemElement.find('.item-actions').hide();
                    itemElement.find('.remove-item-btn').hide();
                }
            });
            
            // Annuler les modifications
            pItem.off('click.cancelEdit').on('click.cancelEdit', '.cancel-edit-btn', function(e) {
                e.stopImmediatePropagation();
                const index = $(this).data('index');
                const itemElement = pItem.find(`.item-details[data-item-index="${index}"]`);
                
                itemElement.find('.editable-field').each(function() {
                    const original = $(this).data('original');
                    $(this).val(original);
                });
            });
            
            // Supprimer un item
            pItem.off('click.removeItem').on('click.removeItem', '.remove-item-btn', function(e) {
                e.stopImmediatePropagation();
                const index = $(this).data('index');
                const currentItems = itemsSection.data('devis-items');
                
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
                        itemsSection.data('devis-items', currentItems);
                        
                        generateEditableItemsHTML(currentItems, itemsSection.find('.devis-items-content'), programmeIndex);
                        initEditModeForProgramme(programmeIndex);
                        updateTotalQuantity(programmeIndex);
                        
                        Swal.fire('Supprimé!', 'L\'article a été supprimé.', 'success');
                    }
                });
            });
            
            // Ajouter un nouvel item
            pItem.off('click.addNewItem').on('click.addNewItem', '.add-new-item-btn', function(e) {
                e.stopImmediatePropagation();
                e.preventDefault();
                
                const currentItems = itemsSection.data('devis-items') || [];
                const devisInfo = itemsSection.data('devis-info') || {};
                const modeTransit = devisInfo.mode_transit || 'aerien';
                
                const newItem = {
                    quantite_colis: 1,
                    service: 'Nouveau service',
                    valeur_colis: 0,
                    type_colis: 'standard',
                    description_colis: 'Description du nouvel article',
                    mode_transit: modeTransit,
                    poids: modeTransit === 'aerien' ? 1 : 0,
                    longueur: modeTransit === 'maritime' ? 10 : 0,
                    largeur: modeTransit === 'maritime' ? 10 : 0,
                    hauteur: modeTransit === 'maritime' ? 10 : 0,
                    is_new: true,
                    id: 'new_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9)
                };
                
                currentItems.push(newItem);
                itemsSection.data('devis-items', currentItems);
                
                console.log("➕ Nouvel article ajouté avec mode transit:", modeTransit);
                
                generateEditableItemsHTML(currentItems, itemsSection.find('.devis-items-content'), programmeIndex);
                initEditModeForProgramme(programmeIndex);
                
                // Activer automatiquement le mode édition
                if (!pItem.find('.toggle-edit-mode').hasClass('btn-warning')) {
                    toggleEditMode(programmeIndex, true);
                }
            });
        }

        function addProgramme() {
            const newIndex = $('.programme-item').length;
            const template = $($('.programme-item')[0]).clone();
            
            template.attr('data-programme-index', newIndex);
            template.find('h6').text(`Programme #${newIndex + 1}`);
            template.find('input, textarea, select').val('');
            template.find('.devis-items-section').hide().removeData('devis-items').find('.devis-items-content').empty();
            template.find('.reference-info').hide();
            template.find('.remove-programme-btn').show();
            
            template.find('[id]').each(function() { 
                $(this).attr('id', $(this).attr('id').replace('_0', `_${newIndex}`)); 
            });
            template.find('[name]').each(function() { 
                $(this).attr('name', $(this).attr('name').replace('[0]', `[${newIndex}]`)); 
            });
            template.find('[data-index]').each(function() { 
                $(this).attr('data-index', newIndex); 
            });

            $('#programmes-container').append(template);
        }
        function initApp() {
            // DÉFINIR pContainer AVANT DE L'UTILISER
            const pContainer = $('#programmes-container');

            // Tableau principal
            loadData();
            $('#search').on('input', updateTable);
            $('.page-size-btn').on('click', function(e) {
                e.preventDefault();
                itemsPerPage = parseInt($(this).data('size'));
                updateTable();
            });
            $('#pagination').on('click', 'a', function(e) {
                e.preventDefault();
                currentPage = parseInt($(this).data('page'));
                updateTable();
            });
            
            // NOUVEL ÉVÉNEMENT pour le filtre d'action
            $('#actionFilter').on('change', function() {
                currentPage = 1; // Retour à la première page
                updateTable();
            });
            
            // Initialiser les modales
            initDepotModal();
            initRecuperationModal(); // Cette fonction inclut maintenant initReferenceHandling()
            initAddItemButtons();
            
            console.log("✅ Événements initialisés");
        }
                // Modale de récupération
                $('#add-programme-btn').on('click', addProgramme);
                pContainer.on('click', '.remove-programme-btn', function() {
                    if ($('.programme-item').length > 1) $(this).closest('.programme-item').remove();
                });
                pContainer.on('input', '.reference-input', function() {
                    const pItem = $(this).closest('.programme-item');
                    searchReferenceInfo($(this).val(), pItem.find('.type-reference').val(), pItem.data('programme-index'));
                });

                // Logique d'édition des articles
            // Logique d'édition des articles - DÉLÉGATION D'ÉVÉNEMENT CORRECTE
        // Logique d'édition des articles - DÉLÉGATION D'ÉVÉNEMENT CORRECTE
        // CORRECTION : Délégation d'événement pour les boutons Modifier
        $(document).on('click', '.toggle-edit-mode', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const button = $(this);
            const programmeItem = button.closest('.programme-item');
            const programmeIndex = programmeItem.data('programme-index');
            
            console.log("🎯 Bouton Modifier cliqué");
            console.log("📦 Élément programme-item:", programmeItem.length);
            console.log("🔍 Data programme-index:", programmeIndex);
            console.log("📍 Bouton trouvé dans:", button.closest('.devis-items-section').length ? 'devis-items-section' : 'autre');
            
            if (programmeIndex !== undefined) {
                toggleEditMode(programmeIndex);
            } else {
                console.error("❌ Impossible de trouver programme-index");
                // Fallback: chercher l'index via l'ID de la section
                const sectionId = button.closest('.devis-items-section').attr('id');
                if (sectionId) {
                    const match = sectionId.match(/devis_items_section_(\d+)/);
                    if (match) {
                        const fallbackIndex = parseInt(match[1]);
                        console.log("🔄 Fallback avec index:", fallbackIndex);
                        toggleEditMode(fallbackIndex);
                    }
                }
            }
        });
                pContainer.on('click', '.add-new-item-btn', function() {
                    const pItem = $(this).closest('.programme-item');
                    const itemsSection = pItem.find('.devis-items-section');
                    let items = itemsSection.data('devis-items') || [];
                    items.push({ is_new: true });
                    itemsSection.data('devis-items', items);
                    generateEditableItemsHTML(items, itemsSection.find('.devis-items-content'));
                    toggleEditMode(pItem.data('programme-index'), true);
                });
                pContainer.on('click', '.remove-item-btn', function() {
                    const pItem = $(this).closest('.programme-item');
                    const itemIndex = $(this).closest('.item-details').data('item-index');
                    const itemsSection = pItem.find('.devis-items-section');
                    let items = itemsSection.data('devis-items') || [];
                    if (items.length > 1) {
                        items.splice(itemIndex, 1);
                        itemsSection.data('devis-items', items);
                        generateEditableItemsHTML(items, itemsSection.find('.devis-items-content'));
                        toggleEditMode(pItem.data('programme-index'), true);
                    }
                });
                pContainer.on('input', '.editable-field', function() {
                    const pItem = $(this).closest('.programme-item');
                    const itemIndex = $(this).closest('.item-details').data('item-index');
                    const field = $(this).data('field');
                    let items = pItem.find('.devis-items-section').data('devis-items');
                    if (items && items[itemIndex]) {
                        items[itemIndex][field] = $(this).val();
                    }
                });
                pContainer.on('input', '.editable-field[data-field="quantite_colis"]', function() {
                    const pItem = $(this).closest('.programme-item');
                    let total = 0;
                    pItem.find('.devis-items-content .editable-field[data-field="quantite_colis"]').each(function() {
                        total += parseInt($(this).val()) || 0;
                    });
                    pItem.find('[name$="[quantite]"]').val(total);
                });

                // Soumission du formulaire
            // Soumission du formulaire - Version avec délai

        function addDepotProgramme() {
            const newIndex = $('.depot-programme-item').length;
            const template = $($('.depot-programme-item')[0]).clone();
            
            template.attr('data-depot-programme-index', newIndex);
            template.find('h6').text(`Dépôt #${newIndex + 1}`);
            template.find('input, textarea').val('');
            template.find('.remove-depot-programme-btn').show();
            
            template.find('[id]').each(function() { 
                $(this).attr('id', $(this).attr('id').replace('_0', `_${newIndex}`)); 
            });
            template.find('[name]').each(function() { 
                $(this).attr('name', $(this).attr('name').replace('[0]', `[${newIndex}]`)); 
            });

            $('#depot-programmes-container').append(template);
        }

        function initDepotModal() {
            // Ajouter un programme de dépôt
            $('#add-depot-programme-btn').on('click', addDepotProgramme);
            
            // Supprimer un programme de dépôt
            $('#depot-programmes-container').on('click', '.remove-depot-programme-btn', function() {
                if ($('.depot-programme-item').length > 1) {
                    $(this).closest('.depot-programme-item').remove();
                }
            });
            
            // Soumission du formulaire de dépôt
            $('#depotForm').on('submit', function(e) {
                e.preventDefault();
                
                // Préparer les données de tous les programmes de dépôt
                const programmes = [];
                $('.depot-programme-item').each(function() {
                    const programmeIndex = $(this).data('depot-programme-index');
                    
                    programmes.push({
                        quantite: $(this).find('[name$="[quantite]"]').val(),
                        nature_du_colis: $(this).find('[name$="[nature_du_colis]"]').val(),
                        nom_expediteur: $(this).find('[name$="[nom_expediteur]"]').val(),
                        tel_expediteur: $(this).find('[name$="[tel_expediteur]"]').val(),
                        lieu_expedition: $(this).find('[name$="[lieu_expedition]"]').val()
                    });
                });

                const payload = {
                    user_id: $('#user_id_depot').val(),
                    date_programme: $('#date_programme_depot').val(),
                    programmes: programmes
                };

                // Afficher un indicateur de chargement
                Swal.fire({
                    title: 'Création en cours...',
                    text: 'Veuillez patienter',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                axios.post($(this).attr('action'), payload)
                    .then(res => {
                        Swal.close();
                        
                        Swal.fire({ 
                            icon: 'success', 
                            title: 'Succès!', 
                            html: `<strong>${res.data.message}</strong><br>Créés: ${res.data.created_count} | Échecs: ${res.data.failed_count}`,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            // Fermer le modal
                            $('#depotModal').modal('hide');
                            // Recharger la page complète
                            location.reload();
                        });
                    })
                    .catch(err => {
                        Swal.close();
                        Swal.fire('Erreur', err.response?.data?.message || 'Erreur inconnue', 'error');
                    });
            });
            
            // Nettoyage de la modale à la fermeture
            $('#depotModal').on('hidden.bs.modal', function(){
                $('#depot-programmes-container').html($($('.depot-programme-item')[0]).clone());
                $('#depotForm')[0].reset();
                $('.depot-programme-item').attr('data-depot-programme-index', 0).find('h6').text('Dépôt #1');
                $('.remove-depot-programme-btn').hide();
            });
        }
        // =========================================================================
        // 6. GESTION DU CHAMP RÉFÉRENCE MANUELLE
        // =========================================================================

        function toggleReferenceInput(programmeIndex) {
            const pItem = $(`.programme-item[data-programme-index="${programmeIndex}"]`);
            const typeReference = pItem.find('.type-reference').val();
            const referenceInput = pItem.find('.reference-input');
            
            if (typeReference === 'manuel') {
                // Mode référence manuelle - désactiver et vider le champ
                referenceInput.prop('readonly', true)
                            .addClass('bg-light')
                            .val('')
                            .attr('placeholder', 'Référence générée automatiquement');
                
                // Masquer les sections d'infos
                pItem.find('.reference-info').hide();
                pItem.find('.devis-items-section').hide();
            } else {
                // Mode devis ou dépôt - activer le champ
                referenceInput.prop('readonly', false)
                            .removeClass('bg-light')
                            .attr('placeholder', 'Entrez la référence du devis, dépôt ou une référence manuelle');
            }
        }

        function initReferenceHandling() {
            // Gestion du changement de type de référence
            $('#programmes-container').on('change', '.type-reference', function() {
                const programmeIndex = $(this).closest('.programme-item').data('programme-index');
                toggleReferenceInput(programmeIndex);
                
                // Réinitialiser les champs quand le type change
                if ($(this).val() !== 'manuel') {
                    const pItem = $(this).closest('.programme-item');
                    pItem.find('.reference-info').hide();
                    pItem.find('.devis-items-section').hide();
                }
            });
            
            // Validation avant soumission - permettre les références vides pour le mode manuel
            $('#recuperationForm').on('submit', function(e) {
                let isValid = true;
                const errorMessages = [];
                
                $('.programme-item').each(function(index) {
                    const pItem = $(this);
                    const typeReference = pItem.find('.type-reference').val();
                    const referenceInput = pItem.find('.reference-input').val();
                    
                    // Pour les types devis ou depot, la référence est obligatoire
                    if ((typeReference === 'devis' || typeReference === 'depot') && !referenceInput.trim()) {
                        isValid = false;
                        errorMessages.push(`Le programme #${index + 1} nécessite une référence pour le type "${typeReference}"`);
                        pItem.find('.reference-input').addClass('is-invalid');
                    } else {
                        pItem.find('.reference-input').removeClass('is-invalid');
                    }
                    
                    // Validation des autres champs obligatoires
                    const requiredFields = pItem.find('input[required], textarea[required], select[required]');
                    requiredFields.each(function() {
                        if (!$(this).val().trim()) {
                            isValid = false;
                            $(this).addClass('is-invalid');
                        } else {
                            $(this).removeClass('is-invalid');
                        }
                    });
                });
                
                if (!isValid) {
                    e.preventDefault();
                    let errorMessage = 'Veuillez corriger les erreurs suivantes:';
                    if (errorMessages.length > 0) {
                        errorMessage += '<ul class="text-left mt-2">';
                        errorMessages.forEach(msg => {
                            errorMessage += `<li>${msg}</li>`;
                        });
                        errorMessage += '</ul>';
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur de validation',
                        html: errorMessage,
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
                
                // Préparer les données pour l'envoi
                prepareRecuperationData(e);
            });
        }

        function prepareRecuperationData(e) {
            e.preventDefault();
            
            // Préparer les données de tous les programmes
            const programmes = [];
            $('.programme-item').each(function() {
                const programmeIndex = $(this).data('programme-index');
                const itemsSection = $(this).find('.devis-items-section');
                const devisItems = itemsSection.data('devis-items') || [];
                
                const programmeData = {
                    type_reference: $(this).find('.type-reference').val(),
                    reference_input: $(this).find('.reference-input').val(),
                    quantite: $(this).find('[name$="[quantite]"]').val(),
                    nature_du_colis: $(this).find('[name$="[nature_du_colis]"]').val(),
                    nom_expediteur: $(this).find('[name$="[nom_expediteur]"]').val(),
                    tel_expediteur: $(this).find('[name$="[tel_expediteur]"]').val(),
                    lieu_expedition: $(this).find('[name$="[lieu_expedition]"]').val(),
                    modifications_apportees: devisItems.length > 0
                };
                
                // Inclure les items seulement s'ils existent
                if (devisItems.length > 0) {
                    programmeData.devis_items = JSON.stringify(devisItems);
                }
                
                programmes.push(programmeData);
            });

            const payload = {
                user_id: $('#user_id_recup').val(),
                date_programme: $('#date_programme_recup').val(),
                programmes: programmes
            };

            // Afficher un indicateur de chargement
            Swal.fire({
                title: 'Création en cours...',
                text: 'Veuillez patienter',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            axios.post($('#recuperationForm').attr('action'), payload)
                .then(res => {
                    Swal.close();
                    let msg = `<strong>${res.data.message}</strong><br>Créés: ${res.data.created_count} | Échecs: ${res.data.failed_count}`;
                    if (res.data.details) {
                        msg += '<ul class="text-left mt-2">' + res.data.details.map(d => `<li>${d.reference}: ${d.status}</li>`).join('') + '</ul>';
                    }
                    
                    Swal.fire({ 
                        icon: 'success', 
                        title: 'Succès!', 
                        html: msg,
                        confirmButtonText: 'OK',
                        timer: 5000
                    }).then((result) => {
                        $('#recuperationModal').modal('hide');
                        location.reload();
                    });
                })
                .catch(err => {
                    Swal.close();
                    Swal.fire('Erreur', err.response?.data?.message || 'Erreur inconnue', 'error');
                });
        }
        function initRecuperationModal() {
            // Ajouter un programme de récupération
            $('#add-programme-btn').on('click', addProgramme);
            
            // Supprimer un programme de récupération
            $('#programmes-container').on('click', '.remove-programme-btn', function() {
                if ($('.programme-item').length > 1) {
                    $(this).closest('.programme-item').remove();
                }
            });
            initReferenceHandling();
            
            // AJOUT: Appliquer le comportement initial pour chaque programme existant
            $('.programme-item').each(function() {
                const programmeIndex = $(this).data('programme-index');
                toggleReferenceInput(programmeIndex);
            });
            // Gestion du changement de type de référence
            $('#programmes-container').on('change', '.type-reference', function() {
                const programmeIndex = $(this).closest('.programme-item').data('programme-index');
                const referenceInput = $(this).closest('.programme-item').find('.reference-input');
                
                // Réinitialiser les champs quand le type change
                if ($(this).val() === 'manuel') {
                    referenceInput.val('');
                    $(this).closest('.programme-item').find('.reference-info').hide();
                    $(this).closest('.programme-item').find('.devis-items-section').hide();
                }
            });
            
            // Recherche automatique lors de la saisie de référence
            $('#programmes-container').on('input', '.reference-input', function() {
                const programmeItem = $(this).closest('.programme-item');
                const programmeIndex = programmeItem.data('programme-index');
                const typeReference = programmeItem.find('.type-reference').val();
                const referenceValue = $(this).val();
                
                if (referenceValue.length >= 3 && typeReference) {
                    searchReferenceInfo(referenceValue, typeReference, programmeIndex);
                } else {
                    programmeItem.find('.reference-info').hide();
                    programmeItem.find('.devis-items-section').hide();
                }
            });
            
            // Gestion du mode édition pour les articles
            $('#programmes-container').on('click', '.toggle-edit-mode', function() {
                const programmeIndex = $(this).closest('.programme-item').data('programme-index');
                toggleEditMode(programmeIndex);
            });
            
            // Nettoyage de la modale à la fermeture
            $('#recuperationModal').on('hidden.bs.modal', function(){
                $('#programmes-container').html($($('.programme-item')[0]).clone());
                $('#recuperationForm')[0].reset();
                $('.programme-item').attr('data-programme-index', 0).find('h6').text('Programme #1');
                $('.remove-programme-btn').hide();
                $('.devis-items-section').hide();
                $('.reference-info').hide();
            });
        }
                // Nettoyage de la modale à la fermeture
                $('#recuperationModal').on('hidden.bs.modal', function(){
                    $('#programmes-container').html($($('.programme-item')[0]).clone());
                    $('#recuperationForm')[0].reset();
                    $('.programme-item').attr('data-programme-index', 0).find('h6').text('Programme #1');
                    $('.remove-programme-btn').hide();
                });
            
        // Gestion de la suppression
        $(document).on('click', '.delete-programme-btn', function() {
            let programmeId = $(this).data('id');
            
            Swal.fire({
                title: 'Êtes-vous sûr?',
                text: "Cette action supprimera le programme et tous ses articles!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer!',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`/aftlb_transport/programme-delete/${programmeId}-aft-louis-b`)
                        .then(response => {
                            if (response.data.success) {
                                Swal.fire('Supprimé!', response.data.message, 'success');
                                // Recharger les données
                                loadData();
                            } else {
                                Swal.fire('Erreur!', response.data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Erreur suppression:', error);
                            Swal.fire('Erreur!', 'Une erreur est survenue lors de la suppression.', 'error');
                        });
                }
            });
        });
            // --- Fonctions utilitaires rapides ---
            function getActionBadgeClass(action) {
            const classes = {
                'depot': 'badge-success',
                'recuperation': 'badge-warning',
                'livraison': 'badge-info'
            };
            return classes[action] || 'badge-secondary';
        }
        function getActionText(action) {
            const texts = {
                'depot': 'DÉPÔT',
                'recuperation': 'RÉCUPÉRATION',
                'livraison': 'LIVRAISON'
            };
            return texts[action] || action;
        }
        // Fonction de filtrage des données
        function filterProgrammes() {
            const searchTerm = $('#search').val().toLowerCase();
            const actionFilter = $('#actionFilter').val();
            const etatFilter = $('#etatFilter').val(); // Nouveau filtre
            
            let filtered = programmesData.programmes.filter(p => {
                if (!p) return false;
                
                // Filtre par recherche texte
                const matchesSearch = !searchTerm || 
                    Object.values(p).some(val => 
                        val && val.toString().toLowerCase().includes(searchTerm)
                    );
                
                // Filtre par action
                const matchesAction = !actionFilter || 
                    p.actions_a_faire === actionFilter;
                
                // Filtre par état
                const matchesEtat = !etatFilter || 
                    p.etat_rdv === etatFilter;
                
                return matchesSearch && matchesAction && matchesEtat;
            });
            
            return filtered;
        }
        function getEtatBadgeClass(etat) {
            const classes = {
                'effectué': 'badge-success',
                'en attente': 'badge-warning',
                'à planifié': 'badge-info',
                'programmé': 'badge-primary'
            };
            return classes[etat] || 'badge-secondary';
        }
        });
        </script>

        <style>
            /* Style pour le filtre d'action */
        #actionFilter {
            min-width: 180px;
        }

        /* Style pour les badges d'action */
        .badge.badge-success { background-color: #28a745; }
        .badge.badge-warning { background-color: #ffc107; color: #212529; }
        .badge.badge-info { background-color: #17a2b8; }

        /* Responsive pour les filtres */
        @media (max-width: 768px) {
            .form-inline {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .form-inline .form-control {
                margin-bottom: 10px;
                width: 100% !important;
            }
            
            #actionFilter {
                min-width: 100%;
            }
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
            /* EMPÊCHER LE GRISAGE AUTOMATIQUE DES CHAMPS */
        .editable-field:not([readonly]) {
            background-color: white !important;
            color: #495057 !important;
        }

        .editable-field[readonly] {
            background-color: #f8f9fa !important;
            border-color: #ced4da !important;
        }

        /* Style pour les champs en mode édition */
        .editable-field.border-primary {
            background-color: white !important;
            border-color: #007bff !important;
        }
        .dimensions-section {
            transition: all 0.3s ease;
        }

        .dimensions-section.d-none {
            display: none !important;
        }

        /* Indicateur visuel pour le mode transit */
        .mode-transit-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        /* Style spécifique pour les articles maritimes */
        .item-details.maritime-item {
            border-left: 4px solid #17a2b8 !important;
            background-color: #f8f9fa;
        }

        .item-details.aerien-item {
            border-left: 4px solid #ffc107 !important;
            background-color: #fffaf0;
        }

        /* Style pour les champs dimensions quand visibles */
        .dimensions-section .form-group {
            margin-bottom: 0.5rem;
        }

        .dimensions-section .form-control {
            background-color: #f8f9fa;
        }
        /* Styles pour les programmes multiples */
        .programme-item {
            background-color: #f8f9fa;
            border-left: 4px solid #28a745 !important;
            transition: all 0.3s ease;
        }

        .programme-item.border-danger {
            border-left: 4px solid #dc3545 !important;
            background-color: #fff5f5;
        }

        .programme-item h6 {
            font-size: 1rem;
            font-weight: 600;
        }

        .remove-programme-btn {
            transition: all 0.3s ease;
        }
        .toggle-edit-mode {
            display: block !important;
            visibility: visible !important;
        }
        .remove-programme-btn:hover {
            transform: scale(1.1);
        }
        /* Styles pour les différents modes transit */
        .maritime-item {
            border-left: 4px solid #17a2b8 !important;
            background-color: #f8f9fa;
        }

        .aerien-item {
            border-left: 4px solid #ffc107 !important;
            background-color: #fffaf0;
        }

        .mode-transit-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        /* Style pour les sections conditionnelles */
        .dimensions-section .form-group {
            margin-bottom: 0.5rem;
        }

        .dimensions-section .form-control {
            background-color: #f8f9fa;
        }

        /* Indication visuelle pour les champs masqués */
        .d-none {
            display: none !important;
        }
        /* Animation pour l'ajout de nouveaux programmes */
        .programme-item:last-child {
            animation: slideIn 0.5s ease;
        }
        /* Styles pour les programmes de dépôt multiples */
        .depot-programme-item {
            background-color: #f8f9fa;
            border-left: 4px solid #28a745 !important;
            transition: all 0.3s ease;
        }

        .depot-programme-item h6 {
            font-size: 1rem;
            font-weight: 600;
        }

        .remove-depot-programme-btn {
            transition: all 0.3s ease;
        }

        .remove-depot-programme-btn:hover {
            transform: scale(1.1);
        }
        /* Styles pour les selects en mode édition */
        .mode-transit-select:not([readonly]),
        .type-colis-select:not([readonly]) {
            background-color: white !important;
            border-color: #007bff !important;
        }

        .mode-transit-select[readonly],
        .type-colis-select[readonly] {
            background-color: #f8f9fa !important;
            border-color: #ced4da !important;
        }

        /* Indicateur visuel pour le mode transit */
        .mode-transit-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        /* Style pour les champs dimensions */
        .dimensions-section .form-group {
            margin-bottom: 0.5rem;
        }

        .dimensions-section .form-control {
            background-color: #f8f9fa;
        }
        /* Garantir l'affichage du bouton d'ajout */
        #add-item-section-0,
        #add-item-section-1,
        #add-item-section-2,
        #add-item-section-3,
        #add-item-section-4 {
            display: block !important;
        }

        /* Style pour le bouton Modifier/Visualiser */
        .toggle-edit-mode.btn-warning {
            background-color: #ffc107 !important;
            border-color: #ffc107 !important;
            color: #212529 !important;
        }

        .toggle-edit-mode.btn-light {
            background-color: #f8f9fa !important;
            border-color: #f8f9fa !important;
            color: #212529 !important;
        }
        .devis-items-section .toggle-edit-mode {
            z-index: 10;
            position: relative;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
=======
        $('#actionFilter, #etatFilter, #chauffeurFilter').on('change', function() {
            currentPage = 1;
            updateTable();
        });

        $('.page-size-btn').on('click', function(e) {
            e.preventDefault();
            itemsPerPage = parseInt($(this).data('size'));
            $('#pageSizeDisplay').text(itemsPerPage);
            currentPage = 1;
            updateTable();
        });

        $('#pagination').on('click', 'a.page-link', function(e) {
            e.preventDefault();
            currentPage = parseInt($(this).data('page'));
            updateTable();
            scrollToTop();
        });

        $('#refreshBtn').on('click', function() {
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Actualisation...');
            loadData().finally(() => {
                setTimeout(() => {
                    $(this).prop('disabled', false).html('<i class="fas fa-sync-alt mr-1"></i>Actualiser');
                }, 1000);
            });
        });

        // Gestion de la suppression
        $(document).on('click', '.delete-programme-btn', function() {
        const programmeId = $(this).data('id');
        const programmeRow = $(this).closest('tr'); // Cible la ligne du tableau

        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: "Cette action est irréversible et supprimera définitivement le programme.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                // Construction de l'URL de suppression
                const url = "{{ route('aftlb_transport.programme.destroy', ['programme' => 'PROGRAMME_ID']) }}".replace('PROGRAMME_ID', programmeId);

                axios.delete(url)
                    .then(response => {
                        Swal.fire(
                            'Supprimé !',
                            response.data.message,
                            'success'
                        );
                        // Recharger les données pour mettre à jour le tableau et les statistiques
                        loadData(); 
                    })
                    .catch(error => {
                        Swal.fire(
                            'Erreur !',
                            error.response?.data?.message || 'Une erreur est survenue lors de la suppression.',
                            'error'
                        );
                    });
            }
        });
    });
    }
 
    function loadData() {
        showLoading(true);
        return axios.get("{{ route('aftlb_transport.programme.data') }}")
            .then(function(response) {
                programmesData = response.data.programmes || [];
                updateStatistics();
                updateChauffeurFilter();
                updateTable();
            })
            .catch(function(error) {
                console.error('Erreur de chargement des données:', error);
                showError('Impossible de charger les données du serveur.');
            })
            .finally(() => {
                showLoading(false);
            });
    }

    function updateStatistics() {
        const total = programmesData.length;
        const attente = programmesData.filter(p => p.etat_rdv === 'en attente').length;
        const effectue = programmesData.filter(p => p.etat_rdv === 'effectué').length;
        const planifie = programmesData.filter(p => p.etat_rdv === 'à planifié').length;

        $('#totalProgrammes').text(total);
        $('#programmesAttente').text(attente);
        $('#programmesEffectue').text(effectue);
        $('#programmesPlanifie').text(planifie);
    }

    function updateChauffeurFilter() {
        const chauffeurs = [...new Set(programmesData
            .filter(p => p.user)
            .map(p => `${p.user.first_name || ''} ${p.user.last_name || ''}`.trim())
            .filter(name => name !== '')
        )];

        const select = $('#chauffeurFilter');
        select.empty().append('<option value="">Tous chauffeurs</option>');
        
        chauffeurs.forEach(chauffeur => {
            select.append(`<option value="${chauffeur}">${chauffeur}</option>`);
        });
        select.trigger('change');
    }

    function filterProgrammes() {
        const searchTerm = $('#search').val().toLowerCase();
        const actionFilter = $('#actionFilter').val();
        const etatFilter = $('#etatFilter').val();
        const chauffeurFilter = $('#chauffeurFilter').val();
        
        return programmesData.filter(p => {
            if (!p) return false;
            
            // Filtre recherche
            const matchesSearch = !searchTerm || 
                (p.reference_a_afficher && p.reference_a_afficher.toLowerCase().includes(searchTerm)) ||
                (p.nom_expediteur && p.nom_expediteur.toLowerCase().includes(searchTerm)) ||
                (p.tel_expediteur && p.tel_expediteur.toLowerCase().includes(searchTerm)) ||
                (p.lieu_expedition && p.lieu_expedition.toLowerCase().includes(searchTerm));

            // Filtres sélect
            const matchesAction = !actionFilter || p.actions_a_faire === actionFilter;
            const matchesEtat = !etatFilter || p.etat_rdv === etatFilter;
            
            // Filtre chauffeur
            const chauffeurName = p.user ? `${p.user.first_name || ''} ${p.user.last_name || ''}`.trim() : '';
            const matchesChauffeur = !chauffeurFilter || chauffeurName === chauffeurFilter;
            
            return matchesSearch && matchesAction && matchesEtat && matchesChauffeur;
        });
    }
    
    function updateTable() {
        const filtered = filterProgrammes();
        totalItems = filtered.length;
        const paginated = filtered.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);
        
        generateTable(paginated);
        generatePagination(totalItems);
        updatePaginationInfo(filtered.length);
    }

    function generateTable(programmes) {
        const tableBody = $('#programmes-table');
        tableBody.empty();
        
        if (!programmes || programmes.length === 0) {
            tableBody.append(`
                <tr>
                    <td colspan="10" class="text-center py-4">
                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                        <p class="text-muted">Aucun programme trouvé</p>
                    </td>
                </tr>
            `);
            return;
        }
        
        programmes.forEach(p => {
            const user = p.user ? `${p.user.first_name || ''} ${p.user.last_name || ''}`.trim() : '<span class="text-muted">N/A</span>';
            const reference = p.reference_a_afficher || 'N/A';
            const etat = p.etat_rdv || 'N/A';
            
            const actionsHtml = etat !== 'effectué' ? 
               `<div class="btn-group btn-group-sm">
                    <a href="{{ route('aftlb_transport.programme.edit.page', '') }}/${p.id}-aft-louis-b" class="btn btn-outline-info" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-outline-danger delete-programme-btn" data-id="${p.id}" title="Supprimer">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>` : 
                `<button class="btn btn-sm btn-outline-success" disabled title="Programme terminé">
                    <i class="fas fa-check-circle"></i>
                </button>`;

            tableBody.append(`
                <tr>
                    <td class="text-center font-weight-bold">${p.quantite || 1}</td>
                    <td class="text-center">${getActionBadge(p.actions_a_faire)}</td>
                    <td>
                        <span class="font-weight-bold text-dark">${reference}</span>
                    </td>
                    <td>
                        <span class="badge badge-light border text-dark">${p.nature_du_colis || 'N/A'}</span>
                    </td>
                    <td>
                        <div class="client-info">
                            <div class="font-weight-bold text-dark">${p.nom_expediteur || 'N/A'}</div>
                        </div>
                    </td>
                    <td>
                        <a href="tel:${p.tel_expediteur || ''}" class="text-dark">
                            <i class="fas fa-phone mr-1 text-muted"></i>${p.tel_expediteur || 'N/A'}
                        </a>
                    </td>
                    <td class="text-truncate" style="max-width: 200px;" title="${p.lieu_expedition || ''}">
                        <i class="fas fa-map-marker-alt text-muted mr-1"></i>
                        ${p.lieu_expedition || 'N/A'}
                    </td>
                    <td>
                        <span class="font-weight-bold text-dark">${user}</span>
                    </td>
                    <td class="text-center">${getEtatBadge(etat)}</td>
                    <td class="text-center">${actionsHtml}</td>
                </tr>
            `);
        });
    }

    function generatePagination(totalItems) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const pagination = $('#pagination');
        pagination.empty();
        
        if (totalPages <= 1) return;

        // Previous button
        pagination.append(`
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link text-dark" href="#" data-page="${currentPage - 1}">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `);

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                pagination.append(`
                    <li class="page-item ${currentPage === i ? 'active' : ''}">
                        <a class="page-link ${currentPage === i ? 'bg-primary border-primary' : 'text-dark'}" href="#" data-page="${i}">${i}</a>
                    </li>
                `);
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                pagination.append('<li class="page-item disabled"><a class="page-link text-muted" href="#">...</a></li>');
            }
        }

        // Next button
        pagination.append(`
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link text-dark" href="#" data-page="${currentPage + 1}">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `);
    }

    function updatePaginationInfo(totalFiltered) {
        const start = totalFiltered === 0 ? 0 : (currentPage - 1) * itemsPerPage + 1;
        const end = Math.min(currentPage * itemsPerPage, totalFiltered);
        
        $('#paginationInfo').html(`
            Affichage de <strong>${start}</strong> à <strong>${end}</strong> sur <strong>${totalFiltered}</strong> élément(s)
        `);
    }

    // Fonctions utilitaires
    function getActionBadge(action) {
        const config = {
            'depot': { class: 'bg-success text-white', text: 'DÉPÔT', icon: 'fa-upload' },
            'recuperation': { class: 'bg-warning text-dark', text: 'RÉCUPÉRATION', icon: 'fa-download' },
            'livraison': { class: 'bg-info text-white', text: 'LIVRAISON', icon: 'fa-truck' }
        };
        
        const cfg = config[action] || { class: 'bg-secondary text-white', text: action, icon: 'fa-question' };
        return `<span class="badge ${cfg.class} font-weight-normal"><i class="fas ${cfg.icon} mr-1"></i>${cfg.text}</span>`;
    }

    function getEtatBadge(etat) {
        const config = {
            'effectué': { class: 'bg-success text-white', icon: 'fa-check-circle' },
            'en attente': { class: 'bg-warning text-dark', icon: 'fa-clock' },
            'à planifié': { class: 'bg-info text-white', icon: 'fa-calendar-plus' },
            'programmé': { class: 'bg-primary text-white', icon: 'fa-calendar-check' }
        };
        
        const cfg = config[etat] || { class: 'bg-secondary text-white', icon: 'fa-question' };
        return `<span class="badge ${cfg.class} font-weight-normal"><i class="fas ${cfg.icon} mr-1"></i>${etat}</span>`;
    }

    function showLoading(show) {
        $('#loadingIndicator').toggle(show);
        $('#programmes-table').toggle(!show);
    }

   
    function showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: message,
            confirmButtonText: 'OK'
        });
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function scrollToTop() {
        $('html, body').animate({ scrollTop: 0 }, 300);
    }

    // Initialisation
    initApp();
});
</script>

<style>
.small-box {
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    position: relative;
    display: block;
    margin-bottom: 20px;
}
.small-box>.inner { padding: 10px; }
.small-box h3 { font-size: 2.2rem; font-weight: bold; margin: 0 0 10px 0; }
.small-box p { font-size: 1rem; }
.small-box .icon { position: absolute; top: 15px; right: 15px; z-index: 0; font-size: 70px; color: rgba(0,0,0,0.15); }

.card { border: 1px solid #dee2e6; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }

.badge { font-size: 0.75rem; padding: 0.4em 0.6em; font-weight: 500; border: none; }

.table th { 
    border-top: 1px solid #dee2e6; 
    border-bottom: 2px solid #dee2e6; 
    font-weight: 600; 
    background-color: #f8f9fa;
    color: #495057;
}
.table td { 
    vertical-align: middle; 
    border-color: #dee2e6;
    color: #495057;
}
.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.03);
}

.pagination { margin-bottom: 0; }
.page-link { color: #495057; border-color: #dee2e6; }
.page-link:hover { color: #495057; background-color: #e9ecef; border-color: #dee2e6; }
.page-item.active .page-link { background-color: #007bff; border-color: #007bff; }

.client-info { max-width: 200px; }

.select2-container .select2-selection--single { 
    height: 38px; 
    border: 1px solid #ced4da;
}
.select2-container--default .select2-selection--single .select2-selection__rendered { 
    line-height: 36px; 
    color: #495057;
}
.select2-container--default .select2-selection--single .select2-selection__arrow { 
    height: 36px; 
}

#loadingIndicator { display: none; }

.btn { border-radius: 0.375rem; }
.btn-outline-info { color: #17a2b8; border-color: #17a2b8; }
.btn-outline-info:hover { background-color: #17a2b8; border-color: #17a2b8; }
.btn-outline-danger { color: #dc3545; border-color: #dc3545; }
.btn-outline-danger:hover { background-color: #dc3545; border-color: #dc3545; }
.btn-outline-success { color: #28a745; border-color: #28a745; }
.btn-outline-success:hover { background-color: #28a745; border-color: #28a745; }

.bg-warning { background-color: #ffc107 !important; }
.bg-warning.text-dark { color: #212529 !important; }
>>>>>>> 1aedc3d299d0a565bfd7b5a51b18f7bff19131ee
</style>
@endsection