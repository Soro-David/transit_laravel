
@extends('customer.layouts.index')

@section('content-header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<section class="py-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                    <h4 class="text-left mt-4">Historique des devis validés</h4>
                    <p class="text-muted">Liste de tous vos devis validés</p>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <div class="form-group">
                            <input type="text" class="form-control" id="searchInput" placeholder="Rechercher par référence...">
                        </div>
                        <button class="btn btn-primary" onclick="refreshData()">
                            <i class="fas fa-sync-alt"></i> Actualiser
                        </button>
                    </div>

                    <div id="products-container">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Référence Devis</th>
                                        <th>Nombre de Colis</th>
                                        <th>Agence Expédition</th>
                                        <th>Destinataire</th>
                                        <th>Contact Dest.</th>
                                        <th>Agence Destination</th>
                                        <th>Prix Total</th> 
                                        <th>Statut</th>
                                        <th>Date Modification</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="devisTableBody">
                                    {{-- Vérification renforcée --}}
                                    @if(!empty($devis) && $devis instanceof \Illuminate\Support\Collection && $devis->count() > 0)
                                        @foreach($devis as $item)
                                            <tr>
                                                <td><strong>{{ $item->reference_colis ?? 'N/A' }}</strong></td>
                                                <td class="text-center">{{ $item->nombre_colis_par_reference ?? '0' }}</td>
                                                <td>{{ $item->expediteur->agence ?? 'N/A' }}</td>
                                                <td>
                                                    {{ ($item->destinataire->nom ?? '') . ' ' . ($item->destinataire->prenom ?? '') }}
                                                </td>
                                                <td>{{ $item->destinataire->tel ?? 'N/A' }}</td>
                                                <td>{{ $item->destinataire->agence ?? 'N/A' }}</td>
                                                <td class="text-right">{{ number_format($item->total_prix_devis ?? 0, 2) }} €</td>
                                                <td class="text-center">
                                                    <span class="badge badge-success">Validé</span>
                                                </td>
                                                <td class="text-center">
                                                    {{ $item->updated_at ? $item->updated_at->format('d/m/Y H:i') : 'N/A' }}
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-info btn-sm view-devis" 
                                                                data-id="{{ $item->id }}" 
                                                                data-reference="{{ $item->reference_colis }}"
                                                                title="Voir les détails">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <a href="#" 
                                                           class="btn btn-warning btn-sm" 
                                                           title="Télécharger le devis">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="10" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                                    Aucun devis validé trouvé
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal pour voir les détails du devis -->
<div class="modal fade" id="viewDevisModal" tabindex="-1" role="dialog" aria-labelledby="viewDevisModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDevisModalLabel">Détails du Devis</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="devisDetails">
                <!-- Les détails seront chargés ici -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Recherche en temps réel
    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#devisTableBody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Gestion du clic sur le bouton "Voir les détails"
    $(document).on('click', '.view-devis', function() {
        var devisId = $(this).data('id');
        var reference = $(this).data('reference');
        viewDevisDetails(devisId, reference);
    });
});

// Fonction pour voir les détails du devis
function viewDevisDetails(devisId, reference) {
    $.ajax({
        url: '#',
        type: 'GET',
        data: {
            devis_id: devisId
        },
        beforeSend: function() {
            $('#devisDetails').html(`
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Chargement...</span>
                    </div>
                    <p>Chargement des détails...</p>
                </div>
            `);
        },
        success: function(response) {
            if (response.success) {
                $('#devisDetails').html(response.html);
            } else {
                $('#devisDetails').html(`
                    <div class="alert alert-danger">
                        Erreur lors du chargement des détails
                    </div>
                `);
            }
        },
        error: function() {
            $('#devisDetails').html(`
                <div class="alert alert-danger">
                    Erreur lors du chargement des détails
                </div>
            `);
        }
    });
    $('#viewDevisModal').modal('show');
}

// Fonction pour actualiser les données
function refreshData() {
    window.location.reload();
}

// Actualisation automatique toutes les 30 secondes
setInterval(function() {
    refreshData();
}, 30000);
</script>

<style>
    body {
        background-color: #f7f7f7;
    }
    .badge-success {
        background-color: #28a745;
    }
    .btn-group .btn {
        margin-right: 5px;
    }
    .table th {
        background-color: #343a40;
        color: white;
        border-color: #454d55;
    }
</style>
@endsection