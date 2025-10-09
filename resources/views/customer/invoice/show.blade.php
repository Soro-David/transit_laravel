@extends('customer.layouts.index')

@section('content-header')
<div class="container-fluid">
    <!-- Première ligne : Titre principal et référence -->
    <div class="row align-items-end mb-2">
        <div class="col">
            <h2 class="h4 fw-bold text-dark mb-1">Détails de la Facture</h2>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary fs-6">{{ $devis->reference }}</span>
            </div>
        </div>
    </div>

    <!-- Deuxième ligne : Informations date et montant -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <p class="text-muted mb-0 small">
                <i class="fas fa-calendar me-1"></i>Créée le {{ $devis->created_at->format('d/m/Y') }}
                • <i class="fas fa-money-bill-wave me-1"></i>Montant : {{ $devis->devise ?? 'XOF' }} {{ number_format($devis->montant ?? 0, 0, ',', ' ') }}
            </p>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Informations principales -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-light py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    Informations Client
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="small text-muted mb-1">Client</label>
                    <p class="fw-semibold mb-0">{{ $devis->nom_expediteur ?? '-' }} {{ $devis->prenom_expediteur ?? '' }}</p>
                </div>
                <div class="mb-3">
                    <label class="small text-muted mb-1">Téléphone</label>
                    <p class="mb-0">{{ $devis->tel_expediteur ?? '-' }}</p>
                </div>
                <div class="mb-3">
                    <label class="small text-muted mb-1">Email</label>
                    <p class="mb-0">{{ $devis->email_expediteur ?? '-' }}</p>
                </div>
                <div class="mb-3">
                    <label class="small text-muted mb-1">Adresse</label>
                    <p class="mb-0">{{ $devis->adresse_expediteur ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations livraison -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-light py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-shipping-fast text-primary me-2"></i>
                    Informations Livraison
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="small text-muted mb-1">Mode de Transit</label>
                    <p class="fw-semibold mb-0">{{ $devis->mode_transit ?? '-' }}</p>
                </div>
                <div class="mb-3">
                    <label class="small text-muted mb-1">Mode de Retrait</label>
                    <p class="mb-0">{{ $devis->mode_de_retrait ?? '-' }}</p>
                </div>
                <div class="mb-3">
                    <label class="small text-muted mb-1">Pays</label>
                    <p class="mb-0">{{ $devis->pays_expedition ?? '-' }}</p>
                </div>
                <div class="mb-3">
                    <label class="small text-muted mb-1">Date de Création</label>
                    <p class="mb-0">{{ $devis->created_at->format('d/m/Y à H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Résumé financier -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-light py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar text-primary me-2"></i>
                    Résumé Financier
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="small text-muted mb-1">Montant Total</label>
                    <p class="h4 fw-bold text-primary mb-0">
                        {{ $devis->devise ?? 'XOF' }} {{ number_format($devis->montant ?? 0, 0, ',', ' ') }}
                    </p>
                </div>
                <div class="mb-3">
                    <label class="small text-muted mb-1">Statut</label>
                    <div>
                        <span class="badge bg-success fs-6">
                            <i class="fas fa-check-circle me-1"></i>
                            {{ ucfirst($devis->etat ?? 'confirmé') }}
                        </span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="small text-muted mb-1">Référence</label>
                    <p class="fw-semibold mb-0">{{ $devis->reference }}</p>
                </div>
                
                <!-- Boutons déplacés ici, en bas de la carte -->
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex gap-2">
                        <a href="{{ route('customer_colis.facture') }}" 
                           class="btn btn-outline-secondary flex-fill d-flex align-items-center justify-content-center gap-2">
                            <i class="fas fa-arrow-left"></i>
                            Retour
                        </a>
                        <a href="{{ route('customer_colis.facture.pdf', $devis->reference) }}" 
                           target="_blank" 
                           class="btn btn-primary flex-fill d-flex align-items-center justify-content-center gap-2">
                            <i class="fas fa-download"></i>
                            Télécharger
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Détails des articles -->
<div class="card shadow-lg border-0">
    <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-list-ul text-primary me-2"></i>
            Détails des Articles
        </h5>
        <span class="badge bg-primary fs-6">
            {{ ($devis->devisItems ?? collect())->count() }} article(s)
        </span>
    </div>
    <div class="card-body p-0">
        @include('customer.invoice._devis_table', ['devis' => $devis, 'show_actions' => false])
    </div>
</div>
@endsection

@section('styles')
<style>
.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.badge {
    font-size: 0.75em;
}

.hover-shadow {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* Améliorations pour l'en-tête */
.content-header h2 {
    font-size: 1.5rem;
}

.content-header .text-muted {
    font-size: 0.85rem;
}
</style>
@endsection