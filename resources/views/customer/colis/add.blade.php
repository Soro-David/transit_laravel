@extends('customer.layouts.index')
@section('content-header')
@endsection
@section('content')
    @csrf
    <section>
        <form action="" method="post">
            @csrf

        <div class="container">
            <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group col-12">
                            <label for="transitMode">Destinataire</label>
                            <button type="button" style="color: #fff;" class="btn gradient-orange-blue" data-bs-toggle="modal" data-bs-target="#info_destinataire">
                                Ajouter le destinataire
                            </button>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group col-12">
                            <label for="transitMode">Agence de Depart</label>
                            <select id="transitMode" name="transit_mode" class="form-control col-10">
                                <option value="" disabled selected>-- Selectionnez l'agence de destination --</option>
                                <option value="maritime">sdfghjk</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group col-12">
                            <label for="nomDestinataire">Saisir le nom du destinataire</label>
                            <input type="text" id="nomDestinataire" name="nomDestinataire" class="form-control" placeholder="Entrez le nom du destinataire" onkeyup="rechercheDestinataire()">
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <div id="products-container">
                <h4 class="text-center mt-4">Ajouter des Articles</h4><br>
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
                            <label for="prix_unitaire">Poids (Kg)</label>
                            <input type="number" name="masse[]" class="form-control" placeholder="Poids en Kg" required>
                        </div>
                        {{-- <div class="col">
                            <label for="prix_total">Prix Total</label>
                            <input type="number" name="prix_total[]" class="form-control" placeholder="Prix Total" required>
                        </div> --}}
                        <div class="col-auto">
                            <button type="button" class="btn btn-success add-product" style="margin-top: 32px;">+</button>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <h6 class="text-right mt-4">Prix total : <span id="prix-total">0</span> FCFA</h6>
                </div>
              {{-- modal destinataire--}}
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
                                                    {{-- @foreach ($agences as $agence)
                                                        <option value="{{$agence->nom_agence}}">{{$agence->nom_agence}}</option>
                                                    @endforeach --}}
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
            {{-- fin modal destinataire --}}
                <div class="container">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="border p-4 rounded shadow-sm">
                                <label for="commentaire1" class="form-label">Commentaire</label>
                                <textarea name="commentaire1" id="commentaire1" rows="5" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="transitMode">Mode de transit :</label>
                                <select id="transitMode" name="transit_mode" class="form-control col-10">
                                    <option value="" disabled selected>--  Selectionnez le Mode de transit--</option>
                                    <option value="maritime">Maritime</option>
                                    <option value="aérien">Aérien</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group ">
                                <label for="reference_contenaire" class="form-label">Référence Contenaire</label>
                                <input type="text" name="reference_contenaire" id="reference_contenaire" 
                                       placeholder="Référence du contenaire" class="form-control">
                            </div>
                        </div>
                       
                    </div>
                </div>
                    <div class="container d-flex justify-content-center align-items-center" style="height: 15vh;">
                        <button type="submit" class="btn btn-success mt-3 d-flex align-items-center justify-content-center">
                            <i class="fas fa-save p-2 px-4 me-2"></i>
                            Valider
                        </button>
                    </div>
            </div>
        </form>
    </section>
    <!-- Scripts -->
    <script>
        $(document).ready(function () {
            // Gestion des produits dynamiques
            $(".add-product").on("click", function () {
                let newRow = `
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
                            <input type="number" name="masse[]" class="form-control" placeholder="Poids en Kg" required>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-danger remove-product" style="margin-top: 32px;">-</button>
                        </div>
                    </div>`;
                $("#dynamic-form").append(newRow);
            });

            $(document).on("click", ".remove-product", function () {
                $(this).closest(".product-row").remove();
            });
            // Gestion des méthodes de paiement
            $("#paymentMethod").on("change", function () {
                $(".payment-details").hide();
                $("#" + $(this).val() + "Details").show();
            });
            // Soumission du formulaire de paiement
            $("#paymentForm").on("submit", function (e) {
                e.preventDefault();
                alert("Mode de paiement choisi : " + $("#paymentMethod").val());
            });
        });

    // Fonction pour effectuer une recherche AJAX
    function rechercheDestinataire() {
        var nom = document.getElementById('nomDestinataire').value;

        if (nom.length >= 3) {  // Si le nom a plus de 3 caractères
            fetch(`/destinataire?nom=${nom}`)
                .then(response => response.json())
                .then(data => {
                    // Si un destinataire est trouvé, afficher ses informations
                    if (data.nom) {
                        document.getElementById('infoAffichee').textContent = "Nom: " + data.nom + ", Numéro: " + data.numero;
                    } else {
                        document.getElementById('infoAffichee').textContent = "Aucun destinataire trouvé.";
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                });
        } else {
            document.getElementById('infoAffichee').textContent = "Aucun destinataire trouvé.";
        }
    }
    </script>

    <!-- Styles -->
    <style>
        table.dataTable {
            width: 100% !important;
        }
        table.dataTable th, table.dataTable td {
            white-space: nowrap;
        }
        .form-group {
            margin: 15px 0;
        }
        label {
            font-weight: bold;
        }
        .form-group {
        margin: 15px 0;
    }
    label {
        font-weight: bold;
        margin-bottom: 5px;
        display: inline-block;
    }
    .form-control {
        padding: 10px;
        font-size: 16px;
        border-radius: 5px;
        border: 1px solid #ccc;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        width: 100%;
        box-sizing: border-box;
    }
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        outline: none;
    }
    .container {
        margin-top: 20px;
    }

    .form-label {
        font-weight: bold;
    }

    .form-control {
        padding: 8px;
        font-size: 16px;
        border-radius: 5px;
        border: 1px solid #ccc;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        outline: none;
    }

    .shadow-sm {
        box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.1);
    }

    .mt-3 {
        margin-top: 1rem;
    }

    .p-4 {
        padding: 1.5rem;
    }
                
    </style>
@endsection
