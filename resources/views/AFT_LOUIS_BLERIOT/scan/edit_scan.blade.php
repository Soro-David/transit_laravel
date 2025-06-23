@extends('AFT_LOUIS_BLERIOT.layouts.agent')

@section('content-header')
    <h2>Modifier l'état des Colis</h2>
@endsection

@section('content')
<section class="p-4 mx-auto">
    <div class="all-forms-container">
        @if(session('success'))
            <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mt-3">{{ session('error') }}</div>
        @endif

        <form id="update-all-form" action="{{ route('aftlb_scan.update.etat') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="reference_colis" class="form-label">Référence du Colis</label>
                <input type="text" name="reference_colis" id="reference_colis" class="form-control" 
                       value="{{ $reference_colis }}" readonly>
            </div>

            <div class="mb-3">
                <label for="etat" class="form-label">Nouvel État</label>
                <select name="etat" id="etat" class="form-control" required>
                    <option value="">-- Choisir un état --</option>
                    <option value="En entrepot">En entrepôt</option>
                    <option value="Chargé">Chargé</option>
                    <option value="Déchargé">Déchargé</option>
                    <option value="Livré">Livré</option>
                </select>
            </div>

            <div class="d-flex justify-content-center gap-2 mt-4">
                <!-- Bouton Retour -->
                <a href="javascript:history.back()" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>

                <!-- Bouton de soumission -->
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> Valider Tous
                </button>
            </div>
        </form>
    </div>
</section>
@endsection
