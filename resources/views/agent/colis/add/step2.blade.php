@extends('agent.layouts.agent')
@section('content-header')
@endsection

@section('content')
<section class="p-4 mx-auto">
    <form action="{{route('agent_colis.store.step2')}}" method="post" class="form-container">
        @csrf
        <div class="progress-card">
            <h3>Progression de la soumission</h3>
            <div class="progress-container">
                <div class="progress-bar" style="width: 40%;">Étape 2 / 5</div>
            </div>
        </div>

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
                            <label for="poids">Poids (Kg)</label>
                            <input type="number" name="poids[]" class="form-control" placeholder="Poids en Kg" required>
                        </div>
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
    </form>
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

    /* Card contenant la barre de progression */
    .progress-card {
        background-color: #fff;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
        margin-bottom: 20px;
    }

    /* Conteneur de la barre de progression */
    .progress-container {
        width: 100%; /* Prend toute la largeur de l'écran */
        background-color: #e0e0e0;
        border-radius: 25px;
        height: 30px;
        margin: 20px 0;
    }

    /* Barre de progression */
    .progress-bar {
        height: 100%;
        width: 40%; /* Étape actuelle (40% pour la deuxième étape) */
        background-color: #4caf50;
        border-radius: 25px;
        text-align: center;
        color: white;
        line-height: 30px; /* Pour centrer le texte verticalement */
    }
</style>

@endsection
