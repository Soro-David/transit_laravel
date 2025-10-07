@extends('AGENCE_CHINE.layouts.agent')

@section('content-header')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<section class="p-4 mx-auto">

    <form action="{{ route('chine_colis.store.colis') }}" method="post" class="form-container">
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

        {{-- Barre de progression --}}
        <div class="progress-bar-container mb-4">
            <ul class="progress-steps">
                <li class="step active" data-step="0"><span>Expédition</span></li>
                <li class="step" data-step="1"><span>Expéditeur</span></li>
                <li class="step" data-step="2"><span>Destinataire</span></li>
                <li class="step" data-step="3"><span>Colis</span></li>
                <li class="step" data-step="4"><span>Récapitulatif</span></li>
                <li class="step" data-step="5"><span>Paiement</span></li>
            </ul>
        </div>

        <!-- Étape 1 : Informations transport -->
        <fieldset id="transport-fieldset">
            <h5 class="text-center mb-4 mt-5">Informations sur le mode de transport</h5>
            <div class="form-section">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="mode_transit" class="form-label">Sélectionnez le mode de transit</label>
                        <select name="mode_transit" id="mode_transit" class="form-control">
                            <option value="" disabled selected>-- Sélectionnez le mode de transit --</option>
                            <option value="maritime">Maritime</option>
                            <option value="aerien">Aérien</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="agence_expediteur" class="form-label">Agence d'expédition</label>
                        <input type="text" name="agence_expediteur" id="agence_expediteur" class="form-control"
                            value="{{ $agencesExpedition->first()->nom_agence ?? '' }}" readonly>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="agence_destinataire" class="form-label">Agence de destination</label>
                        <select name="agence_destinataire" id="agence_destinataire" class="form-control"></select>
                    </div>

                    <div class="col-md-6" id="ref_maritime" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Référence (Maritime)</label>
                            <input type="text" name="reference_colis_maritime" class="form-control"
                                value="{{ $referenceColis_maritime['reference_colis'] ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="col-md-6" id="ref_aerien" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Référence (Aérien)</label>
                            <input type="text" name="reference_colis_aerien" class="form-control"
                                value="{{ $referenceColis_aerien['reference_colis'] ?? '' }}" readonly>
                        </div>
                    </div>


                </div>
            </div>
        </fieldset>

        <!-- Étape 2 : Informations de l'Expéditeur -->
        <fieldset style="display: none;">
            <h5 class="text-center mb-4 mt-5">Informations d'expédition</h5>
            <div class="form-section">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="categorie_client" class="form-label">Sélectionnez la catégorie de client</label>
                        <select name="categorie_client" id="categorie_client" class="form-control">
                            <option value="" disabled selected>-- Sélectionnez la catégorie --</option>
                            <option value="particulier">Particulier</option>
                            <option value="societe">Société</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3 position-relative">
                        <label for="client_select" class="form-label">Rechercher un client existant</label>
                        <input type="text" name="client_select" id="client_select" class="form-control" placeholder="Rechercher par nom, prénom, téléphone...">
                        <div id="client_autocomplete_results" class="autocomplete-results"></div>
                    </div>
                </div>

                {{-- ===== PARTICULIER EXPÉDITEUR ===== --}}
                <div id="particulier_expediteur_section" style="display: none;">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label for="nom_expediteur" class="form-label">Nom</label><input type="text" name="nom_expediteur" id="nom_expediteur" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label for="prenom_expediteur" class="form-label">Prénom</label><input type="text" name="prenom_expediteur" id="prenom_expediteur" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label for="email_expediteur" class="form-label">Email</label><input type="email" name="email_expediteur" id="email_expediteur" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label for="tel_expediteur" class="form-label">Téléphone</label><input type="text" name="tel_expediteur" id="tel_expediteur" class="form-control" placeholder="Ex: 0123456789"></div>
                    </div>
                </div>
                {{-- ===== SOCIÉTÉ EXPÉDITEUR ===== --}}
                <div id="societe_expediteur_section" style="display: none;">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label for="nom_societe_expediteur" class="form-label">Nom de la société</label><input type="text" name="nom_expediteur_societe" id="nom_societe_expediteur" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label for="email_societe_expediteur" class="form-label">Email</label><input type="email" name="email_expediteur_societe" id="email_societe_expediteur" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label for="tel_expediteur_societe" class="form-label">Téléphone</label><input type="text" name="tel_expediteur_societe" id="tel_expediteur_societe" class="form-control" placeholder="Ex: 0123456789"></div>
                        <div class="col-md-6 mb-3"><label for="adresse_expediteur_societe" class="form-label">Adresse</label><input type="text" name="adresse_expediteur_societe" id="adresse_expediteur_societe" class="form-control"></div>
                    </div>
                </div>
            </div>
        </fieldset>

        <!-- Étape 3 : Informations du Destinataire -->
        <fieldset style="display: none;">
            <h5 class="text-center mb-4 mt-5">Informations du destinataire</h5>
            <div class="form-section">
                {{-- ===== PARTICULIER DESTINATAIRE ===== --}}
                <div id="particulier_destinataire_section" style="display: none;">
                    <div class="row">
                        <div class="col-md-4 mb-3"><label for="nom_destinataire" class="form-label">Nom</label><input type="text" name="nom_destinataire" id="nom_destinataire" class="form-control"></div>
                        <div class="col-md-4 mb-3"><label for="prenom_destinataire" class="form-label">Prénom</label><input type="text" name="prenom_destinataire" id="prenom_destinataire" class="form-control"></div>
                        <div class="col-md-4 mb-3"><label for="email_destinataire" class="form-label">Email</label><input type="email" name="email_destinataire" id="email_destinataire" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label for="adresse_destinataire_particulier" class="form-label">Adresse de Livraison</label><select name="adresse_destinataire_particulier" class="form-control"><option value="">-- Sélectionnez une commune --</option><option value="Pas de livraison">Pas de Livraison</option><option value="Abobo">Abobo</option><option value="Adjamé">Adjamé</option><option value="Yopougon">Yopougon</option></select></div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Téléphone</label>
                            <div class="input-group"><select name="country_code_particulier" class="input-group-text"><option value="+33">FR (+33)</option><option value="+225">CI (+225)</option></select><input type="text" name="tel_destinataire" class="form-control" placeholder="Ex: 0123456789"></div>
                        </div>
                    </div>
                </div>
                {{-- ===== SOCIÉTÉ DESTINATAIRE ===== --}}
                <div id="societe_destinataire_section" style="display: none;">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label for="nom_societe_destinataire" class="form-label">Nom de la société</label><input type="text" name="nom_destinataire_societe" id="nom_societe_destinataire" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label for="email_societe_destinataire" class="form-label">Email</label><input type="email" name="email_destinataire_societe" id="email_societe_destinataire" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label for="adresse_destinataire_societe" class="form-label">Adresse de Livraison</label><select name="adresse_destinataire_societe" class="form-control"><option value="">-- Sélectionnez une commune --</option><option value="Pas de livraison">Pas de Livraison</option><option value="Abobo">Abobo</option><option value="Adjamé">Adjamé</option><option value="Yopougon">Yopougon</option></select></div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Téléphone</label>
                            <div class="input-group"><select name="country_code_societe" class="input-group-text"><option value="+33">FR (+33)</option><option value="+225">CI (+225)</option></select><input type="text" name="tel_destinataire_societe" class="form-control" placeholder="Ex: 0123456789"></div>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>

        <!-- Étape 4 : Informations Colis -->
        <fieldset id="colis-fieldset" style="display: none;">
            <h5 class="text-center mb-4 mt-5">Informations du/des Colis</h5>

            <div class="d-flex justify-content-center">
                <div class="colis-item card p-3 mb-4 shadow-sm border-0" style="width: 50%;">
                    <div class="col-md-12 mb-4">
                        <label for="devis_reference_autocomplete" class="form-label">
                            Récupérer les informations d'un programme existant
                        </label>
                        <input type="text" id="devis_reference_autocomplete" class="form-control" placeholder="Saisir une référence...">
                        <input type="hidden" id="selected_devis_id" name="selected_devis_id">
                    </div>
                </div>
            </div>
            <div id="colis-container">
                <!-- Premier colis -->
                <div class="colis-item form-section mb-4">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Quantité</label>
                                <input type="number" name="quantite_colis[]" class="form-control quantite-colis" value="1" min="1">
                            </div>
                        </div>

                        <div class="col-md-4 position-relative">
                            <label class="form-label">Produit(s)</label>
                            <div class="input-group">
                                <input type="text" name="produit[]" class="form-control produit-input" placeholder="Rechercher ou saisir un produit" required>
                                <button type="button" class="btn btn-success btn-add-produit" data-bs-toggle="modal" data-bs-target="#produitModal">+</button>
                            </div>
                            <div class="autocomplete-results"></div>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Prix/Kg</label>
                            <input type="number" name="prix[]" class="form-control prix-colis" placeholder="Prix">
                            <div class="mt-2">Prix Total: <span class="prix-total">0</span></div>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Type colis</label>
                            <select name="type_colis[]" class="form-control">
                                <option value="standard">Standard</option>
                                <option value="fragile">Fragile</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Devise</label>
                            <select name="devise[]" class="form-control devise-select" disabled style="background-color: #e9ecef;">
                                <option value="EUR">EUR</option>
                                <option value="FCFA">FCFA</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 dimension-section" style="display: none;">
                            <label class="form-label">Dimensions (cm)</label>
                            <div class="d-flex gap-2">
                                <input type="number" name="longueur[]" class="form-control" placeholder="Longeur">
                                <input type="number" name="largeur[]" class="form-control" placeholder="Largeur">
                                <input type="number" name="hauteur[]" class="form-control" placeholder="Hauteur">
                            </div>
                        </div>

                        <div class="col-md-6 poids-section" style="display: none;">
                            <label class="form-label">Poids (kg)</label>
                            <input type="number" name="poids[]" class="form-control poids-colis" placeholder="Poids">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Commentaire</label>
                            <textarea name="description_colis[]" class="form-control" rows="3" placeholder="Description du colis"></textarea>
                        </div>
                    </div>

                    <div class="text-end mt-2">
                        <button type="button" class="btn btn-danger remove-colis" style="display: none;">Retirer ce colis</button>
                    </div>
                </div>
            </div>

            <div class="text-end mt-2">
                <button type="button" class="btn btn-success add-colis">Ajouter un colis</button>
            </div>

            <!-- TEMPLATE pour colis lié à un devis programmé -->
            <div class="colis-item-template-devis" style="display:none;">
                <div class="colis-item form-section mb-4 border p-3">
                    <div class="row">
                        <div class="col-md-2"><label class="form-label">Quantité</label><input type="number" name="quantite_colis[]" class="form-control quantite-colis"></div>
                        <div class="col-md-4"><label class="form-label">Produit</label><input type="text" name="service[]" class="form-control produit-input"></div>
                        <div class="col-md-2"><label class="form-label">Valeur</label><input type="number" name="valeur_colis[]" class="form-control prix-colis"></div>
                        <div class="col-md-2">
                            <label class="form-label">Type colis</label>
                            <select name="type_colis[]" class="form-control"><option value="standard">Standard</option><option value="fragile">Fragile</option></select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Devise</label>
                            <select name="devise[]" class="form-control devise-select" disabled="" style="background-color: rgb(233, 236, 239);"><option value="EUR">EUR</option><option value="FCFA">FCFA</option></select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-5 dimension-section" style="">
                            <label class="form-label">Dimensions (cm)</label>
                            <div class="d-flex gap-2"><input type="number" name="longueur[]" class="form-control" placeholder="L"><input type="number" name="largeur[]" class="form-control" placeholder="l"><input type="number" name="hauteur[]" class="form-control" placeholder="H"></div>
                        </div>
                        <div class="col-md-5 poids-section" style="display: none;">
                            <label class="form-label">Poids (kg)</label><input type="number" name="poids[]" class="form-control poids-colis">
                        </div>
                        <div class="col-md-5 mt-2"><label class="form-label">Commentaire</label><textarea name="description_colis[]" class="form-control"></textarea></div>
                        <div class="col-md-2"><label class="form-label">Prix/Kg</label><input type="number" name="prix[]" class="form-control prix-colis" placeholder="Prix">
                            <div class="mt-2">Prix Total: <span class="prix-total">0.00</span></div>
                        </div>
                    </div>
                    <div class="text-end mt-2"><button type="button" class="btn btn-danger remove-colis">Retirer ce colis</button></div>
                </div>
            </div>
        </fieldset>

        <!-- Étape 5 : Services et Récapitulatif -->
        <fieldset style="display: none;">
            <h5 class="text-center mb-4 mt-5">Services Additionnels et Récapitulatif</h5>
            <div class="form-section">
                <div class="row g-3">
                    <div class="col-md-6 position-relative">
                        <label class="form-label">Service(s)</label>
                        <div class="input-group">
                            <input type="text" name="service[]" class="form-control service-input" placeholder="Rechercher ou saisir un service">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#ServiceModal">+</button>
                        </div>
                        <div class="autocomplete-results"></div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Prix</label>
                        <input type="number" name="prix_service[]" class="form-control prix-service" placeholder="Prix">
                        <div class="mt-2">Prix Total: <span class="prix-total-service">0</span></div>
                    </div>
                </div>
            </div>

            <h5 class="text-center mb-4 mt-5">Récapitulatif de votre envoi</h5>
            <div class="recapitulatif-section form-section">
                <h6>Informations sur l'envoi :</h6>
                <p><strong>Mode de Transit :</strong> <span id="recap_mode_transit"></span></p>
                <p><strong>Référence du Colis :</strong> <span id="recap_reference_colis"></span></p>
                <hr>
                <div class="row">
                    <div class="col-md-6"><h6>Expéditeur :</h6><p><strong>Nom :</strong> <span id="recap_nom_expediteur"></span></p><p><strong>Téléphone :</strong> <span id="recap_tel_expediteur"></span></p><p><strong>Agence :</strong> <span id="recap_agence_expediteur"></span></p></div>
                    <div class="col-md-6"><h6>Destinataire :</h6><p><strong>Nom :</strong> <span id="recap_nom_destinataire"></span></p><p><strong>Téléphone :</strong> <span id="recap_tel_destinataire"></span></p><p><strong>Agence :</strong> <span id="recap_agence_destinataire"></span></p></div>
                </div>
                <hr>
                <h6>Détails des Colis :</h6>
                <div id="recap_details_colis" class="mb-3"></div>
                <hr>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <p><strong>Total Colis :</strong> <span id="recap_total_colis">0.00</span> <span class="recap_devise_class"></span></p>
                        <p><strong>Total Services :</strong> <span id="recap_total_services">0.00</span> <span class="recap_devise_class"></span></p>
                    </div>
                    <div class="col-md-6 text-end">
                        <h5 style="font-weight: bold;">Total à Payer : <span id="recap_total_a_payer">0.00</span> <span id="recap_devise"></span></h5>
                    </div>
                </div>
            </div>
        </fieldset>

        <!-- Étape 6 : Paiement -->
        <fieldset id="payment_fieldset" style="display: none;">
            <h5 class="text-center mb-4 mt-5">Informations du paiement</h5>
            <div class="alert alert-info text-center">
                <strong>Total à payer :
                    <span id="payment_total" style="font-size: 1.5em; font-weight: bold;">0.00</span>
                    <span id="payment_devise" style="font-size: 1.5em; font-weight: bold;">EUR</span>
                </strong>
            </div>
            <div class="form-section">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="mode_payement" class="form-label">Sélectionnez le mode de paiement</label>
                        <select name="mode_payement" id="mode_payement" class="form-control">
                            <option value="" disabled selected>-- Sélectionnez --</option>
                            <option value="bank">Virement Bancaire</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="cheque">Chèque</option>
                            <option value="cash">Espèces</option>
                            <option value="delivery">Paiement à la livraison</option>
                        </select>
                    </div>
                </div>
                {{-- Sections de paiement --}}
                <div class="payment-section mt-3" id="bank_section" style="display:none;">
                    <h5>Détails Bancaires</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3"><label class="form-label">Nom de la banque</label><input type="text" name="bank_nom_banque" class="form-control"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Numéro de compte</label><input type="text" name="bank_numero_compte" class="form-control"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Montant</label><input type="text" name="bank_montant" class="form-control"></div>
                    </div>
                </div>
                <div class="payment-section mt-3" id="mobile_money_section" style="display:none;">
                    <h5>Paiement Mobile Money</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Opérateur</label>
                            <select name="mobile_operateur" id="mobile_operateur" class="form-control">
                                <option value="">-- Sélectionnez --</option>
                                <option value="orange_money">Orange Money</option>
                                <option value="wave">Wave</option>
                                <option value="mtn_money">MTN Money</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3"><label class="form-label">Numéro de téléphone</label><input type="text" name="mobile_numero_tel" class="form-control"></div>
                    </div>
                    <button type="button" class="btn btn-primary mt-2" id="cinetpayButton" style="display:none;">Payer via Mobile Money</button>
                </div>
                <div class="payment-section mt-3" id="cheque_section" style="display:none;">
                    <h5>Détails du Chèque</h5>
                    <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Montant du chèque</label><input type="text" name="cheque_montant" class="form-control"></div></div>
                </div>
                <div class="payment-section mt-3" id="cash_section" style="display:none;">
                    <h5>Paiement en Espèces</h5>
                    <div class="mb-3"><label class="form-label">Montant reçu</label><input type="number" name="cash_montant_recu" class="form-control" min="0"></div>
                </div>
                <div class="payment-section mt-3" id="delivery_section" style="display:none;">
                    <h5>Paiement à la Livraison</h5>
                    <p class="alert alert-warning">Le paiement sera effectué lors de la livraison du colis.</p>
                </div>
            </div>
        </fieldset>

        <!-- Boutons de navigation globaux -->
        <div class="text-end mt-4 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary btn-prev" style="display: none;">Précédent</button>
            <button type="button" class="btn btn-primary btn-next">Suivant</button>
            <button type="submit" class="btn btn-success btn-submit" style="display: none;">Valider l'envoi</button>
        </div>
    </form>

    {{-- MODALS --}}
    <!-- Modal : Ajouter un Service -->
    <div class="modal fade" id="ServiceModal" tabindex="-1" aria-labelledby="ServiceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="ServiceModalLabel">Ajouter un Service</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button></div>
                <div class="modal-body">
                    <form id="serviceForm">
                        <div class="mb-3"><label for="description_service" class="form-label">Description</label><input type="text" name="description" id="description_service" class="form-control"></div>
                        <input type="hidden" name="categorie" value="Service">
                        <div class="mb-3"><label for="prix_unitaire_service" class="form-label">Prix Unitaire</label><input type="number" name="prix" id="prix_unitaire_service" class="form-control" min="0"></div>
                        <div class="mb-3"><label for="agence_service" class="form-label">Agence de destination</label><select name="agence" id="agence_service" class="form-control"><option value="" disabled selected>-- Sélectionnez --</option><option value="IPMS-SIMEX-CI Angre 8ème Tranche">DS Translog Angré 8ème Tranche</option><option value="AFT Agence Louis Bleriot">AFT Agence Louis Bleriot</option></select></div>
                    </form>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button><button type="button" class="btn btn-primary btn-save-service" data-url="{{ route('chine_colis.store.service') }}">Créer</button></div>
            </div>
        </div>
    </div>
    <!-- Modal : Ajouter un Produit -->
    <div class="modal fade" id="produitModal" tabindex="-1" aria-labelledby="produitModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="produitModalLabel">Ajouter un Produit</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button></div>
                <div class="modal-body">
                    <form id="produitForm">
                        <div class="mb-3"><label for="description_produit" class="form-label">Description</label><input type="text" name="description" id="description_produit" class="form-control"></div>
                        <input type="hidden" name="categorie" value="Colis">
                        <div class="mb-3"><label for="prix_unitaire" class="form-label">Prix Unitaire</label><input type="number" name="prix" id="prix_unitaire" class="form-control" min="0"></div>
                        <div class="mb-3"><label for="agence" class="form-label">Agence de destination</label><select name="agence" id="agence" class="form-control"><option value="" disabled selected>-- Sélectionnez --</option><option value="IPMS-SIMEX-CI Angre 8ème Tranche">DS Translog Angré 8ème Tranche</option><option value="AFT Agence Louis Bleriot">AFT Agence Louis Bleriot</option></select></div>
                    </form>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button><button type="button" class="btn btn-primary btn-save-produit" data-url="{{ route('chine_colis.store.produit') }}">Créer</button></div>
            </div>
        </div>
    </div>
</section>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- jQuery UI -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>

jQuery(document).ready(function($) {
    const colisContainer = $('#colis-container');
    const colisFieldset = $('#colis-fieldset');

    // ---------- Autocomplete pour la recherche de devis ----------
    $("#devis_reference_autocomplete").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{ route('chine_colis.devis.search') }}",
                dataType: "json",
                data: { term: request.term },
                success: function(data) { response(data); }
            });
        },
        minLength: 2,
        select: function(event, ui) {
            event.preventDefault();
            if(ui.item && ui.item.id){
                $(this).val(ui.item.label);
                $('#selected_devis_id').val(ui.item.id);
                chargerInformationsCompletesDuDevis(ui.item.id);
            }
        }
    });

    /**
     * Charge les informations complètes du devis (programme), y compris
     * expéditeur, destinataire, transport et colis.
     */
    function chargerInformationsCompletesDuDevis(programmeId) {
        $.ajax({
            url: "{{ route('chine_colis.devis.getItems', ['programme' => 'PROGRAMME_ID']) }}".replace('PROGRAMME_ID', programmeId),
            type: 'GET',
            success: function(response) {
                if (response.programme) {
                    const programme = response.programme;

                    // 1. Remplir les informations de transport (Étape 1)
                    if (programme.mode_expedition) {
                        $('#mode_transit').val(programme.mode_expedition).trigger('change');
                    }
                    setTimeout(function() {
                        if (programme.agence_destination) {
                            $('#agence_destinataire').val(programme.agence_destination);
                        }
                    }, 200);

                    // 2. Remplir les informations de l'expéditeur et du destinataire (Étapes 2 & 3)
                    const categorie = programme.categorie_client || 'particulier';
                    $('#categorie_client').val(categorie).trigger('change');

                    if (categorie === 'societe') {
                        $('#nom_societe_expediteur').val(programme.nom_expediteur || '');
                        $('#email_societe_expediteur').val(programme.email_expediteur || '');
                        $('#tel_expediteur_societe').val(programme.tel_expediteur || '');
                        $('#adresse_expediteur_societe').val(programme.adresse_expediteur || '');
                        $('#nom_societe_destinataire').val(programme.nom_destinataire || '');
                        $('#email_societe_destinataire').val(programme.email_destinataire || '');
                        $('input[name="tel_destinataire_societe"]').val(programme.tel_destinataire || '');
                    } else { // Particulier
                        $('#nom_expediteur').val(programme.nom_expediteur || '');
                        $('#prenom_expediteur').val(programme.prenom_expediteur || '');
                        $('#email_expediteur').val(programme.email_expediteur || '');
                        $('#tel_expediteur').val(programme.tel_expediteur || '');
                        $('#nom_destinataire').val(programme.nom_destinataire || '');
                        $('#prenom_destinataire').val(programme.prenom_destinataire || '');
                        $('#email_destinataire').val(programme.email_destinataire || '');
                        $('input[name="tel_destinataire"]').val(programme.tel_destinataire || '');
                    }
                }

                // --- GESTION DES COLIS ---
                colisContainer.empty();
                
                if(response.items && response.items.length > 0){
                    response.items.forEach(item => ajouterLigneColis(item, true));
                } else {
                    ajouterLigneColis(null, true);
                }

                mettreAJourVisibiliteBoutonRetirer();
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('Erreur lors de la récupération des informations du devis.');
            }
        });
    }

    // ---------- Ajouter une ligne colis (fonction utilitaire) ----------
    function ajouterLigneColis(itemData = null, isDevis = false) {
        let template = isDevis ? '.colis-item-template-devis' : '.colis-item-template';
        let ligne = $(template).find('.colis-item').clone();

        if(itemData){
            ligne.find('input[name="quantite_colis[]"]').val(itemData.quantite_colis || itemData.quantite || 1);
            ligne.find('input[name="service[]"]').val(itemData.service || '');
            ligne.find('input[name="valeur_colis[]"]').val(itemData.valeur_colis || itemData.prix_unitaire || '');
            ligne.find('select[name="type_colis[]"]').val(itemData.type_colis || 'standard');
            ligne.find('select[name="devise[]"]').val(itemData.devise || 'EUR');
            ligne.find('textarea[name="description_colis[]"]').val(itemData.description_colis || itemData.description || '');
            ligne.find('input[name="poids[]"]').val(itemData.poids || '');
            ligne.find('input[name="longueur[]"]').val(itemData.longueur || '');
            ligne.find('input[name="largeur[]"]').val(itemData.largeur || '');
            ligne.find('input[name="hauteur[]"]').val(itemData.hauteur || '');
        }

        colisContainer.append(ligne);
        attachColisEventListeners(ligne);
        updateDynamicFields();
    }

    // ---------- Gestion Ajout / Suppression de lignes de colis ----------
    $('.add-colis').on('click', function() {
        ajouterLigneColis(null, false);
        mettreAJourVisibiliteBoutonRetirer();
    });

    colisContainer.on('click', '.remove-colis', function() {
        $(this).closest('.colis-item').remove();
        mettreAJourVisibiliteBoutonRetirer();
    });

    function mettreAJourVisibiliteBoutonRetirer() {
        const nbLignes = colisContainer.find('.colis-item').length;
        colisContainer.find('.remove-colis').toggle(nbLignes > 1);
    }
});

$(document).ready(function () {
    const searchInput = $('#client_select');
    const resultsContainer = $('#client_autocomplete_results');

    searchInput.on('keyup', function () {
        const query = $(this).val();
        if (query.length < 2) { resultsContainer.hide(); return; }
        $.ajax({
            url: "{{ route('chine_colis.clients.search') }}",
            dataType: 'json',
            data: { q: query },
            success: function (data) {
                resultsContainer.empty();
                if (data.length > 0) {
                    data.forEach(function (client) {
                        const item = $(`<div class="autocomplete-item"><strong>${client.first_name} ${client.last_name}</strong><br><small class="text-muted">${client.tel}</small></div>`);
                        item.on('click', function () {
                            searchInput.val(client.first_name + ' ' + client.last_name);
                            resultsContainer.hide();
                            if (client.category === 'particulier') {
                                $('#categorie_client').val('particulier').trigger('change');
                                $('#nom_expediteur').val(client.first_name);
                                $('#prenom_expediteur').val(client.last_name);
                                $('#email_expediteur').val(client.email);
                                $('#tel_expediteur').val(client.tel);
                            } else if (client.category === 'societe') {
                                $('#categorie_client').val('societe').trigger('change');
                                $('#nom_societe_expediteur').val(client.first_name);
                                $('#email_societe_expediteur').val(client.email);
                                $('#tel_expediteur_societe').val(client.tel);
                                $('#adresse_expediteur_societe').val(client.adresse);
                            }
                        });
                        resultsContainer.append(item);
                    });
                    resultsContainer.show();
                } else {
                    resultsContainer.html('<div class="autocomplete-item text-danger">Ce client n\'existe pas.</div>').show();
                }
            }
        });
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.position-relative').length) { resultsContainer.hide(); }
    });
    searchInput.on('input', function() {
        if ($(this).val() === '') { $('#particulier_expediteur_section input, #societe_expediteur_section input').val(''); }
    });

    let currentStep = 0;
    const fieldsets = $("fieldset");
    const steps = $(".step");

    function updateProgressBar() {
        let percentage = (currentStep / (steps.length - 1)) * 100;
        $('.progress-steps').css('--progress-width', percentage + '%');
        steps.each(function(index) { $(this).toggleClass("active", index <= currentStep); });
    }

    function showStep(stepIndex) {
        fieldsets.hide().eq(stepIndex).show();
        currentStep = stepIndex;
        updateProgressBar();
        $(".btn-prev").toggle(stepIndex > 0);
        const isLastStep = stepIndex === fieldsets.length - 1;
        $(".btn-next").toggle(!isLastStep);
        $(".btn-submit").toggle(isLastStep);
    }

    $(".btn-next").click(function() {
        if (currentStep >= 3) { updateRecapitulatif(); }
        if (currentStep < fieldsets.length - 1) {
            if (currentStep + 1 === 5) {
                const total = $('#recap_total_a_payer').text();
                const devise = $('#recap_devise').text();
                $('#payment_total').text(total);
                $('#payment_devise').text(devise);
                const totalValue = parseFloat(total) || 0;
                $('input[name="bank_montant"]').val(totalValue.toFixed(2));
                $('input[name="cheque_montant"]').val(totalValue.toFixed(2));
                $('input[name="cash_montant_recu"]').attr('placeholder', `Montant reçu (Total: ${totalValue.toFixed(2)} ${devise})`);
            }
            showStep(currentStep + 1);
        }
    });

    steps.click(function() {
        const stepIndex = $(this).data("step");
        if (stepIndex >= 4) { updateRecapitulatif(); }
        showStep(stepIndex);
    });

    $(".btn-prev").click(function() {
        if (currentStep > 0) { showStep(currentStep - 1); }
    });
    
    showStep(0);

    const agenceOptionsByMode = {
        maritime: { value: "IPMS-SIMEX-CI", label: "DS Translog Carrefour Angré" },
        aerien: { value: "IPMS-SIMEX-CI Angre 8ème Tranche", label: "DS Translog Angré 8ème Tranche" }
    };

    function updateDynamicFields() {
        const mode = $("#mode_transit").val();
        $("#ref_maritime, .dimension-section").toggle(mode === "maritime");
        $("#ref_aerien, .poids-section").toggle(mode === "aerien");
        $(".quantite-section").toggle(mode !== "aerien");
        const agenceDestSelect = $("#agence_destinataire");
        agenceDestSelect.html('<option value="" disabled selected>-- Sélectionnez --</option>');
        if (mode && agenceOptionsByMode[mode]) {
            const opt = agenceOptionsByMode[mode];
            agenceDestSelect.append(new Option(opt.label, opt.value, true, true));
        }
        updateDeviseBasedOnAgence();
    }

    function updateDeviseBasedOnAgence() {
        const agence = $("#agence_expediteur").val();
        let devise = (agence === 'Agence de Chine') ? 'FCFA' : 'EUR';
        let disabled = (agence === 'Agence de Chine' || agence === 'AFT Agence Louis Bleriot');
        $(".devise-select").val(devise).prop('disabled', disabled).css('background-color', disabled ? '#e9ecef' : '');
    }
    
    function toggleClientSections() {
        const categorie = $("#categorie_client").val();
        const isSociete = categorie === 'societe';
        $("#societe_expediteur_section, #societe_destinataire_section").toggle(isSociete).find("input, select").prop('disabled', !isSociete);
        $("#particulier_expediteur_section, #particulier_destinataire_section").toggle(!isSociete).find("input, select").prop('disabled', isSociete);
    }
    
    $("#mode_transit, #agence_expediteur").on('change', function() {
        updateDynamicFields();
        if ($(this).is('#mode_transit')) {
            $('.colis-item').each(function() { updateTotalForColis($(this)); });
        }
    });

    $("#categorie_client").on('change', toggleClientSections);
    
    updateDynamicFields();
    toggleClientSections();

    function calculateColisTotal(colisElement) {
        const modeTransit = $('#mode_transit').val();
        const prixUnitaire = parseFloat(colisElement.find(".prix-colis").attr("data-prix-unitaire")) || parseFloat(colisElement.find(".prix-colis").val()) || 0;
        if (modeTransit === 'aerien') {
            const poids = parseFloat(colisElement.find(".poids-colis").val()) || 0;
            return prixUnitaire * poids;
        } else {
            const quantite = parseFloat(colisElement.find(".quantite-colis").val()) || 1;
            return quantite * prixUnitaire;
        }
    }

    function updateTotalForColis(colisElement) {
        const prixTotal = calculateColisTotal(colisElement);
        colisElement.find(".prix-total").text(prixTotal.toFixed(2));
    }
    
    function attachColisEventListeners(colisElement) {
        initAutocomplete(colisElement.find(".produit-input"), "{{ route('chine_colis.recherche.auto') }}", 'Colis');
        colisElement.on('input', '.quantite-colis, .prix-colis, .poids-colis', function() {
            updateTotalForColis($(this).closest('.colis-item'));
        });
        colisElement.on("input", ".hauteur, .largeur, .longueur", function () {
            const parent = $(this).closest(".dimension-section");
            const h = parent.find(".hauteur").val(), la = parent.find(".largeur").val(), lo = parent.find(".longueur").val();
            parent.find(".dimension-result").text(h && la && lo ? `${lo}x${la}x${h} cm` : '').toggle(!!(h && la && lo));
        });
    }
    
    initAutocomplete($('.service-input'), "{{ route('chine_colis.recherche.auto.service') }}", 'Service');
    $('.prix-service').on('input', function() { $('.prix-total-service').text((parseFloat($(this).val()) || 0).toFixed(2)); });

    $(".add-colis").click(function() {
        const newColis = $("#colis-container .colis-item:first").clone();
        newColis.find("input, textarea, select").val("");
        newColis.find(".quantite-colis").val("1");
        newColis.find(".prix-total").text("0");
        newColis.find(".dimension-result").hide().text('');
        newColis.find('.autocomplete-results').empty().hide();
        newColis.find(".remove-colis").show();
        $("#colis-container").append(newColis);
        attachColisEventListeners(newColis);
        updateDynamicFields();
    });

    $("#colis-container").on("click", ".remove-colis", function () { $(this).closest(".colis-item").remove(); });
    attachColisEventListeners($("#colis-container .colis-item:first"));
    
    function initAutocomplete(inputElement, url, categorie) {
        inputElement.on("keyup", function() {
            const query = $(this).val().trim();
            const input = $(this);
            const resultsContainer = input.closest('.position-relative').find('.autocomplete-results');
            if (query.length < 2) { resultsContainer.empty().hide(); return; }
            $.ajax({
                url: url, type: "GET", dataType: "json", data: { query: query, categorie: categorie },
                success: function(data) {
                    resultsContainer.empty().show();
                    if (data.length > 0) {
                        $.each(data, function(index, item) {
                            $('<div class="autocomplete-item"></div>').text(item.description).on('click', function() {
                                input.val(item.description);
                                const colisItem = input.closest('.colis-item');
                                const prixInput = categorie === 'Colis' ? colisItem.find('.prix-colis') : $('.prix-service');
                                const prixUnitaire = parseFloat(item.prix);
                                prixInput.val(prixUnitaire).attr('data-prix-unitaire', prixUnitaire);
                                if (categorie === 'Colis') { updateTotalForColis(colisItem); } 
                                else { $('.prix-total-service').text(prixUnitaire.toFixed(2)); }
                                resultsContainer.empty().hide();
                            }).appendTo(resultsContainer);
                        });
                    } else { resultsContainer.hide(); }
                },
                error: function() { resultsContainer.empty().hide(); }
            });
        });
    }

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.produit-input, .service-input, .autocomplete-results').length) { $('.autocomplete-results').hide(); }
    });

    let activeProduitInput = null;
    $(document).on("click", ".btn-add-produit", function() {
        activeProduitInput = $(this).siblings(".produit-input");
        $("#description_produit").val(activeProduitInput.val());
    });
    
    $(".btn-save-produit").on("click", function () {
        const form = $("#produitForm"), btn = $(this);
        const data = { description: form.find("#description_produit").val().trim(), prix: parseFloat(form.find("#prix_unitaire").val()), agence: form.find("#agence").val(), categorie: 'Colis', _token: '{{ csrf_token() }}' };
        if (!data.description || isNaN(data.prix) || data.prix <= 0) { alert("Veuillez remplir tous les champs correctement."); return; }
        btn.prop("disabled", true).text("Enregistrement...");
        $.ajax({
            url: btn.data("url"), type: "POST", data: data, dataType: 'json',
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
            error: function (xhr) { alert("Erreur: " + (xhr.responseJSON?.message || "Erreur serveur")); },
            complete: function() { btn.prop("disabled", false).text("Créer"); }
        });
    });

    $(".btn-save-service").on("click", function () {
        const form = $("#serviceForm"), btn = $(this);
        const data = { description: form.find("#description_service").val().trim(), prix: parseFloat(form.find("#prix_unitaire_service").val()), agence: form.find("#agence_service").val(), categorie: 'Service', _token: '{{ csrf_token() }}' };
        if (!data.description || isNaN(data.prix) || data.prix <= 0) { alert("Veuillez remplir tous les champs correctement."); return; }
        btn.prop("disabled", true).text("Enregistrement...");
        $.ajax({
            url: btn.data("url"), type: "POST", data: data, dataType: 'json',
            success: function (response) {
                alert(response.message);
                $('.service-input').val(data.description);
                $('.prix-service').val(data.prix);
                $('.prix-total-service').text(parseFloat(data.prix).toFixed(2));
                $("#ServiceModal").modal("hide");
                form[0].reset();
            },
            error: function (xhr) { alert("Erreur: " + (xhr.responseJSON?.message || "Erreur serveur")); },
            complete: function() { btn.prop("disabled", false).text("Créer"); }
        });
    });

    function updateRecapitulatif() {
        const isSociete = $('#categorie_client').val() === 'societe';
        $('#recap_mode_transit').text($('#mode_transit option:selected').text() || 'N/A');
        let ref = ($('#mode_transit').val() === 'maritime') ? $('input[name="reference_colis_maritime"]').val() : $('input[name="reference_colis_aerien"]').val();
        $('#recap_reference_colis').text(ref || 'N/A');
        const nomExp = isSociete ? $('#nom_societe_expediteur').val() : `${$('#nom_expediteur').val()} ${$('#prenom_expediteur').val()}`;
        const telExp = isSociete ? $('#tel_expediteur_societe').val() : $('#tel_expediteur').val();
        $('#recap_nom_expediteur').text(nomExp.trim() || 'N/A');
        $('#recap_tel_expediteur').text(telExp || 'N/A');
        $('#recap_agence_expediteur').text($('#agence_expediteur').val() || 'N/A');
        const nomDest = isSociete ? $('#nom_societe_destinataire').val() : `${$('#nom_destinataire').val()} ${$('#prenom_destinataire').val()}`;
        const codePays = isSociete ? $('select[name="country_code_societe"]').val() : $('select[name="country_code_particulier"]').val();
        const telDestNum = isSociete ? $('input[name="tel_destinataire_societe"]').val() : $('input[name="tel_destinataire"]').val();
        $('#recap_nom_destinataire').text(nomDest.trim() || 'N/A');
        $('#recap_tel_destinataire').text(telDestNum ? `${codePays} ${telDestNum}` : 'N/A');
        $('#recap_agence_destinataire').text($('#agence_destinataire option:selected').text() || 'N/A');
        const detailsColisContainer = $('#recap_details_colis').empty();
        let totalColis = 0;
        const devise = $('.devise-select:first').val() || 'EUR';
        $('#colis-container .colis-item').each(function() {
            const produit = ($(this).find('.produit-input').val() || '').trim();
            const quantite = $(this).find('.quantite-colis').val();
            const prixTotalDuColis = calculateColisTotal($(this));
            if (produit && prixTotalDuColis > 0) {
                detailsColisContainer.append(`<div class="d-flex justify-content-between"><span>- ${produit} (Quantité: ${quantite})</span><strong>${prixTotalDuColis.toFixed(2)} ${devise}</strong></div>`);
                totalColis += prixTotalDuColis;
            }
        });
        if (detailsColisContainer.is(':empty')) { detailsColisContainer.html('<p class="text-muted">Aucun colis valide ajouté.</p>'); }
        const totalServices = parseFloat($('.prix-service').val()) || 0;
        const totalAPayer = totalColis + totalServices;
        $('#recap_total_colis').text(totalColis.toFixed(2));
        $('#recap_total_services').text(totalServices.toFixed(2));
        $('#recap_total_a_payer').text(totalAPayer.toFixed(2));
        $('#recap_devise, .recap_devise_class').text(devise);
    }

    $('#mode_payement').on('change', function() {
        $('.payment-section').hide();
        const selectedMethod = $(this).val();
        if (selectedMethod) { $('#' + selectedMethod + '_section').slideDown(); }
    });

    $('#mobile_operateur').on('change', function() { $('#cinetpayButton').toggle($(this).val() !== ''); });
});
</script>

<style>
    .form-container { max-width: 95%; margin: auto; background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); }
    .form-section { background-color: #f8f9fa; padding: 20px; border: 1px solid #dee2e6; border-radius: 8px; margin-bottom: 20px; }
    fieldset { border: none; padding: 0; }
    .progress-bar-container { width: 100%; margin-bottom: 40px; }
    .progress-steps { --progress-width: 0%; display: flex; justify-content: space-between; list-style: none; padding: 0; margin: 0; position: relative; }
    .progress-steps::before { content: ''; position: absolute; top: 50%; transform: translateY(-50%); height: 4px; width: 100%; background-color: #d8d8d8; z-index: 1; }
    .progress-steps::after { content: ''; position: absolute; top: 50%; transform: translateY(-50%); height: 4px; width: var(--progress-width); background-color: #28a745; z-index: 2; transition: width 0.4s ease; }
    .progress-steps .step { display: flex; flex-direction: column; align-items: center; position: relative; z-index: 3; cursor: pointer; }
    .progress-steps .step::before { content: ''; display: block; width: 30px; height: 30px; border-radius: 50%; background-color: #d8d8d8; border: 3px solid #d8d8d8; transition: background-color 0.4s ease, border-color 0.4s ease; margin-bottom: 5px; }
    .progress-steps .step span { font-size: 14px; color: #6c757d; text-align: center; }
    .progress-steps .step.active::before { background-color: #fff; border-color: #28a745; }
    .progress-steps .step.active span { color: #28a745; font-weight: bold; }
    .autocomplete-results { position: absolute; top: 100%; left: 0; right: 0; z-index: 1000; background-color: #fff; border: 1px solid #ccc; border-radius: 4px; max-height: 200px; overflow-y: auto; display: none; }
    .autocomplete-item { padding: 8px 12px; cursor: pointer; }
    .autocomplete-item:hover { background-color: #f0f0f0; }
</style>
@endsection