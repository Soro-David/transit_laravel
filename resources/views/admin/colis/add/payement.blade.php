@extends('admin.layouts.admin')
@section('content-header')
@endsection
@section('content')
<section class="p-4 mx-auto">
    <form id="paymentForm" action="#" method="POST" class="form-container">
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
                            <option value="cinetpay">CinetPay</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        {{-- Sections dynamiques --}}
        <div class="form-section">
                 <!-- Section Paiement Bancaire -->
                 <div class="row payment-section" id="bank_payment" style="display: none;">
                    <h5 class="mb-3">Détails bancaires</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="numero_compte" class="form-label">Numéro de compte</label>
                                <input type="text" name="numero_compte" id="numero_compte"
                                       class="form-control" placeholder="Entrez le numéro de compte">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom_banque" class="form-label">Nom de la banque</label>
                                <input type="text" name="nom_banque" id="nom_banque"
                                       class="form-control" placeholder="Entrez le nom de la banque">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="transaction_id" class="form-label">ID de transaction</label>
                            <input type="text" name="transaction_id" id="transaction_id" class="form-control" placeholder="Entrez l'ID de transaction">
                        </div>
                    </div>
                </div>
                <!-- Section Mobile Money -->
               
                <!-- Section Chèque -->
                <div id="cheque_payment" class="payment-section" style="display: none;">
                    <h5 class="mb-3">Détails du Chèque</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="numero_cheque" class="form-label">Numéro du chèque</label>
                                <input type="text" name="numero_cheque" id="numero_cheque" class="form-control" placeholder="Entrez le numéro du chèque">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom_banque" class="form-label">Nom de la banque</label>
                                <input type="text" name="nom_banque" id="nom_banque" class="form-control" placeholder="Entrez le nom de la banque">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Section Espèces -->
                <div id="cash_payment" class="payment-section" style="display: none;">
                    <h5 class="mb-3">Détails Espèces</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="montant_reçu" class="form-label">Montant reçu</label>
                                <input type="number" name="montant_reçu" id="montant_reçu" class="form-control" placeholder="Entrez le montant reçu">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Section CinetPay -->
                <div id="cinetpay_payment" class="payment-section" style="display: none;">
                    <h5 class="mb-3">Paiement CinetPay</h5>
                    <p>Cliquez sur le bouton ci-dessous pour effectuer le paiement via CinetPay.</p>
                    <button type="button" class="btn btn-primary" id="cinetpayButton">Payer avec CinetPay</button>
                </div>
        </div>
        {{-- Bouton de soumission fallback pour les autres modes de paiement (hors CinetPay) --}}
        <div class="text-end mt-4" id="submit_button_section">
            <button type="submit" class="btn btn-primary">Confirmer le paiement</button>
        </div>
    </form>
</section>
{{-- Scripts JS --}}
<script src="https://cdn.cinetpay.com/seamless/main.js"></script>
<script>
    $(document).ready(function () {
        const sections = $('.payment-section');
        const submitButtonSection = $('#submit_button_section');
        let genererQrcodeUrl = ''; // Variable pour stocker l'URL de redirection vers generer_qrcode

        // Fonction pour cacher toutes les sections de paiement spécifiques
        function hideSections() {
            sections.hide();
            submitButtonSection.show();
        }

        // Fonction checkout de CinetPay (inchangée)
        function checkout() {
            CinetPay.setConfig({
                apikey: '521006956621e4e7a6a3d16.70681548', // Remplacez par votre clé API CinetPay
                site_id: '405886', // Remplacez par votre ID de site CinetPay
                notify_url: '{{ route('colis.cinetpay.notify') }}', // Route de notification CinetPay
                mode: 'PRODUCTION' // ou 'PRODUCTION' selon l'environnement
            });
            CinetPay.getCheckout({
                transaction_id: Math.floor(Math.random() * 100000000).toString(), // ID de transaction unique
                amount: 100, // Montant (à rendre dynamique)
                currency: 'XOF',
                channels: 'ALL',
                description: 'Test de paiement',   
                 //Fournir ces variables pour le paiements par carte bancaire
                customer_name:"Wayne",//Le nom du client
                customer_surname:"The",//Le prenom du client
                customer_email: "redfieldluise@gmail.com",//l'email du client
                customer_phone_number: "0708325027",//l'email du client
                        customer_address : "BP 0024",//addresse du client
                        customer_city: "Abidjan",// La ville du client
                        customer_country : "CI",// le code ISO du pays
                        customer_state : "CI",// le code ISO l'état
                        customer_zip_code : "225", // code postal
            });
            CinetPay.waitResponse(function(data) {
                console.log(data);
                if (data.status == "REFUSED") {
                    alert("Votre paiement a échoué. Veuillez réessayer.");
                } else if (data.status == "ACCEPTED") {
                    alert("Votre paiement a été effectué avec succès.");
                    // Soumettre le formulaire AJAX automatiquement après succès CinetPay
                    submitPaymentForm(); // Appeler la fonction de soumission AJAX ici
                }
            });
            CinetPay.onError(function(data) {
                console.log(data);
                alert("Erreur lors du paiement CinetPay. Veuillez réessayer.");
            });
        }

        // Fonction pour soumettre le formulaire de paiement via AJAX
        function submitPaymentForm() {
            const formData = $('#paymentForm').serializeArray(); // Sérialiser les données du formulaire
            const formDataJson = {};
            formData.forEach(item => {
                formDataJson[item.name] = item.value;
            });
            console.log(JSON.stringify(formDataJson, null, 2)); // Log des données JSON (pour débogage)
            $.ajax({
                url: '{{route('colis.store.payment')}}', // Route pour enregistrer le paiement
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formDataJson),
                success: function (response) {
                    if ($('#mode_payement').val() === 'cinetpay') {
                        // Pour CinetPay, redirection après la soumission AJAX réussie
                        genererQrcodeUrl = response.redirect;
                        window.location.href = genererQrcodeUrl; // Rediriger vers la page de reçu
                    } else {
                        // Pour les autres modes de paiement, redirection immédiate
                        alert('Paiement enregistré avec succès !');
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            console.error('Aucune URL de redirection fournie');
                        }
                        $('#paymentForm')[0].reset();
                        hideSections();
                    }
                },
                error: function (xhr) {
                    alert('Une erreur s\'est produite lors de l\'enregistrement du paiement.');
                    console.error(xhr.responseText);
                }
            });
        }

        // Listener pour le bouton CinetPay
        $('#cinetpayButton').on('click', function() {
            checkout(); // Lancer le processus de paiement CinetPay
        });

        // Listener pour le changement de mode de paiement (affichage des sections)
        $('#mode_payement').on('change', function () {
            hideSections();
            const selectedMode = $(this).val();
            if (selectedMode === 'cinetpay') {
                $('#cinetpay_payment').show();
                submitButtonSection.hide(); // Cacher le bouton "Confirmer le paiement" pour CinetPay
            } else {
                $(`#${selectedMode}_payment`).show();
                submitButtonSection.show(); // Afficher pour les autres modes
            }
        });

        // Listener pour la soumission du formulaire (pour les modes de paiement autres que CinetPay)
        $('#paymentForm').on('submit', function (event) {
            event.preventDefault(); // Empêcher la soumission classique du formulaire
            if ($('#mode_payement').val() !== 'cinetpay') {
                submitPaymentForm(); // Soumettre le formulaire AJAX pour les autres modes
            } else {
                // Ne rien faire ici pour CinetPay, la soumission se fait après le paiement réussi dans waitResponse
            }
        });


        hideSections(); // Cacher les sections au chargement de la page
    });
</script>
{{-- CSS Personnalisé --}}
<style>
    /* ... Votre CSS personnalisé reste inchangé ... */
    .form-container {
        max-width: 95%;
        margin: auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    .form-section {
        margin-bottom: 0px;
    }
    .payment-section {
        border: 1px solid #ddd;
        padding: 15px;
        border-radius: 5px;
        margin-top: 10px;
        display: none;
    }
    body {
        background-color: #f7f7f7;
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



    /* Card contenant la barre de progression */
    .progress-card {
        background-color: #fff;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
        margin-bottom: 20px;
    }

    /* Conteneur de la barre de progression */
    .progress-container {
        width: 100%; /* Prend toute la largeur de l'écran */
        background-color: #e0e0e0;
        border-radius: 25px;
        height: 30px;
        margin: 20px 0;
    }

    /* Barre de progression */
    .progress-bar {
        height: 100%;
        width: 40%; /* Étape actuelle (40% pour la deuxième étape) */
        background-color: #4caf50;
        border-radius: 25px;
        text-align: center;
        color: white;
        line-height: 30px; /* Pour centrer le texte verticalement */
    }
</style>
@endsection