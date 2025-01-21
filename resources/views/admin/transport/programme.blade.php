@extends('admin.layouts.admin')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="py-3">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h4 class="text-center">Liste des programmes aux chauffeurs</h4>
            <div class="table-responsive">
                <table id="agence-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>NOM CHAUFFEUR</th>
                            <th>CONTACT</th>
                            <th>E-MAIL</th>
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
            ajax: '{{ route('transport.get.programme.list') }}',
            columns: [
                {
                    data: null,
                    render: function (data, type, row) {
                        return row.nom + ' ' + row.prenom; // Combine le nom et le prénom
                    }
                }, // NOM CHAUFFEUR
                { data: 'tel', name: 'tel' }, // Numéro de téléphone
                { data: 'email', name: 'email' }, // Email
                { data: 'action', name: 'action', orderable: false, searchable: false } // Actions
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
                            response.success,
                            'success'
                        );
                        $('#agence-table').DataTable().ajax.reload(); // Recharge le tableau
                    },
                    error: function (xhr) {
                        Swal.fire(
                            'Erreur !',
                            xhr.responseJSON.error || 'Une erreur est survenue lors de la suppression.',
                            'error'
                        );
                    }
                });
            }
        });
    });
</script>

@endsection