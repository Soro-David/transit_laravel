@extends('AFT_LOUIS_BLERIOT.layouts.agent')
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
                                                <th>Expéditeur</th>
                                                <th>Contact</th>
                                                {{-- <th>Agence Expéditeur</th> --}}
                                                <th>Nom Destinataire</th>
                                                <th>Contact</th>
                                                <th>Agence Destination</th>
                                                <th>Etat du Colis</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="container">
                                    <h6 class="text-right mt-4">Prix total : <span id="prix-total">0</span> FCFA</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="border p-4 rounded shadow-sm">
                                            <div class="mb-3">
                                                <label for="commentaire1" class="form-label">Commentaire</label>
                                                <textarea name="commentaire1" id="commentaire1" rows="5" class="form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-6">
                                            <label for="reference_contenaire" class="form-label">Référence Contenaire</label>
                                            <input type="text" name="reference_contenaire" id="reference_contenaire"  placeholder="Référence du contenaire" class="form-control" readonly>
                                        </div>
                                    </div>
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
            var table = $("#productTable").DataTable({
                responsive: true, // Rend le tableau réactif
                language: {
                    url: "{{ asset('js/fr-FR.json') }}" // Chemin local vers le fichier
                },
                ajax: '{{ route("aftlb_colis.get.devis.colis") }}', // URL pour récupérer les données
                columns: [
                    { data: 'reference_colis' },
                    {
                        data: null,
                        render: function (data, type, row) {
                            console.log(data);
                            return row.expediteur_nom + ' ' + row.expediteur_prenom;
                        }
                    },
                    { data: 'expediteur_tel' },
                    // { data: 'expediteur_agence' },
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
                dom: 'Bfrtip', // Active les boutons et positionne les contrôles
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
                            var logo = '<img src="' + logoUrl + '" alt="Logo" style="position:relative; top:10px; left:20px; width:100px; height:auto;">';
                            $(win.document.body).find('h1').css({
                                textAlign: 'center',
                                border: '2px solid #000',
                                padding: '1px',
                                marginTop: '10px'
                            });
                            $(win.document.body).find('h1').after(logo);
                            $(win.document.body).find('table').css('margin-top', '30px');
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
