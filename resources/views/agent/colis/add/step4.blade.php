@extends('agent.layouts.agent')
@section('content-header')
@endsection

@section('content')
<section class="p-4 mx-auto">
    <form action="{{route('agent_colis.store.step4')}}" method="post" class="form-container">
        @csrf
        <div class="progress-card">
            <h3>Progression de la soumission</h3>
            <div class="progress-container">
                <div class="progress-bar" style="width: 80%;">Étape 4 / 5</div>
            </div>
        </div>

        {{-- Sélection du Mode de Transit --}}
        <h5 class="text-center mb-4 mt-5">Informations sur le mode de transport</h5>
        <div class="form-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="mode_transit" class="form-label">Sélectionnez le mode de transit</label>
                        <select name="mode_transit" id="mode_transit" class="form-control">
                            <option value="" disabled selected>-- Sélectionnez le mode de transit --</option>
                            <option value="maritime">Maritime</option>
                            <option value="aerien">Aérien</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="reference_colis" class="form-label">Référence</label>
                        <input type="text" name="reference_colis" id="reference_colis" 
                            value="{{ $referenceColis}}" 
                            class="form-control" readonly>
                    </div>
                </div>
            </div>
        </div>

        {{-- Boutons --}}
        <div class="text-end mt-4 d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-primary">Suivant</button>
        </div>
    </form>
</section>

{{-- Scripts JS --}}
<script>
    $(document).ready(function () {
        // Cacher/Afficher les champs en fonction du mode de transit
        $('#mode_transit').on('change', function () {
            const selectedMode = $(this).val();
            if (selectedMode === 'maritime') {
                $('#poids_section').hide();
                $('#dimension_section').show();
            } else if (selectedMode === 'aerien') {
                $('#dimension_section').hide();
                $('#poids_section').show();
            } else {
                $('#poids_section, #dimension_section').hide();
            }
        });

        // Initialisation : cacher les champs au chargement
        $('#poids_section, #dimension_section').hide();
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
        width: 40%; /* Étape actuelle (40% pour la quatrième étape) */
        background-color: #4caf50;
        border-radius: 25px;
        text-align: center;
        color: white;
        line-height: 30px; /* Pour centrer le texte verticalement */
    }
</style>

@endsection
