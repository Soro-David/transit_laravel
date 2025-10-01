@extends('admin.layouts.admin')

@section('content-header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
{{-- Styles pour la page --}}
<style>
    .action-buttons-container { display: flex; justify-content: center; align-items: center; gap: 5px; }
    /* ... autres styles ... */
</style>
@endsection

@section('content')
<section class="py-3">
    <div class="row">
        <div class="col-12">
            <div class="border p-4 rounded shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">Colis du Vol : <strong>{{ $reference_vol }}</strong></h4>
                    <a href="{{ route('cargaison.historique.vol') }}" class="btn btn-secondary">
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
                                <th class="text-center">Action</th>
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

{{-- MODALE DE PAIEMENT --}}
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    {{-- Le contenu de votre modale de paiement ici --}}
</div>
@endsection

{{-- @push('scripts') --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
{{-- Assurez-vous d'inclure les boutons DataTables si vous les utilisez --}}
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

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
        ajax: '{{ route("cargaison.get.colis.pour.vol", ["reference_vol" => $reference_vol]) }}',
        columns: [
            { data: 'statut_paiement', name: 'statut_paiement', orderable: false, searchable: false, className: 'text-center' },
            { data: 'reference_colis', name: 'reference_colis' },
            { data: 'nombre_de_colis', name: 'nombre_de_colis', searchable: false },
            { data: null, name: 'expediteur_nom', render: data => `${data.expediteur_nom} ${data.expediteur_prenom}` },
            { data: 'expediteur_tel', name: 'expediteur_tel' },
            { data: 'expediteur_agence', name: 'expediteur_agence' },
            { data: null, name: 'destinataire_nom', render: data => `${data.destinataire_nom} ${data.destinataire_prenom}` },
            { data: 'destinataire_agence', name: 'destinataire_agence' },
            { data: 'destinataire_tel', name: 'destinataire_tel' },
            { data: 'created_at', name: 'created_at' },
            // La colonne action est bien présente dans le contrôleur, il faut donc la déclarer ici
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        dom: 'Bfrtip', // 'B' pour les boutons (Buttons)
        buttons: ['excel', 'print'],
        order: [[1, 'desc']]
    });

    // Logique pour la modale de paiement
    $('#colisTable tbody').on('click', '.pay-btn', function (e) {
        // ... votre code existant pour ouvrir la modale
        // Exemple :
        // var reference = $(this).data('reference');
        // $('#paymentModal').modal('show');
    });
    
    $('#paymentForm').on('submit', function(e) {
        // ... votre code existant pour soumettre le paiement
    });
});
</script>
{{-- @endpush --}}