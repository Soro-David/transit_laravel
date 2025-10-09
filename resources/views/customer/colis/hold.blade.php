@extends('customer.layouts.index')

@section('content-header')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<section class="py-4">
    <div class="container">

        <form action="" method="POST" class="mt-4" id="colisForm">
            @csrf

            @if (session('success'))
                <div class="alert alert-success shadow-sm rounded">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger shadow-sm rounded">{{ session('error') }}</div>
            @endif

            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header d-flex align-items-center justify-content-between" style="background: linear-gradient(90deg,#0ea05a,#0b7cff); color:#fff;">
                    <div class="d-flex align-items-center">
                        <div style="width:44px;height:44px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-right:12px;">
                            <svg width="20" height="20" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 4h12M4 8h8M6 12h4" stroke="#fff" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <h5 class="mb-0">Liste des devis</h5>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('customer_colis.devis.create') }}" class="btn btn-light btn-sm fw-bold">+ Nouveau devis</a>
                        <button type="button" class="btn btn-outline-light btn-sm" onclick="$('#productTable').DataTable().ajax.reload()">Rafraîchir</button>
                    </div>
                </div>

                <div class="card-body">
                    <div id="products-container">
                        <div class="table-responsive">
                            <table id="productTable" class="display table table-striped table-bordered table-hover" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>Référence</th>
                                        <th>Nombre d'items</th>
                                        <th>Agence Expédition</th>
                                        <th>Expéditeur</th>
                                        <th>Tel Expéditeur</th>
                                        <th>Agence Destination</th>
                                        <th>État</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</section>

<style>
    /* Visuel amélioré, n'altère pas les classes JS existantes */
    .card-header h5 { color: #fff; font-weight:700; margin:0; }
    #productTable th { font-weight:700; color:#374151; }
    #productTable tbody tr:hover { background: #f6fffa !important; }
    .btn-delete-group {
        background-color: #f44336;
        color: white;
        border: none;
        padding: 6px 10px;
        border-radius: 6px;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-delete-group:hover { background-color: #d32f2f; }
    /* Responsive small tweaks */
    @media (max-width:768px) {
        .card-header { flex-direction:column; gap:8px; align-items:flex-start; }
    }
</style>

<script>
    // Options d'affichage des agences (doit être défini AVANT la DataTable)
    const agenceOptionsByMode = {
        maritime: { value: "IPMS-SIMEX-CI", label: "DS Translog Carrefour Angré" },
        aerien: { value: "IPMS-SIMEX-CI Angre 8ème Tranche", label: "DS Translog Angré 8ème Tranche" }
    };

    $(document).ready(function() {
        // CSRF pour AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Template d'URL pour la suppression (on génère l'URL via route nommée avec PLACEHOLDER)
        var deleteUrlTemplate = "{{ route('customer_colis.devis.delete', ['reference' => 'PLACEHOLDER']) }}";

        var table = $("#productTable").DataTable({
            responsive: true,
            language: {
                url: "{{ asset('js/fr-FR.json') }}"
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("customer_colis.get.devis") }}',
                type: 'GET',
                error: function(xhr, error, thrown) {
                    console.error('Erreur AJAX:', error, thrown);
                    console.error('Réponse:', xhr.responseText);
                    alert('Erreur lors du chargement des données. Veuillez réessayer.');
                }
            },
            columns: [
                { data: 'reference', name: 'reference' },
                { data: 'nombre_items', name: 'nombre_items' },
                { data: 'agence_expedition', name: 'agence_expedition' },
                { data: 'expediteur_nom_complet', name: 'expediteur_nom_complet' },
                { data: 'tel_expediteur', name: 'tel_expediteur' },
                {
                    data: 'agence_destination',
                    name: 'agence_destination',
                    render: function (data, type, row) {
                        // retourne le label correspondant à la valeur stockée en base
                        if (!data) return ''; // pas de valeur
                        // trouver la correspondance parmi les options
                        var match = Object.values(agenceOptionsByMode).find(function(opt) {
                            return opt.value === data;
                        });
                        return (match && match.label) ? match.label : data;
                    }
                },
                { data: 'etat', name: 'etat' },
                { data: 'last_updated_at', name: 'last_updated_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            initComplete: function() {
                console.log('DataTable initialisé (devis)');
            }
        });

        // Handler pour suppression (délégation event sur table)
        $('#productTable').on('click', '.btn-delete-group', function(e) {
            e.preventDefault();
            var reference = $(this).data('reference');
            if (!reference) {
                alert('Référence introuvable.');
                return;
            }

            if (!confirm('Êtes-vous sûr de vouloir supprimer le devis : ' + reference + ' ?')) {
                return;
            }

            // Construire l'URL finale
            var finalUrl = deleteUrlTemplate.replace('PLACEHOLDER', reference);

            $.ajax({
                url: finalUrl,
                type: 'DELETE',
                success: function(response) {
                    if (response && (response.success === true || typeof response.success === 'string')) {
                        var msg = (typeof response.success === 'string') ? response.success : (response.message || 'Devis supprimé avec succès.');
                        alert(msg);
                    } else {
                        // si message d'erreur fourni
                        var msg = (response && (response.message || response.error)) || 'Suppression échouée.';
                        alert(msg);
                    }
                    table.ajax.reload(null, false);
                },
                error: function(xhr) {
                    console.error('Erreur delete:', xhr);
                    var err = 'Erreur lors de la suppression du devis.';
                    if (xhr.responseJSON && xhr.responseJSON.message) err = xhr.responseJSON.message;
                    alert(err);
                }
            });
        });
    });
</script>

@endsection
