@extends('chauffeur.layouts.index')

@section('title', 'Programme des Colis')

@push('styles')
<!-- Assurez-vous que Bootstrap CSS est chargé -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    /* Votre CSS existant */
    .card-programme {
        border-radius: 0.8rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        border: none;
    }
    .card-programme .card-header {
        background-color: #f39c12;
        color: white;
        border-top-left-radius: 0.8rem;
        border-top-right-radius: 0.8rem;
    }
    .card-programme .card-title {
        font-weight: 600;
    }
    #search-box {
        border-radius: 20px 0 0 20px;
    }
    #search-box:focus {
        border-color: #f39c12;
        box-shadow: 0 0 0 0.2rem rgba(243, 156, 18, 0.25);
    }
    .input-group-append .btn {
        border-radius: 0 20px 20px 0;
    }
    #programmes-table thead {
        background-color: #343a40;
        color: white;
    }
    #programmes-table th,
    #programmes-table td {
        vertical-align: middle;
        padding: 0.9rem 0.75rem;
    }
    .etat-rdv-select {
        min-width: 120px;
    }
    .row-updated {
        animation: highlight 2s ease-out;
    }
    @keyframes highlight {
        0% { background-color: rgba(243, 156, 18, 0.3); }
        100% { background-color: transparent; }
    }
</style>
@endpush

@section('content-header')
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <h1>Programme des Colis</h1>
@endsection

@section('content')
    <div class="p-4"> 
        <div class="card card-programme">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-truck mr-2"></i>Liste des enlèvements</h3>
                <div class="card-tools">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" id="search-box" class="form-control float-right" placeholder="Rechercher par référence...">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-default"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="programmes-table" class="table table-hover table-striped">
                        <thead>
                            <tr class="text-center">
                                <th>Date</th>
                                <th>Reference du colis</th>
                                <th>Nature du Colis</th>
                                <th>Action à faire</th>
                                <th>Client</th>
                                <th>Adresse d'enlèvement</th>
                                <th>Contact</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($programmes as $row)
                                <tr id="programme-row-{{ $row->id }}" class="text-center">
                                    <td>{{ $row->date_programme }}</td>
                                    <td>{{ $row->reference_generee }}</td>
                                    <td>{{ $row->nature_du_colis }}</td>
                                    <td>
                                        @if($row->actions_a_faire == 'depot')
                                            <span class="badge badge-success">Dépôt</span>
                                        @else
                                            <span class="badge badge-info">Récupération</span>
                                        @endif
                                    </td>
                                    <td>{{ $row->nom_expediteur }}</td>
                                    <td>{{ $row->lieu_expedition }}</td>
                                    <td>{{ $row->tel_expediteur }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-primary btn-sm btn-show-etiquette" data-id="{{ $row->id }}">
                                            <i class="fas fa-print"></i> Étiquette
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center p-4">Aucun programme disponible pour le moment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
        
    <!-- MODAL POUR LA CONFIRMATION -->
    <div class="modal fade" id="qrCodeModal" tabindex="-1" role="dialog" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrCodeModalLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div id="loading-spinner" class="spinner-border text-primary" role="status">
                        <span class="sr-only">Chargement...</span>
                    </div>
                    <div id="confirmation-message" style="display: none; font-size: 1.1rem;">
                        {{-- Le message de confirmation sera injecté ici par JavaScript --}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="download-button" disabled>
                        <i class="fas fa-download"></i> Télécharger & Valider
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
   
@push('scripts')
<!-- Chargement correct des dépendances dans l'ordre -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Utiliser jQuery.noConflict() si nécessaire
(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Gérer l'ouverture et le remplissage de la modale de confirmation
        $('body').on('click', '.btn-show-etiquette', function() {
            var programmeId = $(this).data('id');
            
            // Stocker l'ID du programme sur le bouton de téléchargement pour le réutiliser.
            $('#download-button').data('programme-id', programmeId);

            // Réinitialiser la modale à son état initial
            $('#loading-spinner').show();
            $('#confirmation-message').hide().html('');
            $('#download-button').prop('disabled', true);
            $('#qrCodeModalLabel').text("Confirmation");

            // Construire l'URL pour récupérer les infos (quantité, référence)
            var url = "{{ route('chauffeur.etiquette', ['programme' => ':id']) }}";
            url = url.replace(':id', programmeId);
            
            // Ouvrir la modale avec Bootstrap 5
            var modal = new bootstrap.Modal(document.getElementById('qrCodeModal'));
            modal.show();

            // Appel AJAX pour obtenir la quantité et la référence
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    // Créer le message de confirmation
                    var message = 'Vous allez télécharger <strong>' + response.quantite + '</strong> étiquette(s) pour le colis <strong>' + response.reference + '</strong>.<br><small>Cette action validera l\'opération.</small>';
                    
                    // Afficher le message et cacher le spinner
                    $('#confirmation-message').html(message).show();
                    $('#loading-spinner').hide();

                    // Mettre à jour le titre de la modale
                    $('#qrCodeModalLabel').text("Colis : " + response.reference);

                    // Activer le bouton de téléchargement
                    $('#download-button').prop('disabled', false);
                },
                error: function(xhr) {
                    var errorMessage = '<p class="text-danger">Erreur: Impossible de récupérer les informations du programme.</p>';
                    $('#confirmation-message').html(errorMessage).show();
                    $('#loading-spinner').hide();
                    console.error('Erreur AJAX:', xhr.responseText);
                }
            });
        });

        // Gérer le clic final sur "Télécharger & Valider"
        $('#download-button').on('click', function() {
            var programmeId = $(this).data('programme-id');
            if (!programmeId) {
                alert("Erreur: Identifiant du programme introuvable.");
                return;
            }

            // On construit l'URL vers la route de téléchargement
            var downloadUrl = "{{ route('chauffeur.etiquette.download', ['programme' => ':id']) }}";
            downloadUrl = downloadUrl.replace(':id', programmeId);

            // Fermer la modale avec Bootstrap 5
            var modal = bootstrap.Modal.getInstance(document.getElementById('qrCodeModal'));
            modal.hide();

            // On fait disparaître la ligne du tableau avec une animation
            $('#programme-row-' + programmeId).fadeOut(500, function() {
                $(this).remove();
            });

            // On déclenche le téléchargement du PDF
            window.location.href = downloadUrl;
        });

        // Gérer la fermeture de la modale avec le bouton close
        $('.close, .btn-secondary').on('click', function() {
            var modal = bootstrap.Modal.getInstance(document.getElementById('qrCodeModal'));
            modal.hide();
        });
    });
})(jQuery);
</script>
@endpush