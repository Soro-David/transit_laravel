@extends('customer.layouts.index')

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
                            <option value="paiement_enlevement">Paiement à l'enlèvement</option>
                            <option value="paiement_livraison">Paiement à la livraison</option>
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
                            <select name="operateur_mobile" id="operateur_mobile" class="form-control" >
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
                            <input type="text" name="numero_tel" id="numero_tel" class="form-control" placeholder="Entrez le numéro de téléphone" >
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
                                <label for="nom_banque_cheque" class="form-label">Nom de la banque</label>
                                <input type="text" name="nom_banque_cheque" id="nom_banque_cheque" class="form-control" placeholder="Entrez le nom de la banque">
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
        </div>
        {{-- Bouton de soumission --}}
        <div class="text-end mt-4" id="submit_button_section" style="display: none;"> {{-- MODIFIÉ : Caché par défaut --}}
            <button type="submit" class="btn btn-primary">Confirmer le paiement</button>
        </div>
    </form>
</section>
{{-- Scripts JS --}}
{{-- NOUVEAU : Ajout de la bibliothèque SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.cinetpay.com/seamless/main.js"></script>
<script>
    $(document).ready(function () {
        const sections = $('.payment-section');
        const submitButtonSection = $('#submit_button_section');
        const cinetpayButton = $('#cinetpayButton');
        const operateurMobileSelect = $('#operateur_mobile');
        let genererQrcodeUrl = '';

        function hideAllSectionsAndButtons() {
            sections.hide();
            submitButtonSection.hide(); // MODIFIÉ : Le bouton est maintenant caché
            cinetpayButton.hide();
        }

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
                customer_name:"Wayne",
                customer_surname:"The",
                customer_email: "redfieldluise@gmail.com",
                customer_phone_number: "0708325027",
                customer_address : "BP 0024",
                customer_city: "Abidjan",
                customer_country : "CI",
                customer_state : "CI",
                customer_zip_code : "225",
            });
            CinetPay.waitResponse(function(data) {
                if (data.status == "REFUSED") {
                    Swal.fire("Échec", "Votre paiement a échoué. Veuillez réessayer.", "error");
                } else if (data.status == "ACCEPTED") {
                    Swal.fire("Succès", "Votre paiement a été effectué avec succès.", "success");
                    $('#paymentForm').append(`<input type="hidden" name="cinetpay_transaction_id" value="${data.cpm_trans_id}">`);
                    submitPaymentForm();
                }
            });
            CinetPay.onError(function(data) {
                console.log(data);
                Swal.fire("Erreur", "Erreur lors du paiement CinetPay. Veuillez réessayer.", "error");
            });
        }

        function submitPaymentForm() {
            const formData = $('#paymentForm').serializeArray();
            const formDataJson = {};
            formData.forEach(item => {
                formDataJson[item.name] = item.value;
            });

            if (formDataJson.mode_payement !== 'cash') {
                delete formDataJson.montant_reçu;
            }

            $.ajax({
                url: '{{ route('customer_colis.store.payement', ['id' => $colis->id]) }}',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formDataJson),
                success: function (response) {
                    // NOUVEAU : Utilisation de SweetAlert2 pour la confirmation
                    Swal.fire({
                        title: 'Opération réussie !',
                        text: response.message, // Affiche le message venant du contrôleur
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        // Redirection après que l'utilisateur ait cliqué sur OK
                        if (result.isConfirmed && response.redirect) {
                            window.location.href = response.redirect;
                        }
                    });
                },
                error: function (xhr) {
                    // NOUVEAU : Utilisation de SweetAlert2 pour l'erreur
                    Swal.fire({
                        title: 'Erreur !',
                        text: 'Une erreur s\'est produite lors de l\'enregistrement du paiement.',
                        icon: 'error',
                        confirmButtonText: 'Fermer'
                    });
                    console.error(xhr.responseText);
                }
            });
        }

        cinetpayButton.on('click', checkout);

        operateurMobileSelect.on('change', function() {
            if ($('#mode_payement').val() === 'mobile_money' && $(this).val() !== '') {
                cinetpayButton.show();
            } else {
                cinetpayButton.hide();
            }
        });

        $('#mode_payement').on('change', function () {
            hideAllSectionsAndButtons();
            const selectedMode = $(this).val();

            if (selectedMode === 'mobile_money') {
                $('#mobile_money_payment').show();
                operateurMobileSelect.prop('required', true);
            } else {
                // Pour tous les autres modes, y compris les nouveaux
                $(`#${selectedMode}_payment`).show();
                submitButtonSection.show();
                operateurMobileSelect.prop('required', false);
            }
        });

        $('#paymentForm').on('submit', function (event) {
            event.preventDefault();
            if ($('#mode_payement').val() !== 'mobile_money') {
                submitPaymentForm();
            }
        });

        // Cacher toutes les sections et boutons au chargement initial
        hideAllSectionsAndButtons();
    });
</script>
@php
    // Calculer le montant total pour tous les colis avec la même référence
    $colisWithSameReference = App\Models\Colis::where('reference_colis', $colis->reference_colis)
                                              ->where('expediteur_id', $colis->expediteur_id)
                                              ->get();
    $totalAmount = $colisWithSameReference->sum('prix_transit_colis');
@endphp
<input type="hidden" id="colisPrice" value="{{ $totalAmount }}"> {{-- Champ caché pour stocker le prix du colis --}}
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