@extends('AGENCE_CHINE.layouts.agent')

@section('content-header')
{{-- CSRF Token pour les requêtes AJAX --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
{{-- Font Awesome si pas inclus globalement --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> {{-- Using FA 6 for potentially newer icons --}}
@endsection

@section('content')
<section class="py-3">
    {{-- Pas besoin de <form> ici si DataTables gère tout --}}
    <div class="row">
        <div class="col-md-12">
            <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                <h4 class="text-left mt-4">Liste des colis Validés</h4><br>
                <div id="products-container">
                    <div class="table-responsive">
                        <table id="productTable" class="table table-bordered table-striped display" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center">St. Paiement</th>
                                    <th>Référence</th>
                                    <th class="text-center">Nb. Colis</th>
                                    <th>Expéditeur</th>
                                    <th>Tél. Exp</th>
                                    {{-- <th>Agence Expéditeur</th> --}}
                                    <th>Destinataire</th>
                                    <th>Tél. Dest.</th>
                                    <th>Agence Dest.</th>
                                    <th>Status Colis</th>
                                    <th>Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Le contenu sera chargé par DataTables --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== MODALE DE PAIEMENT ===== --}}
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Enregistrer un Paiement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="paymentForm">
                    @csrf 
                    <div class="modal-body">
                        <input type="hidden" id="modalReferenceColis" name="reference_colis">
                        <input type="hidden" id="modalColisIds" name="colis_ids"> {{-- Pour passer les IDs (chaîne JSON) --}}

                        <div class="mb-3">
                            <label for="modalDisplayReference" class="form-label">Référence Colis</label>
                            <input type="text" class="form-control" id="modalDisplayReference" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="modalTotalAmount" class="form-label">Montant Total Dû</label>
                            <input type="text" class="form-control" id="modalTotalAmount" readonly style="font-weight: bold;">
                        </div>

                         <div class="mb-3">
                            <label for="modalAmountAlreadyPaid" class="form-label">Montant Déjà Payé</label>
                            <input type="text" class="form-control" id="modalAmountAlreadyPaid" readonly style="color: green;">
                        </div>

                        <div class="mb-3">
                            <label for="modalNewPaymentAmount" class="form-label">Montant du Nouveau Paiement <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="modalNewPaymentAmount" name="montant_a_payer" required placeholder="0.00">
                             {{-- Pour afficher les erreurs de validation du backend --}}
                            <div class="invalid-feedback" id="paymentAmountError"></div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary" id="submitPaymentBtn">Enregistrer Paiement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- ===== FIN MODALE DE PAIEMENT ===== --}}

</section>

{{-- Styles (Peuvent être déplacés dans un fichier CSS) --}}
<style>
    /* Styles généraux pour la table et les boutons */
    #productTable {
        width: 100% !important;
        /* white-space: nowrap;  Optionnel: Décommenter si le non-retour à la ligne est préféré */
    }

    #productTable th,
    #productTable td {
        vertical-align: middle; /* Alignement vertical au centre */
        /* padding: 8px 10px; */ /* Ajuster le padding si nécessaire */
    }

    /* Centrer le texte dans des colonnes spécifiques si nécessaire */
    #productTable .text-center {
        text-align: center;
    }

    /* Conteneur pour les boutons d'action pour utiliser Flexbox */
    .action-buttons-container {
        display: flex;
        justify-content: center; /* Centre les boutons horizontalement */
        align-items: center;    /* Centre les boutons verticalement */
        gap: 5px;              /* Espace entre les boutons */
        flex-wrap: nowrap;     /* Empêche le retour à la ligne des boutons */
    }

    /* Ajustements pour DataTables (optionnel) */
    .dataTables_wrapper {
         /* width: 100%; */
         /* margin: 0 auto; */
    }
    .dt-buttons {
        margin-bottom: 15px;
    }

    /* Styles responsives */
    @media (max-width: 768px) {
        #productTable {
             white-space: normal; /* Permettre le retour à la ligne sur petits écrans */
        }
        .dt-buttons {
            text-align: center;
        }
        .dt-button {
            display: block;
            margin: 5px auto;
            width: 80%;
        }
        .action-buttons-container {
            flex-wrap: wrap; /* Permettre aux boutons de passer à la ligne si nécessaire */
            justify-content: center;
        }
    }

    /* Style pour le message d'erreur de validation dans la modale */
     .is-invalid {
        border-color: #dc3545; /* Couleur de bordure Bootstrap pour l'erreur */
    }
    .invalid-feedback {
        display: none; /* Caché par défaut */
        width: 100%;
        margin-top: .25rem;
        font-size: .875em;
        color: #dc3545; /* Couleur du texte d'erreur Bootstrap */
    }
    .is-invalid ~ .invalid-feedback {
        display: block; /* Affiché quand le champ est invalide */
    }

</style>

{{-- Script pour DataTables et les interactions --}}
<script>
$(document).ready(function () {
    // Configuration du header CSRF pour toutes les requêtes AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialisation de DataTables
    var table = $("#productTable").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        language: { url: "{{ asset('js/fr-FR.json') }}" }, // Assurez-vous que ce fichier existe
        ajax: '{{ route("chine_colis.get.colis.valide") }}', // Route vers la méthode du contrôleur
        columns: [
            // La colonne 'statut_paiement' est générée côté serveur avec HTML
            { data: 'statut_paiement', name: 'statut_paiement', orderable: false, searchable: false, className: 'text-center' },
            { data: 'reference_colis', name: 'reference_colis' },
            // La colonne 'nombre_de_colis' est calculée côté serveur
            { data: 'nombre_de_colis', name: 'nombre_de_colis', className: 'text-center' },
            {
                data: null, name: 'expediteur_nom', // Utiliser un nom existant pour le tri/recherche serveur
                render: function (data, type, row) { return (row.expediteur_nom || '') + ' ' + (row.expediteur_prenom || ''); },
                searchable: true, orderable: true // Permettre recherche/tri sur le nom complet
            },
            { data: 'expediteur_tel', name: 'expediteurs.tel' }, // Utiliser le nom de table correct pour le tri/recherche serveur si possible
            {
                data: null, name: 'destinataire_nom', // Utiliser un nom existant pour le tri/recherche serveur
                render: function (data, type, row) { return (row.destinataire_nom || '') + ' ' + (row.destinataire_prenom || ''); },
                 searchable: true, orderable: true // Permettre recherche/tri
            },
            { data: 'destinataire_tel', name: 'destinataires.tel' }, // Utiliser le nom de table correct
            { 
                data: 'destinataire_agence',
                name: 'destinataire_agence.nom_agence',
                render: function(data, type, row) {
                    if (data === 'IPMS-SIMEX-CI Angre 8ème Tranche') {
                        return 'DS Translog Angré 8ème Tranche';
                    } else if (data === 'IPMS-SIMEX-CI') {
                        return 'DS Translog Carrefour Angré';
                    }
                    return data;
                }
            },
            { data: 'etat', name: 'colis.etat' }, // Utiliser le nom de table correct
            { data: 'created_at', name: 'colis.created_at' }, // Utiliser le nom de table correct
             // La colonne 'action' est générée côté serveur avec HTML
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        // Configuration des boutons d'exportation (si utilisés)
        dom: 'Bfrtip', // Afficher les boutons, le filtre, la table, les informations et la pagination
        buttons: [
            'excel', 'pdf', 'print' // Boutons standards DataTables
        ],
         order: [[ 1, 'desc' ]] // Trier par référence par défaut (colonne index 1)
    });

    // --- Logique pour la Modale de Paiement ---

    // 1. Ouvrir la modale et pré-remplir les champs quand on clique sur le bouton Payer (.pay-btn)
    $('#productTable tbody').on('click', '.pay-btn', function (e) {
        e.preventDefault(); // Empêcher le comportement par défaut du bouton

        var button = $(this);
        var reference = button.data('reference');
        var total = parseFloat(button.data('total')).toFixed(2);
        var paid = parseFloat(button.data('paid')).toFixed(2);
        var colisIds = JSON.stringify(button.data('colis-ids'));

        console.log("Opening payment modal for reference:", reference);
        console.log("Total:", total, "Paid:", paid, "Colis IDs:", colisIds);

        // Fonction pour formater les nombres en devise (exemple EUR, ajuster si besoin XOF, etc.)
        function formatCurrency(value) {
            // Vérifier si la valeur est un nombre valide
            const num = Number(value);
            if (isNaN(num)) {
                return 'N/A'; // Ou une autre valeur par défaut
            }
            return num.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR', minimumFractionDigits: 2 }); // Ajuster 'EUR'
        }

        // Remplir les champs de la modale
        $('#modalDisplayReference').val(reference);
        $('#modalTotalAmount').val(formatCurrency(total));
        $('#modalAmountAlreadyPaid').val(formatCurrency(paid));
        $('#modalReferenceColis').val(reference);    // Champ caché pour la soumission
        $('#modalColisIds').val(colisIds);           // Champ caché pour la soumission (JSON string)

        // Réinitialiser le champ du nouveau montant et les erreurs
        $('#modalNewPaymentAmount').val('').removeClass('is-invalid');
        $('#paymentAmountError').text('').hide(); // Cacher le message d'erreur

        // Afficher la modale (Bootstrap 5)
        $('#paymentModal').modal('show');
    });

    // 2. Soumettre le formulaire de paiement via AJAX
    $('#paymentForm').on('submit', function(e) {
        e.preventDefault(); // Empêcher la soumission standard du formulaire

        var form = $(this);
        var submitButton = $('#submitPaymentBtn');
        var originalButtonText = submitButton.html();
        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enregistrement...'); // Indicateur de chargement

        // Vider les erreurs précédentes
        $('#modalNewPaymentAmount').removeClass('is-invalid');
        $('#paymentAmountError').text('').hide();

        $.ajax({
            url: '{{ route("chine_colis.valide.payer") }}', // Utiliser la route nommée pour l'enregistrement du paiement
            type: 'POST',
            data: form.serialize(), // Envoyer les données du formulaire (inclut CSRF, reference_colis, colis_ids, montant_a_payer)
            dataType: 'json', // Attendre une réponse JSON
            // console.log("Submitting payment form:", form.serialize()),
            success: function(response) {
                $('#paymentModal').modal('hide'); // Fermer la modale
                Swal.fire({
                    icon: 'success',
                    title: 'Succès!',
                    text: response.success || 'Paiement enregistré avec succès.',
                    timer: 2500, // Fermer automatiquement après 2.5 secondes
                    showConfirmButton: false
                });
                table.ajax.reload(null, false); // Recharger DataTables sans réinitialiser la pagination/recherche
            },
            error: function(xhr, status, error) {
                var errorMessage = 'Une erreur est survenue lors de l\'enregistrement du paiement.';
                // Vérifier si la réponse contient des erreurs JSON
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.error) { // Erreur générale envoyée par le serveur
                        errorMessage = xhr.responseJSON.error;
                    }
                    // Gérer les erreurs de validation spécifiques (422)
                    if (xhr.status === 422 && xhr.responseJSON.details) {
                         if (xhr.responseJSON.details.montant_a_payer) {
                             $('#modalNewPaymentAmount').addClass('is-invalid');
                             $('#paymentAmountError').text(xhr.responseJSON.details.montant_a_payer[0]).show();
                             errorMessage = 'Veuillez corriger les erreurs dans le formulaire.'; // Message plus général pour Swal
                         }
                    }
                } else {
                    // Loguer l'erreur complète pour le débogage si pas de JSON
                    console.error("Erreur AJAX:", status, error, xhr.responseText);
                }

                // Afficher une alerte d'erreur générique ou spécifique
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur!',
                    text: errorMessage
                });
            },
            complete: function () {
                // Réactiver le bouton et restaurer son texte initial, que la requête réussisse ou échoue
                 submitButton.prop('disabled', false).html(originalButtonText);
            }
        });
    });

    // --- Logique pour le bouton Supprimer/Archiver ---

    // Utilisation de la délégation d'événement sur le tbody pour les boutons ajoutés dynamiquement
    $('#productTable tbody').on('click', '.delete-btn', function (e) {
        e.preventDefault(); // Bonne pratique

        const button = $(this);
        const deleteUrl = button.data('url'); // URL de suppression depuis data-url
        const reference = button.data('reference'); // Référence pour le message de confirmation

        if (!deleteUrl) {
            console.error("URL de suppression non trouvée pour le bouton:", button);
            Swal.fire('Erreur', 'Impossible de trouver l\'action de suppression.', 'error');
            return;
        }

        // Confirmation avec SweetAlert
        Swal.fire({
            title: 'Êtes-vous sûr?',
            html: `Voulez-vous vraiment supprimer le(s) colis avec la référence <strong>${reference}</strong> ?<br><small>Cette action est généralement réversible.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33', // Rouge pour la suppression/archivage
            cancelButtonColor: '#3085d6', // Bleu pour annuler
            confirmButtonText: 'Oui, Supprimer!',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                // Si l'utilisateur confirme, envoyer la requête AJAX DELETE
                $.ajax({
                    url: deleteUrl, // L'URL contient déjà la référence ou l'identifiant nécessaire
                    type: 'DELETE', // Utiliser la méthode DELETE
                    // Le token CSRF est déjà configuré globalement via $.ajaxSetup
                    dataType: 'json', // Attendre une réponse JSON
                    success: function (response) {
                        Swal.fire(
                            'Supprimer!',
                            response.success || `Le(s) colis avec la référence ${reference} ont été supprimés.`,
                            'success'
                        );
                        table.ajax.reload(null, false); // Recharger la table sans réinitialiser
                    },
                    error: function (xhr, status, error) {
                        let errorMsg = 'Une erreur est survenue lors de la suppression.';
                        if(xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        } else {
                             console.error("Erreur AJAX Delete:", status, error, xhr.responseText);
                        }
                        Swal.fire(
                            'Erreur!',
                            errorMsg,
                            'error'
                        );
                    }
                });
            }
        });
    });


}); // Fin $(document).ready
</script>

@endsection