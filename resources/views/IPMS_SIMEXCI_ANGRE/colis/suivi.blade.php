@extends('IPMS_SIMEXCI_ANGRE.layouts.agent')
@section('content-header')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<section class="py-3">
        <form  class="mt-4">
            @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                            <h4 class="text-left mt-4">Liste des colis Validés</h4><br>
                            <div class="row mb-3">
                                <div class="col-md-12 text-end">
                                    <a href="{{ route('ipms_angre_colis.download.pdf') }}" class="btn btn-success">
                                        <i class="fas fa-download me-2"></i>Télécharger PDF
                                    </a>
                                </div>
                            </div>
                            <div id="products-container">
                                <div class="table-responsive">
                                    <table id="productTable" class="table table-bordered table-striped display">
                                        <thead>
                                            <tr>
                                                <th>Référence</th>
                                                <th>Nombre de colis</th>
                                                <th>Expéditeur</th>
                                                <th>Téléphone</th>
                                                <th>Destinataire</th>
                                                <th>Téléphone</th>
                                                <th>Agence Destinataire</th>
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
        ajax: '{{ route("ipms_angre_colis.get.colis.suivi")}}', // Récupération des données via AJAX
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
            {
                data: null,
                render: function (data, type, row) {
                    return row.destinataire_nom + ' ' + row.destinataire_prenom;
                }
            },
            { data: 'destinataire_agence' },
            { data: 'destinataire_tel' },
            { data: 'etat' },
            { data: 'created_at',
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
            // { data: 'action' },
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

$(document).on('click', '.delete-btn', function (event) {
    event.preventDefault(); // Empêche le comportement par défaut du bouton
    const url = $(this).data('url'); // Récupère l'URL de suppression
    Swal.fire({
        title: 'Confirmer la suppression',
        text: "Êtes-vous sûr de vouloir supprimer ce colis ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: 'DELETE', // Assurez-vous que la méthode est DELETE
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                success: function(response) {
                    Swal.fire('Supprimé!', response.success, 'success').then(() => {
                        location.reload(); // Recharger la page après la suppression
                    });
                },
                error: function(xhr) {
                    Swal.fire('Erreur!', xhr.responseJSON.error, 'error');
                }
            });
        }
    });
});
    </script>
    
    
</section>

<style>
    .btn {
        width: auto; /* Ajuste la largeur au contenu */
        height: 40px;
        font-size: 16px; /* Ajuste la taille de la police */
        padding: 0 15px; /* Ajoute du rembourrage */
        border-radius: 5px; /* Coins arrondis */
        transition: background-color 0.3s, transform 0.2s; /* Transition pour les effets */
    }

    .btn-warning {
        background-color: #ffc107; /* Couleur de fond pour le bouton modifier */
        color: white; /* Couleur du texte */
    }

    .btn-warning:hover {
        background-color: #e0a800; /* Couleur de fond au survol */
        transform: scale(1.05); /* Légère augmentation de la taille au survol */
    }

    .btn-info {
        background-color: #17a2b8; /* Couleur de fond pour le bouton imprimer */
        color: white; /* Couleur du texte */
    }

    .btn-info:hover {
        background-color: #138496; /* Couleur de fond au survol */
        transform: scale(1.05); /* Légère augmentation de la taille au survol */
    }

    .btn-danger {
        background-color: #dc3545; /* Couleur de fond pour le bouton supprimer */
        color: white; /* Couleur du texte */
    }

    .btn-danger:hover {
        background-color: #c82333; /* Couleur de fond au survol */
        transform: scale(1.05); /* Légère augmentation de la taille au survol */
    }

    .btn-group {
        display: flex; /* Aligne les boutons horizontalement */
        gap: 5px; /* Espace entre les boutons */
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
