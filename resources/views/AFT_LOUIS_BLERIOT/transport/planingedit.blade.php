@extends('AFT_LOUIS_BLERIOT.layouts.agent')

@section('content-header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">📋 Modifier le Programme</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('aftlb_transport.planing.chauffeur') }}">📊 Programmes</a></li>
                    <li class="breadcrumb-item active">✏️ Modification</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card card-primary card-outline">
        <div class="card-header bg-gradient-primary text-white">
            <h3 class="card-title mb-0">
                <i class="fas fa-edit mr-2"></i>
                MODIFICATION DU PROGRAMME #{{ $programme->id }}
                <span class="badge badge-light ml-2">{{ strtoupper($programme->actions_a_faire) }}</span>
            </h3>
        </div>
        <div class="card-body">
            <!-- Informations générales -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="info-box bg-light">
                        <span class="info-box-icon bg-info"><i class="fas fa-barcode"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Référence</span>
                            <span class="info-box-number">{{ $programme->reference_generee ?? $programme->reference_colis }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box bg-light">
                        <span class="info-box-icon bg-{{ $programme->etat_rdv === 'effectué' ? 'success' : 'warning' }}">
                            <i class="fas fa-{{ $programme->etat_rdv === 'effectué' ? 'check' : 'clock' }}"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">État</span>
                            <span class="info-box-number">{{ ucfirst($programme->etat_rdv) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <form id="editProgrammeForm" action="{{ route('aftlb_transport.programme.update', $programme->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Informations de base -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Informations de base</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_programme" class="font-weight-bold">📅 Date du Programme *</label>
                                    <input type="date" class="form-control" id="date_programme" name="date_programme" 
                                           value="{{ $programme->date_programme->format('Y-m-d') }}" 
                                           min="{{ date('Y-m-d') }}" 
                                           max="{{ date('Y-m-d', strtotime('+1 year')) }}"
                                           required>
                                    <small class="form-text text-muted">
                                        La date ne peut pas être antérieure à aujourd'hui
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id" class="font-weight-bold">🚗 Chauffeur *</label>
                                    <select class="form-control select2" id="user_id" name="user_id" required style="width: 100%;">
                                        <option value="">-- Sélectionner un Chauffeur --</option>
                                        @foreach($chauffeurs as $chauffeur)
                                        <option value="{{ $chauffeur->id }}" {{ $programme->user_id == $chauffeur->id ? 'selected' : '' }}>
                                            {{ $chauffeur->first_name }} {{ $chauffeur->last_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="actions_a_faire" class="font-weight-bold">🎯 Action à faire *</label>
                                    <select class="form-control" id="actions_a_faire" name="actions_a_faire" required>
                                        <option value="depot" {{ $programme->actions_a_faire == 'depot' ? 'selected' : '' }}>📦 Dépôt</option>
                                        <option value="recuperation" {{ $programme->actions_a_faire == 'recuperation' ? 'selected' : '' }}>🔄 Récupération</option>
                                        <option value="livraison" {{ $programme->actions_a_faire == 'livraison' ? 'selected' : '' }}>🚚 Livraison</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quantite" class="font-weight-bold">📊 Quantité totale *</label>
                                    <input type="number" class="form-control" id="quantite" name="quantite" 
                                           value="{{ $programme->quantite }}" required min="1" readonly>
                                    <small class="form-text text-muted">Calculée automatiquement à partir des articles</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations du client -->
                <div class="card card-success mt-4">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user mr-2"></i>Informations du client</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nom_expediteur" class="font-weight-bold">👤 Nom du client *</label>
                                    <input type="text" class="form-control" id="nom_expediteur" name="nom_expediteur" 
                                           value="{{ $programme->nom_expediteur }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tel_expediteur" class="font-weight-bold">📞 Téléphone *</label>
                                    <input type="text" class="form-control" id="tel_expediteur" name="tel_expediteur" 
                                           value="{{ $programme->tel_expediteur }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="lieu_expedition" class="font-weight-bold">📍 Adresse de dépôt ou récupération *</label>
                                    <textarea class="form-control" id="lieu_expedition" name="lieu_expedition" rows="2" required>{{ $programme->lieu_expedition }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="nature_du_colis" class="font-weight-bold">📦 Nature du colis *</label>
                                    <input type="text" class="form-control" id="nature_du_colis" name="nature_du_colis" 
                                           value="{{ $programme->nature_du_colis }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

       <!-- Section pour les articles -->
<div class="card card-warning mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">
            <i class="fas fa-boxes mr-2"></i>Articles du programme
            @if($programme->items && $programme->items->count() > 0)
                <span class="badge badge-primary ml-2">{{ $programme->items->count() }} article(s)</span>
            @endif
        </h3>
        @if($programme->etat_rdv !== 'effectué')
        <button type="button" class="btn btn-success btn-sm" id="addItemBtn">
            <i class="fas fa-plus mr-1"></i> Ajouter un article
        </button>
        @endif
    </div>
    <div class="card-body">
        <div id="items-container">
            @if($programme->items && $programme->items->count() > 0)
                @foreach($programme->items as $index => $item)
                <div class="item-card card mb-3" data-item-id="{{ $item->id }}">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-box text-primary mr-2"></i>
                            Article {{ $index + 1 }}
                            @if($item->valeur_colis > 0)
                                <span class="badge badge-success ml-2">{{ number_format($item->valeur_colis, 0, ',', ' ') }} FCFA</span>
                            @endif
                        </h6>
                        @if($programme->etat_rdv !== 'effectué')
                        <button type="button" class="btn btn-danger btn-sm remove-item-btn" 
                                data-item-id="{{ $item->id }}">
                            <i class="fas fa-trash"></i>
                        </button>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">🛠️ Service *</label>
                                    <input type="text" class="form-control item-service" name="items[{{ $item->id }}][service]"
                                    value="{{ $item->service }}" 
                                    {{ $programme->etat_rdv === 'effectué' ? 'readonly' : 'required' }}
                                    placeholder="Ex: Transport express">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">📋 Type de colis *</label>
                                    <select class="form-control item-type-colis" name="items[{{ $item->id }}][type_colis]"
                                            {{ $programme->etat_rdv === 'effectué' ? 'disabled' : 'required' }}>
                                        <option value="standard" {{ $item->type_colis == 'standard' ? 'selected' : '' }}>Standard</option>
                                        <option value="fragile" {{ $item->type_colis == 'fragile' ? 'selected' : '' }}>Fragile</option>
                                        <option value="dangerous" {{ $item->type_colis == 'dangerous' ? 'selected' : '' }}>Dangereux</option>
                                        <option value="perishable" {{ $item->type_colis == 'perishable' ? 'selected' : '' }}>Périssable</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">📦 Quantité *</label>
                                    <input type="number" class="form-control item-quantite" name="items[{{ $item->id }}][quantite_colis]"
                                           value="{{ $item->quantite_colis }}" min="1" 
                                           {{ $programme->etat_rdv === 'effectué' ? 'readonly' : 'required' }}>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">💰 Valeur (FCFA)</label>
                                    <input type="number" class="form-control item-valeur" name="items[{{ $item->id }}][valeur_colis]"
                                           value="{{ $item->valeur_colis }}" step="0.01" min="0"
                                           {{ $programme->etat_rdv === 'effectué' ? 'readonly' : '' }}
                                           placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">⚖️ Poids (kg)</label>
                                    <input type="number" class="form-control item-poids" name="items[{{ $item->id }}][poids]"
                                           value="{{ $item->poids }}" step="0.01" min="0"
                                           {{ $programme->etat_rdv === 'effectué' ? 'readonly' : '' }}
                                           placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">📏 Dimensions (cm)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control item-longueur" name="items[{{ $item->id }}][longueur]"
                                               value="{{ $item->longueur }}" placeholder="L" min="0"
                                               {{ $programme->etat_rdv === 'effectué' ? 'readonly' : '' }}>
                                        <input type="number" class="form-control item-largeur" name="items[{{ $item->id }}][largeur]"
                                               value="{{ $item->largeur }}" placeholder="l" min="0"
                                               {{ $programme->etat_rdv === 'effectué' ? 'readonly' : '' }}>
                                        <input type="number" class="form-control item-hauteur" name="items[{{ $item->id }}][hauteur]"
                                               value="{{ $item->hauteur }}" placeholder="H" min="0"
                                               {{ $programme->etat_rdv === 'effectué' ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">📝 Description</label>
                            <textarea class="form-control item-description" name="items[{{ $item->id }}][description_colis]" rows="2"
                                      {{ $programme->etat_rdv === 'effectué' ? 'readonly' : '' }}
                                      placeholder="Description de l'article...">{{ $item->description_colis }}</textarea>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-4" id="no-items-message">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucun article trouvé pour ce programme</p>
                    @if($programme->etat_rdv !== 'effectué')
                    <p class="text-muted small">Cliquez sur "Ajouter un article" pour commencer</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

                <!-- Boutons d'action -->
                <div class="mt-4 d-flex justify-content-between">
                    <div>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save mr-1"></i> Enregistrer les modifications
                        </button>
                        <a href="{{ route('aftlb_transport.planing.chauffeur') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Retour
                        </a>
                    </div>
                    
                    @if($programme->etat_rdv !== 'effectué')
                    <button type="button" class="btn btn-danger" id="deleteBtn" data-id="{{ $programme->id }}">
                        <i class="fas fa-trash mr-1"></i> Supprimer le programme
                    </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- Assurez-vous qu'Axios est bien chargé. Si ce n'est pas déjà le cas dans votre layout, ajoutez-le. --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
$(document).ready(function() {
    // Initialisation
    $('.select2').select2();
    checkItemsVisibility();
    updateTotalQuantity(); // Calculer la quantité au chargement

    // --- GESTION DES ARTICLES ---
    let itemCounter = {{ $programme->items->count() > 0 ? $programme->items->pluck('id')->max() : 0 }};

    // Template pour un nouvel article (inchangé)
    const newItemTemplate = (index) => `
    <div class="item-card card mb-3" data-item-id="new-${index}">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-box text-success mr-2"></i>Nouvel article</h6>
            <button type="button" class="btn btn-danger btn-sm remove-item-btn"><i class="fas fa-trash"></i></button>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6"><div class="form-group"><label class="font-weight-bold">🛠️ Service *</label><input type="text" class="form-control item-service" name="items[new-${index}][service]" placeholder="Ex: Transport express" required></div></div>
                <div class="col-md-6"><div class="form-group"><label class="font-weight-bold">📋 Type de colis *</label><select class="form-control item-type-colis" name="items[new-${index}][type_colis]" required><option value="">Sélectionner...</option><option value="standard">Standard</option><option value="fragile">Fragile</option><option value="dangerous">Dangereux</option><option value="perishable">Périssable</option></select></div></div>
            </div>
            <div class="row">
                <div class="col-md-3"><div class="form-group"><label class="font-weight-bold">📦 Quantité *</label><input type="number" class="form-control item-quantite" name="items[new-${index}][quantite_colis]" value="1" min="1" required></div></div>
                <div class="col-md-3"><div class="form-group"><label class="font-weight-bold">💰 Valeur (FCFA)</label><input type="number" class="form-control item-valeur" name="items[new-${index}][valeur_colis]" value="0" step="0.01" min="0" placeholder="0.00"></div></div>
                <div class="col-md-3"><div class="form-group"><label class="font-weight-bold">⚖️ Poids (kg)</label><input type="number" class="form-control item-poids" name="items[new-${index}][poids]" value="0" step="0.01" min="0" placeholder="0.00"></div></div>
                <div class="col-md-3"><div class="form-group"><label class="font-weight-bold">📏 Dimensions (cm)</label><div class="input-group input-group-sm"><input type="number" class="form-control item-longueur" name="items[new-${index}][longueur]" placeholder="L" min="0"><input type="number" class="form-control item-largeur" name="items[new-${index}][largeur]" placeholder="l" min="0"><input type="number" class="form-control item-hauteur" name="items[new-${index}][hauteur]" placeholder="H" min="0"></div></div></div>
            </div>
            <div class="form-group"><label class="font-weight-bold">📝 Description</label><textarea class="form-control item-description" name="items[new-${index}][description_colis]" rows="2" placeholder="Description de l'article..."></textarea></div>
        </div>
    </div>`;

    // CORRECTION : Le bouton "Ajouter un article" fonctionne maintenant
    $('#addItemBtn').on('click', function() {
        itemCounter++;
        $('#no-items-message').remove();
        $('#items-container').append(newItemTemplate(itemCounter));
        updateTotalQuantity();
    });

    // Supprimer un article (logique inchangée mais optimisée)
    $(document).on('click', '.remove-item-btn', function() {
        const itemCard = $(this).closest('.item-card');
        const itemId = itemCard.data('item-id');

        if (itemId && !itemId.toString().startsWith('new-')) {
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Cet article sera supprimé définitivement.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    const deleteUrl = '{{ route("aftlb_transport.programme.deleteItem", ["programmeId" => $programme->id, "itemId" => "PLACEHOLDER"]) }}'.replace('PLACEHOLDER', itemId);
                    axios.delete(deleteUrl)
                        .then(response => {
                            if (response.data.success) {
                                itemCard.remove();
                                updateTotalQuantity();
                                checkItemsVisibility();
                                Swal.fire('Supprimé !', response.data.message, 'success');
                            }
                        })
                        .catch(error => Swal.fire('Erreur', 'La suppression a échoué.', 'error'));
                }
            });
        } else {
            itemCard.remove();
            updateTotalQuantity();
            checkItemsVisibility();
        }
    });

    // Mettre à jour la quantité totale
    function updateTotalQuantity() {
        let total = 0;
        $('.item-quantite').each(function() {
            total += parseInt($(this).val()) || 0;
        });
        $('#quantite').val(total);
    }
    $(document).on('input', '.item-quantite', updateTotalQuantity);

    // Afficher/cacher le message "aucun article"
    function checkItemsVisibility() {
        if ($('.item-card').length === 0) {
            if ($('#no-items-message').length === 0) {
                $('#items-container').html(`<div class="text-center py-4" id="no-items-message"><i class="fas fa-box-open fa-3x text-muted mb-3"></i><p class="text-muted">Aucun article trouvé. Cliquez sur "Ajouter un article".</p></div>`);
            }
        } else {
            $('#no-items-message').remove();
        }
    }

    // --- SOUMISSION DU FORMULAIRE ---
    // CORRECTION : La soumission est maintenant gérée de manière fiable
    $('#editProgrammeForm').on('submit', function(e) {
        e.preventDefault(); // Empêche la soumission classique du formulaire

        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enregistrement...');

        // Utiliser FormData pour collecter toutes les données du formulaire
        const formData = new FormData(this);
        
        // CORRECTION : Ajouter _method et items_data manuellement car FormData les gère différemment
        formData.append('_method', 'PUT');

        const items = [];
        $('.item-card').each(function() {
            const card = $(this);
            const itemId = card.data('item-id');
            items.push({
                id: itemId.toString().startsWith('new-') ? null : itemId,
                service: card.find('.item-service').val(),
                type_colis: card.find('.item-type-colis').val(),
                quantite_colis: card.find('.item-quantite').val(),
                valeur_colis: card.find('.item-valeur').val(),
                poids: card.find('.item-poids').val(),
                longueur: card.find('.item-longueur').val(),
                largeur: card.find('.item-largeur').val(),
                hauteur: card.find('.item-hauteur').val(),
                description_colis: card.find('.item-description').val()
            });
        });
        
        // Valider qu'il y a au moins un article
        if (items.length === 0) {
            Swal.fire('Attention', 'Le programme doit contenir au moins un article.', 'warning');
            submitBtn.prop('disabled', false).html(originalText);
            return;
        }

        formData.append('items_data', JSON.stringify(items));

        const url = $(this).attr('action');

        axios.post(url, formData)
            .then(response => {
                if (response.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès !',
                        text: response.data.message,
                    }).then(() => {
                        // Redirection vers la page du planning
                        window.location.href = "{{ route('aftlb_transport.planing.chauffeur') }}";
                    });
                } else {
                    // Gérer les erreurs métier renvoyées par le serveur
                    Swal.fire('Erreur', response.data.message || 'Une erreur est survenue.', 'error');
                }
            })
            .catch(error => {
                let errorMessage = 'Une erreur est survenue lors de la mise à jour.';
                if (error.response && error.response.data && error.response.data.message) {
                    errorMessage = error.response.data.message;
                }
                Swal.fire('Échec', errorMessage, 'error');
            })
            .finally(() => {
                // Réactiver le bouton dans tous les cas
                submitBtn.prop('disabled', false).html(originalText);
            });
    });

    // Suppression du programme (inchangé)
    $('#deleteBtn').on('click', function() {
        const programmeId = $(this).data('id');
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: "Cette action est irréversible !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                const deleteUrl = `/aftlb_transport/programme-delete/${programmeId}-aft-louis-b`;
                axios.delete(deleteUrl)
                    .then(response => {
                        if (response.data.success) {
                            Swal.fire('Supprimé !', response.data.message, 'success')
                                .then(() => window.location.href = "{{ route('aftlb_transport.planing.chauffeur') }}");
                        } else {
                            Swal.fire('Erreur', response.data.message, 'error');
                        }
                    })
                    .catch(() => Swal.fire('Erreur', 'La suppression a échoué.', 'error'));
            }
        });
    });

    // Logique pour programme déjà effectué (inchangé)
    @if($programme->etat_rdv === 'effectué')
        $('#editProgrammeForm input, #editProgrammeForm select, #editProgrammeForm textarea').prop('disabled', true);
        $('#submitBtn, #addItemBtn, #deleteBtn, .remove-item-btn').prop('disabled', true).hide();
        Swal.fire({
            icon: 'info',
            title: 'Programme effectué',
            text: 'Ce programme est verrouillé et ne peut plus être modifié.',
        });
    @endif
});
</script>
<style>
/* CSS inchangé */
.card { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); border: 1px solid rgba(0, 0, 0, 0.125); }
.card-header { border-bottom: 1px solid rgba(0, 0, 0, 0.125); }
.item-card { border-left: 4px solid #007bff; transition: all 0.3s ease; }
.item-card:hover { box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); }
.info-box { box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2); border-radius: 0.25rem; }
.btn { border-radius: 0.25rem; }
.form-control:disabled { background-color: #f8f9fa; }
</style>
@endsection