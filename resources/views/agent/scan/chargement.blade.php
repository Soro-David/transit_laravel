@extends('agent.layouts.agent')
@section('content-header')

{{-- <script src="'public/js/Html5-qrcode.js'"></script> --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
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
                                        Scanner
                                    </button>
                                </div><br>
                                <div class="table-responsive">
                                    <table id="productTable" class="table table-bordered table-striped display">
                                        <thead>
                                            <tr>
                                                <th>Reference colis</th>
                                                <th>Nom Expéditeur</th>
                                                <th>Contact Expéditeur</th>
                                                <th>Agence Expéditeur</th>
                                                <th>Nom Destinataire</th>
                                                <th>Contact Destinataire</th>
                                                <th>Agence Destinataire</th>
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

    <!-- Modal for editing -->
    <div class="modal fade" id="scanner_entrepot" tabindex="-1" aria-labelledby="scannerEntrepotLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 600px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Scanner les colis pour le déchargement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="reader" ></div>
                    <p id="result">Résultat : Aucun</p>
                    <div class="d-flex justify-content-center">
                        <button id="restartScan" class="btn btn-primary mt-3" style="display: none;">Relancer le scan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    <!-- JavaScript for DataTable and Export -->
        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

document.addEventListener("DOMContentLoaded", function () {
    const html5QrCode = new Html5Qrcode("reader");
    const resultElement = document.getElementById("result");
    const restartButton = document.getElementById("restartScan");
    const readerElement = document.getElementById("reader");
    const modal = document.getElementById("scanner_entrepot");

    const onScanSuccess = (decodedText) => {
    const reference_colis = decodedText.match(/Référence colis:\s*(\S+)/);  // Expression régulière pour extraire la référence
    console.log(`Code détecté : ${decodedText}`);
    console.log(`Référence : ${reference_colis[1]}`);  // Affiche la référence extraite

    resultElement.innerText = `Résultat : ${decodedText}`;

    // Requête AJAX pour mettre à jour l'état du colis
    $.ajax({
    url: "{{ route('agent_scan.update.colis.charge') }}",
    type: "POST",
    headers: {
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
    },
    data: {
        colisId: reference_colis[1], // Envoie la référence extraite
    },
    success: function (response) {
        console.log("Réponse du serveur :", response);

        if (response.success) {
            // Succès : le colis a été mis à jour
            resultElement.innerText = `Colis ${reference_colis[1]} à été déchargé avec succès`;
        } else {
            // Affichage du message d'erreur retourné par le serveur
            if (response.message === "Le colis est déjà déchargé") {
                resultElement.innerText = `Erreur : ${response.message}`;
            } else {
                resultElement.innerText = `Erreur : ${response.message}`;
            }
        }
    },
    error: function (error) {
        console.error("Erreur lors du dechargement :", error);

        // Vérification si l'erreur contient une réponse JSON
        if (error.responseJSON && error.responseJSON.message) {
            resultElement.innerText = `Erreur : ${error.responseJSON.message}`;
        } else {
            resultElement.innerText = "Erreur de dechargement du colis.";
        }
    },
});


    // Arrête le scanner et masque l'élément caméra
    html5QrCode
        .stop()
        .then(() => {
            readerElement.style.display = "none";
            restartButton.style.display = "block";
        })
        .catch((err) => {
            console.error(`Erreur lors de l'arrêt du scanner : ${err}`);
        });
};

    const startScanner = () => {
        readerElement.style.display = "block";

        if (!readerElement || readerElement.offsetWidth === 0 || readerElement.offsetHeight === 0) {
            console.error("Erreur : L'élément #reader n'a pas de dimensions valides.");
            return;
        }

        html5QrCode
            .start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                onScanSuccess
            )
            .then(() => {
                resultElement.innerText = "Résultat : En attente...";
                restartButton.style.display = "none";
            })
            .catch((err) => {
                console.error(`Impossible de démarrer le scanner : ${err}`);
            });
    };

    modal.addEventListener("shown.bs.modal", function () {
        setTimeout(startScanner, 500);
    });

    modal.addEventListener("hidden.bs.modal", function () {
        html5QrCode
            .stop()
            .then(() => {
                console.log("Scanner arrêté avec succès.");
            })
            .catch((err) => {
                console.error(`Erreur lors de l'arrêt du scanner : ${err}`);
            });
    });

    restartButton.addEventListener("click", startScanner);
});



    $(document).ready(function () {
        // Initialisation de la table DataTable
        var table = $("#productTable").DataTable({
            responsive: true,
            language: {
                    url: "{{ asset('js/fr-FR.json') }}" // Chemin local vers le fichier
                },
            ajax: '{{ route("agent_scan.get.colis.charge") }}', // Récupération des données via AJAX
            columns: [
                { data: 'reference_colis' },
                {
                    data: null,
                    render: function (data, type, row) {
                        return row.nom_expediteur + ' ' + row.prenom_expediteur;
                    }
                },
                { data: 'tel_expediteur' },
                { data: 'agence_expedition' },
                {
                    data: null,
                    render: function (data, type, row) {
                        return row.nom_destinataire + ' ' + row.prenom_destinataire;
                    }
                },
                { data: 'tel_destinataire' },
                { data: 'agence_destination' },
                { data: 'created_at',
                    render: function(data, type, row) {
                        // Vérifiez si la date existe et la formater
                        if (data) {
                            var date = new Date(data);
                            // Retourne la date au format aa/mm/jj
                            var day = ('0' + date.getDate()).slice(-2);  // Ajoute un zéro si jour < 10
                            var month = ('0' + (date.getMonth() + 1)).slice(-2);  // +1 car les mois commencent à 0
                            var year = date.getFullYear().toString().slice(-2);  // On garde les deux derniers chiffres de l'année
                            return day + '/' + month + '/' + year;
                        }
                        return data;  // Si la date est vide, on retourne la donnée brute
                    }
                },
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

    });

</script>
    
    {{-- <script src="'public/js/Html5-qrcode.js'"></script> --}}
    
</section>

<style>
    
     #reader {
      width: 100%;
      height: 400px;
      border: 1px solid #c2bdbd; 
    }


    .btn {
        width: 100%; /* Les boutons s'adaptent à la largeur du conteneur */
        max-width: 150px; /* Largeur maximale sur les grands écrans */
        font-size: 16px;
    }

    .dataTable-wrapper {
        width: 100%; /* Prend toute la largeur disponible */
        max-width: 1000px; /* Largeur maximale pour les grands écrans */
        margin: 0 auto; /* Centrer le tableau */
        padding: 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background: #f9f9f9;
    }

    #camera, #snapshot {
        max-width: 100%; /* Rendre la caméra et l'image capturée responsive */
        height: auto;
        margin: 10px auto;
    }

    .modal-dialog {
        max-width: 90%; /* Rendre les modals adaptatifs */
        margin: auto;
    }

    .dt-button {
        padding: 10px 10px;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        h4 {
            font-size: 18px; /* Réduction de la taille des titres */
        }

        .btn {
            font-size: 14px;
            padding: 10px;
        }

        .table-responsive {
            overflow-x: auto; /* Assurer un défilement horizontal sur les petits écrans */
        }
    }
    #camera {
            width: 100%;
            max-height: 300px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        .image-preview {
            margin-top: 15px;
            width: 100%;
            max-height: 300px;
            border: 2px dashed #ffa500;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f9f9f9;
            position: relative;
        }

        .image-preview canvas {
            max-width: 100%;
            max-height: 100%;
        }

        .image-placeholder {
            color: #ccc;
            font-size: 18px;
            position: absolute;
            text-align: center;
        }

</style>
@endsection
