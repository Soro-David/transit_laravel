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

        <!-- Sélection du mode de paiement -->
        <div class="form-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="mode_de_paiement" class="form-label">Sélectionnez le mode de paiement</label>
                        <select name="mode_de_paiement" id="mode_de_paiement" class="form-control" required>
                            <option value="" disabled selected>-- Sélectionnez le mode de paiement --</option>
                            <option value="bank">Virément bancaire</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="cheque">Chèque</option>
                            {{-- <option value="cash">Espèces</option> --}}
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sections dynamiques -->
        <div class="form-section">
            <!-- Section Paiement Bancaire -->
            <div class="row payment-section" id="bank_payment" style="display: none;">
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
                            <input type="tel" name="numero_tel" id="numero_tel" class="form-control" placeholder="Entrez votre numéro de téléphone">
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
                    <label for="transaction_id_mobile" class="form-label">ID de transaction</label>
                    <input type="text" name="transaction_id" id="transaction_id_mobile" class="form-control" placeholder="Entrez l'ID de transaction">
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
                            <label for="nom_banque_cheque" class="form-label">Nom de la banque</label>
                            <input type="text" name="nom_banque" id="nom_banque_cheque" class="form-control" placeholder="Entrez le nom de la banque">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bouton de soumission -->
        <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary">Confirmer le paiement</button>
        </div>
    </form>
</section>

<!-- Script JavaScript -->
<script>
    $(document).ready(function () {
        const sections = $('.payment-section');

        // Fonction pour cacher toutes les sections de paiement
        function hideSections() {
            sections.hide();
        }
        
        // Lors du changement du mode de paiement, afficher la section correspondante
        $('#mode_de_paiement').on('change', function () {
            hideSections();
            const selectedMode = $(this).val();
            $('#' + selectedMode + '_payment').show();
        });
        
        // Gestion de la soumission du formulaire via AJAX
        $('#paymentForm').on('submit', function (e) {
            e.preventDefault(); // Empêche la soumission classique du formulaire
            const formData = $(this).serialize(); // Sérialisation des données du formulaire
            
            $.ajax({
                url: '{{ route('customer_colis.store.payement', ['id' => $colis->id]) }}',
                method: 'POST',
                data: formData,
                success: function (response) {
                    alert('Paiement enregistré avec succès !');
                    if (response.redirect) {
                        window.location.href = response.redirect;
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

<!-- Styles personnalisés -->
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
        background-color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
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
</style>
@endsection
