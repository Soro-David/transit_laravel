@extends('customer.layouts.index')
@section('content-header')
    {{-- <h2>Création de Colis</h2> --}}
@endsection
@section('content')
<section class="p-4 mx-auto">
    <form action="{{route('customer_colis.store.step3')}}" method="post" class="form-container">
        @csrf
            <h5 class="text-center mb-4 mt-5">Informations du colis</h5>
            <div class="form-section">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="quantite" class="form-label">Quantité</label>
                            <input type="number" name="quantite" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="type_emballage" class="form-label">Tye d'emballage</label>
                            <input type="text" name="type_emballage" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="dimension" class="form-label">Dimension</label>
                            <input type="text" name="dimension" class="form-control dimension" placeholder="exemple:20x10x20">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="description_colis" class="form-label">Description</label>
                            <textarea name="description_colis" id="description_colis" class="form-control" cols="4" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="poids_colis" class="form-label">Poids</label>
                            <input type="number" name="poids_colis" id="poids_colis" class="form-control poids">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="valeur_colis" class="form-label">Valeur déclarée en devise</label>
                            <input type="number" name="valeur_colis" id="valeur_colis" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="text-end mt-4 d-flex justify-content-end gap-2">
                    <a href="#" class="btn btn-secondary">Retour</a>
                    <button type="submit" class="btn btn-primary">Suivant</button>
                </div>
        </div>
    </form>
</section>
{{-- Scripts JS --}}
<script>
    // $(document).ready(function () {
    //     // Cacher/Afficher les champs en fonction du mode de transit
    //     function toggleProductSections() {
    //         const selectedMode = $('#mode_transit').val();
    //         if (selectedMode === 'maritime') {
    //             $('#poids_section').hide();
    //             $('#dimension_section').show();
    //         } else if (selectedMode === 'aerien') {
    //             $('#dimension_section').hide();
    //             $('#poids_section').show();
    //         } else {
    //             $('#poids_section, #dimension_section').hide();
    //         }
    //     }

    //     // Initialisation : cacher les champs au chargement
    //     toggleProductSections();

    //     // Lorsque le mode de transit change
    //     $('#mode_transit').on('change', function () {
    //         toggleProductSections();
    //     });

    //     // Ajouter une nouvelle ligne au clic sur "+"
    //     $(document).on('click', '.add-product', function () {
    //         const newRow = `
    //             <div class="row product-row">
    //                 <div class="col-md-2">
    //                     <div class="mb-3">
    //                         <input type="text" name="quantite[]" class="form-control quantite" required>
    //                     </div>
    //                 </div>
    //                 <div class="col-md-3" id="dimension_section">
    //                     <div class="mb-3">
    //                         <input type="text" name="dimension[]" class="form-control dimension">
    //                     </div>
    //                 </div>
    //                 <div class="col-md-2" id="poids_section">
    //                     <div class="mb-3">
    //                         <input type="text" name="poids[]" class="form-control poids">
    //                     </div>
    //                 </div>
    //                 <div class="col-md-2">
    //                     <div class="mb-3">
    //                         <input type="text" name="prix_unitaire[]" class="form-control prix_unitaire">
    //                     </div>
    //                 </div>
    //                 <div class="col-md-2">
    //                     <div class="mb-3">
    //                         <input type="text" name="prix_total[]" class="form-control prix_total">
    //                     </div>
    //                 </div>
    //                 <div class="col-auto">
    //                     <button type="button" class="btn btn-danger remove-product" style="margin-top: 32px;">-</button>
    //                 </div>
    //             </div>`;
    //         $('#product-list').append(newRow);
    //         // Mettre à jour la logique d'affichage pour chaque nouvelle ligne
    //         toggleProductSections();
    //     });

    //     // Supprimer une ligne au clic sur "-"
    //     $(document).on('click', '.remove-product', function () {
    //         $(this).closest('.product-row').remove();
    //         toggleProductSections();
    //     });
    // });
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

    .form-section {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    section {
        margin-top: 0px;
    }
</style>
@endsection
