@extends('IPMS_SIMEXCI.layouts.agent')
@section('content-header')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<section class="py-3">
    <form class="mt-4">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                    <h4 class="text-left mt-4">Liste des colis Validés</h4><br>
                    <div class="row mb-3">
                        <div class="col-md-12 text-end">
                            <a href="{{ route('ipms_colis.downloadColisSuiviPdf') }}" class="btn btn-success">
                                <i class="fas fa-download me-2"></i>Télécharger PDF
                            </a>
                        </div>
                    </div>
                    <div id="products-container">
                        <div class="table-responsive">
                            <table id="productTable" class="table table-bordered table-striped display">
                                <thead>
                                    <tr>
                                        <th >Paiement</th>
                                        <th >Référence</th>
                                        <th >Produit</th>
                                        <th >Nb.colis</th>
                                        <th >Montant Total</th>
                                        <th >Montant Payé</th>
                                        <th >Reste</th>
                                        <th >Expéditeur</th>
                                        <th >Destinataire</th>
                                        <th >Statut</th>
                                        <th >Date</th>
                                        <th >Actions</th>
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

    <!-- JavaScript for DataTable and Export -->
    <script>
    $(document).ready(function () {
        var table = $("#productTable").DataTable({
            responsive: true,
            language: {
                url: "{{ asset('js/fr-FR.json') }}"
            },
            ajax: '{{ route("ipms_colis.get.colis.suivi") }}',
            columns: [
                { data: 'statut_paiement' },
                { data: 'reference_colis' },
                { data: 'nom_produit' },
                { data: 'nombre_de_colis' },
                { 
                    data: 'montant_total',
                    render: function(data, type, row) {
                        var devise = row.devise || 'FCFA';
                        return data ? parseFloat(data).toLocaleString('fr-FR', {minimumFractionDigits: 0}) + ' ' + devise : '0 ' + devise;
                    }
                },
                { 
                    data: 'montant_paye',
                    render: function(data, type, row) {
                        var devise = row.devise || 'FCFA';
                        return data ? parseFloat(data).toLocaleString('fr-FR', {minimumFractionDigits: 0}) + ' ' + devise : '0 ' + devise;
                    }
                },
                { 
                    data: 'reste_a_payer',
                    render: function(data, type, row) {
                        var devise = row.devise || 'FCFA';
                        var color = data > 0 ? 'red' : 'green';
                        return '<span style="color: ' + color + '; font-weight: bold;">' +
                            (data ? parseFloat(data).toLocaleString('fr-FR', {minimumFractionDigits: 0}) + ' ' + devise : '0 ' + devise) +
                            '</span>';
                    }
                },

                {
                    data: null,
                    render: function (data, type, row) {
                        return (row.expediteur_nom || '') + ' ' + (row.expediteur_prenom || '')+ ' ' + (row.expediteur_tel || '');
                    }
                },
                // { data: 'expediteur_tel' },
                {
                    data: null,
                    render: function (data, type, row) {
                        return (row.destinataire_nom || '') + ' ' + (row.destinataire_prenom || '')+ ' ' + (row.destinataire_tel || '');
                    }
                },
                // { data: 'destinataire_tel' },
                // { data: 'destinataire_agence' },
                { 
                    data: 'etat',
                    render: function(data, type, row) {
                        var badgeClass = 'badge bg-secondary';
                        if (data === 'Validé') badgeClass = 'badge bg-success';
                        else if (data === 'En attente') badgeClass = 'badge bg-warning';
                        else if (data === 'Rejeté') badgeClass = 'badge bg-danger';
                        
                        return '<span class="' + badgeClass + '">' + (data || 'N/A') + '</span>';
                    }
                },
                { 
                    data: 'created_at',
                    render: function(data, type, row) {
                        if (data) {
                            var date = new Date(data);
                            var day = ('0' + date.getDate()).slice(-2);  
                            var month = ('0' + (date.getMonth() + 1)).slice(-2);  
                            var year = date.getFullYear().toString().slice(-2);  
                            return day + '/' + month + '/' + year;
                        }
                        return data;
                    }
                },
                { data: 'action' }
            ],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Exporter en Excel',
                    title: 'Liste des Colis Validés',
                    className: 'btn btn-success',
                    customize: function (xlsx) {
                        console.log("Exportation Excel réussie");
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: 'Exporter en PDF',
                    title: 'Liste des Colis Validés',
                    className: 'btn btn-danger',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    customize: function (doc) {
                        var logoUrl = "{{ url('images/LOGOAFT.png') }}";
                        toDataURL(logoUrl, function (dataUrl) {
                            doc.content.unshift({
                                image: dataUrl,
                                width: 100,
                                alignment: 'center',
                                margin: [0, 0, 0, 10]
                            });
                        });
                    }
                },
                {
                    extend: 'print',
                    text: 'Imprimer',
                    title: 'Liste des Colis Validés',
                    className: 'btn btn-info',
                    customize: function (win) {
                        var logoUrl = "{{ url('images/LOGOAFT.png') }}";
                        var logo = '<img src="' + logoUrl + '" alt="Logo" style="position:relative; top:10px; left:20px; width:100px; height:auto;">';
                        $(win.document.body).find('h1')
                            .css('text-align', 'center')
                            .css('margin-top', '10px');
                        $(win.document.body).find('h1').after(logo);
                        $(win.document.body).find('table').css('margin-top', '30px');
                    }
                }
            ]
        });

        /**
         * Fonction pour convertir une image en Base64
         */
        function toDataURL(url, callback) {
            var xhr = new XMLHttpRequest();
            xhr.onload = function () {
                var reader = new FileReader();
                reader.onloadend = function () {
                    callback(reader.result);
                };
                reader.readAsDataURL(xhr.response);
            };
            xhr.open('GET', url);
            xhr.responseType = 'blob';
            xhr.send();
        }

        // Gestion des clics sur les boutons de paiement
        $(document).on('click', '.pay-btn', function (event) {
            event.preventDefault();
            var reference = $(this).data('reference');
            var total = $(this).data('total');
            var paid = $(this).data('paid');
            var rest = total - paid;
            
            Swal.fire({
                title: 'Paiement pour ' + reference,
                html: 'Montant total: <strong>' + total.toLocaleString('fr-FR') + ' FCFA</strong><br>' +
                      'Déjà payé: <strong>' + paid.toLocaleString('fr-FR') + ' FCFA</strong><br>' +
                      'Reste à payer: <strong style="color: red;">' + rest.toLocaleString('fr-FR') + ' FCFA</strong>',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Procéder au paiement',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Ici vous pouvez ajouter la logique pour le paiement
                    console.log('Paiement pour:', reference);
                }
            });
        });

        // Rafraîchissement automatique toutes les 10 secondes
        setInterval(function() {
            table.ajax.reload(null, false);
        }, 10000);
    });
    </script>
</section>

<style>
    .btn {
        width: auto;
        height: 40px;
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