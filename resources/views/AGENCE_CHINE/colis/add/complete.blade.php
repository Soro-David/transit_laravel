@extends('AGENCE_CHINE.layouts.agentprint')

@section('content')
<section class="p-4 mx-auto">
     @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Le message spécial (avertissement) s'affichera ici --}}
        @if(isset($specialMessage) && !empty($specialMessage))
            <div class="alert alert-warning fw-bold">
                {{ $specialMessage }}
            </div>
        @endif
    <div class="form-container text-center">
        {{-- Affichage résumé (basé sur $first et totaux) --}}
        <div class="row d-flex justify-content-around mb-4">
            {{-- Carte Récapitulatif Colis --}}
            <div class="col-md-5 col-lg-4 mb-3">
                 <div class="card border-0 rounded shadow-sm">
                     <div class="card-header bg-light border-0">
                         <h4 class="card-title text-center mb-0 fw-bold">Récapitulatif Global</h4>
                     </div>
                     <div class="card-body p-4">
                         <div class="mb-3 d-flex align-items-center">
                            <label class="form-label fw-bold w-50 text-start">Réf. Principale:</label>
                            <span class="form-control-plaintext w-50">{{ $first['reference_colis'] ?? 'N/A' }}</span>
                         </div>
                         <div class="mb-3 d-flex align-items-center">
                             <label class="form-label fw-bold w-50 text-start">Prix Total :</label>
                             <span class="form-control-plaintext w-50"> {{ number_format($totalPrixTransit ?? 0, 0, ',', ' ') }} {{ $first['devise'] ?? 'FCFA ' }}</span>
                         </div>
                         <div class="mb-3 d-flex align-items-center">
                             <label class="form-label fw-bold w-50 text-start">Montant Payé :</label>
                             <span class="form-control-plaintext w-50"> {{ number_format($totalMontantPaye ?? 0, 0, ',', ' ') }} {{ $first['devise'] ?? 'FCFA' }}</span>
                         </div>
                         <div class="mb-3 d-flex align-items-center">
                             <label class="form-label fw-bold w-50 text-start">Reste à Payer:</label>
                             <span class="form-control-plaintext w-50 fw-bold {{ ($restePaye ?? 0) > 0 ? 'text-danger' : 'text-success' }}"> {{ number_format($restePaye ?? 0, 0, ',', ' ') }} {{ $first['devise'] ?? 'FCFA' }}</span>
                         </div>
                     </div>
                 </div>
             </div>
             {{-- Carte Expediteur --}}
            <div class="col-md-5 col-lg-4 mb-3">
                <div class="card border-0 rounded shadow-sm">
                    <div class="card-header bg-light border-0">
                        <h4 class="card-title text-center mb-0 fw-bold">Expéditeur</h4>
                    </div>
                    <div class="card-body p-4">
                        @if(isset($first))
                        <div class="mb-3 d-flex align-items-center">
                            <label class="form-label fw-bold w-40 text-start">Nom :</label>
                            <span class="form-control-plaintext w-60">
                                {{ $first['nom_expediteur'] ?? 'N/A' }}
                                {{ $first['prenom_expediteur'] ?? '' }}
                            </span>
                        </div>
                        <div class="mb-3 d-flex align-items-center">
                            <label class="form-label fw-bold w-40 text-start">Téléphone :</label>
                            <span class="form-control-plaintext w-60">
                                {{ $first['tel_expediteur'] ?? 'N/A' }}
                            </span>
                        </div>
                        @else
                            <p class="text-muted">Aucune information d'expéditeur.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Carte Destinataire --}}
            <div class="col-md-5 col-lg-4 mb-3">
                <div class="card border-0 rounded shadow-sm">
                    <div class="card-header bg-light border-0">
                        <h4 class="card-title text-center mb-0 fw-bold">Destinataire</h4>
                    </div>
                    <div class="card-body p-4">
                        @if(isset($first))
                        <div class="mb-3 d-flex align-items-center">
                            <label class="form-label fw-bold w-40 text-start">Nom :</label>
                            <span class="form-control-plaintext w-60">
                                {{ $first['nom_destinataire'] ?? 'N/A' }}
                                {{ $first['prenom_destinataire'] ?? '' }}
                            </span>
                        </div>
                        <div class="mb-3 d-flex align-items-center">
                            <label class="form-label fw-bold w-40 text-start">Téléphone :</label>
                            <span class="form-control-plaintext w-60">
                                {{ $first['tel_destinataire'] ?? 'N/A' }}
                            </span>
                        </div>
                        @else
                            <p class="text-muted">Aucune information de destinataire.</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <hr>

        <div class="d-flex align-items-center gap-2">
            <div>
                <a href="{{ route('chine_colis.imprimer.facture', ['id' => $first['id']]) }}" target="_blank" class="btn btn-sm btn-info">
                    <i class="fas fa-file-invoice me-1"></i> Imprimer Facture
                </a>            
            </div>
            <div>
                <a href="{{ route('chine_colis.imprimer.bon_livraison', ['id' => $first['id']]) }}" target="_blank" class="btn btn-sm btn-info">
                    <i class="fas fa-file-invoice me-1"></i> Imprimer Bon de Livraison
                </a>            
            </div>
        </div>
            
        @if(isset($colis) && $colis->isNotEmpty()) 
        <div class="list-group mt-3">
            <div class="list-group-item list-group-item-action flex-column align-items-start mb-3 shadow-sm rounded border-0">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">
                        Colis Réf: {{ $colis->first()->reference_colis }}
                    </h5>
                </div>
                <p class="mb-1">
                    Qté d'étiquettes à imprimer: <span class="fw-bold">{{ $totalQuantite ?? $colis->count() }}</span> |
                    Prix Total Groupe: {{ number_format($totalPrixTransit ?? 0, 0, ',', ' ') }} {{ $first['devise'] ?? ' ' }}
                </p>
                <div class="mt-2 text-end">
                    <a href="{{ route('chine_colis.imprimer.etiquette', ['id' => $colis->first()->id]) }}" target="_blank" class="btn btn-sm btn-success me-2">
                        <i class="fas fa-tags me-1"></i> Imprimer {{ $totalQuantite ?? $colis->count() }} Étiquette(s)
                    </a>
                </div>
            </div>
        </div>
        @else
            <p class="text-danger mt-3">Aucun colis spécifique n'a été enregistré pour cette transaction.</p>
        @endif
    </div>
</section>

<style>
    .list-group-item {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }
    .list-group-item h5 {
        color: #0d6efd;
    }
    .btn-sm i {
         font-size: 0.8rem;
    }
</style>
@endsection