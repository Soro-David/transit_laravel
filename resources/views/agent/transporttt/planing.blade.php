@extends('agent.layouts.agent')
@section('content-header')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
@endsection

@section('content')
<section class="p-4 mx-auto">
    <form action="#" method="post" class="form-container">
        @csrf
        {{-- Section : Informations de l'Expéditeur --}}
        <h5 class="text-center mb-4 mt-5">Planifier un chauffeur</h5>
        <div class="form-section">
            <div class="row">
                <div class="col-md-6">
                    <label for="nom_chauffeur" class="form-label">Sélectionnez le nom du chauffeur</label>
                    <input type="text" name="nom_chauffeur" id="nom_chauffeur" class="form-control">
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="reference_colis" class="form-label">Saisissez la référence du colis</label>
                        <input type="text" name="reference_colis" id="reference_colis" 
                            value="{{ old('reference_colis') }}" class="form-control" required>
                    </div>
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="chauffeur-details">
                            <!-- Les détails sélectionnés apparaîtront ici -->
                        </tbody>
                    </table>
               </div>
           </div>
        <div class="text-end mt-4 d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-primary">Valider</button>
        </div>
    </form>
</section>
@endsection

@section('js')
<script>
   $(document).ready(function() {
      // Fonction d'autocomplétion pour le champ "reference_colis"
      $("#reference_colis").autocomplete({
         source: function(request, response) {
               $.ajax({
                url: '{{ route('agent_transport.reference.auto', ['query' => 'PLACEHOLDER']) }}'.replace('PLACEHOLDER', request.term),
                type: 'GET',
                dataType: 'json',
                  success: function(data) {
                     response(data.map(function(item) {
                           return {
                              label: item.email, // Texte affiché dans les suggestions
                              value: item.email, // Valeur remplie dans le champ
                              first_name: item.first_name,
                              last_name: item.last_name,
                              email: item.email,
                              id: item.id // Identifiant unique pour éviter les doublons
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
                  // Ajouter un nouvel élément à la table
                  let selectedItem = `
                     <tr data-id="${ui.item.id}">
                           <td>${ui.item.id}</td>
                           <td>${ui.item.first_name} ${ui.item.last_name}</td>
                           <td>${ui.item.email}</td>
                           <td>${ui.item.email}</td>
                           <td><button type="button" class="btn btn-danger remove-chauffeur"><i class="fas fa-trash"></i></button></td>
                     </tr>
                  `;
                  $('#chauffeur-details').append(selectedItem);
               }

               // Effacer le champ après la sélection
               $('#reference_colis').val('');
         }
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

    section {
        margin-top: 0px;
    }
</style>
@endsection
