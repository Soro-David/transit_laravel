@extends('admin.layouts.admin')
@section('content-header')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<section class="p-4 mx-auto">

    <form action="{{ route('colis.store.colis') }}" method="post" class="form-container">
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
            <ul class="progress-bar d-flex justify-content-between list-unstyled position-relative">
                <li class="step" data-step="4">5</li>
            </ul>
            <ul class="progress-bar d-flex justify-content-between list-unstyled position-relative">
                <li class="step" data-step="5">6</li>
            </ul>
        </div>

        <!-- Étape 1 : Informations transport -->
        <fieldset style="display: none;">
            <h5 class="text-center mb-4 mt-5">Informations sur le mode de transport</h5>
            <div class="form-section">
                <div class="row">
                    <!-- Sélecteur de mode -->
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="mode_transit" class="form-label">Sélectionnez le mode de transit</label>
                            <select name="mode_transit" id="mode_transit" class="form-control">
                                <option value="" disabled selected>-- Sélectionnez le mode de transit --</option>
                                <option value="maritime">Maritime</option>
                                <option value="aerien">Aérien</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="agence_societe_expediteur" class="form-label">Agence d'expédition</label>
                        <select name="agence_expediteur_societe" id="agence_societe_expediteur" class="form-control">
                            <option value="" disabled selected>-- Sélectionnez l'agence --</option>
                            @foreach ($agencesExpedition as $agence)
                                <option value="{{ $agence->nom_agence }}">{{ $agence->nom_agence }}</option>
                            @endforeach
                        </select>
                    </div>
                     <div class="col-md-4 mb-3">
                        <label for="agence_particulier_destinataire" class="form-label">Agence de destination</label>
                        <select name="agence_destinataire_societe" id="agence_particulier_destinataire_societe" class="form-control">
                            <option value="" disabled selected>-- Sélectionnez l'agence --</option>
                        </select>
                    </div>

                    <!-- Maritime -->
                    <div class="col-md-6" id="ref_maritime"  readonly>
                        <div class="mb-3">
                            <label class="form-label">Référence (Maritime)</label>
                            <input type="text" name="reference_colis_maritime" class="form-control" value="{{ $referenceColis_maritime['reference_colis'] ?? '' }}" readonly>
                        </div>
                    </div>

                    <!-- Aérien -->
                    <div class="col-md-6" id="ref_aerien" readonly>
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
        {{-- ================== EXPÉDITEUR ================== --}}
        <fieldset>
            <div class="form-section">
                <h5 class="text-center mb-4 mt-5">Informations d'expédition</h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="categorie_client" class="form-label">Sélectionnez la catégorie de client</label>
                        <select name="categorie_client" id="categorie_client" class="form-control">
                            <option value="" disabled selected>-- Sélectionnez la catégorie de client --</option>
                            <option value="particulier">Particulier</option>
                            <option value="societe">Société</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="numero_client" class="form-label">Saisissez le numéro du client</label>
                        <!-- J'ai changé l'ID et le nom car c'était une répétition du select précédent -->
                        <input type="text" name="numero_client" id="numero_client" class="form-control" placeholder="Numéro du client">
                    </div>
                </div>

                {{-- ===== SOCIÉTÉ EXPÉDITEUR ===== --}}
                <div id="societe_expediteur_section" style="display: none;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom_societe_expediteur" class="form-label">Nom de la société</label>
                            <input type="text" name="nom_expediteur_societe" id="nom_societe_expediteur" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email_societe_expediteur" class="form-label">Email</label>
                            <input type="email" name="email_expediteur_societe" id="email_societe_expediteur" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tel_expediteur_societe" class="form-label">Téléphone</label>
                            <input type="text" name="tel_expediteur_societe" id="tel_expediteur_societe" class="form-control" placeholder="Ex: 0123456789">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="adresse_expediteur_societe" class="form-label">Adresse</label>
                            <input type="text" name="adresse_expediteur_societe" id="adresse_expediteur_societe" class="form-control">
                        </div>
                    </div>
                </div>

                {{-- ===== PARTICULIER EXPÉDITEUR ===== --}}
                <!-- J'ai ajouté un style display par défaut si vous voulez qu'il soit visible au début -->
                <div id="particulier_expediteur_section">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom_expediteur" class="form-label">Nom</label>
                            <input type="text" name="nom_expediteur" id="nom_expediteur" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="prenom_expediteur" class="form-label">Prénom</label>
                            <input type="text" name="prenom_expediteur" id="prenom_expediteur" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email_expediteur" class="form-label">Email</label>
                            <input type="email" name="email_expediteur" id="email_expediteur" class="form-control">
                        </div>

                        <!-- Indicatif et téléphone sur la même ligne -->
                        <div class="col-md-6 mb-3">
                            <label for="tel_expediteur" class="form-label">Téléphone</label>
                            <input type="text" name="tel_expediteur" id="tel_expediteur" class="form-control" placeholder="Ex: 0123456789">
                        </div>
                    </div>
                    <!-- Vous aviez une div fermante de trop ici -->
                </div>

                {{-- Boutons navigation --}}
                <div class="text-end mt-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary btn-prev" style="display: none;">Précédent</button>
                    <button type="button" class="btn btn-primary btn-next">Suivant</button>
                </div>
            </div>
        </fieldset>

            {{-- ================== DESTINATAIRE ================== --}}
        <fieldset style="display: none;">
            <div class="form-section">
                <h5 class="text-center mb-4">Informations du destinataire</h5>

                <!-- ===== SOCIÉTÉ DESTINATAIRE ===== -->
                <div id="societe_destinataire_section" class="destinataire-section" style="display: none;">
                    <div class="row">
                        <!-- Nom société -->
                        <div class="col-md-6 mb-3">
                            <label for="nom_societe_destinataire" class="form-label">Nom de la société</label>
                            <input type="text" name="nom_destinataire_societe" id="nom_societe_destinataire" class="form-control">
                        </div>

                        <!-- Email société -->
                        <div class="col-md-6 mb-3">
                            <label for="email_societe_destinataire" class="form-label">Email</label>
                            <input type="email" name="email_destinataire_societe" id="email_societe_destinataire" class="form-control">
                        </div>
                        <!-- Téléphone société -->
                        <div class="col-md-6 mb-3">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label for="country_code_societe" class="form-label">Indicatif pays</label>
                                    <select name="country_code_societe" id="country_code_societe" class="form-control">
                                        <option value="+33">France (+33)</option>
                                        <option value="+225">Côte d'Ivoire (+225)</option>
                                        <option value="+86">Chine (+86)</option>
                                        <option value="+1">USA (+1)</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label for="tel_destinataire_societe" class="form-label">Téléphone</label>
                                    <input type="text" name="tel_destinataire_societe" id="tel_destinataire_societe" class="form-control" placeholder="Ex: 0123456789">
                                </div>
                            </div>
                        </div>

                        <!-- Adresse société -->
                        <div class="col-md-6 mb-3">
                            <label for="adresse_societe" class="form-label">Adresse de Livraison</label>
                            <select name="adresse_destinataire_societe" id="adresse_societe" class="form-control">
                                <option value="Pas de livraison">Pas de Livraison</option>
                                <option value="Abobo">Abobo</option>
                                <option value="Adjamé">Adjamé</option>
                                <option value="Attécoubé">Attécoubé</option>
                                <option value="Cocody">Cocody</option>
                                <option value="Palmeraie">Palmeraie</option>
                                <option value="Koumassi">Koumassi</option>
                                <option value="Marcory">Marcory</option>
                                <option value="Plateau">Plateau</option>
                                <option value="Port-Bouët">Port-Bouët</option>
                                <option value="Treichville">Treichville</option>
                                <option value="Yopougon">Yopougon</option>
                                <option value="Songon">Songon</option>
                                <option value="Bingerville">Bingerville</option>
                                <option value="Anyama">Anyama</option>
                                <option value="Grand-Bassam">Grand-Bassam</option>
                                <option value="Dabou">Dabou</option>
                                <option value="Alépé">Alépé</option>
                                <option value="Azaguié">Azaguié</option>
                                <option value="Jacqueville">Jacqueville</option>
                                <option value="Agboville">Agboville</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ===== PARTICULIER DESTINATAIRE ===== -->
                <div id="particulier_destinataire_section" class="destinataire-section">
                    <div class="row">
                        <!-- Nom et prénom -->
                        <div class="col-md-4 mb-3">
                            <label for="nom_destinataire" class="form-label">Nom</label>
                            <input type="text" name="nom_destinataire" id="nom_destinataire" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="prenom_destinataire" class="form-label">Prénom</label>
                            <input type="text" name="prenom_destinataire" id="prenom_destinataire" class="form-control">
                        </div>

                        <!-- Email -->
                        <div class="col-md-4 mb-3">
                            <label for="email_destinataire" class="form-label">Email</label>
                            <input type="email" name="email_destinataire" id="email_destinataire" class="form-control">
                        </div>

                         <!-- Adresse -->
                        <div class="col-md-6 mb-3">
                            <label for="adresse_particulier" class="form-label">Adresse de Livraison</label>
                            <select name="adresse_destinataire_particulier" id="adresse_particulier" class="form-control">
                                <option value="">-- Sélectionnez une commune --</option>
                                <option value="Pas de livraison">Pas de Livraison</option>
                                <option value="Abobo">Abobo</option>
                                <option value="Adjamé">Adjamé</option>
                                <option value="Attécoubé">Attécoubé</option>
                                <option value="Cocody">Cocody</option>
                                <option value="Palmeraie">Palmeraie</option>
                                <option value="Koumassi">Koumassi</option>
                                <option value="Marcory">Marcory</option>
                                <option value="Plateau">Plateau</option>
                                <option value="Port-Bouët">Port-Bouët</option>
                                <option value="Treichville">Treichville</option>
                                <option value="Yopougon">Yopougon</option>
                                <option value="Songon">Songon</option>
                                <option value="Bingerville">Bingerville</option>
                                <option value="Anyama">Anyama</option>
                                <option value="Grand-Bassam">Grand-Bassam</option>
                                <option value="Dabou">Dabou</option>
                                <option value="Alépé">Alépé</option>
                                <option value="Azaguié">Azaguié</option>
                                <option value="Jacqueville">Jacqueville</option>
                                <option value="Agboville">Agboville</option>
                            </select>
                        </div>
                        <!-- Téléphone -->
                        <div class="col-md-6 mb-3">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label for="country_code_particulier" class="form-label">Indicatif pays</label>
                                    <select name="country_code_particulier" id="country_code_particulier" class="form-control">
                                        <option value="+33">France (+33)</option>
                                        <option value="+225">Côte d'Ivoire (+225)</option>
                                        <option value="+86">Chine (+86)</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label for="tel_destinataire" class="form-label">Téléphone</label>
                                    <input type="text" name="tel_destinataire" id="tel_destinataire" class="form-control" placeholder="Ex: 0123456789">
                                </div>
                            </div>
                        </div>

                       
                    </div>
                </div>

                <!-- Boutons navigation -->
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
                            <label for="quantite_colis_1" class="form-label">Quantité</label>
                            <input type="number" name="quantite_colis[]" class="form-control quantite-colis" id_reference="COLIS-1">
                        </div>
                    </div>
                    <div class="col-md-4 position-relative">
                        <label class="form-label">Produit(s)</label><div class="input-group">
                            <input type="text" name="produit_nom[]" class="form-control produit-input">
                            <button type="button" class="btn btn-success btn-add-produit" data-bs-toggle="modal" data-bs-target="#produitModal">+</button>
                        </div>
                        <div class="autocomplete-results"></div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Prix</label>
                        <input type="number" name="prix[]" class="form-control prix-colis" placeholder="Prix">
                        <div class="mt-2">Prix Total: <span class="prix-total">0</span></div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="type_colis_1" class="form-label">Type colis</label>
                            <select name="type_colis[]" class="form-control" id="type_colis_1">
                                <option value="" disabled selected>-- Type de colis --</option>
                                <option value="standard">Standard</option>
                                <option value="fragile">Fragile</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-2 col-md-2 col-lg-2">
                        <div class="mb-3">
                            <label for="devise_1" class="form-label">Devise</label>
                            <select name="devise" id="devise_1" class="form-control">
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
                            <label for="description_colis_1" class="form-label">Commentaire</label>
                            <textarea 
                            name="description_colis[]" 
                            id="description_colis_1" 
                            class="form-control" 
                            rows="4"
                            placeholder="Saisissez la description du colis"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-end mt-2">
                <button type="button" class="btn btn-success add-colis" style="color: rgb(187, 90, 10)">Ajouter un autre colis</button>
                <button type="button" class="btn btn-danger remove-colis" style="display: none">Retirer ce colis</button>
            </div>
            <div id="colisContainer"></div>
            <div class="text-end mt-4 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary btn-prev" style="display: none;">Précédent</button>
                <button type="button" class="btn btn-primary btn-next">Suivant</button>
            </div>
        </fieldset>

        <!-- Étape 5 : Informations sur le service et le recapitulatif -->
        <fieldset style="display: none;">
            <h5 class="text-center mb-4 mt-5">Informations sur Le services et Récapitulatif</h5>
            <div class="form-section">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Service(s)</label>
                        <div class="input-group">
                            <input type="text" name="service[]" class="form-control produit-input">
                            <button type="button" class="btn btn-success btn-add" data-bs-toggle="modal" data-bs-target="#produitModal">+</button>
                        </div>
                        <div class="autocomplete-results" style="position: absolute; z-index: 1000; background-color: white; border: 1px solid #ccc; width: 100%; display: none;"></div>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Prix</label>
                        <input type="number" name="prix[]" class="form-control prix-colis" placeholder="Prix">
                        <div class="mt-2">Prix Total: <span class="prix-total">0</span></div>
                    </div>
                </div>

                <h5 class="text-center mb-4 mt-5">Récapitulatif de votre envoi</h5><br><br>
                <div class="recapitulatif-section">
                    <div class="row">
                        <div class="col-md-4">
                            <h6 ><strong>EXPEDITEUR:</strong></h6><br>
                            <p><strong>Nom :</strong> <span id="recap_nom_expediteur"></span></p>
                            <p><strong>Téléphone :</strong> <span id="recap_tel_expediteur"></span></p>
                            <p><strong>Contact Agence :</strong> <span id="recap_contact_agence_expediteur"></span></p>
                        </div>
                        <div class="col-md-4">
                            <h6><strong>DESCTINATAIRE :</strong></h6><br>
                            <p><strong>Nom :</strong> <span id="recap_nom_destinataire"></span></p>
                            <p><strong>Téléphone :</strong> <span id="recap_tel_destinataire"></span></p>
                            <p><strong>Contact Agence :</strong> <span id="recap_contact_agence_destinataire"></span></p>
                        </div>
                        <div class="col-md-4">
                            <h6><strong>INFO COLIS :</strong></h6><br>
                             <p><strong>Nombre caterorie de colis :</strong> <span id="recap_nombre_colis">0</span></p>
                            <p><strong>Total à payer :</strong> <span id="recap_total_a_payer">0</span> <span id="recap_devise"></span></p>
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-3">
                        <div class="col-md-6">
                        </div>
                        <div class="col-md-6">
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary btn-prev" style="display: none;">Précédent</button>
                    <button type="button" class="btn btn-primary btn-next">Suivant</button>
                </div>
            </div>
        </fieldset>
                        <!-- Étape 6 : Informations du payement -->
        <fieldset style="display: none;">
            <h5 class="text-center mb-4 mt-5">Informations du paiement</h5>
            <div class="form-section">
                <div class="row">
                    <div class="col-md-6">
                    <div class="mb-3">
                        <label for="mode_payement" class="form-label">Sélectionnez le mode de paiement</label>
                        <select name="mode_payement" id="mode_payement" class="form-control" required>
                            <option value="" disabled selected>-- Sélectionnez le mode de paiement --</option>
                            <option value="bank">Virement Banquaire</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="cheque">Chèque</option>
                            <option value="cash">Espèces</option>
                            <option value="delivery">Paiement à la livraison</option>
                        </select>
                    </div>
                </div>
                </div>
            </div>
             <div class="text-end mt-4 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary btn-prev" style="display: none;">Précédent</button>
                <button type="submit" class="btn btn-success" style="display: none;">Valider</button>
            </div>
        </fieldset>
</form>
    {{-- Logique j'ajout de produit --}}
<div class="modal fade" id="produitModal" tabindex="-1" aria-labelledby="produitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="produitModalLabel">Ajouter un Produit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="produitForm">
                    <div class="mb-3">
                        <label for="description_produit" class="form-label">Description</label>
                        <input type="text" name="description" id="description_produit" class="form-control" id_reference>
                    </div>
                    <div class="mb-3">
                        <label for="categorie_produit" class="form-label">Catégorie</label>
                        <select name="categorie" id="categorie_produit" class="form-control" id_reference>
                            <option value="" disabled selected>-- Sélectionnez une catégorie --</option>
                            <option value="Colis">COLIS</option>
                            <option value="Service">SERVICES</option>
                            <option value="Remise">REMISES</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="prix_unitaire" class="form-label">Prix Unitaire</label>
                        <input type="number" name="prix" id="prix_unitaire" class="form-control" min="0" id_reference>
                    </div>
                    <div class="mb-3">
                        <label for="agence" class="form-label">Agence de destination</label>
                        <select name="agence" id="agence" class="form-control">
                            <option value="" disabled selected>-- Sélectionnez l'agence de destination --</option>
                            <option value="IPMS-SIMEX-CI Angre 8ème Tranche">DS Translog Angré 8ème Tranche</option>
                            <option value="AFT Agence Louis Bleriot">AFT Agence Louis Bleriot</option>
                            <option value="Agence de Chine">Agence de Chine</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary btn-save" data-url="{{ route('colis.store.produit') }}">Créer</button>
            </div>
        </div>
    </div>
</div>
</section>


<script>
$(document).ready(function() {

    // =================================================================
    // 1. GESTION DU FORMULAIRE MULTI-ÉTAPES
    // =================================================================
    let currentStep = 0;
    const fieldsets = $("fieldset");
    const steps = $(".step");

    function showStep(stepIndex) {
        fieldsets.hide().eq(stepIndex).show();
        steps.removeClass("active").eq(stepIndex).addClass("active");
        
        // Mettre à jour la barre de progression
        steps.each(function(index) {
            $(this).toggleClass("active", index <= stepIndex);
        });

        // Gérer la visibilité des boutons
        $(".btn-prev").toggle(stepIndex > 0);
        const isLastStep = stepIndex === fieldsets.length - 1;
        $(".btn-next").toggle(!isLastStep);
        $(".btn-submit").toggle(isLastStep);
    }

    $(".btn-next").click(function() {
        // Avant de passer à l'étape suivante, on met à jour le récapitulatif
        // si on est sur le point de quitter l'étape des colis (étape 3, index 3)
        if (currentStep === 3) {
            updateRecapitulatif();
        }
        if (currentStep < fieldsets.length - 1) {
            currentStep++;
            showStep(currentStep);
        }
    });

    $(".btn-prev").click(function() {
        if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
        }
    });
    
    steps.click(function() {
        const stepIndex = $(this).data("step");
        // On ne permet de naviguer que vers les étapes déjà visitées
        if (stepIndex <= currentStep) {
            currentStep = stepIndex;
            showStep(currentStep);
        }
    });

    // Afficher la première étape au chargement
    showStep(currentStep);

    // =================================================================
    // 2. LOGIQUE MÉTIER ET CHAMPS DYNAMIQUES
    // =================================================================
    const agenceOptionsByMode = {
        maritime: { value: "IPMS-SIMEX-CI", label: "DS Translog Carrefour Angré" },
        aerien: { value: "IPMS-SIMEX-CI Angre 8ème Tranche", label: "DS Translog Angré 8ème Tranche" }
    };

    function updateDynamicFields() {
        const mode = $("#mode_transit").val();
        
        // Affiche/masque les champs de référence et de dimensions/poids
        $("#ref_maritime, .dimension-section").toggle(mode === "maritime");
        $("#ref_aerien, .poids-section").toggle(mode === "aerien");

        // Met à jour les agences de destination
        const agenceDestSelect = $("#agence_destinataire");
        agenceDestSelect.html('<option value="" disabled selected>-- Sélectionnez --</option>');
        if (mode && agenceOptionsByMode[mode]) {
            const opt = agenceOptionsByMode[mode];
            agenceDestSelect.append(new Option(opt.label, opt.value, true, true));
        }

        // Met à jour la devise
        updateDeviseBasedOnAgence();
    }

    function updateDeviseBasedOnAgence() {
        const agence = $("#agence_expediteur").val();
        let devise = "EUR"; // Par défaut
        let disabled = false;

        if (agence === 'Agence de Chine') {
            devise = 'FCFA';
            disabled = true;
        } else if (agence === 'AFT Agence Louis Bleriot') {
            devise = 'EUR';
            disabled = true;
        }

        $(".devise-select").val(devise).prop('disabled', disabled).css('background-color', disabled ? '#e9ecef' : '');
    }

    function toggleClientSections() {
        const categorie = $("#categorie_client").val();
        const isSociete = categorie === 'societe';
        $("#societe_expediteur_section, #societe_destinataire_section").toggle(isSociete);
        $("#particulier_expediteur_section, #particulier_destinataire_section").toggle(!isSociete);
    }
    
    // Écouteurs d'événements
    $("#mode_transit, #agence_expediteur").on('change', updateDynamicFields);
    $("#categorie_client").on('change', toggleClientSections);
    
    // Initialisation
    updateDynamicFields();
    toggleClientSections();

    // =================================================================
    // 3. GESTION DES COLIS (AJOUT/SUPPRESSION/CALCULS)
    // =================================================================
    const colisContainer = $("#colis-container");

    function attachColisEventListeners(colisElement) {
        // Activer l'autocomplétion sur le nouveau champ produit
        initAutocomplete(colisElement.find(".produit-input"));

        // Mettre à jour le prix total lors de la modification de la quantité ou du prix
        colisElement.on('input', '.quantite-colis, .prix-colis', function() {
            updateTotalForColis($(this).closest('.colis-item'));
        });

        // Afficher les dimensions en format texte
        colisElement.on("input", ".hauteur, .largeur, .longueur", function () {
            const parent = $(this).closest(".dimension-section");
            const h = parent.find(".hauteur").val();
            const la = parent.find(".largeur").val();
            const lo = parent.find(".longueur").val();
            const resultDiv = parent.find(".dimension-result");
            resultDiv.text(h && la && lo ? `${lo}x${la}x${h} cm` : '').toggle(!!(h && la && lo));
        });
    }

    function updateTotalForColis(colisElement) {
        const quantite = parseFloat(colisElement.find(".quantite-colis").val()) || 0;
        const prixUnitaire = parseFloat(colisElement.find(".prix-colis").attr("data-prix-unitaire")) || parseFloat(colisElement.find(".prix-colis").val()) || 0;
        const prixTotal = quantite * prixUnitaire;
        colisElement.find(".prix-total").text(prixTotal.toFixed(2));
    }
    
    $(".add-colis").click(function() {
        const newColis = colisContainer.find(".colis-item:first").clone(true);
        newColis.find("input, textarea, select").val(""); // Vider les champs
        newColis.find(".quantite-colis").val("1");
        newColis.find(".prix-total").text("0");
        newColis.find(".dimension-result").hide();
        newColis.find(".remove-colis").show(); // Afficher le bouton de suppression
        colisContainer.append(newColis);
        
        // Ré-appliquer les règles de visibilité et devise
        updateDynamicFields(); 
    });

    colisContainer.on("click", ".remove-colis", function () {
        $(this).closest(".colis-item").remove();
    });

    // Attacher les écouteurs au premier colis au chargement
    attachColisEventListeners(colisContainer.find(".colis-item:first"));

    // =================================================================
    // 4. AUTOCOMPLÉTION DES PRODUITS
    // =================================================================
    function initAutocomplete(inputElement) {
        inputElement.on("keyup", function() {
            const query = $(this).val().trim();
            const input = $(this);
            const colisItem = input.closest('.colis-item');
            const resultsContainer = colisItem.find('.autocomplete-results');

            if (query.length < 2) {
                resultsContainer.empty().hide();
                return;
            }

            $.ajax({
                url: "{{ route('colis.recherche.auto') }}",
                type: "GET",
                dataType: "json",
                data: { query: query },
                success: function(data) {
                    resultsContainer.empty().show();
                    if (data.length > 0) {
                        $.each(data, function(index, produit) {
                            $('<div class="autocomplete-item"></div>')
                                .text(produit.description)
                                .on('click', function() {
                                    input.val(produit.description);
                                    const prixInput = colisItem.find('.prix-colis');
                                    const prixUnitaire = parseFloat(produit.prix);
                                    
                                    prixInput.val(prixUnitaire).attr('data-prix-unitaire', prixUnitaire);
                                    updateTotalForColis(colisItem); // Mettre à jour le total
                                    
                                    resultsContainer.empty().hide();
                                }).appendTo(resultsContainer);
                        });
                    } else {
                        resultsContainer.hide();
                    }
                },
                error: function() { resultsContainer.empty().hide(); }
            });
        });
    }

    // Fermer les suggestions en cliquant ailleurs
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.produit-input, .autocomplete-results').length) {
            $('.autocomplete-results').hide();
        }
    });

    // =================================================================
    // 5. GESTION DU MODAL PRODUIT
    // =================================================================
    let activeProduitInput = null;
    $(document).on("click", ".btn-add-produit", function() {
        activeProduitInput = $(this).siblings(".produit-input");
        $("#description_produit").val(activeProduitInput.val());
    });

    $(".btn-save-produit").on("click", function () {
        const form = $("#produitForm");
        const description = form.find("#description_produit").val().trim();
        const prix = parseFloat(form.find("#prix_unitaire").val());
        const data = {
            description: description,
            categorie: form.find("#categorie_produit").val(),
            agence: form.find("#agence").val(),
            prix: prix,
        };

        if (!data.description || !data.categorie || isNaN(prix) || prix <= 0) {
            alert("Veuillez remplir tous les champs correctement.");
            return;
        }

        const btn = $(this);
        btn.prop("disabled", true).text("Enregistrement...");

        $.ajax({
            url: btn.data("url"),
            type: "POST",
            data: JSON.stringify(data),
            contentType: "application/json",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                alert(response.message);
                if (activeProduitInput) {
                    const colisItem = activeProduitInput.closest('.colis-item');
                    activeProduitInput.val(data.description);
                    colisItem.find('.prix-colis').val(data.prix).attr('data-prix-unitaire', data.prix);
                    updateTotalForColis(colisItem);
                }
                $("#produitModal").modal("hide");
                form[0].reset();
            },
            error: function (xhr) {
                alert("Erreur lors de l'enregistrement: " + (xhr.responseJSON?.message || "Erreur serveur"));
            },
            complete: function() {
                btn.prop("disabled", false).text("Créer");
            }
        });
    });

    // =================================================================
    // 6. MISE À JOUR DU RÉCAPITULATIF
    // =================================================================
    function updateRecapitulatif() {
        const isSociete = $('#categorie_client').val() === 'societe';
        
        // Expéditeur
        const nomExp = isSociete ? $('#nom_societe_expediteur').val() : `${$('#nom_expediteur').val()} ${$('#prenom_expediteur').val()}`;
        const telExp = isSociete ? $('#tel_expediteur_societe').val() : $('#tel_expediteur').val();
        $('#recap_nom_expediteur').text(nomExp.trim() || 'N/A');
        $('#recap_tel_expediteur').text(telExp || 'N/A');
        $('#recap_agence_expediteur').text($('#agence_expediteur option:selected').text() || 'N/A');

        // Destinataire
        const nomDest = isSociete ? $('#nom_societe_destinataire').val() : `${$('#nom_destinataire').val()} ${$('#prenom_destinataire').val()}`;
        const codePays = isSociete ? $('select[name="country_code_societe"]').val() : $('select[name="country_code_particulier"]').val();
        const telDestNum = isSociete ? $('input[name="tel_destinataire_societe"]').val() : $('input[name="tel_destinataire"]').val();
        $('#recap_nom_destinataire').text(nomDest.trim() || 'N/A');
        $('#recap_tel_destinataire').text(telDestNum ? `${codePays} ${telDestNum}` : 'N/A');
        $('#recap_agence_destinataire').text($('#agence_destinataire option:selected').text() || 'N/A');

        // Colis & Prix
        const nombreColis = colisContainer.find('.colis-item').length;
        let totalAPayer = 0;
        $('.colis-item').each(function() {
            const quantite = parseFloat($(this).find(".quantite-colis").val()) || 0;
            const prixUnitaire = parseFloat($(this).find(".prix-colis").attr("data-prix-unitaire")) || parseFloat($(this).find(".prix-colis").val()) || 0;
            totalAPayer += quantite * prixUnitaire;
        });

        $('#recap_nombre_colis').text(nombreColis);
        $('#recap_total_a_payer').text(totalAPayer.toFixed(2));
        $('#recap_devise').text($('.devise-select:first').val() || 'EUR');
    }
});
</script>
<script>



        // Fonction pour ajouter un nouveau colis avec gestion de la devise et auto-incrément
        function addNewColis() {
            // On cache tous les boutons "Ajouter" existants
            $('.add-colis').hide();

            const colisCount = document.querySelectorAll('#colisContainer .colis-fieldset').length + 1;
            const idReference = 'COLIS-' + colisCount;

            // Utilisation du contenu HTML du template pour créer le nouvel élément
            const newColisHtml = $('#colisTemplate .form-section').parent().html().replace(/_1/g, `_${colisCount}`).replace('COLIS-1', idReference);
            const newColis = $(`<div class="colis-fieldset mb-4">${newColisHtml}</div>`);

            // Afficher le bouton "Retirer" pour ce nouveau colis
            newColis.find('.remove-colis').show();

            $("#colisContainer").append(newColis);
            
            // Appliquer la configuration de devise au nouveau colis ajouté
            const agenceSocieteSelect = document.getElementById('agence_societe_expediteur');
            const agenceParticulierSelect = document.getElementById('agence_particulier_expediteur');
            
            let agenceValue = '';
            if (agenceSocieteSelect && agenceSocieteSelect.value) {
                agenceValue = agenceSocieteSelect.value;
            } else if (agenceParticulierSelect && agenceParticulierSelect.value) {
                agenceValue = agenceParticulierSelect.value;
            }
            
            if (agenceValue) {
                const deviseSelect = newColis.find('.devise-select')[0];
                if (agenceValue === 'Agence de Chine') {
                    deviseSelect.value = 'FCFA';
                    deviseSelect.disabled = true;
                    deviseSelect.style.backgroundColor = '#e9ecef';
                } else if (agenceValue === 'AFT Agence Louis Bleriot') {
                    deviseSelect.value = 'EUR';
                    deviseSelect.disabled = true;
                    deviseSelect.style.backgroundColor = '#e9ecef';
                }
            }
            
            // Initialiser l'autocomplétion sur le nouveau colis
            initAutocomplete(newColis);
            toggleFields();
            
            // Mettre à jour le récapitulatif après l'ajout du colis
            updateRecapitulatif();
        }

        $(document).ready(function() {
            // Initialisation de l'autocomplétion sur le champ de base (dans le template)
            initAutocomplete($('#colisTemplate'));

        function initAutocomplete(element) {
            // On cible le champ produit à l'intérieur de l'élément passé en paramètre
            $(element).find(".produit-input").off("keyup").on("keyup", function() {
                let query = $(this).val().trim();
                let input = $(this); // L'input sur lequel on tape

                // === LA CORRECTION EST ICI ===
                // On trouve le parent ".colis-fieldset" ou "#colisTemplate" le plus proche.
                // C'est notre conteneur de référence pour CE colis spécifique.
                let fieldset = input.closest('.colis-fieldset, #colisTemplate');

                // Maintenant, on cherche les autres éléments UNIQUEMENT à l'intérieur de ce fieldset
                let resultsContainer = fieldset.find('.autocomplete-results');
                let prixInput = fieldset.find('input[name="prix[]"]');
                let quantiteInput = fieldset.find('input[name="quantite_colis[]"]');
                let prixTotalDisplay = fieldset.find('.prix-total');
                // ============================

                if (query.length >= 2) {
                    $.ajax({
                        url: "{{ route('colis.recherche.auto') }}",
                        type: "GET",
                        dataType: "json",
                        data: { query: query },
                        success: function(data) {
                            resultsContainer.empty().show();
                            if (data.length > 0) {
                                $.each(data, function(index, produit) {
                                    let resultItem = $('<div class="autocomplete-item"></div>')
                                        .text(produit.description)
                                        .css({
                                            padding: "5px",
                                            cursor: "pointer",
                                            borderBottom: "1px solid #eee"
                                        })
                                        .on('click', function() {
                                            input.val(produit.description);
                                            resultsContainer.empty().hide();

                                            // Mise à jour du prix unitaire et du prix total
                                            let prixUnitaire = parseFloat(produit.prix);
                                            let quantite = parseInt(quantiteInput.val()) || 1;
                                            let prixTotal = prixUnitaire * quantite;

                                            prixInput.attr("data-prix-unitaire", prixUnitaire); // Stocker le prix unitaire
                                            prixInput.val(prixTotal);
                                            prixTotalDisplay.text(prixTotal);
                                        });

                                    resultsContainer.append(resultItem);
                                });
                            } else {
                                resultsContainer.hide();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Erreur AJAX:", error);
                            resultsContainer.empty().hide();
                        }
                    });
                } else {
                    resultsContainer.empty().hide();
                }
            });
        }

            // =================================================================
    // 4. AUTOCOMPLÉTION DES PRODUITS
    // =================================================================
    function initAutocomplete(inputElement) {
        inputElement.on("keyup", function() {
            const query = $(this).val().trim();
            const input = $(this);
            const colisItem = input.closest('.colis-item');
            const resultsContainer = colisItem.find('.autocomplete-results');

            if (query.length < 2) {
                resultsContainer.empty().hide();
                return;
            }

            $.ajax({
                url: "{{ route('colis.recherche.auto') }}",
                type: "GET",
                dataType: "json",
                data: { query: query },
                success: function(data) {
                    resultsContainer.empty().show();
                    if (data.length > 0) {
                        $.each(data, function(index, produit) {
                            $('<div class="autocomplete-item"></div>')
                                .text(produit.description)
                                .on('click', function() {
                                    input.val(produit.description);
                                    const prixInput = colisItem.find('.prix-colis');
                                    const prixUnitaire = parseFloat(produit.prix);
                                    
                                    prixInput.val(prixUnitaire).attr('data-prix-unitaire', prixUnitaire);
                                    updateTotalForColis(colisItem); // Mettre à jour le total
                                    
                                    resultsContainer.empty().hide();
                                }).appendTo(resultsContainer);
                        });
                    } else {
                        resultsContainer.hide();
                    }
                },
                error: function() { resultsContainer.empty().hide(); }
            });
        });
    }

    // Fermer les suggestions en cliquant ailleurs
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.produit-input, .autocomplete-results').length) {
            $('.autocomplete-results').hide();
        }
    });
            // Met à jour le prix total lors de la modification de la quantité
            $(document).on('input', '.quantite-colis', function() {
                let fieldset = $(this).closest('.colis-fieldset, #colisTemplate');
                let prixInput = fieldset.find('input[name="prix[]"]');
                let prixTotalDisplay = fieldset.find('.prix-total');
                let quantite = parseInt($(this).val()) || 0;
                let prixUnitaire = parseFloat(prixInput.attr("data-prix-unitaire")) || 0;

                let prixTotal = prixUnitaire * quantite;

                prixInput.val(prixTotal.toFixed(2));
                prixTotalDisplay.text(prixTotal.toFixed(2));
            });

            // Fermer les suggestions en cliquant en dehors
            $(document).on('click', function(event) {
                if (!$(event.target).closest('.input-group, .autocomplete-results').length) {
                    $('.autocomplete-results').hide();
                }
            });

            // Empêcher la soumission du formulaire avec "Enter" si l'autocomplétion est ouverte
            $(document).on('keydown', '.produit-input', function(event) {
                if (event.key === "Enter" && $('.autocomplete-results').is(':visible')) {
                    event.preventDefault();
                }
            });

            // CORRIGÉ : Ajouter un nouveau colis en appelant la fonction
            $(document).on("click", ".add-colis", function(e) {
                e.preventDefault();
                addNewColis();
            });
        
            // Supprimer un colis
            $(document).on("click", ".remove-colis", function () {
                $(this).closest(".colis-fieldset").remove();

                // Afficher le bouton "Ajouter" sur le nouveau dernier formulaire
                if ($("#colisContainer .colis-fieldset").length > 0) {
                    $("#colisContainer .colis-fieldset:last").find('.add-colis').show();
                } else {
                    // S'il ne reste plus de colis ajoutés, on affiche celui du template
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
                let parent = $(this).closest(".colis-fieldset, #colisTemplate");
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
            
            // Logique pour le modal (inchangée)
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            $(".btn-add").on("click", function () {
                let produit = $(this).siblings(".produit-input").val().trim();
                $("#description_produit").val(produit);
            });

            $(".btn-save").on("click", function () {
                let description = $("#description_produit").val().trim();
                let categorie = $("#categorie_produit").val();
                let agence = $("#agence").val();
                let prix = parseFloat($("#prix_unitaire").val().trim()) || 0;
                let url = $(this).data("url");

                if (!description || !categorie || isNaN(prix) || prix <= 0) {
                    alert("Veuillez remplir tous les champs correctement.");
                    return;
                }

                $(".btn-save").prop("disabled", true).text("Enregistrement...");

                $.ajax({
                    url: url,
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        description: description,
                        categorie: categorie,
                        agence: agence,
                        prix: prix,
                    }),
                    success: function (response) {
                        alert(response.message);
                        $("#produitForm")[0].reset();
                        $("#produitModal").modal("hide");
                        $(".btn-save").prop("disabled", false).text("Créer");

                        let activeInput = $(".produit-input:focus");
                        if (activeInput.length) {
                            activeInput.val(description);
                            let fieldset = activeInput.closest(".colis-fieldset, #colisTemplate");
                            let prixInput = fieldset.find('input[name="prix[]"]');
                            let quantiteInput = fieldset.find('input[name="quantite_colis[]"]');
                            let prixTotalDisplay = fieldset.find(".prix-total");

                            let quantite = parseInt(quantiteInput.val()) || 1;
                            let prixTotal = prix * quantite;

                            prixInput.attr("data-prix-unitaire", prix);
                            prixInput.val(prixTotal.toFixed(2));
                            prixTotalDisplay.text(prixTotal.toFixed(2));
                        }
                    },
                    error: function (xhr) {
                        let message = "Erreur lors de l'enregistrement du produit !\n";
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function (key, value) {
                                message += value + "\n";
                            });
                        }
                        alert(message);
                        $(".btn-save").prop("disabled", false).text("Créer");
                    },
                });
            });
        });



        // Fonction pour mettre à jour le récapitulatif avec auto-incrémentation
        function updateRecapitulatif() {
            // Récupérer les informations de l'expéditeur
            let nomExpediteur = '';
            let telExpediteur = '';
            let contactAgenceExpediteur = '';

            if (document.getElementById('categorie_client').value === 'particulier') {
                nomExpediteur = document.getElementById('nom_expediteur').value + ' ' + document.getElementById('prenom_expediteur').value;
                telExpediteur = document.getElementById('tel_expediteur').value;
                contactAgenceExpediteur = document.getElementById('agence_particulier_expediteur') ? document.getElementById('agence_particulier_expediteur').value : '';
            } else { // Société
                nomExpediteur = document.getElementById('nom_societe_expediteur').value;
                telExpediteur = document.getElementById('tel_expediteur_societe').value;
                contactAgenceExpediteur = document.getElementById('agence_societe_expediteur') ? document.getElementById('agence_societe_expediteur').value : '';
            }

            document.getElementById('recap_nom_expediteur').textContent = nomExpediteur || 'Non renseigné';
            document.getElementById('recap_tel_expediteur').textContent = telExpediteur || 'Non renseigné';
            document.getElementById('recap_contact_agence_expediteur').textContent = contactAgenceExpediteur || 'Non renseigné';

            // Récupérer les informations du destinataire
            let nomDestinataire = '';
            let telDestinataire = '';
            let contactAgenceDestinataire = '';

            if (document.getElementById('particulier_destinataire_section').style.display !== 'none') {
                nomDestinataire = document.getElementById('nom_destinataire').value + ' ' + document.getElementById('prenom_destinataire').value;
                telDestinataire = document.getElementById('country_code_particulier').value + ' ' + document.getElementById('tel_destinataire').value;
                contactAgenceDestinataire = document.getElementById('agence_particulier_destinataire_societe') ? document.getElementById('agence_particulier_destinataire_societe').value : '';
            } else if (document.getElementById('societe_destinataire_section').style.display !== 'none') {
                nomDestinataire = document.getElementById('nom_societe_destinataire').value;
                telDestinataire = document.getElementById('country_code_societe').value + ' ' + document.getElementById('tel_destinataire_societe').value;
                contactAgenceDestinataire = document.getElementById('agence_particulier_destinataire_societe') ? document.getElementById('agence_particulier_destinataire_societe').value : '';
            }

            document.getElementById('recap_nom_destinataire').textContent = nomDestinataire || 'Non renseigné';
            document.getElementById('recap_tel_destinataire').textContent = telDestinataire || 'Non renseigné';
            document.getElementById('recap_contact_agence_destinataire').textContent = contactAgenceDestinataire || 'Non renseigné';

            // Calculer le nombre total de colis avec auto-incrément
            let totalColis = 0;
            const colisSections = document.querySelectorAll('#colisContainer .colis-fieldset, #colisTemplate .form-section');
            
            // Appliquer l'auto-incrément pour chaque colis
            colisSections.forEach((section, index) => {
                totalColis++;
                const idReference = 'COLIS-' + (index + 1);
                
                // Trouver l'input quantité dans cette section et lui attribuer un ID unique
                const quantiteInput = section.querySelector('.quantite-colis');
                if (quantiteInput) {
                    quantiteInput.setAttribute('id_reference', idReference);
                    
                    // Mettre à jour l'affichage de la référence
                    let referenceDisplay = section.querySelector('.form-text.text-muted');
                    if (!referenceDisplay) {
                        referenceDisplay = document.createElement('small');
                        referenceDisplay.className = 'form-text text-muted';
                        quantiteInput.parentNode.appendChild(referenceDisplay);
                    }
                    referenceDisplay.textContent = 'Référence: ' + idReference;
                }
            });

            document.getElementById('recap_nombre_colis').textContent = totalColis;

            // Calculer le total à payer pour tous les colis
            let totalAPayerColis = 0;
            document.querySelectorAll('.prix-colis').forEach(input => {
                if (input.value && !isNaN(parseFloat(input.value))) {
                    totalAPayerColis += parseFloat(input.value);
                }
            });

            // Récupérer la devise (prendre celle du premier colis disponible)
            let devise = 'EUR';
            const firstDeviseSelect = document.querySelector('select[name="devise"]');
            if (firstDeviseSelect && firstDeviseSelect.value) {
                devise = firstDeviseSelect.value;
            }

            const totalGeneral = totalAPayerColis;
            
            document.getElementById('recap_total_a_payer').textContent = totalGeneral.toFixed(2);
            document.getElementById('recap_devise').textContent = devise;
        }


        // Initialisation au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            // Initialiser l'auto-incrémentation sur le template de colis
            
            // Écouter les changements sur les champs importants
            const fieldsToWatch = [
                'categorie_client', 'nom_expediteur', 'prenom_expediteur', 'tel_expediteur',
                'nom_societe_expediteur', 'tel_expediteur_societe', 'nom_destinataire',
                'prenom_destinataire', 'tel_destinataire', 'nom_societe_destinataire',
                'tel_destinataire_societe', 'agence_societe_expediteur', 'agence_particulier_expediteur',
                'agence_particulier_destinataire_societe'
            ];

            fieldsToWatch.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.addEventListener('input', updateRecapitulatif);
                    field.addEventListener('change', updateRecapitulatif);
                }
            });

            // Écouter les changements de prix
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('prix-colis')) {
                    updateRecapitulatif();
                }
            });

            // Mettre à jour le récapitulatif au chargement initial
            updateRecapitulatif();
        });

        // Ajouter un événement pour mettre à jour le récapitulatif lors des changements
        document.addEventListener('DOMContentLoaded', function() {
            // Écouter les changements sur les champs importants
            const fieldsToWatch = [
                'categorie_client', 'nom_expediteur', 'prenom_expediteur', 'tel_expediteur',
                'nom_societe_expediteur', 'tel_expediteur_societe', 'nom_destinataire',
                'prenom_destinataire', 'tel_destinataire', 'nom_societe_destinataire',
                'tel_destinataire_societe', 'agence_societe_expediteur', 'agence_particulier_expediteur',
                'agence_particulier_destinataire_societe'
            ];

            fieldsToWatch.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.addEventListener('input', updateRecapitulatif);
                    field.addEventListener('change', updateRecapitulatif);
                }
            });

            // Écouter les changements de prix
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('prix-colis')) {
                    updateRecapitulatif();
                }
            });

            // Mettre à jour le récapitulatif au chargement initial
            updateRecapitulatif();
        });


        // Appelez cette fonction lorsque l'utilisateur passe à l'étape 5.
        // Par exemple, dans votre gestionnaire de clic pour le bouton "Suivant" :
        // if (currentStep === 4) { // Si on passe de l'étape 4 à l'étape 5
        //     updateRecapitulatif();
        // }
        function handleDeviseSelection() {
            // Récupérer les sélecteurs d'agence (expéditeur)
            const agenceSocieteSelect = document.getElementById('agence_societe_expediteur');
            const agenceParticulierSelect = document.getElementById('agence_particulier_expediteur');
            
            // Fonction pour mettre à jour la devise en fonction de l'agence sélectionnée
            function updateDevise(agenceValue) {
                // Trouver tous les sélecteurs de devise dans le formulaire
                const deviseSelects = document.querySelectorAll('select[name="devise"]');
                
                deviseSelects.forEach(deviseSelect => {
                    if (agenceValue === 'Agence de Chine') {
                        // Forcer la sélection du FCFA et désactiver le champ
                        deviseSelect.value = 'FCFA';
                        deviseSelect.disabled = true;
                        deviseSelect.style.backgroundColor = '#e9ecef'; // Griser le champ
                    } else if (agenceValue === 'AFT Agence Louis Bleriot') {
                        // Forcer la sélection de l'EUR et désactiver le champ
                        deviseSelect.value = 'EUR';
                        deviseSelect.disabled = true;
                        deviseSelect.style.backgroundColor = '#e9ecef'; // Griser le champ
                    } else {
                        // Réactiver le champ pour les autres agences
                        deviseSelect.disabled = false;
                        deviseSelect.style.backgroundColor = ''; // Retirer le gris
                    }
                });
            }
            
            // Écouter les changements sur le sélecteur d'agence société
            if (agenceSocieteSelect) {
                agenceSocieteSelect.addEventListener('change', function() {
                    updateDevise(this.value);
                });
                
                // Initialiser au chargement si une valeur est déjà sélectionnée
                if (agenceSocieteSelect.value) {
                    updateDevise(agenceSocieteSelect.value);
                }
            }
            
            // Écouter les changements sur le sélecteur d'agence particulier
            if (agenceParticulierSelect) {
                agenceParticulierSelect.addEventListener('change', function() {
                    updateDevise(this.value);
                });
                
                // Initialiser au chargement si une valeur est déjà sélectionnée
                if (agenceParticulierSelect.value) {
                    updateDevise(agenceParticulierSelect.value);
                }
            }
        }

        // Appeler la fonction au chargement du document
        document.addEventListener('DOMContentLoaded', function() {
            handleDeviseSelection();
        });


        // Modifier l'écouteur d'événement pour utiliser la nouvelle fonction
        $(document).on("click", ".add-colis", function(e) {
            e.preventDefault();
            $(this).hide();
            addNewColis();
        });
        //devise selon agence
        document.addEventListener('DOMContentLoaded', function() {
            // Fonction pour gérer la sélection de devise selon l'agence
            function handleDeviseSelection() {
                // Récupérer les sélecteurs d'agence (expéditeur)
                const agenceSocieteSelect = document.getElementById('agence_societe_expediteur');
                const agenceParticulierSelect = document.getElementById('agence_particulier_expediteur');
                const deviseSelect = document.getElementById('devise');
                
                // Fonction pour mettre à jour la devise en fonction de l'agence sélectionnée
                function updateDevise(agenceValue) {
                    if (agenceValue === 'Agence de Chine') {
                        // Forcer la sélection du FCFA et désactiver le champ
                        deviseSelect.value = 'FCFA';
                        deviseSelect.disabled = true;
                        deviseSelect.style.backgroundColor = '#e9ecef'; // Griser le champ
                    } else if (agenceValue === 'AFT Agence Louis Bleriot') {
                        // Forcer la sélection de l'EUR et désactiver le champ
                        deviseSelect.value = 'EUR';
                        deviseSelect.disabled = true;
                        deviseSelect.style.backgroundColor = '#e9ecef'; // Griser le champ
                    } else {
                        // Réactiver le champ pour les autres agences
                        deviseSelect.disabled = false;
                        deviseSelect.style.backgroundColor = ''; // Retirer le gris
                    }
                }
                
                // Écouter les changements sur le sélecteur d'agence société
                if (agenceSocieteSelect) {
                    agenceSocieteSelect.addEventListener('change', function() {
                        updateDevise(this.value);
                    });
                    
                    // Initialiser au chargement si une valeur est déjà sélectionnée
                    if (agenceSocieteSelect.value) {
                        updateDevise(agenceSocieteSelect.value);
                    }
                }
                
                // Écouter les changements sur le sélecteur d'agence particulier
                if (agenceParticulierSelect) {
                    agenceParticulierSelect.addEventListener('change', function() {
                        updateDevise(this.value);
                    });
                    
                    // Initialiser au chargement si une valeur est déjà sélectionnée
                    if (agenceParticulierSelect.value) {
                        updateDevise(agenceParticulierSelect.value);
                    }
                }
            }
            
            // Appeler la fonction
            handleDeviseSelection();
        });
            





        document.addEventListener('DOMContentLoaded', function() {

            // --- Fonctions utilitaires pour récupérer et mettre à jour les champs ---

            /**
            * Récupère un élément du DOM de manière sécurisée.
            * @param {string} id L'ID de l'élément.
            * @returns {HTMLElement|null} L'élément trouvé ou null.
            */
            function getElement(id) {
                return document.getElementById(id);
            }

            /**
            * Met à jour la valeur d'un champ s'il existe.
            * @param {string} id L'ID du champ.
            * @param {any} value La valeur à définir.
            */
            function updateFieldValue(id, value) {
                const element = getElement(id);
                if (element) {
                    element.value = value || ''; // Définit la valeur, ou une chaîne vide si null/undefined
                }
            }

            /**
            * Sélectionne une option dans un <select> s'il existe et si l'option est présente.
            * @param {string} selector Le sélecteur CSS du <select>.
            * @param {string} value La valeur de l'option à sélectionner.
            */
            function setSelectedOption(selector, value) {
                const selectElement = document.querySelector(selector);
                if (selectElement && value) {
                    const optionExists = Array.from(selectElement.options).some(option => option.value === value);
                    if (optionExists) {
                        selectElement.value = value;
                    }
                }
            }

            /**
            * Gère l'affichage des sections Particulier/Société.
            * @param {HTMLElement} radioParticulier Le bouton radio "Particulier".
            * @param {HTMLElement} radioSociete Le bouton radio "Société".
            * @param {HTMLElement} sectionParticulier La section "Particulier".
            * @param {HTMLElement} sectionSociete La section "Société".
            */
            function setupTypeSwitcher(radioParticulier, radioSociete, sectionParticulier, sectionSociete) {
                if (!radioParticulier || !radioSociete || !sectionParticulier || !sectionSociete) {
                    console.warn('Certains éléments pour le switcher Particulier/Société sont manquants.');
                    return;
                }

                const toggleSections = () => {
                    if (radioParticulier.checked) {
                        sectionParticulier.style.display = 'block';
                        sectionSociete.style.display = 'none';
                    } else if (radioSociete.checked) {
                        sectionParticulier.style.display = 'none';
                        sectionSociete.style.display = 'block';
                    } else {
                        // Par défaut, masquer les deux ou montrer l'un si aucun n'est coché initialement
                        sectionParticulier.style.display = 'none';
                        sectionSociete.style.display = 'none';
                    }
                };

                // Initialisation
                toggleSections();

                // Écouteurs d'événements
                radioParticulier.addEventListener('change', toggleSections);
                radioSociete.addEventListener('change', toggleSections);
            }


            // --- Fonctions de récupération et de remplissage pour l'EXPÉDITEUR ---

            const nomExpediteurInput = getElement('nom_expediteur');
            const prenomExpediteurInput = getElement('prenom_expediteur');
            const nomSocieteExpediteurInput = getElement('nom_societe_expediteur'); // Assurez-vous d'avoir cet ID

            async function fetchExpediteurData() {
                let nom = '';
                let prenom = '';
                let type = ''; // 'particulier' ou 'societe'

                const radioParticulierExpediteur = getElement('type_expediteur_particulier');
                const radioSocieteExpediteur = getElement('type_expediteur_societe');

                if (radioParticulierExpediteur && radioParticulierExpediteur.checked) {
                    nom = nomExpediteurInput ? nomExpediteurInput.value.trim() : '';
                    prenom = prenomExpediteurInput ? prenomExpediteurInput.value.trim() : '';
                    type = 'particulier';
                } else if (radioSocieteExpediteur && radioSocieteExpediteur.checked) {
                    nom = nomSocieteExpediteurInput ? nomSocieteExpediteurInput.value.trim() : ''; // Le nom de la société
                    // Pas de prénom pour une société, mais on peut passer le nom comme paramètre unique
                    type = 'societe';
                } else {
                    console.log('Aucun type d\'expéditeur sélectionné.');
                    return;
                }

                if ((type === 'particulier' && (nom.length === 0 || prenom.length === 0)) ||
                    (type === 'societe' && nom.length === 0)) {
                    return; // Ne pas faire d'appel si les champs requis sont vides
                }

                console.log(`Recherche de l'expéditeur (${type}): ${nom} ${type === 'particulier' ? prenom : ''}`);

                try {
                    const endpoint = type === 'particulier' ? `/api/expediteur?nom=${nom}&prenom=${prenom}` : `/api/expediteur-societe?nom_societe=${nom}`;
                    const response = await fetch(endpoint);
                    if (!response.ok) {
                        if (response.status === 404) {
                            console.log(`Aucun expéditeur (${type}) trouvé avec les informations fournies.`);
                        } else {
                            throw new Error(`Erreur HTTP: ${response.status}`);
                        }
                        // Nettoyer les champs si aucun expéditeur n'est trouvé
                        clearExpediteurFields(type);
                        return;
                    }
                    const data = await response.json();

                    if (data && (data.nom || data.nom_societe)) { // Vérifie si des données valides sont retournées
                        console.log('Expéditeur trouvé:', data);

                        if (type === 'particulier') {
                            updateFieldValue('email_expediteur', data.email);
                            setSelectedOption('#particulier_expediteur_section select[name="country_code_expediteur"]', data.country_code);
                            updateFieldValue('tel_expediteur', data.telephone);
                            updateFieldValue('adresse_expediteur', data.adresse); // ID du champ d'adresse direct
                            setSelectedOption('#agence_particulier_expediteur', data.agence);
                        } else { // type === 'societe'
                            updateFieldValue('email_societe_expediteur', data.email); // ID du champ email pour société
                            setSelectedOption('#societe_expediteur_section select[name="country_code_societe_expediteur"]', data.country_code); // ID du select indicatif pour société
                            updateFieldValue('tel_societe_expediteur', data.telephone); // ID du champ tel pour société
                            updateFieldValue('adresse_societe_expediteur', data.adresse); // ID du champ d'adresse pour société
                            updateFieldValue('numero_siret_expediteur', data.siret); // Assurez-vous d'avoir cet ID
                            setSelectedOption('#agence_societe_expediteur', data.agence); // ID du select agence pour société
                        }
                    } else {
                        console.log(`Aucun expéditeur (${type}) trouvé avec les informations fournies.`);
                        clearExpediteurFields(type);
                    }
                } catch (error) {
                    console.error('Erreur lors de la récupération de l\'expéditeur:', error);
                    clearExpediteurFields(type);
                }
            }

            function clearExpediteurFields(type) {
                if (type === 'particulier') {
                    updateFieldValue('email_expediteur', '');
                    setSelectedOption('#particulier_expediteur_section select[name="country_code_expediteur"]', '');
                    updateFieldValue('tel_expediteur', '');
                    updateFieldValue('adresse_expediteur', '');
                    setSelectedOption('#agence_particulier_expediteur', '');
                } else { // type === 'societe'
                    updateFieldValue('email_societe_expediteur', '');
                    setSelectedOption('#societe_expediteur_section select[name="country_code_societe_expediteur"]', '');
                    updateFieldValue('tel_societe_expediteur', '');
                    updateFieldValue('adresse_societe_expediteur', '');
                    updateFieldValue('numero_siret_expediteur', '');
                    setSelectedOption('#agence_societe_expediteur', '');
                }
            }

            // --- Logique pour les sélecteurs de type (Particulier/Société) ---

            // Expéditeur
            setupTypeSwitcher(
                getElement('type_expediteur_particulier'),
                getElement('type_expediteur_societe'),
                getElement('particulier_expediteur_section'),
                getElement('societe_expediteur_section')
            );

            // Destinataire
            setupTypeSwitcher(
                getElement('type_destinataire_particulier'),
                getElement('type_destinataire_societe'),
                getElement('particulier_destinataire_section'),
                getElement('societe_destinataire_section')
            );

            // Déclenche la recherche lors du changement de type pour pré-remplir si l'utilisateur a déjà tapé
            const radioExpediteurParticulier = getElement('type_expediteur_particulier');
            const radioExpediteurSociete = getElement('type_expediteur_societe');
            if (radioExpediteurParticulier) radioExpediteurParticulier.addEventListener('change', fetchExpediteurData);
            if (radioExpediteurSociete) radioExpediteurSociete.addEventListener('change', fetchExpediteurData);

            const radioDestinataireParticulier = getElement('type_destinataire_particulier');
            const radioDestinataireSociete = getElement('type_destinataire_societe');
            if (radioDestinataireParticulier) radioDestinataireParticulier.addEventListener('change', fetchDestinataireData);
            if (radioDestinataireSociete) radioDestinataireSociataire.addEventListener('change', fetchDestinataireData);

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

            // Options agences selon mode de transit
            const agenceOptionsTransit = {
                maritime: { value: "IPMS-SIMEX-CI", label: "DS Translog Carrefour Angré" },
                aerien: { value: "IPMS-SIMEX-CI Angre 8ème Tranche", label: "DS Translog Angré 8ème Tranche" }
            };

            // Affichage des champs référence selon mode
            function toggleReferenceFields(mode) {
                refMaritime.style.display = mode === 'maritime' ? 'block' : 'none';
                refAerien.style.display = mode === 'aerien' ? 'block' : 'none';
            }

            // Récupération référence via fetch AJAX
            function fetchReference(mode) {
                fetch(`/admin/colis/generer-reference/${mode}`)
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


            


        $(document).ready(function () {
            // Fonction pour afficher les champs en fonction du mode de transport sélectionné
            function toggleFields() {
                let mode = $("#mode_transit").val();
                $(".dimension-section").toggle(mode === "maritime");
                $(".poids-section").toggle(mode === "aerien");
            }

            // Appliquer les changements lors de la sélection du mode de transit
            $("#mode_transit").change(function () {
                toggleFields();
            });
            // Initialiser les champs visibles selon le mode de transport sélectionné
            toggleFields();
        });


        $(document).ready(function () {
            let currentStep = 0;
            const fieldsets = $("fieldset");

            // Fonction pour afficher une étape spécifique
            function showStep(step) {
                fieldsets.hide().eq(step).show();
                toggleButtons(step);
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

            // Gestion des formulaires multi-étapes avec jQuery
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

    .autocomplete-results {
        position: absolute; /* Important pour le positionnement */
        top: 100%; /* Affiche les résultats sous l'input */
        left: 0;
        right: 0;
        z-index: 1000; /* Pour être au-dessus des autres éléments */
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 5px;
        display: none; /* Caché par défaut */
    }

    .autocomplete-item {
        padding: 5px 10px;
        cursor: pointer;
    }

    .autocomplete-item:hover {
        background-color: #f0f0f0;
    }

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