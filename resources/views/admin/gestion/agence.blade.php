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
                                        Ajouter une agence
                                    </button>
                                </div><br>
                                <table id="agence-table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>NOM AGENCE</th>
                                            <th>ADRESSE</th>
                                            <th>PAYS</th>
                                            <th>DEVIS</th>
                                            <th>PRIX AU KG</th>
                                            <th>ACTIONS</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                </div>
            </div>
        </div>
        {{--  --}}
        <div class="modal fade" id="ajouter_agent" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <form action="{{route('agence.store')}}" method="post">
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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nom">Nom de l'agence:</label>
                                            <input type="text" name="nom_agence" value="{{ old('nom_agence') }}" class="form-control" id="nom_agence">
                                            @error('nom')
                                            <div class="text-danger">
                                                <p>{{$message}}</p>
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Adresse:</label>
                                            <input type="email" name="adresse_agence" class="form-control" value="{{ old('adresse_agence') }}" id="adresse_agence">
                                            @error('email')
                                            <div class="text-danger">
                                                <p>{{$message}}</p>
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Pays:</label>
                                            <select name="pays_agence" class="form-control" value="{{ old('pays_agence') }}" id="pays_agence">
                                                <option value="">Selectionnez un pays</option>
                                                <option value="Côte d'Ivoire">CI</option>
                                            </select>
                                            @error('email')
                                            <div class="text-danger">
                                                <p>{{$message}}</p>
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="prenom">Dévise:</label>
                                            <input type="text" name="devise_agence" value="{{ old('devise_agence') }}" class="form-control" id="devise_agence">
                                        </div>
                                        <div class="form-group">
                                            <label >Prix au KG:</label>
                                            <input type="text" name="prix_au_kg" class="form-control" id="prix_au_kg" value="{{ old('prix_au_kg') }}">
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
    </section>
    <script>
        $(document).ready(function () {
            $('#agence-table').DataTable({                
                processing: true,
                serverSide: true,
                        language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
                },
                ajax: '{{ route('agence.getAgence') }}',
                columns: [
                    { data: 'nom_agence', name: 'nom_agence' },
                    { data: 'adresse_agence', name: 'adresse_agence' },
                    { data: 'pays_agence', name: 'pays_agence' },
                    { data: 'devise_agence', name: 'devise_agence' },
                    { data: 'prix_au_kg', name: 'prix_au_kg' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                    
                ]
            });

        });

    $(document).on('click', '.delete-btn', function () {
    const id = $(this).data('id');
    const url = $(this).data('url');

    if (confirm('Voulez-vous vraiment supprimer cette agence ?')) {
        $.ajax({
            url: url,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (response) {
                alert('Agence supprimée avec succès.');
                $('#datatable-agences').DataTable().ajax.reload(); 
            },
            error: function (xhr) {
                alert('Une erreur est survenue lors de la suppression.');
            }
        });
    }
});

        </script>
      
@endsection
