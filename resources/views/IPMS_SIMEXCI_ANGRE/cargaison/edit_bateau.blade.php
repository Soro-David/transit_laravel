@extends('IPMS_SIMEXCI_ANGRE.layouts.agent')
@section('content-header')
@endsection

@section('content')
<div class="container mt-5">
    <h3>Modifier le Bateau</h3>
    
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('ipms_angre_colis.bateaux.update', $bateau->id) }}" method="POST">
        @csrf
        @method('PUT')

    <div class="row">
        <div class="form-group mb-3 col-md-6">
            <label for="reference_bateau">Référence Bateau</label>
            <input type="text" class="form-control" name="reference_bateau" value="{{ $bateau->reference_bateau }}" required>
        </div>

        <div class="form-group mb-3 col-md-6">
            <label for="reference_contenaire">Référence Conteneur</label>
            <input type="text" class="form-control" name="reference_contenaire" value="{{ $bateau->reference_conteneur }}" required>
        </div>
    </div>

    <div class="row">
        <div class="form-group mb-3 col-md-6">
            <label for="date_depart">Date de Départ</label>
            <input type="datetime-local" class="form-control" name="date_depart" value="{{ \Carbon\Carbon::parse($bateau->created_at)->format('Y-m-d\TH:i') }}" required>
        </div>

        <div class="form-group mb-3 col-md-6">
            <label for="date_arriver">Date d’Arrivée</label>
            <input type="datetime-local" class="form-control" name="date_arriver" value="{{ \Carbon\Carbon::parse($bateau->date_arriver)->format('Y-m-d\TH:i') }}" required>
        </div>
    </div>
    <div class="container text-right">
        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Annuler</a>
    </div>
    </form>
</div>
</section>

<style>
    .btn {
        width: 15%;
        height: 40px;
        font-size: 18px;
    }

    .dataTable-wrapper {
        width: 80% !important;
        margin: 20px auto;
        padding: 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background: #f9f9f9;
    }

    .dt-button {
        padding: 10px 20px;
        margin: 5px;
        border: 1px solid transparent;
        border-radius: 5px;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
        text-transform: uppercase;
        transition: all 0.3s ease;
    }
</style>
@endsection
