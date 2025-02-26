@extends('admin.layouts.admin')

@section('content-header')
    {{-- <h2>Création de Colis</h2> --}}
@endsection
@section('content')
<section class="p-4 mx-auto">
    
    <form action="{{route('agence.agent.update',['id' => $users->id]) }})}}" method="POST" class="form-container">
        @csrf
        @method('PUT')
        <div class="form-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">Nom </label>
                        <input type="text" name="first_name" id="first_name" 
                                value="{{$users->first_name}}" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Prénom</label>
                        <input type="text" name="last_name" id="last_name" 
                                value="{{$users->last_name}}" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" name="email" id="email" 
                                value="{{$users->email}}" class="form-control" >
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="role" class="form-label">Rôle</label>
                        <input type="text" name="role" id="role" 
                                value="{{$users->role}}" class="form-control">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="agence" class="form-label">Rôle</label>
                        <input type="text" name="agence" id="agence" 
                                value="{{$users->agence->nom_agence}}" class="form-control" >
                    </div>
                </div>
            </div>
        </div>
        {{-- Boutons "Retour" et "Mise à jour" --}}
        <div class="d-flex justify-content-start gap-2 mt-4">
            <!-- Bouton Retour -->
            <a href="{{ route('managers.agent') }}" class="btn btn-secondary">
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
