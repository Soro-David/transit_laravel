@extends('AFT_LOUIS_BLERIOT.layouts.agent')
@section('content')
<section class="p-4 mx-auto">
    <div class="all-forms-container">
        {{-- On utilise un seul formulaire global qui englobe tout --}}
        <form id="update-all-form" action="{{ route('aftlb_colis.valide.update') }}" method="POST">
            @csrf
            @method('PUT')

            {{-- On boucle sur chaque groupe de colis (par service/description) --}}
            @foreach ($colis_recap_list as $index => $colis)
                <div class="form-container">
                    <div class="form-section">
                        {{-- On affiche les infos expéditeur/destinataire seulement pour le premier formulaire --}}
                        @if($loop->first)
                            <h4>Information de l'expéditeur</h4><hr>
                            <div class="row">
                                <div class="col-md-3"><label>Nom Expediteur</label><input type="text" name="nom_expediteur" value="{{ $colis->expediteur->nom ?? '' }}" class="form-control" required></div>
                                <div class="col-md-3"><label>Prénom Expediteur</label><input type="text" name="prenom_expediteur" value="{{ $colis->expediteur->prenom ?? '' }}" class="form-control" required></div>
                                <div class="col-md-3"><label>Contact expéditeur</label><input type="text" name="tel_expediteur" value="{{ $colis->expediteur->tel ?? '' }}" class="form-control" required></div>
                                <div class="col-md-3"><label>Agence expédition</label><input type="text" name="agence_expediteur" value="{{ $colis->expediteur->agence ?? '' }}" class="form-control" required></div>
                            </div>

                            <h4 class="mt-4">Information destinataire</h4><hr>
                            <div class="row">
                                <div class="col-md-3"><label>Nom destinataire</label><input type="text" name="nom_destinataire" value="{{ $colis->destinataire->nom ?? '' }}" class="form-control" required></div>
                                <div class="col-md-3"><label>Prénom destinataire</label><input type="text" name="prenom_destinataire" value="{{ $colis->destinataire->prenom ?? '' }}" class="form-control" required></div>
                                <div class="col-md-3"><label>Contact destinataire</label><input type="text" name="tel_destinataire" value="{{ $colis->destinataire->tel ?? '' }}" class="form-control" required></div>
                                <div class="col-md-3"><label>Agence de destination</label><input type="text" name="agence_destinataire" value="{{ $colis->destinataire->agence ?? '' }}" class="form-control" required></div>
                            </div>
                        @endif

                        <h4 class="mt-4">Information colis</h4><hr>
                        
                        {{-- On passe les IDs de ce sous-groupe en hidden --}}
                        @foreach ($colis->items as $colisItem)
                            <input type="hidden" name="groupes[{{ $index }}][colis_ids][]" value="{{ $colisItem->id }}">
                        @endforeach

                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Nature du Colis</label>
                                <input type="text" name="groupes[{{ $index }}][service]" value="{{ $colis->service }}" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Quantité de colis</label>
                                <input type="text" value="{{ $colis->quantite_colis }}" class="form-control" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Valeur totale</label>
                                <input type="text" value="{{ $colis->valeur_colis }}" class="form-control" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Mode de transit</label>
                                <input type="text" value="{{ $colis->mode_transit ?? '' }}" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label class="form-label">Référence colis</label>
                                <input type="text" value="{{ $colis->reference_colis ?? '' }}" class="form-control" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Poids total (Kg)</label>
                                <input type="text" value="{{ $colis->poids_colis ?? '' }}" class="form-control" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Dimensions</label>
                                <input type="text" value="{{ $colis->dimension_result ?? '' }}" class="form-control" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Prix du Groupe</label>
                                <input type="number" step="0.01" name="groupes[{{ $index }}][prix_transit_colis]" value="{{ $colis->prix_transit_colis ?? '' }}" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </form>
        <div class="d-flex justify-content-center gap-2 mt-4">
            <a href="javascript:history.back()" class="btn btn-secondary">Retour</a>
            <button type="button" class="btn btn-primary" id="validate-all-btn">Valider</button>
        </div>
    </div>
</section>
{{-- Script SweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('validate-all-btn').addEventListener('click', function() {
            Swal.fire({
                title: 'Confirmer la mise à jour de tous les colis',
                text: "Êtes-vous sûr de vouloir mettre à jour le prix de transit de tous les colis avec cette référence?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "Oui, j'accepte",
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Soumet le formulaire
                    document.getElementById('update-all-form').submit();
                }
            });
        });
    });
</script>

{{-- CSS Personnalisé --}}
<style>
    body {
        background-color: #f7f7f7;
    }

    .all-forms-container {
        max-width: 95%;
        margin: auto;
    }

    .form-container {
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px; /* Espacement entre les formulaires */
    }
</style>
@endsection
