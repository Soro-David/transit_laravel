@extends('customer.layouts.index')

@section('content-header')
<div class="container-fluid">
    <!-- Conteneur principal pour le titre et la description centrés -->
    <div class="d-flex flex-column align-items-center mb-4">
        <!-- Titre principal -->
        <h1 class="h2 fw-bold text-primary mb-2" style="max-width: 500px; position:relative;left: 300px;">Mes Factures</h1>
        
        <!-- Description -->
        <p class="mb-0 text-muted text-center" style="max-width: 500px; position:relative;left: 300px;">
            Gérez et consultez l'ensemble de vos Facture
        </p>
    </div>

    <!-- Barre de recherche complètement à droite -->
    <div class="row justify-content-end">
        <div class="col-auto">
            <div class="search-container" style="min-width: 400px;position:relative;left: 650px;">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0 py-2 px-3">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input id="invoiceSearch" type="search" class="form-control border-start-0 py-2" 
                           placeholder="Référence, nom du produit, service, client..." 
                           aria-label="Rechercher une facture">
                    <button id="clearSearch" class="btn btn-outline-secondary border-start-0 py-2" type="button" 
                            title="Effacer la recherche">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <!-- Indicateur de recherche en temps réel -->
                <div id="searchInfo" class="small text-muted mt-1 text-end" style="display: none;">
                    <span id="resultCount">0</span> résultat(s) trouvé(s)
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
            <i class="fas fa-boxes fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Aucun colis validé</h4>
            <p class="text-muted">Vos colis validés apparaîtront ici</p>
        </div>
    </div>
@else
            <div id="invoicesContainer" class="p-3">
                @foreach($devisList as $devis)
                @php
                    $reference = $devis->reference ?? 'N/A';
                    $clientName = trim(($devis->nom_expediteur ?? '') . ' ' . ($devis->prenom_expediteur ?? ''));
                    $service = $devis->devisItems->first()->service ?? 'Non spécifié';
                    $colisCount = $devis->colis_count ?? $devis->devisItems->count();
                @endphp
     <div class="invoice-item card mb-4 border-0 shadow-sm hover-shadow transition-all" 
     data-reference="{{ strtolower($reference) }}" 
     data-client="{{ strtolower($clientName) }}"
     data-service="{{ strtolower($service) }}"
     data-status="validé">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h6 class="mb-0">
                    <i class="fas fa-hashtag text-primary me-2"></i>
                    <strong class="text-dark">{{ $reference }}</strong>
                    <span class="badge bg-info ms-2">{{ $colisCount }} colis</span>
                </h6>
            </div>
            <div class="col-md-6 text-md-end">
                <span class="badge bg-success">
                    <i class="fas fa-check me-1"></i>Validé
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
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-box text-muted me-2"></i>
                        <strong class="me-2">Service:</strong>
                        <span>{{ $service }}</span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-calendar text-muted me-2"></i>
                        <strong class="me-2">Date:</strong>
                        <span>{{ $devis->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-money-bill text-muted me-2"></i>
                        <strong class="me-2">Montant:</strong>
                        <span>{{ $devis->devise ?? 'XOF' }} {{ number_format($devis->montant ?? 0, 0, ',', ' ') }}</span>
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
                            Voir les détails du colis
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
                <a href="{{ route('customer_colis.facture.pdf', $devis->reference) }}" 
                   class="btn btn-sm btn-outline-primary" target="_blank">
                    <i class="fas fa-download me-1"></i>Télécharger
                </a>
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

/* Styles pour la recherche */
.highlight-match {
    border-left-color: #ffc107 !important;
    background-color: #fffdf6;
}

mark.bg-warning {
    padding: 0.1em 0.2em;
    border-radius: 0.25em;
    font-weight: 600;
}

.search-container {
    position: relative;
}

#searchInfo {
    font-size: 0.8rem;
    opacity: 0.8;
}

/* Animation pour les résultats de recherche */
.invoice-item {
    transition: all 0.3s ease-in-out;
}

/* Améliorations responsive */
@media (max-width: 768px) {
    .search-container {
        min-width: 100% !important;
    }
    
    .content-header h1 {
        font-size: 1.5rem;
        text-align: center;
    }
    
    .content-header .text-muted {
        font-size: 0.9rem;
        text-align: center;
    }
}

@media (max-width: 575.98px) {
    .input-group {
        flex-wrap: nowrap;
    }
    
    #invoiceSearch {
        min-width: 0;
        font-size: 0.9rem;
    }
    
    .input-group-text {
        padding: 0.5rem 0.75rem;
    }
}

/* Style pour le focus de la recherche */
#invoiceSearch:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
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
    const searchInfo = document.getElementById('searchInfo');
    const resultCount = document.getElementById('resultCount');

    if (!search || !container) {
        console.error('Éléments de recherche non trouvés');
        return;
    }

    function filterInvoices() {
        const q = search.value.trim().toLowerCase();
        const items = container.querySelectorAll('.invoice-item');
        let visibleCount = 0;

        if (!q) {
            // Afficher tous les éléments si la recherche est vide
            items.forEach(i => {
                i.style.display = '';
                i.classList.remove('highlight-match');
                visibleCount++;
            });
            noResults.classList.add('d-none');
            container.style.display = '';
            searchInfo.style.display = 'none';
            return;
        }

        items.forEach(i => {
            const ref = i.dataset.reference || '';
            const client = i.dataset.client || '';
            const service = i.dataset.service || '';
            const status = i.dataset.status || '';
            
            // Recherche dans tous les champs
            const matches = ref.includes(q) || 
                          client.includes(q) || 
                          service.includes(q) || 
                          status.includes(q);
            
            if (matches) {
                i.style.display = '';
                i.classList.add('highlight-match');
                visibleCount++;
                
                // Mettre en évidence le texte correspondant
                highlightMatches(i, q);
            } else {
                i.style.display = 'none';
                i.classList.remove('highlight-match');
            }
        });

        // Mettre à jour l'interface en fonction des résultats
        if (visibleCount === 0) {
            container.style.display = 'none';
            noResults.classList.remove('d-none');
        } else {
            container.style.display = '';
            noResults.classList.add('d-none');
        }

        // Afficher le compteur de résultats
        searchInfo.style.display = 'block';
        resultCount.textContent = visibleCount;
    }

    function highlightMatches(element, searchTerm) {
        // Ne pas surligner si la recherche est trop courte
        if (searchTerm.length < 2) return;

        const elementsToHighlight = element.querySelectorAll([
            '.card-header h6 strong', // Référence
            '.card-body strong', // Labels
            '.card-body span:not(.badge)' // Valeurs
        ].join(','));

        elementsToHighlight.forEach(el => {
            const originalHtml = el.getAttribute('data-original') || el.innerHTML;
            el.setAttribute('data-original', originalHtml);
            
            const regex = new RegExp(`(${escapeRegex(searchTerm)})`, 'gi');
            const highlighted = originalHtml.replace(regex, '<mark class="bg-warning text-dark">$1</mark>');
            el.innerHTML = highlighted;
        });
    }

    function removeHighlights() {
        const highlighted = document.querySelectorAll('.invoice-item [data-original]');
        highlighted.forEach(el => {
            el.innerHTML = el.getAttribute('data-original');
            el.removeAttribute('data-original');
        });
    }

    function escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function resetSearch() {
        search.value = '';
        removeHighlights();
        filterInvoices();
        search.focus();
    }

    // Événements
    search.addEventListener('input', function() {
        // Délai pour éviter trop d'opérations pendant la frappe
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(filterInvoices, 300);
    });

    search.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            resetSearch();
        }
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', resetSearch);
    }
    
    if (resetBtn) {
        resetBtn.addEventListener('click', resetSearch);
    }

    // Recherche rapide avec Enter
    search.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const firstVisible = container.querySelector('.invoice-item:not([style*="display: none"])');
            if (firstVisible) {
                firstVisible.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center'
                });
                
                // Animation de surbrillance
                firstVisible.style.transform = 'scale(1.02)';
                firstVisible.style.boxShadow = '0 0 0 3px rgba(102, 126, 234, 0.3)';
                setTimeout(() => {
                    firstVisible.style.transform = '';
                    firstVisible.style.boxShadow = '';
                }, 2000);
            }
        }
    });

    // Initialisation
    filterInvoices();
});

// Fonction utilitaire pour le debounce
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
</script>
@endsection