@extends('AGENCE_CHINE.layouts.agent')

@section('content-header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection

@section('content')
<section class="py-3">
    <div class="row">
        <div class="col-12">
            <div class="border p-4 rounded shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">Colis du conteneur : <strong>{{ $reference_contenaire }}</strong></h4>
                    <a href="{{ route('chine_colis.historique.contenaire') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à l'historique
                    </a>
                </div>
                <div class="table-responsive">
                    <table id="colisTable" class="table table-bordered table-striped display nowrap" style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">St. Paiement</th>
                                <th>Référence</th>
                                <th>Nombre Colis</th>
                                <th>Expéditeur</th>
                                <th>Téléphone</th>
                                <th>Agence Exp.</th>
                                <th>Destinataire</th>
                                <th>Agence Dest.</th>
                                <th>Téléphone</th>
                                <th>Date</th>
                                {{-- <th class="text-center">Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Le contenu sera chargé par DataTables via AJAX --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Collez votre MODALE DE PAIEMENT ici --}}
<div class="modal fade" id="paymentModal" ...>
    ...
</div>
@endsection

{{-- @push('scripts') --}}
{{-- Collez vos styles ici --}}
<style>
    .action-buttons-container { display: flex; justify-content: center; align-items: center; gap: 5px; }
    /* ... */
</style>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    var table = $("#colisTable").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        scrollX: true,
        language: { url: "{{ asset('js/fr-FR.json') }}" },
        // **** CORRECTION : URL AJAX dynamique qui inclut la référence du conteneur ****
        ajax: '{{ route("chine_colis.get.colis.pour.contenaire", ["reference_contenaire" => $reference_contenaire]) }}',
        columns: [
            { data: 'statut_paiement', name: 'statut_paiement', orderable: false, searchable: false, className: 'text-center' },
            { data: 'reference_colis', name: 'reference_colis' },
            { data: 'nombre_de_colis', name: 'nombre_de_colis' },
            { data: null, name: 'expediteur_nom', render: data => `${data.expediteur_nom} ${data.expediteur_prenom}` },
            { data: 'expediteur_tel', name: 'expediteur_tel' },
            { data: 'expediteur_agence', name: 'expediteur_agence' },
            { data: null, name: 'destinataire_nom', render: data => `${data.destinataire_nom} ${data.destinataire_prenom}` },
            { data: 'destinataire_agence', name: 'destinataire_agence' },
            { data: 'destinataire_tel', name: 'destinataire_tel' },
            { data: 'created_at', name: 'created_at' },
            // { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        dom: 'Bfrtip',
        buttons: ['excel', 'print'],
        order: [[1, 'desc']]
    });

    // Collez votre logique JavaScript pour la modale de paiement et la suppression ici
    // Assurez-vous qu'elle cible '#colisTable' au lieu de '#productTable'
    $('#colisTable tbody').on('click', '.pay-btn', function (e) {
        // ... votre code existant
    });
    
    $('#paymentForm').on('submit', function(e) {
        // ... votre code existant
    });
});
</script>
{{-- @endpush --}}