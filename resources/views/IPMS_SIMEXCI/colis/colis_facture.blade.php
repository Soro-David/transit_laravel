@extends('agent.layouts.agent')
@section('content-header')
    {{-- <h2>Création de Colis</h2> --}}
@endsection


@section('content')
<div class="container">
    <h1>Facture pour le Colis {{ $colis->reference_colis }}</h1>
    <p><strong>Expéditeur :</strong> {{ $colis->expediteur->nom }} {{ $colis->expediteur->prenom }}</p>
    <p><strong>Contact :</strong> {{ $colis->expediteur->tel }}</p>
    <p><strong>Agence :</strong> {{ $colis->expediteur->agence }}</p>

    <h2>Détails du Colis</h2>
    <p><strong>Référence :</strong> {{ $colis->reference_colis }}</p>
    <p><strong>Poids :</strong> {{ $colis->poids_colis }} kg</p>
    <p><strong>Valeur :</strong> {{ $colis->valeur_colis }} CFA</p>
    <p><strong>Destinataire :</strong> {{ $colis->destinataire->nom }} {{ $colis->destinataire->prenom }}</p>
    <p><strong>Contact :</strong> {{ $colis->destinataire->tel }}</p>
    <p><strong>Agence :</strong> {{ $colis->destinataire->agence }}</p>

    <button onclick="window.print();" class="btn btn-primary">Imprimer</button>
</div>
@endsection


