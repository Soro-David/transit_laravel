@extends('agent.layouts.agent')
@section('content-header')
    {{-- <h2>Création de Colis</h2> --}}
@endsection

@section('content')
<section class="p-4 mx-auto">
    
    <form action="{{route('agent_colis.store.step1')}}" method="post" class="form-container">
        @csrf
        <div class="progress-card">
            <h3>Progression de la soumission</h3>
            <div class="progress-container">
                <div class="progress-bar" style="width: 20%;">Étape 1 / 5</div>
            </div>
        </div>
    
    {{-- Section : Informations de l'Expéditeur --}}
    <h5 class="text-center mb-4 mt-5">Informations de l'Expéditeur</h5>
    <div class="form-section">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="nom_expediteur" class="form-label">Nom</label>
                    <input type="text" name="nom_expediteur" id="nom_expediteur" 
                            value="{{ old('nom_expediteur') }}" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="prenom_expediteur" class="form-label">Prénom</label>
                    <input type="text" name="prenom_expediteur" id="prenom_expediteur" 
                            value="{{ old('prenom_expediteur') }}" class="form-control" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="email_expediteur" class="form-label">Email</label>
                    <input type="email" name="email_expediteur" id="email_expediteur" 
                            value="{{ old('email_expediteur') }}" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="tel_expediteur" class="form-label">Téléphone</label>
                    <input type="text" name="tel_expediteur" id="tel_expediteur" 
                            value="{{ old('tel_expediteur') }}" class="form-control" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="agence_expedition" class="form-label">Agence d'expédition</label>
                    <select name="agence_expedition" id="agence_expedition" class="form-control">
                        <option value="" disabled selected>-- Sélectionnez l'agence d'expédition --</option>
                        @foreach ($agences as $agence)
                            <option value="{{ $agence->nom_agence }}">{{ $agence->nom_agence }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="lieu_expedition" class="form-label">Lieu d'expédition'</label>
                    <select name="lieu_expedition" id="lieu_expedition" class="form-control" required>
                        <option value="" disabled selected>-- Sélectionnez le lieu de Expédition --</option>
                        <option value="angre">Angré</option>
                        <option value="cocody">Cocody</option>
                        <option value="yop">Yopougon</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Section : Informations du Destinataire --}}
    <h5 class="text-center mb-4">Informations du Destinataire</h5>
    <div class="form-section">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="nom_destinataire" class="form-label">Nom</label>
                    <input type="text" name="nom_destinataire" id="nom_destinataire" 
                               value="{{ old('nom_destinataire') }}" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="prenom_destinataire" class="form-label">Prénom</label>
                    <input type="text" name="prenom_destinataire" id="prenom_destinataire" 
                               value="{{ old('prenom_destinataire') }}" class="form-control" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="email_destinataire" class="form-label">Email</label>
                    <input type="email" name="email_destinataire" id="email_destinataire" 
                               value="{{ old('email_destinataire') }}" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="tel_destinataire" class="form-label">Téléphone</label>
                    <input type="text" name="tel_destinataire" id="tel_destinataire" 
                               value="{{ old('tel_destinataire') }}" class="form-control" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="agence_destination" class="form-label">Agence de destination</label>
                    <select name="agence_destination" id="agence_destination" class="form-control">
                        <option value="" disabled selected>-- Sélectionnez l'agence de destination --</option>
                        @foreach ($agences as $agence)
                            <option value="{{ $agence->nom_agence }}">{{ $agence->nom_agence }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="lieu_destination" class="form-label">Lieu de Livraison</label>
                    <select name="lieu_destination" id="lieu_destination" class="form-control" required>
                        <option value="" disabled selected>-- Sélectionnez le lieu de livraison --</option>
                        <option value="angre">Angré</option>
                        <option value="cocody">Cocody</option>
                        <option value="yop">Yopougon</option>
                    </select>
                </div>
            </div>
        </div>
    </div> 

   {{-- Bouton Suivant --}}
    <div class="text-end mt-4 d-flex justify-content-end gap-2">
        <button type="submit" class="btn btn-primary">Suivant</button>
    </div>
    <input type="hidden" name="client_id" id="client_id" value="{{ $agence->id }}"> 
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
