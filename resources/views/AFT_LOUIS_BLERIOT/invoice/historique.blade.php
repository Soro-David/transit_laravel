@extends('AFT_LOUIS_BLERIOT.layouts.agent')
@section('content-header')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<section class="py-3">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h2>Historique des Factures</h2>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>N° Facture</th>
                                <th>Nom</th>
                                <th>ADRESSE</th>
                                <th>PAYS</th>
                                <th>DEVIS</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
</section>

<script>
    $(document).ready(function () {
        $('#agence-table').DataTable({                
            processing: true,
            serverSide: true,
            language: {
            url: "{{ asset('js/fr-FR.json') }}" // Chemin local vers le fichier
        },
            ajax: '{{ route('agence.getAgence') }}',
            columns: [
                { data: 'nom_agence', name: 'nom_agence' },
                { data: 'adresse_agence', name: 'adresse_agence' },
                { data: 'pays_agence', name: 'pays_agence' },
                { data: 'devise_agence', name: 'devise_agence' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
        });

    });

    $(document).on('click', '.delete-btn', function () {
        const url = $(this).data('url'); // URL pour la suppression
        // SweetAlert2 : Confirmation de suppression
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: "Vous ne pourrez pas annuler cette action !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                // Requête AJAX pour supprimer
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function (response) {
                        Swal.fire(
                            'Supprimé !',
                            'L\'agence a été supprimée avec succès.',
                            'success'
                        );
                        $('#agence-table').DataTable().ajax.reload(); // Recharge le tableau
                    },
                    error: function (xhr) {
                        Swal.fire(
                            'Erreur !',
                            'Une erreur est survenue lors de la suppression.',
                            'error'
                        );
                    }
                });
            }
        });
    });


</script>


      
@endsection
