{{-- views/AGENCE_CHINE/transport/recuperation.blade.php --}}
@extends('AGENCE_CHINE.layouts.agent')

@section('content-header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Créer une nouvelle Récupération</h1>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <form id="recuperationForm" action="{{ route('chine_programme.programme.createMultipleRecuperation') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-12">
                <!-- Section pour programmes multiples -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Programmes de récupération</h6>
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
                    <!-- Bouton ajouter déplacé en bas -->
                    <div class="card-footer">
                        <button type="button" class="btn btn-success" id="add-programme-btn">
                            <i class="fas fa-plus"></i> Ajouter un programme
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bouton d'enregistrement -->
        <div class="row">
            <div class="col-12 text-right mb-4">
                <a href="{{ route('chine_programme.planing.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="button" class="btn btn-warning" id="save-recuperation-btn">
                    Enregistrer les Récupérations
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Modal pour choisir chauffeur et date -->
<div class="modal fade" id="driverDateModal" tabindex="-1" role="dialog" aria-labelledby="driverDateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="driverDateModalLabel">Sélection du Chauffeur et de la Date</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="user_id_recup_modal">Chauffeur <span style="color: brown">*</span></label>
                    <select class="form-control" id="user_id_recup_modal" required>
                        <option value="">-- Sélectionner un Chauffeur --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="date_programme_recup_modal">Date du Programme <span style="color: brown">*</span></label>
                    <input type="date" class="form-control" id="date_programme_recup_modal" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-warning" id="confirm-save-btn">Confirmer et Enregistrer</button>
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
    // =========================================================================
    // 1. VARIABLES GLOBALES ET INITIALISATION
    // =========================================================================
    let programmeCount = 1;
    let chauffeurs = [];
    let currentDate = new Date().toISOString().split('T')[0];

    // Initialisation
    loadChauffeurs();

    // =========================================================================
    // 2. FONCTIONS DE GESTION DES CHAUFFEURS
    // =========================================================================
    function loadChauffeurs() {
        axios.get("{{ route('chine_programme.programme.data') }}")
            .then(function(response) {
                chauffeurs = response.data.chauffeurs || [];
                const select = $('#user_id_recup_modal');
                select.empty().append('<option value="">-- Sélectionner un Chauffeur --</option>');
                
                chauffeurs.forEach(function(chauffeur) {
                    const nomComplet = `${chauffeur.first_name || ''} ${chauffeur.last_name || ''}`.trim();
                    select.append(`<option value="${chauffeur.id}">${nomComplet}</option>`);
                });
            })
            .catch(error => console.error('Erreur chargement chauffeurs:', error));
    }

    // =========================================================================
    // 3. FONCTIONS DE GESTION DES PROGRAMMES MULTIPLES
    // =========================================================================
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

    // =========================================================================
    // 4. LOGIQUE DE RECHERCHE DE RÉFÉRENCE (DEVIS/DÉPÔT) - AMÉLIORÉE
    // =========================================================================
    function searchReferenceInfo(reference, typeReference, programmeIndex) {
    if (reference.length < 3 || !typeReference) return;
    
    let url = `{{ route('chine_programme.programme.referenceInfo', ['reference' => 'PLACEHOLDER']) }}`.replace('PLACEHOLDER', reference);
    
    axios.get(url)
        .then(res => {
            const { data } = res;
            const pItem = $(`.programme-item[data-programme-index="${programmeIndex}"]`);
            const itemsSection = pItem.find('.devis-items-section');
            
            if (data.existe) {
                // VÉRIFICATION DE CORRESPONDANCE DU TYPE - GARDER CETTE LOGIQUE IMPORTANTE
                if (typeReference === 'devis' && data.type !== 'devis') {
                    // L'utilisateur a sélectionné "Devis confirmé" mais la référence est un dépôt
                    const infoDiv = pItem.find('.reference-info');
                    infoDiv.removeClass('alert-success alert-info').addClass('alert-danger');
                    pItem.find('.reference-message').html(`
                        <strong>❌ Type de référence incorrect!</strong><br>
                        <small>Vous avez sélectionné "Devis confirmé" mais cette référence correspond à un <strong>${data.type}</strong>.<br>
                        Veuillez sélectionner "<strong>Dépôt effectué</strong>" dans le type de récupération.</small>
                    `);
                    infoDiv.show();
                    
                    // Popup d'avertissement
                    Swal.fire({
                        icon: 'error',
                        title: 'Type de référence incorrect',
                        html: `Vous avez sélectionné <strong>"Devis confirmé"</strong> mais la référence <strong>"${reference}"</strong> correspond à un <strong>${data.type}</strong>.<br><br>
                              Veuillez sélectionner <strong>"Dépôt effectué"</strong> dans le type de récupération.`,
                        confirmButtonText: 'Corriger le type',
                        showCancelButton: true,
                        cancelButtonText: 'Ignorer'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Corriger automatiquement le type
                            pItem.find('.type-reference').val('depot');
                            toggleReferenceInput(programmeIndex);
                            // Relancer la recherche avec le bon type
                            searchReferenceInfo(reference, 'depot', programmeIndex);
                        }
                    });
                    return;
                } else if (typeReference === 'depot' && data.type !== 'depot') {
                    // L'utilisateur a sélectionné "Dépôt effectué" mais la référence est un devis
                    const infoDiv = pItem.find('.reference-info');
                    infoDiv.removeClass('alert-success alert-info').addClass('alert-danger');
                    pItem.find('.reference-message').html(`
                        <strong>❌ Type de référence incorrect!</strong><br>
                        <small>Vous avez sélectionné "Dépôt effectué" mais cette référence correspond à un <strong>${data.type}</strong>.<br>
                        Veuillez sélectionner "<strong>Devis confirmé</strong>" dans le type de récupération.</small>
                    `);
                    infoDiv.show();
                    
                    // Popup d'avertissement
                    Swal.fire({
                        icon: 'error',
                        title: 'Type de référence incorrect',
                        html: `Vous avez sélectionné <strong>"Dépôt effectué"</strong> mais la référence <strong>"${reference}"</strong> correspond à un <strong>${data.type}</strong>.<br><br>
                              Veuillez sélectionner <strong>"Devis confirmé"</strong> dans le type de récupération.`,
                        confirmButtonText: 'Corriger le type',
                        showCancelButton: true,
                        cancelButtonText: 'Ignorer'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Corriger automatiquement le type
                            pItem.find('.type-reference').val('devis');
                            toggleReferenceInput(programmeIndex);
                            // Relancer la recherche avec le bon type
                            searchReferenceInfo(reference, 'devis', programmeIndex);
                        }
                    });
                    return;
                }

                // Si le type correspond, on continue avec le remplissage automatique
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
                    // Stocker les items ET les informations (dont mode_transit)
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
                    // Si pas d'items mais type "depot" valide, afficher la section vide pour ajout manuel
                    if (typeReference === 'depot' && data.valide) {
                        itemsSection.data('devis-items', []);
                        itemsSection.data('devis-info', {
                            mode_transit: data.data.mode_transit || 'aerien',
                            quantite_originale: data.data.quantite || 1
                        });
                        
                        generateEditableItemsHTML([], itemsSection.find('.devis-items-content'), programmeIndex);
                        itemsSection.show();
                        initEditModeForProgramme(programmeIndex);
                    } else {
                        itemsSection.hide();
                    }
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
                    // Afficher le bon message selon la validité du dépôt
                    if (data.valide) {
                        infoDiv.removeClass('alert-danger alert-warning').addClass('alert-success');
                        messageSpan.html(`
                            <strong>✅ Dépôt effectué trouvé!</strong><br>
                            <small>• Quantité: ${quantiteTotale} article(s)<br>
                            • Nature: ${data.data.nature_du_colis || 'N/A'}<br>
                            • Client: ${data.data.nom_expediteur || 'N/A'}</small>
                        `);
                    } else {
                        infoDiv.removeClass('alert-success alert-info').addClass('alert-danger');
                        messageSpan.html(`
                            <strong>❌ Dépôt non effectué!</strong><br>
                            <small>Ce dépôt est encore en attente et ne peut pas être récupéré.<br>
                            Veuillez attendre qu'il soit marqué comme "effectué".</small>
                        `);
                        
                        // Bloquer la soumission pour ce programme
                        pItem.addClass('blocked-depot');
                    }
                }
            } else {
                // Référence non trouvée - considérée comme manuelle
                const infoDiv = pItem.find('.reference-info');
                infoDiv.removeClass('alert-success alert-danger').addClass('alert-info');
                pItem.find('.reference-message').text('ℹ️ Référence non trouvée - Veuillez remplir les informations manuellement');
                
                // Pour les références manuelles ou non trouvées, définir le mode transit par défaut (aérien)
                itemsSection.data('devis-info', {
                    mode_transit: 'aerien',
                    quantite_originale: 1
                });
                
                // Afficher la section des articles pour ajout manuel
                if (typeReference === 'manuel') {
                    itemsSection.data('devis-items', []);
                    generateEditableItemsHTML([], itemsSection.find('.devis-items-content'), programmeIndex);
                    itemsSection.show();
                    initEditModeForProgramme(programmeIndex);
                } else {
                    itemsSection.hide();
                }
            }
            
            pItem.find('.reference-info').show();
        })
        .catch(err => {
            console.error('Erreur recherche référence:', err);
            $(`.programme-item[data-programme-index="${programmeIndex}"] .reference-info`).hide();
            $(`.programme-item[data-programme-index="${programmeIndex}"] .devis-items-section`).hide();
        });
}

    // =========================================================================
    // 5. GESTION DES ARTICLES (PROGRAMME_ITEMS) - AMÉLIORÉE
    // =========================================================================
    function generateEditableItemsHTML(items, container, programmeIndex) {
        let html = '';
        
        // Récupérer le mode transit depuis les données stockées
        const pItem = $(`.programme-item[data-programme-index="${programmeIndex}"]`);
        const itemsSection = pItem.find('.devis-items-section');
        const devisInfo = itemsSection.data('devis-info') || {};
        const modeTransit = devisInfo.mode_transit || 'aerien';
        
        console.log(`🚚 Mode transit détecté pour programme ${programmeIndex}:`, modeTransit);
        
        // Si aucun article, créer un article vide par défaut pour l'ajout manuel
        if (items.length === 0) {
            items = [{
                quantite_colis: 1,
                service: 'Transport standard',
                valeur_colis: 0,
                type_colis: 'standard',
                description_colis: 'Description de l\'article',
                mode_transit: modeTransit,
                poids: modeTransit === 'aerien' ? 1 : 0,
                longueur: modeTransit === 'maritime' ? 10 : 0,
                largeur: modeTransit === 'maritime' ? 10 : 0,
                hauteur: modeTransit === 'maritime' ? 10 : 0,
                is_new: true
            }];
            // Mettre à jour les données stockées
            itemsSection.data('devis-items', items);
        }
        
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
                                    <option value="dangerous" ${(item.type_colis || 'standard') === 'dangerous' ? 'selected' : ''}>Dangereux</option>
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
        
        // Bouton d'ajout d'article
        html += `
            <div class="text-center mt-3" id="add-item-section-${programmeIndex}">
                <button type="button" class="btn btn-sm btn-primary add-new-item-btn" data-programme-index="${programmeIndex}">
                    <i class="fas fa-plus"></i> Ajouter un nouvel article
                </button>
            </div>
        `;
        
        container.html(html);
    }

    function toggleEditMode(programmeIndex, forceOn) {
        if (forceOn === undefined) forceOn = false;
        const pItem = $(`.programme-item[data-programme-index="${programmeIndex}"]`);
        
        // VÉRIFICATION DE SÉCURITÉ
        if (pItem.length === 0) {
            console.error(`❌ Programme item avec index ${programmeIndex} non trouvé`);
            const fallbackItem = $('.programme-item').first();
            if (fallbackItem.length > 0) {
                const fallbackIndex = fallbackItem.data('programme-index');
                console.log(`🔄 Utilisation du fallback index: ${fallbackIndex}`);
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

    // =========================================================================
    // 6. GESTION DU CHAMP RÉFÉRENCE - AMÉLIORÉE
    // =========================================================================
    function toggleReferenceInput(programmeIndex) {
        const pItem = $(`.programme-item[data-programme-index="${programmeIndex}"]`);
        const typeReference = pItem.find('.type-reference').val();
        const referenceInput = pItem.find('.reference-input');
        const itemsSection = pItem.find('.devis-items-section');
        
        if (typeReference === 'manuel') {
            // Mode référence manuelle - désactiver et vider le champ
            referenceInput.prop('readonly', true)
                         .addClass('bg-light')
                         .val('')
                         .attr('placeholder', 'Référence générée automatiquement');
            
            // Afficher la section des articles pour ajout manuel
            itemsSection.data('devis-items', []);
            itemsSection.data('devis-info', {
                mode_transit: 'aerien',
                quantite_originale: 1
            });
            generateEditableItemsHTML([], itemsSection.find('.devis-items-content'), programmeIndex);
            itemsSection.show();
            initEditModeForProgramme(programmeIndex);
            
            // Masquer les infos de référence
            pItem.find('.reference-info').hide();
        } else {
            // Mode devis ou dépôt - activer le champ
            referenceInput.prop('readonly', false)
                         .removeClass('bg-light')
                         .attr('placeholder', 'Entrez la référence du devis, dépôt ou une référence manuelle');
            
            // Pour les types devis et depot, on attend la saisie d'une référence
            itemsSection.hide();
            pItem.find('.reference-info').hide();
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
                // On ne cache pas la section des articles pour les dépôts, car on veut pouvoir les charger
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
                // Pour les types non-manuels, on cache la section des articles si pas de référence valide
                if (typeReference !== 'manuel') {
                    programmeItem.find('.devis-items-section').hide();
                }
            }
        });
    }

    // =========================================================================
    // 7. VALIDATION ET SOUMISSION DU FORMULAIRE
    // =========================================================================
    
    // Valider le formulaire avant d'ouvrir le modal
    function validateFormBeforeSave() {
        const programmeItems = $('.programme-item');
        if (programmeItems.length === 0) {
            Swal.fire('Erreur', 'Veuillez ajouter au moins un programme.', 'error');
            return false;
        }

        let isValid = true;
        const errorMessages = [];
        
        programmeItems.each(function(index) {
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
                    errorMessages.push(`Le programme #${index + 1} a des champs obligatoires non remplis`);
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
        });
        
        if (!isValid) {
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
        }

        return isValid;
    }

    function prepareRecuperationData() {
        // Vérifier s'il y a des programmes bloqués (types incorrects)
        const blockedProgrammes = $('.programme-item.blocked-depot');
        if (blockedProgrammes.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Programmes bloqués',
                html: `Certains programmes ont des références de dépôt non effectués.<br>
                      Veuillez corriger ces références avant de soumettre le formulaire.`,
                confirmButtonText: 'OK'
            });
            return false;
        }
        
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
            user_id: $('#user_id_recup_modal').val(),
            date_programme: $('#date_programme_recup_modal').val(),
            programmes: programmes
        };

        return payload;
    }

    function submitForm() {
        $('#driverDateModal').modal('hide');
        
        const payload = prepareRecuperationData();
        if (!payload) return;

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
                    window.location.href = "{{ route('chine_programme.planing.index') }}";
                });
            })
            .catch(err => {
                Swal.close();
                Swal.fire('Erreur', err.response?.data?.message || 'Erreur inconnue', 'error');
            });
    }

    // =========================================================================
    // 8. ÉVÉNEMENTS PRINCIPAUX
    // =========================================================================
    
    // Ajouter un programme
    $('#add-programme-btn').on('click', addProgramme);
    
    // Supprimer un programme
    $('#programmes-container').on('click', '.remove-programme-btn', function() {
        if ($('.programme-item').length > 1) {
            $(this).closest('.programme-item').remove();
        }
    });
    
    // Gestion du mode édition pour les articles
    $('#programmes-container').on('click', '.toggle-edit-mode', function() {
        const programmeIndex = $(this).closest('.programme-item').data('programme-index');
        toggleEditMode(programmeIndex);
    });
    
    // Initialisation de la gestion des références
    initReferenceHandling();
    
    // Appliquer le comportement initial pour chaque programme existant
    $('.programme-item').each(function() {
        const programmeIndex = $(this).data('programme-index');
        toggleReferenceInput(programmeIndex);
    });
    
    // Empêcher la sélection des dates passées
    function setMinDate() {
        const today = new Date().toISOString().split('T')[0];
        $('#date_programme_recup_modal').attr('min', today);
    }

    // Ouvrir le modal pour choisir chauffeur et date
    $('#save-recuperation-btn').on('click', function() {
        if (validateFormBeforeSave()) {
            setMinDate();
            $('#date_programme_recup_modal').val(currentDate);
            $('#driverDateModal').modal('show');
        }
    });

    // Confirmer l'enregistrement
    $('#confirm-save-btn').on('click', function() {
        const driverId = $('#user_id_recup_modal').val();
        const dateProgramme = $('#date_programme_recup_modal').val();

        if (!driverId || !dateProgramme) {
            Swal.fire('Erreur', 'Veuillez sélectionner un chauffeur et une date.', 'error');
            return;
        }

        // Soumettre le formulaire
        submitForm();
    });

    // Initialisation
    setMinDate();

    console.log("✅ Récupération page initialisée avec succès");
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
    
    .devis-items-section .card-header {
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

    /* Indication visuelle pour les champs masqués */
    .d-none {
        display: none !important;
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
    /* Style pour les programmes bloqués */
.programme-item.blocked-depot {
    border-left: 4px solid #dc3545 !important;
    background-color: #fff5f5;
}

.programme-item.blocked-depot input,
.programme-item.blocked-depot textarea,
.programme-item.blocked-depot select {
    background-color: #ffe6e6;
    border-color: #dc3545;
}
</style>
@endsection