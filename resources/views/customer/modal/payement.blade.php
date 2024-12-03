<!-- Modal Payment -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Mode de paiement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <h4 class="text-center mb-3">Paiement de Facture</h4>
                <form id="paymentForm">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Montant</label>
                        <input type="number" class="form-control" id="amount" placeholder="Montant" required>
                    </div>
                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label">Mode de Paiement</label>
                        <select class="form-control" id="paymentMethod" required>
                            <option value="" selected>Sélectionnez un mode de paiement</option>
                            <option value="cheque">Chèque</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="banque">Banque</option>
                        </select>
                    </div>
                    <div id="chequeDetails" class="payment-details d-none">
                        <div class="mb-3">
                            <label for="chequeNumber" class="form-label">Numéro de Chèque</label>
                            <input type="text" class="form-control" id="chequeNumber" placeholder="Numéro de Chèque">
                        </div>
                        <div class="mb-3">
                            <label for="chequeBank" class="form-label">Nom de la Banque</label>
                            <input type="text" class="form-control" id="chequeBank" placeholder="Nom de la Banque">
                        </div>
                    </div>
                    <div id="mobileMoneyDetails" class="payment-details d-none">
                        <div class="mb-3">
                            <label for="mobileMoneyNumber" class="form-label">Numéro Mobile Money</label>
                            <input type="text" class="form-control" id="mobileMoneyNumber" placeholder="Numéro Mobile Money">
                        </div>
                        <div class="mb-3">
                            <label for="mobileMoneyProvider" class="form-label">Fournisseur Mobile Money</label>
                            <input type="text" class="form-control" id="mobileMoneyProvider" placeholder="Fournisseur Mobile Money">
                        </div>
                    </div>
                    <div id="banqueDetails" class="payment-details d-none">
                        <div class="mb-3">
                            <label for="bankAccountNumber" class="form-label">Numéro de Compte Bancaire</label>
                            <input type="text" class="form-control" id="bankAccountNumber" placeholder="Numéro de Compte Bancaire">
                        </div>
                        <div class="mb-3">
                            <label for="bankName" class="form-label">Nom de la Banque</label>
                            <input type="text" class="form-control" id="bankName" placeholder="Nom de la Banque">
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Payer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>