@extends('admin.layouts.admin')

@section('content-header')
@endsection

@section('content')
<section class="py-3">
        <form action="" method="POST" class="mt-4">
            @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                            <h4 class="text-left mt-4">Liste des colis en attente</h4><br>
                            <div id="products-container">
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
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </form>
    </div>
</section>
    <!-- Script JavaScript -->
    <script>
$(document).ready(function () {
    const table = $("#productTable").DataTable({
        responsive: true, 
        language: {
            url: "{{ asset('js/fr-FR.json') }}"
        },
        ajax: '{{ route("colis.get.devis.colis") }}',
        columns: [
            { data: 'reference_colis' },
            { data: 'nombre_de_colis' },
            { 
                data: null,
                render: function (data, type, row) {
                    return row.expediteur_nom + ' ' + row.expediteur_prenom;
                }
            },
            { data: 'expediteur_tel' },
            { data: 'expediteur_agence' },
            { 
                data: null,
                render: function (data, type, row) {
                    return row.destinataire_nom + ' ' + row.destinataire_prenom;
                }
            },
            { data: 'destinataire_tel' },
            { data: 'destinataire_agence' },
            { data: 'etat' },
            { 
                data: 'created_at',
                render: function(data) {
                    if (data) {
                        var date = new Date(data);
                        var day = ('0' + date.getDate()).slice(-2);
                        var month = ('0' + (date.getMonth() + 1)).slice(-2);
                        var year = date.getFullYear();
                        return day + '/' + month + '/' + year;
                    }
                    return '';
                }
            },
            { data: 'action', orderable: false, searchable: false }
        ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Exporter en Excel',
                title: 'Liste des devis en attente'
            },
            {
                extend: 'pdfHtml5',
                text: 'Exporter en PDF',
                title: 'Liste des devis en attente',
                orientation: 'landscape',
                pageSize: 'A4'
            },
            {
                extend: 'print',
                text: 'Imprimer',
                title: 'Liste des devis en attente',
                customize: function (win) {
                    var logoUrl = "{{ asset('images/LOGOAFT.png') }}";
                    var logo = '<img src="' + logoUrl + '" alt="Logo" style="width:100px;">';
                    $(win.document.body).prepend(logo);
                }
            }
        ]
    });
});

    </script>
    
    
</section>
<style>
    body {
        background-color: #f7f7f7;
    }

    .form-container {
        max-width: 97%;
        margin:auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .form-section {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    section {
        margin-top: 0px;
    }
</style>
@endsection
