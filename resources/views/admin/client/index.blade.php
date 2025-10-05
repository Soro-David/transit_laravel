@extends('admin.layouts.admin')

@section('content')
<h1>Envoi de Messages aux Clients</h1>

{{-- ... (partie supérieure du formulaire, inchangée) ... --}}

<div class="card mb-4">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="messagingTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="grouped-tab" data-bs-toggle="tab" data-bs-target="#grouped" type="button" role="tab" aria-controls="grouped" aria-selected="true">
                    Message Groupé / Par Sélection
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="messagingTabsContent">
            <div class="tab-pane fade show active" id="grouped" role="tabpanel" aria-labelledby="grouped-tab">

                {{-- Filtres --}}
                <h5><i class="bi bi-funnel"></i> Filtrer les clients par Conteneur ou Vol</h5>
                <div class="row g-3 align-items-center bg-light p-3 rounded mb-4 border">
                    <div class="col-md-5">
                        <label for="container_ref_filter" class="form-label">Référence du Conteneur :</label>
                        <select class="form-select" id="container_ref_filter">
                            <option value="" selected>Tous les conteneurs</option>
                            @foreach($containerReferences as $ref)
                                <option value="{{ $ref }}">{{ $ref }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="flight_ref_filter" class="form-label">Référence du Vol :</label>
                        <select class="form-select" id="flight_ref_filter">
                            <option value="" selected>Tous les vols</option>
                            @foreach($flightReferences as $ref)
                                <option value="{{ $ref }}">{{ $ref }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button id="resetFilters" class="btn btn-secondary w-100">
                            <i class="bi bi-arrow-clockwise"></i> Réinitialiser
                        </button>
                    </div>
                </div>
                <hr>

                {{-- Formulaire message groupé --}}
                <form action="{{ route('message.sendByFilter') }}" method="POST">
                    @csrf
                    <input type="hidden" name="reference_contenaire" id="form_container_ref">
                    <input type="hidden" name="reference_vol" id="form_flight_ref">

                    <div class="mb-3">
                        <label for="grouped_message" class="form-label">Votre Message :</label>
                        <textarea class="form-control" id="grouped_message" name="message" rows="4" required maxlength="160" placeholder="Écrivez votre message ici..."></textarea>
                        <small class="form-text text-muted">Le message sera envoyé à tous les clients actuellement affichés dans la liste ci-dessous.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Type de destinataires :</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="recipient_type" id="send_to_expediteurs" value="expediteurs" checked>
                            <label class="form-check-label" for="send_to_expediteurs">Clients Expéditeurs</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="recipient_type" id="send_to_destinataires" value="destinataires">
                            <label class="form-check-label" for="send_to_destinataires">Clients Destinataires</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="bi bi-people-fill"></i> Envoyer aux clients filtrés</button>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- ... (Modal pour message individuel, inchangé) ... --}}
<div class="modal fade" id="sendIndividualMessageModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Envoyer un message à <span id="client_name_modal" class="fw-bold"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="sendIndividualMessageForm" action="{{ route('message.sendIndividual') }}" method="POST">
                @csrf
                <input type="hidden" name="client_id" id="individual_client_id">
                <input type="hidden" name="client_type" id="individual_client_type">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="individual_message" class="form-label">Message :</label>
                        <textarea class="form-control" id="individual_message" name="message" rows="4" required maxlength="160"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Envoyer</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- Table des clients --}}
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div><i class="bi bi-list-ul"></i> Liste des Clients (Expéditeurs & Destinataires)</div>
        <div class="text-muted small"><span id="clientCount">Chargement...</span> clients trouvés</div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="allClientsTable" class="table table-bordered table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Téléphone</th>
                        <th>Type</th>
                        <th class="text-center">Statut</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
{{-- SweetAlert2 pour de plus jolies notifications --}}
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.css" rel="stylesheet">
@endpush

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.js"></script>
{{-- SweetAlert2 pour de plus jolies notifications --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    var allClientsTable = $('#allClientsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '{{ route("message.clients.data") }}',
            data: function(d) {
                d.reference_contenaire = $('#container_ref_filter').val();
                d.reference_vol = $('#flight_ref_filter').val();
            }
        },
        columns: [
            { data: 'nom', name: 'nom' },
            { data: 'prenom', name: 'prenom' },
            { data: 'tel', name: 'tel' },
            { data: 'type_client', name: 'type', orderable: false, searchable: false },
            { data: 'statut', name: 'statut', className: 'text-center', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.12.1/i18n/fr-FR.json"
        },
        order: [[0, 'asc']],
        drawCallback: function(settings) {
            var api = this.api();
            $('#clientCount').text(api.page.info().recordsDisplay);
        }
    });

    // ... (Logique des filtres, inchangée) ...
    function syncFormFilters() {
        $('#form_container_ref').val($('#container_ref_filter').val());
        $('#form_flight_ref').val($('#flight_ref_filter').val());
    }
    $('#container_ref_filter, #flight_ref_filter').on('change', function() {
        if ($(this).attr('id') === 'container_ref_filter') {
            $('#flight_ref_filter').val('');
        } else {
            $('#container_ref_filter').val('');
        }
        allClientsTable.ajax.reload();
        syncFormFilters();
    });
    $('#resetFilters').on('click', function() {
        $('#container_ref_filter, #flight_ref_filter').val('');
        allClientsTable.ajax.reload();
        syncFormFilters();
    });
    syncFormFilters();


    // Gestion du clic sur le bouton "Message" pour remplir le modal
    $('#allClientsTable tbody').on('click', '.message-individual-btn', function() {
        var button = $(this);
        $('#individual_client_id').val(button.data('client-id'));
        $('#individual_client_type').val(button.data('client-type'));
        $('#client_name_modal').text(button.data('client-name'));
    });
    $('#sendIndividualMessageModal').on('hidden.bs.modal', function() {
        $('#sendIndividualMessageForm')[0].reset();
    });

    // *** NOUVEAU : Logique pour bloquer/débloquer un client ***
    $('#allClientsTable tbody').on('click', '.toggle-block-btn', function() {
        var button = $(this);
        var userId = button.data('user-id');
        var userName = button.data('user-name');
        var isBlocking = button.text().trim() === 'Bloquer';
        var url = "{{ route('client.toggleBlock', ['user' => ':id']) }}".replace(':id', userId);


        Swal.fire({
            title: `Êtes-vous sûr de vouloir ${isBlocking ? 'bloquer' : 'débloquer'} ${userName} ?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: `Oui, ${isBlocking ? 'bloquer' : 'débloquer'} !`,
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Succès!', response.message, 'success');
                            // Recharger la table pour mettre à jour l'affichage
                            allClientsTable.ajax.reload(null, false); 
                        } else {
                            Swal.fire('Erreur!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        var errorMsg = xhr.responseJSON && xhr.responseJSON.message 
                            ? xhr.responseJSON.message 
                            : 'Une erreur est survenue.';
                        Swal.fire('Erreur!', errorMsg, 'error');
                    }
                });
            }
        });
    });
});
</script>