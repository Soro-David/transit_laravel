@extends('customer.layouts.index')

@section('content-header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('content')
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<section class="py-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                    <h4 class="text-left mt-4">Historique des devis validés</h4>
                    <p class="text-muted">Liste de tous vos devis qui ont été validés.</p>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <div class="form-group">
                            <input type="text" class="form-control" id="searchInput" placeholder="Rechercher...">
                        </div>
                        <button class="btn btn-primary" onclick="window.location.reload()">
                            <i class="fas fa-sync-alt"></i> Actualiser
                        </button>
                    </div>

                    <div id="products-container">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Référence Devis</th>
                                        <th>Nombre de Colis</th>
                                        <th>Agence Expédition</th>
                                        <th>Expéditeur</th>
                                        <th>Contact Exp.</th>
                                        <th>Agence Destination</th>
                                        <th>Montant</th>
                                        <th>Statut</th>
                                        <th>Date Modification</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="devisTableBody">
                                    @forelse($devis as $item)
                                        <tr id="devis-row-{{ $item->reference }}">
                                            <td><strong>{{ $item->reference ?? 'N/A' }}</strong></td>
                                            <td class="text-center">{{ $item->items->sum('quantite_colis') ?? '0' }}</td>
                                            <td>{{ $item->agence_expedition ?? 'N/A' }}</td>
                                            <td>{{ ($item->nom_expediteur ?? '') . ' ' . ($item->prenom_expediteur ?? '') }}</td>
                                            <td>{{ $item->tel_expediteur ?? 'N/A' }}</td>
                                            <td>{{ $item->agence_destination ?? 'N/A' }}</td>
                                            <td class="text-right"><strong>{{ number_format($item->montant ?? 0, 0, ',', ' ') }} {{ $item->devise }}</strong></td>
                                            <td class="text-center">
                                                <span class="badge badge-success">{{ ucfirst($item->etat) }}</span>
                                            </td>
                                            <td class="text-center">
                                                {{ $item->updated_at ? $item->updated_at->format('d/m/Y H:i') : 'N/A' }}
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    @php
                                                        $etatLower = strtolower($item->etat ?? '');
                                                        $isConfirmed = in_array($etatLower, ['confirmé','confirme','confirmed']);
                                                    @endphp
                                            
                                                    @if(!$isConfirmed)
                                                        <button class="btn btn-success btn-sm btn-confirm-devis"
                                                                data-reference="{{ $item->reference }}"
                                                                title="Confirmer la réception du colis">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-success btn-sm" disabled title="Devis confirmé">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif
                                            
                                                    <button class="btn btn-danger btn-sm btn-cancel-devis"
                                                            data-reference="{{ $item->reference }}"
                                                            title="Annuler ce devis">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                                    Aucun devis validé trouvé.
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Recherche simple en temps réel
    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#devisTableBody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // NOUVELLE APPROCHE : Création d'un modal personnalisé
    function showRetraitModal(reference) {
        const url = `/customer/customer_colis/devis/${reference}/confirm`;
        
        // Créer un modal Bootstrap personnalisé
        const modalHtml = `
            <div class="modal fade" id="retraitModal" tabindex="-1" aria-labelledby="retraitModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="retraitModalLabel">Confirmation du devis</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <p class="mb-4">Veuillez choisir le mode de retrait pour le devis <strong>${reference}</strong>.</p>
                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <div class="mode-card" data-mode="retrait">
                                        <div class="mode-icon mb-3">
                                            <i class="fas fa-home fa-3x text-primary"></i>
                                        </div>
                                        <div class="mode-label fw-bold">Retrait à domicile</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mode-card" data-mode="envoi">
                                        <div class="mode-icon mb-3">
                                            <i class="fas fa-building fa-3x text-primary"></i>
                                        </div>
                                        <div class="mode-label fw-bold">Envoi à l'agence</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Retour</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Ajouter le modal au body
        $('body').append(modalHtml);
        
        // Afficher le modal
        const modal = new bootstrap.Modal(document.getElementById('retraitModal'));
        modal.show();

        // Gérer les clics sur les cartes de mode
        $('.mode-card').on('click', function() {
            const selectedMode = $(this).data('mode');
            const modeLabel = $(this).find('.mode-label').text();
            
            // Fermer le premier modal
            modal.hide();
            
            // Afficher la confirmation SweetAlert2
            Swal.fire({
                title: 'Confirmer votre choix',
                html: `
                    <p>Vous avez choisi : <strong>${modeLabel}</strong></p>
                    <p>Êtes-vous sûr de vouloir confirmer le devis <strong>${reference}</strong> ?</p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Oui, confirmer !',
                cancelButtonText: 'Non, modifier'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Envoyer requête AJAX
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: csrfToken,
                            mode_de_retrait: selectedMode
                        },
                        success: function(response) {
                            Swal.fire(
                                'Confirmé !',
                                response.success ?? 'Le devis a été confirmé.',
                                'success'
                            ).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Erreur !',
                                'Une erreur est survenue lors de la confirmation.',
                                'error'
                            );
                        }
                    });
                } else {
                    // Re-afficher le modal de sélection si l'utilisateur veut modifier
                    showRetraitModal(reference);
                }
            });
        });

        // Nettoyer le modal quand il est fermé
        $('#retraitModal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }

    // Gestion du clic sur le bouton "Confirmer"
    $(document).on('click', '.btn-confirm-devis', function() {
        const reference = $(this).data('reference');
        showRetraitModal(reference);
    });

    // Gestion du clic sur le bouton "Annuler"
    $(document).on('click', '.btn-cancel-devis', function() {
        const reference = $(this).data('reference');
        const url = `/customer/customer_colis/devis/${reference}/cancel`;

        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: `Vous êtes sur le point d'annuler le devis ${reference}.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, annuler !',
            cancelButtonText: 'Non, garder'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: { _token: csrfToken },
                    success: function(response) {
                        Swal.fire(
                            'Annulé !',
                            response.success ?? 'Le devis a été annulé.',
                            'success'
                        );
                        $('#devis-row-' + reference).fadeOut(500, function() { $(this).remove(); });
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Erreur !',
                            'Une erreur est survenue lors de l\'annulation.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>

<style>
body {
    background-color: #f7f7f7;
}
.badge-success {
    background-color: #28a745;
}
.btn-group .btn {
    margin-right: 5px;
}

/* Styles pour les cartes de mode */
.mode-card {
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 25px 15px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fff;
    user-select: none;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.mode-card:hover {
    border-color: #28a745;
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    background-color: #f8fff9;
}

.mode-card:active {
    transform: translateY(-2px);
}

.mode-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #e9ecef;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.mode-card:hover .mode-icon {
    border-color: #28a745;
    background-color: #ffffff;
    color: white;
}

.mode-label {
    font-size: 1rem;
    font-weight: 600;
    color: #495057;
    transition: all 0.3s ease;
    margin-top: 10px;
}

.mode-card:hover .mode-label {
    color: #28a745;
}

/* Petite amélioration visuelle table */
.table th {
    background-color: #343a40;
    color: white;
    border-color: #454d55;
}

/* Responsive */
@media (max-width: 576px) {
    .mode-card {
        padding: 20px 10px;
    }
    .mode-icon {
        width: 60px;
        height: 60px;
    }
}
</style>
@endsection