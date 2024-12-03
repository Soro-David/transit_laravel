@extends('customer.layouts.index')
@section('content-header')
@endsection
@section('content')
    @csrf
    <section>
        <form action="" method="post">
            @csrf
            <div id="products-container">
                <h4 class="text-center mt-4">Ajouter les produits</h4><br>
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
                            <label for="prix_unitaire">Masse (Kg)</label>
                            <input type="number" name="masse[]" class="form-control" placeholder="Masse en Kg" required>
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
                <div class="container">
                    <h6 class="text-right mt-4">Prix total : <span id="prix-total">0</span> FCFA</h6>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="border p-4 rounded shadow-sm">
                            <label for="commentaire1" class="form-label">Commentaire</label>
                            <textarea name="commentaire1" id="commentaire1" rows="5" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-8">
                            <label for="reference_contenaire" class="form-label">Référence Contenaire</label>
                            <input type="text" name="reference_contenaire" id="reference_contenaire" placeholder="Référence du contenaire" class="form-control">
                        </div>
                    </div>
                    <div class="container text-center">
                        <button type="submit" class="btn btn-success mt-3">
                            <i class="fas fa-save mx-5"></i>
                            Valider
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </section>
    <!-- Scripts -->
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
                            <input type="number" name="masse[]" class="form-control" placeholder="Masse en Kg" required>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-danger remove-product" style="margin-top: 32px;">-</button>
                        </div>
                    </div>`;
                $("#dynamic-form").append(newRow);
            });

            $(document).on("click", ".remove-product", function () {
                $(this).closest(".product-row").remove();
            });
            // Gestion des méthodes de paiement
            $("#paymentMethod").on("change", function () {
                $(".payment-details").hide();
                $("#" + $(this).val() + "Details").show();
            });
            // Soumission du formulaire de paiement
            $("#paymentForm").on("submit", function (e) {
                e.preventDefault();
                alert("Mode de paiement choisi : " + $("#paymentMethod").val());
            });
        });
    </script>

    <!-- Styles -->
    <style>
        table.dataTable {
            width: 100% !important;
        }
        table.dataTable th, table.dataTable td {
            white-space: nowrap;
        }
    </style>
@endsection
