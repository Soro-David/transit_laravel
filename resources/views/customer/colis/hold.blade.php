@extends('customer.layouts.index')

@section('content-header')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<section class="py-3">
    <form action="" method="POST" class="mt-4" id="colisForm">
        @csrf
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
        <div class="row">
            <div class="col-md-12">
                <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                    <h4 class="text-left mt-4">Liste des colis en attente</h4><br>
                    <div id="products-container">
                        <div class="table-responsive">
                            <table id="productTable" class="display table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Référence Colis</th>
                                        <th>Nombre de colis</th>
                                        <th>Agence Expédition</th>
                                        <th>Destinataire</th>
                                        <th>Téléphone Dest.</th>
                                        <th>Agence Destination</th>
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

<style>
    .btn-delete-group {
        background-color: #f44336;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-delete-group:hover {
        background-color: #d32f2f;
    }
</style>

<script>
$(document).ready(function() {
    // Configuration de CSRF token pour toutes les requêtes AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Template d'URL pour la suppression
    var deleteUrlTemplate = "{{ route('customer_colis.delete', ['reference_colis' => 'PLACEHOLDER_REF']) }}";

    var table = $("#productTable").DataTable({
        responsive: true,
        language: {
            url: "{{ asset('js/fr-FR.json') }}"
        },
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("customer_colis.get.colis") }}',
            type: 'GET',
            error: function(xhr, error, thrown) {
                console.log('Erreur AJAX:', error, thrown);
                console.log('Réponse:', xhr.responseText);
                
                // Afficher un message d'erreur à l'utilisateur
                alert('Erreur lors du chargement des données. Veuillez réessayer.');
            }
        },
        columns: [
            { data: 'reference_colis', name: 'reference_colis' },
            { data: 'nombre_colis', name: 'nombre_colis' },
            { data: 'expediteur_agence', name: 'expediteur_agence' },
            { data: 'destinataire_nom_complet', name: 'destinataire_nom_complet' },
            { data: 'destinataire_tel', name: 'destinataire_tel' },
            { data: 'destinataire_agence', name: 'destinataire_agence' },
            { 
                data: 'last_updated_at', 
                name: 'last_updated_at',
                render: function(data, type, row) {
                    if (data) {
                        // Formatage de la date côté client
                        var date = new Date(data);
                        var day = ('0' + date.getDate()).slice(-2);
                        var month = ('0' + (date.getMonth() + 1)).slice(-2);
                        var year = date.getFullYear();
                        var hours = ('0' + date.getHours()).slice(-2);
                        var minutes = ('0' + date.getMinutes()).slice(-2);
                        return day + '/' + month + '/' + year + ' ' + hours + ':' + minutes;
                    }
                    return '';
                }
            },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        initComplete: function() {
            console.log('DataTable initialisé');
        },
        error: function (xhr, error, thrown) {
            console.log('Erreur DataTable:', error, thrown);
            alert('Erreur lors du chargement du tableau. Veuillez réessayer.');
        }
    });

    // Gestionnaire d'événement pour la suppression
    $('#productTable').on('click', '.btn-delete-group', function(e) {
        e.preventDefault();
        var referenceColis = $(this).data('reference');

        if(confirm('Êtes-vous sûr de vouloir supprimer tous les colis avec la référence : ' + referenceColis + ' ?')) {
            var finalDeleteUrl = deleteUrlTemplate.replace('PLACEHOLDER_REF', referenceColis);

            $.ajax({
                url: finalDeleteUrl,
                type: 'DELETE',
                success: function(response) {
                    if(response.success) {
                        alert(response.success);
                        table.ajax.reload();
                    } else {
                        alert(response.error || 'Une erreur est survenue lors de la suppression.');
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'Erreur lors de la suppression du groupe de colis.';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                }
            });
        }
    });
});
</script>
@endsection