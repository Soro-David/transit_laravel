@extends('admin.layouts.admin')

@section('content-header')
{{-- Vous pouvez garder ou enlever signature_pad si non utilisé sur CETTE page --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/4.0.0/signature_pad.min.js"></script> --}}
@endsection

@section('content')
<section class="p-4 mx-auto">
    <div class="form-container text-center">
        <div class="row d-flex justify-content-between">
            <!-- Informations Colis -->
            <div class="col-md-5">
                <div class="card border-0 rounded shadow-sm mb-3">
                    <div>
                        <h4 class="card-title text-center mb-3 fw-bold">Informations des colis</h4><br>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3 d-flex align-items-center">
                           <h3>REF COLIS: {{ $first['reference_colis'] ?? 'N/A' }}</h3>
                        </div>
                        <div class="mb-3 d-flex align-items-center">
                            <label class="form-label fw-bold w-50">Prix Total :</label>
                            <span class="form-control border-0 bg-light w-50"> {{ $totalPrixTransit ?? 'N/A' }}</span>
                        </div>
                        <div class="mb-3 d-flex align-items-center">
                            <label class="form-label fw-bold w-50">Payé :</label>
                            <span class="form-control border-0 bg-light w-50"> {{ $first['montant_paye'] ?? $totalPrixTransit ?? 'N/A' }}</span>
                        </div>
                        <div class="mb-3 d-flex align-items-center">
                            <label class="form-label fw-bold w-50">Reste :</label>
                            <span class="form-control border-0 bg-light w-50"> {{ $first['reste'] ?? '0' }} F</span>
                        </div>
                    </div>
                </div>
            </div>

             <div class="col-md-5">
                <div class="card border-0 rounded shadow-sm mb-3"> {{-- Ajout mb-3 --}}
                    <div>
                        <h4 class="card-title text-center mb-3 fw-bold">Informations de l'expéditeur</h4><br>
                    </div>
                     <div class="card-body p-4">
                        <div class="mb-3 d-flex align-items-center">
                            <label class="form-label fw-bold w-50">Nom :</label>
                            <span class="form-control border-0 bg-light w-50">{{ $first['nom_expediteur'] ?? 'N/A' }}</span>
                        </div>
                        <div class="mb-3 d-flex align-items-center">
                            <label class="form-label fw-bold w-50">Prénom :</label>
                            <span class="form-control border-0 bg-light w-50">{{ $first['prenom_expediteur'] ?? 'N/A' }}</span>
                        </div>
                        <div class="mb-3 d-flex align-items-center">
                            <label class="form-label fw-bold w-50">Téléphone :</label>
                            <span class="form-control border-0 bg-light w-50">{{ $first['tel_expediteur'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                 <div class="card border-0 rounded shadow-sm mb-3"> {{-- Ajout mb-3 --}}
                    <div>
                        <h4 class="card-title text-center mb-3 fw-bold">Informations du destinataire</h4><br>
                    </div>
                   <div class="card-body p-4">
                        <div class="mb-3 d-flex align-items-center">
                            <label class="form-label fw-bold w-50">Nom :</label>
                            <span class="form-control border-0 bg-light w-50">{{ $first['nom_destinataire'] ?? 'N/A' }}</span>
                        </div>
                        <div class="mb-3 d-flex align-items-center">
                            <label class="form-label fw-bold w-50">Prénom :</label>
                            <span class="form-control border-0 bg-light w-50">{{ $first['prenom_destinataire'] ?? 'N/A' }}</span>
                        </div>
                        <div class="mb-3 d-flex align-items-center">
                            <label class="form-label fw-bold w-50">Téléphone:</label>
                            <span class="form-control border-0 bg-light w-50">{{ $first['tel_destinataire'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div> 

        @if(isset($colis) && !empty($colis) && isset($colis[0]))
            <input type="hidden" id="id" value="{{ $colis[0]->id }}">
        @else
            <p class="text-danger mt-3">Erreur : Impossible de récupérer l'ID du colis pour l'impression.</p>
        @endif

        <div class="d-flex justify-content-center align-items-center gap-3 mt-4">
            <a href="javascript:history.back()" class="btn btn-secondary d-flex align-items-center">
                <i class="fas fa-arrow-left me-2" style="font-size: 18px;"></i> Retour
            </a>
            @if(isset($colis) && !empty($colis) && isset($colis[0]))
                <a href="javascript:void(0)" id="imprimer-etiquette" class="btn btn-success">
                    Imprimer l'étiquette
                </a>
                <a href="javascript:void(0)" id="imprimer-facture" class="btn btn-success" style="background-color: #90EE90; border-color: #90EE90; color: #fff;">
                    Imprimer la facture
                </a>
            @endif
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnEtiquette = document.getElementById('imprimer-etiquette');
        const btnFacture = document.getElementById('imprimer-facture');
        const colisIdInput = document.getElementById('id'); 

        if (btnEtiquette && colisIdInput) {
            btnEtiquette.addEventListener('click', function() {
                const colisId = colisIdInput.value; 
                if (colisId) {
                    console.log('ID du colis pour étiquette :', colisId);
                    window.location.href = `{{ route('colis.edit.etiquette', '') }}/${colisId}`;
                } else {
                    console.error("ID du colis non trouvé pour l'étiquette.");
                    alert("Erreur : L'ID du colis est manquant.");
                }
            });
        }

        if (btnFacture && colisIdInput) {
            btnFacture.addEventListener('click', function() {
                const colisId = colisIdInput.value; 
                 if (colisId) {
                    console.log('ID du colis pour facture :', colisId);
                    window.location.href = `{{ route('colis.edit.facture', '') }}/${colisId}`;
                } else {
                    console.error("ID du colis non trouvé pour la facture.");
                    alert("Erreur : L'ID du colis est manquant.");
                }
            });
        }
    });
</script>

<style>
    .form-container {
        max-width: 95%; 
        margin: auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    body {
        background-color: #f7f7f7;
    }
    .card {
        border-radius: 10px;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem; 
    }
    .form-control {
        border: none; 
        background-color: #f8f9fa; 
        padding: .375rem .75rem; 
        border-radius: .25rem; 
    }
    .form-label {
        margin-bottom: 0; 
    }
    .w-50 {
        flex-basis: 50%;
    }
    .d-flex.align-items-center .form-label {
        padding-right: 10px; 
    }
</style>
@endsection