@extends('customer.layouts.index')

@section('content')
<div class="container py-4">
    <!-- Modal de succès -->
    @if(session('success_popup'))
    <div class="modal fade show" id="successModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Succès</h5>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                    <h5>{{ session('success_popup') }}</h5>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('customer_colis.devis.show', $devis->reference) }}" class="btn btn-success">OK</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = new bootstrap.Modal(document.getElementById('successModal'));
            modal.show();
            
            // Redirection automatique après 2 secondes
            setTimeout(() => {
                window.location.href = "{{ route('customer_colis.devis.show', $devis->reference) }}";
            }, 2000);
        });
    </script>
    @endif

    <div class="card shadow-lg rounded-3 border-0">
        <div class="card-header" style="background: linear-gradient(90deg,#05a805,#0b7cff); color:#fff;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">Devis : <span style="font-weight:700;">{{ $devis->reference }}</span></h4>
                    <small class="text-light">Dernière mise à jour : {{ $devis->updated_at->format('d/m/Y H:i') }}</small>
                </div>
                <div class="text-end">
                    <a href="{{ route('customer_colis.devis.hold') }}" class="btn btn-light btn-sm">← Retour</a>
                    <a href="{{ route('customer_colis.devis.edit', $devis->reference) }}" class="btn btn-light btn-sm">Modifier</a>
                </div>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 rounded" style="background:#f6fff6; border-left:4px solid #05a805;">
                            <strong>État</strong>
                            <div class="mt-2">
                                <span class="badge bg-{{ $devis->etat === 'validé' ? 'success' : ($devis->etat === 'en attente' ? 'warning text-dark' : 'secondary') }}">
                                    {{ ucfirst($devis->etat) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-3 rounded" style="background:#f0f8ff; border-left:4px solid #0b7cff;">
                            <strong>Expéditeur</strong>
                            <div class="mt-2">{{ $devis->nom_expediteur }} {{ $devis->prenom_expediteur }}</div>
                            <div class="small text-muted mt-1">{{ $devis->tel_expediteur ?? '' }}</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-3 rounded" style="background:#fff8f0; border-left:4px solid #f59e0b;">
                            <strong>Agence expédition</strong>
                            <div class="mt-2">{{ $devis->agence_expedition }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="fw-bold mb-3">📦 Items</h5>
            
            @php
                $groupedItems = $devis->items->groupBy('service');
            @endphp
            
            @foreach($groupedItems as $serviceName => $items)
            <div class="mb-4 border rounded p-3">
                <h6 class="fw-bold text-primary mb-3">
                    <i class="fas fa-box me-2"></i>Produit / Service : {{ $serviceName }}
                </h6>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Quantité</th>
                                <th>Type</th>
                                <th>Valeur</th>
                                <th>Description</th>
                                @if($devis->mode_transit === 'aerien')
                                <th>Poids</th>
                                @else
                                <th>Dimensions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td>{{ $item->quantite_colis }}</td>
                                <td>
                                    <span class="badge bg-{{ $item->type_colis === 'standard' ? 'primary' : 'warning' }}">
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
                                <td>{{ $item->description_colis ?? 'Non spécifié' }}</td>
                                <td>
                                    @if($devis->mode_transit === 'aerien')
                                        {{ $item->poids ? $item->poids . ' kg' : 'Non spécifié' }}
                                    @else
                                        @if($item->longueur && $item->largeur && $item->hauteur)
                                            {{ $item->longueur }}x{{ $item->largeur }}x{{ $item->hauteur }} cm
                                        @else
                                            Non spécifié
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach

            <div class="mt-4 d-flex justify-content-between">
                <a href="{{ route('customer_colis.devis.hold') }}" class="btn btn-light btn-sm">← Retour à la liste</a>
                <a href="{{ route('customer_colis.devis.edit', $devis->reference) }}" class="btn btn-success">Modifier le devis</a>
            </div>
        </div>
    </div>
</div>

<style>
    .card-header h4 { font-weight:700; }
    .badge { font-size:0.95rem; padding:0.45rem 0.6rem; }
</style>
@endsection