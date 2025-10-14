@extends('admin.layouts.admin')

@section('content-header')
{{-- CSRF Token pour les requêtes AJAX --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
{{-- Font Awesome --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        <fieldset>
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
                        <select name="agence_expediteur" id="agence_expediteur" class="form-control">
                            <option value="" disabled selected>-- Sélectionnez l'agence --</option>
                            @foreach ($agencesExpedition as $agence)
                            <option value="{{ $agence->nom_agence }}">{{ $agence->nom_agence }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="agence_destinataire" class="form-label">Agence de destination</label>
                        <select name="agence_destinataire" id="agence_destinataire" class="form-control">
                            <option value="" disabled selected>-- Sélectionnez l'agence --</option>
                            {{-- Remplir dynamiquement via JavaScript --}}
                        </select>
                    </div>
                    <div class="col-md-6" id="ref_container" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label" id="ref_label">Référence</label>
                            <input type="text" name="reference_colis" id="reference_colis_input" class="form-control" value="" readonly>
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
                        <div class="col-md-6 mb-3">
                            <label for="adresse_destinataire_particulier" class="form-label">Adresse de Livraison</label>
                            <select name="adresse_destinataire_particulier"  id="adresse_destinataire_particulier" class="form-control" required>
                                <option value="">-- Sélectionnez une commune ou un quartier --</option>
                                <option value="Pas de livraison">Pas de Livraison</option>

                                <!-- 🌆 ABIDJAN CENTRE -->
                                <optgroup label="Abobo">
                                    <option value="Abobo Avocatier">Avocatier</option>
                                    <option value="Abobo Baoulé">Baoulé</option>
                                    <option value="Abobo PK18">PK18</option>
                                    <option value="Abobo Banco">Banco</option>
                                    <option value="Abobo Kennedy">Kennedy</option>
                                    <option value="Abobo Belleville">Belleville</option>
                                    <option value="Abobo Sagbé">Sagb&eacute;</option>
                                    <option value="Abobo N’Dotré">N’Dotré</option>
                                    <option value="Abobo SOS">SOS</option>
                                    <option value="Abobo Derrière Rail">Derrière Rail</option>
                                    <option value="Abobo Anador">Anador</option>
                                    <option value="Abobo Clouetcha">Clouetcha</option>
                                    <option value="Abobo Gagnoa Gare">Gagnoa Gare</option>
                                </optgroup>

                                <optgroup label="Adjamé">
                                    <option value="Adjamé Liberté">Liberté</option>
                                    <option value="Adjamé Bracodi">Bracodi</option>
                                    <option value="Adjamé Williamsville">Williamsville</option>
                                    <option value="Adjamé Camp Commandant">Camp Commandant</option>
                                    <option value="Adjamé Indénié">Indénié</option>
                                    <option value="Adjamé 220 Logements">220 Logements</option>
                                </optgroup>

                                <optgroup label="Attécoubé">
                                    <option value="Attécoubé Santé">Santé</option>
                                    <option value="Attécoubé Mossikro">Mossikro</option>
                                    <option value="Attécoubé Abobo Doumé">Abobo Doumé</option>
                                    <option value="Attécoubé Toit Rouge">Toit Rouge</option>
                                    <option value="Attécoubé Djibi">Djibi</option>
                                </optgroup>

                                <optgroup label="Cocody">
                                    <option value="Cocody Deux Plateaux">Deux Plateaux</option>
                                    <option value="Cocody Angré">Angré</option>
                                    <option value="Cocody Riviera 1">Riviera 1</option>
                                    <option value="Cocody Riviera 2">Riviera 2</option>
                                    <option value="Cocody Riviera 3">Riviera 3</option>
                                    <option value="Cocody Riviera 4">Riviera 4</option>
                                    <option value="Cocody Riviera Bonoumin">Riviera Bonoumin</option>
                                    <option value="Cocody Riviera Palmeraie">Riviera Palmeraie</option>
                                    <option value="Cocody Riviera Golf">Riviera Golf</option>
                                    <option value="Cocody 7e Tranche">7e Tranche</option>
                                    <option value="Cocody Blockhaus">Blockhaus</option>
                                    <option value="Cocody Danga">Danga</option>
                                    <option value="Cocody M’Badon">M’Badon</option>
                                    <option value="Cocody Ambassades">Ambassades</option>
                                </optgroup>

                                <optgroup label="Koumassi">
                                    <option value="Koumassi Campement">Campement</option>
                                    <option value="Koumassi Remblais">Remblais</option>
                                    <option value="Koumassi Sopim">Sopim</option>
                                    <option value="Koumassi Divo">Divo</option>
                                    <option value="Koumassi Sicogi">Sicogi</option>
                                    <option value="Koumassi Prodomo">Prodomo</option>
                                </optgroup>

                                <optgroup label="Marcory">
                                    <option value="Marcory Zone 4">Zone 4</option>
                                    <option value="Marcory Biétry">Biétry</option>
                                    <option value="Marcory Résidentiel">Résidentiel</option>
                                    <option value="Marcory Poto-Poto">Poto-Poto</option>
                                    <option value="Marcory Konan Raphael">Konan Raphael</option>
                                </optgroup>

                                <optgroup label="Le Plateau">
                                    <option value="Plateau Administratif">Administratif</option>
                                    <option value="Plateau Cité Financière">Cité Financière</option>
                                    <option value="Plateau Indénié">Indénié</option>
                                </optgroup>

                                <optgroup label="Port-Bouët">
                                    <option value="Port-Bouët Vridi">Vridi</option>
                                    <option value="Port-Bouët Gonzagueville">Gonzagueville</option>
                                    <option value="Port-Bouët Aéroport">Aéroport</option>
                                    <option value="Port-Bouët Petit Bassam">Petit Bassam</option>
                                    <option value="Port-Bouët Port">Port</option>
                                </optgroup>

                                <optgroup label="Treichville">
                                    <option value="Treichville Avenue 16">Avenue 16</option>
                                    <option value="Treichville Zone 3">Zone 3</option>
                                    <option value="Treichville Rue 12">Rue 12</option>
                                    <option value="Treichville Rue 21">Rue 21</option>
                                    <option value="Treichville Biafra">Biafra</option>
                                </optgroup>

                                <optgroup label="Yopougon">
                                    <option value="Yopougon Toits Rouges">Toits Rouges</option>
                                    <option value="Yopougon Niangon">Niangon</option>
                                    <option value="Yopougon Sideci">Sideci</option>
                                    <option value="Yopougon Maroc">Maroc</option>
                                    <option value="Yopougon Kouté">Kouté</option>
                                    <option value="Yopougon Andokoi">Andokoi</option>
                                    <option value="Yopougon Gesco">Gesco</option>
                                    <option value="Yopougon Siporex">Siporex</option>
                                    <option value="Yopougon Selmer">Selmer</option>
                                    <option value="Yopougon Banco">Banco</option>
                                </optgroup>

                                <!-- 🌍 COMMUNES PÉRIPHÉRIQUES -->
                                <optgroup label="Anyama">
                                    <option value="Anyama Centre">Centre</option>
                                    <option value="Anyama Akoupé Zeudji">Akoupé Zeudji</option>
                                    <option value="Anyama Ebimpé">Ebimpé</option>
                                    <option value="Anyama Ahouabo">Ahouabo</option>
                                </optgroup>

                                <optgroup label="Bingerville">
                                    <option value="Bingerville Centre">Centre</option>
                                    <option value="Bingerville Adjamé Bingerville">Adjamé Bingerville</option>
                                    <option value="Bingerville Akandjé">Akandjé</option>
                                    <option value="Bingerville M’Pouto">M’Pouto</option>
                                    <option value="Bingerville Eloka">Eloka</option>
                                </optgroup>

                                <optgroup label="Songon">
                                    <option value="Songon Kassemblé">Kassemblé</option>
                                    <option value="Songon Dagbé">Dagbé</option>
                                    <option value="Songon M’Braté">M’Braté</option>
                                    <option value="Songon Agban">Agban</option>
                                </optgroup>

                                <optgroup label="Brofodoumé">
                                    <option value="Brofodoumé Centre">Centre</option>
                                    <option value="Brofodoumé M’Bédo">M’Bédo</option>
                                    <option value="Brofodoumé Akouai-Santé">Akouai-Santé</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Téléphone</label>
                            <div class="input-group">
                                <select name="country_code_particulier" class="input-group-text">
                                    <option value="+33">FR (+33)</option>
                                    <option value="+225">CI (+225)</option>
                                    <option value="+86">CN (+86)</option>
                                </select>
                                <input type="text" name="tel_destinataire" class="form-control" placeholder="Ex: 0123456789">
                            </div> 
                        </div>
                    </div>
                </div>
                {{-- ===== SOCIÉTÉ DESTINATAIRE ===== --}}
                <div id="societe_destinataire_section" style="display: none;">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label for="nom_societe_destinataire" class="form-label">Nom de la société</label><input type="text" name="nom_destinataire_societe" id="nom_societe_destinataire" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label for="email_societe_destinataire" class="form-label">Email</label><input type="email" name="email_destinataire_societe" id="email_societe_destinataire" class="form-control"></div>
                        <div class="col-md-6 mb-3">
                            <label for="adresse_destinataire_societe" class="form-label">Adresse de Livraison</label>
                            <select name="adresse_destinataire_societe" id="adresse_destinataire_societe" class="form-control" required>
                                <option value="">-- Sélectionnez une commune ou un quartier --</option>
                                <option value="Pas de livraison">Pas de Livraison</option>

                                <!-- 🌆 ABIDJAN CENTRE -->
                                <optgroup label="Abobo">
                                    <option value="Abobo Avocatier">Avocatier</option>
                                    <option value="Abobo Baoulé">Baoulé</option>
                                    <option value="Abobo PK18">PK18</option>
                                    <option value="Abobo Banco">Banco</option>
                                    <option value="Abobo Kennedy">Kennedy</option>
                                    <option value="Abobo Belleville">Belleville</option>
                                    <option value="Abobo Sagbé">Sagb&eacute;</option>
                                    <option value="Abobo N’Dotré">N’Dotré</option>
                                    <option value="Abobo SOS">SOS</option>
                                    <option value="Abobo Derrière Rail">Derrière Rail</option>
                                    <option value="Abobo Anador">Anador</option>
                                    <option value="Abobo Clouetcha">Clouetcha</option>
                                    <option value="Abobo Gagnoa Gare">Gagnoa Gare</option>
                                </optgroup>

                                <optgroup label="Adjamé">
                                    <option value="Adjamé Liberté">Liberté</option>
                                    <option value="Adjamé Bracodi">Bracodi</option>
                                    <option value="Adjamé Williamsville">Williamsville</option>
                                    <option value="Adjamé Camp Commandant">Camp Commandant</option>
                                    <option value="Adjamé Indénié">Indénié</option>
                                    <option value="Adjamé 220 Logements">220 Logements</option>
                                </optgroup>

                                <optgroup label="Attécoubé">
                                    <option value="Attécoubé Santé">Santé</option>
                                    <option value="Attécoubé Mossikro">Mossikro</option>
                                    <option value="Attécoubé Abobo Doumé">Abobo Doumé</option>
                                    <option value="Attécoubé Toit Rouge">Toit Rouge</option>
                                    <option value="Attécoubé Djibi">Djibi</option>
                                </optgroup>

                                <optgroup label="Cocody">
                                    <option value="Cocody Deux Plateaux">Deux Plateaux</option>
                                    <option value="Cocody Angré">Angré</option>
                                    <option value="Cocody Riviera 1">Riviera 1</option>
                                    <option value="Cocody Riviera 2">Riviera 2</option>
                                    <option value="Cocody Riviera 3">Riviera 3</option>
                                    <option value="Cocody Riviera 4">Riviera 4</option>
                                    <option value="Cocody Riviera Bonoumin">Riviera Bonoumin</option>
                                    <option value="Cocody Riviera Palmeraie">Riviera Palmeraie</option>
                                    <option value="Cocody Riviera Golf">Riviera Golf</option>
                                    <option value="Cocody 7e Tranche">7e Tranche</option>
                                    <option value="Cocody Blockhaus">Blockhaus</option>
                                    <option value="Cocody Danga">Danga</option>
                                    <option value="Cocody M’Badon">M’Badon</option>
                                    <option value="Cocody Ambassades">Ambassades</option>
                                </optgroup>

                                <optgroup label="Koumassi">
                                    <option value="Koumassi Campement">Campement</option>
                                    <option value="Koumassi Remblais">Remblais</option>
                                    <option value="Koumassi Sopim">Sopim</option>
                                    <option value="Koumassi Divo">Divo</option>
                                    <option value="Koumassi Sicogi">Sicogi</option>
                                    <option value="Koumassi Prodomo">Prodomo</option>
                                </optgroup>

                                <optgroup label="Marcory">
                                    <option value="Marcory Zone 4">Zone 4</option>
                                    <option value="Marcory Biétry">Biétry</option>
                                    <option value="Marcory Résidentiel">Résidentiel</option>
                                    <option value="Marcory Poto-Poto">Poto-Poto</option>
                                    <option value="Marcory Konan Raphael">Konan Raphael</option>
                                </optgroup>

                                <optgroup label="Le Plateau">
                                    <option value="Plateau Administratif">Administratif</option>
                                    <option value="Plateau Cité Financière">Cité Financière</option>
                                    <option value="Plateau Indénié">Indénié</option>
                                </optgroup>

                                <optgroup label="Port-Bouët">
                                    <option value="Port-Bouët Vridi">Vridi</option>
                                    <option value="Port-Bouët Gonzagueville">Gonzagueville</option>
                                    <option value="Port-Bouët Aéroport">Aéroport</option>
                                    <option value="Port-Bouët Petit Bassam">Petit Bassam</option>
                                    <option value="Port-Bouët Port">Port</option>
                                </optgroup>

                                <optgroup label="Treichville">
                                    <option value="Treichville Avenue 16">Avenue 16</option>
                                    <option value="Treichville Zone 3">Zone 3</option>
                                    <option value="Treichville Rue 12">Rue 12</option>
                                    <option value="Treichville Rue 21">Rue 21</option>
                                    <option value="Treichville Biafra">Biafra</option>
                                </optgroup>

                                <optgroup label="Yopougon">
                                    <option value="Yopougon Toits Rouges">Toits Rouges</option>
                                    <option value="Yopougon Niangon">Niangon</option>
                                    <option value="Yopougon Sideci">Sideci</option>
                                    <option value="Yopougon Maroc">Maroc</option>
                                    <option value="Yopougon Kouté">Kouté</option>
                                    <option value="Yopougon Andokoi">Andokoi</option>
                                    <option value="Yopougon Gesco">Gesco</option>
                                    <option value="Yopougon Siporex">Siporex</option>
                                    <option value="Yopougon Selmer">Selmer</option>
                                    <option value="Yopougon Banco">Banco</option>
                                </optgroup>

                                <!-- 🌍 COMMUNES PÉRIPHÉRIQUES -->
                                <optgroup label="Anyama">
                                    <option value="Anyama Centre">Centre</option>
                                    <option value="Anyama Akoupé Zeudji">Akoupé Zeudji</option>
                                    <option value="Anyama Ebimpé">Ebimpé</option>
                                    <option value="Anyama Ahouabo">Ahouabo</option>
                                </optgroup>

                                <optgroup label="Bingerville">
                                    <option value="Bingerville Centre">Centre</option>
                                    <option value="Bingerville Adjamé Bingerville">Adjamé Bingerville</option>
                                    <option value="Bingerville Akandjé">Akandjé</option>
                                    <option value="Bingerville M’Pouto">M’Pouto</option>
                                    <option value="Bingerville Eloka">Eloka</option>
                                </optgroup>

                                <optgroup label="Songon">
                                    <option value="Songon Kassemblé">Kassemblé</option>
                                    <option value="Songon Dagbé">Dagbé</option>
                                    <option value="Songon M’Braté">M’Braté</option>
                                    <option value="Songon Agban">Agban</option>
                                </optgroup>

                                <optgroup label="Brofodoumé">
                                    <option value="Brofodoumé Centre">Centre</option>
                                    <option value="Brofodoumé M’Bédo">M’Bédo</option>
                                    <option value="Brofodoumé Akouai-Santé">Akouai-Santé</option>
                                </optgroup>
                            </select>
                        </div>                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Téléphone</label>
                            <div class="input-group">
                                <select name="country_code_societe" class="input-group-text">
                                    <option value="+33">FR (+33)</option>
                                    <option value="+225">CI (+225)</option>
                                    <option value="+86">CN (+86)</option>
                                </select>
                                <input type="text" name="tel_destinataire_societe" class="form-control" placeholder="Ex: 0123456789">
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>

        <!-- Étape 4 : Informations du Colis -->
        <fieldset id="colis-fieldset" style="display: none;">
            <h5 class="text-center mb-4 mt-5">Informations du/des Colis</h5>
            <div class="d-flex justify-content-center">
                <div class="card p-3 mb-4 shadow-sm border-0 w-50">
                    <div class="col-md-12">
                        <label for="devis_reference_autocomplete" class="form-label">Récupérer les informations d'un devis existant</label>
                        <input type="text" id="devis_reference_autocomplete" class="form-control" placeholder="Saisir la référence du devis...">
                        <input type="hidden" id="selected_devis_id" name="selected_devis_id">
                    </div>
                </div>
            </div>
            
            {{-- Conteneur pour les colis dynamiques --}}
            <div id="colis-container">
                {{-- Le premier colis (visible au chargement) --}}
                <div class="colis-item form-section mb-4">
                    <div class="row">
                        <div class="col-md-2 mb-3"><label class="form-label">Quantité</label><input type="number" name="quantite_colis[]" class="form-control quantite-colis" value="1" min="1"></div>
                        <div class="col-md-4 position-relative mb-3"><label class="form-label">Produit(s)</label><div class="input-group"><input type="text" name="produit[]" class="form-control produit-input" placeholder="Rechercher ou saisir un produit" required><button type="button" class="btn btn-success btn-add-produit" data-bs-toggle="modal" data-bs-target="#produitModal">+</button></div><div class="autocomplete-results"></div></div>
                        <div class="col-md-2 mb-3"><label class="form-label">Prix/Kg</label><input type="number" name="prix[]" class="form-control prix-colis" placeholder="Prix"><div class="mt-2">Prix Total: <span class="prix-total">0</span></div></div>
                        <div class="col-md-2 mb-3"><label class="form-label">Type colis</label><select name="type_colis[]" class="form-control"><option value="standard">Standard</option><option value="fragile">Fragile</option></select></div>
                        <div class="col-md-2 mb-3"><label class="form-label">Devise</label><select name="devise[]" class="form-control devise-select"><option value="EUR">EUR</option><option value="FCFA">FCFA</option></select></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 dimension-section" style="display: none;"><label class="form-label">Dimensions (cm)</label><div class="d-flex gap-2"><input type="number" name="longueur[]" class="form-control longueur" placeholder="Longeur"><input type="number" name="largeur[]" class="form-control largeur" placeholder="Largeur"><input type="number" name="hauteur[]" class="form-control hauteur" placeholder="Hauteur"></div><div class="dimension-result mt-2" style="display: none; font-weight: bold;"></div></div>
                        <div class="col-md-6 poids-section" style="display: none;"><label class="form-label">Poids (kg)</label><input type="number" name="poids[]" class="form-control poids-colis" placeholder="Poids"></div>
                        <div class="col-md-6"><div class="mb-3"><label class="form-label">Commentaire</label><textarea name="description_colis[]" class="form-control" rows="3" placeholder="Description du colis"></textarea></div></div>
                    </div>
                    <div class="text-end mt-2"><button type="button" class="btn btn-danger remove-colis" style="display: none;">Retirer ce colis</button></div>
                </div>
            </div>

            <div class="text-end mt-2">
                <button type="button" class="btn btn-success add-colis">Ajouter un autre colis</button>
            </div>
        </fieldset>

        <!-- TEMPLATE pour colis (caché) -->
        <div id="colis-item-template" style="display:none;">
             <div class="colis-item form-section mb-4">
                <div class="row">
                    <div class="col-md-2 mb-3"><label class="form-label">Quantité</label><input type="number" name="quantite_colis[]" class="form-control quantite-colis" value="1" min="1"></div>
                    <div class="col-md-4 position-relative mb-3"><label class="form-label">Produit(s)</label><div class="input-group"><input type="text" name="produit[]" class="form-control produit-input" placeholder="Rechercher ou saisir un produit"><button type="button" class="btn btn-success btn-add-produit" data-bs-toggle="modal" data-bs-target="#produitModal">+</button></div><div class="autocomplete-results"></div></div>
                    <div class="col-md-2 mb-3"><label class="form-label">Prix/Kg</label><input type="number" name="prix[]" class="form-control prix-colis" placeholder="Prix"><div class="mt-2">Prix Total: <span class="prix-total">0</span></div></div>
                    <div class="col-md-2 mb-3"><label class="form-label">Type colis</label><select name="type_colis[]" class="form-control"><option value="standard">Standard</option><option value="fragile">Fragile</option></select></div>
                    <div class="col-md-2 mb-3"><label class="form-label">Devise</label><select name="devise[]" class="form-control devise-select"><option value="EUR">EUR</option><option value="FCFA">FCFA</option></select></div>
                </div>
                <div class="row">
                    <div class="col-md-6 dimension-section" style="display: none;"><label class="form-label">Dimensions (cm)</label><div class="d-flex gap-2"><input type="number" name="longueur[]" class="form-control longueur" placeholder="Longeur"><input type="number" name="largeur[]" class="form-control largeur" placeholder="Largeur"><input type="number" name="hauteur[]" class="form-control hauteur" placeholder="Hauteur"></div><div class="dimension-result mt-2" style="display: none; font-weight: bold;"></div></div>
                    <div class="col-md-6 poids-section" style="display: none;"><label class="form-label">Poids (kg)</label><input type="number" name="poids[]" class="form-control poids-colis" placeholder="Poids"></div>
                    <div class="col-md-6"><div class="mb-3"><label class="form-label">Commentaire</label><textarea name="description_colis[]" class="form-control" rows="3" placeholder="Description du colis"></textarea></div></div>
                </div>
                <div class="text-end mt-2"><button type="button" class="btn btn-danger remove-colis">Retirer ce colis</button></div>
            </div>
        </div>


        <!-- Étape 5 : Services et Récapitulatif -->
        <fieldset style="display: none;">
            <h5 class="text-center mb-4 mt-5">Services Additionnels et Récapitulatif</h5>
            <div class="form-section">
                <div class="row g-3">
                    <div class="col-md-6 position-relative"><label class="form-label">Service(s)</label><div class="input-group"><input type="text" name="service[]" class="form-control service-input" placeholder="Rechercher ou saisir un service"><button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#ServiceModal">+</button></div><div class="autocomplete-results"></div></div>
                    <div class="col-md-3"><label class="form-label">Prix</label><input type="number" name="prix_service[]" class="form-control prix-service" placeholder="Prix"><div class="mt-2">Prix Total: <span name="prix_service[]" class="prix-total-service">0</span></div></div>
                </div>
            </div>

            <h5 class="text-center mb-4 mt-5">Récapitulatif de votre envoi</h5>
            <div class="recapitulatif-section form-section">
                <div class="row">
                    <div class="col-md-6"><p><strong>Référence :</strong> <span id="recap_reference"></span></p></div>
                    <div class="col-md-6"><p><strong>Mode de Transit :</strong> <span id="recap_mode_transit"></span></p></div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6"><h6>Expéditeur :</h6><p><strong>Nom :</strong> <span id="recap_nom_expediteur"></span></p><p><strong>Téléphone :</strong> <span id="recap_tel_expediteur"></span></p><p><strong>Agence :</strong> <span id="recap_agence_expediteur"></span></p></div>
                    <div class="col-md-6"><h6>Destinataire :</h6><p><strong>Nom :</strong> <span id="recap_nom_destinataire"></span></p><p><strong>Téléphone :</strong> <span id="recap_tel_destinataire"></span></p><p><strong>Agence :</strong> <span id="recap_agence_destinataire"></span></p></div>
                </div>
                <hr>
                <h6>Détails des Colis et Services :</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light"><tr><th>Produit</th><th>Quantité</th><th id="recap_colis_prix_header">Prix Total</th></tr></thead>
                        <tbody id="recap_colis_details"></tbody>
                    </table>
                </div>
                <hr>
                <div class="row mt-3">
                    <div class="col-md-12 text-end"><h5 class="fw-bold">Total à payer : <span id="recap_total_a_payer">0.00</span> <span id="recap_devise"></span></h5></div>
                </div>
            </div>
        </fieldset>

        <!-- Étape 6 : Paiement -->
        <fieldset style="display: none;">
            <h5 class="text-center mb-4 mt-5">Informations du paiement</h5>
            <div class="alert alert-info text-center"><strong>Total à payer : <span id="payment_total" class="fw-bold fs-4">0.00</span> <span id="payment_devise" class="fw-bold fs-4">EUR</span></strong></div>
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
                <div class="payment-section mt-3" id="bank_section" style="display:none;"><h5>Détails Bancaires</h5><div class="row"><div class="col-md-4 mb-3"><label for="bank_nom_banque" class="form-label">Nom de la banque</label><input type="text" name="bank_nom_banque" id="bank_nom_banque" class="form-control"></div><div class="col-md-4 mb-3"><label for="bank_numero_compte" class="form-label">Numéro de compte</label><input type="text" name="bank_numero_compte" id="bank_numero_compte" class="form-control"></div><div class="col-md-4 mb-3"><label for="bank_montant" class="form-label">Montant</label><input type="number" name="montant_reçu" id="bank_montant" class="form-control" min="0"></div></div></div>
                <div class="payment-section mt-3" id="mobile_money_section" style="display:none;"><h5>Paiement Mobile Money</h5><div class="row"><div class="col-md-6 mb-3"><label for="mobile_operateur" class="form-label">Opérateur</label><select name="mobile_operateur" id="mobile_operateur" class="form-control"><option value="">-- Sélectionnez --</option><option value="orange_money">Orange Money</option><option value="wave">Wave</option><option value="mtn_money">MTN Money</option></select></div><div class="col-md-6 mb-3"><label for="mobile_numero_tel" class="form-label">Numéro de téléphone</label><input type="text" name="mobile_numero_tel" id="mobile_numero_tel" class="form-control"></div></div><button type="button" class="btn btn-primary mt-2" id="cinetpayButton" style="display:none;">Payer via Mobile Money</button></div>
                <div class="payment-section mt-3" id="cheque_section" style="display:none;"><h5>Détails du Chèque</h5><div class="row"><div class="col-md-6 mb-3"><label for="cheque_montant" class="form-label">Montant du chèque</label><input type="number" name="montant_reçu" id="cheque_montant" class="form-control" min="0"></div></div></div>
                <div class="payment-section mt-3" id="cash_section" style="display:none;"><h5>Paiement en Espèces</h5><div class="col-md-6 mb-3"><label for="cash_montant_recu" class="form-label">Montant reçu</label><input type="number" name="montant_reçu" id="cash_montant_recu" class="form-control" min="0"></div></div>
                <div class="payment-section mt-3" id="delivery_section" style="display:none;"><h5>Paiement à la Livraison</h5><p class="alert alert-warning">Le paiement sera effectué lors de la livraison du colis.</p></div>
            </div>
        </fieldset>

        <!-- Boutons de navigation -->
        <div class="text-end mt-4 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary btn-prev" style="display: none;">Précédent</button>
            <button type="button" class="btn btn-primary btn-next">Suivant</button>
            <button type="submit" class="btn btn-success btn-submit" style="display: none;">Valider l'envoi</button>
        </div>
    </form>

    {{-- MODALS --}}
    <div class="modal fade" id="ServiceModal" tabindex="-1" aria-labelledby="ServiceModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="ServiceModalLabel">Ajouter un Service</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><form id="serviceForm"><div class="mb-3"><label for="description_service" class="form-label">Description</label><input type="text" name="description" id="description_service" class="form-control"></div><input type="hidden" name="categorie" value="Service"><div class="mb-3"><label for="prix_unitaire_service" class="form-label">Prix Unitaire</label><input type="number" name="prix" id="prix_unitaire_service" class="form-control" min="0"></div><div class="mb-3"><label for="agence_service" class="form-label">Agence de destination</label><select name="agence" id="agence_service" class="form-control"><option value="" disabled selected>-- Sélectionnez --</option><option value="IPMS-SIMEX-CI Angre 8ème Tranche">DS Translog Angré 8ème Tranche</option><option value="AFT Agence Louis Bleriot">AFT Agence Louis Bleriot</option></select></div></form></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button><button type="button" class="btn btn-primary btn-save-service" data-url="{{ route('colis.store.service') }}">Créer</button></div></div></div></div>
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
                            <input type="text" name="description" id="description_produit" class="form-control">
                        </div>
                        <input type="hidden" name="categorie" value="Colis">
                        <div class="mb-3">
                            <label for="prix_unitaire" class="form-label">Prix Unitaire</label>
                            <input type="number" name="prix" id="prix_unitaire" class="form-control" min="0">
                        </div>
                        <div class="mb-3">
                            <label for="agence" class="form-label">Agence de destination</label>
                            <select name="agence" id="agence" class="form-control">
                                <option value="" disabled selected>-- Sélectionnez --</option>
                                {{-- <option value="IPMS-SIMEX-CI Angre 8ème Tranche">DS Translog Angré 8ème Tranche</option> --}}
                                <option value="Agence de Chine">Agence de Chine</option>
                                <option value="AFT Agence Louis Bleriot">AFT Agence Louis Bleriot</option>
                            </select></div></form></div><div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                <button type="button" class="btn btn-primary btn-save-produit" data-url="{{ route('colis.store.produit') }}">Créer</button>
                            </div>
                        </div>
                    </div>
                </div>
</section>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>

$(document).ready(function() {
    // =================================================================
    // INITIALISATION ET VARIABLES GLOBALES
    // =================================================================
    let currentStep = 0;
    const fieldsets = $("fieldset");
    const steps = $(".step");
    const mainForm = $('.form-container');
    const colisContainer = $("#colis-container");
    const colisTemplate = $("#colis-item-template .colis-item").clone(true, true); // Clone le template une seule fois
    
    // CORRECTION : On supprime le template du DOM pour éviter que ses champs ne soient soumis avec le formulaire.
    $("#colis-item-template").remove();

    let specialContainerMessage = null;
    let activeProduitInput = null;

    // =================================================================
    // NAVIGATION DU FORMULAIRE MULTI-ÉTAPES
    // =================================================================
    function updateProgressBar() {
        let percentage = (currentStep / (steps.length - 1)) * 100;
        $('.progress-steps').css('--progress-width', percentage + '%');
        steps.each(function(index) {
            $(this).toggleClass("active", index <= currentStep);
        });
    }

    function showStep(stepIndex) {
        if (stepIndex === 4) { // Récapitulatif
            updateRecapitulatif();
        }
        if (stepIndex === 5) { // Paiement
            updateRecapitulatif(); // Mettre à jour une dernière fois
            const total = $('#recap_total_a_payer').text();
            const devise = $('#recap_devise').text();
            $('#payment_total').text(total);
            $('#payment_devise').text(devise);
            const totalValue = parseFloat(total) || 0;
            $('input[name="montant_reçu"]').attr('max', totalValue).attr('placeholder', `Montant max: ${totalValue}`);
        }

        fieldsets.hide().eq(stepIndex).show();
        currentStep = stepIndex;
        updateProgressBar();

        $(".btn-prev").toggle(stepIndex > 0);
        const isLastStep = stepIndex === fieldsets.length - 1;
        $(".btn-next").toggle(!isLastStep);
        $(".btn-submit").toggle(isLastStep);
    }

    $(".btn-next").click(() => { if (currentStep < fieldsets.length - 1) showStep(currentStep + 1); });
    $(".btn-prev").click(() => { if (currentStep > 0) showStep(currentStep - 1); });
    steps.click(function() { showStep($(this).data("step")); });

    showStep(0);

    // =================================================================
    // GESTION DE LA SOUMISSION
    // =================================================================
    mainForm.on('submit', function(e) {
        e.preventDefault();
        if (specialContainerMessage) {
            Swal.fire({
                title: 'Attention !',
                text: specialContainerMessage + ". Voulez-vous continuer ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, valider !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) this.submit();
            });
        } else {
            this.submit();
        }
    });

    // =================================================================
    // ÉTAPE 1 : LOGIQUE DE TRANSPORT ET RÉFÉRENCE
    // =================================================================
    const modeTransitSelect = $('#mode_transit');
    const agenceExpediteurSelect = $('#agence_expediteur');

    modeTransitSelect.add(agenceExpediteurSelect).on('change', fetchReference);

    function fetchReference() {
        const mode = modeTransitSelect.val();
        const agence = agenceExpediteurSelect.val();
        specialContainerMessage = null;

        if (!mode || !agence) {
            $('#ref_container').hide();
            return;
        }

        $('#reference_colis_input').val('Chargement...');
        $('#ref_container').show();

        $.ajax({
            url: "{{ route('colis.getReference') }}",
            type: 'POST',
            dataType: 'json',
            data: {
                mode_transit: mode,
                agence_expediteur: agence,
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                if (data.error) {
                    $('#reference_colis_input').val(data.error);
                } else {
                    $('#ref_label').text(`Référence (${mode.charAt(0).toUpperCase() + mode.slice(1)})`);
                    $('#reference_colis_input').val(data.reference_colis);
                    if (data.message) specialContainerMessage = data.message;
                }
            },
            error: function() {
                $('#reference_colis_input').val('Impossible de générer la référence.');
            }
        });
    }

    // =================================================================
    // ÉTAPE 2 & 3 : LOGIQUE EXPÉDITEUR ET DESTINATAIRE
    // =================================================================
    $('#categorie_client').on('change', function() {
        const categorie = $(this).val();
        const isSociete = categorie === 'societe';
        $("#societe_expediteur_section, #societe_destinataire_section").toggle(isSociete);
        $("#societe_expediteur_section input, #societe_destinataire_section input, #societe_destinataire_section select").prop('disabled', !isSociete);
        $("#particulier_expediteur_section, #particulier_destinataire_section").toggle(!isSociete);
        $("#particulier_expediteur_section input, #particulier_destinataire_section input, #particulier_destinataire_section select").prop('disabled', isSociete);
    }).trigger('change');

    $('#client_select').on('keyup', function() {
        const query = $(this).val();
        const resultsContainer = $('#client_autocomplete_results');
        
        if (query.length < 2) {
            resultsContainer.hide();
            return;
        }

        $.ajax({
            url: "{{ route('colis.clients.search') }}",
            dataType: 'json',
            data: { q: query },
            success: function(data) {
                resultsContainer.empty();
                if (data.length > 0) {
                    data.forEach(client => {
                        const clientName = client.category === 'societe' ? client.first_name : `${client.first_name} ${client.last_name}`;
                        const item = $(`<div class="autocomplete-item"><strong>${clientName}</strong><br><small class="text-muted">${client.tel}</small></div>`);
                        
                        item.data('client', client).on('click', function() {
                            const selectedClient = $(this).data('client');
                            const selectedClientName = selectedClient.category === 'societe' ? selectedClient.first_name : `${selectedClient.first_name} ${selectedClient.last_name}`;
                            
                            $('#client_select').val(selectedClientName);
                            resultsContainer.hide();
                            $('#categorie_client').val(selectedClient.category).trigger('change');

                            if (selectedClient.category === 'particulier') {
                                $('#nom_expediteur').val(selectedClient.first_name);
                                $('#prenom_expediteur').val(selectedClient.last_name);
                                $('#email_expediteur').val(selectedClient.email);
                                $('#tel_expediteur').val(selectedClient.tel);
                            } else if (selectedClient.category === 'societe') {
                                $('#nom_societe_expediteur').val(selectedClient.first_name);
                                $('#email_societe_expediteur').val(selectedClient.email);
                                $('#tel_expediteur_societe').val(selectedClient.tel);
                                $('#adresse_expediteur_societe').val(selectedClient.adresse);
                            }
                        });
                        resultsContainer.append(item);
                    });
                    resultsContainer.show();
                } else {
                    resultsContainer.html('<div class="autocomplete-item text-danger">Aucun client trouvé.</div>').show();
                }
            },
            error: () => resultsContainer.html('<div class="autocomplete-item text-danger">Erreur lors de la recherche.</div>').show()
        });
    });

    // =================================================================
    // ÉTAPE 4 : LOGIQUE COLIS (AJOUT, SUPPRESSION, CALCULS)
    // =================================================================
    const agenceOptionsByMode = {
        maritime: { value: "IPMS-SIMEX-CI", label: "DS Translog Carrefour Angré" },
        aerien: { value: "IPMS-SIMEX-CI Angre 8ème Tranche", label: "DS Translog Angré 8ème Tranche" }
    };

    function updateDynamicFields() {
        const mode = $("#mode_transit").val();
        $(".dimension-section").toggle(mode === "maritime");
        $(".poids-section").toggle(mode === "aerien");
        
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
        let devise = "EUR", disabled = false;
        if (agence === 'Agence de Chine') { devise = 'FCFA'; disabled = true; } 
        else if (agence === 'AFT Agence Louis Bleriot') { devise = 'EUR'; disabled = true; }
        $(".devise-select").val(devise).prop('disabled', disabled).css('background-color', disabled ? '#e9ecef' : '');
    }

    $("#mode_transit, #agence_expediteur").on('change', function() {
        updateDynamicFields();
        $('.colis-item').each(function() { updateTotalForColis($(this)); });
    });
    
    updateDynamicFields();

    function attachColisEventListeners(colisElement) {
        initAutocomplete(colisElement.find(".produit-input"), "{{ route('colis.recherche.auto') }}", 'Colis');
        colisElement.on('input', '.quantite-colis, .prix-colis, .poids-colis', function() {
            updateTotalForColis($(this).closest('.colis-item'));
        });
        colisElement.on("input", ".hauteur, .largeur, .longueur", function() {
            const parent = $(this).closest(".dimension-section");
            const h = parent.find(".hauteur").val(), la = parent.find(".largeur").val(), lo = parent.find(".longueur").val();
            const resultText = h && la && lo ? `${lo}x${la}x${h} cm` : '';
            parent.find(".dimension-result").text(resultText).toggle(!!resultText);
        });
    }

    function updateTotalForColis(colisElement) {
        const mode = $('#mode_transit').val();
        const prixUnitaire = parseFloat(colisElement.find(".prix-colis").val()) || 0;
        let prixTotal = 0;
        
        if (mode === 'aerien') {
            const poids = parseFloat(colisElement.find(".poids-colis").val()) || 0;
            prixTotal = poids * prixUnitaire;
        } else { // maritime ou non défini
            const quantite = parseFloat(colisElement.find(".quantite-colis").val()) || 0;
            prixTotal = quantite * prixUnitaire;
        }
        colisElement.find(".prix-total").text(prixTotal.toFixed(2));
    }
    
    function updateRemoveButtonVisibility() {
        colisContainer.find('.remove-colis').toggle(colisContainer.find('.colis-item').length > 1);
    }
    
    $(".add-colis").click(function() {
        const newColis = colisTemplate.clone(true, true);
        newColis.find("input, textarea").val("");
        newColis.find(".quantite-colis").val("1");
        newColis.find(".prix-total").text("0");
        newColis.find(".dimension-result").hide().text('');
        newColis.find('.autocomplete-results').empty().hide();
        colisContainer.append(newColis);

        // MODIFICATION CI-DESSOUS : C'est la correction clé.
        // On attache les écouteurs d'événements (y compris l'autocomplétion)
        // à la nouvelle ligne de colis qui vient d'être ajoutée.
        attachColisEventListeners(newColis);
        
        updateDynamicFields();
        updateRemoveButtonVisibility();
    });

    colisContainer.on("click", ".remove-colis", function() {
        $(this).closest(".colis-item").remove();
        updateRemoveButtonVisibility();
    });

    // Attacher les écouteurs au premier colis déjà présent sur la page.
    attachColisEventListeners(colisContainer.find(".colis-item:first"));
    updateRemoveButtonVisibility();

    // =================================================================
    // LOGIQUE DEVIS
    // =================================================================
    $("#devis_reference_autocomplete").autocomplete({
        source: (request, response) => {
            $.ajax({
                url: "{{ route('colis.devis.search') }}",
                dataType: "json",
                data: { term: request.term },
                success: data => response(data)
            });
        },
        minLength: 2,
        select: (event, ui) => {
            event.preventDefault();
            if (ui.item && ui.item.id) {
                $(event.target).val(ui.item.label);
                $('#selected_devis_id').val(ui.item.id);
                loadDevisData(ui.item.id);
            }
        }
    });

    function loadDevisData(programmeId) {
        const url = "{{ route('colis.devis.getItems', ':id') }}".replace(':id', programmeId);
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                colisContainer.empty();
                if (response.items && response.items.length > 0) {
                    response.items.forEach(item => addDevisColisRow(item));
                } else {
                    $(".add-colis").click();
                }
                updateRemoveButtonVisibility();
                updateDynamicFields();
                if (response.programme) {
                    $('#categorie_client').val('particulier').trigger('change');
                }
            },
            error: () => Swal.fire('Erreur', 'Impossible de récupérer les informations du devis.', 'error')
        });
    }

    function addDevisColisRow(item = {}) {
        let newRow = colisTemplate.clone(true, true);
        
        newRow.find('input[name="produit[]"]').val(item.description || item.service || '');
        newRow.find('input[name="quantite_colis[]"]').val(item.quantite || 1);
        newRow.find('input[name="prix[]"]').val(item.prix || item.prix_unitaire || 0);
        newRow.find('textarea[name="description_colis[]"]').val(item.commentaire || '');
        newRow.find('input[name="poids[]"]').val(item.poids || '');
        newRow.find('input[name="longueur[]"]').val(item.longueur || '');
        newRow.find('input[name="largeur[]"]').val(item.largeur || '');
        newRow.find('input[name="hauteur[]"]').val(item.hauteur || '');

        colisContainer.append(newRow);
        
        // Attacher les écouteurs à cette nouvelle ligne
        attachColisEventListeners(newRow);
        
        // Déclencher le calcul du total
        newRow.find('.quantite-colis, .prix-colis, .poids-colis').trigger('input');
        newRow.find(".hauteur, .largeur, .longueur").trigger('input');
    }
    
    // =================================================================
    // LOGIQUE GÉNÉRIQUE (AUTOCOMPLETE, MODALS, ETC.)
    // =================================================================
    function initAutocomplete(inputElement, url, categorie) {
        inputElement.on("keyup", function() {
            const query = $(this).val().trim();
            const resultsContainer = inputElement.closest('.position-relative').find('.autocomplete-results');
            if (query.length < 2) { resultsContainer.empty().hide(); return; }
            $.ajax({
                url: url, type: "GET", dataType: "json", data: { query: query, categorie: categorie },
                success: function(data) {
                    resultsContainer.empty().show();
                    if (data.length > 0) {
                        $.each(data, (index, item) => {
                            $('<div class="autocomplete-item"></div>').text(item.description).on('click', function() {
                                inputElement.val(item.description);
                                const itemContainer = inputElement.closest('.colis-item, .form-section');
                                const prixInput = categorie === 'Colis' ? itemContainer.find('.prix-colis') : itemContainer.find('.prix-service');
                                prixInput.val(parseFloat(item.prix)).trigger('input'); // Trigger input pour le calcul
                                resultsContainer.empty().hide();
                            }).appendTo(resultsContainer);
                        });
                    } else { resultsContainer.html('<div class="autocomplete-item text-danger">Aucun résultat.</div>'); }
                }
            });
        });
    }

    initAutocomplete($('.service-input'), "{{ route('colis.recherche.auto.service') }}", 'Service');
    $('.prix-service').on('input', function() { $('.prix-total-service').text((parseFloat($(this).val()) || 0).toFixed(2)); });

    $(document).on("click", ".btn-add-produit", function() {
        activeProduitInput = $(this).siblings(".produit-input");
        $("#description_produit").val(activeProduitInput.val());
    });

    $(".btn-save-produit, .btn-save-service").on("click", function() {
        const isService = $(this).hasClass('btn-save-service');
        const form = isService ? $("#serviceForm") : $("#produitForm");
        const data = {
            description: form.find("input[name='description']").val().trim(),
            prix: parseFloat(form.find("input[name='prix']").val()),
            agence: form.find("select[name='agence']").val(),
            categorie: isService ? 'Service' : 'Colis',
            _token: '{{ csrf_token() }}'
        };

        if (!data.description || isNaN(data.prix) || data.prix <= 0) {
            Swal.fire('Champs Incomplets', 'Veuillez remplir tous les champs correctement.', 'warning');
            return;
        }

        const btn = $(this);
        btn.prop("disabled", true).text("Enregistrement...");

        $.ajax({
            url: btn.data("url"), type: "POST", data: data, dataType: 'json',
            success: function(response) {
                Swal.fire('Succès!', response.message, 'success');
                const modal = isService ? $("#ServiceModal") : $("#produitModal");
                if (isService) {
                    $('.service-input').val(data.description);
                    $('.prix-service').val(data.prix).trigger('input');
                } else if (activeProduitInput) {
                    const colisItem = activeProduitInput.closest('.colis-item');
                    activeProduitInput.val(data.description);
                    colisItem.find('.prix-colis').val(data.prix).trigger('input');
                }
                modal.modal("hide");
                form[0].reset();
            },
            error: xhr => Swal.fire('Erreur', xhr.responseJSON?.message || "Une erreur est survenue.", 'error'),
            complete: () => btn.prop("disabled", false).text("Créer")
        });
    });

    // =================================================================
    // ÉTAPE 5 : RÉCAPITULATIF
    // =================================================================
    function updateRecapitulatif() {
        const isSociete = $('#categorie_client').val() === 'societe';
        const devise = $('.devise-select:first').val() || 'EUR';
        let totalAPayer = 0;

        $('#recap_reference').text($('#reference_colis_input').val() || 'N/A');
        $('#recap_mode_transit').text($('#mode_transit option:selected').text() || 'N/A');
        $('#recap_agence_expediteur').text($('#agence_expediteur option:selected').text() || 'N/A');
        $('#recap_agence_destinataire').text($('#agence_destinataire option:selected').text() || 'N/A');

        const nomExp = isSociete ? $('#nom_societe_expediteur').val() : `${$('#nom_expediteur').val()} ${$('#prenom_expediteur').val()}`;
        const telExp = isSociete ? $('#tel_expediteur_societe').val() : $('#tel_expediteur').val();
        $('#recap_nom_expediteur').text(nomExp.trim() || 'N/A');
        $('#recap_tel_expediteur').text(telExp || 'N/A');

        const nomDest = isSociete ? $('#nom_societe_destinataire').val() : `${$('#nom_destinataire').val()} ${$('#prenom_destinataire').val()}`;
        const codePays = isSociete ? $('select[name="country_code_societe"]').val() : $('select[name="country_code_particulier"]').val();
        const telDestNum = isSociete ? $('input[name="tel_destinataire_societe"]').val() : $('input[name="tel_destinataire"]').val();
        $('#recap_nom_destinataire').text(nomDest.trim() || 'N/A');
        $('#recap_tel_destinataire').text(telDestNum ? `${codePays} ${telDestNum}` : 'N/A');
        
        const colisDetailsContainer = $('#recap_colis_details').empty();
        $('#recap_colis_prix_header').text(`Prix Total (${devise})`);

        $('.colis-item').each(function() {
            const nomProduit = $(this).find(".produit-input").val().trim();
            if (!nomProduit) return;
            const quantite = parseInt($(this).find(".quantite-colis").val()) || 1;
            const totalLigne = parseFloat($(this).find(".prix-total").text()) || 0;
            totalAPayer += totalLigne;
            colisDetailsContainer.append(`<tr><td>${nomProduit}</td><td>${quantite}</td><td>${totalLigne.toFixed(2)}</td></tr>`);
        });

        const prixService = parseFloat($('.prix-total-service').text()) || 0;
        const descService = $('.service-input').val().trim();
        if (prixService > 0 && descService) {
            totalAPayer += prixService;
            colisDetailsContainer.append(`<tr class="table-info"><td>${descService} <em>(Service)</em></td><td>1</td><td>${prixService.toFixed(2)}</td></tr>`);
        }

        $('#recap_total_a_payer').text(totalAPayer.toFixed(2));
        $('#recap_devise').text(devise);
    }
    
    // =================================================================
    // ÉTAPE 6 : PAIEMENT
    // =================================================================
    $('#mode_payement').on('change', function() {
        $('.payment-section').hide();
        const selectedMethod = $(this).val();
        if (selectedMethod) $('#' + selectedMethod + '_section').slideDown();
    });

    $('#mobile_operateur').on('change', function() {
        $('#cinetpayButton').toggle($(this).val() !== '');
    });
    
    // Cacher les résultats d'autocomplétion si on clique ailleurs
    $(document).on('click', e => {
        if (!$(e.target).closest('.position-relative').length) {
            $('.autocomplete-results').hide();
        }
    });
});
</script>

<style>
    .form-container {
        max-width: 95%;
        margin: auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    .form-section {
        background-color: #f8f9fa;
        padding: 20px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    fieldset {
        border: none;
        padding: 0;
    }
    .progress-bar-container {
        width: 100%;
        margin-bottom: 40px;
    }
    .progress-steps {
        --progress-width: 0%;
        display: flex;
        justify-content: space-between;
        list-style: none;
        padding: 0;
        margin: 0;
        position: relative;
    }
    .progress-steps::before,
    .progress-steps::after {
        content: '';
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        height: 4px;
        width: 100%;
        background-color: #d8d8d8;
        z-index: 1;
    }
    .progress-steps::after {
        width: var(--progress-width);
        background-color: #28a745;
        z-index: 2;
        transition: width 0.4s ease;
    }
    .progress-steps .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 3;
        cursor: pointer;
    }
    .progress-steps .step::before {
        content: '';
        display: block;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #d8d8d8;
        border: 3px solid #d8d8d8;
        transition: background-color 0.4s ease, border-color 0.4s ease;
        margin-bottom: 5px;
    }
    .progress-steps .step span {
        font-size: 14px;
        color: #6c757d;
        text-align: center;
    }
    .progress-steps .step.active::before {
        background-color: #fff;
        border-color: #28a745;
    }
    .progress-steps .step.active span {
        color: #28a745;
        font-weight: bold;
    }
    .autocomplete-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1000;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 4px;
        max-height: 200px;
        overflow-y: auto;
        display: none;
    }
    .autocomplete-item {
        padding: 8px 12px;
        cursor: pointer;
    }
    .autocomplete-item:hover {
        background-color: #f0f0f0;
    }
</style>
@endsection