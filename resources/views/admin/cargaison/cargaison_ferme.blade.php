@extends('admin.layouts.admin')
@section('content-header')
@endsection

@section('content')
<section class="py-3">
    <div class="row d-flex justify-content-center">
        <div class="col-md-12">
            <div class="card border-0 rounded shadow-sm">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="card-title mb-0 fw-bold">Informations du Véhicule de Navigation</h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('colis.bateaux.store') }}" method="POST">
                        @csrf
                        <div class="row g-3 align-items-center">
                            <!-- Référence du bateau (rempli automatiquement) -->
                            <div class="col-md-3">
                                <label for="reference_bateau" class="form-label fw-bold">Référence du véhicule:</label>
                                <input type="text" name="reference_bateau" id="reference_bateau" class="form-control" readonly>
                            </div>
    
                            <!-- Sélection de la référence du conteneur -->
                            <div class="col-md-3">
                                <label for="reference_conteneur" class="form-label fw-bold">Référence conteneur/vol:</label>
                                <select id="reference_conteneur" name="reference_conteneur" class="form-select" onchange="generateReference()">
                                    <option value="" disabled selected>-- Sélectionnez la référence --</option>
                                   @foreach ($referenceFermes as $reference)
                                        <option value="{{ $reference }}">{{ $reference }}</option>
                                    @endforeach
                                </select>
                            </div>
    
                            <!-- Type de véhicule -->
                            <div class="col-md-3">
                                <label for="type" class="form-label fw-bold">Véhicule de navigation:</label>
                                <select id="type" name="type" class="form-select" onchange="toggleFields(); generateReference();">
                                    <option value="" disabled selected>Choisir un type</option>
                                    <option value="bateau">BATEAU</option>
                                    <option value="ballon">AVION</option>
                                </select>
                            </div>
    
                            <!-- Date d'arrivée -->
                            <div class="col-md-3">
                                <label for="date_arrive" class="form-label fw-bold">Date d'arrivée:</label>
                                <input type="date" name="date_arrive" id="date_arrive" class="form-control">
                            </div>
    
                            <!-- Compagnie -->
                            <div class="col-md-3">
                                <label for="compagnie" class="form-label fw-bold">Compagnie:</label>
                                <input type="text" name="compagnie" id="compagnie" class="form-control" placeholder="Nom de la compagnie">
                            </div>
    
                            <!-- Champs du bateau -->
                            <div class="col-md-3 bateau-fields" style="display: none;">
                                <label for="numero_bateau" class="form-label fw-bold">Numéro du bateau:</label>
                                <input type="text" name="numero_bateau" id="numero_bateau" class="form-control" placeholder="Ex: B12345">
                            </div>
    
                            <div class="col-md-3 bateau-fields" style="display: none;">
                                <label for="nom_bateau" class="form-label fw-bold">Nom du bateau:</label>
                                <input type="text" name="nom_bateau" id="nom_bateau" class="form-control" placeholder="Ex: Océanic">
                            </div>
    
                            <!-- Champs de l'avion -->
                            <div class="col-md-3 ballon-fields" style="display: none;">
                                <label for="numero_ballon" class="form-label fw-bold">Numéro de vol:</label>
                                <input type="text" name="numero_ballon" id="numero_ballon" class="form-control" placeholder="Ex: AF702">
                            </div>
    
                            <div class="col-md-3 ballon-fields" style="display: none;">
                                <label for="nom_ballon" class="form-label fw-bold">Nom de l'avion:</label>
                                <input type="text" name="nom_ballon" id="nom_ballon" class="form-control" placeholder="Ex: Airbus A380">
                            </div>
    
                            <!-- Agence de destination -->
                            <div class="col-md-3">
                                <label for="agence_destination" class="form-label fw-bold">Agence de destination:</label>
                                <select id="agence_destination" name="agence_destination" class="form-select">
                                    <option value="" disabled selected>-- Sélectionnez l'agence --</option>
                                    @foreach ($agencesDestination as $agence)
                                        <option value="{{ $agence->nom_agence }}">{{ $agence->nom_agence }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="hidden" name="agence_expedition" value="{{ old('agence_expedition', 'AFT Agence Louis Bleriot') }}">
    
                            <!-- Bouton de soumission -->
                            <div class="col-md-12 text-center mt-3">
                                <button type="submit" class="btn btn-success">Créer</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row d-flex justify-content-center mt-4">
        <div class="col-md-12">
            <div class="border p-4 rounded shadow-sm">
                <h4 class="text-left mt-4">Liste des Véhicules de Navigation</h4><br>
                <div class="table-responsive">
                    <table id="productTable" class="table table-bordered table-striped display">
                        <thead>
                            <tr>
                                <th>Référence Véhicule</th>
                                <th>Date de Départ</th>
                                <th>Date d'Arrivée</th>
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

<!-- Script JavaScript -->
<script>
    function generateReference() {
        const type = document.getElementById("type").value;
        const referenceConteneur = document.getElementById("reference_conteneur").value;
        const mois = "{{ $mois }}";
        const annee = "{{ $annee }}";
        const referenceInput = document.getElementById("reference_bateau");
        
        if (referenceConteneur && type) {
            let prefix = type === 'bateau' ? 'BAT' : 'AV';
            referenceInput.value = `${prefix}-${referenceConteneur}-${mois}-${annee}`;
        } else {
            referenceInput.value = "";
        }
    }

    function toggleFields() {
        const type = document.getElementById("type").value;
        const agenceDestinationSelect = document.getElementById("agence_destination");
        
        const bateauFields = document.querySelectorAll(".bateau-fields");
        const ballonFields = document.querySelectorAll(".ballon-fields");

        if (type === "bateau") {
            bateauFields.forEach(field => field.style.display = "block");
            ballonFields.forEach(field => field.style.display = "none");
            
            // Sélectionne automatiquement "IPMS-SIMEX-CI" pour le bateau
            agenceDestinationSelect.value = "IPMS-SIMEX-CI";

        } else if (type === "ballon") {
            bateauFields.forEach(field => field.style.display = "none");
            ballonFields.forEach(field => field.style.display = "block");
            
            // *** CORRECTION APPLIQUÉE ICI ***
            // Sélectionne automatiquement "IPMS-SIMEX-CI Angre 8ème Tranche" pour l'avion
            agenceDestinationSelect.value = "IPMS-SIMEX-CI Angre 8ème Tranche";

        } else {
            bateauFields.forEach(field => field.style.display = "none");
            ballonFields.forEach(field => field.style.display = "none");
            
            // Réinitialise la sélection si aucune option n'est choisie
            agenceDestinationSelect.value = "";
        }
    }
</script>


<!-- JavaScript for DataTable and Export -->
<script>
$(document).ready(function () {
    var table = $("#productTable").DataTable({
        responsive: true,
        language: {
                url: "{{ asset('js/fr-FR.json') }}"
            },
        ajax: '{{ route("colis.get.cargaison.ferme") }}',
        columns: [
            { data: 'reference_bateau', title: "Référence Véhicule" },
            { data: 'date_depart', title: "Date de Départ" },
            { data: 'date_arriver', title: "Date d'Arrivée" },
            { data: 'actions', title: "Actions", orderable: false, searchable: false }
        ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Exporter en Excel',
                title: 'Liste des Véhicules de Navigation',
            },
            {
                extend: 'pdfHtml5',
                text: 'Exporter en PDF',
                title: 'Liste des Véhicules de Navigation',
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
                title: 'Liste des Véhicules de Navigation',
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

<style>
    /* Les styles restent les mêmes, ils sont déjà corrects */
    .btn {
        width: 15%;
        height: 40px;
        font-size: 18px;
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
        width: auto;
        padding: 0.5rem 1rem;
        height: 40px;
        font-size: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        border: none;
        outline: none;
        margin: 5px;
    }

    .dt-button:hover {
        transform: scale(1.05);
        background-color: #c82333 !important;
    }

    .dt-button:active {
        transform: scale(0.98);
        box-shadow: none;
    }
</style>
@endsection