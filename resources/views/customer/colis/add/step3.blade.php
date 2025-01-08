@extends('customer.layouts.index')
@section('content-header')
@section('content')
<section>
    <form action="{{route('customer_colis.store.step3')}}" method="post" class="form-container">
        @csrf
    <div class="form-section">
        <div id="products-container">
            <h4 class="text-center mt-4">Ajouter des Articles</h4><br>
            <div id="dynamic-form">
                <div class="form-row align-items-end mb-3 product-row">
                    <div class="col-4">
                        <label for="description">Description</label>
                        <input type="text" name="description[]" class="form-control" placeholder="Description" required>
                    </div>
                    <div class="col">
                        <label for="quantite">Quantité</label>
                        <input type="number" name="quantite[]" class="form-control" placeholder="Quantité" required>
                    </div>
                    <div class="col">
                        <label for="dimension">Dimensions</label>
                        <input type="text" name="dimension[]" class="form-control" placeholder="Dimensions" required>
                    </div>
                    <div class="col">
                        <label for="prix_unitaire">Poids (Kg)</label>
                        <input type="number" name="poids[]" class="form-control" placeholder="Poids en Kg" required>
                    </div>
                    {{-- <div class="col">
                        <label for="prix_total">Prix Total</label>
                        <input type="number" name="prix_total[]" class="form-control" placeholder="Prix Total" required>
                    </div> --}}
                    <div class="col-auto">
                        <button type="button" class="btn btn-success add-product" style="margin-top: 32px;">+</button>
                    </div>
                </div>
            </div>
            <div class="text-end mt-4 d-flex justify-content-end gap-2">
                <a href="#" class="btn btn-secondary">Retour</a>
                <button type="submit" class="btn btn-primary">Suivant</button>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).ready(function () {
    // Gestion des produits dynamiques
    $(".add-product").on("click", function () {
        let newRow = `
            <div class="form-row align-items-end mb-3 product-row">
                <div class="col-4">
                    <input type="text" name="description[]" class="form-control" placeholder="Description" required>
                </div>
                <div class="col">
                    <input type="number" name="quantite[]" class="form-control" placeholder="Quantité" required>
                </div>
                <div class="col">
                    <input type="text" name="dimension[]" class="form-control" placeholder="Dimensions" required>
                </div>
                <div class="col">
                    <input type="number" name="poids[]" class="form-control" placeholder="Poids en Kg" required>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-danger remove-product" style="margin-top: 32px;">-</button>
                </div>
            </div>`;
        $("#dynamic-form").append(newRow);
    });
    // Supprimer une ligne au clic sur "-"
        $(document).on('click', '.remove-product', function () {
            $(this).closest('.product-row').remove();
            toggleProductSections();
        });
});
</script>
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
