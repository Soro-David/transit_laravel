@extends('admin.layouts.admin')
@section('content-header')
{{-- CSRF Token pour les requêtes AJAX --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
{{-- Font Awesome --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection

@section('content')
<section class="py-3">
        <form action="" method="POST" class="mt-4">
            @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                            <h4 class="text-left mt-4">Colis en entrepot</h4><br>
                            <div id="products-container">
                                <div class="text-right">
                                    <button type="button" style="color: #fff;" class="btn gradient-orange-blue" data-bs-toggle="modal" data-bs-target="#scanner_entrepot">
                                        Scanner pour la mise en entrepot
                                    </button>
                                </div><br>
                                <div class="table-responsive">
                                    <table id="productTable" class="table table-bordered table-striped display">
                                        <thead>
                                            <tr>
                                                <th>ST Paiement</th>
                                                <th>Reference</th>
                                                <th>Nombre de colis</th>
                                                <th>Expéditeur</th>
                                                <th>Téléphone</th>
                                                <th>Agence Expéditeur</th>
                                                <th>Destinataire</th>
                                                <th>Téléphone</th>
                                                <th>Agence Destination</th>
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
                </div>
        </form>

    <!-- Modal for editing -->
    <div class="modal fade" id="scanner_entrepot" tabindex="-1" aria-labelledby="scannerEntrepotLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 600px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Scanner les colis pour le chargement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="reader" ></div>
                    <p id="result">Résultat : Aucun</p>
                    <div class="d-flex justify-content-center">
                        <button id="restartScan" class="btn btn-primary mt-3" style="display: none;">Relancer le scan</button>
                    </div>
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

    <!-- JavaScript for DataTable and Export -->
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
            console.error("Impossible d'extraire la référence ou l'identifiant.");
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
            url: "{{ route('scan.update.colis.entrepot') }}",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
            data: {
                colisId: referenceColis, // Envoie la référence extraite
                id: identifiant,         // Envoie l'identifiant extrait
            },
            success: function (response) {
                console.log("Réponse du serveur :", response);
                // Affichage des messages retournés par le serveur
                if (response.success) {
                    resultElement.innerText = response.messages.join("\n");
                } else {
                    resultElement.innerText = "Erreur : " + response.messages.join("\n");
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
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    var table = $("#productTable").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        language: { url: "{{ asset('js/fr-FR.json') }}" },
        ajax: '{{ route("scan.get.colis.entrepot") }}',
        columns: [
            { data: 'statut_paiement', className: 'text-center' },
            { data: 'reference_colis' },
            { data: 'nombre_de_colis', className: 'text-center' },
            {
                data: null,
                render: function (data, type, row) { return (row.expediteur_nom || '') + ' ' + (row.expediteur_prenom || ''); }
            },
            { data: 'expediteur_tel' },
            {
                data: null,
                render: function (data, type, row) { return (row.destinataire_nom || '') + ' ' + (row.destinataire_prenom || ''); }
            },
            { data: 'destinataire_tel' },
            {
                data: 'destinataire_agence',
                render: function(data) {
                    if (data === 'IPMS-SIMEX-CI Angre 8ème Tranche') return 'DS Translog Angré 8ème Tranche';
                    if (data === 'IPMS-SIMEX-CI') return 'DS Translog Carrefour Angré';
                    return data;
                }
            },
            { data: 'etat' },
            { data: 'created_at' },
            { 
                data: 'action',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    return `<div class="action-buttons">${data}</div>`;
                }
            }
        ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5', text: 'Exporter en Excel', title: 'Liste des Colis en Entrepôt',
                exportOptions: { columns: [1,2,3,4,5,6,7,8,9] }
            },
            {
                extend: 'print', text: 'Imprimer', title: 'Liste des Colis en Entrepôt',
                exportOptions: { columns: [1,2,3,4,5,6,7,8,9] }
            }
        ],
        order: [[ 1, 'desc' ]]
    });

    // Rafraîchissement automatique toutes les 5 secondes
    setInterval(function() {
        table.ajax.reload(null, false); 
    }, 5000);

    // --- Logique modale paiement ---
    $('#productTable tbody').on('click', '.pay-btn', function (e) {
        e.preventDefault();
        var button = $(this);
        var reference = button.data('reference');
        var total = parseFloat(button.data('total'));
        var paid = parseFloat(button.data('paid'));
        var colisIds = JSON.stringify(button.data('colis-ids'));
        var remainingDue = total - paid;

        $('#paymentForm').data('remaining-due', remainingDue.toFixed(2));

        function formatCurrency(value) {
            return Number(value).toLocaleString('fr-CI', { style: 'currency', currency: 'XOF' });
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
            return;
        }

        var form = $(this);
        var submitButton = $('#submitPaymentBtn');
        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Enregistrement...');

        $.ajax({
            url: '{{ route("colis.valide.payer") }}',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                $('#paymentModal').modal('hide');
                Swal.fire({ icon: 'success', title: 'Succès!', text: response.success, timer: 2500, showConfirmButton: false });
                table.ajax.reload(null, false);
            },
            error: function(xhr) {
                var errorMessage = 'Une erreur est survenue.';
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    $('#modalNewPaymentAmount').addClass('is-invalid');
                    $('#paymentAmountError').text(xhr.responseJSON.errors.montant_a_payer[0]).show();
                    errorMessage = 'Veuillez corriger les erreurs.';
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                Swal.fire({ icon: 'error', title: 'Erreur!', text: errorMessage });
            },
            complete: function () {
                submitButton.prop('disabled', false).html('Enregistrer Paiement');
            }
        });
    });

    $('#productTable tbody').on('click', '.delete-btn', function (e) {
        e.preventDefault();
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
                    success: function (response) {
                        Swal.fire('Archivé!', response.success, 'success');
                        table.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        let errorMsg = xhr.responseJSON ? xhr.responseJSON.error : 'Erreur lors de l\'archivage.';
                        Swal.fire('Erreur!', errorMsg, 'error');
                    }
                });
            }
        });
    });
});

</script>
    
    {{-- <script src="'public/js/Html5-qrcode.js'"></script> --}}
    
</section>

<style>
    
     #reader {
      width: 100%;
      height: 400px;
      border: 1px solid #c2bdbd; 
    }

    /* ==== CORRECTION POUR L'ALIGNEMENT DES BOUTONS D'ACTION ==== */
    .action-buttons {
        display: flex;
        justify-content: center; /* Centre les boutons dans la cellule */
        align-items: center;     /* Aligne verticalement au cas où */
        gap: 5px;                /* Ajoute un petit espace entre les boutons */
        flex-wrap: nowrap;       /* Empêche les boutons de passer à la ligne */
    }

    .action-buttons .btn {
        width: auto !important; /* Annule la largeur de 100% spécifiquement pour ces boutons */
        flex-shrink: 0;         /* Empêche les boutons de se rétrécir s'il manque de place */
    }
    /* ==== FIN DE LA CORRECTION ==== */


    .btn {
        width: 100%; /* Les boutons s'adaptent à la largeur du conteneur */
        max-width: 280px; /* Largeur maximale sur les grands écrans */
        font-size: 16px;
        height: 40px;

    }

    .dataTable-wrapper {
        width: 100%; /* Prend toute la largeur disponible */
        max-width: 1000px; /* Largeur maximale pour les grands écrans */
        margin: 0 auto; /* Centrer le tableau */
        padding: 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background: #f9f9f9;
    }

    #camera, #snapshot {
        max-width: 100%; /* Rendre la caméra et l'image capturée responsive */
        height: auto;
        margin: 10px auto;
    }

    .modal-dialog {
        max-width: 90%; /* Rendre les modals adaptatifs */
        margin: auto;
    }

    .dt-button {
        padding: 10px 10px;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        h4 {
            font-size: 18px; /* Réduction de la taille des titres */
        }

        .btn {
            font-size: 14px;
            padding: 10px;
        }

        .table-responsive {
            overflow-x: auto; /* Assurer un défilement horizontal sur les petits écrans */
        }
    }
    #camera {
            width: 100%;
            max-height: 300px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        .image-preview {
            margin-top: 15px;
            width: 100%;
            max-height: 300px;
            border: 2px dashed #ffa500;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f9f9f9;
            position: relative;
        }

        .image-preview canvas {
            max-width: 100%;
            max-height: 100%;
        }

        .image-placeholder {
            color: #ccc;
            font-size: 18px;
            position: absolute;
            text-align: center;
        }

</style>
@endsection