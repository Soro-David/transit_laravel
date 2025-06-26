@extends('IPMS_SIMEXCI_ANGRE.layouts.agent')

@section('content-header')
{{-- CSRF Token pour les requêtes AJAX --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
{{-- Font Awesome --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection

@section('content')
<section class="py-3">
    <div class="row">
        <div class="col-md-12">
            <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                <h4 class="text-left mt-4">Liste des colis à livrer</h4><br>
                <div class="table-responsive">
                    <table id="productTable" class="table table-bordered table-striped display" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center">St. Paiement</th>
                                <th>Référence</th>
                                <th class="text-center">Nb. Colis</th>
                                <th>Expéditeur</th>
                                <th>Tél. Exp</th>
                                <th>Destinataire</th>
                                <th>Tél. Dest.</th>
                                <th>Agence Dest.</th>
                                <th>Status Colis</th>
                                <th>Date</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Le contenu sera chargé par DataTables --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

   {{-- ===== MODALE DE PAIEMENT (NETTOYÉE) ===== --}}
   <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Enregistrer un Paiement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paymentForm">
                @csrf 
                <div class="modal-body">
                    <input type="hidden" id="modalColisId" name="colis_id" value="">
                    <input type="hidden" id="modalColisIds" name="colis_ids" value="">
                   
                    <div class="mb-3">
                        <label for="modalDisplayReference" class="form-label">Référence Colis</label>
                        <input type="text" class="form-control" id="modalDisplayReference" name="reference_colis" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="modalTotalAmount" class="form-label">Montant Total Dû</label>
                        <input type="text" class="form-control" id="modalTotalAmount" readonly style="font-weight: bold;">
                    </div>

                     <div class="mb-3">
                        <label for="modalAmountAlreadyPaid" class="form-label">Montant Déjà Payé</label>
                        <input type="text" class="form-control" id="modalAmountAlreadyPaid" readonly style="color: green;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Montant Restant à Payer</label>
                        <div id="modalRemainingAmountDisplay" class="form-control" style="font-weight: bold; color: #dc3545; background-color: #f8f9fa;"></div>
                    </div>

                    <div class="mb-3">
                        <label for="modalNewPaymentAmount" class="form-label">Montant du Nouveau Paiement (en FCFA) <span class="text-danger">*</span></label>
                        <input type="number" step="1" class="form-control" id="modalNewPaymentAmount" required placeholder="0" name="montant_a_payer">
                        <div class="invalid-feedback" id="paymentAmountError"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" id="submitPaymentBtn">Enregistrer Paiement</button>
                </div>
            </form>
        </div>
    </div>
</div>
</section>

{{-- Styles --}}
<style>
    .action-buttons-container { display: flex; justify-content: center; gap: 5px; }
    .is-invalid { border-color: #dc3545; }
    .invalid-feedback { display: none; width: 100%; margin-top: .25rem; font-size: .875em; color: #dc3545; }
    .is-invalid ~ .invalid-feedback { display: block; }
</style>

{{-- Script --}}
<script>
$(document).ready(function () {
    // Configuration CSRF
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // UNE SEULE INITIALISATION DE DATATABLES (CORRIGÉ)
    var table = $("#productTable").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        language: { url: "{{ asset('js/fr-FR.json') }}" },
        ajax: '{{ route("ipms_angre_colis.get.colis.dump") }}',
        columns: [
            { data: 'statut_paiement', name: 'statut_paiement', orderable: false, searchable: false, className: 'text-center' },
            { data: 'reference_colis', name: 'reference_colis' },
            { data: 'nombre_de_colis', name: 'nombre_de_colis', className: 'text-center' },
            { data: null, name: 'expediteur_nom', render: (data, type, row) => `${row.expediteur_nom || ''} ${row.expediteur_prenom || ''}`.trim() },
            { data: 'expediteur_tel', name: 'expediteurs.tel' },
            { data: null, name: 'destinataire_nom', render: (data, type, row) => `${row.destinataire_nom || ''} ${row.destinataire_prenom || ''}`.trim() },
            { data: 'destinataire_tel', name: 'destinataires.tel' },
            { data: 'destinataire_agence', name: 'destinataires.agence' },
            { data: 'etat', name: 'colis.etat' },
            { data: 'created_at', name: 'colis.created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        dom: 'Bfrtip',
        buttons: ['excel', 'pdf', 'print'],
        order: [[ 1, 'desc' ]]
    });

    // --- Logique pour la Modale de Paiement ---
    $('#productTable tbody').on('click', '.pay-btn', function (e) {
        e.preventDefault();

        var button = $(this);
        var total = parseFloat(button.data('total')) || 0;
        var paid = parseFloat(button.data('paid')) || 0;
        var remaining = total - paid;
        
        if (remaining <= 0) {
            Swal.fire('Information', 'Ce colis est déjà entièrement payé.', 'info');
            return;
        }
        
        var colisId = button.data('colis-id'); 
        var colisIds = button.data('colis-ids');
        var reference = button.data('reference');
        
        function formatCfa(value) {
            return Number(value).toLocaleString('fr-FR') + ' FCFA';
        }

        $('#modalDisplayReference').val(reference);
        $('#modalTotalAmount').val(formatCfa(total));
        $('#modalAmountAlreadyPaid').val(formatCfa(paid));
        $('#modalRemainingAmountDisplay').text(formatCfa(remaining)); 
        
        $('#modalNewPaymentAmount').val(Math.round(remaining));
        $('#modalNewPaymentAmount').attr('max', Math.round(remaining));

        $('#modalColisId').val(colisId);
        $('#modalColisIds').val(JSON.stringify(colisIds)); 

        $('#modalNewPaymentAmount').removeClass('is-invalid');
        $('#paymentAmountError').text('').hide();
        
        $('#paymentModal').modal('show');
    });

    // --- Logique de soumission du formulaire ---
    $('#paymentForm').on('submit', function(e) {
        e.preventDefault(); 

        var form = $(this);
        var submitButton = $('#submitPaymentBtn');
        var originalButtonText = submitButton.html();
        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Enregistrement...');

        $.ajax({
            url: '{{ route("ipms_angre_colis.valide.payer") }}',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                $('#paymentModal').modal('hide');
                Swal.fire({ icon: 'success', title: 'Succès!', text: response.success, timer: 2500, showConfirmButton: false });
                table.ajax.reload(null, false); 
            },
            error: function(xhr) {
                var errorMessage = 'Une erreur est survenue lors de l\'enregistrement.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                Swal.fire({ icon: 'error', title: 'Erreur!', text: errorMessage });
            },
            complete: function () {
                submitButton.prop('disabled', false).html(originalButtonText);
            }
        });
    });
    // --- Logique pour le bouton Supprimer/Archiver ---

    // Utilisation de la délégation d'événement sur le tbody pour les boutons ajoutés dynamiquement
    $('#productTable tbody').on('click', '.delete-btn', function (e) {
        e.preventDefault(); // Bonne pratique

        const button = $(this);
        const deleteUrl = button.data('url'); // URL de suppression depuis data-url
        const reference = button.data('reference'); // Référence pour le message de confirmation

        if (!deleteUrl) {
            console.error("URL de suppression non trouvée pour le bouton:", button);
            Swal.fire('Erreur', 'Impossible de trouver l\'action de suppression.', 'error');
            return;
        }

        // Confirmation avec SweetAlert
        Swal.fire({
            title: 'Êtes-vous sûr?',
            html: `Voulez-vous vraiment archiver le(s) colis avec la référence <strong>${reference}</strong> ?<br><small>Cette action est généralement réversible.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33', // Rouge pour la suppression/archivage
            cancelButtonColor: '#3085d6', // Bleu pour annuler
            confirmButtonText: 'Oui, archiver!',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                // Si l'utilisateur confirme, envoyer la requête AJAX DELETE
                $.ajax({
                    url: deleteUrl, // L'URL contient déjà la référence ou l'identifiant nécessaire
                    type: 'DELETE', // Utiliser la méthode DELETE
                    // Le token CSRF est déjà configuré globalement via $.ajaxSetup
                    dataType: 'json', // Attendre une réponse JSON
                    success: function (response) {
                        Swal.fire(
                            'Archivé!',
                            response.success || `Le(s) colis avec la référence ${reference} ont été archivés.`,
                            'success'
                        );
                        table.ajax.reload(null, false); // Recharger la table sans réinitialiser
                    },
                    error: function (xhr, status, error) {
                        let errorMsg = 'Une erreur est survenue lors de l\'archivage.';
                        if(xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        } else {
                             console.error("Erreur AJAX Delete:", status, error, xhr.responseText);
                        }
                        Swal.fire(
                            'Erreur!',
                            errorMsg,
                            'error'
                        );
                    }
                });
            }
        });
    });


}); // Fin $(document).ready
</script>

@endsection