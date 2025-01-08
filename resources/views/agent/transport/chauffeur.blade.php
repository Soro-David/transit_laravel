@extends('agent.layouts.agent')
@section('content-header')
@section('content')
   <div class="container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                        <h2>Liste des chauffeur</h2>
                        <div class="container">
                            <div class="text-right">
                                <button type="button" style="color: #fff;" class="btn gradient-orange-blue" data-bs-toggle="modal" data-bs-target="#ajouter_chauffeur">
                                    Ajouter un chauffeur
                                </button>
                            </div><br>
                            <table id="chauffeur-table" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nom </th>
                                        <th>Contact </th>
                                        <th>Email</th>
                                        <th>Agence</th>
                                        <th>ACTIONS</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
            </div>
        </div>
    </div>
    {{--  --}}
    <div class="modal fade" id="ajouter_chauffeur" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <form action="#" method="post">
            @csrf
            @method('POST')
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between align-items-center">
                        <h5 class="text-center flex-grow-1 m-0">Information du chauffeur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nom_chauffeur">Nom du chauffeur:</label>
                                        <input type="text" name="nom_chauffeur" 
                                               value="{{ old('nom_chauffeur') }}" 
                                               class="form-control" 
                                               id="nom_chauffeur">
                                        @error('nom_chauffeur')
                                        <div class="text-danger">
                                            <p>{{ $message }}</p>
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email_chauffeur">Email Chauffeur:</label>
                                        <input type="email" name="email_chauffeur" 
                                               class="form-control" 
                                               value="{{ old('email_chauffeur') }}" 
                                               id="email_chauffeur">
                                        @error('email_chauffeur')
                                        <div class="text-danger">
                                            <p>{{ $message }}</p>
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tel_chauffeur">Contact du chauffeur:</label>
                                        <input type="text" name="tel_chauffeur" 
                                               value="{{ old('tel_chauffeur') }}" 
                                               class="form-control" 
                                               id="tel_chauffeur">
                                        @error('tel_chauffeur')
                                        <div class="text-danger">
                                            <p>{{ $message }}</p>
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nom_agence">Agence:</label>
                                        <select name="nom_agence" 
                                                class="form-control" 
                                                value="{{ old('nom_agence') }}" 
                                                id="nom_agence">
                                            <option value="" disabled selected>Selectionnez une Agence</option>
                                            <option value="Côte d'Ivoire">CI</option>
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
        </form>
    </div>        
</section>
<script>
        $(document).ready(function () {
            $('#chauffeur-table').DataTable({                
                processing: true,
                serverSide: true,
                        language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
                },
                ajax: '{{ route('transport.get.chauffeur.list') }}',
                columns: [
                    { data: 'nom_agence', name: 'nom_agence' },
                    { data: 'adresse_agence', name: 'email_chauffeur' },
                    { data: 'pays_agence', name: 'tel_chauffeur' },
                    { data: 'prix_au_kg', name: 'nom_agence' },
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
