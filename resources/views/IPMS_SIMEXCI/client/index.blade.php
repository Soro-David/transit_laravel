@extends('admin.layouts.admin')

@section('content')
    <h1>Liste des Clients Uniques</h1>

    <table id="clientsTable" class="table table-bordered">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Téléphone</th>
                <th>Email</th>
                <th>Type de Client</th>
                <th>Date de Création</th>
                <th>Actions</th> <!-- Nouvelle colonne -->
            </tr>
        </thead>
        <tbody>
            @foreach($uniqueClients as $client)
                <tr>
                    <td>{{ $client->nom }}</td>
                    <td>{{ $client->prenom }}</td>
                    <td>{{ $client->tel }}</td>
                    <td>{{ $client->email }}</td>
                    <td>{{ $client->type_client }}</td>
                    <td>{{ $client->created_at }}</td>
                    <td>
                        <!-- Liens ou boutons pour les actions -->
                        <a href="{{ route('client.edit', ['nom' => $client->nom, 'prenom' => $client->prenom, 'tel' => $client->tel, 'email' => $client->email]) }}" class="btn btn-sm btn-primary">Éditer</a>

                        <form action="{{ route('client.destroy', ['nom' => $client->nom, 'prenom' => $client->prenom, 'tel' => $client->tel, 'email' => $client->email]) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        $(document).ready(function() {
            $('#clientsTable').DataTable();
        });
    </script>
@endsection