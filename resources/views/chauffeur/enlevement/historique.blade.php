@extends('chauffeur.layouts.index')

@section('title', 'Historique des Opérations')

{{-- La section @push('styles') est la même que sur take.blade.php --}}

@section('content-header')
    <h1>Historique des Opérations</h1>
@endsection

@section('content')
    <div class="p-4"> 
        <div class="card card-programme">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history mr-2"></i>Liste des opérations effectuées</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="programmes-table" class="table table-hover table-striped">
                        <thead>
                            <tr class="text-center">
                                <th>Date Fin</th>
                                <th>Référence</th>
                                <th>Action</th>
                                <th>Client</th>
                                <th>Adresse</th>
                                <th class="text-center">Détails</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($programmes as $row)
                                <tr class="text-center">
                                    {{-- On utilise updated_at car c'est la date où l'état a été changé --}}
                                    <td>{{ $row->updated_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        {{ $row->actions_a_faire === 'depot' ? $row->reference_generee : $row->reference_colis }}
                                    </td>
                                    <td>
                                        @if($row->actions_a_faire == 'depot')
                                            <span class="badge badge-success">Dépôt</span>
                                        @else
                                            <span class="badge badge-info">Récupération</span>
                                        @endif
                                    </td>
                                    <td>{{ $row->nom_expediteur }}</td>
                                    <td>{{ $row->lieu_expedition }}</td>
                                    <td class="text-center">
                                        {{-- Le bouton "oeil" qui ouvre une modale de détails --}}
                                        <button type="button" class="btn btn-info btn-sm btn-view-details" 
                                                data-toggle="modal" data-target="#detailsModal"
                                                data-reference="{{ $row->actions_a_faire === 'depot' ? $row->reference_generee : $row->reference_colis }}"
                                                data-client="{{ $row->nom_expediteur }}"
                                                data-contact="{{ $row->tel_expediteur }}"
                                                data-adresse="{{ $row->lieu_expedition }}"
                                                data-nature="{{ $row->nature_du_colis }}"
                                                data-quantite="{{ $row->quantite }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center p-4">Aucun historique disponible.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modale pour afficher les détails -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Détails de l'opération : <span id="modal-reference"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Client :</strong> <span id="modal-client"></span></p>
                    <p><strong>Contact :</strong> <span id="modal-contact"></span></p>
                    <p><strong>Adresse :</strong> <span id="modal-adresse"></span></p>
                    <p><strong>Nature du colis :</strong> <span id="modal-nature"></span></p>
                    <p><strong>Quantité :</strong> <span id="modal-quantite"></span></p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Script pour remplir la modale de détails
    $('#detailsModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        modal.find('#modal-reference').text(button.data('reference'));
        modal.find('#modal-client').text(button.data('client'));
        modal.find('#modal-contact').text(button.data('contact'));
        modal.find('#modal-adresse').text(button.data('adresse'));
        modal.find('#modal-nature').text(button.data('nature'));
        modal.find('#modal-quantite').text(button.data('quantite'));
    });
</script>
@endpush