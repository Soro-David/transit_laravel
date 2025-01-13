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
                            <h4 class="text-left mt-4">Liste des clients</h4><br>
                            <div id="products-container">
                                <div class="table-responsive">
                                    <table id="productTable" class="table table-bordered table-striped display">
                                        <thead>
                                            <tr>
                                                <th>Nom </th>
                                                <th>E-mail</th>
                                                <th>Date de Création</th>
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
    <!-- JavaScript for DataTable and Export -->
    <script>
        $(document).ready(function () {
            var table = $("#productTable").DataTable({
                responsive: true,
                language: {
                    url: "{{ asset('js/fr-FR.json') }}" // Chemin vers le fichier de traduction
                },
                ajax: '{{ route("client.get.client") }}', // Route pour récupérer les données
                columns: [
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `${row.first_name} ${row.last_name}`;
                        }
                    },
                    { data: 'email' },
                    {
                        data: 'created_at',
                        render: function (data) {
                            if (data) {
                                const date = new Date(data);
                                const day = ('0' + date.getDate()).slice(-2);
                                const month = ('0' + (date.getMonth() + 1)).slice(-2);
                                const year = date.getFullYear().toString().slice(-2);
                                return `${day}/${month}/${year}`;
                            }
                            return '-';
                        }
                    },
                    { data: 'action', orderable: false, searchable: false }
                ],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Exporter en Excel',
                        title: 'Liste des Colis en attente',
                        customize: function (xlsx) {
                            console.log("Exportation Excel effectuée.");
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'Exporter en PDF',
                        title: 'Liste des Colis en attente',
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
                        title: 'Liste des Colis en attente',
                        customize: function (win) {
                            var logoUrl = "{{ url('images/LOGOAFT.png') }}";
                            var logo = `<img src="${logoUrl}" alt="Logo" style="position:relative; top:10px; left:20px; width:100px; height:auto;">`;
                            $(win.document.body).find('h1').css('text-align', 'center').css('margin-top', '10px');
                            $(win.document.body).find('h1').after(logo);
                            $(win.document.body).find('table').css('margin-top', '30px');
                        }
                    }
                ]
            });
    
            /**
             * Fonction pour convertir une image en Base64
             * @param {string} url - URL de l'image
             * @param {function} callback - Fonction callback
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
        });
    </script>
    
</section>

<style>
    .btn {
        width: 15%;
        height: 40px;
        font-size: 18px;
    }

    .dataTable-wrapper {
        width: 80% !important;
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
</style>
@endsection
