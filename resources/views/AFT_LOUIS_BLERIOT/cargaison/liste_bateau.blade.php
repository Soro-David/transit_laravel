@extends('AFT_LOUIS_BLERIOT.layouts.agent')
@section('content-header')
{{-- CSRF Token pour les requêtes AJAX --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
{{-- Font Awesome pour les icônes --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection

@section('content')
<section class="py-3">
    <div class="row">
        <div class="col-12">
            <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                <h4 class="mb-4">Liste des colis</h4>
                <div class="table-responsive">
                    <table id="productTable" class="table table-bordered table-striped display nowrap" style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">St. Paiement</th>
                                <th>Référence</th>
                                <th>Nombre de colis</th>
                                <th>Expéditeur</th>
                                <th>Téléphone</th>
                                <th>Agence Expéditeur</th>
                                <th>Destinataire</th>
                                <th>Agence Destinataire</th>
                                <th>Téléphone</th>
                                <th>Date</th>
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

{{-- ===== MODALE DE PAIEMENT (copiée de l'exemple) ===== --}}
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
{{-- ===== FIN MODALE DE PAIEMENT ===== --}}


{{-- Styles (Peuvent être dans un fichier CSS externe) --}}
<style>
    .action-buttons-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 5px;
        flex-wrap: nowrap;
    }
    .dataTables_wrapper { width: 100% !important; margin: 0 auto; }
    .dt-button { padding: 10px 20px; margin: 5px; border-radius: 5px; font-size: 14px; font-weight: bold; }
    .table th, .table td { vertical-align: middle; }
    .is-invalid { border-color: #dc3545; }
    .invalid-feedback { display: none; width: 100%; margin-top: .25rem; font-size: .875em; color: #dc3545; }
    .is-invalid ~ .invalid-feedback { display: block; }
</style>


<script>
$(document).ready(function () {
    // Configuration du header CSRF pour toutes les requêtes AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialisation de DataTables
    var table = $("#productTable").DataTable({
        processing: true, // Affiche un indicateur de chargement
        serverSide: true, // Active le traitement côté serveur
        responsive: true,
        scrollX: true,
        language: { url: "{{ asset('js/fr-FR.json') }}" },
        ajax: '{{ route("aftlb_colis.get.colis.bateau") }}', // Route vers la méthode du contrôleur
        columns: [
            { data: 'statut_paiement', name: 'statut_paiement', orderable: false, searchable: false, className: 'text-center' },
            { data: 'reference_colis', name: 'reference_colis' },
            { data: 'nombre_de_colis', name: 'nombre_de_colis' },
            {
                data: null,
                name: 'expediteur_nom',
                render: function (data) { return data.expediteur_nom + ' ' + data.expediteur_prenom; }
            },
            { data: 'expediteur_tel', name: 'expediteur_tel' },
            { data: 'expediteur_agence', name: 'expediteur_agence' },
            {
                data: null,
                name: 'destinataire_nom',
                render: function (data) { return data.destinataire_nom + ' ' + data.destinataire_prenom; }
            },
            { data: 'destinataire_agence', name: 'destinataire_agence' },
            { data: 'destinataire_tel', name: 'destinataire_tel' },
            { data: 'created_at', name: 'created_at' },
            // Nouvelle colonne pour les actions
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }

        ],
        dom: 'Bfrtip',
        buttons: [
            { extend: 'excelHtml5', text: 'Exporter en Excel', title: 'Liste des Colis en attente', exportOptions: { columns: ':visible:not(:last-child)' } },
            { extend: 'print', text: 'Imprimer', title: 'Liste des Colis en attente', exportOptions: { columns: ':visible:not(:last-child)' } }
        ],
        order: [[1, 'desc']] // Trier par référence par défaut
    });

    // --- LOGIQUE POUR LA MODALE DE PAIEMENT ---
    $('#productTable tbody').on('click', '.pay-btn', function (e) {
        e.preventDefault();

        var button = $(this);
        var reference = button.data('reference');
        var total = parseFloat(button.data('total'));
        var paid = parseFloat(button.data('paid'));
        var colisIds = JSON.stringify(button.data('colis-ids'));
        var remainingDue = total - paid;

        $('#paymentForm').data('remaining-due', remainingDue.toFixed(2));

        // Formatage en devise (optionnel mais recommandé)
        const formatCurrency = (val) => new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(val);

        $('#modalDisplayReference').val(reference);
        $('#modalTotalAmount').val(formatCurrency(total));
        $('#modalAmountAlreadyPaid').val(formatCurrency(paid));
        $('#modalReferenceColis').val(reference);
        $('#modalColisIds').val(colisIds);
        $('#modalNewPaymentAmount').val('').removeClass('is-invalid');
        $('#paymentAmountError').text('').hide();

        $('#paymentModal').modal('show');
    });

    // --- SOUMISSION DU PAIEMENT ---
    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validation côté client
        var newPaymentAmount = parseFloat($('#modalNewPaymentAmount').val());
        var remainingDue = parseFloat($('#paymentForm').data('remaining-due'));

        // Retirer la classe is-invalid pour un nouveau test
        $('#modalNewPaymentAmount').removeClass('is-invalid').siblings('.invalid-feedback').text('').hide();

        if (isNaN(newPaymentAmount) || newPaymentAmount <= 0) {
            $('#modalNewPaymentAmount').addClass('is-invalid').siblings('.invalid-feedback').text('Veuillez saisir un montant valide et positif.').show();
            return;
        }
        if (newPaymentAmount.toFixed(2) > remainingDue.toFixed(2)) { // Comparaison avec 2 décimales
            $('#modalNewPaymentAmount').addClass('is-invalid').siblings('.invalid-feedback').text('Le montant ne peut pas dépasser le solde restant dû.').show();
            return;
        }
        
        var submitButton = $('#submitPaymentBtn');
        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Enregistrement...');

        $.ajax({
            // Assurez-vous d'avoir une route nommée pour cette action
            url: '{{ route("aftlb_colis.valide.payer") }}', 
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                $('#paymentModal').modal('hide');
                Swal.fire('Succès!', response.success || 'Paiement enregistré.', 'success');
                table.ajax.reload(null, false); // Recharger la table
            },
            error: function(xhr) {
                let errorMsg = 'Une erreur est survenue.';
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    errorMsg = xhr.responseJSON.errors.montant_a_payer[0];
                    $('#modalNewPaymentAmount').addClass('is-invalid').siblings('.invalid-feedback').text(errorMsg).show();
                }
                Swal.fire('Erreur!', errorMsg, 'error');
            },
            complete: function() {
                submitButton.prop('disabled', false).html('Enregistrer Paiement');
            }
        });
    });
    
    // --- LOGIQUE POUR LA SUPPRESSION ---
    $('#productTable tbody').on('click', '.delete-btn', function (e) {
        e.preventDefault();
        const button = $(this);
        const deleteUrl = button.data('url');
        const reference = button.data('reference');

        Swal.fire({
            title: 'Êtes-vous sûr?',
            html: `Voulez-vous vraiment supprimer les colis de la référence <strong>${reference}</strong> ?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer!',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    dataType: 'json',
                    success: function (response) {
                        Swal.fire('Supprimé!', response.success, 'success');
                        table.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        Swal.fire('Erreur!', xhr.responseJSON.error || 'Erreur de suppression.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endsection