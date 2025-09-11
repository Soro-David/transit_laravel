// History.blade.php
@extends('customer.layouts.index')

@section('content-header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<section class="py-3">
    <form action="" method="POST" class="mt-4">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                    <h4 class="text-left mt-4">Historique des devis validés</h4><br>
                    <div id="products-container">
                        <div class="table-responsive">
                            <table id="productTable" class="display table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Référence Devis</th>
                                        <th>Nombre de Colis</th>
                                        <th>Agence Expédition</th>
                                        <th>Destinataire</th>
                                        <th>Contact Dest.</th>
                                        <th>Agence Destination</th>
                                        <th>Prix Total Devis</th> 
                                        <th>Statut</th>
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
    </form>
</section>

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
                url: "{{ asset('js/fr-FR.json') }}",
                emptyTable: "Aucun devis validé trouvé",
                zeroRecords: "Aucun résultat correspondant trouvé"
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("customer_colis.get.colis.valide") }}',
                type: 'GET',
                error: function(xhr, error, thrown) {
                    console.log('Erreur AJAX:', error, thrown);
                    console.log('Réponse:', xhr.responseText);
                    
                    // Afficher un message d'erreur convivial
                    alert('Erreur lors du chargement des données. Veuillez actualiser la page.');
                }
            },
            columns: [
                { data: 'reference_colis', name: 'reference_colis' },
                { data: 'nombre_colis_par_reference', name: 'nombre_colis_par_reference' },
                { data: 'expediteur_agence', name: 'expediteurs.agence' },
                {
                    data: null,
                    name: 'destinataire_nom_complet',
                    render: function(data, type, row) {
                        return (row.destinataire_nom || '') + ' ' + (row.destinataire_prenom || '');
                    }
                },
                { data: 'destinataire_contact', name: 'destinataires.tel' },
                { data: 'destinataire_agence', name: 'destinataires.agence' },
                { 
                    data: 'total_prix_devis', 
                    name: 'total_prix_devis', 
                    render: function(data, type, row) { 
                        return parseFloat(data).toFixed(2) + ' €'; 
                    } 
                },
                { data: 'etat_display', name: 'etat_display' },
                {
                    data: 'last_updated_at',
                    name: 'last_updated_at',
                    render: function(data, type, row) {
                        if (data) {
                            var date = new Date(data);
                            var day = ('0' + date.getDate()).slice(-2);
                            var month = ('0' + (date.getMonth() + 1)).slice(-2);
                            var year = date.getFullYear();
                            var hours = ('0' + date.getHours()).slice(-2);
                            var minutes = ('0' + date.getMinutes()).slice(-2);
                            return day + '/' + month + '/' + year + ' ' + hours + ':' + minutes;
                        }
                        return data;
                    }
                },
                { 
                    data: 'action', 
                    name: 'action', 
                    orderable: false, 
                    searchable: false 
                }
            ],
            initComplete: function() {
                console.log('DataTable initialisé');
            },
            error: function (xhr, error, thrown) {
                console.log('Erreur DataTable:', error, thrown);
                // Afficher un message d'erreur dans le tableau
                $("#productTable").find('tbody').html(
                    '<tr class="odd">' +
                    '<td valign="top" colspan="10" class="dataTables_empty">' +
                    'Erreur lors du chargement des données. Veuillez actualiser la page.' +
                    '</td>' +
                    '</tr>'
                );
            }
        });
        
        // Rafraîchir automatiquement les données toutes les 30 secondes
        setInterval(function() {
            table.ajax.reload(null, false);
        }, 30000);
    });
    </script>
<style>
    body {
        background-color: #f7f7f7;
    }
    .form-container {
        max-width: 95%;
        margin: auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    #productTable_wrapper .row:first-child > div {
        margin-bottom: 10px;
    }
</style>
@endsection