@extends('AFT_LOUIS_BLERIOT.layouts.agent')
@section('content')
@php
    // =================================================================
    // PRÉPARATION DES DONNÉES
    // =================================================================
    // On récupère le premier groupe de colis pour accéder aux informations partagées.
    $firstColisGroup = $colis_recap_list->first();

    // On détermine la devise de manière sécurisée. Valeur par défaut : '€'.
    $devise = $firstColisGroup->devise ?? '€';
    
    // On récupère l'ID de référence global pour l'ensemble de la transaction.
    $id_reference = $firstColisGroup->id_reference ?? '';

    // Informations partagées pour le service additionnel
    $service = $firstColisGroup->service ?? null;
    $montant_service = $firstColisGroup->montant_service ?? null;
    $reference = $firstColisGroup->reference_colis ?? null;

    // On vérifie si un service additionnel existe déjà pour l'afficher au chargement.
    $has_service = !empty($service) && isset($montant_service);
@endphp

<section class="p-4 mx-auto">
    {{-- On vérifie s'il y a des colis avant d'afficher le formulaire --}}
    @if(!$colis_recap_list->isEmpty())
        <div class="all-forms-container">
            {{-- ================================================================= --}}
            {{-- FORMULAIRE GLOBAL                                               --}}
            {{-- ================================================================= --}}
            <form id="update-all-form" action="{{ route('aftlb_colis.valide.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                {{-- Données cachées globales pour le formulaire --}}
                <input type="hidden" id="currency-symbol" value="{{ $devise }}">
                <input type="hidden" name="id_reference" value="{{ $id_reference }}">
                <input type="hidden" name="reference_colis" value="{{ $info_partagees->reference_colis ?? '' }}">

                {{-- ================================================================= --}}
                {{-- SECTION EXPÉDITEUR / DESTINATAIRE                               --}}
                {{-- ================================================================= --}}
                <div class="form-container">
                    <div class="form-section">
                        <h4><i class="fas fa-user-friends"></i> Informations Expéditeur & Destinataire</h4><hr>
                        {{-- Section Expéditeur avec autocomplétion --}}
                        <div class="sub-section">
                            <h5>Expéditeur</h5>
                            <div class="row mb-3">
                                <div class="col-md-6 position-relative">
                                    <label for="client_select" class="form-label">Rechercher un client existant</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" id="client_select" class="form-control" placeholder="Rechercher par nom, prénom, téléphone...">
                                    </div>
                                    <div id="client_autocomplete_results" class="autocomplete-results"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3"><label>Nom</label><input type="text" name="nom_expediteur" value="{{ $info_partagees->expediteur->nom ?? '' }}" class="form-control" required></div>
                                <div class="col-md-3"><label>Prénom</label><input type="text" name="prenom_expediteur" value="{{ $info_partagees->expediteur->prenom ?? '' }}" class="form-control" required></div>
                                <div class="col-md-3"><label>Contact</label><input type="text" name="tel_expediteur" value="{{ $info_partagees->expediteur->tel ?? '' }}" class="form-control" required></div>
                                <div class="col-md-3"><label>Agence d'expédition</label><input type="text" name="agence_expediteur" value="{{ $info_partagees->agence ?? '' }}" class="form-control" required></div>
                                <div class="col-md-3"><label>Reference</label><input type="text" name="" value="{{ $reference ?? '' }}" class="form-control" disabled></div>
                            </div>
                        </div>

                        {{-- Section Destinataire --}}
                        <div class="sub-section mt-4">
                            <h5>Destinataire</h5>
                            <div class="row">
                                <div class="col-md-3"><label>Nom</label><input type="text" name="nom_destinataire" value="{{ $info_partagees->destinataire->nom ?? '' }}" class="form-control" required></div>
                                <div class="col-md-3"><label>Prénom</label><input type="text" name="prenom_destinataire" value="{{ $info_partagees->destinataire->prenom ?? '' }}" class="form-control" required></div>
                                <div class="col-md-3"><label>Contact</label><input type="text" name="tel_destinataire" value="{{ $info_partagees->destinataire->tel ?? '' }}" class="form-control" required></div>
                                <div class="col-md-3"><label>Agence de destination</label><input type="text" name="agence_destinataire" value="{{ $info_partagees->destinataire->agence ?? '' }}" class="form-control" required></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================================================================= --}}
                {{-- SECTION DES COLIS                                               --}}
                {{-- ================================================================= --}}
                <div class="form-container">
                    <h4><i class="fas fa-box-open"></i> Colis</h4><hr>
                    <div id="colis-groups-container">
                        {{-- Boucle sur les groupes de colis existants --}}
                        @foreach ($colis_recap_list as $index => $colis_group)
                            <div class="form-section position-relative" data-group-id="{{ $index }}">
                                <h5 class="mt-2 text-primary">Groupe de colis #{{ $loop->iteration }}</h5>
                                
                                {{-- Champs cachés pour lier les ID de colis et le prix de base --}}
                                @foreach ($colis_group->items as $colisItem)
                                    <input type="hidden" name="groupes[{{ $index }}][colis_ids][]" value="{{ $colisItem->id }}">
                                @endforeach
                                <input type="hidden" class="base-price" value="{{ $colis_group->base_price ?? 0 }}">

                                <div class="row mb-3">
                                    <div class="col-md-6 position-relative">
                                        <label class="form-label">Nature du Colis (Produit)</label>
                                        <input type="text" name="groupes[{{ $index }}][produit]" value="{{ $colis_group->produit }}" class="form-control produit-autocomplete">
                                        <div class="autocomplete-results"></div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Quantité</label>
                                        <input type="number" name="groupes[{{ $index }}][quantite_colis]" value="{{ $colis_group->quantite_colis }}" class="form-control" min="1">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Mode de transit</label>
                                        <select name="groupes[{{ $index }}][mode_transit]" class="form-select">
                                            <option value="maritime" @if($colis_group->mode_transit == 'maritime') selected @endif>Maritime</option>
                                            <option value="aerien" @if($colis_group->mode_transit == 'aerien') selected @endif>Aérien</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">Poids total (Kg)</label>
                                        <input type="number" step="any" name="groupes[{{ $index }}][poids_colis]" value="{{ $colis_group->poids_colis ?? '' }}" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Dimensions</label>
                                        <input type="text" name="groupes[{{ $index }}][dimension_result]" value="{{ $colis_group->dimension_result ?? '' }}" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Prix du Groupe</label>
                                        <div class="input-group">
                                             <input type="number" step="0.01" name="groupes[{{ $index }}][prix_transit_colis]" value="{{ $colis_group->prix_transit_colis ?? '' }}" class="form-control colis-price" required>
                                             <span class="input-group-text currency-symbol">{{ $devise }}</span>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-danger remove-existing-colis position-absolute top-0 end-0 m-2"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        @endforeach
                    </div>
                    {{-- Bouton pour ajouter dynamiquement un nouveau groupe de colis --}} 
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-outline-success" id="add-colis-group"><i class="fas fa-plus"></i> Ajouter un groupe de colis</button>
                    </div>
                </div>

                {{-- ================================================================= --}}
                {{-- SECTION SERVICE ADDITIONNEL                                     --}}
                {{-- ================================================================= --}}
                <div class="form-container">
                     <h4><i class="fas fa-concierge-bell"></i> Service Additionnel</h4><hr>
                     <div id="service-container">
                        {{-- Si un service existe au chargement, on l'affiche directement --}}
                        @if($has_service)
                            <div class="form-section" id="service-section">
                                <div class="row align-items-end">
                                    <div class="col-md-6 position-relative">
                                        <label class="form-label">Description du service</label>
                                        <input type="text" name="service" class="form-control service-autocomplete" placeholder="Rechercher ou saisir un service..." value="{{ $service }}" required>
                                        <div class="autocomplete-results"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Prix du service</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" name="montant_service" id="service-price" class="form-control" value="{{ $montant_service ?? '0.00' }}" required>
                                            <span class="input-group-text currency-symbol">{{ $devise }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <button type="button" class="btn btn-danger" id="remove-service"><i class="fas fa-trash-alt"></i> Retirer</button>
                                    </div>
                                </div>
                            </div>
                        @endif
                     </div>
                     {{-- Le bouton pour ajouter un service n'est visible que si aucun service n'est défini --}}
                     <div class="text-center mt-3" id="add-service-button-container" @if($has_service) style="display: none;" @endif>
                        <button type="button" class="btn btn-outline-primary" id="add-service"><i class="fas fa-plus"></i> Ajouter un service</button>
                    </div>
                </div>

                {{-- ================================================================= --}}
                {{-- SECTION DES TOTAUX                                              --}}
                {{-- ================================================================= --}}
                <div class="form-container total-section">
                    <div class="row justify-content-end text-end">
                        <div class="col-md-5">
                            <h4 class="mb-3"><i class="fas fa-file-invoice-dollar"></i> Récapitulatif</h4>
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Total Colis
                                    <strong><span id="total-colis">0.00</span> <span class="currency-symbol">{{ $devise }}</span></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Total Service
                                    <strong><span id="total-service">0.00</span> <span class="currency-symbol">{{ $devise }}</span></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center fs-4">
                                    <strong>MONTANT TOTAL</strong>
                                    <strong class="text-success"><span id="grand-total">0.00</span> <span class="currency-symbol">{{ $devise }}</span></strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>

            {{-- ================================================================= --}}
            {{-- BOUTONS D'ACTION GLOBAUX                                        --}}
            {{-- ================================================================= --}}
            <div class="d-flex justify-content-center gap-3 mt-4">
                <a href="javascript:history.back()" class="btn btn-secondary btn-lg"><i class="fas fa-arrow-left"></i> Retour</a>
                <button type="button" class="btn btn-primary btn-lg" id="validate-all-btn"><i class="fas fa-check"></i> Valider les modifications</button>
            </div>
        </div>
    @else
        {{-- Message affiché si aucun colis n'est trouvé --}}
        <div class="alert alert-warning text-center">
            <h3>Aucun colis à modifier.</h3>
            <p>Il se peut que les informations soient incorrectes ou que le colis ait été supprimé.</p>
            <a href="javascript:history.back()" class="btn btn-secondary mt-2"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
    @endif
</section>

{{-- ================================================================= --}}
{{-- DÉPENDANCES & SCRIPTS                                           --}}
{{-- ================================================================= --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<script>
    $(document).ready(function() {
        // Index pour les nouveaux groupes, initialisé après les groupes existants.
        let newGroupIndex = {{ $colis_recap_list->count() }};
        const deviseSymbol = $('#currency-symbol').val();
        
        // Tableau pour stocker les IDs des colis à supprimer.
        let idsToDelete = [];

        // ===============================================
        // FONCTION GLOBALE DE CALCUL DES TOTAUX
        // ===============================================
        function calculateTotals() {
            let totalColis = 0;
            $('input[name*="[prix_transit_colis]"]').each(function() {
                totalColis += parseFloat($(this).val()) || 0;
            });
            $('#total-colis').text(totalColis.toFixed(2));

            let totalService = 0;
            if ($('#service-price').length && $('#service-section').is(':visible')) {
                totalService = parseFloat($('#service-price').val()) || 0;
            }
            $('#total-service').text(totalService.toFixed(2));

            let grandTotal = totalColis + totalService;
            $('#grand-total').text(grandTotal.toFixed(2));
        }

        $('body').on('input', 'input.colis-price, #service-price', calculateTotals);

        // ========================================================
        // FONCTION DE CALCUL DE PRIX DYNAMIQUE POUR TOUS LES COLIS
        // ========================================================
        function updateDynamicPrice(groupContainer) {
            const mode = groupContainer.find('select[name*="[mode_transit]"]').val();
            const basePrice = parseFloat(groupContainer.find('.base-price').val()) || 0;
            const quantity = parseInt(groupContainer.find('input[name*="[quantite_colis]"]').val()) || 1;
            const weight = parseFloat(groupContainer.find('input[name*="[poids_colis]"]').val()) || 0;
            let finalPrice = 0;

            if (mode === 'maritime') {
                finalPrice = basePrice * quantity;
            } else if (mode === 'aerien') {
                finalPrice = basePrice * weight;
            }
            groupContainer.find('.colis-price').val(finalPrice.toFixed(2)).trigger('input');
        }
        
        $('body').on('change input', '.form-section select[name*="[mode_transit]"], .form-section input[name*="[quantite_colis]"], .form-section input[name*="[poids_colis]"]', function() {
            let groupContainer = $(this).closest('.form-section');
            updateDynamicPrice(groupContainer);
        });

        // ===============================================
        // LOGIQUE D'AUTOCOMPLÉTION
        // ===============================================
        function initAutocomplete(element, url, categorie, onSelectCallback) {
            element.on('keyup', function() {
                const query = $(this).val();
                const resultsContainer = $(this).siblings('.autocomplete-results');
                if (query.length < 2) { resultsContainer.hide(); return; }
                
                $.ajax({
                    url: url, data: { query: query, categorie: categorie },
                    success: function(data) {
                        resultsContainer.html('');
                        if (data.length > 0) {
                            data.forEach(function(item) {
                                const resultItem = $(`<div class="autocomplete-item">${item.description}</div>`);
                                resultItem.on('click', function() {
                                    if (onSelectCallback) onSelectCallback(element, item);
                                    resultsContainer.hide();
                                });
                                resultsContainer.append(resultItem);
                            });
                            resultsContainer.show();
                        } else { resultsContainer.hide(); }
                    }
                });
            });
        }
        
        const productAutocompleteCallback = (el, item) => {
            el.val(item.description);
            let groupContainer = el.closest('.form-section');
            let basePrice = parseFloat(item.prix || 0);
            groupContainer.find('.base-price').val(basePrice);
            updateDynamicPrice(groupContainer);
        };

        function initializeProductAutocomplete(context) {
            $(context).find('.produit-autocomplete').each(function() {
                initAutocomplete($(this), "{{ route('aftlb_colis.recherche.auto') }}", 'Colis', productAutocompleteCallback);
            });
        }
        initializeProductAutocomplete(document);

        // ===============================================
        // LOGIQUE D'AJOUT / SUPPRESSION DE COLIS
        // ===============================================
        $('#add-colis-group').on('click', function() {
            const newColisHtml = `
                <div class="form-section new-group position-relative" data-new-group-id="${newGroupIndex}">
                    <h5 class="mt-2 text-success">Nouveau groupe de colis</h5>
                    <div class="row mb-3">
                        <div class="col-md-6 position-relative">
                            <label class="form-label">Nature du Colis</label>
                            <input type="text" name="new_group[${newGroupIndex}][produit]" class="form-control produit-autocomplete" placeholder="Rechercher un produit..." required>
                            <input type="hidden" class="base-price" name="new_group[${newGroupIndex}][base_price]" value="0">
                            <div class="autocomplete-results"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Quantité</label>
                            <input type="number" name="new_group[${newGroupIndex}][quantite_colis]" class="form-control" required min="1" value="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Mode de transit</label>
                            <select name="new_group[${newGroupIndex}][mode_transit]" class="form-select" required>
                                <option value="maritime" selected>Maritime</option>
                                <option value="aerien">Aérien</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><label class="form-label">Poids total (Kg)</label><input type="number" step="any" name="new_group[${newGroupIndex}][poids_colis]" class="form-control" placeholder="Poids total" min="0" value="0"></div>
                        <div class="col-md-3"><label class="form-label">Dimensions</label><input type="text" name="new_group[${newGroupIndex}][dimension]" class="form-control" placeholder="Ex: 20x30x40"></div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Prix du Groupe</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="new_group[${newGroupIndex}][prix_transit_colis]" class="form-control colis-price" required>
                                <span class="input-group-text">${deviseSymbol}</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-warning remove-new-colis position-absolute top-0 end-0 m-2"><i class="fas fa-times"></i></button>
                </div>`;
            const newElement = $(newColisHtml);
            $('#colis-groups-container').append(newElement);
            initializeProductAutocomplete(newElement);
            newGroupIndex++;
        });

        $('body').on('click', '.remove-new-colis', function() {
            $(this).closest('.form-section.new-group').remove();
            calculateTotals();
        });
        
        $('body').on('click', '.remove-existing-colis', function() {
            Swal.fire({
                title: 'Supprimer ce groupe ?', text: "Cette action est irréversible.", icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: "Oui, supprimer !", cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    let container = $(this).closest('.form-section');
                    container.find('input[name*="[colis_ids]"]').each(function() {
                        const colisId = $(this).val();
                        if (!idsToDelete.includes(colisId)) {
                            idsToDelete.push(colisId);
                        }
                    });
                    container.fadeOut(300, function() { 
                        $(this).remove(); 
                        calculateTotals();
                    });
                }
            });
        });

        $('body').on('change', 'input[name*="[quantite_colis]"]', function() {
            const $input = $(this);
            const groupContainer = $input.closest('.form-section');
            
            if (groupContainer.hasClass('new-group') || groupContainer.find('input[name*="[colis_ids]"]').length === 0) {
                return;
            }

            const newQuantity = parseInt($input.val(), 10);
            const idInputs = groupContainer.find('input[name*="[colis_ids]"]');
            const currentQuantity = idInputs.length;
            
            if (newQuantity < currentQuantity) {
                const quantityToRemove = currentQuantity - newQuantity;
                const inputsToRemove = idInputs.slice(-quantityToRemove);

                inputsToRemove.each(function() {
                    const colisId = $(this).val();
                    if (!idsToDelete.includes(colisId)) {
                        idsToDelete.push(colisId);
                    }
                    $(this).remove();
                });

            } else if (newQuantity > currentQuantity) {
                const quantityToAdd = newQuantity - currentQuantity;
                $input.val(currentQuantity);
                
                const produit = groupContainer.find('input[name*="[produit]"]').val();
                const modeTransit = groupContainer.find('select[name*="[mode_transit]"]').val();
                const dimension = groupContainer.find('input[name*="[dimension_result]"]').val();
                const poidsUnitaire = (parseFloat(groupContainer.find('input[name*="[poids_colis]"]').val()) || 0) / currentQuantity;
                const prixUnitaire = (parseFloat(groupContainer.find('input[name*="[prix_transit_colis]"]').val()) || 0) / currentQuantity;

                $('#add-colis-group').trigger('click');

                const newGroupContainer = $('.new-group').last();
                newGroupContainer.find('input[name*="[produit]"]').val(produit);
                newGroupContainer.find('select[name*="[mode_transit]"]').val(modeTransit);
                newGroupContainer.find('input[name*="[quantite_colis]"]').val(quantityToAdd);
                newGroupContainer.find('input[name*="[dimension]"]').val(dimension);
                newGroupContainer.find('input[name*="[poids_colis]"]').val((poidsUnitaire * quantityToAdd).toFixed(2));
                newGroupContainer.find('input[name*="[prix_transit_colis]"]').val((prixUnitaire * quantityToAdd).toFixed(2));
                
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'info',
                    title: 'Un nouveau groupe a été créé pour la quantité ajoutée.',
                    showConfirmButton: false,
                    timer: 3500
                });

                calculateTotals();
            }
        });

        // ===============================================
        // LOGIQUE POUR LE SERVICE ADDITIONNEL
        // ===============================================
        const serviceAutocompleteCallback = (el, item) => {
            el.val(item.description);
            $('#service-price').val(parseFloat(item.prix).toFixed(2)).trigger('input');
        };

        function initializeServiceAutocomplete() {
            // Cible l'input à l'intérieur du conteneur pour éviter les conflits
            initAutocomplete($('#service-container .service-autocomplete'), "{{ route('aftlb_colis.recherche.auto.service') }}", 'Service', serviceAutocompleteCallback);
        }
        
        // Initialise pour le service existant au chargement
        initializeServiceAutocomplete();

        $('#add-service').on('click', function() {
            // On crée le HTML pour la nouvelle section de service
            const serviceHtml = `
                <div class="form-section" id="service-section" style="display: none;">
                    <div class="row align-items-end">
                        <div class="col-md-6 position-relative">
                            <label class="form-label">Description du service</label>
                            <input type="text" name="service" class="form-control service-autocomplete" placeholder="Rechercher ou saisir un service..." required>
                            <div class="autocomplete-results"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Prix du service</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="montant_service" id="service-price" class="form-control" value="0.00" required>
                                <span class="input-group-text currency-symbol">${deviseSymbol}</span>
                            </div>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-danger" id="remove-service"><i class="fas fa-trash-alt"></i> Retirer</button>
                        </div>
                    </div>
                </div>`;
            
            // On ajoute le HTML au conteneur et on l'affiche avec un effet
            $('#service-container').html(serviceHtml);
            $('#service-section').fadeIn(300);
            
            // On initialise l'autocomplétion pour les nouveaux champs
            initializeServiceAutocomplete();
            
            // On cache le bouton "Ajouter un service"
            $(this).parent().hide();
            
            // On recalcule les totaux
            calculateTotals();
        });

        $('body').on('click', '#remove-service', function() {
            $('#service-section').fadeOut(300, function() {
                $(this).remove();
                $('#add-service-button-container').show();
                calculateTotals();
            });
        });

        // ===============================================
        // AUTOCOMPLETION CLIENT
        // ===============================================
        const clientAutocompleteCallback = (el, item) => {
            $('input[name="nom_expediteur"]').val(item.nom || '');
            $('input[name="prenom_expediteur"]').val(item.prenom || '');
            $('input[name="tel_expediteur"]').val(item.tel || '');
        };
        initAutocomplete($('#client_select'), "{{ route('aftlb_colis.recherche.auto.service') }}", 'Client', clientAutocompleteCallback);

        $(document).on('click', function (e) {
            if (!$(e.target).closest('.position-relative').length) {
                $('.autocomplete-results').hide();
            }
        });

        // ===============================================
        // VALIDATION FINALE
        // ===============================================
        $('#validate-all-btn').on('click', function() {
            $('input[name="deleted_ids[]"]').remove();
            
            $.each([...new Set(idsToDelete)], function(i, id) {
                $('#update-all-form').append(`<input type="hidden" name="deleted_ids[]" value="${id}">`);
            });

            Swal.fire({
                title: 'Confirmer les modifications',
                text: "Voulez-vous enregistrer toutes les modifications apportées ?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "Oui, valider",
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) { 
                    $('#update-all-form').submit(); 
                }
            });
        });

        // Calcul initial des totaux au chargement de la page.
        calculateTotals();
    });
</script>

{{-- ================================================================= --}}
{{-- STYLES CSS PERSONNALISÉS                                        --}}
{{-- ================================================================= --}}
<style>
    body { background-color: #f4f6f9; }
    .all-forms-container { max-width: 85%; margin: auto; }
    .form-container { background-color: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); margin-bottom: 25px; }
    .form-section { background-color: #fdfdfd; padding: 20px; border: 1px solid #e9ecef; border-radius: 8px; margin-bottom: 15px; }
    .sub-section { padding: 15px; border: 1px solid #f1f1f1; border-radius: 5px; background-color: #fafbfe; }
    h4 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; margin-bottom: 20px; }
    h5 { color: #4a6a8a; }
    hr { margin-top: 0; }
    .form-label { font-weight: 500; color: #495057; }
    .autocomplete-results { position: absolute; top: 100%; left: 0; right: 0; z-index: 1050; background-color: #fff; border: 1px solid #ced4da; border-radius: 0 0 5px 5px; max-height: 250px; overflow-y: auto; box-shadow: 0 5px 10px rgba(0,0,0,0.1); display: none; }
    .autocomplete-item { padding: 10px 15px; cursor: pointer; border-bottom: 1px solid #eee; }
    .autocomplete-item:last-child { border-bottom: none; }
    .autocomplete-item:hover { background-color: #007bff; color: #fff; }
    .total-section { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); }
    .form-section.new-group { border-left: 4px solid #28a745; }
</style>
@endsection