@extends('admin.layouts.admin')

@section('content-header')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-primary">✅ Liste des devis confirmés</h1>
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
                <table id="devisConfirmeTable" class="table align-middle table-striped mb-0">
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

<!-- Modal : détails du devis -->
<div class="modal fade" id="devisModal" tabindex="-1" aria-labelledby="devisModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <!-- En-tête du modal avec fond dégradé -->
        <div class="modal-header bg-gradient-primary text-white py-4 rounded-top">
          <div class="d-flex align-items-center">
            <div class="modal-icon bg-white bg-opacity-20 rounded-circle p-3 me-3">
              <i class="fas fa-file-invoice-dollar fa-lg text-white"></i>
            </div>
            <div>
              <h5 class="modal-title fw-bold mb-0" id="devisModalLabel">Détails du Devis</h5>
              <p class="mb-0 opacity-75" id="modalReferenceSubtitle">Référence: <span id="modalReference" class="fw-semibold"></span></p>
            </div>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        
        <div class="modal-body p-0">
          <div id="modalBodyContent">
            
            <!-- Section Informations Générales avec carte moderne -->
            <div class="card border-0 m-4 mb-3 shadow-sm">
              <div class="card-header bg-transparent border-bottom-0 py-3">
                <h6 class="fw-bold text-primary mb-0 d-flex align-items-center">
                  <i class="fas fa-info-circle me-2"></i>Informations Générales
                </h6>
              </div>
              <div class="card-body pt-0">
                <div class="row g-3">
                  <div class="col-md-3">
                    <div class="info-item">
                      <small class="text-muted d-block">Référence</small>
                      <div id="modal_ref" class="fw-bold text-dark fs-6"></div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="info-item">
                      <small class="text-muted d-block">Mode de Transit</small>
                      <div id="modal_mode_transit" class="fw-semibold"></div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="info-item">
                      <small class="text-muted d-block">Pays Expédition</small>
                      <div id="modal_pays_expedition"></div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="info-item">
                      <small class="text-muted d-block">Devise</small>
                      <div id="modal_devise" class="badge bg-light text-dark"></div>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="info-item">
                      <small class="text-muted d-block">Montant Total</small>
                      <div id="modal_montant" class="fw-bold text-success fs-5"></div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="info-item">
                      <small class="text-muted d-block">Mode de Retrait</small>
                      <div id="modal_mode_retrait"></div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="info-item">
                      <small class="text-muted d-block">Statut</small>
                      <div id="modal_statut"></div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="info-item">
                      <small class="text-muted d-block">Date de création</small>
                      <div id="modal_date"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Section Informations Expéditeur -->
            <div class="card border-0 m-4 mb-3 shadow-sm">
              <div class="card-header bg-transparent border-bottom-0 py-3">
                <h6 class="fw-bold text-primary mb-0 d-flex align-items-center">
                  <i class="fas fa-user me-2"></i>Informations Expéditeur
                </h6>
              </div>
              <div class="card-body pt-0">
                <div class="row g-3">
                  <div class="col-md-4">
                    <div class="info-item">
                      <small class="text-muted d-block">Nom Complet</small>
                      <div id="modal_expediteur_nom_complet" class="fw-semibold"></div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="info-item">
                      <small class="text-muted d-block">Email</small>
                      <div id="modal_expediteur_email"></div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="info-item">
                      <small class="text-muted d-block">Téléphone</small>
                      <div id="modal_expediteur_tel"></div>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="info-item">
                      <small class="text-muted d-block">Adresse</small>
                      <div id="modal_expediteur_adresse" class="border rounded p-2 bg-light"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Section Agences -->
            <div class="card border-0 m-4 mb-3 shadow-sm">
              <div class="card-header bg-transparent border-bottom-0 py-3">
                <h6 class="fw-bold text-primary mb-0 d-flex align-items-center">
                  <i class="fas fa-building me-2"></i>Agences
                </h6>
              </div>
              <div class="card-body pt-0">
                <div class="row g-3">
                  <div class="col-md-6">
                    <div class="info-item">
                      <small class="text-muted d-block">Agence Expédition</small>
                      <div id="modal_agence_expedition" class="p-2 border rounded bg-light"></div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-item">
                      <small class="text-muted d-block">Agence Destination</small>
                      <div id="modal_agence_destination" class="p-2 border rounded bg-light"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Section Items Groupés par Service -->
            <div class="card border-0 m-4 shadow-sm">
              <div class="card-header bg-transparent border-bottom-0 py-3">
                <h6 class="fw-bold text-primary mb-0 d-flex align-items-center">
                  <i class="fas fa-boxes me-2"></i>Colis Groupés par Service
                </h6>
              </div>
              <div class="card-body pt-0">
                <div id="modal_services_list"></div>
              </div>
            </div>

          </div> <!-- modalBodyContent -->
        </div>
  
        <div class="modal-footer bg-light rounded-bottom py-3">
          <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i>Fermer
          </button>
          <a href="#" id="modal_invoice_btn" class="btn btn-primary px-4">
            <i class="fas fa-file-invoice me-2"></i> Générer facture
          </a>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('js')
<script>
$(document).ready(function () {
    var table = $("#devisConfirmeTable").DataTable({
        responsive: true,
        language: {
            url: "{{ asset('js/fr-FR.json') }}"
        },
        ajax: {
          url: '{{ route("colis.get.devis.confirmes") }}',
            dataSrc: 'data'
        },
        columns: [
            { 
                data: 'reference_colis',
                render: function(data) {
                    return '<span class="fw-bold text-primary">' + data + '</span>';
                }
            },
            { data: 'nombre_de_colis', className: 'text-center fw-semibold' },
            { render: function (data, type, row) { return row.expediteur_nom + ' ' + row.expediteur_prenom; }},
            { data: 'expediteur_tel', className:'text-muted' },
            { data: 'destinataire_agence', className:'fw-semibold' },
            { 
                data: 'etat',
                render: function(data) {
                    let badgeClass = "bg-success";
                    return '<span class="badge rounded-pill px-3 py-2 '+badgeClass+'">' + data + '</span>';
                }
            },
            { 
                data: 'created_at',
                render: function(data) {
                    if (data) {
                        var date = new Date(data);
                        return date.toLocaleDateString('fr-FR');
                    }
                    return '';
                }
            },
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    const invoiceUrl = "{{ route('aftlb_colis.valide.edit.invoice', ['id' => ':id']) }}".replace(':id', row.id);
                    
                    return `
                        <a href="#" data-devis-id="${row.id}" class="btn btn-sm btn-info btn-action me-1 view-devis-details" title="Voir les détails">
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

    $('#devisConfirmeTable tbody').on('click', '.view-devis-details', function(e) {
        e.preventDefault();
        
        var devisId = $(this).data('devis-id');
        var detailsUrl = "{{ route('colis.get.devis.details', ['id' => ':id']) }}".replace(':id', devisId);
        
        // Mettre à jour le titre de la modale et afficher le loader
        $('#modalReference').text('');
        $('#modalBodyContent').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-3 text-muted">Chargement des détails du devis...</p>
            </div>
        `);
        
        $('#devisModal').modal('show');
        
        $.ajax({
            url: detailsUrl,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    renderModalContent(response.data);
                } else {
                    $('#modalBodyContent').html(`<div class="alert alert-danger text-center">${response.message || 'Erreur lors du chargement des détails'}</div>`);
                }
            },
            error: function(xhr, status, error) {
                $('#modalBodyContent').html(`<div class="alert alert-danger text-center">Erreur de communication avec le serveur: ${error}</div>`);
            }
        });
    });

    // FONCTION POUR FORMATER LES MONTANTS (supprime les décimales)
    function formatMontant(montant) {
        if (!montant) return '0';
        // Convertir en nombre et supprimer les décimales
        const montantNum = parseFloat(montant);
        if (isNaN(montantNum)) return montant;
        
        // Arrondir à l'entier le plus proche
        return Math.round(montantNum).toString();
    }

    function renderModalContent(data) {
        try {
            // Mettre à jour le titre de la modale
            $('#modalReference').text(data.reference || '—');

            // --- Construction de la section des colis ---
            let servicesHtml = '';
            if (Array.isArray(data.items) && data.items.length) {
                var services = {};
                data.items.forEach(item => {
                    var service = item.service || 'Sans service';
                    if (!services[service]) services[service] = [];
                    services[service].push(item);
                });

                Object.keys(services).forEach(service => {
                    var serviceItems = services[service];
                    var totalColisService = serviceItems.reduce((sum, item) => sum + (parseInt(item.quantite_colis) || 1), 0);
                    
                    servicesHtml += `
                        <div class="card mb-3 border-light shadow-sm">
                            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-primary">${escapeHtml(service)}</h6>
                                <span class="badge bg-primary">${totalColisService} colis</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Description</th>
                                            <th>Type</th>
                                            <th class="text-center">Qté</th>
                                            <th class="text-center">Poids (kg)</th>
                                            <th class="text-end">Valeur</th>
                                            <th>Dimensions</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;
                    serviceItems.forEach(item => {
                        var dimensions = (item.longueur && item.largeur && item.hauteur) ? `${item.longueur}×${item.largeur}×${item.hauteur} cm` : '—';
                        // Formater la valeur du colis sans décimales
                        var valeurColis = formatMontant(item.valeur_colis);
                        servicesHtml += `
                            <tr>
                                <td>${escapeHtml(item.description_colis || '—')}</td>
                                <td><span class="badge bg-secondary">${escapeHtml(item.type_colis || '—')}</span></td>
                                <td class="text-center fw-bold">${escapeHtml(item.quantite_colis || '1')}</td>
                                <td class="text-center">${escapeHtml(item.poids || '—')}</td>
                                <td class="text-end fw-semibold">${escapeHtml(valeurColis)} ${escapeHtml(data.devise || '')}</td>
                                <td><small class="text-muted">${escapeHtml(dimensions)}</small></td>
                            </tr>`;
                    });
                    servicesHtml += `</tbody></table></div></div>`;
                });
            } else {
                servicesHtml = '<div class="alert alert-light text-center">Aucun colis trouvé pour ce devis.</div>';
            }

            // Formater le montant total sans décimales
            const montantTotal = formatMontant(data.montant);

            // --- Construction de la structure HTML complète pour le corps de la modale ---
            const modalBodyHtml = `
                <!-- Section Informations Générales -->
                <div class="card border-0 m-4 mb-3 shadow-sm">
                  <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="fw-bold text-primary mb-0 d-flex align-items-center">
                      <i class="fas fa-info-circle me-2"></i>Informations Générales
                    </h6>
                  </div>
                  <div class="card-body pt-0">
                    <div class="row g-3">
                      <div class="col-md-3">
                        <div class="info-item">
                          <small class="text-muted d-block">Référence</small>
                          <div class="fw-bold text-dark fs-6">${escapeHtml(data.reference)}</div>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="info-item">
                          <small class="text-muted d-block">Mode de Transit</small>
                          <div>${escapeHtml(data.mode_transit)}</div>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="info-item">
                          <small class="text-muted d-block">Pays Expédition</small>
                          <div>${escapeHtml(data.pays_expedition)}</div>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="info-item">
                          <small class="text-muted d-block">Montant Total</small>
                          <div class="fw-bold text-success fs-5">${escapeHtml(montantTotal)} ${escapeHtml(data.devise)}</div>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="info-item">
                          <small class="text-muted d-block">Mode de Retrait</small>
                          <div>${escapeHtml(data.mode_de_retrait)}</div>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="info-item">
                          <small class="text-muted d-block">Statut</small>
                          <div><span class="badge bg-success">${escapeHtml(data.etat)}</span></div>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="info-item">
                          <small class="text-muted d-block">Date création</small>
                          <div>${new Date(data.created_at).toLocaleString('fr-FR')}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Section Informations Expéditeur -->
                <div class="card border-0 m-4 mb-3 shadow-sm">
                  <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="fw-bold text-primary mb-0 d-flex align-items-center">
                      <i class="fas fa-user me-2"></i>Informations Expéditeur
                    </h6>
                  </div>
                  <div class="card-body pt-0">
                    <div class="row g-3">
                      <div class="col-md-4">
                        <div class="info-item">
                          <small class="text-muted d-block">Nom Complet</small>
                          <div class="fw-semibold">${escapeHtml(data.nom_expediteur)} ${escapeHtml(data.prenom_expediteur)}</div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="info-item">
                          <small class="text-muted d-block">Email</small>
                          <div>${escapeHtml(data.email_expediteur)}</div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="info-item">
                          <small class="text-muted d-block">Téléphone</small>
                          <div>${escapeHtml(data.tel_expediteur)}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Section Agences -->
                <div class="card border-0 m-4 mb-3 shadow-sm">
                  <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="fw-bold text-primary mb-0 d-flex align-items-center">
                      <i class="fas fa-building me-2"></i>Agences
                    </h6>
                  </div>
                  <div class="card-body pt-0">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <div class="info-item">
                          <small class="text-muted d-block">Agence Expédition</small>
                          <div class="p-2 border rounded bg-light">${escapeHtml(data.agence_expedition)}</div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="info-item">
                          <small class="text-muted d-block">Agence Destination</small>
                          <div class="p-2 border rounded bg-light">${escapeHtml(data.agence_destination)}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Section Colis Groupés par Service -->
                <div class="card border-0 m-4 shadow-sm">
                  <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="fw-bold text-primary mb-0 d-flex align-items-center">
                      <i class="fas fa-boxes me-2"></i>Colis Groupés par Service
                    </h6>
                  </div>
                  <div class="card-body pt-0">
                    ${servicesHtml}
                  </div>
                </div>
            `;
            
            // Remplacer le loader par le contenu final
            $('#modalBodyContent').html(modalBodyHtml);

            // Mettre à jour le bouton de facture
            var invoiceUrl = "{{ route('colis.valide.edit.invoice', ['id' => ':id']) }}".replace(':id', data.id || ''); {{-- MODIFICATION --}}
            $('#modal_invoice_btn').attr('href', invoiceUrl);

        } catch (error) {
            $('#modalBodyContent').html(`<div class="alert alert-danger text-center">Erreur lors de l'affichage des données: ${error.message}</div>`);
        }
    }

    function escapeHtml(text) {
        if (text === null || typeof text === 'undefined') return '—';
        return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }
});
</script>

<style>
    .btn {
        width: auto;
        height: 30px;
        font-size: 16px;
        padding: 0 15px;
        border-radius: 5px;
        transition: background-color 0.3s, transform 0.2s;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
        
    }

    .btn-success:hover {
        background-color: #218838;
        transform: scale(1.05);
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #545b62;
    }

    .badge {
        font-size: 0.85em;
        padding: 0.4em 0.6em;
    }

    .dataTable-wrapper {
        width: 100% !important;
        margin: 20px auto;
        padding: 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background: #f9f9f9;
    }

    .dt-button {
        padding: 10px 20px;
        margin: 5px;
        border: 1px solid transparent;
        border-radius: 5px;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
        text-transform: uppercase;
        transition: all 0.3s ease;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: bold;
        text-align: center;
        vertical-align: middle;
        font-size: 12px
    }

    .table td {
        text-align: center;
        vertical-align: middle;
        font-size: 12px
    }

    .action-buttons-container {
        display: flex;
        justify-content: center;
        gap: 5px;
    }
</style>
@endsection