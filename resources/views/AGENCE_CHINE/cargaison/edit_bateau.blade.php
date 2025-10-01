@extends('AGENCE_CHINE.layouts.agent')

@section('content-header')
    {{-- Section content-header --}}
@endsection

@section('content')
<div class="container mt-5">
    <h3>Modifier le Véhicule de Navigation</h3>
    
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('chine_colis.bateaux.update', $bateau->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card border-0 rounded shadow-sm">
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4 form-group">
                        <label for="reference_bateau" class="form-label fw-bold">Référence Véhicule</label>
                        <input type="text" class="form-control" name="reference_bateau" value="{{ $bateau->reference_bateau }}" readonly>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="reference_conteneur" class="form-label fw-bold">Référence Conteneur/Vol</label>
                        {{-- CORRECTION : 'reference_contenaire' a été remplacé par 'reference_conteneur' --}}
                        <input type="text" class="form-control" name="reference_conteneur" value="{{ $bateau->reference_conteneur }}" readonly>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="compagnie" class="form-label fw-bold">Compagnie</label>
                        <input type="text" class="form-control" name="compagnie" id="compagnie" value="{{ old('compagnie', $bateau->compagnie) }}" placeholder="Nom de la compagnie">
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="date_depart" class="form-label fw-bold">Date de Départ</label>
                        <input type="datetime-local" class="form-control" name="date_depart" value="{{ \Carbon\Carbon::parse($bateau->created_at)->format('Y-m-d\TH:i') }}" required>
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="date_arriver" class="form-label fw-bold">Date d’Arrivée</label>
                        <input type="datetime-local" class="form-control" name="date_arriver" value="{{ \Carbon\Carbon::parse($bateau->date_arriver)->format('Y-m-d\TH:i') }}" required>
                    </div>
                    
                    <!-- Champs Bateau -->
                    <div class="col-md-6 bateau-fields" style="display: none;">
                        <label for="numero_bateau" class="form-label fw-bold">Numéro du bateau:</label>
                        <input type="text" name="numero_bateau" id="numero_bateau" class="form-control" value="{{ old('numero_bateau', $bateau->numero_bateau) }}" placeholder="Ex: B12345">
                    </div>

                    <div class="col-md-6 bateau-fields" style="display: none;">
                        <label for="nom_bateau" class="form-label fw-bold">Nom du bateau:</label>
                        <input type="text" name="nom_bateau" id="nom_bateau" class="form-control" value="{{ old('nom_bateau', $bateau->nom_bateau) }}" placeholder="Ex: Océanic">
                    </div>

                    <!-- Champs Avion -->
                    <div class="col-md-6 ballon-fields" style="display: none;">
                        <label for="numero_ballon" class="form-label fw-bold">Numéro de vol:</label>
                        <input type="text" name="numero_ballon" id="numero_ballon" class="form-control" value="{{ old('numero_ballon', $bateau->numero_ballon) }}" placeholder="Ex: AF702">
                    </div>

                    <div class="col-md-6 ballon-fields" style="display: none;">
                        <label for="nom_ballon" class="form-label fw-bold">Nom de l'avion:</label>
                        <input type="text" name="nom_ballon" id="nom_ballon" class="form-control" value="{{ old('nom_ballon', $bateau->nom_ballon) }}" placeholder="Ex: Airbus A380">
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Annuler</a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Le script suppose que l'objet $bateau a une propriété 'type'
        // qui est soit 'bateau', soit 'ballon' (avion).
        const type = "{{ $bateau->type ?? '' }}"; 

        const showFields = (selector) => document.querySelectorAll(selector).forEach(field => field.style.display = "block");
        const hideFields = (selector) => document.querySelectorAll(selector).forEach(field => field.style.display = "none");

        if (type === "bateau") {
            showFields(".bateau-fields");
            hideFields(".ballon-fields");
        } else if (type === "ballon") {
            hideFields(".bateau-fields");
            showFields(".ballon-fields");
        }
    });
</script>

<style>
    .btn {
        width: auto;
        padding: 10px 20px;
        min-width: 150px;
        font-size: 16px;
    }
</style>
@endsection