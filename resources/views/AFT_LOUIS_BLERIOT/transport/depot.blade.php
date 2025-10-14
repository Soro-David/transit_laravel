{{-- views/AFT_LOUIS_BLERIOT/transport/depot.blade.php --}}
@extends('AFT_LOUIS_BLERIOT.layouts.agent')

@section('content-header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Créer un nouveau Dépôt</h1>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <form id="depotForm" action="{{ route('aftlb_transport.programme.createMultipleDepot') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-12">
                <!-- Section pour programmes multiples -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Programmes de dépôt</h6>
                    </div>
                    <div class="card-body" id="depot-programmes-container">
                        <!-- Programmes seront générés ici -->
                    </div>
                    <!-- Bouton ajouter déplacé en bas -->
                    <div class="card-footer">
                        <button type="button" class="btn btn-success" id="add-depot-programme-btn">
                            <i class="fas fa-plus"></i> Ajouter un autre dépôt
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bouton d'enregistrement -->
        <div class="row">
            <div class="col-12 text-right mb-4">
                <a href="{{ route('aftlb_transport.planing.chauffeur') }}" class="btn btn-secondary">Annuler</a>
                <button type="button" class="btn btn-success" id="save-depot-btn">
                    Enregistrer les Dépôts
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Modal pour choisir chauffeur et date -->
<div class="modal fade" id="driverDateModal" tabindex="-1" role="dialog" aria-labelledby="driverDateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="driverDateModalLabel">Sélection du Chauffeur et de la Date</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="user_id_depot_modal">Chauffeur *</label>
                    <select class="form-control" id="user_id_depot_modal" required>
                        <option value="">-- Sélectionner un Chauffeur --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="date_programme_depot_modal">Date du Programme *</label>
                    <input type="date" class="form-control" id="date_programme_depot_modal" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success" id="confirm-save-btn">Confirmer et Enregistrer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    let chauffeurs = [];
    let currentDate = new Date().toISOString().split('T')[0];
    $('#date_programme_depot_modal').attr('min', currentDate); // <-- AJOUTEZ CETTE LIGNE
    // Charger les chauffeurs
    function loadChauffeurs() {
        axios.get("{{ route('aftlb_transport.programme.data') }}")
            .then(function(response) {
                chauffeurs = response.data.chauffeurs || [];
                const select = $('#user_id_depot_modal');
                select.empty().append('<option value="">-- Sélectionner un Chauffeur --</option>');
                
                chauffeurs.forEach(function(chauffeur) {
                    const nomComplet = `${chauffeur.first_name || ''} ${chauffeur.last_name || ''}`.trim();
                    select.append(`<option value="${chauffeur.id}">${nomComplet}</option>`);
                });
            })
            .catch(error => console.error('Erreur chargement chauffeurs:', error));
    }

    // Template pour un dépôt
    function getDepotTemplate(index) {
        return `
            <div class="depot-programme-item border rounded p-3 mb-3" data-depot-programme-index="${index}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-primary mb-0">Dépôt #${index + 1}</h6>
                    ${index > 0 ? '<button type="button" class="btn btn-sm btn-outline-danger remove-depot-programme-btn"><i class="fas fa-times"></i></button>' : ''}
                </div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Quantité *</label>
                        <input type="number" class="form-control" name="programmes[${index}][quantite]" required min="1">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Nature de l'objet *</label>
                        <input type="text" class="form-control" name="programmes[${index}][nature_du_colis]" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Nom du Concerné *</label>
                        <input type="text" class="form-control" name="programmes[${index}][nom_expediteur]" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Téléphone *</label>
                        <input type="text" class="form-control" name="programmes[${index}][tel_expediteur]" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Adresse de Dépôt *</label>
                        <textarea class="form-control" name="programmes[${index}][lieu_expedition]" rows="2" required></textarea>
                    </div>
                </div>
            </div>`;
    }

    // Ajouter un nouveau dépôt
    function addDepotProgramme() {
        const newIndex = $('.depot-programme-item').length;
        $('#depot-programmes-container').append(getDepotTemplate(newIndex));
    }

    // Supprimer un dépôt
    function removeDepotProgramme() {
        if ($('.depot-programme-item').length > 1) {
            $(this).closest('.depot-programme-item').remove();
            // Mettre à jour les index et les titres
            $('.depot-programme-item').each(function(i) {
                $(this).find('h6').text(`Dépôt #${i + 1}`);
                $(this).attr('data-depot-programme-index', i);
                $(this).find('[name]').each(function() {
                    let newName = $(this).attr('name').replace(/\[\d+\]/, `[${i}]`);
                    $(this).attr('name', newName);
                });
            });
        } else {
            Swal.fire('Information', 'Au moins un dépôt est requis.', 'info');
        }
    }

    // Valider le formulaire avant d'ouvrir le modal
    function validateFormBeforeSave() {
        const depotItems = $('.depot-programme-item');
        if (depotItems.length === 0) {
            Swal.fire('Erreur', 'Veuillez ajouter au moins un dépôt.', 'error');
            return false;
        }

        let isValid = true;
        depotItems.each(function() {
            const inputs = $(this).find('input[required], textarea[required]');
            inputs.each(function() {
                if (!$(this).val().trim()) {
                    isValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
        });

        if (!isValid) {
            Swal.fire('Erreur', 'Veuillez remplir tous les champs obligatoires pour chaque dépôt.', 'error');
        }

        return isValid;
    }

    // Événements
    $('#add-depot-programme-btn').on('click', addDepotProgramme);
    $('#depot-programmes-container').on('click', '.remove-depot-programme-btn', removeDepotProgramme);

    // Ouvrir le modal pour choisir chauffeur et date
    $('#save-depot-btn').on('click', function() {
        if (validateFormBeforeSave()) {
            $('#date_programme_depot_modal').val(currentDate);
            $('#driverDateModal').modal('show');
        }
    });

    // Confirmer l'enregistrement
    $('#confirm-save-btn').on('click', function() {
        const driverId = $('#user_id_depot_modal').val();
        const dateProgramme = $('#date_programme_depot_modal').val();

        if (!driverId || !dateProgramme) {
            Swal.fire('Erreur', 'Veuillez sélectionner un chauffeur et une date.', 'error');
            return;
        }

        // Ajouter les champs cachés au formulaire
        $('#depotForm').append(`<input type="hidden" name="user_id" value="${driverId}">`);
        $('#depotForm').append(`<input type="hidden" name="date_programme" value="${dateProgramme}">`);

        // Soumettre le formulaire
        submitForm();
    });

    // Soumettre le formulaire
    function submitForm() {
        $('#driverDateModal').modal('hide');
        
        Swal.fire({
            title: 'Création en cours...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        axios.post($('#depotForm').attr('action'), new FormData($('#depotForm')[0]))
            .then(res => {
                Swal.fire({
                    icon: 'success',
                    title: 'Succès!',
                    html: `<strong>${res.data.message}</strong><br>Créés: ${res.data.created_count} | Échecs: ${res.data.failed_count}`,
                }).then(() => {
                    window.location.href = "{{ route('aftlb_transport.planing.chauffeur') }}";
                });
            })
            .catch(err => {
                Swal.fire('Erreur', err.response?.data?.message || 'Une erreur est survenue.', 'error');
            });
    }

    // Initialisation
    loadChauffeurs();
    addDepotProgramme(); // Ajoute le premier formulaire au chargement
});
</script>
@endsection