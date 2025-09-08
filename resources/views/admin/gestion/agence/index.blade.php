@extends('admin.layouts.admin')
@section('content-header')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<section class="py-3">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h2>Liste des Agences</h2>
            <div class="table-responsive">
                <div class="text-right">
                    <button type="button" style="color: #fff;" class="btn gradient-orange-blue" data-bs-toggle="modal" data-bs-target="#ajouter_agent">
                        Ajouter une agence
                    </button>
                </div><br>
                <table id="agence-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>NOM AGENCE</th>
                            <th>ADRESSE</th>
                            <th>PAYS</th>
                            <th>DEVISE</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Les données seront chargées ici par DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- Modal pour ajouter une agence --}}
<div class="modal fade" id="ajouter_agent" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('agence.store')}}" method="post">
        @csrf
        @method('POST')
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="text-center flex-grow-1 m-0">Information de l'Agence</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nom_agence">Nom de l'agence:</label>
                                    <input type="text" name="nom_agence" value="{{ old('nom_agence') }}" class="form-control" id="nom_agence">
                                    @error('nom_agence')
                                    <div class="text-danger">
                                        <p>{{$message}}</p>
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="adresse_agence">Adresse:</label>
                                    <input type="text" name="adresse_agence" class="form-control" value="{{ old('adresse_agence') }}" id="adresse_agence">
                                    @error('adresse_agence')
                                    <div class="text-danger">
                                        <p>{{$message}}</p>
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="devise_agence">Devise:</label>
                                    <input type="text" name="devise_agence" value="{{ old('devise_agence') }}" class="form-control" id="devise_agence">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pays_agence">Pays:</label>
                                    <select name="pays_agence" class="form-control" id="pays_agence">
                                        <option value="">Selectionnez un pays</option>
                                        <option value="Côte d'Ivoire" {{ old('pays_agence') == "Côte d'Ivoire" ? 'selected' : '' }}>Côte d'Ivoire</option>
                                        <option value="France" {{ old('pays_agence') == "France" ? 'selected' : '' }}>France</option>
                                        <option value="Chine" {{ old('pays_agence') == "Chine" ? 'selected' : '' }}>Chine</option>
                                    </select>
                                    @error('pays_agence')
                                    <div class="text-danger">
                                        <p>{{$message}}</p>
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>
            </div>
        </div>
    </form>
</div>     

<!-- Modal modifView (assurez-vous que cette modal est correctement gérée par JavaScript pour l'édition/affichage) -->
<div class="modal fade" id="modifModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Détails</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit-agence-form">
                    @csrf
                    @method('PUT') {{-- Ou PATCH selon votre implémentation --}}
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_nom_agence" class="form-label">Nom Agence</label>
                            <input type="text" id="edit_nom_agence" name="nom_agence" class="form-control" placeholder="Nom Agence" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_adresse_agence" class="form-label">Adresse</label>
                            <input type="text" id="edit_adresse_agence" name="adresse_agence" class="form-control" placeholder="Adresse" required>
                        </div>
                        <div class="col-md-12">
                            <label for="edit_pays_agence" class="form-label">Pays</label>
                            <textarea id="edit_pays_agence" name="pays_agence" class="form-control" placeholder="Pays agence" rows="3" required></textarea>
                        </div>
                        <div class="col-md-3">
                            <label for="edit_devise_agence" class="form-label">Devise</label>
                            <input type="text" id="edit_devise_agence" name="devise_agence" class="form-control" placeholder="Devise" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-success">Valider</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
                { data: 'displayed_nom_agence', name: 'displayed_nom_agence' }, // Utilisez la nouvelle colonne
                { data: 'adresse_agence', name: 'adresse_agence' },
                { data: 'pays_agence', name: 'pays_agence' },
                { data: 'devise_agence', name: 'devise_agence' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Gestion de la suppression
        $(document).on('click', '.delete-btn', function () {
            const url = $(this).data('url');
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
                            $('#agence-table').DataTable().ajax.reload();
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

        // Gestion de l'édition (exemple, à adapter selon votre implémentation)
        $(document).on('click', '.edit-btn', function () {
            const agenceId = $(this).data('id');
            const editUrl = '{{ route("agence.agence.edit", ":id") }}'.replace(':id', agenceId);
            const updateUrl = '{{ route("agence.agence.update", ":id") }}'.replace(':id', agenceId); // Assurez-vous d'avoir une route update

            $.ajax({
                url: editUrl,
                type: 'GET',
                success: function (response) {
                    $('#edit_nom_agence').val(response.agence.nom_agence);
                    $('#edit_adresse_agence').val(response.agence.adresse_agence);
                    $('#edit_pays_agence').val(response.agence.pays_agence);
                    $('#edit_devise_agence').val(response.agence.devise_agence);
                    $('#edit-agence-form').attr('action', updateUrl); // Met à jour l'action du formulaire
                    $('#modifModal').modal('show');
                },
                error: function (xhr) {
                    Swal.fire('Erreur', 'Impossible de charger les données de l\'agence.', 'error');
                }
            });
        });

        // Soumission du formulaire d'édition (si vous utilisez une modal pour l'édition)
        $('#edit-agence-form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr('action');

            $.ajax({
                url: url,
                type: 'PUT', // Ou PATCH
                data: form.serialize(),
                success: function(response) {
                    Swal.fire('Succès', 'Agence mise à jour avec succès.', 'success');
                    $('#modifModal').modal('hide');
                    $('#agence-table').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    Swal.fire('Erreur', 'Une erreur est survenue lors de la mise à jour.', 'error');
                }
            });
        });

    });
</script>

@endsection