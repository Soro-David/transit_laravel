@extends('AFT_LOUIS_BLERIOT.layouts.agent')
@section('content')
    <h1>Éditer le Client</h1>

    <form action="{{ route('client.update', ['nom' => $client->nom, 'prenom' => $client->prenom, 'tel' => $client->tel, 'email' => $client->email]) }}" method="POST">
        @csrf
        @method('PUT') <!-- Ou PATCH selon vos besoins -->

        <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" value="{{ $client->nom }}">
        </div>

        <div class="form-group">
            <label for="prenom">Prénom</label>
            <input type="text" class="form-control" id="prenom" name="prenom" value="{{ $client->prenom }}">
        </div>

        <div class="form-group">
            <label for="tel">Téléphone</label>
            <input type="text" class="form-control" id="tel" name="tel" value="{{ $client->tel }}">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ $client->email }}">
        </div>

        <button type="submit" class="btn btn-primary">Mettre à jour</button>
        <a href="{{ route('client.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
@endsection