@extends('admin.layouts.admin')

@section('content-header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection

@section('content')
<section class="py-3">
    <div class="row">
        <div class="col-md-12">
            <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                <h4 class="text-left mt-4">Historique de tous les colis</h4><br>
                <div class="table-responsive">
                    <table id="productTable" class="table table-bordered table-striped display" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center">St. Paiement</th>
                                <th>Référence</th>
                                <th class="text-center">Nb. Colis</th>
                                <th>Produit</th>
                                <th>Expéditeur</th>
                                <th>Agence Exp.</th>
                                <th>Destinataire</th>
                                <th>Agence Dest.</th>
                                <th>État</th>
                                <th>Date</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Contenu chargé par DataTables --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

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
</section>

{{-- Styles CSS --}}
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

{{-- Script pour DataTables et AJAX --}}
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        var table = $("#productTable").DataTable({
            processing: true, serverSide: true, responsive: true,
            language: { url: "{{ asset('js/fr-FR.json') }}" },
            ajax: '{{ route("colis.get.tout.colis") }}',
            columns: [
                { data: 'statut_paiement', name: 'statut_paiement', orderable: false, searchable: false, className: 'text-center' },
                { data: 'reference_colis', name: 'reference_colis' },
                { data: 'nombre_de_colis', name: 'nombre_de_colis', className: 'text-center' },
                { data: 'produit', name: 'produit' },
                { data: null, name: 'expediteur_nom', render: (d,t,r) => `${r.expediteur_nom||''} ${r.expediteur_prenom||''}<br><small>${r.expediteur_tel||''}</small>` },
                { data: 'expediteur_agence', name: 'expediteur_agence' },
                { data: null, name: 'destinataire_nom', render: (d,t,r) => `${r.destinataire_nom||''} ${r.destinataire_prenom||''}<br><small>${r.destinataire_tel||''}</small>` },
                { data: 'destinataire_agence', name: 'destinataire_agence' },
                { data: 'etat', name: 'colis.etat' },
                { data: 'created_at', name: 'colis.created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            dom: 'Bfrtip',
            buttons: [
                { extend: 'excelHtml5', text: 'Exporter Excel', title: 'MANIFESTE', exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8, 9] } },
                { extend: 'print', text: 'Imprimer', title: 'MANIFESTE', exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8, 9] } }
            ],
            order: [[ 9, 'desc' ]]
        });

        // --- Logique pour la Modale de Paiement (MISE À JOUR) ---
        $('#productTable tbody').on('click', '.pay-btn', function () {
            var button = $(this);
            var reference = button.data('reference');
            var total = parseFloat(button.data('total'));
            var paid = parseFloat(button.data('paid'));
            var colisIds = JSON.stringify(button.data('colis-ids'));
            // **1. RÉCUPÉRER LA DEVISE DEPUIS L'ATTRIBUT DATA**
            var devise = button.data('devise') || '';

            var remainingDue = total - paid;
            $('#paymentForm').data('remaining-due', remainingDue.toFixed(2));

            // **2. CRÉER UNE FONCTION DE FORMATAGE DYNAMIQUE**
            const formatCurrency = (value, currencySymbol) => {
                // Formate le nombre avec des séparateurs de milliers et 2 décimales
                const formattedValue = Number(value).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                // Ajoute le symbole de la devise
                return `${formattedValue} ${currencySymbol}`;
            };
            
            // **3. UTILISER LA FONCTION DE FORMATAGE AVEC LA DEVISE**
            $('#modalDisplayReference').val(reference);
            $('#modalTotalAmount').val(formatCurrency(total, devise));
            $('#modalAmountAlreadyPaid').val(formatCurrency(paid, devise));
            $('#modalReferenceColis').val(reference);
            $('#modalColisIds').val(colisIds);
            
            $('#modalNewPaymentAmount').val('').removeClass('is-invalid');
            $('#paymentAmountError').text('').hide();

            $('#paymentModal').modal('show');
        });

        // Soumission du formulaire de paiement (inchangé)
        $('#paymentForm').on('submit', function(e) {
            e.preventDefault();
            var newPaymentAmount = parseFloat($('#modalNewPaymentAmount').val());
            var remainingDue = parseFloat($('#paymentForm').data('remaining-due'));
            $('#modalNewPaymentAmount').removeClass('is-invalid');
            $('#paymentAmountError').hide();

            if (isNaN(newPaymentAmount) || newPaymentAmount <= 0) {
                $('#modalNewPaymentAmount').addClass('is-invalid').siblings('.invalid-feedback').text('Veuillez saisir un montant valide et positif.').show();
                return;
            }
            if (newPaymentAmount > remainingDue + 0.01) {
                $('#modalNewPaymentAmount').addClass('is-invalid').siblings('.invalid-feedback').text('Le montant ne peut pas dépasser le solde restant.').show();
                return;
            }

            var submitButton = $('#submitPaymentBtn');
            submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Enregistrement...');

            $.ajax({
                url: '{{ route("colis.valide.payer") }}',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: (response) => {
                    $('#paymentModal').modal('hide');
                    Swal.fire({ icon: 'success', title: 'Succès!', text: response.success, timer: 2000, showConfirmButton: false });
                    table.ajax.reload(null, false);
                },
                error: (xhr) => {
                    let errorMsg = 'Une erreur est survenue.';
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        $('#modalNewPaymentAmount').addClass('is-invalid').siblings('.invalid-feedback').text(xhr.responseJSON.errors.montant_a_payer[0]).show();
                    } else if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    }
                    Swal.fire({ icon: 'error', title: 'Erreur!', text: errorMsg });
                },
                complete: () => {
                    submitButton.prop('disabled', false).html('Enregistrer Paiement');
                }
            });
        });

        // Logique pour l'archivage (inchangé)
        $('#productTable tbody').on('click', '.delete-btn', function () {
            const deleteUrl = $(this).data('url');
            const reference = $(this).data('reference');
            Swal.fire({
                title: 'Êtes-vous sûr?',
                html: `Voulez-vous vraiment archiver le(s) colis avec la référence <strong>${reference}</strong> ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonText: 'Annuler',
                confirmButtonText: 'Oui, archiver!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        success: (response) => {
                            Swal.fire('Archivé!', response.success, 'success');
                            table.ajax.reload(null, false);
                        },
                        error: (xhr) => Swal.fire('Erreur!', xhr.responseJSON?.error || 'Une erreur est survenue.', 'error')
                    });
                }
            });
        });
    });
</script>
@endsection