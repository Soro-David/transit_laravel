@extends('customer.layouts.index')
@section('content-header')
    {{-- <h2>Création de Colis</h2> --}}
@endsection
@section('content')
<section class="p-4 mx-auto">
    <form action="{{route('customer_colis.store.step1')}}" method="post" class="form-container">
        @csrf
    {{-- Section : Informations de l'Expéditeur --}}
    <h5 class="text-center mb-4 mt-5">Informations de l'Expéditeur</h5>
    <div class="form-section">
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="nom_expediteur" class="form-label">Nom</label>
                    <input type="text" name="nom_expediteur" id="nom_expediteur" 
                            value="{{$user->first_name}}" class="form-control" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="prenom_expediteur" class="form-label">Prénom</label>
                    <input type="text" name="prenom_expediteur" id="prenom_expediteur" 
                            value="{{$user->last_name}}" class="form-control" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="email_expediteur" class="form-label">Email</label>
                    <input type="email" name="email_expediteur" id="email_expediteur" 
                            value="{{$user->email}}" class="form-control">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="tel_expediteur" class="form-label">Téléphone</label>
                    <input type="text" name="tel_expediteur" id="tel_expediteur" 
                            value="{{$user->last_name}}" class="form-control" required>
                </div>
            </div>
            <div class="col-md-4">
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
            <div class="col-md-4">
                <div class="mb-3">
                    <div class="mb-3">
                        <label for="adresse_expediteur" class="form-label">Adresse</label>
                        <input type="text" name="adresse_expediteur" id="adresse_expediteur" 
                                value="{{$user->email}}" class="form-control" required>
                    </div>
                </div>
            </div>
        </div>
    </div>
        {{-- Section : Informations du Destinataire --}}
        <h5 class="text-center mb-4">Informations du Destinataire</h5>
        <div class="form-section">
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="nom_destinataire" class="form-label">Nom</label>
                        <input type="text" name="nom_destinataire" id="nom_destinataire" 
                               value="{{ old('nom_destinataire') }}" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="prenom_destinataire" class="form-label">Prénom</label>
                        <input type="text" name="prenom_destinataire" id="prenom_destinataire" 
                               value="{{ old('prenom_destinataire') }}" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="email_destinataire" class="form-label">Email</label>
                        <input type="email" name="email_destinataire" id="email_destinataire" 
                               value="{{ old('email_destinataire') }}" class="form-control">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="tel_destinataire" class="form-label">Téléphone</label>
                        <input type="text" name="tel_destinataire" id="tel_destinataire" 
                               value="{{ old('tel_destinataire') }}" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4">
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
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="adresse_destinataire" class="form-label">Adresse</label>
                        <input type="text" name="adresse_destinataire" id="adresse_destinataire" 
                                value="{{ old('adresse_destinataire') }}" class="form-control" required>
                    </div>
                </div>
            </div>
        </div> 
       {{-- Bouton Suivant --}}
        <div class="text-end mt-4 d-flex justify-content-end gap-2">
            <a href="#" class="btn btn-secondary">Retour</a>
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
</style>
@endsection
