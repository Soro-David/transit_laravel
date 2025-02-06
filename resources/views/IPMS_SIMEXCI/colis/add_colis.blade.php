@extends('IPMS_SIMEXCI.layouts.agent')

@section('content-header')
@endsection

@section('content')
<section class="p-4 mx-auto">
    
    <form action="{{ route('ipms_colis.store.colis') }}" method="post" class="form-container">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="progress-bar-container text-center mb-4">
            <ul class="progress-bar d-flex justify-content-between list-unstyled position-relative">
                <li class="step active" data-step="0">1</li>
            </ul>
            <ul class="progress-bar d-flex justify-content-between list-unstyled position-relative">
                <li class="step" data-step="1">2</li>
                
            </ul>
            <ul class="progress-bar d-flex justify-content-between list-unstyled position-relative">
                <li class="step" data-step="2">3</li>
                
            </ul>
            <ul class="progress-bar d-flex justify-content-between list-unstyled position-relative">
                <li class="step" data-step="3">4</li>
            </ul>
        </div>
        <!-- Étape 1 : Informations de l'Expéditeur -->
        <fieldset>
            <div class="form-section">
                <h5 class="text-center mb-4 mt-5">Informations de l'Expéditeur</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nom_expediteur" class="form-label">Nom</label>
                            <input type="text" name="nom_expediteur" id="nom_expediteur" 
                                    value="{{ old('nom_expediteur') }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="prenom_expediteur" class="form-label">Prénom</label>
                            <input type="text" name="prenom_expediteur" id="prenom_expediteur" 
                                    value="{{ old('prenom_expediteur') }}" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email_expediteur" class="form-label">Email</label>
                            <input type="email" name="email_expediteur" id="email_expediteur" 
                                    value="{{ old('email_expediteur') }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tel_expediteur" class="form-label">Téléphone</label>
                            <input type="text" name="tel_expediteur" id="tel_expediteur" 
                                    value="{{ old('tel_expediteur') }}" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="adresse_expediteur" class="form-label">Adresse</label>
                            <input type="text" name="adresse_expediteur" id="adresse_expediteur" 
                                       value="{{ old('adresse_expediteur') }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="agence_expedition" class="form-label">Agence d'expédition</label>
                            <select name="agence_expedition" id="agence_expedition" class="form-control">
                                <option value="" disabled selected>-- Sélectionnez l'agence d'expédition --</option>
                                @foreach ($agences as $agence)
                                    <option value="{{ $agence->nom_agence }}">{{ $agence->nom_agence }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Boutons navigation --}}
            <div class="text-end mt-4 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary btn-prev" style="display: none;">Précédent</button>
                <button type="button" class="btn btn-primary btn-next">Suivant</button>
            </div>
        </fieldset>

        <!-- Étape 2 : Informations du Destinataire -->
        <fieldset style="display: none;">
            <h5 class="text-center mb-4">Informations du Destinataire</h5>
            <div class="form-section">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nom_destinataire" class="form-label">Nom</label>
                            <input type="text" name="nom_destinataire" id="nom_destinataire" 
                                       value="{{ old('nom_destinataire') }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="prenom_destinataire" class="form-label">Prénom</label>
                            <input type="text" name="prenom_destinataire" id="prenom_destinataire" 
                                       value="{{ old('prenom_destinataire') }}" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email_destinataire" class="form-label">Email</label>
                            <input type="email" name="email_destinataire" id="email_destinataire" 
                                       value="{{ old('email_destinataire') }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tel_destinataire" class="form-label">Téléphone</label>
                            <input type="text" name="tel_destinataire" id="tel_destinataire" 
                                       value="{{ old('tel_destinataire') }}" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="adresse_destinataire" class="form-label">Adresse</label>
                            <input type="text" name="adresse_destinataire" id="adresse_destinataire" 
                                       value="{{ old('adresse_destinataire') }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="agence_destination" class="form-label">Agence de destination</label>
                            <select name="agence_destination" id="agence_destination" class="form-control">
                                <option value="" disabled selected>-- Sélectionnez l'agence de destination --</option>
                                @foreach ($agences as $agence)
                                    <option value="{{ $agence->nom_agence }}">{{ $agence->nom_agence }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Boutons navigation --}}
            <div class="text-end mt-4 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary btn-prev" style="display: none;">Précédent</button>
                <button type="button" class="btn btn-primary btn-next">Suivant</button>
            </div>
        </fieldset>
        <!-- Étape 3 : Mode de transit -->
        <fieldset style="display: none;">
            <h5 class="text-center mb-4 mt-5">Informations sur le mode de transport</h5>
            <div class="form-section">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="mode_transit" class="form-label">Sélectionnez le mode de transit</label>
                            <select name="mode_transit" id="mode_transit" class="form-control">
                                <option value="" disabled selected>-- Sélectionnez le mode de transit --</option>
                                <option value="maritime">Maritime</option>
                                <option value="aerien">Aérien</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="reference_colis" class="form-label">Référence</label>
                            <input type="text" name="reference_colis" id="reference_colis" 
                                value="{{ $referenceColis}}" 
                                class="form-control" readonly>
                        </div>
                    </div>
                    <div class="text-end mt-4 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary btn-prev" style="display: none;">Précédent</button>
                        <button type="button" class="btn btn-primary btn-next">Suivant</button>
                    </div>
                </div>
            </div>
        </fieldset>
        <!-- Étape 4 : Informations du Colis -->
        <fieldset id="colisTemplate" style="display: none;">
            <h5 class="text-center mb-4 mt-5">Informations du Colis</h5>
            <div class="form-section">
                <div class="row">
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="quantite_colis" class="form-label">Quantité</label>
                            <input type="number" name="quantite_colis[]" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="type_embalage" class="form-label">Type d'emballage</label>
                            <input type="text" name="type_embalage[]" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6" id="dimension_section">
                        <label class="form-label">Dimensions (cm)</label>
                        <div class="d-flex gap-2">
                            <input type="number" id="hauteur" name="hauteur[]" class="form-control" placeholder="Hauteur">
                            <input type="number" id="largeur" name="largeur[]" class="form-control" placeholder="Largeur">
                            <input type="number" id="longueur" name="longueur[]" class="form-control" placeholder="Longueur">
                        </div>
                        <div id="dimension_result" name="dimension_result" class="mt-2" style="display: none; font-weight: bold;"></div>
                        {{-- <div id="dimension_result"   class="mt-2" style="display: none; font-weight: bold;"></div> --}}
                    </div>                    
                    <div class="col-md-6" id="poids_section" style="display: none;">
                        <label class="form-label">Poids (kg)</label>
                        <input type="number" name="poids[]" class="form-control" placeholder="Poids">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="type_colis" class="form-label">Type de colis</label>
                            <select name="type_colis[]" class="form-control">
                                <option value="" disabled selected>-- Sélectionnez le type de colis --</option>
                                <option value="standard">Standard</option>
                                <option value="fragile">Fragile</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-8 col-md-8 col-lg-8">
                        <div class="mb-3">
                            <label for="description_colis" class="form-label">Description colis</label>
                            <textarea 
                            name="description_colis[]" 
                            id="description_colis" 
                            class="form-control" 
                            rows="4"
                            placeholder="Saisissez la description du colis"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-end mt-2">
                {{-- <a href="#" class="btn btn-link add-colis">Ajouter un autre colis</a> --}}
                <button type="button" class="btn btn-seccess add-colis" style="color: rgb(187, 90, 10)">Ajouter un autre colis</button>
                <button type="button" class="btn btn-danger remove-colis">Retirer ce colis</button>
            </div>
            <div id="colisContainer"></div>
            <div class="text-end mt-4 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary btn-prev" style="display: none;">Précédent</button>
                <button type="submit" class="btn btn-success" style="display: none;" >Valider</button>
            </div>
        </fieldset>
</form>
</section>

<script>
    $(document).ready(function () {
    // Fonction pour afficher les champs en fonction du mode de transport sélectionné
    $("#mode_transit").change(function () {
        let mode = $(this).val();
        if (mode === "maritime") {
            $("#dimension_section").show();
            $("#poids_section").hide();
        } else if (mode === "aerien") {
            $("#dimension_section").hide();
            $("#poids_section").show();
        }
    });

    // Afficher les dimensions en texte (ex: 30x50x20)
    $(document).on("input", "#hauteur, #largeur, #longueur", function () {
        let hauteur = $("#hauteur").val();
        let largeur = $("#largeur").val();
        let longueur = $("#longueur").val();

        if (hauteur && largeur && longueur) {
            $("#dimension_result").text(`${longueur}x${largeur}x${hauteur} cm`).show();
        } else {
            $("#dimension_result").hide();
        }
    });

    // Ajouter un nouveau colis avec la même logique
    $(document).on("click", ".add-colis", function (e) {
        e.preventDefault();
        const newColis = `
            <div class="colis-fieldset mb-4">
                <div class="row">
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="quantite_colis" class="form-label">Quantité de colis</label>
                            <input type="number" name="quantite_colis[]" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="type_embalage" class="form-label">Type d'emballage</label>
                            <input type="text" name="type_embalage[]" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6 dimension-section">
                        <label class="form-label">Dimensions (cm)</label>
                        <div class="d-flex gap-2">
                            <input type="number" name="hauteur[]" class="form-control hauteur" placeholder="Hauteur">
                            <input type="number" name="largeur[]" class="form-control largeur" placeholder="Largeur">
                            <input type="number" name="longueur[]" class="form-control longueur" placeholder="Longueur">
                        </div>
                        <div class="dimension-result mt-2" style="display: none; font-weight: bold;"></div>
                    </div>
                    <div class="col-md-6 poids-section" style="display: none;">
                        <label class="form-label">Poids (kg)</label>
                        <input type="number" name="poids[]" class="form-control" placeholder="Poids">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="type_colis" class="form-label">Type de colis</label>
                            <select name="type_colis[]" class="form-control">
                                <option value="" disabled selected>-- Sélectionnez le type de colis --</option>
                                <option value="standard">Standard</option>
                                <option value="fragile">Fragile</option>
                            </select>
                        </div>
                    </div>
                </div>
               <div class="col-8 col-md-8 col-lg-8">
                    <div class="mb-3">
                        <label for="description_colis" class="form-label">Description colis</label>
                        <textarea 
                        name="description_colis[]" 
                        id="description_colis" 
                        class="form-control" 
                        rows="4"
                        placeholder="Saisissez la description du colis"></textarea>
                    </div>
                </div>
            </div>
        `;
        $("#colisContainer").append(newColis);
        toggleFields(); // Appliquer les règles d'affichage pour le nouveau colis
    });

    // Supprimer un colis
    $(document).on("click", ".remove-colis", function () {
        $(this).closest(".colis-fieldset").remove();
    });

    // Fonction pour appliquer les règles d'affichage sur les colis existants
    function toggleFields() {
        let mode = $("#mode_transit").val();
        $(".dimension-section").toggle(mode === "maritime");
        $(".poids-section").toggle(mode === "aerien");
    }

    // Appliquer les changements lors de la sélection du mode de transit
    $("#mode_transit").change(function () {
        toggleFields();
    });

    // Afficher les dimensions sous format texte pour chaque colis ajouté
    $(document).on("input", ".hauteur, .largeur, .longueur", function () {
        let parent = $(this).closest(".colis-fieldset");
        let hauteur = parent.find(".hauteur").val();
        let largeur = parent.find(".largeur").val();
        let longueur = parent.find(".longueur").val();
        let resultDiv = parent.find(".dimension-result");

        if (hauteur && largeur && longueur) {
            resultDiv.text(`${longueur}x${largeur}x${hauteur} cm`).show();
        } else {
            resultDiv.hide();
        }
    });

    // Initialiser les champs visibles selon le mode de transport sélectionné
    toggleFields();
});


$(document).ready(function () {
    let currentStep = 0;
    const fieldsets = $("fieldset");

    // Fonction pour afficher une étape spécifique
    function showStep(step) {
        fieldsets.hide().eq(step).show(); // Afficher uniquement l'étape actuelle
        toggleButtons(step); // Gérer la visibilité des boutons
    }

    // Fonction pour gérer la visibilité des boutons
    function toggleButtons(step) {
        const isLastStep = step === fieldsets.length - 1; // Vérifie si c'est la dernière étape

        // Afficher ou masquer les boutons en fonction de l'étape
        $(".btn-prev").toggle(step > 0); // Afficher "Précédent" sauf à l'étape 0
        $(".btn-next").toggle(!isLastStep); // Afficher "Suivant" sauf à la dernière étape
        $("button[type='submit']").toggle(isLastStep); // Afficher "Valider" uniquement à la dernière étape
    }

    // Gestion des boutons "Suivant" et "Précédent"
    $(".btn-next").click(function () {
        if (currentStep < fieldsets.length - 1) {
            currentStep++;
            showStep(currentStep);
        }
    });

    $(".btn-prev").click(function () {
        if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
        }
    });


    // Afficher l'étape initiale
    showStep(currentStep);
});
$(document).on("input", ".hauteur, .largeur, .longueur", function () {
    const parent = $(this).closest(".dimension_section");
    const hauteur = parent.find(".hauteur").val().trim();
    const largeur = parent.find(".largeur").val().trim();
    const longueur = parent.find(".longueur").val().trim();
    const dimensionResult = parent.find(".dimension_result");

    if (hauteur && largeur && longueur) {
        dimensionResult.text(`${hauteur}x${largeur}x${longueur} cm`).show();
    } else {
        dimensionResult.hide();
    }
});

    $(document).ready(function () {
        $(document).on('click', '#remove-colis', function () {
            var colisFieldset = $(this).closest('fieldset');
            if ($('fieldset').length > 1) {
                colisFieldset.remove();
                updateFieldsetButtons();
            }
        });
        // Update the buttons visibility for the fieldsets
        function updateFieldsetButtons() {
            var allFieldsets = $('fieldset');
            allFieldsets.each(function (index) {
                var btnPrev = $(this).find('.btn-prev');
                var btnValider = $(this).find('button[type="submit"]');
                if (index === allFieldsets.length - 1) {
                    btnPrev.show();
                    btnValider.show();
                } else {
                    btnPrev.hide();
                    btnValider.hide();
                }
            });
    }



        // Handle transit mode visibility based on selection
        $('#mode_transit').on('change', function () {
            const selectedMode = $(this).val();
            const modeActions = {
                'maritime': () => { $('#poids_section').hide(); $('#dimension_section').show(); },
                'aerien': () => { $('#dimension_section').hide(); $('#poids_section').show(); },
                '': () => { $('#poids_section, #dimension_section').hide(); }
            };
            (modeActions[selectedMode] || modeActions[''])();
        });

        // Initial hiding of sections
        $('#poids_section, #dimension_section').hide();

        // Multi-step form handling
        let currentStep = 0;
        const fieldsets = document.querySelectorAll("fieldset");
        const steps = document.querySelectorAll(".step");

        function showStep(step) {
            fieldsets.forEach((fieldset, index) => {
                fieldset.style.display = index === step ? "block" : "none";
            });
            updateProgressBar(step);
            toggleButtons(step);
        }

        function updateProgressBar(step) {
            steps.forEach((stepElement, index) => {
                stepElement.classList.toggle("active", index <= step);
            });
        }

        function toggleButtons(step) {
        const isLastStep = step === fieldsets.length - 1; // Vérifie si c'est la dernière étape

        // Afficher ou masquer les boutons en fonction de l'étape
        $(".btn-prev").toggle(step > 0); // Afficher "Précédent" sauf à l'étape 0
        $(".btn-next").toggle(!isLastStep); // Afficher "Suivant" sauf à la dernière étape
        $("button[type='submit']").toggle(isLastStep); // Afficher "Valider" uniquement à la dernière étape
    }
        // Handle next and previous buttons for multi-step form
        document.querySelectorAll(".btn-next").forEach(button => {
            button.addEventListener("click", (e) => {
                e.preventDefault();
                if (currentStep < fieldsets.length - 1) {
                    currentStep++;
                    showStep(currentStep);
                }
            });
        });

        document.querySelectorAll(".btn-prev").forEach(button => {
            button.addEventListener("click", (e) => {
                e.preventDefault();
                if (currentStep > 0) {
                    currentStep--;
                    showStep(currentStep);
                }
            });
        });

        // Click on step number to navigate
        steps.forEach((stepElement, index) => {
            stepElement.addEventListener("click", () => {
                currentStep = index;
                showStep(currentStep);
            });
        });

        // Initial step display
        showStep(currentStep);
        });

        document.addEventListener("DOMContentLoaded", function () {
    const hauteurInput = document.getElementById("hauteur");
    const largeurInput = document.getElementById("largeur");
    const longueurInput = document.getElementById("longueur");
    const dimensionResult = document.getElementById("dimension_result");

    function updateDimensionDisplay() {
        const hauteur = hauteurInput.value.trim();
        const largeur = largeurInput.value.trim();
        const longueur = longueurInput.value.trim();
        
        if (hauteur !== "" && largeur !== "" && longueur !== "") {
            dimensionResult.textContent = `${hauteur} x ${largeur} x ${longueur} cm`;
            dimensionResult.style.display = "block";
        } else {
            dimensionResult.style.display = "none";
        }
    }

    [hauteurInput, largeurInput, longueurInput].forEach(input => {
        input.addEventListener("input", updateDimensionDisplay);
    });
});

</script>
<style>
body {
    background-color: #f7f7f7;
}

fieldset + fieldset {
    border-top: 2px solid #ccc;
    padding-top: 15px;
    margin-top: 15px;
}

.form-container {
    max-width: 95%;
    margin: auto;
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

.progress-bar {
    display: flex; /* Utilise flexbox pour aligner les éléments */
    justify-content: space-between; /* Espace égal entre les étapes */
    list-style: none; /* Supprime les puces de la liste */
    background: #fff; /* Couleur de fond */
    padding: 0; 
    margin: 50px; /* Supprime les marges */
}
.progress-bar-container {
    margin-bottom: 20px;
    display: flex; /* Use flexbox for centering */
    justify-content: center; /* Center the progress bar */
    width: 100%; /* Prend toute la largeur disponible */
}

/* Permettre le défilement horizontal si nécessaire */
.progress-bar-container {
  overflow-x: auto;
}

/* Les listes de progression sont déjà en flex via Bootstrap ;
   on peut ajouter quelques réglages pour améliorer l’affichage */
.progress-bar {
  flex-wrap: wrap; /* si les écrans sont trop petits, les éléments peuvent se répartir sur plusieurs lignes */
  margin: 0 auto;  /* centrer */
}

/* Pour les éléments de la liste, on s’assure qu’ils s’adaptent */
.progress-bar li.step {
  flex: 1;              /* prend une part égale de l’espace disponible */
  min-width: 40px;      /* largeur minimale pour conserver la lisibilité */
  text-align: center;   /* centrer le contenu */
  font-size: 1rem;      /* taille de police par défaut */
}

/* Sur écrans moyens à grands, on peut augmenter la taille de police */
@media (min-width: 768px) {
  .progress-bar li.step {
    font-size: 1.25rem;
  }
}

.progress-bar::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    height: 5px;
    background: #ddd;
    z-index: -1;
    transform: translateY(-50%);
}

.step {
    width: 40px;
    height: 40px;
    line-height: 40px;
    background: #ddd;
    color: #333;
    text-align: center;
    border-radius: 50%;
    cursor: pointer;
    font-weight: bold;
    position: relative;
    /* z-index: 1; */
}

.step.active {
    background: #05a805;
    color: #fff;
}


.step::after {
    content: ''; /* Create a line after each step */
    position: absolute; /* Position the line absolutely */
    top: 50%; /* Center vertically */
    left: 100%; /* Position to the right of the step */
    width: 100%; /* Width of the line */
    height: 4px; /* Height of the line */
    background-color: #ddd; /* Color of the line */
    z-index: -1; /* Send the line behind the text */
}

.step:last-child::after {
    content: none; /* Remove the line after the last step */
}

.step.active {
    font-weight: bold; /* Bold the active step */
    color: #ffffff; /* Color of the active step */
}

</style>

@endsection



