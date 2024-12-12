
@extends('admin.layouts.admin')
@section('content-header')
@section('content')
        @csrf
        <section>
            <div class="container">
                <h1>Colis enregistré avec succès</h1>
                <p><strong>ID :</strong> {{ $colis->id }}</p>
                <p><strong>Statut :</strong> {{ $colis->status }}</p>
                <p><strong>Statut :</strong> {{ $colis->qr_code_path }}</p>
                <p><strong>Expéditeur :</strong> {{ $data['nom_expediteur'] }} {{ $data['prenom_expediteur'] }}</p>
                <p><strong>Destinataire :</strong> {{ $data['nom_destinataire'] }} {{ $data['prenom_destinataire'] }}</p>
        
                <h3>Votre QR Code :</h3>
                <img src="{{ asset('storage/' . $colis->qr_code_path) }}" alt="QR Code">
            </div>
        </section>
<style>
    section{
        background-color: #fff !important;
    }
    table.dataTable {
        width: 100% !important;
    }
    table.dataTable th,
    table.dataTable td {
        white-space: nowrap;
    }
</style>

@endsection