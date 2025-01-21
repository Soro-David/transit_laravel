@extends('admin.layouts.admin')

@section('content-header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    <section class="py-3">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h2>Liste des Clients</h2>
                <div class="table-responsive">
                    <table id="clientsTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Téléphone</th>
                                <th>E-mail</th>
                                <th>Adresse</th>
                                <th>Type Client</th>
                                <th>Agence</th>
                                <th>Date inscription</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <script>
        $(document).ready(function() {
            $('#clientsTable').DataTable({
                processing: true,
                serverSide: true,
                language: {
                    url: "{{ asset('js/fr-FR.json') }}" // Chemin local vers le fichier
                },
                ajax: "{{ route('client.clients.data') }}",
                columns: [
                    { data: 'nom', name: 'nom' },
                    { data: 'prenom', name: 'prenom' },
                    { data: 'telephone', name: 'telephone' },
                    { data: 'email', name: 'email' },
                    { data: 'adresse', name: 'adresse' },
                    { data: 'type_client', name: 'type_client' },
                    { data: 'agence', name: 'agence' },
                    { data: 'created_at', name: 'created_at',
                render: function(data, type, row) {
                    // Vérifiez si la date existe et la formater
                    if (data) {
                        var date = new Date(data);
                        // Retourne la date au format aa/mm/jj
                        var day = ('0' + date.getDate()).slice(-2);  // Ajoute un zéro si jour < 10
                        var month = ('0' + (date.getMonth() + 1)).slice(-2);  // +1 car les mois commencent à 0
                        var year = date.getFullYear().toString().slice(-2);  // On garde les deux derniers chiffres de l'année
                        return day + '/' + month + '/' + year;
                    }
                    return data;  // Si la date est vide, on retourne la donnée brute
                } }
                ]
            });
        });
    </script>
@endsection