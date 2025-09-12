@extends('customer.layouts.index')
@section('content-header')
@endsection

@section('content')
<section class="p-4 mx-auto">
    
    <form action="{{ route('customer_colis.store.colis') }}" method="post" class="form-container">
        @csrf

            @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
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
        <!-- Étape 1 : Informations transport -->

        <fieldset style="display: none;">
            <h5 class="text-center mb-4 mt-5">Informations sur le mode de transport</h5>
            <div class="form-section">
                <div class="row">
                    <!-- Sélecteur de mode -->
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
                            <label for="categorie_client" class="form-label">Sélectionnez la catégorie de client</label>
                            <select name="categorie_client" id="categorie_client" class="form-control">
                                <option value="" disabled selected>-- Sélectionnez la catégorie de client --</option>
                                <option value="particulier">Particulier</option>
                                <option value="societe">Sociéte</option>
                            </select>
                        </div>
                    </div>

                    <!-- Maritime -->
                    <div class="col-md-6" id="ref_maritime" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Référence (Maritime)</label>
                            <input type="text" name="reference_colis_maritime" class="form-control" value="{{ $referenceColis_maritime['reference_colis'] ?? '' }}" readonly>
                        </div>
                    </div>

                    <!-- Aérien -->
                    <div class="col-md-6" id="ref_aerien" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Référence (Aérien)</label>
                            <input type="text" name="reference_colis_aerien" class="form-control" value="{{ $referenceColis_aerien['reference_colis'] ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="text-end mt-4 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary btn-prev" style="display: none;">Précédent</button>
                        <button type="button" class="btn btn-primary btn-next">Suivant</button>
                    </div>
                </div>
            </div>
        </fieldset>

        <!-- Étape 2 : Informations de l'Expéditeur -->

        <fieldset>
            <div class="form-section">
                <h5 class="text-center mb-4 mt-5">Informations de l'Expéditeur</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nom_expediteur" class="form-label">Nom</label>
                            <input type="text" name="nom_expediteur" id="nom_expediteur" value="{{$user->first_name }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="prenom_expediteur" class="form-label">Prénom</label>
                            <input type="text" name="prenom_expediteur" id="prenom_expediteur" value="{{$user->last_name }}" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email_expediteur" class="form-label">Email</label>
                            <input type="email" name="email_expediteur" id="email_expediteur" value="{{$user->email }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="row g-2">
                            {{-- <div class="col-md-4">
                                <label for="country_code_expediteur" class="form-label">Indicatif pays</label>
                                <select name="country_code_expediteur" id="country_code_expediteur" class="form-control">
                                    <option value="+225" selected>Côte d'Ivoire (+225)</option>
                                    <option value="+33">France (+33)</option>
                                    <option value="+86">Chine (+86)</option>
                                    <option value="+1">USA (+1)</option>
                                </select>
                            </div> --}}
                            <div class="col-md-12">
                                <label for="tel_expediteur" class="form-label">Téléphone</label>
                                <input type="text" name="tel_expediteur" id="tel_expediteur" value="{{$user->tel}}" class="form-control" placeholder="Ex: 0123456789">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="adresse_expediteur" class="form-label">Adresse</label>
                            <input type="text" name="adresse_expediteur" id="adresse_expediteur" value="{{$user->adresse}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="pays_expedition" class="form-label">Pays d'expédition</label>
                            <select name="pays_expedition" id="pays_expedition" class="form-control">
                                <option value="" disabled selected>-- Sélectionnez le pays d'expédition --</option>
                                @foreach ($paysUniques as $pays)
                                <option value="{{ $pays }}">{{ $pays }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="agence_expedition" class="form-label">Agence d'expédition</label>
                            <select name="agence_expedition" id="agence_expedition" class="form-control">
                                {{-- <option value="" disabled selected>-- Sélectionnez l'agence d'expédition --</option> --}}
                                @foreach ($agencesExpedition as $agence)
                                <option value="{{ $agence->nom_agence }}" data-pays="{{ $agence->pays_agence }}">
                                    {{ $agence->nom_agence }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const paysSelect = document.getElementById('pays_expedition');
                        const agenceSelect = document.getElementById('agence_expedition');
                        const agenceOptions = agenceSelect.querySelectorAll('option[data-pays]');

                        paysSelect.addEventListener('change', function() {
                            const selectedPays = this.value;

                            // Réinitialiser les options
                            agenceSelect.innerHTML = '';

                            // Ajouter uniquement les options correspondant au pays sélectionné
                            agenceOptions.forEach(option => {
                                if (option.getAttribute('data-pays') === selectedPays) {
                                    agenceSelect.appendChild(option.cloneNode(true));
                                }
                            });
                        });

                        // Déclencher l'événement "change" au chargement de la page si un pays est déjà sélectionné
                        if (paysSelect.value) {
                            paysSelect.dispatchEvent(new Event('change'));
                        }
                    });
                </script>
            </div>
            {{-- Boutons navigation --}}
            <div class="text-end mt-4 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary btn-prev" style="display: none;">Précédent</button>
                <button type="button" class="btn btn-primary btn-next">Suivant</button>
            </div>
        </fieldset>

         {{-- ================== DESTINATAIRE ================== --}}
        <fieldset>
            <div class="form-section">
                <h5 class="text-center mb-4">Informations du destinataire</h5>

                {{-- Section pour le destinataire SOCIÉTÉ --}}
                <div id="societe_destinataire_section" style="display: none;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom_societe_destinataire" class="form-label">Nom de la société</label>
                            <input type="text" name="nom_destinataire_societe" id="nom_societe_destinataire" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email_societe_destinataire" class="form-label">Email</label>
                            <input type="email" name="email_destinataire_societe" id="email_societe_destinataire" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label for="country_code_destinataire_societe" class="form-label">Indicatif pays</label>
                                    <select name="country_code_destinataire_societe" id="country_code_destinataire_societe" class="form-control">
                                        <option value="+225" selected>Côte d'Ivoire (+225)</option>
                                        <option value="+33">France (+33)</option>
                                        <option value="+86">Chine (+86)</option>
                                        <option value="+1">USA (+1)</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label for="tel_destinataire_societe" class="form-label">Téléphone</label>
                                    <input type="text" name="tel_destinataire_societe" id="tel_destinataire_societe" class="form-control" placeholder="Ex: 0123456789" value="{{ $user->tel ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="adresse_destinataire_societe" class="form-label">Adresse</label>
                            <input type="text" name="adresse_destinataire_societe" id="adresse_destinataire_societe" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="agence_destination_societe" class="form-label">Agence de Destination</label>
                                <select name="agence_destination_societe" id="agence_destination_societe" class="form-control">
                                    <option value="" disabled selected>-- Sélectionnez l'agence de destination --</option>
                                    <option value="IPMS-SIMEX-CI">Carrefour Angré</option>
                                    <option value="IPMS-SIMEX-CI Angre 8ème Tranche">Angré 8ème Tranche</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section pour le destinataire PARTICULIER --}}
                <div id="particulier_destinataire_section">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom_destinataire" class="form-label">Nom</label>
                            <input type="text" name="nom_destinataire" id="nom_destinataire" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="prenom_destinataire" class="form-label">Prénom</label>
                            <input type="text" name="prenom_destinataire" id="prenom_destinataire" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email_destinataire" class="form-label">Email</label>
                            <input type="email" name="email_destinataire" id="email_destinataire" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label for="country_code_destinataire_particulier" class="form-label">Indicatif pays</label>
                                    <select name="country_code_destinataire_particulier" id="country_code_destinataire_particulier" class="form-control">
                                        <option value="+225" selected>Côte d'Ivoire (+225)</option>
                                        <option value="+33">France (+33)</option>
                                        <option value="+86">Chine (+86)</option>
                                        <option value="+1">USA (+1)</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label for="tel_destinataire" class="form-label">Téléphone</label>
                                    <input type="text" name="tel_destinataire" id="tel_destinataire" class="form-control" placeholder="Ex: 0123456789" value="{{ $user->tel ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="adresse_destinataire" class="form-label">Adresse</label>
                            <input type="text" name="adresse_destinataire" id="adresse_destinataire" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="agence_destination_particulier" class="form-label">Agence de Destination</label>
                                <select name="agence_destination_particulier" id="agence_destination_particulier" class="form-control">
                                    <option value="" disabled selected>-- Sélectionnez l'agence de destination --</option>
                                    <option value="IPMS-SIMEX-CI">Carrefour Angré</option>
                                    <option value="IPMS-SIMEX-CI Angre 8ème Tranche">Angré 8ème Tranche</option>
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
            </div>
        </fieldset>


        <!-- Étape 4 : Informations du Colis -->
        <fieldset id="colisTemplate" style="display: none;">
            <h5 class="text-center mb-4 mt-5">Informations du Colis</h5>
            <div class="form-section">
                <div class="row">
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="quantite_colis" class="form-label">Quantité de colis</label>
                            <input type="number" name="quantite_colis[]" class="form-control quantite-colis" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Produit(s) ou Service(s)</label>
                        <div class="input-group">
                            <input type="text" name="service[]" class="form-control produit-input">
                            {{-- <button type="button" class="btn btn-success btn-add" data-bs-toggle="modal" data-bs-target="#produitModal">+</button> --}}
                        </div>
                        <div class="autocomplete-results" style="position: absolute; z-index: 1000; background-color: white; border: 1px solid #ccc; width: 100%; display: none;"></div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">valeur du colis</label>
                        <input type="number" name="valeur_colis[]" class="form-control prix-colis" placeholder="valeur colis">
                        {{-- <div class="mt-2">Prix Total: <span class="prix-total">0</span></div> --}}
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="type_colis" class="form-label">Type colis</label>
                            <select name="type_colis[]" class="form-control">
                                <option value="" disabled selected>-- Type de colis --</option>
                                <option value="standard">Standard</option>
                                <option value="fragile">Fragile</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-2 col-md-2 col-lg-2">
                        <div class="mb-3">
                            <label for="devise" class="form-label">Devise</label>
                            <select name="devise" id="devise" class="form-control">
                                <option value="" disabled selected>-- Devise --</option>
                                <option value="EUR">EUR</option>
                                <option value="FCFA">FCFA</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 dimension-section">
                        <label class="form-label">Dimensions (cm)</label>
                        <div class="d-flex gap-2">
                            <input type="number" name="longueur[]" class="form-control longueur" placeholder="Longueur">
                            <input type="number" name="largeur[]" class="form-control largeur" placeholder="Largeur">
                            <input type="number" name="hauteur[]" class="form-control hauteur" placeholder="Hauteur">
                        </div>
                        <div class="dimension-result mt-2"  name="dimension_result" style="display: none; font-weight: bold;"></div>
                    </div>
                    <div class="col-md-6 poids-section" style="display: none;">
                        <label class="form-label">Poids (kg)</label>
                        <input type="number" name="poids[]" class="form-control" placeholder="Poids">
                    </div>
                    <div class="col-6 col-md-6 col-lg-6">
                        <div class="mb-3">
                            <label for="description_colis" class="form-label">Description Colis</label>
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
                <button type="button" class="btn btn-danger remove-colis" style="display: none">Retirer ce colis</button>
            </div>
            <div id="colisContainer"></div>
            <div class="text-end mt-4 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary btn-prev" style="display: none;">Précédent</button>
                <button type="submit" class="btn btn-success" style="display: none;">Valider</button>
            </div>
        </fieldset>

</form>
</section>

<script>

document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour gérer la sélection de devise selon l'agence
    function handleDeviseSelection() {
        const agenceExpeditionSelect = document.getElementById('agence_expedition');
        const deviseSelect = document.getElementById('devise');
        
        function updateDevise(agenceValue) {
            if (agenceValue === 'Agence de Chine') {
                deviseSelect.value = 'FCFA';
                deviseSelect.disabled = true;
                deviseSelect.style.backgroundColor = '#e9ecef';
            } else if (agenceValue === 'AFT Agence Louis Bleriot') {
                deviseSelect.value = 'EUR';
                deviseSelect.disabled = true;
                deviseSelect.style.backgroundColor = '#e9ecef';
            } else {
                deviseSelect.disabled = false;
                deviseSelect.style.backgroundColor = ''; 
                deviseSelect.value = ''; // Réinitialiser la sélection
            }
        }
        
        // Écouter les changements sur le sélecteur d'agence d'expédition
        if (agenceExpeditionSelect) {
            agenceExpeditionSelect.addEventListener('change', function() {
                updateDevise(this.value);
            });
            
            // Initialiser au chargement si une valeur est déjà sélectionnée
            if (agenceExpeditionSelect.value) {
                updateDevise(agenceExpeditionSelect.value);
            }
        }
    }
    
    // Appeler la fonction
    handleDeviseSelection();
});

    document.addEventListener('DOMContentLoaded', function () {
        const modeTransitSelect = document.getElementById('mode_transit');
        const refMaritime = document.getElementById('ref_maritime');
        const refAerien = document.getElementById('ref_aerien');
        const categorieClientSelect = document.getElementById('categorie_client');

        // Agences destinataires avec IDs différents
        const agenceSelectParticulier = document.getElementById('agence_particulier_destinataire_particulier');
        const agenceSelectSociete = document.getElementById('agence_particulier_destinataire_societe');

        const societeExpediteurSection = document.getElementById('societe_expediteur_section');
        const particulierExpediteurSection = document.getElementById('particulier_expediteur_section');
        const societeDestinataireSection = document.getElementById('societe_destinataire_section');
        const particulierDestinataireSection = document.getElementById('particulier_destinataire_section');


        // Affichage des champs référence selon mode
        function toggleReferenceFields(mode) {
            refMaritime.style.display = mode === 'maritime' ? 'block' : 'none';
            refAerien.style.display = mode === 'aerien' ? 'block' : 'none';
        }

        // Récupération référence via fetch AJAX
        function fetchReference(mode) {
            fetch(`/customer/customer_colis/generer-reference/${mode}`)
                .then(res => res.json())
                .then(data => {
                    if (mode === 'maritime') {
                        document.querySelector('input[name="reference_colis_maritime"]').value = data.reference_colis;
                    } else if (mode === 'aerien') {
                        document.querySelector('input[name="reference_colis_aerien"]').value = data.reference_colis;
                    }
                })
                .catch(err => console.error('Erreur génération référence :', err));
        }

        // Met à jour les options agences destinataires selon mode de transit
        function updateAgenceOptionsByMode(mode) {
            if (!agenceOptionsTransit[mode]) return;

            // Remise à zéro + ajout option unique dans les deux select
            [agenceSelectParticulier, agenceSelectSociete].forEach(select => {
                if (!select) return;
                select.innerHTML = '<option value="" disabled selected>-- Sélectionnez l\'agence --</option>';
                const option = document.createElement('option');
                option.value = agenceOptionsTransit[mode].value;
                option.textContent = agenceOptionsTransit[mode].label;
                select.appendChild(option);
                select.value = option.value; // sélection automatique
            });
        }

        // Affiche/masque les sections selon la catégorie client
        function toggleCategorieClientFields(categorie) {
            const isSociete = categorie === 'societe';
            societeExpediteurSection.style.display = isSociete ? 'block' : 'none';
            particulierExpediteurSection.style.display = isSociete ? 'none' : 'block';
            societeDestinataireSection.style.display = isSociete ? 'block' : 'none';
            particulierDestinataireSection.style.display = isSociete ? 'none' : 'block';
        }

        // Écouteur changement mode transit
        modeTransitSelect.addEventListener('change', function () {
            const selectedMode = this.value;
            toggleReferenceFields(selectedMode);
            fetchReference(selectedMode);
            updateAgenceOptionsByMode(selectedMode);
        });

        // Écouteur changement catégorie client
        categorieClientSelect.addEventListener('change', function () {
            toggleCategorieClientFields(this.value);
        });

        // Initialisation au chargement si valeurs déjà sélectionnées
        if (modeTransitSelect.value) {
            toggleReferenceFields(modeTransitSelect.value);
            updateAgenceOptionsByMode(modeTransitSelect.value);
        }
        if (categorieClientSelect.value) {
            toggleCategorieClientFields(categorieClientSelect.value);
        }
    });

            // Fonction pour mettre à jour l'agence de destination selon le mode de transit sélectionné
    document.addEventListener('DOMContentLoaded', function() {
        // Supprimez le style="display: none;" du fieldset si vous voulez qu'il soit visible par défaut
        // document.querySelector('fieldset').style.display = 'block';

        // Gérer le changement du mode de destinataire (particulier/société)
        // Vous aurez besoin d'un élément de sélection ou de boutons radio pour cela.
        // Par exemple, si vous avez un select avec l'ID 'type_destinataire':
        var typeDestinataireSelect = document.getElementById('type_destinataire'); // Assurez-vous que cet ID existe dans votre HTML principal
        var societeSection = document.getElementById('societe_destinataire_section');
        var particulierSection = document.getElementById('particulier_destinataire_section');

        // Fonction pour afficher/masquer les sections
        function toggleDestinataireSections() {
            if (typeDestinataireSelect && typeDestinataireSelect.value === 'societe') {
                societeSection.style.display = 'block';
                particulierSection.style.display = 'none';
            } else { // Par défaut ou si 'particulier' est sélectionné
                societeSection.style.display = 'none';
                particulierSection.style.display = 'block';
            }
        }

        // Appeler au chargement de la page et lors du changement
        if (typeDestinataireSelect) {
            typeDestinataireSelect.addEventListener('change', toggleDestinataireSections);
            toggleDestinataireSections(); // Appeler une fois au chargement pour définir l'état initial
        }


        // Gérer le changement du mode de transit pour les agences de destination
        // Assurez-vous que l'élément 'mode_transit' existe dans votre HTML principal
        var modeTransitSelect = document.getElementById('mode_transit');
        var agenceDestinationSociete = document.getElementById('agence_destination_societe');
        var agenceDestinationParticulier = document.getElementById('agence_destination_particulier');

        if (modeTransitSelect) {
            modeTransitSelect.addEventListener('change', function() {
                var modeTransit = this.value;

                // Réinitialiser les valeurs pour éviter des sélections incorrectes
                agenceDestinationSociete.value = '';
                agenceDestinationParticulier.value = '';

                if (modeTransit === 'maritime') {
                    agenceDestinationSociete.value = 'IPMS-SIMEX-CI';
                    agenceDestinationParticulier.value = 'IPMS-SIMEX-CI';
                } else if (modeTransit === 'aerien') {
                    agenceDestinationSociete.value = 'IPMS-SIMEX-CI Angre 8ème Tranche';
                    agenceDestinationParticulier.value = 'IPMS-SIMEX-CI Angre 8ème Tranche';
                }
            });
        }

        // Gestion de la navigation (btn-prev, btn-next) - vous devrez implémenter la logique
        // pour passer d'un fieldset à l'autre si vous avez plusieurs fieldsets.
        // Pour un seul fieldset, ces boutons peuvent contrôler l'affichage du fieldset lui-même
        // ou des étapes internes si votre form-section est une étape.
    });

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
document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour gérer la sélection de devise selon l'agence
    function handleDeviseSelection() {
        const agenceExpeditionSelect = document.getElementById('agence_expedition');
        
        function updateDevise(agenceValue, deviseSelect) {
            if (agenceValue === 'Agence de Chine') {
                deviseSelect.value = 'FCFA';
                deviseSelect.disabled = true;
                deviseSelect.style.backgroundColor = '#e9ecef';
            } else if (agenceValue === 'AFT Agence Louis Bleriot') {
                deviseSelect.value = 'EUR';
                deviseSelect.disabled = true;
                deviseSelect.style.backgroundColor = '#e9ecef';
            } else {
                deviseSelect.disabled = false;
                deviseSelect.style.backgroundColor = ''; 
                deviseSelect.value = ''; 
            }
        }
        
        // Fonction pour appliquer la configuration de devise à tous les sélecteurs
        function applyDeviseToAllSelects(agenceValue) {
            const deviseSelects = document.querySelectorAll('select[name="devise"]');
            deviseSelects.forEach(deviseSelect => {
                updateDevise(agenceValue, deviseSelect);
            });
        }
        
        // Écouter les changements sur le sélecteur d'agence d'expédition
        if (agenceExpeditionSelect) {
            agenceExpeditionSelect.addEventListener('change', function() {
                applyDeviseToAllSelects(this.value);
            });
            
            // Initialiser au chargement si une valeur est déjà sélectionnée
            if (agenceExpeditionSelect.value) {
                applyDeviseToAllSelects(agenceExpeditionSelect.value);
            }
        }
    }
    
    // Appeler la fonction
    handleDeviseSelection();
});

$(document).on("click", ".add-colis", function (e) {
    e.preventDefault();

    // Cacher le bouton "Ajouter" qui vient d'être cliqué
    $(this).hide();

    const newColis = `
       <div class="colis-fieldset mb-4">
            <div class="form-section">
                <div class="row">
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="quantite_colis" class="form-label">Quantité de colis</label>
                            <input type="number" name="quantite_colis[]" class="form-control quantite-colis" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Produit(s) ou Service(s)</label>
                        <div class="input-group">
                            <input type="text" name="service[]" class="form-control produit-input">
                        </div>
                        <div class="autocomplete-results" style="position: absolute; z-index: 1000; background-color: white; border: 1px solid #ccc; width: 100%; display: none;"></div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">valeur du colis</label>
                        <input type="number" name="valeur_colis[]" class="form-control prix-colis" placeholder="valeur colis">
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="type_colis" class="form-label">Type colis</label>
                            <select name="type_colis[]" class="form-control">
                                <option value="" disabled selected>-- Type de colis --</option>
                                <option value="standard">Standard</option>
                                <option value="fragile">Fragile</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-2 col-md-2 col-lg-2">
                        <div class="mb-3">
                            <label for="devise" class="form-label">Devise</label>
                            <select name="devise" class="form-control devise-select">
                                <option value="" disabled selected>-- Devise --</option>
                                <option value="EUR">EUR</option>
                                <option value="FCFA">FCFA</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 dimension-section">
                        <label class="form-label">Dimensions (cm)</label>
                        <div class="d-flex gap-2">
                            <input type="number" name="longueur[]" class="form-control longueur" placeholder="Longueur">
                            <input type="number" name="largeur[]" class="form-control largeur" placeholder="Largeur">
                            <input type="number" name="hauteur[]" class="form-control hauteur" placeholder="Hauteur">
                        </div>
                        <div class="dimension-result mt-2" name="dimension_result" style="display: none; font-weight: bold;"></div>
                    </div>
                    <div class="col-md-6 poids-section" style="display: none;">
                        <label class="form-label">Poids (kg)</label>
                        <input type="number" name="poids[]" class="form-control" placeholder="Poids">
                    </div>
                    <div class="col-6 col-md-6 col-lg-6">
                        <div class="mb-3">
                            <label for="description_colis" class="form-label">Description Colis</label>
                            <textarea 
                            name="description_colis[]" 
                            class="form-control" 
                            rows="4"
                            placeholder="Saisissez la description du colis"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-end mt-2">
                <button type="button" class="btn btn-seccess add-colis" style="color: rgb(187, 90, 10)">Ajouter un autre colis</button>
                <button type="button" class="btn btn-danger remove-colis">Retirer ce colis</button>
            </div>
        </div>
    `;

    const $newColis = $(newColis);
    $("#colisContainer").append($newColis);
    
    // Appliquer la configuration de devise au nouveau colis
    const agenceExpeditionSelect = document.getElementById('agence_expedition');
    if (agenceExpeditionSelect && agenceExpeditionSelect.value) {
        const deviseSelect = $newColis.find('.devise-select')[0];
        if (deviseSelect) {
            if (agenceExpeditionSelect.value === 'Agence de Chine') {
                deviseSelect.value = 'FCFA';
                deviseSelect.disabled = true;
                deviseSelect.style.backgroundColor = '#e9ecef';
            } else if (agenceExpeditionSelect.value === 'AFT Agence Louis Bleriot') {
                deviseSelect.value = 'EUR';
                deviseSelect.disabled = true;
                deviseSelect.style.backgroundColor = '#e9ecef';
            }
        }
    }
    
    toggleFields();
    attachDimensionListeners($newColis);
});

// Fonction pour attacher les écouteurs d'événements aux champs de dimension
function attachDimensionListeners($element) {
    $element.find(".hauteur, .largeur, .longueur").on("input", function() {
        const parent = $(this).closest(".colis-fieldset");
        const hauteur = parent.find(".hauteur").val().trim();
        const largeur = parent.find(".largeur").val().trim();
        const longueur = parent.find(".longueur").val().trim();
        const dimensionResult = parent.find(".dimension-result");

        if (hauteur && largeur && longueur) {
            dimensionResult.text(`${longueur}x${largeur}x${hauteur} cm`).show();
        } else {
            dimensionResult.hide();
        }
    });
}

// Fonction pour basculer l'affichage des champs selon le mode de transport
function toggleFields() {
    const mode = $("#mode_transit").val();
    $(".dimension-section").toggle(mode === "maritime");
    $(".poids-section").toggle(mode === "aerien");
}

// Initialiser au chargement
$(document).ready(function() {
    toggleFields();
    attachDimensionListeners($(document));
});
// Fonction pour mettre à jour l'affichage des dimensions
function attachDimensionListeners(context) {
    $(context).find('.hauteur, .largeur, .longueur').on('input', function () {
        const parent = $(this).closest('.dimension-section, [data-dimension-block]');
        const hauteur = parent.find('.hauteur').val();
        const largeur = parent.find('.largeur').val();
        const longueur = parent.find('.longueur').val();
        const result = parent.find('.dimension-result, .dimension_result');

        if (hauteur && largeur && longueur) {
            result.text(`${hauteur} x ${largeur} x ${longueur} cm`).show();
        } else {
            result.hide();
        }
    });
}

// Activer les listeners pour les colis existants au chargement de la page
$(document).ready(function () {
    $('.colis-fieldset').each(function () {
        attachDimensionListeners(this);
    });
});


// Activer la logique au chargement de la page
$(document).ready(function () {
    $('.colis-fieldset').each(function () {
        attachDimensionListeners(this);
    });
});

    // MODIFICATION - Cette partie est déplacée plus haut pour éviter la redondance
    // $(document).on("click", ".add-colis", function (e) { ... });

    // Supprimer un colis
    $(document).on("click", ".remove-colis", function () {
        $(this).closest(".colis-fieldset").remove();

        // MODIFICATION : Après suppression, on affiche le bouton "Ajouter" du nouveau dernier formulaire
        if ($("#colisContainer .colis-fieldset").length > 0) {
            // S'il reste des formulaires ajoutés, on cible le dernier et on montre son bouton
            $("#colisContainer .colis-fieldset:last").find('.add-colis').show();
        } else {
            // S'il n'y a plus de formulaire ajouté, on montre le bouton du formulaire original
            $('#colisTemplate').find('.add-colis').show();
        }
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

    .row.align-items-end.mb-3 > .col-md-3,
        .row.align-items-end.mb-3 > .col-md-9 {
            display: flex;
            align-items: flex-end;
        }

        .col-md-3 .form-label,
        .col-md-9 .form-label {
            width: 100%;
        }

        .col-md-3 .form-control,
        .col-md-9 .form-control {
            width: 100%;
        }
</style>

@endsection