@extends('chauffeur.layouts.index')

@section('title', 'Programme des Colis')

{{-- Ajout d'une section pour les styles CSS personnalisés --}}
@push('styles')
<style>
    /* Amélioration du style général de la carte */
    .card-programme {
        border-radius: 0.8rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        border: none;
    }

    /* Personnalisation de l'en-tête de la carte avec votre couleur */
    .card-programme .card-header {
        background-color: #f39c12; /* Votre couleur orange */
        color: white;
        border-top-left-radius: 0.8rem;
        border-top-right-radius: 0.8rem;
    }
    
    .card-programme .card-title {
        font-weight: 600;
    }

    /* Style pour la barre de recherche */
    #search-box {
        border-radius: 20px 0 0 20px;
    }
    #search-box:focus {
        border-color: #f39c12;
        box-shadow: 0 0 0 0.2rem rgba(243, 156, 18, 0.25);
    }
    .input-group-append .btn {
        border-radius: 0 20px 20px 0;
    }

    /* Personnalisation de l'en-tête du tableau */
    #programmes-table thead {
        background-color: #343a40; /* Un gris foncé pour un bon contraste */
        color: white;
    }

    #programmes-table th {
        vertical-align: middle;
    }
    
    #programmes-table td {
        vertical-align: middle;
        padding: 0.9rem 0.75rem;
    }
    
    /* Style pour les selects d'état */
    .etat-rdv-select {
        min-width: 120px;
    }

    /* Style pour la surbrillance de la ligne mise à jour */
    .row-updated {
        animation: highlight 2s ease-out;
    }

    @keyframes highlight {
        0% { background-color: rgba(243, 156, 18, 0.3); }
        100% { background-color: transparent; }
    }
</style>
@endpush

@section('content-header')
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <h1>Programme des Colis</h1>
@endsection

@section('content')
    {{-- Ajout de padding autour du contenu pour l'espacer de la navbar et des bords --}}
    <div class="p-4"> 
        <div class="card card-programme"> {{-- Remplacement par notre classe personnalisée --}}
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-truck mr-2"></i>Liste des enlèvements et livraisons</h3>
                <div class="card-tools">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" id="search-box" class="form-control float-right" placeholder="Rechercher par référence...">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-default"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="programmes-table" class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Référence Colis</th>
                                <th>quantité</th>
                                <th>Nature du Colis</th>
                                <th>Action à faire</th>
                                <th>Expéditeur</th>
                                <th></th>
                                <th>État RDV</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($programmes as $row)
                                <tr id="programme-row-{{ $row->id }}">
                                    <td>{{ $row->date_programme }}</td>
                                    <td><strong>{{ $row->reference_colis }}</strong></td>
                                    <td>{{ $row->nature_du_colis }}</td>
                                    <td>
                                        @if($row->actions_a_faire == 'depot')
                                            <span class="badge badge-success">Dépôt</span>
                                        @else
                                            <span class="badge badge-info">Récupération</span>
                                        @endif
                                    </td>
                                    <td>{{ $row->nom_expediteur }}</td>
                                    <td>{{ $row->nom_destinataire }}</td>
                                    <td>{{ $row->lieu_destinataire }}</td>
                                    <td>
                                        <select class="form-control form-control-sm etat-rdv-select" 
                                                data-programme-id="{{ $row['id'] }}" 
                                                {{ $row->etat_rdv == 'effectué' ? 'disabled' : '' }} 
                                                title="{{ $row->etat_rdv == 'effectué' ? 'RDV terminé' : '' }}">
                                            <option value="en cours" {{ $row->etat_rdv == 'en cours' ? 'selected' : '' }}>En cours</option>
                                            <option value="effectué" {{ $row->etat_rdv == 'effectué' ? 'selected' : '' }}>Effectué</option>
                                            <option value="à replanifié" {{ $row->etat_rdv == 'à replanifié' ? 'selected' : '' }}>À replanifié</option>
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" 
                                                class="btn {{ $row->etat_rdv == 'effectué' ? 'btn-secondary' : ($row['reste_a_payer'] ? 'btn-warning' : 'btn-primary') }} btn-sm btn-valider-rdv" 
                                                data-programme-id="{{ $row->id }}" 
                                                data-reference-colis="{{ $row->reference_colis }}"
                                                {{ $row->etat_rdv == 'effectué' ? 'disabled' : '' }}>
                                            @if($row->reste_a_payer)
                                                <i class="fas fa-euro-sign"></i> Encaisser
                                            @else
                                                <i class="fas fa-check"></i> Valider
                                            @endif
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center p-4">Aucun programme disponible pour le moment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <script src="https://cdn.cinetpay.com/seamless/main.js"></script>

   <script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('#programmes-table').DataTable({
            "paging": true, "lengthChange": false, "searching": true, "ordering": true, "info": true, "autoWidth": false, "responsive": true,
            "language": { "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json" },
            "dom": 'lrtip'
        });
        $('#search-box').on('keyup', function(){ table.search(this.value).draw(); });

        $('.btn-valider-rdv').on('click', function(e) {
            e.preventDefault();
            var programmeId = $(this).data('programme-id');
            var referenceColis = $(this).data('reference-colis');
            
            $.ajax({
                type: 'GET',
                url: `/chauffeur/payment-details/${referenceColis}`,
                success: function(paymentData) {
                    if (paymentData.reste_a_payer <= 0) {
                        // Si aucun paiement n'est dû, on met simplement à jour l'état du RDV
                        updateRdvStatus(programmeId, 'effectué');
                    } else {
                        // S'il reste un paiement, on affiche la popup d'encaissement
                        showPaymentPopup(programmeId, referenceColis, paymentData);
                    }
                },
                error: function(error) {
                    Swal.fire('Erreur', 'Impossible de récupérer les informations de paiement.', 'error');
                }
            });
        });

        function showPaymentPopup(programmeId, referenceColis, paymentData) {
            const montantTotal = parseFloat(paymentData.montant_total);
            const montantPaye = parseFloat(paymentData.montant_paye);
            const resteAPayer = parseFloat(paymentData.reste_a_payer);

            Swal.fire({
                title: 'Règlement du Colis',
                html: `
                    <div style="text-align: left; padding: 1rem;">
                        <p><strong>Référence :</strong> ${referenceColis}</p>
                        <hr>
                        <p>Montant Total : <strong style="float: right;">${montantTotal.toFixed(2)} €</strong></p>
                        <p>Déjà Réglé : <strong style="float: right;">${montantPaye.toFixed(2)} €</strong></p>
                        <p class="text-danger h5">Reste à Payer : <strong style="float: right;">${resteAPayer.toFixed(2)} €</strong></p>
                        <hr>
                        <div class="form-group">
                            <label for="montant_encaisse">Montant Encaissé (Espèces)</label>
                            <input type="number" id="montant_encaisse" class="swal2-input" value="${resteAPayer.toFixed(2)}" step="0.01">
                        </div>
                        <hr>
                        <p class="text-center">OU</p>
                        <button id="payWithCinetpay" class="btn btn-warning btn-block">Payer ${resteAPayer.toFixed(2)} € par Mobile Money</button>
                    </div>
                `,
                confirmButtonText: 'Encaisser (Espèces)',
                showCancelButton: true,
                cancelButtonText: 'Annuler',
                didOpen: () => {
                    $('#payWithCinetpay').on('click', function() {
                        checkoutCinetpay(paymentData, referenceColis, programmeId);
                    });
                },
                preConfirm: () => {
                    return {
                        montant_encaisse: document.getElementById('montant_encaisse').value,
                        methode_paiement: 'especes'
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const paymentFormData = result.value;
                    paymentFormData.reference_colis = referenceColis;
                    processPayment(paymentFormData, programmeId);
                }
            });
        }
        
        function checkoutCinetpay(paymentData, referenceColis, programmeId) {
            CinetPay.setConfig({
                apikey: paymentData.cinetpay_apikey,
                site_id: paymentData.cinetpay_site_id,
                notify_url: paymentData.notify_url,
                mode: 'PRODUCTION'
            });
            CinetPay.getCheckout({
                transaction_id: Math.floor(Math.random() * 100000000).toString(),
                amount: paymentData.reste_a_payer,
                currency: 'XOF',
                channels: 'ALL',
                description: `Paiement du colis ${referenceColis}`,
            });
            CinetPay.waitResponse(function(data) {
                if (data.status == "ACCEPTED") {
                    const paymentFormData = {
                        montant_encaisse: paymentData.reste_a_payer,
                        methode_paiement: 'mobile_money',
                        reference_colis: referenceColis,
                    };
                    processPayment(paymentFormData, programmeId);
                } else {
                    Swal.fire("Échec", "Le paiement Mobile Money a échoué.", "error");
                }
            });
        }

        function processPayment(paymentFormData, programmeId) {
            paymentFormData._token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                type: 'POST',
                url: '{{ route("chauffeur.payment.process") }}',
                data: paymentFormData,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Après un paiement réussi, on met à jour l'état du RDV à "effectué"
                        updateRdvStatus(programmeId, 'effectué', 'Paiement et RDV mis à jour !');
                    } else {
                        Swal.fire('Erreur', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Un problème est survenu lors de la communication avec le serveur.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    console.error('Détails de l\'erreur de paiement:', xhr.responseText);
                    Swal.fire('Erreur', errorMessage, 'error');
                }
            });
        }

        function updateRdvStatus(programmeId, etatRdv, successMessage = 'État du RDV mis à jour !') {
            $.ajax({
                type: 'POST',
                url: `/chauffeur/programme/${programmeId}/update-etat-rdv`,
                data: { 
                    etat_rdv: etatRdv,
                    _method: 'PATCH'
                },
                success: function(data) {
                    const row = $(`#programme-row-${programmeId}`);
                    const selectElement = row.find('.etat-rdv-select');
                    const buttonElement = row.find('.btn-valider-rdv');
                    
                    selectElement.val(etatRdv);
                    if (etatRdv === 'effectué') {
                        selectElement.prop('disabled', true).attr('title', 'RDV terminé');
                        buttonElement.prop('disabled', true).removeClass('btn-primary btn-warning').addClass('btn-secondary').html('<i class="fas fa-check"></i> Validé');
                    }
                    
                    // AMÉLIORATION : Ajout d'un effet visuel sur la ligne mise à jour
                    row.addClass('row-updated');
                    setTimeout(() => {
                        row.removeClass('row-updated');
                    }, 2000);
                    
                    Swal.fire({
                        icon: 'success', 
                        title: 'Succès !', 
                        text: successMessage,
                        toast: true, // Affichage plus discret
                        position: 'top-end', // En haut à droite
                        showConfirmButton: false, 
                        timer: 3000,
                        timerProgressBar: true
                    });
                },
                error: function(error) {
                    console.error('Détails de l\'erreur:', error.responseText);
                    Swal.fire('Erreur', 'La mise à jour du statut du RDV a échoué.', 'error');
                }
            });
        }
    });
   </script>
@endpush