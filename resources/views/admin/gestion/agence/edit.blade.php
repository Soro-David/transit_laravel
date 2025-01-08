@extends('admin.layouts.admin')

@section('content-header')
    {{-- <h2>Création de Colis</h2> --}}
@endsection
@section('content')
<section class="p-4 mx-auto">
    
    <form action="{{route('agence.agence.update',['id' => $agence->id]) }})}}" method="POST" class="form-container">
        @csrf
        @method('PUT')
   <div class="form-section">
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="nom_agence" class="form-label">Nom de l'agence</label>
                    <input type="text" name="nom_agence" id="nom_agence" 
                            value="{{ $agence->nom_agence }}" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="adresse_agence" class="form-label">Adresse de l'agence</label>
                    <input type="text" name="adresse_agence" id="adresse_agence" 
                            value="{{ $agence->adresse_agence }}" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="pays_agence" class="form-label">Pays de l'agence</label>
                    <input type="text" name="pays_agence" id="pays_agence" 
                            value="{{ $agence->pays_agence }}" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="devise_agence" class="form-label">Dévise</label>
                    <input type="text" name="devise_agence" id="devise_agence" 
                            value="{{ $agence->devise_agence }}" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="prix_au_kg" class="form-label">Prix en kg</label>
                    <input type="text" name="prix_kg" id="prix_au_kg" 
                            value="{{ $agence->prix_au_kg }}" class="form-control" required>
                </div>
            </div>
        </div>
    </div>
    {{-- Boutons "Retour" et "Mise à jour" --}}
    <div class="d-flex justify-content-start gap-2 mt-4">
        <!-- Bouton Retour -->
        <a href="{{ route('managers.agence') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left" style="font-size: 18px; margin-right: 5px;"></i> Retour
        </a>

        <!-- Bouton Mise à jour -->
        <button type="submit" class="btn btn-primary">Mise à jour</button>
    </div>

</form>
</section>

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
        width: 20%; /* Mettre ici la largeur souhaitée en fonction de l'étape */
        background-color: #4caf50;
        border-radius: 25px;
        text-align: center;
        color: white;
        line-height: 30px; /* Pour centrer le texte verticalement */
    }

    /* Texte indiquant l'étape */
    .step {
        font-size: 16px;
        font-weight: bold;
        margin-top: 10px;
        color: #333;
    }
</style>
@endsection
