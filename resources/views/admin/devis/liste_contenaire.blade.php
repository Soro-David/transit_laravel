@extends('admin.layouts.admin')
@section('content-header')
@section('content')
<section class="py-3">
<div class="container">
    <h2 class="">Colis en attente</h2>
    <form action="" method="POST" class="mt-4">
        @csrf
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                            <div id="products-container">
                                <div class="table-responsive">
                                    <table id="productTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Description</th>
                                                <th>Expéditeur</th>
                                                <th>Quantité</th>
                                                <th>Dimensions</th>
                                                <th>Prix</th>
                                                <th>Status</th>
                                                <th>Destinataire</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="container">
                                    <h6 class="text-right mt-4">Prix total : <span id="prix-total">0</span> FCFA</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="border p-4 rounded shadow-sm">
                                            <div class="mb-3">
                                                <label for="commentaire1" class="form-label">Commentaire</label>
                                                <textarea name="commentaire1" id="commentaire1" rows="5" class="form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-8">
                                            <label for="reference_contenaire" class="form-label">Référence Contenaire</label>
                                            <input type="text" name="reference_contenaire" id="reference_contenaire" placeholder="Référence du contenaire" class="form-control">
                                        </div>
                                    </div>
                                    <div class="container text-right">
                                        <button type="submit" class="btn btn-danger mt-3">
                                            <i class="fas fa-times mr-2"></i> Fermer le conteneur
                                        </button>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
        
    </form>

    <!-- Modal View -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">Détails</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="destinataire" class="form-label">Destinataire</label>
                                <input type="text" id="destinataire" class="form-control" placeholder="Nom du destinataire" required>
                            </div>
                            <div class="col-md-6">
                                <label for="expediteur" class="form-label">Expéditeur</label>
                                <input type="text" id="expediteur" class="form-control" placeholder="Nom de l'expéditeur" required>
                            </div>
                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea id="description" class="form-control" placeholder="Description détaillée" rows="3" required></textarea>
                            </div>
                            <div class="col-md-3">
                                <label for="quantite" class="form-label">Quantité</label>
                                <input type="number" id="quantite" class="form-control" placeholder="Quantité" required>
                            </div>
                            <div class="col-md-3">
                                <label for="dimension" class="form-label">Dimensions</label>
                                <input type="text" id="dimension" class="form-control" placeholder="Dimensions" required>
                            </div>
                            <div class="col-md-3">
                                <label for="prix_unitaire" class="form-label">Prix Unitaire</label>
                                <input type="number" id="prix_unitaire" class="form-control" placeholder="Prix Unitaire" required>
                            </div>
                            <div class="col-md-3">
                                <label for="prix_total" class="form-label">Prix Total</label>
                                <input type="number" id="prix_total" class="form-control" placeholder="Prix Total" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-success add-product">Valider</button>
                            </div>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
    

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
<!-- Script JavaScript -->
<script>
    $(document).ready(function() {
    var table = $("#productTable").DataTable({
        responsive: true,
        language: {
            url: "//cdn.datatables.net/plug-ins/2.1.8/i18n/fr-FR.json"
        },
        ajax: '{{ route('colis.getColis') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'first_name', name: 'first_name' },
                    { data: 'email', name: 'email' },
                    { data: 'role', name: 'role' },
                    { data: 'role', name: 'role' },
                    { data: 'role', name: 'role' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                        ]
    });
    $(".add-product").on("click", function() {
        var description = $("#description").val();
        var quantite = $("#quantite").val();
        var dimension = $("#dimension").val();
        var prix = $("#prix").val();

        if (description && quantite && dimension && prix) {
            $.ajax({
                url: '{{ route("colis.store") }}',
                method: "POST",
                data: {
                    description: description,
                    quantite: quantite,
                    dimension: dimension,
                    prix: prix,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    table.row
                        .add([
                            response.description,
                            response.quantite,
                            response.dimension,
                            response.prix
                        ])
                        .draw(false);
                }
            });
        }
    });
});
        $(document).ready(function() {
            $('#paymentMethod').change(function() {
                $('.payment-details').hide();
                let paymentMethod = $(this).val();
                if (paymentMethod === 'cheque') {
                    $('#chequeDetails').show();
                } else if (paymentMethod === 'mobile_money') {
                    $('#mobileMoneyDetails').show();
                } else if (paymentMethod === 'banque') {
                    $('#banqueDetails').show();
                }
            });
        });

         // Préparer les données pour chaque action
    document.addEventListener('DOMContentLoaded', function () {
        // Exemple : Remplir les informations du modal d'édition
        document.querySelector('a[title="Edit"]').addEventListener('click', function () {
            document.getElementById('editField').value = "Valeur actuelle à modifier";
        });

        // Exemple : Afficher les détails dans le modal de visualisation
        document.querySelector('a[title="View"]').addEventListener('click', function () {
            document.getElementById('viewDetails').textContent = "Voici les détails de l'élément sélectionné.";
        });

        // Gestion du formulaire de paiement
        document.getElementById('paymentForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const selectedMethod = document.getElementById('paymentMethod').value;
            alert("Mode de paiement choisi : " + selectedMethod);
        });
    });
</script>

<style>
    table.dataTable {
        width: 100% !important;
    }
    table.dataTable th,
    table.dataTable td {
        white-space: nowrap;
    }
</style>
@endsection
