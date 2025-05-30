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

{{-- S'assurer que jQuery et DataTables sont chargés avant ce script --}}
{{-- S'assurer que SweetAlert2 est chargé si vous l'utilisez --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}

{{-- S'assurer que la balise meta csrf-token est dans votre <head> du layout principal --}}
{{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}

<script>
// Définir le template d'URL ici, en dehors de $(document).ready pour qu'il soit disponible globalement
// Nous utilisons un placeholder unique 'PLACEHOLDER_REF' qui sera remplacé.
var deleteUrlTemplate = "{{ route('customer_colis.delete', ['reference_colis' => 'PLACEHOLDER_REF']) }}";

$(document).ready(function() {
    var table = $("#productTable").DataTable({
        responsive: true,
        language: {
            url: "{{ asset('js/fr-FR.json') }}" // Assurez-vous que ce fichier existe
        },
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("customer_colis.get.colis") }}', // Route pour récupérer les données
            type: 'GET'
        },
        columns: [
            { data: 'reference_colis', name: 'reference_colis' },
            { data: 'nombre_colis', name: 'nombre_colis' },
            { data: 'expediteur_agence', name: 'expediteur_agence' },
            { data: 'destinataire_nom_complet', name: 'destinataire_nom_complet' },
            { data: 'destinataire_tel', name: 'destinataire_tel' },
            { data: 'destinataire_agence', name: 'destinataire_agence' },
            { data: 'last_updated_at', name: 'last_updated_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#productTable').on('click', '.btn-delete-group', function(e) {
        e.preventDefault();
        var referenceColis = $(this).data('reference');

        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: "Vous êtes sur le point de supprimer tous les colis avec la référence : " + referenceColis + ". Cette action est irréversible !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                var finalDeleteUrl = deleteUrlTemplate.replace('PLACEHOLDER_REF', referenceColis);

                $.ajax({
                    url: finalDeleteUrl,
                    type: 'DELETE',
                    success: function(response) {
                        if(response.success) {
                            Swal.fire(
                                'Supprimé !',
                                response.success,
                                'success'
                            );
                            table.ajax.reload();
                        } else {
                             Swal.fire(
                                'Erreur !',
                                response.error || 'Une erreur est survenue lors de la tentative de suppression.',
                                'error'
                            );
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = 'Erreur lors de la suppression du groupe de colis.';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire(
                            'Erreur !',
                            errorMessage,
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>
@endsection