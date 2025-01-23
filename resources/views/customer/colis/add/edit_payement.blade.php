@extends('customer.layouts.index')

@section('content-header')
    <h4 class="text-center">Effectuer le paiement pour le colis</h4>
@endsection

@section('content')
<section class="mx-auto">
    <form action="{{ route('customer_colis.store.payement', $colis->id) }}" id="paymentForm" method="post" class="form-container">
        @csrf
        <div class="form-section">
            <div class="mb-3">
                <label for="mode_payement" class="form-label">Sélectionnez le mode de paiement</label>
                <select name="mode_de_paiement" id="mode_payement" class="form-control" required>
                    <option value="" disabled selected>-- Sélectionnez le mode de paiement --</option>
                    <option value="bank">Paiement bancaire</option>
                    <option value="mobile_money">Mobile Money</option>
                    <option value="cheque">Chèque</option>
                </select>
            </div>

            <div id="bank_payment" class="payment-section" style="display: none;">
                <h5 class="mb-3">Détails bancaires</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="numero_compte" class="form-label">Numéro de compte</label>
                            <input type="text" name="numero_compte" id="numero_compte" class="form-control" placeholder="Entrez le numéro de compte">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nom_banque" class="form-label">Nom de la banque</label>
                            <input type="text" name="nom_banque" id="nom_banque" class="form-control" placeholder="Entrez le nom de la banque">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="transaction_id_bank" class="form-label">ID de transaction</label>
                    <input type="text" name="transaction_id" id="transaction_id_bank" class="form-control" placeholder="Entrez l'ID de transaction">
                </div>
            </div>

            <div id="mobile_money_payment" class="payment-section" style="display: none;">
                <h5 class="mb-3">Détails Mobile Money</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="numero_tel" class="form-label">Numéro de téléphone</label>
                            <input type="tel" name="numero_tel" id="numero_tel" class="form-control" placeholder="Entrez votre numéro de téléphone">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="operateur_mobile" class="form-label">Opérateur</label>
                            <select name="operateur_mobile" id="operateur_mobile" class="form-control">
                                <option value="mtn">MTN</option>
                                <option value="orange">Orange</option>
                                <option value="airtel">Moov</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="transaction_id_mobile" class="form-label">ID de transaction</label>
                    <input type="text" name="transaction_id" id="transaction_id_mobile" class="form-control" placeholder="Entrez l'ID de transaction">
                </div>
            </div>

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
                            <input type="text" name="nom_banque" id="nom_banque_cheque" class="form-control" placeholder="Entrez le nom de la banque">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="colis_id" value="{{ $colis->id }}">
        <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary">Confirmer le paiement</button>
        </div>
    </form>
</section>

{{-- Script SweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        $('#paymentForm').on('submit', function (e) {
            e.preventDefault(); // Empêche le rechargement de la page
            const formData = $(this).serialize();

            $.ajax({
                url: $(this).attr('action'), // Utilise l'URL du formulaire
                method: 'POST',
                data: formData,
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès',
                        text: response.message,
                    });
                    $('#paymentForm')[0].reset();
                    hideSections();
                },
                error: function (xhr) {
                    // Vérifiez si le code d'état est 400 pour un paiement déjà effectué
                    if (xhr.status === 400) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Attention',
                            text: xhr.responseJSON.message, // Message d'erreur renvoyé par le serveur
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Une erreur s\'est produite lors de l\'enregistrement du paiement.',
                        });
                    }
                    console.error(xhr.responseText);
                }
            });
        });

        hideSections(); // Cache les sections au chargement
    });
</script>

{{-- CSS Personnalisé --}}
<style>
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
</style>
@endsection