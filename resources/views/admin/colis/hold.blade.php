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
                                                <th>Agence d'expédition</th>
                                                <th>Destinataire</th>
                                                <th>Téléphone</th>
                                                <th>Agence de destination</th>
                                                <th>Status</th>
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

    <!-- JavaScript for DataTable and Export -->
    <script>
$(document).ready(function () {
    var table = $("#productTable").DataTable({
        responsive: true,
        language: {
                url: "{{ asset('js/fr-FR.json') }}" // Chemin local vers le fichier
            },
        ajax: '{{ route("colis.get.colis.hold") }}', // Récupération des données via AJAX
        columns: [
            { data: 'reference_colis' },
            { data: 'nombre_de_colis' },

            {
                data: null,
                render: function (data, type, row) {
                    console.log(data);
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
            { data: 'destinataire_agence' },
            { data: 'destinataire_tel' },
            { data: 'etat' },
            {
                data: 'created_at',
                render: function (data) {
                    if (!data) {
                        return ''; // Retourne une chaîne vide si la date est null
                    }
                    var date = new Date(data);
                    if (isNaN(date.getTime())) {
                        return ''; // Vérifie si la date est invalide
                    }
                    var day = ('0' + date.getDate()).slice(-2);
                    var month = ('0' + (date.getMonth() + 1)).slice(-2);
                    var year = date.getFullYear();
                    return day + '/' + month + '/' + year;
                }
            }

            { data: 'action', orderable: false, searchable: false }
        ],
        dom: 'Bfrtip', // Placement des boutons
        buttons: [
            // Bouton Excel
            {
                extend: 'excelHtml5',
                text: 'Exporter en Excel',
                title: 'Liste des Colis en attente',
                customize: function (xlsx) {
                    console.log("Exportation Excel réussie sans image.");
                }
            },
            // Bouton PDF
            {
                extend: 'pdfHtml5',
                text: 'Exporter en PDF',
                title: 'Liste des Colis en attente',
                orientation: 'landscape', // Mode paysage
                pageSize: 'A4', // Taille de la page
                customize: function (doc) {
                    // Ajout du logo encodé en Base64 dans le PDF
                    var logoUrl = "{{ url('images/LOGOAFT.png') }}";
                    toDataURL(logoUrl, function (dataUrl) {
                        // Ajout de l'image au début du contenu PDF
                        console.log(dataUrl);
                        doc.content.unshift({
                            image: dataUrl,
                            width: 100, // Taille du logo
                            alignment: 'center',
                            margin: [0, 0, 0, 10] // Espacement
                        });
                    });
                }
            },
            // Bouton Imprimer
            {
                extend: 'print',
                text: 'Imprimer',
                title: 'Liste des Colis en attente',
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
     * @param {string} url - L'URL de l'image
     * @param {function} callback - Fonction de retour contenant l'image en Base64
     */
    function toDataURL(url, callback) {
        var xhr = new XMLHttpRequest();
        xhr.onload = function () {
            var reader = new FileReader();
            reader.onloadend = function () {
                callback(reader.result); // Retourne l'image encodée en Base64
            };
            reader.readAsDataURL(xhr.response);
        };
        xhr.open('GET', url);
        xhr.responseType = 'blob'; // Type de réponse : Blob
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
