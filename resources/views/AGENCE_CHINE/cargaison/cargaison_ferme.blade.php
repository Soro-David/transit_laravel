@extends('AGENCE_CHINE.layouts.agent')
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
                    <form action="{{ route('chine_colis.bateaux.store') }}" method="POST">
                        @csrf
                        <div class="row g-3 align-items-center">
                            <!-- Référence du bateau (rempli automatiquement) -->
                            <div class="col-md-3">
                                <label for="reference_bateau" class="form-label fw-bold">Référence du bateau:</label>
                                <input type="text" name="reference_bateau" id="reference_bateau" class="form-control" readonly>
                            </div>
    
                            <!-- Sélection de la référence du conteneur -->
                            <div class="col-md-3">
                                <label for="reference_conteneur" class="form-label fw-bold">Référence conteneur:</label>
                                <select id="reference_conteneur" name="reference_conteneur" class="form-select" onchange="generateReferenceBateau()">
                                    <option value="" disabled selected>-- Sélectionnez la référence --</option>
                                    @foreach ($referenceFermes as $reference)
                                        <option value="{{ $reference }}">{{ $reference }}</option>
                                    @endforeach
                                </select>
                            </div>
    
                            <!-- Type de véhicule -->
                            <div class="col-md-3">
                                <label for="type" class="form-label fw-bold">Véhicule de navigation:</label>
                                <select id="type" name="type" class="form-select" onchange="toggleFields()">
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
    
                            <!-- Champs du ballon -->
                            <div class="col-md-3 ballon-fields" style="display: none;">
                                <label for="numero_ballon" class="form-label fw-bold">Numéro de vol:</label>
                                <input type="text" name="numero_ballon" id="numero_ballon" class="form-control" placeholder="Ex: BA123">
                            </div>
    
                            <div class="col-md-3 ballon-fields" style="display: none;">
                                <label for="nom_ballon" class="form-label fw-bold">Nom de l'Avion:</label>
                                <input type="text" name="nom_ballon" id="nom_ballon" class="form-control" placeholder="Ex: AirOcean">
                            </div>
    
                            <!-- Agence de destination -->
                            <div class="col-md-3">
                                <label for="agence_destination" class="form-label fw-bold">Agence de destination:</label>
                                <select id="agence_destination" name="agence_destination" class="form-select">
                                    <option value="" disabled selected>-- Sélectionnez l'agence --</option>
                                    {{-- Les options seront ajoutées dynamiquement par JavaScript --}}
                                </select>
                            </div>
                            <input type="hidden" name="agence_expedition" value="{{ old('agence_expedition', 'Agence de Chine') }}">
    
                            <!-- Bouton de soumission -->
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-success">Créer</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
        
    <!-- Script JavaScript -->
    <script>
        // Tableau des agences de destination
        const agencesData = {
            'IPMS-SIMEX-CI': 'Carrefour Angre',
            'IPMS-SIMEX-CI Angre 8ème Tranche': 'Angre 8ème Tranche'
        };

        function generateReferenceBateau() {
            const type = document.getElementById("type").value;
            const referenceConteneur = document.getElementById("reference_conteneur").value;
            const mois = "{{ $mois }}";
            const annee = "{{ $annee }}";
            const referenceInput = document.getElementById("reference_bateau");

            if (referenceConteneur && type) {
                const prefix = type === 'bateau' ? 'BAT' : 'AV';
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
            
            generateReferenceBateau(); // Appelle la génération de référence à chaque changement de type

            // Nettoie les options existantes
            agenceDestinationSelect.innerHTML = '<option value="" disabled selected>-- Sélectionnez l\'agence --</option>';

            if (type === "bateau") {
                bateauFields.forEach(field => field.style.display = "block");
                ballonFields.forEach(field => field.style.display = "none");
                
                // Ajoute l'option pour l'agence maritime
                const optionMaritime = document.createElement('option');
                optionMaritime.value = "IPMS-SIMEX-CI";
                optionMaritime.textContent = agencesData["IPMS-SIMEX-CI"]; // Affiche "Carrefour Angre"
                agenceDestinationSelect.appendChild(optionMaritime);
                agenceDestinationSelect.value = "IPMS-SIMEX-CI"; // Sélectionne automatiquement

            } else if (type === "ballon") {
                bateauFields.forEach(field => field.style.display = "none");
                ballonFields.forEach(field => field.style.display = "block");
                
                // Ajoute l'option pour l'agence aérienne
                const optionAerienne = document.createElement('option');
                optionAerienne.value = "IPMS-SIMEX-CI Angre 8ème Tranche";
                optionAerienne.textContent = agencesData["IPMS-SIMEX-CI Angre 8ème Tranche"]; // Affiche "Angre 8ème Tranche"
                agenceDestinationSelect.appendChild(optionAerienne);
                agenceDestinationSelect.value = "IPMS-SIMEX-CI Angre 8ème Tranche"; // Sélectionne automatiquement

            } else {
                bateauFields.forEach(field => field.style.display = "none");
                ballonFields.forEach(field => field.style.display = "none");
                agenceDestinationSelect.value = ""; // Réinitialise la sélection
            }
        }

        // Appeler toggleFields au chargement de la page pour initialiser les champs et l'agence
        document.addEventListener('DOMContentLoaded', toggleFields);
    </script>

    <div class="mt-4">
        <form action="" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="border p-4 rounded shadow-sm">
                        <h4 class="text-left mt-4">Liste des bateaux</h4><br>
                        <div class="table-responsive">
                            <table id="productTable" class="table table-bordered table-striped display">
                                <thead>
                                    <tr>
                                        <th>Référence Bateau</th>
                                        <th>Date depart</th>
                                        <th>Date arriver</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <!-- JavaScript for DataTable and Export -->
    <script>
    $(document).ready(function () {
        var table = $("#productTable").DataTable({
            responsive: true,
            language: {
                    url: "{{ asset('js/fr-FR.json') }}"
                },
            ajax: '{{ route("chine_colis.get.cargaison.ferme") }}',
            columns: [
                { data: 'reference_bateau', title: "Référence Bateau" },
                { data: 'date_depart', title: "Date de Départ" },
                { data: 'date_arriver', title: "Date d'Arrivée" },
                { data: 'actions', title: "Actions", orderable: false, searchable: false }
            ],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Exporter en Excel',
                    title: 'Liste des Colis en attente',
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
                xhr.open('GET', url);
                xhr.responseType = 'blob';
                xhr.send();
            };
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
        width: 100%;
        height: 40px;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        border: none;
        outline: none;
    }

    .dt-button:hover {
        transform: scale(1.1);
        background-color: #c82333 !important;
    }

    .dt-button:active {
        transform: scale(0.95);
        box-shadow: none;
    }
</style>
@endsection