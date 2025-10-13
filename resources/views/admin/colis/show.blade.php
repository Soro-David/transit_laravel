@extends('admin.layouts.admin')

@section('content-header')
    Détails du Devis
@endsection

@section('content')
<div class="container py-4">
    <div class="card shadow-lg rounded-3 border-0">
        <div class="card-header" style="background: linear-gradient(90deg, #0d6efd, #6f42c1); color:#fff;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">Devis : <span style="font-weight:700;">{{ $devis->reference }}</span></h4>
                    <small class="text-light">Créé le : {{ $devis->created_at->format('d/m/Y H:i') }}</small>
                </div>
                <div class="text-end">
                    {{-- MODIFICATION : Mise à jour de la route --}}
                    <a href="{{ route('colis.hold') }}" class="btn btn-light btn-sm">← Retour à la liste</a>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            {{-- Section d'informations générales --}}
            <div class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 rounded" style="background:#f0f8ff; border-left:4px solid #0d6efd;">
                            <strong>État Actuel</strong>
                            <div class="mt-2">
                                <span class="badge bg-warning text-dark fs-6">
                                    {{ ucfirst($devis->etat) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-3 rounded" style="background:#f8f9fa; border-left:4px solid #6c757d;">
                            <strong>Clients</strong>
                            <div class="mt-2">{{ $devis->nom_expediteur }} {{ $devis->prenom_expediteur }}</div>
                            <div class="small text-muted mt-1"><i class="fas fa-phone-alt me-1"></i> {{ $devis->tel_expediteur ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-3 rounded" style="background:#f8f9fa; border-left:4px solid #6c757d;">
                            <strong>Agence d'expédition</strong>
                            <div class="mt-2">{{ $devis->agence_expedition }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="fw-bold mb-3 mt-4">📦 Contenu du Devis</h5>
            
            {{-- Grouper les items par service --}}
            @php
                $groupedItems = $devis->items->groupBy('service');
                $montantTotal = 0;
            @endphp
            
            @forelse($groupedItems as $serviceName => $items)
            <div class="mb-4 border rounded p-3">
                <h6 class="fw-bold text-primary mb-3">
                    <i class="fas fa-box me-2"></i>Nature ou produit : {{ $serviceName }}
                </h6>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Quantité</th>
                                <th>Type</th>
                                <th>Valeur Déclarée</th>
                                <th>Description</th>
                                <th>Montant ({{ $devis->devise }})</th>
                                @if($devis->mode_transit === 'aerien')
                                    <th>Poids</th>
                                @else
                                    <th>Dimensions (L×l×h)</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $item)
                            @php
                                $montantTotal += $item->montant ?? 0;
                            @endphp
                            <tr>
                                <td>{{ $item->quantite_colis }}</td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ ucfirst($item->type_colis) }}
                                    </span>
                                </td>
                                <td>
                                    @if($item->valeur_colis)
                                        {{ number_format($item->valeur_colis, 0, ',', ' ') }} {{ $devis->devise }}
                                    @else
                                        <span class="text-muted">Non spécifié</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $item->description_colis ?? 'Aucune' }}</td>
                                <td>
                                    <input type="number" 
                                           step="0.01" 
                                           class="form-control form-control-sm montant-item" 
                                           name="montant_items[{{ $item->id }}]" 
                                           value="{{ $item->montant ?? 0 }}" 
                                           data-item-id="{{ $item->id }}"
                                           style="min-width: 100px;">
                                </td>
                                <td>
                                    @if($devis->mode_transit === 'aerien')
                                        {{ $item->poids ? $item->poids . ' kg' : 'N/A' }}
                                    @else
                                        @if($item->longueur && $item->largeur && $item->hauteur)
                                            {{ $item->longueur }}×{{ $item->largeur }}×{{ $item->hauteur }} cm
                                        @else
                                            N/A
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @empty
                <div class="alert alert-warning text-center">
                    Aucun item n'a été trouvé pour ce devis.
                </div>
            @endforelse
        </div>

        {{-- SECTION FORMULAIRE DE VALIDATION --}}
        <div class="card-footer bg-light p-4">
            <h5 class="fw-bold mb-3">Validation du Devis</h5>
            {{-- MODIFICATION : Mise à jour de la route --}}
            <form id="validationForm" action="{{ route('colis.hold.update', ['id' => $devis->id]) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Champ caché pour les montants des items --}}
                <input type="hidden" name="montant_items" id="montantItemsInput">

                <div class="row align-items-end">
                    <div class="col-md-8">
                        <label for="montant" class="form-label">
                            <strong>Montant Final de la Transaction ({{ $devis->devise }})</strong>
                        </label>
                        <input type="number" 
                               step="0.01" 
                               id="montant" 
                               name="montant" 
                               class="form-control form-control-lg @error('montant') is-invalid @enderror" 
                               value="{{ $montantTotal }}"
                               placeholder="Ex: 150000" 
                               required 
                               readonly>
                        @error('montant')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Ce montant est calculé automatiquement à partir de la somme des montants des items.
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-check-circle me-2"></i>Valider et Envoyer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const validationForm = document.getElementById('validationForm');
    const montantItemsInput = document.getElementById('montantItemsInput');
    const montantTotalInput = document.getElementById('montant');
    const montantItemInputs = document.querySelectorAll('.montant-item');

    // Fonction pour calculer le montant total
    function calculerMontantTotal() {
        let total = 0;
        montantItemInputs.forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        montantTotalInput.value = total.toFixed(2);
        return total;
    }

    // Mettre à jour le montant total quand un montant d'item change
    montantItemInputs.forEach(input => {
        input.addEventListener('change', function() {
            calculerMontantTotal();
        });
        
        input.addEventListener('input', function() {
            calculerMontantTotal();
        });
    });

    // Préparer les données avant soumission
    if (validationForm) {
        validationForm.addEventListener('submit', function (event) {
            // Rassembler tous les montants des items dans un objet
            const montantItems = {};
            montantItemInputs.forEach(input => {
                const itemId = input.getAttribute('data-item-id');
                montantItems[itemId] = parseFloat(input.value) || 0;
            });
            
            // Mettre l'objet dans le champ caché
            montantItemsInput.value = JSON.stringify(montantItems);
            
            // Afficher le pop-up de confirmation
            event.preventDefault();
            Swal.fire({
                title: 'Confirmer la validation',
                text: "Êtes-vous sûr de vouloir valider et envoyer ce devis ?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Oui, valider le devis',
                cancelButtonText: 'Annuler',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return new Promise((resolve) => {
                        // Soumettre le formulaire
                        validationForm.submit();
                        resolve();
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    // Cette partie sera exécutée après la soumission réussie
                    // grâce à la redirection depuis le contrôleur
                }
            });
        });
    }

    // Calcul initial
    calculerMontantTotal();

    // Afficher le message de succès si présent dans la session
    @if(session('success'))
    Swal.fire({
        title: 'Devis envoyé !',
        text: '{{ session('success') }}',
        icon: 'success',
        confirmButtonColor: '#28a745',
        confirmButtonText: 'OK'
    });
    @endif

    // Afficher les erreurs si présentes
    @if($errors->any())
    Swal.fire({
        title: 'Erreur',
        text: '{{ $errors->first() }}',
        icon: 'error',
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'OK'
    });
    @endif
});
</script>
@endsection