@extends('admin.layouts.admin')
@section('content-header')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<section class="py-3">
            <div class="row justify-content-center">
                <div class="col-md-12">
                            <h2>Liste des utilisateurs</h2>
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
                                            <th>DEVIS</th>
                                            <th>ACTIONS</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                </div>
            </div>
</section>
            {{--  --}}
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
                                            <label for="nom">Nom de l'agence:</label>
                                            <input type="text" name="nom_agence" value="{{ old('nom_agence') }}" class="form-control" id="nom_agence">
                                            @error('nom')
                                            <div class="text-danger">
                                                <p>{{$message}}</p>
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Adresse:</label>
                                            <input type="email" name="adresse_agence" class="form-control" value="{{ old('adresse_agence') }}" id="adresse_agence">
                                            @error('email')
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
                                            <label for="prenom">Dévise:</label>
                                            <input type="text" name="devise_agence" value="{{ old('devise_agence') }}" class="form-control" id="devise_agence">
                                        </div>
                                        
                                        {{-- <div class="form-group">
                                            <label >Prix au KG:</label>
                                            <input type="text" name="prix_au_kg" class="form-control" id="prix_au_kg" value="{{ old('prix_au_kg') }}">
                                        </div> --}}
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                                <label for="email">Pays:</label>
                                                <select name="pays_agence" class="form-control" value="{{ old('pays_agence') }}" id="pays_agence">
                                                    <option value="">Selectionnez un pays</option>
                                                    <option value="Côte d'Ivoire">CI</option>
                                                    <option value="France">France</option>
                                                    <option value="Chine">Chine</option>
                                                </select>
                                                @error('email')
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
        
 

<!-- Modal modifView -->
<div class="modal fade" id="modifModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Détails</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nom_agence" class="form-label">Nom Agence</label>
                            <input type="text" id="nom_agence" class="form-control" placeholder="Nom Agence" required>
                        </div>
                        <div class="col-md-6">
                            <label for="adresse" class="form-label">Adresse</label>
                            <input type="text" id="adresse" class="form-control" placeholder="Adresse" required>
                        </div>
                        <div class="col-md-12">
                            <label for="pays_agence" class="form-label">Pays</label>
                            <textarea id="pays_agence" class="form-control" placeholder="Pays agence" rows="3" required></textarea>
                        </div>
                        <div class="col-md-3">
                            <label for="devise" class="form-label">Dévise</label>
                            <input type="number" id="devise" class="form-control" placeholder="Dévise" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-success add-product">Valider</button>
                        </div>
                    </div>
            </div>
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

//  // Gestion de l'édition
//  $(document).on('click', '.edit-btn', function () {
//             const id = $(this).data('id');
//             console.log(id);
            
//             $.get(`/agences/${id}/edit`, function (data) {
//                 $('#editModal #nom_agence').val(data.nom_agence);
//                 $('#editModal #adresse').val(data.adresse_agence);
//                 $('#editModal #pays_agence').val(data.pays_agence);
//                 $('#editModal #devise').val(data.devise_agence);
//                 $('#editModal #prix_au_kg').val(data.prix_au_kg);
//             });
//         });

//         // Gestion de la modification
//         $(document).on('click', '.modif-btn', function () {
//             const id = $(this).data('id');
//             $.get(`/agences/${id}`, function (data) {
//                 $('#modifModal #nom_agence').val(data.nom_agence);
//                 $('#modifModal #adresse').val(data.adresse_agence);
//                 $('#modifModal #pays_agence').val(data.pays_agence);
//                 $('#modifModal #devise').val(data.devise_agence);
//                 $('#modifModal #prix_au_kg').val(data.prix_au_kg);
//             });
//         });


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
