@extends('admin.layouts.admin')

@section('content-header')

@section('content')
<section class="py-3">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">
                            <h2>Liste des utilisateurs</h2>
                            <div class="container">
                                <div class="text-right">
                                    <button type="button" style="color: #fff;" class="btn gradient-orange-blue" data-bs-toggle="modal" data-bs-target="#ajouter_agent">
                                        Ajouter un agent
                                    </button>
                                </div><br>
                                <table id="users-table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Rôle</th>
                                            <th>Date de création</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                </div>
            </div>
        </div>
        {{--  --}}
        <div class="modal fade" id="ajouter_agent" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <form action="{{route('managers.store')}}" method="post">
                @csrf
                @method('POST')
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between align-items-center">
                            <h5 class="text-center flex-grow-1 m-0">Information de l'Expéditeur</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="container">
                                <div class="row">
                                    <!-- Colonne gauche -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nom">Nom:</label>
                                            <input type="text" name="nom" value="{{ old('nom') }}" class="form-control" id="nom">
                                            @error('nom')
                                            <div class="text-danger">
                                                <p>{{$message}}</p>
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email:</label>
                                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" id="email">
                                            @error('email')
                                            <div class="text-danger">
                                                <p>{{$message}}</p>
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="role">Rôle:</label>
                                            <select name="role" value="{{ old('role') }}" class="form-control" id="role">
                                                <option value="admin">Admin</option>
                                                <option value="user">Utilisateur</option>
                                                <option value="provider">Fournisseur</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Colonne droite -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="prenom">Prénom:</label>
                                            <input type="text" name="prenom" value="{{ old('prenom') }}" class="form-control" id="prenom">
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Mot de passe:</label>
                                            <input type="password" name="password" class="form-control" id="password">
                                        </div>
                                        <div class="form-group">
                                            <label for="agence">Agence</label>
                                            <select name="agence_id" id="agence_id" class="form-control">
                                                <option value="" disabled selected>-- Sélectionnez une agence --</option>
                                                @foreach ($agences as $agence)
                                                    <option value="{{$agence->id}}">{{$agence->nom_agence}}</option>
                                                @endforeach
                                            </select>
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
                {{-- <input type="hidden" name="agence_id" id="agence_id"> --}}
            </form>
        </div>        
    </section>
    <script>
        $(document).ready(function () {
            $('#users-table').DataTable({                
                processing: true,
                serverSide: true,
                        language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
                },
                ajax: '{{ route('managers.getUsers') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'first_name', name: 'first_name' },
                    { data: 'email', name: 'email' },
                    { data: 'role', name: 'role' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                    
                ]
            });

        });
        </script>
      
@endsection
