@extends('AFT_LOUIS_BLERIOT.layouts.agent')

@section('content-header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
{{-- Assurez-vous d'inclure SweetAlert2 si ce n'est pas déjà fait dans votre layout principal --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('content')
<section class="py-3">
    <div class="row">
        <div class="col-12">
            <div class="border p-4 rounded shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">Colis du conteneur : <strong>{{ $reference_contenaire }}</strong></h4>
                    <a href="{{ route('aftlb_colis.historique.contenaire') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à l'historique
                    </a>
                </div>
                <div class="table-responsive">
                    <table id="colisTable" class="table table-bordered table-striped display nowrap" style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">St. Paiement</th>
                                <th>Référence</th>
                                <th>Nombre Colis</th>
                                <th>Expéditeur</th>
                                <th>Téléphone</th>
                                <th>Agence Exp.</th>
                                <th>Destinataire</th>
                                <th>Agence Dest.</th>
                                <th>Téléphone</th>
                                <th>Date</th>
                                {{-- NOUVELLE COLONNE ACTION --}}
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Le contenu sera chargé par DataTables via AJAX --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== MODALE DE PAIEMENT ===== --}}
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
                    <input type="hidden" id="modalReferenceColis" name="reference_colis">
                    <input type="hidden" id="modalColisIds" name="colis_ids">

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
                        <label for="modalNewPaymentAmount" class="form-label">Montant du Nouveau Paiement <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="modalNewPaymentAmount" name="montant_a_payer" required placeholder="0.00">
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
@endsection


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    var table = $("#colisTable").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        scrollX: true,
        language: { url: "{{ asset('js/fr-FR.json') }}" },
        ajax: '{{ route("aftlb_colis.get.colis.pour.contenaire", ["reference_contenaire" => $reference_contenaire]) }}',
        columns: [
            { data: 'statut_paiement', name: 'statut_paiement', orderable: false, searchable: false, className: 'text-center' },
            { data: 'reference_colis', name: 'reference_colis' },
            { data: 'nombre_de_colis', name: 'nombre_de_colis' },
            { data: null, name: 'expediteur_nom', render: data => `${data.expediteur_nom} ${data.expediteur_prenom}` },
            { data: 'expediteur_tel', name: 'expediteur_tel' },
            { data: 'expediteur_agence', name: 'expediteur_agence' },
            { data: null, name: 'destinataire_nom', render: data => `${data.destinataire_nom} ${data.destinataire_prenom}` },
            { data: 'destinataire_agence', name: 'destinataire_agence' },
            { data: 'destinataire_tel', name: 'destinataire_tel' },
            { data: 'created_at', name: 'created_at' },
            // COLONNE ACTION RÉACTIVÉE
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        dom: 'Bfrtip',
        buttons: ['excel', 'print'],
        order: [[1, 'desc']]
    });

    // --- LOGIQUE POUR LA MODALE DE PAIEMENT ---

    // 1. Ouvrir la modale au clic sur le bouton Payer
    $('#colisTable tbody').on('click', '.pay-btn', function (e) {
        e.preventDefault();

        var button = $(this);
        var reference = button.data('reference');
        var total = parseFloat(button.data('total'));
        var paid = parseFloat(button.data('paid'));
        var colisIds = JSON.stringify(button.data('colis-ids'));
        var remainingDue = total - paid;

        $('#paymentForm').data('remaining-due', remainingDue.toFixed(2));

        function formatCurrency(value) {
            const num = Number(value);
            return isNaN(num) ? 'N/A' : num.toLocaleString('fr-CI', { style: 'currency', currency: 'XOF' });
        }

        $('#modalDisplayReference').val(reference);
        $('#modalTotalAmount').val(formatCurrency(total));
        $('#modalAmountAlreadyPaid').val(formatCurrency(paid));
        $('#modalReferenceColis').val(reference);
        $('#modalColisIds').val(colisIds);
        $('#modalNewPaymentAmount').val('').removeClass('is-invalid');
        $('#paymentAmountError').text('').hide();

        $('#paymentModal').modal('show');
    });

    // 2. Soumettre le formulaire de paiement via AJAX
    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();

        $('#modalNewPaymentAmount').removeClass('is-invalid');
        $('#paymentAmountError').text('').hide();

        var newPaymentAmount = parseFloat($('#modalNewPaymentAmount').val());
        var remainingDue = parseFloat($('#paymentForm').data('remaining-due'));

        if (isNaN(newPaymentAmount) || newPaymentAmount <= 0) {
            $('#modalNewPaymentAmount').addClass('is-invalid');
            $('#paymentAmountError').text('Veuillez saisir un montant valide et positif.').show();
            return;
        }

        if (newPaymentAmount > remainingDue) {
            $('#modalNewPaymentAmount').addClass('is-invalid');
            $('#paymentAmountError').text('Le montant saisi ne peut pas dépasser le solde restant.').show();
            Swal.fire({
                icon: 'error',
                title: 'Montant Invalide',
                text: 'Le montant du paiement est supérieur au solde restant à payer.'
            });
            return;
        }

        var submitButton = $('#submitPaymentBtn');
        var originalButtonText = submitButton.html();
        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enregistrement...');

        $.ajax({
            url: '{{ route("aftlb_colis.valide.payer") }}',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                $('#paymentModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Succès!',
                    text: response.success || 'Paiement enregistré avec succès.',
                    timer: 2500,
                    showConfirmButton: false
                });
                table.ajax.reload(null, false);
            },
            error: function(xhr) {
                var errorMessage = 'Une erreur est survenue.';
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    if (xhr.responseJSON.errors.montant_a_payer) {
                        $('#modalNewPaymentAmount').addClass('is-invalid');
                        $('#paymentAmountError').text(xhr.responseJSON.errors.montant_a_payer[0]).show();
                        errorMessage = 'Veuillez corriger les erreurs indiquées.';
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                Swal.fire({ icon: 'error', title: 'Erreur!', text: errorMessage });
            },
            complete: function () {
                 submitButton.prop('disabled', false).html(originalButtonText);
            }
        });
    });
});
</script>

<style>
    .action-buttons-container { display: flex; justify-content: center; align-items: center; gap: 5px; flex-wrap: nowrap; }
    .is-invalid { border-color: #dc3545; }
    .invalid-feedback { display: none; width: 100%; margin-top: .25rem; font-size: .875em; color: #dc3545; }
    .is-invalid ~ .invalid-feedback { display: block; }
</style>