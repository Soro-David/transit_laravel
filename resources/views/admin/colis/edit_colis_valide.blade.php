@extends('admin.layouts.admin')

@section('content-header')
    {{-- <h2>Modification des Colis</h2> --}}
@endsection

@section('content')
<section class="p-4 mx-auto">
    <div class="all-forms-container">
        <form id="update-all-form" action="{{ route('colis.valide.update') }}" method="POST">
            @csrf
            @method('PUT')
            @foreach ($colis as $colisItem)
            <div class="form-container">
                <div class="form-section">
                    <div class="row">
                        <div class="row">
                            <h4>Information de l'expéditeur </h4><hr>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Nom Expediteur</label>
                                    <input type="text" name="colis[{{ $colisItem->id }}][nom_expediteur]" id="nom_expediteur"
                                     value="{{ $colisItem->expediteur->nom ?? '' }}" class="form-control"
                                    required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Prénom Expediteur</label>
                                    <input type="text" name="colis[{{ $colisItem->id }}][prenom_expediteur]" id="prenom_expediteur"
                                     value="{{ $colisItem->expediteur->prenom ?? '' }}" class="form-control"
                                    required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Contact expéditeur</label>
                                    <input type="text" name="colis[{{ $colisItem->id }}][tel_expediteur]" id="tel_expediteur"
                                    value="{{ $colisItem->expediteur->tel ?? '' }}" class="form-control"
                                    required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Agence expédition</label>
                                    <input type="text" name="colis[{{ $colisItem->id }}][agence_expediteur]" id="agence_expediteur"
                                    value="{{ $colisItem->expediteur->agence ?? '' }}" class="form-control"
                                    required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <h4>Information destinataire</h4><hr>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Nom destinataire</label>
                                    <input type="text" name="colis[{{ $colisItem->id }}][nom_destinataire]" id="nom_destinataire"
                                     value="{{ $colisItem->destinataire->nom ?? '' }}" class="form-control"
                                    required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Prénom destinataire</label>
                                    <input type="text" name="colis[{{ $colisItem->id }}][prenom_destinataire]" id="prenom_destinataire"
                                     value="{{ $colisItem->destinataire->prenom ?? '' }}" class="form-control"
                                    required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Contact destinataire</label>
                                    <input type="text" name="colis[{{ $colisItem->id }}][tel_destinataire]" id="tel_destinataire"
                                    value="{{ $colisItem->destinataire->tel ?? '' }}" class="form-control"
                                    required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Agence de destination</label>
                                    <input type="text" name="colis[{{ $colisItem->id }}][agence_destinataire]" id="agence_destinataire"
                                    value="{{ $colisItem->destinataire->agence ?? '' }}" class="form-control"
                                    required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <h4>Information colis</h4><hr>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Quantité de colis</label>
                                    <input type="text" name="colis[{{ $colisItem->id }}][quantite_colis]" id="quantite_colis"
                                    value="{{ $colisItem->quantite_colis ?? '' }}" class="form-control"
                                     required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Valeur du Colis</label>
                                    <input type="text" name="colis[{{ $colisItem->id }}][valeur_colis]" id="valeur_colis"
                                    value="{{ $colisItem->valeur_colis ?? '' }}" class="form-control"
                                     required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="mode_transit" class="form-label">Mode de transit</label>
                                    <input type="text" name="colis[{{ $colisItem->id }}][mode_transit]"
                                    id="mode_transit" value="{{ $colisItem->mode_transit ?? '' }}" class="form-control"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Référence colis</label>
                                    <input type="text" name="colis[{{ $colisItem->id }}][reference_colis]" id="reference_colis"
                                     value="{{ $colisItem->reference_colis ?? '' }}" class="form-control"
                                     disabled required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">Poids du Colis</label>
                                    <input type="text" name="colis[{{ $colisItem->id }}][poids_colis]" id="poids_colis"
                                    value="{{ $colisItem->poids_colis ?? '' }}" class="form-control"
                                    required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Dimension</label>
                                    <input type="text" name="colis[{{ $colisItem->id }}][poids_colis]" id="poids_colis"
                                    value="{{ $colisItem->dimension_result ?? '' }}" class="form-control"
                                    required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="prix_transit_colis" class="form-label">Prix du Colis</label>
                                    <input
                                        type="number"
                                        name="colis[{{ $colisItem->id }}][prix_transit_colis]"
                                        id="prix_transit_colis"
                                        value="{{ $colisItem->prix_transit_colis ?? '' }}"
                                        class="form-control"
                                        placeholder="Somme en CFA"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </form>
        <div class="d-flex justify-content-center gap-2 mt-4">
            <!-- Bouton Retour -->
            <a href="javascript:history.back()" class="btn btn-secondary">
                <i class="fas fa-arrow-left" style="font-size: 18px; margin-right: 5px;"></i> Retour
            </a>
            <!-- Bouton Mise à jour -->
            <button type="button" class="btn btn-primary" id="validate-all-btn">Valider Tous</button>
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