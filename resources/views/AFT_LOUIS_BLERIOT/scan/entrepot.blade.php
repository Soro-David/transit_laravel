@extends('AFT_LOUIS_BLERIOT.layouts.agent')

@section('content-header')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<section class="py-3">
    <div class="row">
        <div class="col-md-12">
            <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                <h4 class="text-left mt-4">Colis en entrepôt</h4><br>
                
                <div class="text-right mb-3">
                    <button type="button" style="color: #fff;" class="btn gradient-orange-blue" data-bs-toggle="modal" data-bs-target="#scanner_entrepot">
                        Scanner pour la mise en entrepôt
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table id="productTable" class="table table-bordered table-striped display" style="width:100%">
                        <thead>
                            <tr>
                                <th>ST Paiement</th>
                                <th>Référence</th>
                                <th>Nbr Colis</th>
                                <th>Expéditeur</th>
                                <th>Téléphone (Exp)</th>
                                <th>Agence Exp.</th>
                                <th>Destinataire</th>
                                <th>Téléphone (Dest)</th>
                                <th>Agence Dest.</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MODAL DU SCANNER QR CODE -->
<div class="modal fade" id="scanner_entrepot" tabindex="-1" aria-labelledby="scannerEntrepotLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scanner les colis pour le chargement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="reader"></div>
                <p id="result" class="mt-2">Résultat : Aucun</p>
                <div class="d-flex justify-content-center">
                    <button id="restartScan" class="btn btn-primary mt-3" style="display: none;">Relancer le scan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DE PAIEMENT -->
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
                        <label for="modalNewPaymentAmount" class="form-label">Montant du Paiement <span class="text-danger">*</span></label>
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

{{-- Dépendances JS --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const html5QrCode = new Html5Qrcode("reader");
    const resultElement = document.getElementById("result");
    const restartButton = document.getElementById("restartScan");
    const readerElement = document.getElementById("reader");
    const modal = document.getElementById("scanner_entrepot");

    const onScanSuccess = (decodedText) => {
        // Extraction de la référence et de l'identifiant à l'aide d'expressions régulières
        const referenceMatch = decodedText.match(/Ref:\s*(\S+)/i);
        const idMatch = decodedText.match(/ID:\s*(\S+)/i);

        // Vérifier que les deux valeurs ont bien été extraites
        if (!referenceMatch || !idMatch) {
            console.error("Impossible d'extraire la référence ou l'identifiant.",referenceMatch);
            resultElement.innerText = "Erreur : données QR code invalides.";
            return;
        }

        // Extraction des valeurs capturées
        const referenceColis = referenceMatch[1];
        const identifiant = idMatch[1];

        console.log(`Code détecté : ${decodedText}`);
        console.log(`Référence : ${referenceColis}`);
        console.log(`Identifiant : ${identifiant}`);
        resultElement.innerText = `Résultat : ${decodedText}`;

        // Envoi des données extraites via une requête AJAX pour mettre à jour l'état du colis
        $.ajax({
            url: "{{ route('aftlb_scan.update.colis.entrepot') }}",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
            data: {
                colisId: referenceColis, // Envoie la référence extraite
                id: identifiant,          // Envoie l'identifiant extrait
            },
            success: function (response) {
                console.log("Réponse du serveur helo :", response);
                // Affichage des messages retournés par le serveur
                if (response.messages && Array.isArray(response.messages)) {
                    resultElement.innerText = response.messages.join("\n");
                } else {
                    resultElement.innerText = "Réponse inconnue du serveur.";
                }
            },
            error: function (error) {
                console.error("Erreur lors du chargement :", error);
                if (error.responseJSON && error.responseJSON.messages) {
                    resultElement.innerText = error.responseJSON.messages.join("\n");
                } else {
                    resultElement.innerText = "Erreur de chargement du colis.";
                }
            },
        });

        // Arrêt du scanner et mise à jour de l'affichage
        html5QrCode
            .stop()
            .then(() => {
                readerElement.style.display = "none";
                restartButton.style.display = "block";
            })
            .catch((err) => {
                console.error(`Erreur lors de l'arrêt du scanner : ${err}`);
            });
    };

    const startScanner = () => {
        // Affiche l'élément du lecteur
        readerElement.style.display = "block";

        // Vérifie que l'élément #reader a des dimensions valides
        if (!readerElement || readerElement.offsetWidth === 0 || readerElement.offsetHeight === 0) {
            console.error("Erreur : L'élément #reader n'a pas de dimensions valides.");
            return;
        }

        // Démarrage du scanner avec les options définies
        html5QrCode
            .start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                onScanSuccess
            )
            .then(() => {
                resultElement.innerText = "Résultat : En attente...";
                restartButton.style.display = "none";
            })
            .catch((err) => {
                console.error(`Impossible de démarrer le scanner : ${err}`);
            });
    };

    // Démarrer le scanner dès que la modale est affichée
    modal.addEventListener("shown.bs.modal", function () {
        setTimeout(startScanner, 500);
    });

    // Arrêter le scanner lorsque la modale est fermée
    modal.addEventListener("hidden.bs.modal", function () {
        html5QrCode
            .stop()
            .then(() => {
                console.log("Scanner arrêté avec succès.");
            })
            .catch((err) => {
                console.error(`Erreur lors de l'arrêt du scanner : ${err}`);
            });
    });

    // Bouton de redémarrage du scanner
    restartButton.addEventListener("click", startScanner);
});


$(document).ready(function () {
    // Configuration globale AJAX pour inclure le token CSRF
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // ===================================================================
    // INITIALISATION DE DATATABLES
    // ===================================================================
    var table = $("#productTable").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        language: { url: "{{ asset('js/fr-FR.json') }}" },
        ajax: '{{ route("aftlb_scan.get.colis.entrepot") }}',
        columns: [
            // Les colonnes ici doivent correspondre EXACTEMENT aux <th> du HTML
            { data: 'statut_paiement', name: 'statut_paiement', className: 'text-center' },
            { data: 'reference_colis', name: 'reference_colis' },
            { data: 'nombre_de_colis', name: 'nombre_de_colis', className: 'text-center' },
            {
                data: null, name: 'expediteur_nom',
                render: (data, type, row) => `${row.expediteur_nom || ''} ${row.expediteur_prenom || ''}`.trim()
            },
            { data: 'expediteur_tel', name: 'expediteur_tel' },
            { data: 'expediteur_agence', name: 'expediteur_agence' },
            {
                data: null, name: 'destinataire_nom',
                render: (data, type, row) => `${row.destinataire_nom || ''} ${row.destinataire_prenom || ''}`.trim()
            },
            { data: 'destinataire_tel', name: 'destinataire_tel' },
            {
                data: 'destinataire_agence', name: 'destinataire_agence',
                render: function(data) {
                    if (data === 'IPMS-SIMEX-CI Angre 8ème Tranche') return 'DS Translog Angré 8ème Tranche';
                    if (data === 'IPMS-SIMEX-CI') return 'DS Translog Carrefour Angré';
                    return data;
                }
            },
            { data: 'created_at', name: 'created_at' },
            { 
                data: 'action', name: 'action',
                orderable: false, searchable: false, className: 'text-center'
            }
        ],
        dom: 'Bfrtip',
        buttons: [
            { extend: 'excelHtml5', text: 'Exporter en Excel', title: 'Liste des Colis en Entrepôt', exportOptions: { columns: [1,2,3,4,5,6,7,8,9] } },
            { extend: 'print', text: 'Imprimer', title: 'Liste des Colis en Entrepôt', exportOptions: { columns: [1,2,3,4,5,6,7,8,9] } }
        ],
        order: [[ 9, 'desc' ]] // Trier par la colonne date (10ème, index 9)
    });

    // Rafraîchissement automatique de la table toutes les 5 secondes
    // Utile pour un dashboard, mais peut être remplacé par des WebSockets pour plus d'efficacité.
    setInterval(() => table.ajax.reload(null, false), 5000);

    // ===================================================================
    // GESTION DE LA MODALE DE PAIEMENT
    // ===================================================================
    $('#productTable tbody').on('click', '.pay-btn', function () {
        const button = $(this);
        const remainingDue = parseFloat(button.data('total')) - parseFloat(button.data('paid'));

        // Formateur de devise
        const formatCurrency = (value) => Number(value).toLocaleString('fr-CI', { style: 'currency', currency: 'XOF' });

        $('#paymentForm').data('remaining-due', remainingDue.toFixed(2));
        $('#modalDisplayReference').val(button.data('reference'));
        $('#modalTotalAmount').val(formatCurrency(button.data('total')));
        $('#modalAmountAlreadyPaid').val(formatCurrency(button.data('paid')));
        $('#modalReferenceColis').val(button.data('reference'));
        $('#modalColisIds').val(JSON.stringify(button.data('colis-ids')));
        
        // Réinitialiser le champ et les erreurs
        $('#modalNewPaymentAmount').val('').removeClass('is-invalid');
        $('#paymentAmountError').text('').hide();
        
        $('#paymentModal').modal('show');
    });

    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        
        const newPaymentAmount = parseFloat($('#modalNewPaymentAmount').val());
        const remainingDue = parseFloat($('#paymentForm').data('remaining-due'));

        // Validation front-end
        if (isNaN(newPaymentAmount) || newPaymentAmount <= 0) {
            $('#modalNewPaymentAmount').addClass('is-invalid').focus();
            $('#paymentAmountError').text('Veuillez saisir un montant valide et positif.').show();
            return;
        }
        if (newPaymentAmount > remainingDue + 0.01) { // Tolérance
            $('#modalNewPaymentAmount').addClass('is-invalid').focus();
            $('#paymentAmountError').text('Le montant ne peut pas dépasser le solde restant.').show();
            return;
        }

        const submitButton = $('#submitPaymentBtn');
        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Enregistrement...');

        $.ajax({
            url: '{{ route("aftlb_colis.valide.payer") }}',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: (response) => {
                $('#paymentModal').modal('hide');
                Swal.fire({ icon: 'success', title: 'Succès!', text: response.success, timer: 2500, showConfirmButton: false });
                table.ajax.reload(null, false);
            },
            error: (xhr) => {
                let errorMsg = 'Une erreur est survenue.';
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    $('#modalNewPaymentAmount').addClass('is-invalid');
                    $('#paymentAmountError').text(xhr.responseJSON.errors.montant_a_payer[0]).show();
                } else if (xhr.responseJSON?.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                Swal.fire({ icon: 'error', title: 'Erreur!', text: errorMsg });
            },
            complete: () => submitButton.prop('disabled', false).html('Enregistrer Paiement')
        });
    });

    // ===================================================================
    // GESTION DE L'ARCHIVAGE (SUPPRESSION)
    // ===================================================================
    $('#productTable tbody').on('click', '.delete-btn', function () {
        const button = $(this);
        const deleteUrl = button.data('url');
        const reference = button.data('reference');

        Swal.fire({
            title: 'Êtes-vous sûr?',
            html: `Voulez-vous vraiment archiver le(s) colis avec la référence <strong>${reference}</strong> ?`,
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
                    success: (response) => {
                        Swal.fire('Archivé!', response.success, 'success');
                        table.ajax.reload(null, false);
                    },
                    error: (xhr) => {
                        let errorMsg = xhr.responseJSON?.error || 'Erreur lors de l\'archivage.';
                        Swal.fire('Erreur!', errorMsg, 'error');
                    }
                });
            }
        });
    });
});
</script>

<style>
    /* Styles pour le scanner QR Code */
    #reader {
        width: 100%;
        height: 400px;
        border: 1px solid #c2bdbd; 
    }

    /* Correction pour l'alignement des boutons d'action dans DataTables */
    .action-buttons {
        display: flex;
        justify-content: center; /* Centre les boutons dans la cellule */
        align-items: center;
        gap: 5px;
        flex-wrap: nowrap;
    }
    .action-buttons .btn {
        width: auto !important; /* Annule la largeur de 100% pour ces boutons */
        flex-shrink: 0;
    }

    /* Styles généraux responsives */
    .btn {
        width: 100%;
        max-width: 280px;
        font-size: 16px;
        height: 40px;
    }
    .modal-dialog {
        max-width: 90%;
        margin: auto;
    }
    .dt-button {
        padding: 10px;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        h4 {
            font-size: 18px;
        }
        .btn {
            font-size: 14px;
            padding: 10px;
        }
    }
</style>
@endsection