@extends('AFT_LOUIS_BLERIOT.layouts.agent')
@section('content-header')
@endsection

@section('content')
<section class="py-3">
    <form action="" method="POST" class="mt-4">
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                    <h4 class="mb-4">Liste des colis en attente</h4>
                    <div class="table-responsive">
                        <table id="productTable" class="table table-bordered table-striped display nowrap" style="width:100%">
                            <thead class="table-dark">
                                <tr>
                                    <th>Référence</th>
                                    <th>Nombre de colis</th>
                                    <th>Expéditeur</th>
                                    <th>Téléphone</th>
                                    <th>Agence Expéditeur</th>
                                    <th>Destinataire</th>
                                    <th>Agence Destinataire</th>
                                    <th>Téléphone</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
    $(document).ready(function () {
        var table = $("#productTable").DataTable({
            responsive: true,
            scrollX: true, // permet le défilement horizontal si le tableau est large
            language: { url: "{{ asset('js/fr-FR.json') }}" },
            ajax: '{{ route("aftlb_colis.get.colis.hold") }}',
            columns: [
                { data: 'reference_colis' },
                { data: 'nombre_de_colis' },
                {
                    data: null,
                    render: function (data) {
                        return data.expediteur_nom + ' ' + data.expediteur_prenom;
                    }
                },
                { data: 'expediteur_tel' },
                { data: 'expediteur_agence' },
                {
                    data: null,
                    render: function (data) {
                        return data.destinataire_nom + ' ' + data.destinataire_prenom;
                    }
                },
                {
                    data: 'destinataire_agence',
                    render: function(data) {
                        if(data === 'IPMS-SIMEX-CI Angre 8ème Tranche') return 'DS Translog Angré 8ème Tranche';
                        if(data === 'IPMS-SIMEX-CI') return 'DS Translog Carrefour Angré';
                        return data;
                    }
                },
                { data: 'destinataire_tel' },
                { data: 'etat' },
                { data: 'created_at' }
            ],
            dom: 'Bfrtip',
            buttons: [
                { extend: 'excelHtml5', text: 'Exporter en Excel', title: 'Manifeste' },
                { extend: 'print', text: 'Imprimer', title: 'Manifeste' }
            ]
        });
    });
    </script>
</section>

<style>
    .btn { width: 15%; height: 40px; font-size: 18px; }
    .dataTables_wrapper { width: 100% !important; margin: 0 auto; }
    .dt-button {
        padding: 10px 20px;
        margin: 5px;
        border-radius: 5px;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
        text-transform: uppercase;
        transition: all 0.3s ease;
    }
    .table th, .table td {
        white-space: nowrap; /* empêche les retours à la ligne intempestifs */
        vertical-align: middle;
    }
</style>
@endsection

