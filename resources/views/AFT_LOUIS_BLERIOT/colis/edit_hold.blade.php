@extends('AFT_LOUIS_BLERIOT.layouts.agent')
@section('content-header')
    {{-- <h2>Création de Colis</h2> --}}
@endsection

@section('content')
<section class="p-4 mx-auto">
    <form id="update-form" action="{{ route('aftlb_colis.hold.update', ['id' => $colis->id]) }}" method="POST" class="form-container">
        @csrf
        @method('PUT')
        <div class="form-section">
            <div class="row">
                <h4>Information destinateur & expéditeur</h4><hr>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Agence d'expédition</label>
                        <input type="text" name="destinataire_agence" id="destinataire_agence"
                         value="{{ $colis->destinataire->agence }}" class="form-control" 
                         disabled required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Contact expéditeur</label>
                        <input type="text" name="destinataire_tel" id="destinataire_tel" 
                        value="{{ $colis->destinataire->tel }}" class="form-control" 
                        disabled required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="destinataire_agence" class="form-label">Agence de destination</label>
                        <input type="text" name="destinataire_agence" id="destinataire_agence"
                        value="{{ $colis->destinataire->agence }}" class="form-control" 
                         disabled>
                    </div>
                </div>
            </div>
            <div class="row">
                <h4>Information colis</h4><hr>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Quantité de colis</label>
                        <input type="text" name="quantite_colis" id="quantite_colis"
                        value="{{ $colis->quantite_colis }}" class="form-control" 
                         disabled required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Valeur du Colis</label>
                        <input type="text" name="valeur_colis" id="valeur_colis"
                        value="{{ $colis->valeur_colis }}" class="form-control" disabled  
                         required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="mode_transit" class="form-label">Mode de transit</label>
                        <input type="text" name="mode_transit" 
                        id="mode_transit" value="{{ $colis->mode_transit }}" class="form-control" 
                        disabled>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="description" class="form-label">Description du colis</label>
                        <textarea name="description" id="" cols="5" rows="5" class="form-control" 
                        disabled>{{ $colis->description_colis }}</textarea>
                    </div>
                </div>                
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Poids du Colis</label>
                        <input type="text" name="poids_colis" id="poids_colis"
                        value="{{ $colis->poids_colis }}" class="form-control" 
                         disabled required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="prix_transit_colis" class="form-label">Prix du Colis</label>
                        <input 
                            type="number" 
                            name="prix_transit_colis" 
                            id="prix_transit_colis" 
                            value="{{ $colis->prix_transit_colis ?? '' }}" 
                            class="form-control" 
                            placeholder="Somme en CFA" 
                            required>
                    </div>
                </div>                
            </div>
        </div>
        <div class="d-flex justify-content-start gap-2 mt-4">
            <!-- Bouton Retour -->
            <a href="" class="btn btn-secondary">
                <i class="fas fa-arrow-left" style="font-size: 18px; margin-right: 5px;"></i> Retour
            </a>
            <!-- Bouton Mise à jour -->
            <button type="button" id="validate-btn" class="btn btn-primary">Valider</button>
        </div>
    </form>
</section>

{{-- Script SweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('validate-btn').addEventListener('click', function () {
        Swal.fire({
            title: 'Confirmer la mise à jour',
            text: "Êtes-vous sûr de vouloir mettre à jour le prix de transit du colis ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "Oui, j'accepte",
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                // Soumet le formulaire si l'utilisateur confirme
                document.getElementById('update-form').submit();
            }
        });
    });
</script>

{{-- CSS Personnalisé --}}
<style>
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
</style>
@endsection
