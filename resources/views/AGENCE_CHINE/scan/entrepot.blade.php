@extends('AGENCE_CHINE.layouts.agent')

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
                <h5 class="modal-title">Scanner les colis pour la mise en entrepôt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="reader"></div>
                <p id="result" class="mt-3">Résultat : En attente...</p>
               <div class="d-flex justify-content-center">
                    <button id="restartScan" class="btn btn-primary mt-3" style="display: none;">
                        Relancer le scan
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Dépendances JS --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    let table;
    const html5QrCode = new Html5Qrcode("reader");
    const resultElement = document.getElementById("result");
    const restartButton = document.getElementById("restartScan");
    const readerElement = document.getElementById("reader");
    const modalElement = document.getElementById('scanner_entrepot');

    // -------------------------------
    // 1. Fonction succès du scan
    // -------------------------------
    const onScanSuccess = (decodedText) => {
        // Arrêter après un scan
        html5QrCode.stop().catch(err => console.error("Erreur stop caméra:", err));

        const referenceMatch = decodedText.match(/Ref:\s*(\S+)/i);
        const idMatch = decodedText.match(/ID:\s*(\S+)/i);

        if (!referenceMatch || !idMatch) {
            Swal.fire({ icon: 'error', title: 'Erreur', text: 'Format QR invalide.' });
            restartButton.style.display = 'block';
            return;
        }

        const referenceColis = referenceMatch[1];
        const identifiant = idMatch[1];

        resultElement.innerText = `Référence: ${referenceColis} | ID: ${identifiant}`;

        $.ajax({
            url: "{{ route('chine_scan.update.colis.entrepot') }}",
            type: "POST",
            data: { reference_colis: referenceColis, id: identifiant },
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    if (table) table.ajax.reload(null, false);
                } else {
                    Swal.fire({ icon: 'warning', title: 'Attention', text: response.message });
                }
                restartButton.style.display = 'block'; // on peut relancer
            },
            error: function (xhr) {
                Swal.fire({ icon: 'error', title: 'Erreur', text: xhr.responseJSON?.message || "Erreur inconnue." });
                restartButton.style.display = 'block';
            },
        });
    };

    // -------------------------------
    // 2. Lancer le scanner
    // -------------------------------
    const startScanner = () => {
        resultElement.innerText = "Résultat : En attente...";
        restartButton.style.display = "none";
        readerElement.style.display = "block";

        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            onScanSuccess
        ).catch((err) => {
            console.error("Impossible de démarrer:", err);
            resultElement.innerText = "Erreur: Caméra non accessible.";
        });
    };

    // -------------------------------
    // 3. Gestion modale
    // -------------------------------
    modalElement.addEventListener('shown.bs.modal', () => setTimeout(startScanner, 300));
    modalElement.addEventListener('hidden.bs.modal', () => html5QrCode.stop().catch(()=>{}));
    restartButton.addEventListener("click", startScanner);

    // -------------------------------
    // 4. DataTable
    // -------------------------------
    table = $("#productTable").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        language: { url: "{{ asset('js/fr-FR.json') }}" },
        ajax: '{{ route("chine_scan.get.colis.entrepot") }}',
        columns: [
            { data: 'statut_paiement', className: 'text-center' },
            { data: 'reference_colis' },
            { data: 'nombre_de_colis', className: 'text-center' },
            { data: null, render: (data,row) => (data.expediteur_nom||'')+' '+(data.expediteur_prenom||'') },
            { data: 'expediteur_tel' },
            { data: 'expediteur_agence' },
            { data: null, render: (data,row) => (data.destinataire_nom||'')+' '+(data.destinataire_prenom||'') },
            { data: 'destinataire_tel' },
            {
                data: 'destinataire_agence',
                render: function(data) {
                    if (data === 'IPMS-SIMEX-CI Angre 8ème Tranche') return 'DS Translog Angré 8ème Tranche';
                    if (data === 'IPMS-SIMEX-CI') return 'DS Translog Carrefour Angré';
                    return data;
                }
            },
            { data: 'created_at' },
            { data: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        dom: 'Bfrtip',
        buttons: [
            { extend: 'excelHtml5', text: 'Exporter en Excel', title: 'Colis en Entrepôt', exportOptions: { columns: [1,2,3,4,5,6,7,8,9] } },
            { extend: 'print', text: 'Imprimer', title: 'Colis en Entrepôt', exportOptions: { columns: [1,2,3,4,5,6,7,8,9] } }
        ],
        order: [[9, 'desc']]
    });
});
</script>

<style>
#reader { width:100%; max-width:500px; margin:auto; border:1px solid #ccc; }
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
@endsection
