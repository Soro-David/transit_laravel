@extends('agent.layouts.agent')
@section('content-header')
    {{-- <h2>Création de Colis</h2> --}}
@endsection

@section('content')
<section class="p-4 mx-auto">
    <form id="update-form" action="{{ route('agent_colis.valide.update', ['id' => $colis->id]) }}" method="POST" class="form-container">
        @csrf
        @method('PUT')
        <div class="form-section">
            <div class="row">
            <div class="row">
                <h4>Information de l'expéditeur</h4><hr>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Nom Expediteur</label>
                        <input type="text" name="nom_expediteur" id="nom_expediteur"
                         value="{{ $colis->expediteur->nom }}" class="form-control" 
                        required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Prénom Expediteur</label>
                        <input type="text" name="prenom_expediteur" id="prenom_expediteur"
                         value="{{ $colis->expediteur->prenom }}" class="form-control" 
                        required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Contact expéditeur</label>
                        <input type="text" name="destinataire_tel" id="destinataire_tel" 
                        value="{{ $colis->expediteur->tel }}" class="form-control" 
                        required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Agence expédition</label>
                        <input type="text" name="agence_expediteur" id="agence_expediteur" 
                        value="{{ $colis->expediteur->agence }}" class="form-control" 
                        required>
                    </div>
                </div>
            </div>
            <div class="row">
                <h4>Information destinataire</h4><hr>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Nom destinataire</label>
                        <input type="text" name="nom_destinataire" id="nom_destinataire"
                         value="{{ $colis->destinataire->nom }}" class="form-control" 
                        required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Prénom destinataire</label>
                        <input type="text" name="prenom_destinataire" id="prenom_destinataire"
                         value="{{ $colis->destinataire->prenom }}" class="form-control" 
                        required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Contact destinataire</label>
                        <input type="text" name="destinataire_tel" id="destinataire_tel" 
                        value="{{ $colis->destinataire->tel }}" class="form-control" 
                        required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Agence de destination</label>
                        <input type="text" name="agence_destinataire" id="agence_destinataire" 
                        value="{{ $colis->destinataire->agence }}" class="form-control" 
                        required>
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
                         required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Valeur du Colis</label>
                        <input type="text" name="valeur_colis" id="valeur_colis"
                        value="{{ $colis->valeur_colis }}" class="form-control"  
                         required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="mode_transit" class="form-label">Mode de transit</label>
                        <input type="text" name="mode_transit" 
                        id="mode_transit" value="{{ $colis->mode_transit }}" class="form-control" 
                        >
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Référence colis</label>
                        <input type="text" name="reference_colis" id="reference_colis"
                         value="{{ $colis->reference_colis }}" class="form-control" 
                         disabled required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Poids du Colis</label>
                        <input type="text" name="poids_colis" id="poids_colis"
                        value="{{ $colis->poids_colis }}" class="form-control" 
                        required>
                    </div>
                </div>
                <div class="col-md-4">
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
            <a href="javascript:history.back()" class="btn btn-secondary">
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
