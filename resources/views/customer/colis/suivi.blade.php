@extends('customer.layouts.index')

@section('content-header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<section class="py-3">
    <form action="" method="POST" class="mt-4">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                    <h4 class="text-left mt-4">Suivi des colis</h4><br>
                    <div id="products-container">
                            <table id="productTable" class="display table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Référence Colis</th>
                                        <th>Nb. Colis</th> 
                                        <th>Agence Expédition</th>
                                        <th>Destinataire</th>
                                        <th>Téléphone Dest.</th>
                                        <th>Agence Destination</th>
                                        <th>Statut Actuel</th>
                                        <th>Etat colis</th> <!-- Nouvelle colonne -->
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

<!-- Script JavaScript -->
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var table = $("#productTable").DataTable({
        responsive: true,
        language: {
            url: "{{ asset('js/fr-FR.json') }}"
        },
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("customer_colis.get.colis.suivi") }}',
            type: 'GET' 
        },
        columns: [
            { data: 'reference_colis', name: 'reference_colis' },
            { data: 'nombre_colis_par_reference', name: 'nombre_colis_par_reference' },
            { data: 'expediteur_agence', name: 'expediteurs.agence' }, 
            {
                data: 'destinataire_complet',
                name: 'destinataires.nom',
                render: function (data, type, row) {
                    return data;
                }
            },
            { data: 'destinataire_tel', name: 'destinataires.tel' },
            { data: 'destinataire_agence', name: 'destinataires.agence' },
            { data: 'etat', name: 'colis.etat' },
            { 
                data: 'statut_personnalise', 
                name: 'statut_personnalise',
                orderable: false,
                searchable: false
            },
            {
                data: 'last_updated_at',
                name: 'last_updated_at',
            }
        ],
        order: [[8, 'desc']] // Trier par date par défaut
    });
});
</script>

<style>
.badge {
    padding: 0.5em 0.75em;
    border-radius: 0.25rem;
    font-size: 0.875em;
}
.badge-primary { background-color: #007bff; color: white; }
.badge-success { background-color: #28a745; color: white; }
.badge-info { background-color: #17a2b8; color: white; }
.badge-secondary { background-color: #6c757d; color: white; }
</style>
@endsection