@extends('AGENCE_CHINE.layouts.agent')

@section('content-header')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        {{-- MODIFIÉ : Titre pour la Chine --}}
        <h1 class="h3 fw-bold text-primary">✅ Liste des devis confirmés (Chine)</h1>
        <p class="mb-0 text-muted">Consultez l'historique et les détails de vos devis confirmés.</p>
    </div>
</div>
@endsection

@section('content')
<section class="py-3">
    <div class="card border-0 shadow-lg rounded-3">
        <div class="card-header bg-gradient-primary text-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fas fa-check-double me-2"></i> Devis Confirmés</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="devisConfirmeTableChine" class="table align-middle table-striped mb-0">
                    <thead class="table-light text-uppercase">
                        <tr>
                            <th>Référence</th>
                            <th>Nbr. Colis</th>
                            <th>Expéditeur</th>
                            <th>Téléphone Exp.</th>
                            <th>Agence</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script>
$(document).ready(function () {
    $("#devisConfirmeTableChine").DataTable({
        responsive: true,
        language: {
            url: "{{ asset('js/fr-FR.json') }}"
        },
        ajax: {
            // <!-- MODIFIÉ : C'est le point crucial, on appelle la route pour la Chine. -->
            url: '{{ route("chine_colis.get.devis.confirmes") }}',
            dataSrc: 'data'
        },
        columns: [
            { 
                data: 'reference_colis',
                render: function(data) { return '<span class="fw-bold text-primary">' + data + '</span>'; }
            },
            { data: 'nombre_de_colis', className: 'text-center fw-semibold' },
            { render: function (data, type, row) { return row.expediteur_nom + ' ' + row.expediteur_prenom; }},
            { data: 'expediteur_tel', className:'text-muted' },
            { data: 'destinataire_agence', className:'fw-semibold' },
            { 
                data: 'etat',
                render: function(data) {
                    return '<span class="badge rounded-pill px-3 py-2 bg-success">' + data + '</span>';
                }
            },
            { 
                data: 'created_at',
                render: function(data) {
                    return data ? new Date(data).toLocaleDateString('fr-FR') : '';
                }
            },
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    // <!-- MODIFIÉ : On utilise les routes préfixées par "chine_colis." -->
                    const showUrl = "{{ route('chine_colis.devis.show', ['id' => ':id']) }}".replace(':id', row.id);
                    const invoiceUrl = "{{ route('chine_colis.valide.edit.invoice', ['id' => ':id']) }}".replace(':id', row.id);
                    
                    return `
                        <a href="${showUrl}" class="btn btn-sm btn-info btn-action me-1" title="Voir les détails">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="${invoiceUrl}" class="btn btn-sm btn-primary btn-action" title="Générer la facture">
                            <i class="fas fa-file-invoice"></i>
                        </a>
                    `;
                }
            }
        ]
    });
});
</script>


<style>
    .table thead th {
        font-size: 0.85rem;
        letter-spacing: .5px;
        font-weight: 600;
    }
    .table td {
        vertical-align: middle;
    }
    .btn-action {
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all .2s ease;
    }
    .btn-action:hover {
        transform: scale(1.1);
    }
    .bg-gradient-primary {
        background: linear-gradient(45deg, #007bff, #0056b3);
    }
    </style>
@endsection