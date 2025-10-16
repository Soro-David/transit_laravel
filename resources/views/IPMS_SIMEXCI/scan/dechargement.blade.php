@extends('IPMS_SIMEXCI.layouts.agent')
@section('content-header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection

@section('content')
<section class="py-3">
    <div class="row">
        <div class="col-md-12">
            <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                <h4 class="text-left mt-4">Liste des colis Déchargés</h4><br>
                <div class="text-right">
                    <button type="button" style="color: #fff;" class="btn gradient-orange-blue" data-bs-toggle="modal" data-bs-target="#scanner_entrepot">
                        Scanner pour décharger
                    </button>
                </div><br>
                <div id="products-container">
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
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for scanning -->
    <div class="modal fade" id="scanner_entrepot" tabindex="-1" aria-labelledby="scannerEntrepotLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 600px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Scanner les colis pour le déchargement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="reader"></div>
                    <p id="result">Résultat : Aucun</p>
                    <div class="d-flex justify-content-center">
                        <button id="restartScan" class="btn btn-primary mt-3" style="display: none;">Relancer le scan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODALE DE PAIEMENT --}}
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

<style>
    #productTable th, #productTable td { vertical-align: middle; }
    .action-buttons-container { display: flex; justify-content:center; gap:5px; }
    .is-invalid { border-color: #dc3545; }
    .invalid-feedback { color:#dc3545; font-size:.875em; display:none; }
    .is-invalid ~ .invalid-feedback { display:block; }
    
    #reader {
        width: 100%;
        height: 400px;
        border: 1px solid #c2bdbd; 
        position: relative;
    }
    
    .camera-selector {
        margin-bottom: 10px;
    }
    
    .scanning-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        z-index: 10;
    }
</style>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    let html5QrCode = null;
    let currentCameraId = null;

    // Configuration AJAX globale
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Fonction pour détecter la caméra arrière
    function findRearCamera(cameras) {
        // Priorité 1: Caméras avec label contenant "back", "rear", "arrière"
        let rearCamera = cameras.find(cam => 
            cam.label.toLowerCase().includes('back') || 
            cam.label.toLowerCase().includes('rear') ||
            cam.label.toLowerCase().includes('arrière') ||
            cam.label.toLowerCase().includes('environment') ||
            cam.label.toLowerCase().includes('world')
        );
        
        // Priorité 2: Caméras avec facingMode "environment"
        if (!rearCamera) {
            rearCamera = cameras.find(cam => {
                const constraints = cam.getCapabilities ? cam.getCapabilities() : {};
                return constraints.facingMode && constraints.facingMode.includes('environment');
            });
        }
        
        // Priorité 3: Dernière caméra (souvent la caméra arrière sur mobile)
        if (!rearCamera && cameras.length > 1) {
            rearCamera = cameras[cameras.length - 1];
        }
        
        // Fallback: Première caméra
        return rearCamera || cameras[0];
    }

    // Fonction pour démarrer le scanner
    function startScanner(cameraId = null) {
        const $result = $('#result');
        const $restartBtn = $('#restartScan');
        const $reader = $('#reader');

        $result.text('Résultat : Initialisation du scanner...');
        $restartBtn.hide();

        // Vider le conteneur du scanner
        $reader.empty();
        
        // Créer un overlay de chargement
        $reader.append('<div class="scanning-overlay">Initialisation de la caméra...</div>');

        html5QrCode = new Html5Qrcode("reader");

        // Obtenir la liste des caméras
        Html5Qrcode.getCameras().then(cameras => {
            if (cameras.length === 0) {
                $reader.empty().append('<div class="scanning-overlay">Aucune caméra détectée</div>');
                $result.text("❌ Aucune caméra disponible sur cet appareil.");
                $restartBtn.show();
                return;
            }

            console.log("Caméras disponibles:", cameras);
            
            // Si aucune caméra spécifique n'est demandée, trouver la caméra arrière
            const targetCamera = cameraId ? 
                cameras.find(cam => cam.id === cameraId) : 
                findRearCamera(cameras);

            if (!targetCamera) {
                $reader.empty().append('<div class="scanning-overlay">Caméra non trouvée</div>');
                $result.text("❌ Caméra sélectionnée non disponible.");
                $restartBtn.show();
                return;
            }

            console.log("Utilisation de la caméra:", targetCamera.label, targetCamera.id);
            currentCameraId = targetCamera.id;

            // Démarrer le scanner
            html5QrCode.start(
                targetCamera.id,
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                },
                (decodedText) => {
                    // QR Code détecté
                    console.log("QR Code scanné:", decodedText);
                    
                    // Extraction référence et ID
                    const refMatch = decodedText.match(/Ref:\s*(\S+)/i);
                    const idMatch = decodedText.match(/ID:\s*(\S+)/i);

                    if (!refMatch || !idMatch) {
                        $result.text("❌ QR Code invalide - Format attendu: 'Ref: XXX ID: YYY'");
                        return;
                    }

                    const referenceColis = refMatch[1];
                    const identifiant = idMatch[1];

                    // Arrêter le scanner
                    html5QrCode.stop().then(() => {
                        $reader.hide();
                        $restartBtn.show();
                        $result.text(`✅ QR Code détecté: ${referenceColis}`);

                        // Envoyer les données au serveur
                        $.ajax({
                            url: "{{ route('ipms_scan.update.colis.decharge') }}",
                            method: "POST",
                            dataType: "json",
                            data: {
                                colisId: referenceColis,
                                id: identifiant
                            },
                            success: (resp) => {
                                if (resp.success) {
                                    $result.text(`✅ ${resp.message || 'Colis déchargé avec succès!'}`);
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Succès!',
                                        text: `Colis ${referenceColis} déchargé avec succès`,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    
                                    // Recharger le tableau après un délai
                                    setTimeout(() => {
                                        $('#scanner_entrepot').modal('hide');
                                        if (typeof table !== 'undefined') {
                                            table.ajax.reload(null, false);
                                        }
                                    }, 1500);
                                } else {
                                    $result.text(`❌ ${resp.messages?.join(' ') || 'Erreur lors du déchargement'}`);
                                    $restartBtn.show();
                                }
                            },
                            error: (xhr) => {
                                let msg = "Erreur serveur lors du déchargement";
                                if (xhr.responseJSON?.message) {
                                    msg = xhr.responseJSON.message;
                                } else if (xhr.responseJSON?.messages) {
                                    msg = xhr.responseJSON.messages.join(' ');
                                }
                                $result.text(`❌ ${msg}`);
                                $restartBtn.show();
                            }
                        });
                    }).catch(err => {
                        console.error("Erreur lors de l'arrêt du scanner:", err);
                    });

                },
                (errorMessage) => {
                    // Ignorer les erreurs de décodage continues
                    // console.log("Scan en cours...", errorMessage);
                }
            ).then(() => {
                // Scanner démarré avec succès
                $reader.find('.scanning-overlay').remove();
                $result.text('✅ Scanner activé - Scannez un QR Code');
            }).catch(err => {
                console.error("Erreur démarrage scanner:", err);
                $reader.empty().append('<div class="scanning-overlay">Erreur de démarrage</div>');
                $result.text("❌ Impossible de démarrer le scanner: " + err.message);
                $restartBtn.show();
            });

        }).catch(err => {
            console.error("Erreur détection caméras:", err);
            $reader.empty().append('<div class="scanning-overlay">Erreur caméra</div>');
            $result.text("❌ Erreur d'accès aux caméras: " + err.message);
            $restartBtn.show();
        });
    }

    // Gestion de l'ouverture de la modale
    $('#scanner_entrepot').on('shown.bs.modal', function () {
        startScanner();
    });

    // Gestion de la fermeture de la modale
    $('#scanner_entrepot').on('hidden.bs.modal', function () {
        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().catch(err => {
                console.error("Erreur arrêt scanner:", err);
            });
        }
        $('#reader').show();
        $('#restartScan').hide();
        $('#result').text('Résultat : Aucun');
    });

    // Bouton relancer le scan
    $('#restartScan').on('click', function() {
        $('#reader').show();
        $('#restartScan').hide();
        startScanner(currentCameraId);
    });

    // Initialisation de DataTables
    var table = $("#productTable").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        language: { url: "{{ asset('js/fr-FR.json') }}" },
        ajax: '{{ route("ipms_scan.get.colis.decharge") }}',
        columns: [
            { data: 'statut_paiement', name: 'statut_paiement', orderable: false, searchable: false, className: 'text-center' },
            { data: 'reference_colis', name: 'reference_colis' },
            { data: 'nombre_de_colis', name: 'nombre_de_colis', className: 'text-center' },
            { data: null, name: 'expediteur_nom', render: (data, type, row) => (row.expediteur_nom || '') + ' ' + (row.expediteur_prenom || ''), searchable: true, orderable: true },
            { data: 'expediteur_tel', name: 'expediteurs.tel' },
            { data: null, name: 'destinataire_nom', render: (data, type, row) => (row.destinataire_nom || '') + ' ' + (row.destinataire_prenom || ''), searchable: true, orderable: true },
            { data: 'destinataire_tel', name: 'destinataires.tel' },
            { data: 'destinataire_agence', name: 'destinataires.agence' },
            { data: 'etat', name: 'colis.etat' },
            { data: 'created_at', name: 'colis.created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        dom: 'Bfrtip',
        buttons: ['excel', 'pdf', 'print'],
        order: [[1, 'desc']]
    });

    // Gestion de la modale de paiement
    $('#productTable tbody').on('click', '.pay-btn', function (e) {
        e.preventDefault();
        var button = $(this);
        var reference = button.data('reference');
        var total = parseFloat(button.data('total')).toFixed(2);
        var paid = parseFloat(button.data('paid')).toFixed(2);
        var colisIds = JSON.stringify(button.data('colis-ids'));

        function formatCurrency(value) {
            const num = Number(value);
            if (isNaN(num)) return 'N/A';
            return num.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR', minimumFractionDigits: 2 });
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
        var form = $(this);
        var submitButton = $('#submitPaymentBtn');
        var originalButtonText = submitButton.html();
        
        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Enregistrement...');

        $('#modalNewPaymentAmount').removeClass('is-invalid');
        $('#paymentAmountError').text('').hide();

        $.ajax({
            url: '{{ route("ipms_colis.valide.payer") }}',
            type: 'POST',
            data: form.serialize(),
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
                var errorMessage = 'Une erreur est survenue lors de l\'enregistrement du paiement.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    }
                    if (xhr.status === 422 && xhr.responseJSON.details) {
                        if (xhr.responseJSON.details.montant_a_payer) {
                            $('#modalNewPaymentAmount').addClass('is-invalid');
                            $('#paymentAmountError').text(xhr.responseJSON.details.montant_a_payer[0]).show();
                            errorMessage = 'Veuillez corriger les erreurs dans le formulaire.';
                        }
                    }
                }
                Swal.fire({ icon: 'error', title: 'Erreur!', text: errorMessage });
            },
            complete: function () {
                submitButton.prop('disabled', false).html(originalButtonText);
            }
        });
    });

    // Gestion de la suppression/archivage
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
                        Swal.fire('Archivé!', response.success || `Le(s) colis ${reference} archivés.`, 'success');
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