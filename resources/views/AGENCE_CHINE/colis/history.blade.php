@extends('AGENCE_CHINE.layouts.agent')
@section('content-header')
@endsection

@section('content')
<section class="py-3">

    <div class="container">
        <form action="" method="POST" class="mt-4">
            @csrf
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                            <h4 class="text-left mt-4">Historique des colis</h4><br>
                            <div id="products-container">
                                <div class="table-responsive">
                                    <table id="productTable" class="display">
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
                                <h6 class="text-right mt-4">Prix total : <span id="prix-total">0</span> FCFA</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Script JavaScript -->

<script>
  $(document).ready(function () {
    // Initialisation de la DataTable avec options de personnalisation
    var table = $("#productTable").DataTable({
        responsive: true,
        language: {
                url: "{{ asset('js/fr-FR.json') }}" // Chemin local vers le fichier
            },
        dom: 'Bfrtip', // Barre de boutons pour les fonctionnalités supplémentaires
        buttons: [
            {
                extend: 'excelHtml5', // Export en Excel
                text: 'Exporter en Excel', // Texte du bouton Excel
                title: 'Liste des Produits' // Titre du fichier exporté
            },
            {
                extend: 'pdfHtml5', // Export en PDF
                text: 'Exporter en PDF', // Texte du bouton PDF
                title: 'Liste des Produits', // Titre du fichier exporté
                orientation: 'landscape', // Orientation du document
                pageSize: 'A4' // Taille de la page
            },
            {
                extend: 'print', // Impression
                text: 'Imprimer', // Texte du bouton Imprimer
                title: 'Liste des Produits', // Titre de l'impression
                customize: function (win) {
                            var logoUrl = "{{ asset('images/LOGOAFT.png') }}";
                            var logo = '<img src="' + logoUrl + '" alt="Logo" style="position:relative; top:10px; left:20px; width:100px; height:auto;">';
                            $(win.document.body).find('h1').css('text-align', 'center').css('border', '2px solid #000').css('padding', '1px').css('margin-top', '10px');
                            $(win.document.body).find('h1').after(logo);
                            $(win.document.body).find('table').css('margin-top', '30px');
                        }
            }
        ]
    });

    // Écouter l'événement de clic sur le bouton "ajouter un produit"
    $(".add-product").on("click", function () {
        // Récupérer les valeurs du formulaire
        var description = $("#description").val();
        var quantite = $("#quantite").val();
        var dimension = $("#dimension").val();
        var prix = $("#prix").val();

        // Vérifier que tous les champs sont remplis
        if (description && quantite && dimension && prix) {
            // Effectuer la requête AJAX pour ajouter le produit
            $.ajax({
                url: '{{ route("chine_colis.store") }}',
                method: "POST",
                data: {
                    description: description,
                    quantite: quantite,
                    dimension: dimension,
                    prix: prix,
                    _token: "{{ csrf_token() }}" // Ajout du token CSRF pour sécuriser la requête
                },
                success: function (response) {
                    // Ajouter une nouvelle ligne dans le tableau DataTable
                    table.row.add([
                        response.description, // Description du produit
                        response.quantite, // Quantité
                        response.dimension, // Dimension
                        response.prix // Prix
                    ]).draw(false); // Dessiner le tableau avec la nouvelle ligne
                    // Réinitialiser les champs du formulaire après ajout
                    $("#description").val('');
                    $("#quantite").val('');
                    $("#dimension").val('');
                    $("#prix").val('');
                },
                error: function (xhr, status, error) {
                    // Afficher une erreur si la requête échoue
                    alert("Une erreur s'est produite : " + error);
                }
            });
        } else {
            // Si des champs sont manquants, afficher une alerte
            alert("Veuillez remplir tous les champs.");
        }
    });
});

        
</script>


@endsection
