@extends('agent.layouts.agent')
@section('content-header')
@endsection
@section('content')
<section class="p-4 mx-auto">
    <form id="paymentForm" action="#" method="POST" class="form-container">
        @csrf
        <div class="progress-card">
            <h3>Progression de la soumission</h3>
            <div class="progress-container">
                <div class="progress-bar" style="width: 100%;">Étape 5 / 5</div>
            </div>
        </div>
        <div class="form-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="mode_payement" class="form-label">Sélectionnez le mode de paiement</label>
                        <select name="mode_payement" id="mode_payement" class="form-control" required>
                            <option value="" disabled selected>-- Sélectionnez le mode de paiement --</option>
                            <option value="bank">Paiement bancaire</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="cheque">Chèque</option>
                            <option value="cash">Espèces</option>
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
                    <h5 class="mb-3">Détails Mobile Money</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="numero_tel" class="form-label">Numéro de téléphone</label>
                                <input type="tel" name="numero_tel" id="numero_tel" 
                                       class="form-control" placeholder="Entrez votre numéro de téléphone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="operateur_mobile" class="form-label">Opérateur</label>
                                <select name="operateur_mobile" id="operateur_mobile" class="form-control">
                                    <option value="mtn">MTN</option>
                                    <option value="orange">Orange</option>
                                    <option value="airtel">Airtel</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="transaction_id" class="form-label">ID de transaction</label>
                        <input type="text" name="transaction_id" id="transaction_id" class="form-control">
                    </div>
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
                                <label for="montant_reçu" class="form-label">Montant reçu</label>
                                <input type="number" name="montant_reçu" id="montant_reçu" class="form-control" placeholder="Entrez le montant reçu">
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        {{-- Bouton de soumission --}}
        <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary">Confirmer le paiement</button>
        </div>
    </form>
</section>
{{-- Scripts JS --}}
<script>
    $(document).ready(function () {
        const sections = $('.payment-section');
        // Cacher toutes les sections
        function hideSections() {
            sections.hide();
        }
        // Changement de mode de paiement
        $('#mode_payement').on('change', function () {
            hideSections();
            const selectedMode = $(this).val();
            $(`#${selectedMode}_payment`).show();
        });
        $('#paymentForm').on('submit', function () {
            const formData = $(this).serializeArray();
            const formDataJson = {};
            formData.forEach(item => {
                formDataJson[item.name] = item.value;
            });
            console.log(JSON.stringify(formDataJson, null, 2));
            $.ajax({
                url: '{{route('agent_colis.store.payement')}}',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formDataJson),
                success: function (response) {
                    alert('Paiement enregistré avec succès !');
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        console.error('Aucune URL de redirection fournie');
                    }
                    $('#paymentForm')[0].reset();
                    hideSections();
                },
                error: function (xhr) {
                    alert('Une erreur s\'est produite lors de l\'enregistrement du paiement.');
                    console.error(xhr.responseText);
                }
            });
        });
        hideSections();
    });
</script>
{{-- CSS Personnalisé --}}
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
