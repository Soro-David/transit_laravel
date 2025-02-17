@extends('admin.layouts.admin')

@section('content-header')
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h2>Liste des chauffeur</h2>

            {{-- Filter and Search Section --}}
            <div class="row">
                {{-- Country Filter --}}
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="pays_agence" class="col-label">Country:</label>
                        <select class="form-control" id="pays_agence" name="pays_agence">
                            <option value="">Tous les pays</option>
                            @foreach($pays_agence as $pays)
                            <option value="{{ $pays }}">{{ $pays }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
              

            <div class="text-right">
                <button type="button" style="color: #fff;" class="btn gradient-orange-blue" data-bs-toggle="modal"
                    data-bs-target="#ajouter_chauffeur">
                    Ajouter un chauffeur
                </button>
            </div><br>
            <table id="chauffeur-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Contact</th>
                        <th>Tel</th>
                        <th>Email</th>
                        <th>Agence</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    {{-- Edit Chauffeur Modal --}}
    <div class="modal fade" id="editChauffeurModal" tabindex="-1" aria-labelledby="editChauffeurModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="text-center flex-grow-1 m-0" id="editChauffeurModalLabel">Modifier le chauffeur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <form id="editChauffeurForm">
                            @csrf
                            @method('PUT')

                            {{-- Read-Only Fields --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_nom_chauffeur">Nom du chauffeur:</label>
                                        <input type="text" class="form-control" id="edit_nom_chauffeur" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_prenom_chauffeur">Prénom du chauffeur:</label>
                                        <input type="text" class="form-control" id="edit_prenom_chauffeur" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                {{-- Editable Agency Field --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_agence_expedition">Agence:</label>
                                        <select name="agence_expedition" id="edit_agence_expedition" class="form-control">
                                            <option value="" disabled>-- Sélectionnez l'agence d'expédition --</option>
                                            @foreach ($agences as $agence)
                                                <option value="{{ $agence->id }}">{{ $agence->nom_agence }}</option>
                                            @endforeach
                                        </select>
                                        @error('agence_expedition')
                                            <div class="text-danger">
                                                <p>{{ $message }}</p>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Editable Phone Field --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_tel_chauffeur">Contact du chauffeur:</label>
                                        <input type="text" class="form-control" id="edit_tel_chauffeur" name="tel_chauffeur">
                                    </div>
                                </div>
                            </div>

                            {{-- Editable Password Fields --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Mot de passe:</label>
                                        <input type="password" name="password" class="form-control" id="password">
                                        @error('password')
                                            <div class="text-danger">
                                                <p>{{ $message }}</p>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password_confirmation">Confirmation Mot de passe:</label>
                                        <input type="password" name="password_confirmation" class="form-control"
                                            id="password_confirmation">
                                        @error('password_confirmation')
                                            <div class="text-danger">
                                                <p>{{ $message }}</p>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                <button type="submit" class="btn btn-primary">Modifier</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Chauffeur Modal --}}
    <div class="modal fade" id="ajouter_chauffeur" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <form action="{{ route('transport.store.chauffeur') }}" method="post">
            @csrf
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between align-items-center">
                        <h5 class="text-center flex-grow-1 m-0">Information du chauffeur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <!-- Nom du chauffeur -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nom_chauffeur">Nom du chauffeur:</label>
                                        <input type="text" name="nom_chauffeur" value="{{ old('nom_chauffeur') }}"
                                            class="form-control" id="nom_chauffeur">
                                        @error('nom_chauffeur')
                                            <div class="text-danger">
                                                <p>{{ $message }}</p>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <!-- Prénom du chauffeur -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="prenom_chauffeur">Prénom du chauffeur:</label>
                                        <input type="text" name="prenom_chauffeur"
                                            value="{{ old('prenom_chauffeur') }}" class="form-control"
                                            id="prenom_chauffeur">
                                        @error('prenom_chauffeur')
                                            <div class="text-danger">
                                                <p>{{ $message }}</p>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Email du chauffeur -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email_chauffeur">Email Chauffeur:</label>
                                        <input type="email" name="email_chauffeur" class="form-control"
                                            value="{{ old('email_chauffeur') }}" id="email_chauffeur">
                                        @error('email_chauffeur')
                                            <div class="text-danger">
                                                <p>{{ $message }}</p>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <!-- Contact du chauffeur -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tel_chauffeur">Contact du chauffeur:</label>
                                        <input type="text" name="tel_chauffeur" value="{{ old('tel_chauffeur') }}"
                                            class="form-control" id="tel_chauffeur">
                                        @error('tel_chauffeur')
                                            <div class="text-danger">
                                                <p>{{ $message }}</p>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Mot de passe du chauffeur -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Mot de passe:</label>
                                        <input type="password" name="password" class="form-control" id="password">
                                        @error('password')
                                            <div class="text-danger">
                                                <p>{{ $message }}</p>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <!-- Confirmation mot de passe du chauffeur -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password_confirmation">Confirmation Mot de passe:</label>
                                        <input type="password" name="password_confirmation" class="form-control"
                                            id="password_confirmation">
                                        @error('password_confirmation')
                                            <div class="text-danger">
                                                <p>{{ $message }}</p>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Agence -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="agence_expedition">Agence:</label>
                                        <select name="agence_expedition" id="agence_expedition" class="form-control">
                                            <option value="" disabled selected>-- Sélectionnez l'agence d'expédition
                                            </option>
                                            @foreach ($agences as $agence)
                                                <option value="{{ $agence->id }}"
                                                    {{ old('agence_expedition') == $agence->id ? 'selected' : '' }}>
                                                    {{ $agence->nom_agence }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('agence_expedition')
                                            <div class="text-danger">
                                                <p>{{ $message }}</p>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="agence_id" value="{{ $agence->id }}">
                    {{-- @dd($agence->id) --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Confirm Delete Modal --}}
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmation de Suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer ce chauffeur ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Supprimer</button>
                </div>
            </div>
        </div>
    </div>

    </section>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        let chauffeurIdToDelete;

        $(document).ready(function() {
            // Initialize DataTables
            var table = $('#chauffeur-table').DataTable({
                processing: true,
                serverSide: true,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
                },
                ajax: {
                    url: '{{ route('transport.get.chauffeur.list') }}',
                    data: function(d) {
                        d.pays_agence = $('#pays_agence').val()
                    }
                },
                columns: [{
                        data: 'nom',
                        name: 'nom'
                    },
                    {
                        data: 'prenom',
                        name: 'prenom'
                    },
                    {
                        data: 'tel',
                        name: 'tel'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'agence', // Data for the agency column
                        name: 'agence',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            // Generate URLs using the route() helper
                            var editUrl = `{{ route('transport.chauffeur.edit', ['id' => '__ID__']) }}`.replace('__ID__', row.id);
                            var deleteUrl = `{{ route('transport.chauffeur.destroy', ['id' => '__ID__']) }}`.replace('__ID__', row.id);

                            return `
                                <button class="btn btn-sm btn-primary edit-chauffeur-btn" data-id="${row.id}" data-bs-toggle="modal" data-bs-target="#editChauffeurModal" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-chauffeur-btn" data-id="${row.id}" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            `;
                        }
                    }
                ]
            });

            // Reload table on country change
            $('#pays_agence').on('change', function() {
                table.ajax.reload();
            });

            // Delete Chauffeur
            $('#confirmDeleteBtn').click(function() {
                $.ajax({
                    url: `{{ route('transport.chauffeur.destroy', ['id' => '__ID__']) }}`.replace('__ID__', chauffeurIdToDelete),
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#confirmDeleteModal').modal('hide');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        console.error('Error deleting chauffeur:', xhr);
                    }
                });
            });

            $('#confirmDeleteModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                chauffeurIdToDelete = button.data('id');
            });

            // Show Edit Chauffeur Modal and Populate Data
            $('#chauffeur-table').on('click', '.edit-chauffeur-btn', function() {
                const chauffeurId = $(this).data('id');
                $('#editChauffeurForm').data('id', chauffeurId); // Store chauffeurId in the form

                $.ajax({
                    url: `{{ route('transport.chauffeur.edit', ['id' => '__ID__']) }}`.replace('__ID__', chauffeurId),
                    type: 'GET',
                    success: function(data) {
                        // Populate modal with chauffeur data, setting read-only values
                        $('#editChauffeurModal #edit_nom_chauffeur').val(data.chauffeur.nom);
                        $('#editChauffeurModal #edit_prenom_chauffeur').val(data.chauffeur.prenom);
                        $('#editChauffeurModal #edit_tel_chauffeur').val(data.chauffeur.tel);

                        // Set agency selection
                        $('#editChauffeurModal #edit_agence_expedition').val(data.chauffeur.agence_id);

                        // Show the modal
                        $('#editChauffeurModal').modal('show');
                    },
                    error: function(xhr) {
                        console.error('Error fetching chauffeur data:', xhr);
                    }
                });
            });

            // Handle the form submission for editing the chauffeur
            $('#editChauffeurForm').submit(function(e) {
                e.preventDefault(); // Prevent the form from submitting normally

                const chauffeurId = $(this).data('id'); // Retrieve the chauffeur ID from the form's data attribute

                const formData = $(this).serialize(); // Serialize the form data

                $.ajax({
                    url: `{{ route('transport.chauffeur.update', ['id' => '__ID__']) }}`.replace('__ID__', chauffeurId), // Dynamic URL with the chauffeur ID
                    type: 'PUT', // Use PUT method for updating
                    data: formData,
                    success: function(response) {
                        // Handle success response
                        $('#editChauffeurModal').modal('hide'); // Hide the modal
                        table.ajax.reload(); // Reload the DataTable
                        alert('Chauffeur mis à jour avec succès.');
                    },
                    error: function(xhr) {
                        console.error('Error updating chauffeur data:', xhr);
                        alert('Une erreur est survenue lors de la mise à jour du chauffeur.');
                    }
                });
            });

        });
    </script>
@endsection