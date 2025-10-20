@extends('AFT_LOUIS_BLERIOT.layouts.agent')

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
                    <h4 class="text-left mt-4">Colis Chargés</h4><br>
                    <div id="products-container">
                        <div class="text-right">
                            <button type="button" style="color: #fff;" class="btn gradient-orange-blue" data-bs-toggle="modal" data-bs-target="#scanner_entrepot">
                                Scanner pour charger
                            </button>
                        </div><br>
                        <div class="table-responsive">
                            <table id="productTable" class="table table-bordered table-striped display">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Nombre de colis</th>
                                        <th>Expéditeur</th>
                                        <th>Téléphone</th>
                                        <th>Agence Expéditeur</th>
                                        <th>Destinataire</th>
                                        <th>Téléphone</th>
                                        <th>Agence Destinataire</th>
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

    <!-- Modal de scan -->
    <div class="modal fade" id="scanner_entrepot" tabindex="-1" aria-labelledby="scannerEntrepotLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 600px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Scanner les colis pour le chargement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div id="reader"></div>
                    <p id="result" class="mt-3">Résultat : Aucun</p>
                    <div class="d-flex justify-content-center">
                        <button id="restartScan" class="btn btn-primary mt-3" style="display: none;">Relancer le scan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}

<script>
    var table;

    document.addEventListener("DOMContentLoaded", function () {
        const html5QrCode = new Html5Qrcode("reader");
        const resultElement = document.getElementById("result");
        const restartButton = document.getElementById("restartScan");
        const readerElement = document.getElementById("reader");
        const modal = document.getElementById("scanner_entrepot");

        // Fonction déclenchée lors d’un scan
        const onScanSuccess = (decodedText) => {
            const referenceMatch = decodedText.match(/Ref:\s*(\S+)/i);
            const idMatch = decodedText.match(/ID:\s*(\S+)/i);

            if (!referenceMatch || !idMatch) {
                resultElement.innerText = "❌ QR Code invalide.";
                return;
            }

            const referenceColis = referenceMatch[1];
            const identifiant = idMatch[1];
            resultElement.innerText = `📦 Scan détecté : ${decodedText}`;

            $.ajax({
                url: "{{ route('aftlb_scan.update.colis.charge') }}",
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                },
                data: { colisId: referenceColis, id: identifiant },
                success: function (response) {
                    resultElement.innerText = response.messages?.join("\n") || "✅ Colis chargé avec succès.";
                    if (table) {
                        table.ajax.reload(null, false);
                    }
                },
                error: function (error) {
                    let errorMsg = "Erreur de chargement du colis.";
                    if (error.responseJSON?.messages) {
                        errorMsg = error.responseJSON.messages.join("\n");
                    }
                    resultElement.innerText = `❌ ${errorMsg}`;
                },
            });

            // Stoppe la caméra après un scan
            html5QrCode.stop().then(() => {
                readerElement.style.display = "none";
                restartButton.style.display = "block";
            });
        };

        // Démarrage du scanner
        const startScanner = () => {
            readerElement.style.display = "block";
            restartButton.style.display = "none";
            resultElement.innerText = "Résultat : En attente...";

            html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                onScanSuccess
            ).catch((err) => {
                console.error("Impossible de démarrer le scanner :", err);
            });
        };

        // Quand la modale s'ouvre
        modal.addEventListener("shown.bs.modal", function () {
            setTimeout(startScanner, 300);
        });

        // Quand la modale se ferme
        modal.addEventListener("hidden.bs.modal", function () {
            html5QrCode.stop().catch(() => {});
        });

        // Relancer le scan manuellement
        restartButton.addEventListener("click", startScanner);
    });

    $(document).ready(function () {
        table = $("#productTable").DataTable({
            responsive: true,
            language: { url: "{{ asset('js/fr-FR.json') }}" },
            ajax: '{{ route("aftlb_scan.get.colis.charge") }}',
            columns: [
                { data: 'reference_colis' },
                { data: 'nombre_de_colis' },
                { data: null, render: (d) => `${d.expediteur_nom} ${d.expediteur_prenom}` },
                { data: 'expediteur_tel' },
                { data: 'expediteur_agence' },
                { data: null, render: (d) => `${d.destinataire_nom} ${d.destinataire_prenom}` },
                { data: 'destinataire_tel' },
                { 
                    data: 'destinataire_agence',
                    render: function(data) {
                        if (data === 'IPMS-SIMEX-CI Angre 8ème Tranche') return 'DS Translog Angré 8ème Tranche';
                        if (data === 'IPMS-SIMEX-CI') return 'DS Translog Carrefour Angré';
                        return data;
                    }
                },
                {
                    data: 'created_at',
                    render: function (data) {
                        if (!data) return '';
                        const date = new Date(data);
                        return `${('0' + date.getDate()).slice(-2)}/${('0' + (date.getMonth() + 1)).slice(-2)}/${date.getFullYear()}`;
                    }
                },
                { data: 'action', orderable: false, searchable: false }
            ],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Exporter en Excel',
                    title: 'MANIFESTE',
                    exportOptions: { columns: [0,1,2,3,4,5,6,7] }
                },
                {
                    extend: 'print',
                    text: 'Imprimer',
                    title: 'MANIFESTE',
                    exportOptions: { columns: [0,1,2,3,4,5,6,7] },
                    customize: function (win) {
                        var logoUrl = "{{ url('images/LOGOAFT.png') }}";
                        var logo = `<img src="${logoUrl}" alt="Logo" style="position:absolute; top:10px; left:20px; width:100px;">`;
                        $(win.document.body).prepend(logo);
                        $(win.document.body).find('h1').css('text-align', 'center').css('margin-top', '20px');
                        $(win.document.body).find('table').css('margin-top', '40px');
                    }
                }
            ],
        });
    });
</script>

<style>
#reader {
  width: 100%;
  height: 400px;
  border: 1px solid #c2bdbd; 
}
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
.dataTable-wrapper {
    width: 100%;
    max-width: 1000px;
    margin: 0 auto;
    padding: 15px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background: #f9f9f9;
}
.modal-dialog {
    max-width: 90%;
    margin: auto;
}
.dt-button {
    padding: 10px 10px;
    font-size: 14px;
}
@media (max-width: 768px) {
    h4 { font-size: 18px; }
    .btn { max-width: 200px; font-size: 16px; height: 40px; }
    .table-responsive { overflow-x: auto; }
}
</style>
@endsection
