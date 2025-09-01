@extends('AGENCE_CHINE.layouts.agent')

@section('content')
    <h1>Liste des Clients Actifs</h1>

    {{-- Formulaire d'envoi de message global --}}
    <div class="card mb-4">
        <div class="card-header">
            Envoyer un Message Global à tous les Clients Actifs
        </div>
        <div class="card-body">
            <form action="{{ route('chine_client.sendGlobalMessage') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="global_message">Message :</label>
                    <textarea class="form-control" id="global_message" name="global_message" rows="3" required maxlength="160"></textarea>
                    <small class="form-text text-muted">Max 160 caractères pour un SMS.</small>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Envoyer à tous les clients</button>
            </form>
        </div>
    </div>

    <table id="clientsTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Téléphone</th>
                <th>Email</th>
                <th>Type Client</th>
                <th>Date de Création</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($uniqueClients as $client)
                <tr>
                    <td>{{ $client->nom }}</td>
                    <td>{{ $client->prenom }}</td>
                    <td>{{ $client->tel }}</td>
                    <td>{{ $client->email }}</td>
                    <td>{{ $client->type }}</td> {{-- Utilisez $client->type ici --}}
                    <td>{{ $client->created_at }}</td>
                    <td>
                        {{-- Bouton pour envoyer un message --}}
                        <button type="button" 
                                class="btn btn-sm btn-info me-1 message-client-btn"
                                data-bs-toggle="modal" 
                                data-bs-target="#sendMessageModal"
                                data-client-id="{{ $client->id }}">
                            <i class="bi bi-chat-dots"></i> Message
                        </button>

                        {{-- Bouton Activer / Désactiver --}}
                        <form action="{{ route('chine_client.toggleActivation', ['id' => $client->id]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" 
                                    class="btn btn-sm {{ $client->is_active ? 'btn-danger' : 'btn-success' }}">
                                <i class="bi {{ $client->is_active ? 'bi-person-x' : 'bi-person-check' }}"></i>
                                {{ $client->is_active ? 'Désactiver' : 'Activer' }}
                            </button>
                        </form>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Modal pour envoyer un message à un client spécifique --}}
    <div class="modal fade" id="sendMessageModal" tabindex="-1" aria-labelledby="sendMessageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="sendMessageForm" action="" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="sendMessageModalLabel">Envoyer un Message au Client</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="client_tel_modal" id="clientTelModal">
                        <div class="form-group">
                            <label for="message_content">Message :</label>
                            <textarea class="form-control" id="message_content" name="message" rows="4" required maxlength="160"></textarea>
                            <small class="form-text text-muted">Max 160 caractères pour un SMS.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Envoyer le Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#clientsTable').DataTable();

            $('#sendMessageModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var clientId = button.data('client-id'); // on change data-client-tel en data-client-id

                var modal = $(this);
                modal.find('#clientTelModal').val(clientId);
                modal.find('#sendMessageForm').attr('action', '{{ route('chine_client.sendMessage', ['id' => '__ID__']) }}'.replace('__ID__', clientId));
            });

        });
    </script>
@endsection