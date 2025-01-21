@extends('admin.layouts.admin')
@section('content-header')
@section('content')
        <div class="row justify-content-center">
            <div class="col-md-12">
                        <h4 class="text-center">Liste des chauffeur</h4>
                            <div class="text-right">
                                <button type="button" style="color: #fff;" class="btn gradient-orange-blue" data-bs-toggle="modal" data-bs-target="#ajouter_chauffeur">
                                    Ajouter un chauffeur
                                </button>
                            </div><br>
                            <table id="chauffeur-table" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nom </th>
                                        <th>Prénom</th>
                                        <th>Contact</th>
                                        <th>Email</th>
                                        <th>Agence</th>
                                        <th>ACTIONS</th>
                                    </tr>
                                </thead>
                            </table>
            </div>
        </div>
    {{--  --}}
    <div class="modal fade" id="ajouter_chauffeur" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                <!-- Prénom du chauffeur -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="prenom_chauffeur">Prénom du chauffeur:</label>
                                        <input type="text" name="prenom_chauffeur" 
                                               value="{{ old('prenom_chauffeur') }}" 
                                               class="form-control" 
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
                                <!-- Contact du chauffeur -->
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
                            </div>
                    
                            <div class="row">
                                <!-- Agence -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="agence_expedition">Agence:</label>
                                        <select name="agence_expedition" id="agence_expedition" class="form-control">
                                            <option value="" disabled selected>-- Sélectionnez l'agence d'expédition --</option>
                                            @foreach ($agences as $agence)
                                                <option value="{{ $agence->nom_agence }}" {{ old('agence_expedition') == $agence->id ? 'selected' : '' }}>
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
                    {{-- <input type="hidden" name="agence_id" value="{{ $agence->id }}"> --}}
                    {{-- @dd($agence->id) --}}
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
                    url: "{{ asset('js/fr-FR.json') }}" // Chemin local vers le fichier
                },
                ajax: '{{ route('transport.get.chauffeur.list') }}',
                columns: [
                    { data: 'nom', name: 'nom' },
                    { data: 'prenom', name: 'prenom' },
                    { data: 'tel', name: 'tel' },
                    { data: 'email', name: 'email' },
                    { data: 'agence', name: 'agence' },
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
