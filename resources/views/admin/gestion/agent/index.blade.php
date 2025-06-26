@extends('admin.layouts.admin')

@section('content-header')
    {{-- Le contenu de content-header doit être ici, pas la meta tag --}}
@endsection

@section('content')
{{-- La meta tag CSRF est mieux placée dans le layout principal (layouts/admin.blade.php) dans la section <head>, mais si elle doit être ici, c'est OK. --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<section class="py-3">
    <div class="container-fluid"> {{-- Utiliser container-fluid pour occuper toute la largeur si besoin --}}
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h2>Liste des agents</h2>
                <div class="text-right mb-3"> {{-- mb-3 pour ajouter une marge en bas --}}
                    <button type="button" style="color: #fff;" class="btn gradient-orange-blue" data-bs-toggle="modal" data-bs-target="#ajouter_agent">
                        Ajouter un agent
                    </button>
                </div>
                
                <table id="users-table" class="table table-bordered table-striped"> {{-- table-striped pour un meilleur style --}}
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Agence</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div> 
</section>

{{-- Modal pour ajouter un agent (pas de changement majeur ici, mais j'ai nettoyé un peu) --}}
<div class="modal fade" id="ajouter_agent" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{ route('managers.store') }}" method="post">
        @csrf
        {{-- @method('POST') n'est pas nécessaire pour une route de type store --}}
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Information de l'agent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <!-- Colonne gauche -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nom">Nom:</label>
                                    <input type="text" name="nom" value="{{ old('nom') }}" class="form-control" id="nom" required>
                                    @error('nom')<div class="text-danger"><p>{{ $message }}</p></div>@enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="email">Email:</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" id="email" required>
                                    @error('email')<div class="text-danger"><p>{{ $message }}</p></div>@enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="role">Rôle:</label>
                                    <input type="text" name="role" class="form-control" value="agent" readonly>
                                </div>
                            </div>
                            <!-- Colonne droite -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="prenom">Prénom:</label>
                                    <input type="text" name="prenom" value="{{ old('prenom') }}" class="form-control" id="prenom" required>
                                    @error('prenom')<div class="text-danger"><p>{{ $message }}</p></div>@enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="password">Mot de passe:</label>
                                    <input type="password" name="password" class="form-control" id="password" required>
                                    @error('password')<div class="text-danger"><p>{{ $message }}</p></div>@enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="agence_id">Agence</label>
                                    <select name="agence_id" id="agence_id" class="form-control" required>
                                        <option value="" disabled selected>-- Sélectionnez une agence --</option>
                                        @foreach ($agences as $agence)
                                            <option value="{{ $agence->id }}">{{ $agence->nom_agence }}</option>
                                        @endforeach
                                    </select>
                                    @error('agence_id')<div class="text-danger"><p>{{ $message }}</p></div>@enderror
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

{{-- Le script doit être dans une section dédiée comme @section('scripts') pour une meilleure organisation --}}
{{-- @push('scripts') --}}
<script>
$(document).ready(function () {
    // Initialisation de la DataTable
    var table = $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        language: {
            url: "{{ asset('js/fr-FR.json') }}"
        },
        ajax: '{{ route("agence.get.agent") }}',
        columns: [
            // L'ordre doit correspondre à celui des <th>
            { data: 'nom', name: 'nom' },
            { data: 'prenom', name: 'prenom' },
            { data: 'email', name: 'email' },
            { data: 'agence', name: 'agence.nom_agence' }, // <<< NOUVELLE COLONNE
            {
                data: 'created_at',
                name: 'created_at',
                render: function(data, type, row) {
                    if (!data) return '';
                    var date = new Date(data);
                    // Formatage de la date en JJ/MM/AA
                    return ('0' + date.getDate()).slice(-2) + '/' + ('0' + (date.getMonth() + 1)).slice(-2) + '/' + date.getFullYear().toString().slice(-2);
                }
            },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Gestion de la suppression (déléguée au corps du tableau pour les nouvelles lignes)
    $('#users-table tbody').on('click', '.delete-btn', function () {
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
                            'L\'utilisateur a été supprimé avec succès.',
                            'success'
                        );
                        table.ajax.reload(); // Recharger la table
                    },
                    error: function (xhr) {
                        Swal.fire(
                            'Erreur !',
                            'Une erreur est survenue lors de la suppression. (' + xhr.statusText + ')',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>
{{-- @endpush --}}
@endsection