@extends('admin.layouts.adminprint')

@section('content')

    <h1>Détails du Prospect</h1>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $prospect->nom }} {{ $prospect->prenom }}</h5>
            <p class="card-text"><strong>Email:</strong> {{ $prospect->email }}</p>
            <p class="card-text"><strong>Téléphone:</strong> {{ $prospect->tel }}</p>
            <p class="card-text"><strong>Adresse:</strong> {{ $prospect->adresse }}</p>
            <p class="card-text"><strong>Type:</strong> {{ $prospect->type == 'expediteur_non_user' ? 'Expéditeur non utilisateur' : 'Prospect enregistré' }}</p>
            <a href="{{ route('prospect.edit', $prospect->id) }}" class="btn btn-warning">Modifier</a>
            <a href="{{ route('prospect.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>
    </div>
</section>
