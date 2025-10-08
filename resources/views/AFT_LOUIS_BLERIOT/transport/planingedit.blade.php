@extends('AFT_LOUIS_BLERIOT.layouts.agent')

@section('content-header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Modifier le Programme</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('aftlb_transport.planing.chauffeur') }}">Programmes</a></li>
                    <li class="breadcrumb-item active">Modification</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">MODIFICATION DU PROGRAMME #{{ $programme->id }}</h3>
        </div>
        <div class="card-body">
            <form id="editProgrammeForm" action="{{ route('aftlb_transport.programme.update', $programme->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Informations de base -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date_programme">Date du Programme *</label>
                            <input type="date" class="form-control" id="date_programme" name="date_programme" value="{{ $programme->date_programme->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="user_id">Chauffeur *</label>
                            <select class="form-control" id="user_id" name="user_id" required>
                                <option value="">-- Sélectionner un Chauffeur --</option>
                                @foreach($chauffeurs as $chauffeur)
                                    <option value="{{ $chauffeur->id }}" {{ $programme->user_id == $chauffeur->id ? 'selected' : '' }}>
                                        {{ $chauffeur->first_name }} {{ $chauffeur->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="actions_a_faire">Action à faire *</label>
                            <select class="form-control" id="actions_a_faire" name="actions_a_faire" required>
                                <option value="depot" {{ $programme->actions_a_faire == 'depot' ? 'selected' : '' }}>Dépôt</option>
                                <option value="recuperation" {{ $programme->actions_a_faire == 'recuperation' ? 'selected' : '' }}>Récupération</option>
                                <option value="livraison" {{ $programme->actions_a_faire == 'livraison' ? 'selected' : '' }}>Livraison</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="quantite">Quantité *</label>
                            <input type="number" class="form-control" id="quantite" name="quantite" value="{{ $programme->quantite }}" required min="1">
                        </div>
                    </div>
                </div>

                <!-- Informations du client -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nom_expediteur">Nom du client *</label>
                            <input type="text" class="form-control" id="nom_expediteur" name="nom_expediteur" value="{{ $programme->nom_expediteur }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tel_expediteur">Téléphone *</label>
                            <input type="text" class="form-control" id="tel_expediteur" name="tel_expediteur" value="{{ $programme->tel_expediteur }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="lieu_expedition">Adresse de dépôt ou récupération *</label>
                            <textarea class="form-control" id="lieu_expedition" name="lieu_expedition" rows="2" required>{{ $programme->lieu_expedition }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="nature_du_colis">Nature du colis *</label>
                            <input type="text" class="form-control" id="nature_du_colis" name="nature_du_colis" value="{{ $programme->nature_du_colis }}" required>
                        </div>
                    </div>
                </div>

                <!-- Section pour les articles -->
                @if($programme->items && $programme->items->count() > 0)
                <div class="card mt-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Articles du programme</h6>
                        <span class="badge badge-primary">{{ $programme->items->count() }} article(s)</span>
                    </div>
                    <div class="card-body">
                        @foreach($programme->items as $index => $item)
                        <div class="item-details mb-3 p-3 border rounded">
                            <h6 class="text-primary">Article {{ $index + 1 }}</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Service</label>
                                        <input type="text" class="form-control" value="{{ $item->service }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Type de colis</label>
                                        <input type="text" class="form-control" value="{{ $item->type_colis }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Quantité</label>
                                        <input type="text" class="form-control" value="{{ $item->quantite_colis }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Valeur (FCFA)</label>
                                        <input type="text" class="form-control" value="{{ number_format($item->valeur_colis, 0, ',', ' ') }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Poids (kg)</label>
                                        <input type="text" class="form-control" value="{{ $item->poids }}" readonly>
                                    </div>
                                </div>
                            </div>
                            @if($item->description_colis)
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" rows="2" readonly>{{ $item->description_colis }}</textarea>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Informations de référence -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Référence</label>
                            <input type="text" class="form-control" value="{{ $programme->reference_generee ?? $programme->reference_colis }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>État</label>
                            <input type="text" class="form-control" value="{{ ucfirst($programme->etat_rdv) }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                    <a href="{{ route('aftlb_transport.planing.chauffeur') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    
                    @if($programme->etat_rdv !== 'effectué')
                    <button type="button" class="btn btn-danger float-right" id="deleteBtn" data-id="{{ $programme->id }}">
                        <i class="fas fa-trash"></i> Supprimer le programme
                    </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Soumission du formulaire
    $('#editProgrammeForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $('#submitBtn');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enregistrement...');

        const formData = new FormData(this);

        axios.post($(this).attr('action'), formData)
            .then(response => {
                if (response.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès!',
                        text: response.data.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = "{{ route('aftlb_transport.planing.chauffeur') }}";
                    });
                } else {
                    Swal.fire('Erreur!', response.data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                let errorMessage = 'Une erreur est survenue';
                if (error.response && error.response.data && error.response.data.message) {
                    errorMessage = error.response.data.message;
                }
                Swal.fire('Erreur!', errorMessage, 'error');
            })
            .finally(() => {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Enregistrer les modifications');
            });
    });

    // Suppression du programme
    $('#deleteBtn').on('click', function() {
        const programmeId = $(this).data('id');
        
        Swal.fire({
            title: 'Êtes-vous sûr?',
            text: "Cette action supprimera le programme et tous ses articles!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer!',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/aftlb_transport/programme-delete/${programmeId}-aft-louis-b`)
                    .then(response => {
                        if (response.data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Supprimé!',
                                text: response.data.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.href = "{{ route('aftlb_transport.planing.chauffeur') }}";
                            });
                        } else {
                            Swal.fire('Erreur!', response.data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur suppression:', error);
                        Swal.fire('Erreur!', 'Une erreur est survenue lors de la suppression.', 'error');
                    });
            }
        });
    });

    // Empêcher la modification si le programme est effectué
    @if($programme->etat_rdv === 'effectué')
        Swal.fire({
            icon: 'info',
            title: 'Programme effectué',
            text: 'Ce programme a déjà été effectué et ne peut pas être modifié.',
            confirmButtonText: 'Compris'
        }).then(() => {
            // Désactiver tous les champs
            $('#editProgrammeForm input, #editProgrammeForm select, #editProgrammeForm textarea').prop('disabled', true);
            $('#submitBtn').prop('disabled', true).addClass('btn-secondary');
        });
    @endif
});
</script>

<style>
.item-details {
    background-color: #f8f9fa;
    border-left: 4px solid #007bff;
}

.form-control:read-only {
    background-color: #e9ecef;
    opacity: 1;
}

.btn:disabled {
    cursor: not-allowed;
}
</style>
@endsection