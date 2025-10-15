@extends('IPMS_SIMEXCI_ANGRE.layouts.agent')

@section('content-header')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<section class="py-3">
    <div class="container">

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                    <h4 class="mb-3">Liste des Colis du Ballon : {{ $referenceVol }}</h4>
                    <div class="table-responsive">
                        <table id="colisTable" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Nombre de colis</th>
                                    <th>Expéditeur</th>
                                    <th>Téléphone</th>
                                    <th>Agence Expéditeur</th>
                                    <th>Destinataire</th>
                                    <th>Téléphone</th>
                                    <th>Agence Destinataire</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let referenceVol = "{{ $referenceVol }}";

    let table = $("#colisTable").DataTable({
    responsive: true,
    processing: true,
    serverSide: true,
    language: { url: "{{ asset('js/fr-FR.json') }}" },
    ajax: {
        url: '{{ route("ipms_angre_colis.get_colis_list") }}',
        type: 'GET',
        data: { reference_vol: referenceVol }
    },
    columns: [
        { data: 'reference_colis', name: 'colis.reference_colis' },
        { data: 'nombre_de_colis', name: null, searchable: false },
        { data: 'expediteur_complet', name: null },
        { data: 'expediteur_tel', name: 'expediteurs.tel' },
        { data: 'expediteur_agence', name: 'expediteurs.agence' },
        { data: 'destinataire_complet', name: null },
        { data: 'destinataire_tel', name: 'destinataires.tel' },
        { data: 'destinataire_agence', name: 'destinataires.agence' },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', name: null, orderable: false, searchable: false }
    ]
});


    // Exemple de gestion d'événement pour le bouton "Valider"
    $('#colisTable tbody').on('click', '.btn-valider', function () {
        var refColis = $(this).data('ref');
        alert('Validation pour le colis : ' + refColis);
        
    });

});
</script>

{{-- Le CSS reste identique --}}
<style>
    .btn-valider {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background-color: #28a745;
        color: white;
        font-size: 16px;
        font-weight: bold;
        padding: 10px 20px;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        transition: background-color 0.3s, transform 0.3s;
    }
    .btn-valider:hover { 
        background-color: #218838; 
        transform: scale(1.05); 
    }
</style>