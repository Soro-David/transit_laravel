@extends('IPMS_SIMEXCI_ANGRE.layouts.agent')

@section('content-header')
<meta name="csrf-token" content="{{ csrf_token() }}">
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

   {{-- ===== MODALE DE PAIEMENT UNIVERSELLE ===== --}}
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
                    <div class="mb-3"><label class="form-label">Référence Colis</label><input type="text" class="form-control" id="modalDisplayReference" readonly></div>
                    <div class="mb-3"><label class="form-label">Montant Total Dû</label><input type="text" class="form-control" id="modalTotalAmount" readonly style="font-weight: bold;"></div>
                    <div class="mb-3"><label class="form-label">Montant Déjà Payé</label><input type="text" class="form-control" id="modalAmountAlreadyPaid" readonly style="color: green;"></div>
                    <div class="mb-3"><label class="form-label">Montant Restant à Payer</label><div id="modalRemainingAmountDisplay" class="form-control" style="font-weight: bold; color: #dc3545; background-color: #f8f9fa;"></div></div>

                    {{-- Section pour les agences type EURO (cachée par défaut) --}}
                    <div id="euroPaymentFields" style="display:none;">
                        <div class="mb-3"><label for="modalNewPaymentAmountEur" class="form-label">Nouveau Paiement (en EUR) <span class="text-danger">*</span></label><input type="number" step="0.01" class="form-control" id="modalNewPaymentAmountEur" required placeholder="0.00"></div>
                        <div class="mb-3"><label for="modalConvertedAmountCfa" class="form-label">Équivalent en FCFA</label><input type="text" class="form-control" id="modalConvertedAmountCfa" readonly style="font-weight: bold; background-color: #e9ecef;"></div>
                    </div>
                    {{-- Section pour l'agence id=7 (cachée par défaut) --}}
                    <div id="fcfaPaymentFields" style="display:none;"><div class="mb-3"><label for="modalNewPaymentAmountCfa" class="form-label">Nouveau Paiement (en FCFA) <span class="text-danger">*</span></label><input type="number" step="1" class="form-control" id="modalNewPaymentAmountCfa" required placeholder="0"></div></div>
                    
                    <input type="hidden" id="modalNewPaymentAmount" name="montant_a_payer">
                    <div class="invalid-feedback" id="paymentAmountError"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary" id="submitPaymentBtn">Enregistrer Paiement</button></div>
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

{{-- Script (LOGIQUE CORRIGÉE) --}}
<script>
$(document).ready(function () {
    const EUR_TO_FCFA_RATE = parseFloat("{{ App\Services\CurrencyConverterService::FCFA_TO_EUR_RATE }}") || 655.957;

    function formatCfa(value) { return Math.round(value).toLocaleString('fr-FR') + ' FCFA'; }
    function formatEur(value) { return Number(value).toFixed(2).replace('.', ',') + ' €'; }

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    if ($.fn.DataTable.isDataTable('#productTable')) {
        $('#productTable').DataTable().clear().destroy();
    }
    var table = $("#productTable").DataTable({
        processing: true, serverSide: true, responsive: true,
        language: { url: "{{ asset('js/fr-FR.json') }}" },
        ajax: '{{ route("ipms_angre_colis.get.colis.dump") }}',
        columns: [
            { data: 'statut_paiement', name: 'statut_paiement', orderable: false, searchable: false, className: 'text-center' },
            { data: 'reference_colis', name: 'reference_colis' },
            { data: 'nombre_de_colis', name: 'nombre_de_colis', className: 'text-center' },
            { data: null, name: 'expediteur_nom', render: function(d,t,r){ return (r.expediteur_nom||'')+' '+(r.expediteur_prenom||''); } },
            { data: 'expediteur_tel', name: 'expediteurs.tel' },
            { data: null, name: 'destinataire_nom', render: function(d,t,r){ return (r.destinataire_nom||'')+' '+(r.destinataire_prenom||''); } },
            { data: 'destinataire_tel', name: 'destinataires.tel' },
            { data: 'destinataire_agence', name: 'destinataires.agence' },
            { data: 'etat', name: 'colis.etat' },
            { data: 'created_at', name: 'colis.created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        dom: 'Bfrtip', buttons: ['excel', 'pdf', 'print'], order: [[ 1, 'desc' ]]
    });

   // --- LOGIQUE CORRIGÉE POUR L'OUVERTURE DE LA MODALE ---
   $('#productTable tbody').on('click', '.pay-btn', function (e) {
        e.preventDefault();
        var button = $(this);
        
        // On récupère les éléments input une seule fois
        var euroInput = $('#modalNewPaymentAmountEur');
        var fcfaInput = $('#modalNewPaymentAmountCfa');

        var creatorAgenceId = parseInt(button.data('creator-agence-id')) || 0;
        var reference = button.data('reference');
        var colisId = button.data('colis-id'); 
        var colisIds = button.data('colis-ids');
        
        $('#modalDisplayReference').val(reference);
        $('#modalColisId').val(colisId);
        // Assurez-vous que colisIds est bien une chaîne JSON valide
        $('#modalColisIds').val(typeof colisIds === 'string' ? colisIds : JSON.stringify(colisIds));

        if (creatorAgenceId === 7) {
            // Logique pour l'agence FCFA
            $('#euroPaymentFields').hide();
            euroInput.prop('disabled', true); // <-- NOUVEAU : On désactive le champ EUR

            $('#fcfaPaymentFields').show();
            fcfaInput.prop('disabled', false); // <-- NOUVEAU : On active le champ FCFA

            let total = parseFloat(button.data('total')) || 0;
            let paid = parseFloat(button.data('paid')) || 0;
            let remaining = total - paid;
            if (remaining <= 0) { Swal.fire('Information', 'Ce colis est déjà entièrement payé.', 'info'); return; }
            $('#modalTotalAmount').val(formatCfa(total));
            $('#modalAmountAlreadyPaid').val(formatCfa(paid));
            $('#modalRemainingAmountDisplay').html(`<strong style="color: #dc3545;">${formatCfa(remaining)}</strong>`);
            $('#modalNewPaymentAmountCfa').val(Math.round(remaining));
            $('#modalNewPaymentAmountCfa').trigger('input');
        } else {
            // Logique pour les agences EURO
            $('#fcfaPaymentFields').hide();
            fcfaInput.prop('disabled', true); // <-- NOUVEAU : On désactive le champ FCFA

            $('#euroPaymentFields').show();
            euroInput.prop('disabled', false); // <-- NOUVEAU : On active le champ EUR

            let totalEur = parseFloat(button.data('total')) || 0;
            let paidEur = parseFloat(button.data('paid')) || 0;
            let remainingEur = totalEur - paidEur;
            if (remainingEur <= 0) { Swal.fire('Information', 'Ce colis est déjà entièrement payé.', 'info'); return; }
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


    $('#modalNewPaymentAmountEur').on('input', function() {
        let amountEur = parseFloat($(this).val()) || 0;
        $('#modalConvertedAmountCfa').val(formatCfa(amountEur * EUR_TO_FCFA_RATE));
        $('#modalNewPaymentAmount').val(Math.round(amountEur * EUR_TO_FCFA_RATE));
    });

    $('#modalNewPaymentAmountCfa').on('input', function() {
        $('#modalNewPaymentAmount').val(Math.round(parseFloat($(this).val()) || 0));
    });
    // --- Logique de soumission du formulaire (inchangée) ---
    $('#paymentForm').on('submit', function(e) {
        e.preventDefault(); 
        var form = $(this);
        var submitButton = $('#submitPaymentBtn');
        var originalButtonText = submitButton.html();
        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Enregistrement...');
        var formData = form.serialize();

        $.ajax({
            url: '{{ route("ipms_angre_colis.valide.payer") }}',
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
                if (xhr.responseJSON && xhr.responseJSON.error) { errorMessage = xhr.responseJSON.error; }
                Swal.fire({ icon: 'error', title: 'Erreur!', text: errorMessage });
            },
            complete: function () {
                submitButton.prop('disabled', false).html(originalButtonText);
            }
        });
    });

    // --- Logique pour le bouton Supprimer/Archiver (inchangée) ---
    $('#productTable tbody').on('click', '.delete-btn', function (e) {
        // ... (logique de suppression inchangée) ...
        e.preventDefault();
        const button = $(this);
        const deleteUrl = button.data('url');
        const reference = button.data('reference');
        if (!deleteUrl) { Swal.fire('Erreur', 'Impossible de trouver l\'action de suppression.', 'error'); return; }
        Swal.fire({
            title: 'Êtes-vous sûr?',
            html: `Voulez-vous vraiment archiver le(s) colis avec la référence <strong>${reference}</strong> ?<br><small>Cette action est généralement réversible.</small>`,
            icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Oui, archiver!', cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: deleteUrl, type: 'DELETE', dataType: 'json',
                    success: function (response) {
                        Swal.fire('Archivé!', response.success || `Le(s) colis avec la référence ${reference} ont été archivés.`, 'success');
                        table.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        let errorMsg = 'Une erreur est survenue lors de l\'archivage.';
                        if(xhr.responseJSON && xhr.responseJSON.error) { errorMsg = xhr.responseJSON.error; }
                        Swal.fire('Erreur!', errorMsg, 'error');
                    }
                });
            }
        });
    });
});
</script>

@endsection