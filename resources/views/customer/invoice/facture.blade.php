@extends('customer.layouts.index')

@section('content-header')
<div class="container-fluid">
    <!-- Première ligne : Titre principal centré -->
    <div class="row mb-2">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                <h1 class="h3 fw-bold text-primary mb-0">Mes Factures</h1>
            </div>
        </div>
    </div>
    
    <!-- Deuxième ligne : Description centrée avec texte plus petit -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                <p class="mb-0 text-muted small">Gérez et consultez l'ensemble de vos devis confirmés</p>
            </div>
        </div>
    </div>
    
    <!-- Troisième ligne : Barre de recherche à droite et plus petite -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-end">
                <div style="max-width: 350px;">
                    <div class="input-group shadow-sm input-group-sm">
                        <span class="input-group-text bg-white border-end-0 py-2">
                            <i class="fas fa-search text-muted small"></i>
                        </span>
                        <input id="invoiceSearch" type="search" class="form-control border-start-0 py-2" 
                               placeholder="Référence, client, statut..." aria-label="Rechercher">
                        <button id="clearSearch" class="btn btn-outline-secondary py-2" type="button" title="Effacer la recherche">
                            <i class="fas fa-times small"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('content')
<div class="card shadow-lg border-0">
    <div class="card-header bg-light py-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-file-invoice me-2"></i>Liste des factures
                </h5>
            </div>
            <div class="col-md-6 text-md-end">
                <span class="badge bg-primary fs-6">{{ $devisList->count() }} facture(s)</span>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        @if($devisList->isEmpty())
            <div class="text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Aucun devis confirmé</h4>
                    <p class="text-muted">Vos devis confirmés apparaîtront ici</p>
                </div>
            </div>
        @else
            <div id="invoicesContainer" class="p-3">
                @foreach($devisList as $devis)
                    @php
                        $reference = $devis->reference ?? 'N/A';
                        $nomClient = $devis->nom_expediteur ?? '';
                        $prenomClient = $devis->prenom_expediteur ?? '';
                        $clientName = trim($nomClient . ' ' . $prenomClient);
                    @endphp
                    
                    <div class="invoice-item card mb-4 border-0 shadow-sm hover-shadow transition-all" 
                         data-reference="{{ strtolower($reference) }}" 
                         data-client="{{ strtolower($clientName) }}"
                         data-status="confirmé">
                        <div class="card-header bg-white py-3">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-0">
                                        <i class="fas fa-hashtag text-primary me-2"></i>
                                        <strong class="text-dark">{{ $reference }}</strong>
                                    </h6>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Confirmé
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-user text-muted me-2"></i>
                                        <strong class="me-2">Client:</strong>
                                        <span>{{ $clientName ?: 'Non spécifié' }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-calendar text-muted me-2"></i>
                                        <strong class="me-2">Date:</strong>
                                        <span>{{ $devis->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Section dépliante pour les détails -->
                            <div class="accordion" id="accordion{{ $devis->id }}">
                                <div class="accordion-item border-0">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-light" type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#collapse{{ $devis->id }}" 
                                                aria-expanded="false">
                                            <i class="fas fa-list-ul me-2 text-primary"></i>
                                            Voir les détails des articles
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $devis->id }}" class="accordion-collapse collapse" 
                                         data-bs-parent="#accordion{{ $devis->id }}">
                                        <div class="accordion-body p-0 pt-3">
                                            @include('customer.invoice._devis_table', ['devis' => $devis])
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-sync-alt me-1"></i>
                                    Mis à jour {{ $devis->updated_at->diffForHumans() }}
                                </small>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download me-1"></i>Télécharger
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Résultat de recherche vide -->
<div id="noResults" class="text-center py-5 d-none">
    <div class="empty-state">
        <i class="fas fa-search fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">Aucun résultat trouvé</h4>
        <p class="text-muted">Aucune facture ne correspond à votre recherche</p>
        <button id="resetSearch" class="btn btn-primary mt-3">
            <i class="fas fa-redo me-1"></i>Réinitialiser la recherche
        </button>
    </div>
</div>
@endsection

@section('styles')
<style>
.hover-shadow {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    border-left-color: #667eea;
}

.empty-state {
    padding: 3rem 1rem;
}

.transition-all {
    transition: all 0.3s ease;
}

.accordion-button:not(.collapsed) {
    background-color: #f8f9fa !important;
    color: #495057;
    box-shadow: none;
}

.badge {
    font-size: 0.75em;
}

/* Améliorations responsive pour l'en-tête */
@media (max-width: 991.98px) {
    .content-header .row {
        gap: 1rem;
    }
    
    .content-header h1 {
        font-size: 1.5rem;
    }
    
    .content-header .text-muted {
        font-size: 0.9rem;
    }
}

@media (max-width: 575.98px) {
    .input-group {
        flex-wrap: nowrap;
    }
    
    #invoiceSearch {
        min-width: 0;
    }
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('invoiceSearch');
    const clearBtn = document.getElementById('clearSearch');
    const resetBtn = document.getElementById('resetSearch');
    const container = document.getElementById('invoicesContainer');
    const noResults = document.getElementById('noResults');

    if (!search || !container) {
        console.error('Éléments de recherche non trouvés');
        return;
    }

    function filterInvoices() {
        const q = search.value.trim().toLowerCase();
        console.log('Recherche:', q);
        const items = container.querySelectorAll('.invoice-item');
        let visibleCount = 0;

        if (!q) {
            items.forEach(i => {
                i.style.display = '';
                visibleCount++;
            });
            noResults.classList.add('d-none');
            container.style.display = '';
            return;
        }

        items.forEach(i => {
            const ref = i.dataset.reference || '';
            const client = i.dataset.client || '';
            const status = i.dataset.status || '';
            
            console.log('Vérification facture:', {
                reference: ref,
                client: client,
                recherche: q
            });
            
            // Recherche insensible à la casse
            if (ref.includes(q) || client.includes(q) || status.includes(q)) {
                i.style.display = '';
                visibleCount++;
                console.log('Facture trouvée:', ref);
            } else {
                i.style.display = 'none';
            }
        });

        if (visibleCount === 0) {
            container.style.display = 'none';
            noResults.classList.remove('d-none');
            console.log('Aucun résultat trouvé');
        } else {
            container.style.display = '';
            noResults.classList.add('d-none');
            console.log('Résultats trouvés:', visibleCount);
        }
    }

    function resetSearch() {
        search.value = '';
        filterInvoices();
        search.focus();
    }

    // Débogage pour vérifier que les événements sont bien attachés
    console.log('Initialisation de la recherche...');
    console.log('Éléments trouvés:', {
        search: search ? 'Oui' : 'Non',
        container: container ? 'Oui' : 'Non',
        clearBtn: clearBtn ? 'Oui' : 'Non',
        items: container ? container.querySelectorAll('.invoice-item').length : 0
    });

    search.addEventListener('input', filterInvoices);
    
    if (clearBtn) {
        clearBtn.addEventListener('click', resetSearch);
    }
    
    if (resetBtn) {
        resetBtn.addEventListener('click', resetSearch);
    }

    // Recherche par Enter avec focus sur premier résultat
    search.addEventListener('keydown', function(e){
        if(e.key === 'Enter') {
            const first = container.querySelector('.invoice-item:not([style*="display: none"])');
            if (first) {
                window.scrollTo({ 
                    top: first.offsetTop - 120, 
                    behavior: 'smooth' 
                });
                first.style.backgroundColor = '#f8f9fa';
                setTimeout(() => {
                    first.style.backgroundColor = '';
                }, 2000);
            }
        }
    });

    // Test initial
    filterInvoices();
});
</script>
@endsection