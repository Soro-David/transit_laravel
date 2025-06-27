@extends('IPMS_SIMEXCI.layouts.agent')

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
                <h4 class="text-left mt-4">Liste des colis Validés</h4><br>
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

   {{-- ===== MODIFICATION : MODALE DE PAIEMENT UNIVERSELLE ===== --}}
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
                    {{-- Champs généraux --}}
                    <input type="hidden" id="modalColisId" name="colis_id" value="">
                    <input type="hidden" id="modalColisIds" name="colis_ids" value="">
                   
                    <div class="mb-3">
                        <label for="modalDisplayReference" class="form-label">Référence Colis</label>
                        <input type="text" class="form-control" id="modalDisplayReference" readonly>
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

                    {{-- Section pour les agences type EURO (cachée par défaut) --}}
                    <div id="euroPaymentFields" style="display:none;">
                        <div class="mb-3">
                            <label for="modalNewPaymentAmountEur" class="form-label">Montant du Nouveau Paiement (en EUR) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="modalNewPaymentAmountEur" required placeholder="0.00">
                        </div>
                        <div class="mb-3">
                            <label for="modalConvertedAmountCfa" class="form-label">Équivalent en FCFA</label>
                            <input type="text" class="form-control" id="modalConvertedAmountCfa" readonly style="font-weight: bold; background-color: #e9ecef;">
                        </div>
                    </div>

                    {{-- Section pour l'agence id=7 (cachée par défaut) --}}
                    <div id="fcfaPaymentFields" style="display:none;">
                         <div class="mb-3">
                            <label for="modalNewPaymentAmountCfa" class="form-label">Montant du Nouveau Paiement (en FCFA) <span class="text-danger">*</span></label>
                            <input type="number" step="1" class="form-control" id="modalNewPaymentAmountCfa" required placeholder="0">
                        </div>
                    </div>
                
                    {{-- CHAMP CACHÉ UNIVERSEL qui sera soumis au serveur (toujours en FCFA) --}}
                    <input type="hidden" id="modalNewPaymentAmount" name="montant_a_payer">
                    <div class="invalid-feedback" id="paymentAmountError"></div>
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

{{-- Styles (inchangés) --}}
<style>
    #productTable { width: 100% !important; }
    #productTable th, #productTable td { vertical-align: middle; }
    #productTable .text-center { text-align: center; }
    .action-buttons-container { display: flex; justify-content: center; align-items: center; gap: 5px; flex-wrap: nowrap; }
    .dt-buttons { margin-bottom: 15px; }
    @media (max-width: 768px) {
        #productTable { white-space: normal; }
        .dt-buttons { text-align: center; }
        .dt-button { display: block; margin: 5px auto; width: 80%; }
        .action-buttons-container { flex-wrap: wrap; justify-content: center; }
    }
    .is-invalid { border-color: #dc3545; }
    .invalid-feedback { display: none; width: 100%; margin-top: .25rem; font-size: .875em; color: #dc3545; }
    .is-invalid ~ .invalid-feedback { display: block; }
</style>

{{-- ===== MODIFICATION : SCRIPT UNIVERSEL ===== --}}
<script>
$(document).ready(function () {
    const EUR_TO_FCFA_RATE = parseFloat("{{ App\Services\CurrencyConverterService::FCFA_TO_EUR_RATE }}") || 655.957;

    function formatCfa(value) {
        return Math.round(value).toLocaleString('fr-FR') + ' FCFA';
    }
    function formatEur(value) {
        return Number(value).toFixed(2).replace('.', ',') + ' €';
    }

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    if ($.fn.DataTable.isDataTable('#productTable')) {
        $('#productTable').DataTable().clear().destroy();
    }
    var table = $("#productTable").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        language: { url: "{{ asset('js/fr-FR.json') }}" },
        ajax: '{{ route("ipms_colis.get.colis.dump") }}',
        columns: [
            { data: 'statut_paiement', name: 'statut_paiement', orderable: false, searchable: false, className: 'text-center' },
            { data: 'reference_colis', name: 'reference_colis' },
            { data: 'nombre_de_colis', name: 'nombre_de_colis', className: 'text-center' },
            { data: null, name: 'expediteur_nom', render: function (data, type, row) { return (row.expediteur_nom || '') + ' ' + (row.expediteur_prenom || ''); }, searchable: true, orderable: true },
            { data: 'expediteur_tel', name: 'expediteurs.tel' },
            { data: null, name: 'destinataire_nom', render: function (data, type, row) { return (row.destinataire_nom || '') + ' ' + (row.destinataire_prenom || ''); }, searchable: true, orderable: true },
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

    // LOGIQUE CONDITIONNELLE BASÉE SUR L'AGENT CRÉATEUR DU COLIS
    $('#productTable tbody').on('click', '.pay-btn', function (e) {
        e.preventDefault();
        var button = $(this);
        
        // On lit l'ID de l'agence du créateur du colis depuis le bouton
        var creatorAgenceId = parseInt(button.data('creator-agence-id')) || 0;
        
        var reference = button.data('reference');
        var colisId = button.data('colis-id'); 
        var colisIds = button.data('colis-ids');
        
        $('#modalDisplayReference').val(reference);
        $('#modalColisId').val(colisId);
        $('#modalColisIds').val(JSON.stringify(colisIds));

        // CAS 1 : Colis créé par l'agence avec id = 7 (FCFA)
        if (creatorAgenceId === 7) {
            $('#euroPaymentFields').hide();
            $('#fcfaPaymentFields').show();

            let total = parseFloat(button.data('total')) || 0;
            let paid = parseFloat(button.data('paid')) || 0;
            let remaining = total - paid;

            if (remaining <= 0) {
                Swal.fire('Information', 'Ce colis est déjà entièrement payé.', 'info');
                return;
            }

            $('#modalTotalAmount').val(formatCfa(total));
            $('#modalAmountAlreadyPaid').val(formatCfa(paid));
            $('#modalRemainingAmountDisplay').html(`<strong style="color: #dc3545;">${formatCfa(remaining)}</strong>`);
            
            $('#modalNewPaymentAmountCfa').val(Math.round(remaining));
            $('#modalNewPaymentAmountCfa').trigger('input');
        } 
        // CAS 2 : Colis créé par une autre agence (Euro)
        else {
            $('#fcfaPaymentFields').hide();
            $('#euroPaymentFields').show();

            let totalEur = parseFloat(button.data('total')) || 0;
            let paidEur = parseFloat(button.data('paid')) || 0;
            let remainingEur = totalEur - paidEur;

            if (remainingEur <= 0) {
                Swal.fire('Information', 'Ce colis est déjà entièrement payé.', 'info');
                return;
            }

            let totalCfa = totalEur * EUR_TO_FCFA_RATE;
            let paidCfa = paidEur * EUR_TO_FCFA_RATE;
            let remainingCfa = remainingEur * EUR_TO_FCFA_RATE;

            $('#modalTotalAmount').val(`${formatEur(totalEur)} soit ${formatCfa(totalCfa)}`);
            $('#modalAmountAlreadyPaid').val(`${formatEur(paidEur)} soit ${formatCfa(paidCfa)}`);
            $('#modalRemainingAmountDisplay').html(`<strong style="color: #dc3545;">${formatEur(remainingEur)}</strong> soit ${formatCfa(remainingCfa)}`);
            
            $('#modalNewPaymentAmountEur').val(remainingEur.toFixed(2));
            $('#modalNewPaymentAmountEur').trigger('input');
        }
        
        $('#paymentModal').modal('show');
    });

    // Écouteur pour le champ EURO
    $('#modalNewPaymentAmountEur').on('input', function() {
        let amountEur = parseFloat($(this).val()) || 0;
        let amountCfa = amountEur * EUR_TO_FCFA_RATE;
        $('#modalConvertedAmountCfa').val(formatCfa(amountCfa));
        $('#modalNewPaymentAmount').val(Math.round(amountCfa));
    });

    // Écouteur pour le champ FCFA
    $('#modalNewPaymentAmountCfa').on('input', function() {
        let amountCfa = parseFloat($(this).val()) || 0;
        $('#modalNewPaymentAmount').val(Math.round(amountCfa));
    });
    // --- Logique de soumission du formulaire ---
    $('#paymentForm').on('submit', function(e) {
        e.preventDefault(); 
        var form = $(this);
        var submitButton = $('#submitPaymentBtn');
        var originalButtonText = submitButton.html();
        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Enregistrement...');
        var formData = form.serialize();

        $.ajax({
            url: '{{ route("ipms_colis.valide.payer") }}',
            type: 'POST',
            data: formData,
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
    $('#productTable tbody').on('click', '.delete-btn', function (e) {
        e.preventDefault();
        const button = $(this);
        const deleteUrl = button.data('url');
        const reference = button.data('reference');

        if (!deleteUrl) {
            Swal.fire('Erreur', 'Impossible de trouver l\'action de suppression.', 'error');
            return;
        }

        Swal.fire({
            title: 'Êtes-vous sûr?',
            html: `Voulez-vous vraiment archiver le(s) colis avec la référence <strong>${reference}</strong> ?<br><small>Cette action est généralement réversible.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, archiver!',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    dataType: 'json',
                    success: function (response) {
                        Swal.fire('Archivé!', response.success || `Le(s) colis avec la référence ${reference} ont été archivés.`, 'success');
                        table.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        let errorMsg = 'Une erreur est survenue lors de l\'archivage.';
                        if(xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        }
                        Swal.fire('Erreur!', errorMsg, 'error');
                    }
                });
            }
        });
    });
});
</script>
@endsection