@extends('chauffeur.layouts.index')

@section('title', 'Programme des Colis')

@section('content-header')
    <h1>Programme des Colis</h1>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card card-box">
            <div class="table-responsive">
                <table id="programmes-table" class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Date du Programme</th>
                            <th>Référence du Colis</th>
                            <th>Actions à faire</th>
                            <th>Nom de l'Expéditeur</th>
                            <th>Nom du Destinataire</th>
                            <th>Lieu de Destination</th>
                            <th>Etat du RDV</th>
                           <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($data) && count($data) > 0)
                            @foreach($data as $row)
                                @php
                                    $rowClass = '';
                                    switch ($row['etat_rdv']) {
                                        case 'effectué':
                                            $rowClass = 'table-success';
                                            break;
                                        case 'à replanifié':
                                            $rowClass = 'table-warning';
                                            break;
                                        default:
                                            $rowClass = '';
                                            break;
                                    }
                                @endphp
                                <tr class="{{ $rowClass }}" id="programme-row-{{ $row['id'] }}">
                                    <td>{{ $row['date_programme_formatted'] }}</td>
                                    <td>{{ $row['reference_colis'] }}</td>
                                    <td>{{ $row['actions_a_faire'] }}</td>
                                    <td>{{ $row['nom_expediteur'] }}</td>
                                    <td>{{ $row['nom_destinataire'] }}</td>
                                    <td>{{ $row['lieu_destinataire'] }}</td>
                                    <td>
                                        <form id="etat-rdv-form-{{ $row['id'] }}" action="{{ route('chauffeur.programme.updateEtatRdv', $row['id']) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <select class="form-control etat-rdv-select" name="etat_rdv" data-programme-id="{{ $row['id'] }}">
                                                <option value="en cours" {{ $row['etat_rdv'] == 'en cours' ? 'selected' : '' }}>En cours</option>
                                                <option value="effectué" {{ $row['etat_rdv'] == 'effectué' ? 'selected' : '' }}>Effectué</option>
                                                <option value="à replanifié" {{ $row['etat_rdv'] == 'à replanifié' ? 'selected' : '' }}>À replanifié</option>
                                            </select>
                                        </form>
                                    </td>
                                   <td class="text-center">
                                       <button type="button" class="btn btn-primary btn-sm btn-valider-rdv" data-programme-id="{{ $row['id'] }}">Valider</button>
                                   </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8">Aucun programme disponible</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

   <script>
       $(document).ready(function() {
            console.log('Document ready!');
            $('.btn-valider-rdv').on('click', function() {
                console.log('Bouton Valider cliqué!');
                 var programmeId = $(this).data('programme-id');
                console.log('Programme ID:', programmeId);
                var form = $('#etat-rdv-form-' + programmeId);

                 form.on('submit', function (e){
                 e.preventDefault(); // Empêche la soumission par défaut
                 var formData = new FormData(this); // Créer un objet FormData
                  $.ajax({
                         type: 'POST',
                         url: $(this).attr('action'),
                         data: formData,
                         processData: false,
                         contentType: false,
                         headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Assurez-vous d'avoir une balise meta csrf-token
                         },
                         success: function (data) {
                            console.log('Response:', data)
                          Swal.fire({
                              icon: 'success',
                              title: 'RDV mis à jour!',
                             showConfirmButton: false,
                            timer: 1500
                           })
                             .then(() =>{
                                window.location.reload();
                             }) // Recharger la page pour afficher les modifications
                        },
                        error: function (error) {
                            console.error('Erreur:', error);
                          Swal.fire({
                                icon: 'error',
                                title: 'Erreur!',
                                text: 'Une erreur est survenue lors de la mise à jour'
                           })
                        }
                   })
               });
                form.submit();
            });
          $('.etat-rdv-select').on('change', function() {
                 var programmeId = $(this).data('programme-id');
                var etatRdv = $(this).val();
                var row = $('#programme-row-' + programmeId);

                row.removeClass('table-success table-warning');

                if (etatRdv === 'effectué') {
                   row.addClass('table-success');
                } else if (etatRdv === 'à replanifié') {
                   row.addClass('table-warning');
                }
            });
       });
    </script>
@endpush