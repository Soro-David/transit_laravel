@extends('agent.layouts.agent')
@section('content-header')

@endsection

@section('content')
<section class="p-4 mx-auto">
    <div class="card mx-auto" style="max-width: 1200px;">
        <div class="card-body">
            <form action="{{route('agent_transport.store.plannification')}}" method="post" class="form-container">
                @csrf
                {{-- Section : Informations de l'Expéditeur --}}
                <h5 class="text-center mb-4 mt-5">Planifier un chauffeur</h5>
                <div class="form-section">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 mb-3">
                            <label for="nom_chauffeur">Sélectionnez le nom du chauffeur</label>
                            <select name="chauffeur_id" id="chauffeur_id" class="form-control">
                                <option value="" disabled selected>-- Sélectionnez un chauffeur --</option>
                                @foreach ($chauffeurs as $nom_chauffeur)
                                    <option value="{{$nom_chauffeur->id}}">{{$nom_chauffeur->nom}} {{$nom_chauffeur->prenom}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-3">
                            <label for="reference_colis" class="form-label">Saisissez la référence du colis</label>
                            <input type="text" name="reference_colis" id="reference_colis" 
                                value="{{ old('reference_colis') }}" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <h5 class="text-center mb-4 mt-5">Détails du colis de la référence sélectionnée</h5>
                        <div class="responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Nom complet</th>
                                        <th>Adresse Client</th>
                                        <th>Téléphone</th>
                                        <th>Lieu de Destination</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="chauffeur-details" name="chauffeur_details[]">
                                    <!-- Les détails sélectionnés apparaîtront ici -->
                                </tbody>
                            </table>
                            <input type="hidden" id="chauffeur_details_data" name="chauffeur_details_data">
                            {{-- <input type="hidden" id="colis_id" name="colis_id" value="{{$}}"> --}}
                        </div>
                    </div>
                </div>
                <div class="text-end mt-4 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">Valider</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Fonction d'autocomplétion pour le champ "reference_colis"
    $("#reference_colis").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: '{{ route('agent_transport.reference.auto', ['query' => '__QUERY__']) }}'.replace('__QUERY__', request.term),
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    response(data.map(function(item) {
                        return {
                            label: item.reference_colis, // Affichage de la référence du colis
                            value: item.reference_colis,
                            reference_colis: item.reference_colis,
                            destinataire_nom: item.destinataire_nom,
                            destinataire_prenom: item.destinataire_prenom,
                            destinataire_email: item.destinataire_email,
                            destinataire_tel: item.destinataire_tel,
                            destinataire_lieu: item.destinataire_lieu,
                            id: item.id 
                        };
                    }));
                },
                error: function(xhr, status, error) {
                    console.error('Erreur AJAX:', error);
                }
            });

        },
        minLength: 2,
        select: function(event, ui) {
            // Vérifier si l'élément est déjà ajouté
            let alreadySelected = $('#chauffeur-details tr').filter(function() {
                return $(this).data('id') === ui.item.id;
            }).length > 0;

            if (!alreadySelected) {
                // Ajouter un nouvel élément à la table avec les informations du colis et du destinataire
                let selectedItem = `
                    <tr data-id="${ui.item.id}">
                        <td class="reference" data-label="Référence">${ui.item.reference_colis}</td>
                        <td class="nom_destinataire" data-label="Nom complet">${ui.item.destinataire_nom} ${ui.item.destinataire_prenom}</td>
                        <td class="email" data-label="Adresse Client">${ui.item.destinataire_email}</td>
                        <td class="phone" data-label="Téléphone">${ui.item.destinataire_tel}</td>
                        <td class="lieu" data-label="Lieu de destination">${ui.item.destinataire_lieu}</td>
                        <td class="actions" data-label="Actions">
                            <button type="button" class="btn btn-danger remove-chauffeur">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#chauffeur-details').append(selectedItem);
            }

            // Effacer le champ après la sélection
            $('#reference_colis').val('');
        }
    });
    $(document).ready(function() {
    // Récupération des données de la table avant soumission
    $('form').on('submit', function(e) {
        let details = [];
        $('#chauffeur-details tr').each(function() {
            let row = $(this);
            let detail = {
                id: row.data('id'),
                reference: row.find('.reference').text(),
                nom_destinataire: row.find('.nom_destinataire').text(),
                email: row.find('.email').text(),
                phone: row.find('.phone').text(),
                lieu: row.find('.lieu').text()
            };
            details.push(detail);
        });

        // Convertir les détails en JSON et les insérer dans le champ caché
        $('#chauffeur_details_data').val(JSON.stringify(details));
    });
});


    // Gestion de la suppression des éléments
    $(document).on('click', '.remove-chauffeur', function() {
        $(this).closest('tr').remove();
    });
});

</script>
@endsection

@section('styles')
<style>
/* Table responsive */
@media (max-width: 768px) {
    .responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch; /* Pour un meilleur défilement sur iOS */
    }

    .table {
        min-width: 600px; /* Si nécessaire, ajustez cette largeur pour un bon affichage */
        table-layout: auto; /* Pour que la table puisse mieux s'adapter à son contenu */
    }

    .table th, .table td {
        padding: 8px;
        text-align: left;
        display: block; /* Afficher les éléments en block pour une meilleure lisibilité */
        width: 100%; /* Chaque cellule occupe toute la largeur disponible */
        border-bottom: 1px solid #ddd;
    }

    .table th {
        background-color: #f4f4f4;
        font-weight: bold;
    }

    .table td::before {
        /* Pour une présentation en mode "table" mobile */
        content: attr(data-label); /* Utiliser un attribut "data-label" pour indiquer ce que chaque cellule représente */
        font-weight: bold;
        display: block;
        margin-bottom: 4px;
    }

    .table td {
        text-align: right;
    }

    /* Suppression des bordures sur les écrans très petits */
    .table td, .table th {
        border: none;
    }
}

</style>
@endsection
