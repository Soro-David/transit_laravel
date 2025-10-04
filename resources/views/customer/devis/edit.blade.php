@extends('customer.layouts.index')

@section('content')
<div class="container py-4">
    <div class="card shadow-lg rounded-3 border-0">
        <div class="card-header" style="background: linear-gradient(90deg,#05a805,#0b7cff); color:#fff;">
            <h4 class="mb-0">Modifier Devis : <span style="font-weight:700;">{{ $devis->reference }}</span></h4>
        </div>

        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('customer_colis.devis.update', $devis->reference) }}" method="POST" id="editDevisForm">
                @csrf
                @method('PUT')

                {{-- Mode Transit --}}
                <div class="mb-3">
                    <label for="mode_transit" class="form-label">Mode de transit</label>
                    <select name="mode_transit" id="mode_transit" class="form-select" required>
                        <option value="maritime" {{ $devis->mode_transit == 'maritime' ? 'selected' : '' }}>Maritime</option>
                        <option value="aerien" {{ $devis->mode_transit == 'aerien' ? 'selected' : '' }}>Aérien</option>
                    </select>
                </div>

                {{-- Pays Expédition --}}
                <div class="mb-3">
                    <label for="pays_expedition" class="form-label">Pays d'expédition</label>
                    <select name="pays_expedition" id="pays_expedition" class="form-select" required>
                        <option value="France" {{ $devis->pays_expedition == 'France' ? 'selected' : '' }}>France</option>
                        <option value="Chine" {{ $devis->pays_expedition == 'Chine' ? 'selected' : '' }}>Chine</option>
                    </select>
                </div>

                {{-- Agences --}}
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="agence_expedition" class="form-label">Agence expédition</label>
                        <select name="agence_expedition" id="agence_expedition" class="form-select" required>
                            @foreach($agencesExpedition as $agence)
                                <option value="{{ $agence->nom_agence }}" data-pays="{{ $agence->pays_agence }}" 
                                    {{ $devis->agence_expedition == $agence->nom_agence ? 'selected' : '' }}>
                                    {{ $agence->nom_agence }} ({{ $agence->pays_agence }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="agence_destination_societe" class="form-label">Agence destination</label>
                        <select name="agence_destination_societe" id="agence_destination_societe" class="form-select" required>
                            @foreach($agencesDestination as $agence)
                                <option value="{{ $agence->nom_agence }}" {{ $devis->agence_destination == $agence->nom_agence ? 'selected' : '' }}>
                                    {{ $agence->nom_agence }} ({{ $agence->pays_agence }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Expéditeur --}}
                <div class="mb-3 row">
                    <div class="col-md-3">
                        <label for="nom_expediteur" class="form-label">Nom</label>
                        <input type="text" name="nom_expediteur" class="form-control" value="{{ $devis->nom_expediteur }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="prenom_expediteur" class="form-label">Prénom</label>
                        <input type="text" name="prenom_expediteur" class="form-control" value="{{ $devis->prenom_expediteur }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="email_expediteur" class="form-label">Email</label>
                        <input type="email" name="email_expediteur" class="form-control" value="{{ $devis->email_expediteur }}">
                    </div>
                    <div class="col-md-3">
                        <label for="tel_expediteur" class="form-label">Téléphone</label>
                        <input type="text" name="tel_expediteur" class="form-control" value="{{ $devis->tel_expediteur }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="adresse_expediteur" class="form-label">Adresse</label>
                    <input type="text" name="adresse_expediteur" class="form-control" value="{{ $devis->adresse_expediteur }}" required>
                </div>

                {{-- Devise --}}
                <div class="mb-3">
                    <label for="devise" class="form-label">Devise</label>
                    <select name="devise" id="devise" class="form-select" required>
                        <option value="EUR" {{ $devis->devise == 'EUR' ? 'selected' : '' }}>EUR</option>
                        <option value="FCFA" {{ $devis->devise == 'FCFA' ? 'selected' : '' }}>FCFA</option>
                    </select>
                </div>

                {{-- Items --}}
                <h5 class="fw-bold mt-4">📦 Items</h5>
                <div id="items-container">
                    @foreach($devis->items as $key => $item)
                    <div class="item-row border rounded p-3 mb-3">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Quantité</label>
                                <input type="number" name="quantite_colis[]" class="form-control" value="{{ $item->quantite_colis }}" min="1" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Produit / Service</label>
                                <input type="text" name="service[]" class="form-control" value="{{ $item->service }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Type</label>
                                <select name="type_colis[]" class="form-select" required>
                                    <option value="standard" {{ $item->type_colis == 'standard' ? 'selected' : '' }}>Standard</option>
                                    <option value="fragile" {{ $item->type_colis == 'fragile' ? 'selected' : '' }}>Fragile</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Valeur</label>
                                <input type="number" name="valeur_colis[]" class="form-control" value="{{ $item->valeur_colis }}" min="0">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Description</label>
                                <input type="text" name="description_colis[]" class="form-control" value="{{ $item->description_colis }}">
                            </div>
                            <div class="col-md-2 poids-field" style="display: {{ $devis->mode_transit === 'maritime' ? 'none' : 'block' }};">
                                <label class="form-label">Poids</label>
                                <input type="number" step="0.01" name="poids[]" class="form-control" value="{{ $item->poids }}">
                            </div>
                            <div class="col-md-2 longueur-field" style="display: {{ $devis->mode_transit === 'maritime' ? 'block' : 'none' }};">
                                <label class="form-label">Longueur</label>
                                <input type="number" step="0.01" name="longueur[]" class="form-control" value="{{ $item->longueur }}">
                            </div>
                            <div class="col-md-2 largeur-field" style="display: {{ $devis->mode_transit === 'maritime' ? 'block' : 'none' }};">
                                <label class="form-label">Largeur</label>
                                <input type="number" step="0.01" name="largeur[]" class="form-control" value="{{ $item->largeur }}">
                            </div>
                            <div class="col-md-2 hauteur-field" style="display: {{ $devis->mode_transit === 'maritime' ? 'block' : 'none' }};">
                                <label class="form-label">Hauteur</label>
                                <input type="number" step="0.01" name="hauteur[]" class="form-control" value="{{ $item->hauteur }}">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <a href="{{ route('customer_colis.devis.show', $devis->reference) }}" class="btn btn-light btn-sm me-2">Annuler</a>
                    <button type="submit" class="btn btn-success" id="submitBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmation -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Succès</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                <h5>Devis mis à jour avec succès !</h5>
                <p class="text-muted">Redirection en cours...</p>
            </div>
        </div>
    </div>
</div>

<style>
.card-header h4 { font-weight:700; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modeTransitSelect = document.getElementById('mode_transit');
    const paysExpeditionSelect = document.getElementById('pays_expedition');
    const agenceExpeditionSelect = document.getElementById('agence_expedition');
    const agenceDestinationSelect = document.getElementById('agence_destination_societe');
    const form = document.getElementById('editDevisForm');
    const submitBtn = document.getElementById('submitBtn');

    // Configuration des agences automatiques
    const agenceConfig = {
        pays: {
            'France': 'AFT Agence Louis Bleriot',
            'Chine': 'Agence de Chine'
        },
        modeTransit: {
            'maritime': 'Carrefour Angré',
            'aerien': 'Angré 8ème Tranche'
        }
    };

    function updateAgenceExpedition() {
        const pays = paysExpeditionSelect.value;
        const agenceNom = agenceConfig.pays[pays];
        
        if (agenceNom) {
            // Trouver l'option correspondante et la sélectionner
            const options = agenceExpeditionSelect.options;
            for (let i = 0; i < options.length; i++) {
                if (options[i].textContent.includes(agenceNom)) {
                    agenceExpeditionSelect.value = options[i].value;
                    break;
                }
            }
        }
    }

    function updateAgenceDestination() {
        const modeTransit = modeTransitSelect.value;
        const agenceNom = agenceConfig.modeTransit[modeTransit];
        
        if (agenceNom) {
            // Trouver l'option correspondante et la sélectionner
            const options = agenceDestinationSelect.options;
            for (let i = 0; i < options.length; i++) {
                if (options[i].textContent.includes(agenceNom)) {
                    agenceDestinationSelect.value = options[i].value;
                    break;
                }
            }
        }
    }

    function updateDevise() {
        const pays = paysExpeditionSelect.value;
        const deviseSelect = document.getElementById('devise');
        deviseSelect.value = pays === 'France' ? 'EUR' : 'FCFA';
    }

    function updateColisFields() {
        const maritime = modeTransitSelect.value === 'maritime';
        document.querySelectorAll('.poids-field').forEach(el => {
            el.style.display = maritime ? 'none' : 'block';
        });
        document.querySelectorAll('.longueur-field, .largeur-field, .hauteur-field').forEach(el => {
            el.style.display = maritime ? 'block' : 'none';
        });
    }

    // Événements
    modeTransitSelect.addEventListener('change', function() {
        updateAgenceDestination();
        updateColisFields();
    });

    paysExpeditionSelect.addEventListener('change', function() {
        updateAgenceExpedition();
        updateDevise();
    });

    // Initialisation au chargement
    updateAgenceExpedition();
    updateAgenceDestination();
    updateDevise();
    updateColisFields();

    // Gestion de la soumission du formulaire
    form.addEventListener('submit', function(e) {
        const spinner = submitBtn.querySelector('.spinner-border');
        const buttonText = submitBtn.querySelector('span:not(.spinner-border)');
        
        // Afficher le spinner
        spinner.classList.remove('d-none');
        submitBtn.disabled = true;
    });
});
</script>
@endsection