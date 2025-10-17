{{-- resources/views/customer/invoice/_devis_table.blade.php --}}
@php
    $items = $devis->devisItems ?? $devis->items ?? collect();
    $show_actions = $show_actions ?? true;
@endphp

<div class="invoice-card p-4 rounded shadow-sm border-0" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
    <!-- En-tête améliorée -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-4 gap-3">
        <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="bg-primary rounded p-2">
                    <i class="fas fa-file-invoice text-white fs-4"></i>
                </div>
                <div>
                    <h4 class="mb-1 text-dark">Facture <span class="text-primary">{{ $devis->reference }}</span></h4>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge bg-success">
                            <i class="fas fa-check me-1"></i>{{ ucfirst($devis->etat ?? 'confirmé') }}
                        </span>
                        <span class="badge bg-info">
                            <i class="fas fa-box me-1"></i>{{ $devis->colis_count ?? $devis->devisItems->count() }} colis
                        </span>
                        <span class="text-muted small">
                            <i class="fas fa-calendar me-1"></i>{{ $devis->created_at ? $devis->created_at->format('d/m/Y H:i') : '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-lg-end">
            <div class="bg-light rounded p-3 text-center">
                <div class="small text-muted">Total</div>
                <div class="h4 fw-bold text-primary mb-0">
                    {{ $devis->devise ?? 'XOF' }} {{ number_format($devis->montant ?? 0, 0, ',', ' ') }}
                </div>
            </div>
        </div>
    </div>

   <!-- Informations client et entreprise -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 bg-white shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title text-primary mb-3">
                    <i class="fas fa-user me-2"></i>Informations Expéditeur
                </h6>
                <div class="mb-2">
                    <strong class="text-dark">{{ $devis->nom_expediteur ?? '-' }} {{ $devis->prenom_expediteur ?? '' }}</strong>
                </div>
                <div class="small text-muted mb-1">
                    <i class="fas fa-phone me-1"></i>{{ $devis->tel_expediteur ?? 'Non spécifié' }}
                </div>
                <div class="small text-muted mb-1">
                    <i class="fas fa-envelope me-1"></i>{{ $devis->email_expediteur ?? 'Non spécifié' }}
                </div>
                <div class="small text-muted">
                    <i class="fas fa-map-marker-alt me-1"></i>{{ $devis->lieu_expedition ?? 'Non spécifié' }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mt-3 mt-md-0">
        <div class="card border-0 bg-white shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title text-primary mb-3">
                    <i class="fas fa-user me-2"></i>Informations Destinataire
                </h6>
                <div class="mb-2">
                    <strong class="text-dark">{{ $devis->nom_destinataire ?? '-' }} {{ $devis->prenom_destinataire ?? '' }}</strong>
                </div>
                <div class="small text-muted mb-1">
                    <i class="fas fa-phone me-1"></i>{{ $devis->tel_destinataire ?? 'Non spécifié' }}
                </div>
                <div class="small text-muted">
                    <i class="fas fa-map-marker-alt me-1"></i>{{ $devis->lieu_destination ?? 'Non spécifié' }}
                </div>
                <div class="mt-3 pt-2 border-top">
                    <div class="small text-muted">Mode / Retrait</div>
                    <strong class="text-dark">{{ $devis->mode_transit ?? '-' }} — {{ $devis->mode_de_retrait ?? '-' }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Tableau des articles -->
    <div class="table-responsive mb-4">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-primary">
                <tr>
                    <th class="border-0 ps-4">
                        <i class="fas fa-cube me-2"></i>Produit / Description
                    </th>
                    <th class="border-0 text-center" style="width:100px">
                        <i class="fas fa-layer-group me-2"></i>Quantité
                    </th>
                    <th class="border-0 text-center" style="width:120px">
                        <i class="fas fa-weight me-2"></i>Poids (kg)
                    </th>
                    <th class="border-0 text-center" style="width:150px">
                        <i class="fas fa-ruler-combined me-2"></i>Dimensions
                    </th>
                    <th class="border-0 text-end" style="width:140px">
                        <i class="fas fa-tag me-2"></i>Valeur
                    </th>
                    <th class="border-0 text-end pe-4" style="width:150px">
                        <i class="fas fa-money-bill-wave me-2"></i>Montant
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr class="hover-shadow">
                        <td class="ps-4">
                            <div class="fw-semibold text-dark">{{ $item->service ?? $item->description_colis ?? '—' }}</div>
                            @if($item->type_colis)
                                <div class="small text-muted">
                                    <i class="fas fa-tag me-1"></i>{{ $item->type_colis }}
                                </div>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border fs-6">{{ $item->quantite_colis ?? 1 }}</span>
                        </td>
                        <td class="text-center">
                            @if($item->poids)
                                <span class="fw-semibold">{{ $item->poids }} kg</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($item->longueur || $item->largeur || $item->hauteur)
                                <div class="small">
                                    {{ $item->longueur ?? '-' }} × {{ $item->largeur ?? '-' }} × {{ $item->hauteur ?? '-' }}
                                </div>
                                <div class="text-muted smaller">L × l × H</div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <span class="text-dark fw-semibold">
                                {{ $devis->devise ?? 'XOF' }} {{ number_format($item->valeur_colis ?? 0, 0, ',', ' ') }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <span class="fw-bold text-primary">
                                {{ $devis->devise ?? 'XOF' }} {{ number_format($item->montant ?? 0, 0, ',', ' ') }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <h5>Aucun article trouvé</h5>
                                <p class="mb-0">Aucun item n'est associé à ce devis</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($items->isNotEmpty())
                <tfoot class="table-light">
                    <tr>
                        <td colspan="5" class="text-end border-0 pt-3">
                            <strong class="fs-5 text-dark">Total {{ $devis->devise ?? 'XOF' }}</strong>
                        </td>
                        <td class="text-end border-0 pt-3 pe-4">
                            <strong class="fs-4 text-primary">
                                {{ number_format($devis->montant ?? ($items->sum('montant') ?? 0), 0, ',', ' ') }}
                            </strong>
                        </td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    <!-- Pied de page avec actions -->
    @if($show_actions)
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center pt-3 border-top gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success fs-6">
                    <i class="fas fa-check-circle me-1"></i>État : {{ ucfirst($devis->etat ?? '-') }}
                </span>
                <small class="text-muted">Référence interne : {{ $devis->id }}</small>
            </div>

            <div class="d-flex gap-2 flex-wrap justify-content-center">
                <a href="{{ route('customer_colis.facture') }}" 
                   class="btn btn-light border d-flex align-items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </a>
                <a href="{{ route('customer_colis.facture.show', $devis->reference) }}" 
                   class="btn btn-outline-primary d-flex align-items-center gap-2">
                    <i class="fas fa-eye"></i>
                    Détails
                </a>
                <a href="{{ route('customer_colis.facture.pdf', $devis->reference) }}" 
                   class="btn btn-primary d-flex align-items-center gap-2"
                   target="_blank">
                    <i class="fas fa-download"></i>
                    Télécharger PDF
                </a>
            </div>
        </div>
    @endif
</div>

<style>
.invoice-card {
    border-left: 4px solid #667eea;
}

.table th {
    font-weight: 600;
    font-size: 0.85rem;
}

.hover-shadow {
    transition: all 0.2s ease;
}

.hover-shadow:hover {
    background-color: #f8f9fa;
    transform: translateX(2px);
}

.smaller {
    font-size: 0.75rem;
}

.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}
</style>