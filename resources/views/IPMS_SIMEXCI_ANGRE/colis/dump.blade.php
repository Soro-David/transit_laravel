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
                <h4 class="text-left mt-4 mb-3">
                    <i class="fa fa-box"></i> Liste des colis à arrivés
                </h4>

                <div class="table-responsive">
                    <table id="productTable" class="table table-bordered table-striped display nowrap" style="width:100%">
                        <thead class="table-warning text-center align-middle">
                            <tr>
                                <th>St. Paiement</th>
                                <th>Référence</th>
                                <th>Produit</th>
                                <th>Nb. Colis</th>
                                <th>Montant Total</th>
                                <th>Payé</th>
                                <th>Reste à Payer</th>
                                <th>Expéditeur</th>
                                <th>Tél. Exp</th>
                                <th>Destinataire</th>
                                <th>Tél. Dest.</th>
                                <th>Status Colis</th>
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

{{-- ========= STYLES PERSONNALISÉS ========= --}}
<style>
        #productTable {
            width: 100% !important;
            font-size: 13px;
            border-spacing: 0 8px !important;
            border-collapse: separate !important;
        }

        #productTable th, #productTable td {
            text-align: center;
            vertical-align: middle;
            padding: 10px 12px !important;
        }

        #productTable th {
            background-color: #fff3cd;
            font-weight: 600;
            color: #000;
            white-space: nowrap;
        }

        #productTable td {
            background: #fff;
            border: 1px solid #eee;
        }

        #productTable tbody tr:hover td {
            background-color: #fef9e7;
        }

        .dt-buttons {
            margin-bottom: 15px;
        }

        .action-buttons-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            flex-wrap: nowrap;
        }

        @media (max-width: 768px) {
            #productTable { font-size: 12px; }
            .dt-buttons { text-align: center; }
            .dt-button { display: block; margin: 5px auto; width: 90%; }
        }
</style>

{{-- ========= SCRIPT DATATABLE ========= --}}
<script>
$(document).ready(function () {
    const EUR_TO_FCFA_RATE = parseFloat("{{ App\Services\CurrencyConverterService::FCFA_TO_EUR_RATE }}") || 655.957;
    const logoBase64 = 'data:image/png;base64,{{ base64_encode(file_get_contents(public_path("images/LOGOAFT.png"))) }}';

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    const table = $("#productTable").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        scrollX: true,
        language: { url: "{{ asset('js/fr-FR.json') }}" },
        ajax: '{{ route("ipms_angre_colis.get.colis.dump") }}',
        columns: [
            { data: 'statut_paiement' },
            { data: 'reference_colis' },
            { data: 'nom_produit' },
            { data: 'nombre_de_colis', render: d => d ? d.toLocaleString('fr-FR') : '-' },
            { data: 'montant_total', render: d => d ? d.toLocaleString('fr-FR') + ' FCFA' : '-' },
            { data: 'montant_paye', render: d => d ? d.toLocaleString('fr-FR') + ' FCFA' : '-' },
            { data: 'reste_a_payer', render: d => d ? d.toLocaleString('fr-FR') + ' FCFA' : '-' },
            { data: null, render: (d, t, r) => (r.expediteur_nom || '') + ' ' + (r.expediteur_prenom || '') },
            { data: 'expediteur_tel' },
            { data: null, render: (d, t, r) => (r.destinataire_nom || '') + ' ' + (r.destinataire_prenom || '') },
            { data: 'destinataire_tel' },
            { data: 'etat' },
            { data: 'created_at' },
            { data: 'action', orderable: false, searchable: false }
        ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdfHtml5',
                text: '<i class="fa fa-file-pdf"></i> PDF',
                title: '', // 🔥 empêche "Exported data" d'apparaître
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: { columns: ':visible:not(:last-child)' },
                customize: function (doc) {
                    doc.pageMargins = [30, 50, 30, 40];
                    doc.defaultStyle.fontSize = 9;
                    doc.styles.tableHeader.fontSize = 10;
                    doc.styles.tableHeader.alignment = 'center';
                    doc.styles.tableBodyEven.alignment = 'center';
                    doc.styles.tableBodyOdd.alignment = 'center';

                    // Logo + Titre
                    doc.content.splice(0, 0, {
                        alignment: 'center',
                        image: logoBase64,
                        width: 100,
                        margin: [0, 0, 0, 10]
                    });
                    doc.content.splice(1, 0, {
                        text: 'MANIFESTE DES COLIS À ARRIVER',
                        fontSize: 16,
                        bold: true,
                        alignment: 'center',
                        margin: [0, 10, 0, 20]
                    });

                    // Espacement et bordures
                    doc.content[2].layout = {
                        hLineWidth: () => 0.5,
                        vLineWidth: () => 0.5,
                        hLineColor: () => '#aaa',
                        vLineColor: () => '#aaa',
                        paddingLeft: () => 6,
                        paddingRight: () => 6,
                        paddingTop: () => 4,
                        paddingBottom: () => 4
                    };

                    // Pied de page
                    doc.footer = (page, pages) => ({
                        columns: [
                            { text: 'Date : ' + new Date().toLocaleDateString('fr-FR'), alignment: 'left', margin: [40, 0, 0, 0] },
                            { text: 'Page ' + page + ' / ' + pages, alignment: 'right', margin: [0, 0, 40, 0] }
                        ],
                        fontSize: 9
                    });
                }
            },
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel"></i> Excel',
                title: 'MANIFESTE_COLIS',
                exportOptions: { columns: ':visible:not(:last-child)' }
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Imprimer',
                title: '', // 🔥 empêche "Exported data" à l’impression
                exportOptions: { columns: ':visible:not(:last-child)' },
                customize: function (win) {
                    var logoUrl = "{{ url('images/LOGOAFT.png') }}";
                    $(win.document.body).prepend(`
                        <div style="text-align:center; margin-bottom:20px;">
                            <img src="${logoUrl}" style="width:100px; margin-bottom:10px;">
                            <h3 style="margin:0;">MANIFESTE DES COLIS À ARRIVER</h3>
                            <hr style="border:1px solid #000;">
                        </div>
                    `);
                    $(win.document.body).find('table').css({
                        'font-size': '11px',
                        'border-collapse': 'collapse',
                        'width': '100%'
                    });
                    $(win.document.body).find('th, td').css({
                        'border': '1px solid #000',
                        'padding': '6px 8px',
                        'text-align': 'center'
                    });
                }
            }
        ],
        order: [[1, 'desc']]
    });
});
</script>

</script>
@endsection
