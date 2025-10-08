@extends('AGENCE_CHINE.layouts.agent')

@section('content-header')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-primary">📑 Liste des devis à traiter</h1>
        <p class="mb-0 text-muted">Gérez et suivez vos devis en cours de validation</p>
    </div>
</div>
@endsection

@section('content')
<section class="py-3">
    <div class="card border-0 shadow-lg rounded-3">
        <div class="card-header bg-gradient-primary text-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fas fa-clock me-2"></i> Devis en attente et validés (FCFA)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="chineHoldTable" class="table align-middle table-striped mb-0">
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
                    <tbody>
                        <!-- Les données seront chargées via AJAX -->
                    </tbody>
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
                <button type="button" class="btn btn-warning" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-1"></i>Oui, supprimer
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- CHANGEMENT : 'scripts' -> 'js' pour correspondre au layout --}}
@section('js')

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM chargé, initialisation DataTable...');

    const tableElement = document.getElementById('chineHoldTable');
    if (!tableElement) {
        console.error('Élément table non trouvé');
        return;
    }

    // Initialiser DataTable
    const table = $('#chineHoldTable').DataTable({
        processing: true,
        serverSide: false,
        responsive: true,
        ajax: {
    url: '{{ route("chine_colis.get.colis.hold") }}',
    type: 'GET',
    dataType: 'json',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
    },
    dataSrc: function (json) {
        console.log('Réponse reçue:', json);
        
        // Si c'est déjà un tableau, retournez-le directement
        if (Array.isArray(json)) {
            return json;
        }
        // Si c'est un objet avec une propriété data
        if (json && json.data) {
            return json.data;
        }
        // Si la structure est différente, ajustez selon votre API
        if (json && json.colis) {
            return json.colis;
        }
        
        console.warn('Format de réponse inattendu:', json);
        return [];
    },
    error: function(xhr, textStatus, errorThrown) {
        console.error('Erreur AJAX détaillée:');
        console.error('Status:', xhr.status);
        console.error('Status Text:', textStatus);
        console.error('Error:', errorThrown);
        console.error('Réponse complète:', xhr.responseText);
        
        // Afficher plus de détails dans l'alerte
        let errorMsg = 'Erreur lors du chargement des devis. ';
        
        if (xhr.status === 0) {
            errorMsg += 'Problème de connexion réseau.';
        } else if (xhr.status === 401) {
            errorMsg += 'Non authentifié. Veuillez vous reconnecter.';
            // Redirection automatique vers login
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else if (xhr.status === 403) {
            errorMsg += 'Accès non autorisé.';
        } else if (xhr.status === 404) {
            errorMsg += 'URL non trouvée. Vérifiez la route.';
        } else if (xhr.status === 500) {
            errorMsg += 'Erreur serveur. Contactez l\'administrateur.';
        } else {
            errorMsg += `Code d'erreur: ${xhr.status}`;
        }
        
        showAlert('error', errorMsg);
    }
},
        columns: [
            { 
                data: 'reference_colis',
                render: function(data) { 
                    return data ? '<span class="fw-bold text-primary">' + data + '</span>' : 'N/A';
                }
            },
            { data: 'nombre_de_colis', className: 'text-center fw-semibold' },
            { 
                data: null,
                render: function (data, type, row) { 
                    return (row.expediteur_nom || '') + ' ' + (row.expediteur_prenom || '');
                }
            },
            { data: 'expediteur_tel', className: 'text-muted' },
            { data: 'destinataire_agence', className: 'fw-semibold' },
            { 
                data: 'etat',
                render: function(data) {
                    if (!data) return '<span class="badge bg-secondary">Inconnu</span>';
                    let badgeClass = (data + '').toLowerCase() === "validé" ? "bg-success" : "bg-warning text-dark";
                    return '<span class="badge rounded-pill px-3 py-2 ' + badgeClass + '">' + data + '</span>';
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
                    const showUrl = "{{ route('chine_colis.devis.show', ['id' => ':id']) }}".replace(':id', row.id);
                    const reference = row.reference_colis ? row.reference_colis.replace(/'/g, "\\'") : '';

                    return `
                        <a href="${showUrl}" class="btn btn-sm btn-info btn-action me-1" title="Voir les détails">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button class="btn btn-sm btn-danger btn-action delete-btn" data-id="${row.id}" data-reference="${reference}" title="Annuler le devis">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    `;
                }
            }
        ],
        language: {
            emptyTable: "Aucun devis à afficher",
            info: "Affichage de _START_ à _END_ sur _TOTAL_ devis",
            infoEmpty: "Aucun devis à afficher",
            infoFiltered: "(filtrés sur _MAX_ devis au total)",
            lengthMenu: "Afficher _MENU_ devis",
            loadingRecords: "Chargement...",
            processing: "Traitement...",
            search: "Rechercher:",
            zeroRecords: "Aucun devis correspondant trouvé"
        }
    });

    console.log('DataTable initialisé');

    // Suppression avec délégation
    let devisToDelete = null;
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        const reference = $(this).data('reference');
        confirmDelete(id, reference);
    });

    function confirmDelete(id, reference) {
        if (!id) {
            console.error('ID du devis manquant');
            return;
        }
        devisToDelete = id;
        const message = reference ? 
            `Êtes-vous sûr de vouloir annuler le devis ${reference} ?` : 
            'Êtes-vous sûr de vouloir annuler ce devis ?';
        $('#deleteMessage').text(message);
        $('#deleteModal').modal('show');
    }

    $('#confirmDeleteBtn, #confirmDelete').on('click', function () {
        if (!devisToDelete) return;
        const deleteUrl = "{{ route('chine_colis.devis.destroy', ['id' => ':id']) }}".replace(':id', devisToDelete);

        $.ajax({
            url: deleteUrl,
            type: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'DELETE'
            },
            success: function(response) {
                $('#deleteModal').modal('hide');
                table.ajax.reload(null, false);
                showAlert('success', (response.message || 'Devis annulé avec succès!'));
            },
            error: function(xhr) {
                $('#deleteModal').modal('hide');
                console.error('Erreur suppression:', xhr.status, xhr.responseText);
                let message = 'Erreur lors de l\'annulation du devis.';
                if (xhr.responseJSON && xhr.responseJSON.message) message = xhr.responseJSON.message;
                showAlert('error', message);
            }
        });
    });

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertId = 'alert-' + Date.now();
        const alertHtml = `
            <div id="${alertId}" class="alert ${alertClass} alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999;">
                <strong>${type === 'success' ? 'Succès!' : 'Erreur!'}</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('body').append(alertHtml);
        setTimeout(() => { $('#' + alertId).alert('close'); }, 5000);
    }
});
</script>

<style>
.table thead th {
    font-size: 0.85rem;
    letter-spacing: .5px;
    font-weight: 600;
}
.table td {
    vertical-align: middle;
}
.btn-action {
    border-radius: 50%;
    width: 35px;
    height: 35px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all .2s ease;
}
.btn-action:hover {
    transform: scale(1.1);
}
.bg-gradient-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
}
</style>
@endsection
