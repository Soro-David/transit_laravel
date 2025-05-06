@extends('IPMS_SIMEXCI_ANGRE.layouts.agent')
@section('content-header')
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Liste des colis du conteneur : {{ $colis->first()->reference_contenaire ?? 'Aucun' }}</h4>
        <a href="{{ url()->previous() }}" class="btn btn-danger">
            <i class="fas fa-arrow-left me-1"></i> Retour
        </a>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Référence Colis</th>
                <th>Quantité</th>
                <th>Poids</th>
            </tr>
        </thead>
        <tbody>
            @foreach($colis as $item)
            <tr>
                <td>{{ $item->reference_colis }}</td>
                <td>{{ $item->quantite_colis }}</td>
                <td>{{ $item->poids_colis }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
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
