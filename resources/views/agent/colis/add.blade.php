@extends('agent.layouts.agent')
@section('content-header')
@endsection
        @csrf
<section>
    <form action="" method="post">
        @csrf
        <div class="container">
            <div class="border p-4 rounded shadow-sm" style="border-color: #ffffff;">
                <h4 class="text-center mb-4">
                    Informations Expéditeur et Destinataire
                </h4>
                <div class="row">
                    <div class="container">
                        <div class="d-flex justify-content-between">
                            <div class="text-left">
                                <button type="button" class="btn gradient-orange-blue" style="color: #fff;" data-bs-toggle="modal" data-bs-target="#info_expediteur">
                                    Ajouter l'expéditeur
                                </button>
                            </div>
                            <div class="text-right">
                                <button type="button" style="color: #fff;" class="btn gradient-orange-blue" data-bs-toggle="modal" data-bs-target="#info_destinataire">
                                    Ajouter le destinataire
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
         <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="border p-4 rounded shadow-sm" style="border-color: #ffffff;">
                        <h4 class="text-center mt-4">Expediteur & Destinataire</h4><br>
                        <div class="container mt-3">
                            <div class="row">
                                    <div class="col-md-4">
                                        <label for="expediteur-left" class="form-label">Expéditeur</label>
                                        <select id="expediteur-left" class="form-select select2">
                                            <option value="" disabled selected>Sélectionnez un Expéditeur</option>
                                            @foreach ($client_expediteurs as $client)
                                                <option value="{{ $client->id }}">{{ $client->nom }} {{ $client->prenom }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 ">
                                        <label for="expediteur-right" class="form-label">Destinataire</label>
                                        <select id="expediteur-right" class="form-select">
                                            <option value="" disabled selected>Sélectionnez un destinataire</option>
                                            @foreach ($client_destinataires as $client)
                                                <option value="{{$client->id}}">{{ $client->nom }} {{ $client->prenom }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                            </div>
                        </div><br>
                    </div>
                </div>
            </div>
            <input type="hidden" id="client_id" name="client_id">
        </div>
        <div id="products-container">
            <h4 class="text-center mt-4">Ajouter les produits</h4><br>
            <div id="dynamic-form">
                <div class="form-row align-items-end mb-3 product-row">
                    <div class="col-4">
                        <label for="description">Description</label>
                        <input type="text" name="description[]" class="form-control" placeholder="Description" required>
                    </div>
                    <div class="col">
                        <label for="quantite">Quantité</label>
                        <input type="number" name="quantite[]" class="form-control" placeholder="Quantité" required>
                    </div>
                    <div class="col">
                        <label for="dimension">Dimensions</label>
                        <input type="text" name="dimension[]" class="form-control" placeholder="Dimensions" required>
                    </div>
                    <div class="col">
                        <label for="prix_unitaire">Prix Unitaire</label>
                        <input type="number" name="prix_unitaire[]" class="form-control" placeholder="Prix Unitaire" required>
                    </div>
                    <div class="col">
                        <label for="prix_total">Prix Total</label>
                        <input type="number" name="prix_total[]" class="form-control" placeholder="Prix Total" required>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-success add-product" style="margin-top: 32px;">+</button>
                    </div>
                </div>
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
                    <button type="submit" class="btn btn-success mt-3">
                        <i class="fas fa-save mr-2"></i> Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </form>
</section>

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

 <!-- Modal View -->
 <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Détails</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
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
            </div>
        </div>
    </div>
</div>

 {{--  --}}
<div class="modal fade" id="info_destinataire" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('colis.store.destinataire')}}" method="post">
        @csrf
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="text-center flex-grow-1 m-0">Information du Destinataire</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nom</label>
                                    <input type="text" name="nom" id="nom" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Prénom</label>
                                    <input type="text" name="prenom" id="prenom" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="destinataire_email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="destinataire_telephone" class="form-label">Téléphone</label>
                                    <input type="text" name="telephone" id="telephone" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="agence" class="form-label">Agence</label>
                                    <select name="agence" id="agence" class="form-control">
                                        <option value="" disabled selected>-- Sélectionnez une agence --</option>
                                        @foreach ($agences as $agence)
                                            <option value="{{$agence->nom_agence}}">{{$agence->nom_agence}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lieu_livraison" class="form-label">Lieu de Livraison</label>
                                    <select name="lieu_livraison" id="lieu_livraison" class="form-control" required>
                                        <option value="">-- Sélectionnez le lieu de livraison --</option>
                                        <option value="angre">Angré</option>
                                        <option value="cocody">Cocody</option>
                                        <option value="yop">Yopougon</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>
                </div>
            </div>
        </div>
    </form>
</div>

{{--  --}}
<div class="modal fade" id="info_expediteur" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('colis.store.expediteur')}}" method="POST">
        @csrf
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="text-center flex-grow-1 m-0">Information de l'Expéditeur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nom</label>
                                    <input type="text" name="nom" id="nom" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Prénom</label>
                                    <input type="text" name="prenom" id="prenom" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expediteur_email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expediteur_telephone" class="form-label">Téléphone</label>
                                    <input type="text" name="telephone" id="telephone" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="adresse" class="form-label">Adresse</label>
                                    <input type="text" name="adresse" id="adresse" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type_facture" class="form-label">Agence:</label>
                                    <select name="agence" id="agence" class="form-control">
                                        <option value="" disabled selected>-- Sélectionnez une agence --</option>
                                        @foreach ($agences as $agence)
                                            <option value="{{$agence->nom_agence}}">{{$agence->nom_agence}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>
                </div>
            </div>
        </div>
    </form>
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
                    { data: 'created_at',
                        render: function(data, type, row) {
                            // Vérifiez si la date existe et la formater
                            if (data) {
                                var date = new Date(data);
                                // Retourne la date au format aa/mm/jj
                                var day = ('0' + date.getDate()).slice(-2);  // Ajoute un zéro si jour < 10
                                var month = ('0' + (date.getMonth() + 1)).slice(-2);  // +1 car les mois commencent à 0
                                var year = date.getFullYear().toString().slice(-2);  // On garde les deux derniers chiffres de l'année
                                return day + '/' + month + '/' + year;
                            }
                            return data;  // Si la date est vide, on retourne la donnée brute
                        }
                    },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                        ]
    });
    // Ajouter une nouvelle ligne lorsqu'on clique sur le bouton "+"
    $(document).on("click", ".add-product", function () {
        var newRow = `
            <div class="form-row align-items-end mb-3 product-row">
                <div class="col-4">
                    <input type="text" name="description[]" class="form-control" placeholder="Description" required>
                </div>
                <div class="col">
                    <input type="number" name="quantite[]" class="form-control" placeholder="Quantité" required>
                </div>
                <div class="col">
                    <input type="text" name="dimension[]" class="form-control" placeholder="Dimensions" required>
                </div>
                <div class="col">
                    <input type="number" name="prix_unitaire[]" class="form-control" placeholder="Prix Unitaire" required>
                </div>
                <div class="col">
                    <input type="number" name="prix_total[]" class="form-control" placeholder="Prix Total" required>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-danger remove-product" style="margin-top: 32px;">-</button>
                </div>
            </div>
        `;

        // Ajouter la nouvelle ligne après la dernière ligne
        $("#dynamic-form").append(newRow);
    });

    // Supprimer une ligne lorsqu'on clique sur le bouton "-"
    $(document).on("click", ".remove-product", function () {
        $(this).closest(".product-row").remove();
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
    // document.addEventListener('DOMContentLoaded', function () {
    //     // Exemple : Remplir les informations du modal d'édition
    //     document.querySelector('a[title="Edit"]').addEventListener('click', function () {
    //         document.getElementById('editField').value = "Valeur actuelle à modifier";
    //     });

    //     // Exemple : Afficher les détails dans le modal de visualisation
    //     document.querySelector('a[title="View"]').addEventListener('click', function () {
    //         document.getElementById('viewDetails').textContent = "Voici les détails de l'élément sélectionné.";
    //     });

    //     // Gestion du formulaire de paiement
    //     document.getElementById('paymentForm').addEventListener('submit', function (e) {
    //         e.preventDefault();
    //         const selectedMethod = document.getElementById('paymentMethod').value;
    //         alert("Mode de paiement choisi : " + selectedMethod);
    //     });
    // });

    // Recherche automatique 
$(document).ready(function () {
    $('#expediteur-left').select2({
        placeholder: "Sélectionnez un Expéditeur",
        allowClear: true, 
        width: '100%', 
        ajax: {
            url: "{{ route('colis.search.expediteurs') }}",
            type: "GET",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    term: params.term 
                };
            },
            processResults: function (data) {
                if (Array.isArray(data)) {
                    return {
                        results: data.map(function (item) {
                            return {
                                id: item.id, 
                                text: item.nom + ' ' + item.prenom 
                            };
                        })
                    };
                } else {
                    console.error("Les données renvoyées ne sont pas un tableau valide");
                    return { results: [] };
                }
            },
            error: function (xhr, status, error) {
                console.error("Erreur lors de l'appel AJAX :", error);
            }
        }
    }).on('select2:select', function (e) {
        var selected = e.params.data;
        console.log("Client sélectionné :", selected);
        $("#client_id").val(selected.id); // Enregistre l'ID du client dans un champ caché
    });
});

</script>

<style>
    section{
        background-color: #fff !important;
    }
    table.dataTable {
        width: 100% !important;
    }
    table.dataTable th,
    table.dataTable td {
        white-space: nowrap;
    }
</style>

@endsection
