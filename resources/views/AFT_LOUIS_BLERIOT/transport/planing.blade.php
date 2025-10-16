{{-- views/AFT_LOUIS_BLERIOT/transport/planing.blade.php --}}
@extends('AFT_LOUIS_BLERIOT.layouts.agent')

@section('content-header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-primary">
                    <i class="fas fa-calendar-alt mr-2"></i>Programmes de Transport
                </h1>
            </div>
            <div class="col-sm-6 text-right">
                <button class="btn btn-success" id="refreshBtn">
                    <i class="fas fa-sync-alt mr-1"></i>Actualiser
                </button>
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
                                <div class="col-md-3 mb-2">
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
                                <!-- NOUVEAU FILTRE PAR DATE -->
                                <div class="col-md-2 mb-2">
                                    <input type="date" id="dateFilter" class="form-control" placeholder="Filtrer par date">
                                </div>
                                <div class="col-md-1 mb-2">
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
                            <!-- NOUVELLE COLONNE DATE PROGRAMME -->
                            <th class="text-center" style="width: 150px;">
                                <i class="fas fa-calendar-day text-muted"></i> Date Programme
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
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">

<script>
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

        loadData();
        
        // Événements de filtrage
        $('#search').on('input', debounce(function() {
            currentPage = 1;
            updateTable();
        }, 300));

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
        $('#dateFilter').on('change', function() {
    currentPage = 1;
    updateTable();
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
        const dateFilter = $('#dateFilter').val(); // Nouveau filtre date
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
             // NOUVEAU : Filtre par date
        let matchesDate = true;
        if (dateFilter && p.date_programme) {
            const programmeDate = new Date(p.date_programme).toISOString().split('T')[0];
            matchesDate = programmeDate === dateFilter;
        }
        return matchesSearch && matchesAction && matchesEtat && matchesChauffeur && matchesDate;
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
                <td colspan="11" class="text-center py-4">  <!-- Changé de 10 à 11 -->
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
        
        // Gestion de la date du programme
        let dateProgramme = 'Non planifié';
        let dateClass = 'text-muted';
        
        if (p.date_programme) {
            // Formater la date en français
            const dateObj = new Date(p.date_programme);
            dateProgramme = dateObj.toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            dateClass = 'text-dark';
            
            // Ajouter l'heure si disponible
            if (p.date_programme.includes(':')) {
                const heures = dateObj.getHours().toString().padStart(2, '0');
                const minutes = dateObj.getMinutes().toString().padStart(2, '0');
                dateProgramme += `<br><small class="text-muted">${heures}:${minutes}</small>`;
            }
        }
        
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
                <!-- NOUVELLE COLONNE DATE -->
                <td class="text-center ${dateClass}">
                    <small>${dateProgramme}</small>
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
</style>
@endsection