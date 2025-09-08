@extends('AFT_LOUIS_BLERIOT.layouts.agent')
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
                            <h4 class="text-left mt-4">Colis déchargés</h4><br>
                            <div id="products-container">
                                <div class="text-right">
                                    <button type="button" style="color: #fff;" class="btn gradient-orange-blue" data-bs-toggle="modal" data-bs-target="#scanner_entrepot">
                                        Scanner pour décharger
                                    </button>
                                </div><br>
                                 <div class="table-responsive">
                                            <table id="productTable" class="table table-bordered table-striped display">
                                                <thead>
                                                    <tr>
                                                        <th>Reference</th>
                                                        <th>Nombre de colis</th>
                                                        <th>Expéditeur</th>
                                                        <th>Téléphone</th>
                                                        {{-- <th>Agence Expéditeur</th> --}}
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
        // Extraction de la référence et de l'identifiant à l'aide d'expressions régulières
        const referenceMatch = decodedText.match(/Ref:\s*(\S+)/i);
        const idMatch = decodedText.match(/ID:\s*(\S+)/i);

        // Vérifier que les deux valeurs ont bien été extraites
        if (!referenceMatch || !idMatch) {
            console.error("Impossible d'extraire la référence ou l'identifiant.");
            resultElement.innerText = "Erreur : données QR code invalides.";
            return;
        }

        // Extraction des valeurs capturées
        const referenceColis = referenceMatch[1];
        const identifiant = idMatch[1];

        console.log(`Code détecté : ${decodedText}`);
        console.log(`Référence : ${referenceColis}`);
        console.log(`Identifiant : ${identifiant}`);
        resultElement.innerText = `Résultat : ${decodedText}`;

        // Envoi des données extraites via une requête AJAX pour mettre à jour l'état du colis
        $.ajax({
            url: "{{ route('aftlb_scan.update.colis.decharge') }}",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
            data: {
                colisId: referenceColis, // Envoie la référence extraite
                id: identifiant,          // Envoie l'identifiant extrait
            },
            success: function (response) {
                console.log("Réponse du serveur :", response);
                // Affichage des messages retournés par le serveur
                if (response.messages && Array.isArray(response.messages)) {
                    resultElement.innerText = response.messages.join("\n");
                } else {
                    resultElement.innerText = "Réponse inconnue du serveur.";
                }
            },
            error: function (error) {
                console.error("Erreur lors du dechargement :", error);
                if (error.responseJSON && error.responseJSON.messages) {
                    resultElement.innerText = error.responseJSON.messages.join("\n");
                } else {
                    resultElement.innerText = "Ce colis n'est indisponible dans cette agence ou a déjà été déchargé.";
                }
            },
        });

        // Arrêt du scanner et mise à jour de l'affichage
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
        // Affiche l'élément du lecteur
        readerElement.style.display = "block";

        // Vérifie que l'élément #reader a des dimensions valides
        if (!readerElement || readerElement.offsetWidth === 0 || readerElement.offsetHeight === 0) {
            console.error("Erreur : L'élément #reader n'a pas de dimensions valides.");
            return;
        }

        // Démarrage du scanner avec les options définies
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

    // Démarrer le scanner dès que la modale est affichée
    modal.addEventListener("shown.bs.modal", function () {
        setTimeout(startScanner, 500);
    });

    // Arrêter le scanner lorsque la modale est fermée
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

    // Bouton de redémarrage du scanner
    restartButton.addEventListener("click", startScanner);
});


    $(document).ready(function () {
        // Initialisation de la table DataTable
        var table = $("#productTable").DataTable({
            responsive: true,
            language: {
                    url: "{{ asset('js/fr-FR.json') }}" // Chemin local vers le fichier
                },
            ajax: '{{ route("aftlb_scan.get.colis.decharge") }}', // Récupération des données via AJAX
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
            { 
                data: 'destinataire_agence',
                name: 'destinataire_agence.nom_agence',
                render: function(data, type, row) {
                    if (data === 'IPMS-SIMEX-CI Angre 8ème Tranche') {
                        return 'DS Translog Angré 8ème Tranche';
                    } else if (data === 'IPMS-SIMEX-CI') {
                        return 'DS Translog Carrefour Angré';
                    }
                    return data;
                }
            },
            { data: 'destinataire_tel' },
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
        max-width: 200px; /* Largeur maximale sur les grands écrans */
        font-size: 16px;
        height: 40px;

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
