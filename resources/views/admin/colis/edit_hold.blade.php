@extends('admin.layouts.admin')

@section('content')
<section class="p-4 mx-auto">
    <div class="all-forms-container">
        <form id="main-update-form" action="{{ route('colis.hold.update') }}" method="POST">
            @csrf
            @method('PUT')

            {{-- On boucle sur chaque groupe de colis (par service/nature) --}}
            @foreach ($colis_recap_list as $index => $colis)
                <div class="form-container">
                    <div class="form-section">
                        {{-- Afficher les infos expéditeur/destinataire seulement pour le premier formulaire --}}
                        @if($loop->first)
                            <h4>Information destinateur & expéditeur</h4>
                            <hr>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Agence d'expédition</label>
                                    <input type="text" value="{{ $colis->expediteur->agence ?? '' }}" class="form-control" disabled>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Contact expéditeur</label>
                                    <input type="text" value="{{ $colis->expediteur->tel ?? '' }}" class="form-control" disabled>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Agence de destination</label>
                                    <input type="text" value="{{ $colis->destinataire->agence ?? '' }}" class="form-control" disabled>
                                </div>
                            </div>
                        @endif

                        <h4 class="mt-4">Information colis</h4>
                        <hr>
                        
                        {{-- On passe les IDs de ce sous-groupe en hidden --}}
                        @foreach ($colis->items as $colis_item)
                            <input type="hidden" name="groupes[{{ $index }}][colis_ids][]" value="{{ $colis_item->id }}">
                        @endforeach
                        
                        {{-- Première ligne --}}
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Quantité</label>
                                <input type="text" value="{{ $colis->quantite_colis }}" class="form-control" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Nature du colis</label>
                                <input type="text" value="{{ $colis->service }}" class="form-control" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Valeur totale</label>
                                <input type="text" value="{{ $colis->valeur_colis }}" class="form-control" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Poids total (Kg)</label>
                                <input type="text" value="{{ $colis->poids_colis }}" class="form-control" disabled>
                            </div>
                        </div>

                        {{-- Deuxième ligne --}}
                        <div class="row g-3 mt-1">
                            <div class="col-md-3">
                                <label class="form-label">Mode de transit</label>
                                <input type="text" value="{{ $colis->mode_transit }}" class="form-control" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Dimensions</label>
                                <input type="text" value="{{ $colis->dimension_result }}" class="form-control" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prix du Devis (pour ce groupe)</label>
                                <input type="number" step="0.01" name="groupes[{{ $index }}][prix_transit_colis]" class="form-control" placeholder="Entrez le prix pour ce groupe" required>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </form>

        <div class="d-flex justify-content-center gap-2 mt-4">
            <a href="{{ route('aftlb_colis.hold') }}" class="btn btn-secondary">Retour</a>
            <button type="button" id="validate-btn" class="btn btn-primary">Valider</button>
        </div>
    </div>
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

        .form-label {
            font-weight: 600;
        }

        input[disabled] {
            background-color: #f5f5f5 !important;
            color: #555;
        }
    </style>
@endsection
