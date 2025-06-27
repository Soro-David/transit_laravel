@extends('AFT_LOUIS_BLERIOT.layouts.agent')
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
                            <option value="delivery">Paiement à la livraison</option>
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
            <div id="mobile_money_payment" class="payment-section" style="display: none;">
                <h5 class="mb-3">Paiement Mobile Money</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="operateur_mobile" class="form-label">Opérateur Mobile</label>
                            <select name="operateur_mobile" id="operateur_mobile" class="form-control">
                                <option value="" disabled selected>-- Sélectionnez un opérateur --</option>
                                <option value="orange_money">Orange Money</option>
                                <option value="wave">Wave</option>
                                <option value="mtn_money">MTN Money</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="numero_tel" class="form-label">Numéro de téléphone</label>
                            <input type="text" name="numero_tel" id="numero_tel" class="form-control" placeholder="Entrez le numéro de téléphone">
                        </div>
                    </div>
                </div>
                <p>Cliquez sur le bouton ci-dessous pour effectuer le paiement via Mobile Money.</p>
                <button type="button" class="btn btn-primary" id="cinetpayButton" style="display: none;">Payer Par Mobile Money</button>
            </div>

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
                <label for="montant_reçu" class="form-label">Montant reçu (Montant total: {{ number_format($totalPrice ?? 0, 0, ',', ' ') }} EUR)</label>
                <input type="number" name="montant_reçu" id="montant_reçu"
                       class="form-control"
                       placeholder="Entrez le montant reçu (max: {{ number_format($totalPrice ?? 0, 0, ',', ' ') }} EUR)"
                       max="{{ $totalPrice ?? 0 }}" {{-- Utilisation de la variable $totalPrice --}}
                       min="0" {{-- Gardez si nécessaire --}}
                       step="1">
            </div>
        </div>
    </div>
</div>

            <!-- Section Paiement à la livraison -->
            <div id="delivery_payment" class="payment-section" style="display: none;">
                <h5 class="mb-3">Paiement à la Livraison</h5>
                <p>Le paiement sera effectué lors de la livraison du colis.</p>
                <button type="button" class="btn btn-primary" id="deliveryPaymentButton">Confirmer Paiement à la Livraison</button>
            </div>
        </div>

        {{-- Bouton de soumission fallback pour les autres modes de paiement (hors CinetPay et Livraison) --}}
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
        const cinetpayButton = $('#cinetpayButton');
        const operateurMobileSelect = $('#operateur_mobile');
        const deliveryPaymentButton = $('#deliveryPaymentButton');
        let genererQrcodeUrl = '';

        // Fonction pour cacher toutes les sections de paiement spécifiques
        function hideSections() {
            sections.hide();
            submitButtonSection.show();
            cinetpayButton.hide();
        }

        // Fonction checkout de CinetPay (inchangée)
        function checkout() {
            CinetPay.setConfig({
                apikey: '521006956621e4e7a6a3d16.70681548',
                site_id: '405886',
                notify_url: '{{ route('colis.cinetpay.notify') }}',
                mode: 'PRODUCTION'
            });
            CinetPay.getCheckout({
                transaction_id: Math.floor(Math.random() * 100000000).toString(),
                amount: parseFloat(document.getElementById('colisPrice').value),
                currency: 'XOF',
                channels: 'ALL',
                operator: operateurMobileSelect.val(),
                description: 'Paiement de colis',
                customer_name: "Wayne",
                customer_surname: "The",
                customer_email: "redfieldluise@gmail.com",
                customer_phone_number: "0708325027",
                customer_address: "BP 0024",
                customer_city: "Abidjan",
                customer_country: "CI",
                customer_state: "CI",
                customer_zip_code: "225",
            });
            CinetPay.waitResponse(function(data) {
                if (data.status == "REFUSED") {
                    alert("Votre paiement a échoué. Veuillez réessayer.");
                } else if (data.status == "ACCEPTED") {
                    alert("Votre paiement a été effectué avec succès.");
                    $('#paymentForm').append(`<input type="hidden" name="cinetpay_transaction_id" value="${data.cpm_trans_id}">`);
                    submitPaymentForm();
                }
            });
            CinetPay.onError(function(data) {
                console.log(data);
                alert("Erreur lors du paiement CinetPay. Veuillez réessayer.");
            });
        }

        // Fonction pour soumettre le formulaire de paiement via AJAX
        function submitPaymentForm(paymentType = null) {
            const formData = $('#paymentForm').serializeArray();
            const formDataJson = {};
            formData.forEach(item => {
                formDataJson[item.name] = item.value;
            });

            // Forcer le mode de paiement si spécifié
            if (paymentType === 'delivery') {
                formDataJson.mode_payement = 'delivery';
            }
            if (formDataJson.mode_payement === 'cash') {
                const prixColis = parseFloat($('#colisPrice').val()) || 0;
                const montantRecu = parseFloat(formDataJson.montant_reçu);

                if (isNaN(montantRecu)) {
                    alert('Veuillez entrer un montant numérique valide pour le paiement en espèces.');
                    return false; // Arrêter la soumission
                }
                // if (montantRecu < 100) { // <<<<----- Vérification du minimum
                //     alert('Le montant reçu en espèces doit être d\'au moins 100 FCFA.');
                //     $('#montant_reçu').addClass('is-invalid').focus(); // Indiquer l'erreur
                //     return false; // Arrêter la soumission
                // }
                if (montantRecu > prixColis) { // Vérification du maximum
                    alert('Le montant reçu ne peut pas dépasser le prix du colis (' + prixColis + ' FCFA).');
                     $('#montant_reçu').addClass('is-invalid').focus(); // Indiquer l'erreur
                    return false; // Arrêter la soumission
                }
                 // Si tout est OK pour cash, on peut continuer
                 $('#montant_reçu').removeClass('is-invalid'); // Nettoyer au cas où
            }
             // *** Fin Vérification Client-side ***

            // Supprimer 'montant_reçu' si mode_payement n'est pas 'cash'
            if (formDataJson.mode_payement !== 'cash') {
                delete formDataJson.montant_reçu;
            }

            $.ajax({
                url: '{{ route('aftlb_colis.store.payement') }}',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formDataJson),
                success: function (response) {
                    if (formDataJson.mode_payement === 'cinetpay') {
                        genererQrcodeUrl = response.redirect;
                        window.location.href = genererQrcodeUrl;
                    } else {
                        alert('Paiement enregistré avec succès !');
                        if (response.redirect) {
                            window.location.href = response.redirect;
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
        $('#montant_reçu').on('input', function() {
            const prixColis = parseFloat($('#colisPrice').val()) || 0; // Valeur par défaut 0
            const montantSaisi = parseFloat($(this).val());
            let isValid = true;
            let errorMessage = ''; // Pour un éventuel affichage d'erreur plus précis

            // Vérifier seulement si le mode est 'cash'
            if ($('#mode_payement').val() === 'cash') {
                 if (isNaN(montantSaisi)) {
                //     // Permettre la saisie, la validation backend attrapera si c'est vide mais requis
                //     // On ne bloque pas ici pour un champ vide, mais on pourrait si on voulait
                // } else if (montantSaisi < 100) { // <<<<----- Vérification du minimum
                //     isValid = false;
                //     errorMessage = 'Le montant doit être d\'au moins 100 FCFA.';
                //     console.log(errorMessage); // Pour débogage
                 } else if (montantSaisi > prixColis) { // Vérification du maximum
                    isValid = false;
                    errorMessage = 'Le montant ne peut pas dépasser ' + prixColis + ' FCFA.';
                    console.log(errorMessage); // Pour débogage
                }
            }

            // Gérer l'état visuel et le bouton de soumission
            if (!isValid && $('#mode_payement').val() === 'cash') { // Appliquer seulement si cash et invalide
                $(this).addClass('is-invalid');
                // Optionnel : Afficher errorMessage dans un span dédié
                // $('.montant-recu-error-span').text(errorMessage).show();
                $('#submit_button_section button[type="submit"]').prop('disabled', true);
            } else {
                $(this).removeClass('is-invalid');
                // Optionnel : Cacher le message d'erreur
                // $('.montant-recu-error-span').text('').hide();
                // Réactiver le bouton SEULEMENT si le mode sélectionné est 'cash'
                // (les autres modes gèrent leur propre état de bouton)
                if ($('#mode_payement').val() === 'cash') {
                    $('#submit_button_section button[type="submit"]').prop('disabled', false);
                }
            }
        });

        // Listener pour le bouton CinetPay
        cinetpayButton.on('click', function() {
            checkout();
        });

        // Listener pour le bouton Paiement à la livraison
        deliveryPaymentButton.on('click', function() {
            submitPaymentForm('delivery');
        });

        // Listener for operator select change
        operateurMobileSelect.on('change', function() {
            if ($('#mode_payement').val() === 'mobile_money' && $(this).val() !== '') {
                cinetpayButton.show();
            } else {
                cinetpayButton.hide();
            }
        });

        // Listener pour le changement de mode de paiement (affichage des sections)
        $('#mode_payement').on('change', function () {
            hideSections();
            const selectedMode = $(this).val();
            if (selectedMode === 'mobile_money') {
                $('#mobile_money_payment').show();
                submitButtonSection.hide();
                operateurMobileSelect.prop('required', true);
            } else if (selectedMode === 'delivery') {
                $('#delivery_payment').show();
                submitButtonSection.hide();
            } else {
                $(`#${selectedMode}_payment`).show();
                submitButtonSection.show();
                operateurMobileSelect.prop('required', false);
                cinetpayButton.hide();
            }
        });

        // Listener pour la soumission du formulaire
        $('#paymentForm').on('submit', function (event) {
            event.preventDefault();
            if ($('#mode_payement').val() !== 'mobile_money' && $('#mode_payement').val() !== 'delivery') {
                submitPaymentForm();
            }
        });

        hideSections();
    });
</script>
<input type="hidden" id="colisPrice" value="{{ $totalPrice ?? 0 }}">
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
    .progress-card {
        background-color: #fff;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
        margin-bottom: 20px;
    }
    .progress-container {
        width: 100%;
        background-color: #e0e0e0;
        border-radius: 25px;
        height: 30px;
        margin: 20px 0;
    }
    .progress-bar {
        height: 100%;
        width: 40%;
        background-color: #4caf50;
        border-radius: 25px;
        text-align: center;
        color: white;
        line-height: 30px;
    }
</style>
@endsection