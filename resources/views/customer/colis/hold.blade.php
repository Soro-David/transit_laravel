@extends('customer.layouts.index')
@section('content-header')
@section('content')
<section class="py-3">
    <form action="" method="POST" class="mt-4">
        @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                        <h4 class="text-left mt-4">Liste des colis en attente</h4><br>
                        <div id="products-container">
                            <div class="table-responsive">
                                <table id="productTable" class="display">
                                    <thead>
                                        <tr>
                                            <th>Reference colis</th>
                                            <th>Nom Expéditeur</th>
                                            <th>Email Expéditeur</th>
                                            <th>Agence Expéditeur</th>
                                            <th>Nom Destinataire</th>
                                            <th>Email Destinataire</th>
                                            <th>Agence Destinataire</th>
                                            {{-- <th> Status</th> --}}
                                            <th> Etat</th>
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
                        </div>
                    </div>
                </div>
            </div>
    </form>
</div>

{{-- les Modals --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Modifier les informations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" action="/users/{id}/edit" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="nom_expediteur" class="form-label">Nom destinataire</label>
                                <input type="text" name="nom_expediteur" id="nom_expediteur" 
                                        value="{{ old('nom_expediteur') }}" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="prenom_expediteur" class="form-label">Prénom destinataire</label>
                                <input type="text" name="prenom_expediteur" id="prenom_expediteur" 
                                        value="{{ old('prenom_expediteur') }}" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="prenom_expediteur" class="form-label">E-mail destinataire</label>
                                <input type="text" name="prenom_expediteur" id="prenom_expediteur" 
                                        value="{{ old('prenom_expediteur') }}" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="nom_expediteur" class="form-label">Agence destination</label>
                                <input type="text" name="nom_expediteur" id="nom_expediteur" 
                                        value="{{ old('nom_expediteur') }}" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="prenom_expediteur" class="form-label">Agence expédition</label>
                                <input type="text" name="prenom_expediteur" id="prenom_expediteur" 
                                        value="{{ old('prenom_expediteur') }}" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="prenom_expediteur" class="form-label">Status</label>
                                <input type="text" name="prenom_expediteur" id="prenom_expediteur" 
                                        value="{{ old('prenom_expediteur') }}" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-center">
                <h5 class="modal-title" id="paymentModalLabel">Effectuer un paiement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="paymentForm" action="#" method="POST">
                    @csrf
                    <div class="form-section">
                        <div class="mb-3">
                            <label for="mode_payement" class="form-label">Sélectionnez le mode de paiement</label>
                            <select name="mode_payement" id="mode_payement" class="form-control" required>
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
                                <label for="transaction_id" class="form-label">ID de transaction</label>
                                <input type="text" name="transaction_id" id="transaction_id" class="form-control" placeholder="Entrez l'ID de transaction">
                            </div>
                        </div>
                        <div id="mobile_money_payment" class="payment-section" style="display: none;">
                            <h5 class="mb-3">Détails Mobile Money</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tel" class="form-label">Numéro de téléphone</label>
                                        <input type="tel" name="tel" id="tel" class="form-control" placeholder="Entrez votre numéro de téléphone">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="operateur" class="form-label">Opérateur</label>
                                        <select name="operateur" id="operateur" class="form-control">
                                            <option value="mtn">MTN</option>
                                            <option value="orange">Orange</option>
                                            <option value="airtel">Moov</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="transaction_id" class="form-label">ID de transaction</label>
                                <input type="text" name="transaction_id" id="transaction_id" class="form-control">
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
                                        <label for="nom_banque" class="form-label">Nom de la banque</label>
                                        <input type="text" name="nom_banque" id="nom_banque" class="form-control" placeholder="Entrez le nom de la banque">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- <div id="cash_payment" class="payment-section" style="display: none;">
                            <h5 class="mb-3">Détails Espèces</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="montant_reçu" class="form-label">Montant reçu</label>
                                        <input type="number" name="montant_reçu" id="montant_reçu" class="form-control" placeholder="Entrez le montant reçu">
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary">Confirmer le paiement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


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
                url: '{{route('colis.store.payement')}}',
                method: 'POST',
                data: formData,
                success: function (response) {
                    alert('Paiement enregistré avec succès !');
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
        max-width: 850px;
        margin: auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    .form-section {
        margin-bottom: 20px;
    }
    .payment-section {
        border: 1px solid #ddd;
        padding: 15px;
        border-radius: 5px;
        margin-top: 10px;
        display: none;
    }
</style>


<!-- Script JavaScript -->
<script>
   $(document).ready(function() {
    var table = $("#productTable").DataTable({
        responsive: true, // Rend la table responsive
        language: {
                url: "{{ asset('js/fr-FR.json') }}" // Chemin local vers le fichier
            },
        ajax: {
            url: '{{ route("customer_colis.get.colis") }}',

        },
        columns: [
            { data: 'reference_colis' },
            {
                data: null,
                render: function (data, type, row) {
                    return row.nom_expediteur + ' ' + row.prenom_expediteur;
                }
            },
            { data: 'expediteur_email' },
            { data: 'expediteur_agence' },
            {
                data: null,
                render: function (data, type, row) {
                    return row.nom_destinataire + ' ' + row.prenom_destinataire;
                }
            },
            { data: 'destinataire_email' },
            { data: 'destinataire_agence' },
            { data: 'created_at' },
            { data: 'action', orderable: false, searchable: false }
        ],
    });

    // $(".add-product").on("click", function() {
    //     var description = $("#description").val();
    //     var quantite = $("#quantite").val();
    //     var dimension = $("#dimension").val();
    //     var prix = $("#prix").val();
    //     if (description && quantite && dimension && prix) {
    //         $.ajax({
    //             url: '{{ route("customer_colis.get.colis") }}',
    //             method: "POST",
    //             data: {
    //                 description: description,
    //                 quantite: quantite,
    //                 dimension: dimension,
    //                 prix: prix,
    //                 _token: "{{ csrf_token() }}"
    //             },
    //             success: function(response) {
    //                 table.row
    //                     .add([
    //                         response.description,
    //                         response.quantite,
    //                         response.dimension,
    //                         response.prix
    //                     ])
    //                     .draw(false);
    //             }
    //         });
    //     }
    // });
});
</script>
@endsection
