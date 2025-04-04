@extends('AGENCE_CHINE.layouts.agent')
@section('content-header')
    {{-- <h2>Création de Colis</h2> --}}
@endsection

@section('content')
    <section class="p-4 mx-auto">
        <form id="main-update-form" action="{{ route('chine_colis.hold.update') }}" method="POST">
            @csrf
            @method('PUT')

            @foreach($colis as $colis_item)
                <div class="form-container">
                    <div class="form-section">
                        <div class="row">
                            <h4>Information destinateur & expéditeur</h4>
                            <hr>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Agence d'expédition</label>
                                    <input type="text" name="colis[{{ $colis_item->id }}][destinataire_agence]"
                                           id="destinataire_agence_{{ $colis_item->id }}"
                                           value="{{ $colis_item->destinataire->agence ?? '' }}" class="form-control"
                                           disabled required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Contact expéditeur</label>
                                    <input type="text" name="colis[{{ $colis_item->id }}][destinataire_tel]"
                                           id="destinataire_tel_{{ $colis_item->id }}"
                                           value="{{ $colis_item->destinataire->tel ?? '' }}" class="form-control"
                                           disabled required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="destinataire_agence" class="form-label">Agence de destination</label>
                                    <input type="text" name="colis[{{ $colis_item->id }}][destinataire_agence_dest]"
                                           id="destinataire_agence_dest_{{ $colis_item->id }}"
                                           value="{{ $colis_item->destinataire->agence ?? '' }}" class="form-control"
                                           disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <h4>Information colis</h4>
                            <hr>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Quantité de colis</label>
                                    <input type="text" name="colis[{{ $colis_item->id }}][quantite_colis]"
                                           id="quantite_colis_{{ $colis_item->id }}"
                                           value="{{ $colis_item->quantite_colis ?? '' }}" class="form-control"
                                           disabled required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Valeur du Colis</label>
                                    <input type="text" name="colis[{{ $colis_item->id }}][valeur_colis]"
                                           id="valeur_colis_{{ $colis_item->id }}"
                                           value="{{ $colis_item->valeur_colis ?? '' }}" class="form-control" disabled
                                           required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="mode_transit" class="form-label">Mode de transit</label>
                                    <input type="text" name="colis[{{ $colis_item->id }}][mode_transit]"
                                           id="mode_transit_{{ $colis_item->id }}"
                                           value="{{ $colis_item->mode_transit ?? '' }}" class="form-control"
                                           disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description du colis</label>
                                    <textarea name="colis[{{ $colis_item->id }}][description]"
                                              id="description_{{ $colis_item->id }}" cols="5" rows="5"
                                              class="form-control"
                                              disabled>{{ $colis_item->description_colis ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Poids du Colis -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Poids du Colis</label>
                                    <input type="text"
                                           name="colis[{{ $colis_item->id }}][poids_colis]"
                                           id="poids_colis_{{ $colis_item->id }}"
                                           value="{{ $colis_item->poids_colis ?? '' }}"
                                           class="form-control"
                                           disabled
                                           required>
                                </div>
                            </div>
                            <!-- Dimension -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Dimension</label>
                                    <input type="text"
                                           name="colis[{{ $colis_item->id }}][dimension_result]"
                                           id="dimension_result_{{ $colis_item->id }}"
                                           value="{{ $colis_item->dimension_result ?? '' }}"
                                           class="form-control"
                                           disabled
                                           required>
                                </div>
                            </div>
                            <!-- Prix du Colis -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="prix_transit_colis_{{ $colis_item->id }}" class="form-label">Prix du Colis</label>
                                    <input type="number"
                                           name="colis[{{ $colis_item->id }}][prix_transit_colis]"
                                           id="prix_transit_colis_{{ $colis_item->id }}"
                                           value="{{ $colis_item->prix_transit_colis ?? '' }}"
                                           class="form-control prix_transit_colis"
                                           placeholder="Somme en CFA"
                                           required>
                                </div>
                            </div>
                        </div>    
                    </div>
                </div>
            @endforeach

            <div class="d-flex justify-content-center gap-2 mt-4">
                <!-- Bouton Retour -->
                <a href="{{ route('chine_colis.hold') }}" class="btn btn-secondary">
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
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('validate-btn').addEventListener('click', function () {
                Swal.fire({
                    title: 'Confirmer la mise à jour',
                    text: "Êtes-vous sûr de vouloir mettre à jour les prix de transit des colis sélectionnés ?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "Oui, j'accepte",
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Soumet le formulaire principal
                        document.getElementById('main-update-form').submit();
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

        .form-container {
            max-width: 95%;
            margin: auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px; /* Ajout d'un espace entre les formulaires */
        }
    </style>
@endsection