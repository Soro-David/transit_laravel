@extends('admin.layouts.admin')
@section('content-header')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        {{-- MODIFIÉ : Titre plus général --}}
        <h1 class="h3 fw-bold text-primary">📑 Liste des devis à traiter</h1>
        <p class="mb-0 text-muted">Gérez et suivez vos devis en cours de validation</p>
    </div>
</div>
@endsection

@section('content')
<section class="py-3">
    <div class="card border-0 shadow-lg rounded-3">
        <div class="card-header bg-gradient-primary text-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fas fa-clock me-2"></i> Devis en attente et validés (EUR)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="productTable" class="table align-middle table-striped mb-0">
                    <thead class="table-light text-uppercase">
                        <tr>
                            <th>Référence</th>
                            <th>Nbr. Colis</th>
                            <th>Expéditeur</th>
                            <th>Téléphone Exp.</th>
                            <th>Agence Dest.</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmation de suppression
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-trash-alt fa-3x text-warning mb-3"></i>
                    <h5 id="deleteMessage">Êtes-vous sûr de vouloir annuler ce devis ?</h5>
                    <p class="text-muted">Cette action est irréversible. Toutes les données associées à ce devis seront définitivement supprimées.</p>
                </div>
                <div class="alert alert-warning" role="alert">
                    <small>
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Attention :</strong> La suppression d'un devis ne peut pas être annulée.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Annuler
                </button>
                <button type="button" class="btn btn-warning" id="confirmDelete">
                    <i class="fas fa-trash me-1"></i>Oui, supprimer
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function () {
    var table = $("#productTable").DataTable({
        responsive: true,
        language: { url: "{{ asset('js/fr-FR.json') }}" },
        ajax: {
            url: '{{ route("colis.get.colis.hold") }}',
            dataSrc: 'data'
        },
        // MODIFICATION DES COLONNES
        columns: [
            { 
                data: 'reference_colis',
                render: function(data) { return '<span class="fw-bold text-primary">' + data + '</span>'; }
            },
            { data: 'nombre_de_colis', className: 'text-center fw-semibold' },
            { render: function (data, type, row) { return row.expediteur_nom + ' ' + row.expediteur_prenom; }},
            { data: 'expediteur_tel', className:'text-muted' },
            // { render: function (data, type, row) { return... }}, // <!-- SUPPRIMÉ -->
            // { data: 'destinataire_tel', className:'text-muted' }, // <!-- SUPPRIMÉ -->
            // { data: 'destinataire_agence', className:'fw-semibold' },
            { 
                data: 'destinataire_agence',
                name: 'destinataire_agence.nom_agence',
                render: function(data, type, row) {
                    if (data === 'IPMS-SIMEX-CI Angre 8ème Tranche') {
                        return 'DS Translog Angré 8ème Tranche';
                    } else if (data === 'IPMS-SIMEX-CI') {
                        return 'DS Translog Carrefour Angré';
                    }
                    return data;
                }
            },
            { 
                data: 'etat',
                render: function(data) {
                    // La couleur du badge s'adapte à l'état "Validé" ou "En attente"
                    let badgeClass = data.toLowerCase() === "validé" ? "bg-success" : "bg-warning text-dark";
                    return '<span class="badge rounded-pill px-3 py-2 '+badgeClass+'">' + data + '</span>';
                }
            },
            { 
                data: 'created_at',
                render: function(data) {
                    return data ? new Date(data).toLocaleDateString('fr-FR') : '';
                }
            },
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                className: 'text-center',
                render: function(data, type, row) {
    // Utiliser la route correcte qui existe
    const showUrl = "{{ route('colis.show', ['coli' => ':id']) }}".replace(':id', row.id);
    const editUrl = "{{ route('colis.hold.edit', ['id' => ':id']) }}".replace(':id', row.id);
    const reference = row.reference_colis.replace(/'/g, "\\'");

    return `
        <a href="${showUrl}" class="btn btn-sm btn-info btn-action me-1" title="Voir les détails">
            <i class="fas fa-eye"></i>
        </a>
        <a href="${editUrl}" class="btn btn-sm btn-warning btn-action me-1" title="Modifier">
            <i class="fas fa-edit"></i>
        </a>
        <button class="btn btn-sm btn-danger btn-action" onclick="confirmDelete(${row.id}, '${reference}')" title="Annuler le devis">
            <i class="fas fa-trash-alt"></i>
        </button>
    `;
}
            }
        ]
    });

    // Le reste du script pour la suppression reste identique
    let devisToDelete = null;
    window.confirmDelete = function(id, reference) {
        devisToDelete = id;
        $('#deleteMessage').text(`Êtes-vous sûr de vouloir annuler le devis ${reference} ?`);
        // Assurez-vous que votre modal a bien l'ID 'deleteModal'
        $('#deleteModal').modal('show');
    };

    $('#confirmDelete').on('click', function () {
        if (devisToDelete) {
            const deleteUrl = "{{ route('colis.devis.destroy', ['id' => ':id']) }}".replace(':id', devisToDelete);
            $.ajax({
                url: deleteUrl,
                type: 'POST', // Il est préférable d'utiliser POST/DELETE pour la suppression
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE' // Method spoofing
                },
                success: function(response) {
                    table.ajax.reload();
                    $('#deleteModal').modal('hide');
                    showAlert('success', 'Devis annulé avec succès!');
                },
                error: function(xhr) {
                    showAlert('error', 'Erreur lors de l\'annulation du devis.');
                }
            });
        }
    });

    // Fonction pour afficher les alertes
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999;">
                <strong>${type === 'success' ? 'Succès!' : 'Erreur!'}</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('body').append(alertHtml);
        
        // Supprimer l'alerte après 5 secondes
        setTimeout(() => {
            $('.alert').alert('close');
        }, 5000);
    }
});
</script>

<style>
    .btn {
        width: auto;
        height: 30px;
        font-size: 16px;
        padding: 0 15px;
        border-radius: 5px;
        transition: background-color 0.3s, transform 0.2s;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
        
    }

    .btn-success:hover {
        background-color: #218838;
        transform: scale(1.05);
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #545b62;
    }

    .badge {
        font-size: 0.85em;
        padding: 0.4em 0.6em;
    }

    .dataTable-wrapper {
        width: 100% !important;
        margin: 20px auto;
        padding: 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background: #f9f9f9;
    }

    .dt-button {
        padding: 10px 20px;
        margin: 5px;
        border: 1px solid transparent;
        border-radius: 5px;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
        text-transform: uppercase;
        transition: all 0.3s ease;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: bold;
        text-align: center;
        vertical-align: middle;
        font-size: 12px
    }

    .table td {
        text-align: center;
        vertical-align: middle;
        font-size: 12px
    }

    .action-buttons-container {
        display: flex;
        justify-content: center;
        gap: 5px;
    }
</style>
@endsection