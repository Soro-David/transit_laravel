@extends('AGENCE_CHINE.layouts.agent')

@section('content-header')
<script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/4.0.0/signature_pad.min.js"></script>
@endsection

@section('content')
<section class="p-4 mx-auto">
    <div class="form-container text-center">
        <div class="row d-flex justify-content-between">
           <!-- Expéditeur à gauche -->
           <div class="col-md-5">
            <div class="card border-0 rounded shadow-sm">
                <div>
                    <h4 class="card-title text-center mb-3 fw-bold">Informations des colis</h4><br>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3 d-flex align-items-center">
                       <h3>REF COLIS: {{  $colis_info->reference_colis }}</h3>
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                        <label class="form-label fw-bold w-50">Prix Total :</label>
                        <span class="form-control border-0 bg-light w-50"> 56346 F</span>
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                        <label class="form-label fw-bold w-50">Payé :</label>
                        <span class="form-control border-0 bg-light w-50">4055</span>
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                        <label class="form-label fw-bold w-50">Reste :</label>
                        <span class="form-control border-0 bg-light w-50">40 F</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-5">
            <div class="card border-0 rounded shadow-sm">
                <div>
                    <h4 class="card-title text-center mb-3 fw-bold">Informations de l'expéditeur</h4><br>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3 d-flex align-items-center">
                        <label class="form-label fw-bold w-50">Nom :</label>
                        <span class="form-control border-0 bg-light w-50">{{ $colis_info->expediteur_nom }}</span>
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                        <label class="form-label fw-bold w-50">Prénom :</label>
                        <span class="form-control border-0 bg-light w-50">{{ $colis_info->expediteur_prenom }}</span>
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                        <label class="form-label fw-bold w-50">Téléphone :</label>
                        <span class="form-control border-0 bg-light w-50">{{ $colis_info->expediteur_tel }}</span>
                    </div>
                </div>
            </div>
        </div>                  
        <!-- Destinataire à droite -->
        <div class="col-md-5">
            <div class="card border-0 rounded shadow-sm">
                <div>
                    <h4 class="card-title text-center mb-3 fw-bold">Informations du destinataire</h4><br>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3 d-flex align-items-center">
                        <label class="form-label fw-bold w-50">Nom :</label>
                        <span class="form-control border-0 bg-light w-50">{{ $colis_info->destinataire_nom }}</span>
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                        <label class="form-label fw-bold w-50">Prénom :</label>
                        <span class="form-control border-0 bg-light w-50">{{ $colis_info->destinataire_prenom }}</span>
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                        <label class="form-label fw-bold w-50">Téléphone:</label>
                        <span class="form-control border-0 bg-light w-50">{{ $colis_info->destinataire_tel }}</span>
                    </div>
                </div>
            </div>
        </div>
             
              <div class="col-md-5">
                <div class="card border-0 rounded shadow-sm">
                    <div>
                        <h4 class="card-title text-center mb-3 fw-bold">Formulaire de Signature</h4><br>
                    </div>
                    <canvas id="signature-pad" width="400" height="200" style="border: 1px solid black;"></canvas>
                    <br>
                    <button id="clear">Effacer</button>
                    <button id="save">Sauvegarder</button>
                
                    <form id="signature-form" action="{{ route('save.signature') }}" method="POST" style="display:none;">
                        @csrf
                        <input type="hidden" name="signature" id="signature">
                    </form>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center align-items-center gap-3 mt-4">
            <a href="javascript:history.back()" class="btn btn-secondary d-flex align-items-center">
                <i class="fas fa-arrow-left me-2" style="font-size: 18px;"></i> Retour
            </a>
            <a href="javascript:void(0)" id="imprimer-etiquette" class="btn btn-success">
                Imprimer l'étiquette
            </a>
            <a href="javascript:void(0)" id="imprimer-facture" class="btn btn-success" style="background-color: #90EE90; border-color: #90EE90; color: #fff;">
                Imprimer la facture
            </a>
        </div>        
    </div>
    <input type="hidden" name="id" id="id" value="{{$colis->first()->id}}">
</section>
<!-- Signature Pad Script -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/2.3.2/signature_pad.min.js"></script>
<script>

document.getElementById('imprimer-etiquette').addEventListener('click', function() {
    const colisId = document.getElementById('id').value;  // Récupère l'ID du colis
    console.log('ID du colis :', colisId);
    // Redirige vers l'URL en utilisant l'ID du colis
    window.location.href = `{{ route('chine_colis.imprimer.etiquette', '') }}/${colisId}`;
});

document.getElementById('imprimer-facture').addEventListener('click', function() {
    const colisId = document.getElementById('id').value;  // Récupère l'ID du colis
    console.log('ID du colis :', colisId);
    // Redirige vers l'URL en utilisant l'ID du colis
    window.location.href = `{{ route('chine_colis.imprimer.facture', '') }}/${colisId}`;
});

    document.getElementById('clear').addEventListener('click', () => {
        signaturePad.clear();
    });

    document.getElementById('save').addEventListener('click', () => {
        const signature = signaturePad.toDataURL();
        document.getElementById('signature').value = signature;
        document.getElementById('signature-form').submit();
    });
</script>

<!-- Style -->
<style>
        .form-container {
        max-width: 600px;
        margin: auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    body {
        background-color: #f7f7f7;
    }
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
    }
    .form-control {
        border: none;
        background-color: transparent;
    }
</style>
@endsection
