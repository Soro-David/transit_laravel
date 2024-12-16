@extends('admin.layouts.admin')

@section('content-header')
    <h2>Création de Colis</h2>
@endsection

@section('content')
<section class="p-4 mx-auto">
    <form action="{{route('colis.store.step3')}}" method="post" class="form-container">
        @csrf
        {{-- Sélection du Mode de Transit --}}
        <div class="form-section">
            <div class="row">
                
            </div>
        </div>
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
                               value="{{ old('reference_colis') }}" class="form-control" required>
                    </div>
                </div>
            </div>
        </div>
        {{-- Boutons --}}
        <div class="text-end mt-4 d-flex justify-content-end gap-2">
            <a href="#" class="btn btn-secondary">Retour</a>
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
        max-width: 1000px;
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
        margin-top: 30px;
    }
</style>
@endsection
