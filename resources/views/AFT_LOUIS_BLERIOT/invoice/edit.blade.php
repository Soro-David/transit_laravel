@extends('AFT_LOUIS_BLERIOT.layouts.agent')

@section('content')
<section class="p-4 mx-auto">
    <div class="form-container text-center">
        {{-- Affichage résumé (basé sur $first et totaux) --}}
        <div class="row d-flex justify-content-around mb-4">
            {{-- Carte Récapitulatif Colis (utilisant $totalPrixTransit, $first['montant_paye'], $first['reste']) --}}
            <div class="col-md-5 col-lg-4">
                 <div class="card border-0 rounded shadow-sm">
                     <div class="card-header bg-light border-0">
                         <h4 class="card-title text-center mb-0 fw-bold">Récapitulatif Global</h4>
                     </div>
                     <div class="card-body p-4">
                         <div class="mb-3 d-flex align-items-center">
                            <label class="form-label fw-bold w-50">Réf. Principale:</label> {{-- Référence du premier colis comme réf globale --}}
                            <span class="form-control-plaintext w-50">{{ $first['reference_colis'] ?? 'N/A' }}</span>
                         </div>
                         <div class="mb-3 d-flex align-items-center">
                             <label class="form-label fw-bold w-50">Prix Total :</label>
                             <span class="form-control-plaintext w-50"> {{ number_format($totalPrixTransit ?? 0, 0, ',', ' ') }} F CFA</span>
                         </div>
                         <div class="mb-3 d-flex align-items-center">
                             <label class="form-label fw-bold w-50">Montant Payé :</label>
                             <span class="form-control-plaintext w-50"> {{ number_format($totalMontantPaye ?? 0, 0, ',', ' ') }} F CFA</span>
                         </div>
                         <div class="mb-3 d-flex align-items-center">
                             <label class="form-label fw-bold w-50">Reste à Payer:</label>
                             <span class="form-control-plaintext w-50 fw-bold {{ ($restePaye ?? 0) > 0 ? 'text-danger' : 'text-success' }}"> {{ number_format($restePaye ?? 0, 0, ',', ' ') }} F CFA</span>
                         </div>
                     </div>
                 </div>
             </div>
             {{-- Carte Expediteur (utilise $first) --}}
             <div class="col-md-5 col-lg-4">
                 <div class="card border-0 rounded shadow-sm">
                     <div class="card-header bg-light border-0">
                         <h4 class="card-title text-center mb-0 fw-bold">Expéditeur</h4>
                     </div>
                     <div class="card-body p-4">
                         @if(isset($first))
                         <div class="mb-3 d-flex align-items-center">
                             <label class="form-label fw-bold w-40">Nom :</label>
                             <span class="form-control-plaintext w-60">{{ $first['nom_expediteur'] ?? 'N/A' }} {{ $first['prenom_expediteur'] ?? '' }}</span>
                         </div>
                         <div class="mb-3 d-flex align-items-center">
                             <label class="form-label fw-bold w-40">Téléphone :</label>
                             <span class="form-control-plaintext w-60">{{ $first['tel_expediteur'] ?? 'N/A' }}</span>
                         </div>
                         @else
                          <p class="text-muted">Aucune information d'expéditeur.</p>
                         @endif
                     </div>
                 </div>
             </div>
            {{-- Carte Destinataire (utilise $first) --}}
             <div class="col-md-5 col-lg-4">
                  <div class="card border-0 rounded shadow-sm">
                     <div class="card-header bg-light border-0">
                         <h4 class="card-title text-center mb-0 fw-bold">Destinataire</h4>
                     </div>
                    <div class="card-body p-4">
                         @if(isset($first))
                         <div class="mb-3 d-flex align-items-center">
                             <label class="form-label fw-bold w-40">Nom :</label>
                             <span class="form-control-plaintext w-60">{{ $first['nom_destinataire'] ?? 'N/A' }} {{ $first['prenom_destinataire'] ?? '' }}</span>
                         </div>
                         <div class="mb-3 d-flex align-items-center">
                             <label class="form-label fw-bold w-40">Téléphone:</label>
                             <span class="form-control-plaintext w-60">{{ $first['tel_destinataire'] ?? 'N/A' }}</span>
                         </div>
                          @else
                           <p class="text-muted">Aucune information de destinataire.</p>
                          @endif
                     </div>
                 </div>
             </div>
        </div>

        <hr> {{-- Séparateur visuel --}}

        {{-- Liste détaillée des colis enregistrés avec leurs boutons --}}
        <div>
            {{-- @dd($firstColis->first()->id) --}}
            <a href="{{ route('aftlb_colis.imprimer.facture', ['id' => $premierColis->id ?? 0]) }}" target="_blank" class="btn btn-sm btn-info">
                <i class="fas fa-file-invoice me-1"></i> Imprimer Facture
            </a>            
        </div>
        {{-- <h4 class="mb-3">Colis Enregistrés dans cette Transaction</h4> --}}
        @if(!empty($colisEnregistres))
            <div class="list-group">
                @foreach($colisEnregistres as $index => $colis)
                    <div class="list-group-item list-group-item-action flex-column align-items-start mb-3 shadow-sm rounded border-0">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">
                                <span class="badge bg-secondary me-2">{{ $index + 1 }}</span>
                                Colis Réf: {{ $colis->reference_colis }}
                            </h5>
                            <small>ID: {{ $colis->id }}</small>
                        </div>
                        <p class="mb-1">
                            Type: {{ $colis->type_colis ?? 'N/A' }} |
                            Qté: <span class="fw-bold">{{ $colis->quantite_colis }}</span> |
                            Prix: {{ number_format($colis->prix_transit_colis ?? 0, 0, ',', ' ') }} F CFA |
                        </p>
                        <div class="mt-2 text-end"> {{-- Boutons alignés à droite --}}
                            <a href="{{ route('aftlb_colis.imprimer.etiquette', ['id' => $colis->id]) }}" target="_blank" class="btn btn-sm btn-success me-2">
                                <i class="fas fa-tags me-1"></i> Imprimer {{ $colis->quantite_colis }} Étiquette(s)
                            </a>
                        
                            {{-- Ajouter d'autres boutons si nécessaire (modifier, etc.) --}}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-danger mt-3">Aucun colis spécifique n'a été enregistré lors de cette transaction.</p>
        @endif

        {{-- Boutons d'action généraux --}}
        <div class="d-flex justify-content-center align-items-center gap-3 mt-4">
            <a href="{{ url()->previous() }}" class="btn btn-secondary d-flex align-items-center">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
        </div>
    </div>
</section>


<style>
    /* ... (garder les styles existants) ... */
    .list-group-item {
        background-color: #f8f9fa; /* Fond léger pour les items */
        border: 1px solid #dee2e6; /* Bordure subtile */
    }
    .list-group-item h5 {
        color: #0d6efd; /* Couleur titre */
    }
    .btn-sm i {
         font-size: 0.8rem; /* Icones plus petites pour boutons sm */
    }
</style>
@endsection