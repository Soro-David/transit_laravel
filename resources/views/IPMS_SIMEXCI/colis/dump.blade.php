@extends('IPMS_SIMEXCI.layouts.agent')

@section('content-header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection

@section('content')
<section class="py-3">
    {{-- Ajoutez ce bouton dans votre section content --}}

    <div class="row">
        <div class="col-md-12">
            <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                <h4 class="text-left mt-4">Liste des colis Arrivés</h4><br>
                <div class="row mb-3">
                    <div class="col-md-12 text-end">
                        <a href="{{ route('ipms_colis.download.pdf') }}" class="btn btn-success">
                            <i class="fas fa-download me-2"></i>Télécharger PDF
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="productTable" class="table table-bordered table-striped display" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center">St. Paiement</th>
                                <th>Référence</th>
                                <th>Nom Produit</th>
                                <th class="text-center">Nb. Colis</th>
                                <th>Montant Total</th>
                                <th>Montant Payé</th>
                                <th>Reste à Payer</th>
                                <th>Expéditeur</th>
                                <th>Tél. Exp</th>
                                <th>Destinataire</th>
                                <th>Tél. Dest.</th>
                                <th>Status Colis</th>
                                <th>Date</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODALE DE PAIEMENT --}}
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Enregistrer un Paiement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="paymentForm">
                    @csrf 
                    <div class="modal-body">
                        <input type="hidden" id="modalColisId" name="colis_id">
                        <input type="hidden" id="modalColisIds" name="colis_ids">
                        <div class="mb-3">
                            <label class="form-label">Référence Colis</label>
                            <input type="text" id="modalDisplayReference" class="form-control" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Montant Total Dû</label>
                            <input type="text" id="modalTotalAmount" class="form-control" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Montant Déjà Payé</label>
                            <input type="text" id="modalAmountAlreadyPaid" class="form-control" readonly style="color:green;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Montant Restant à Payer</label>
                            <div id="modalRemainingAmountDisplay" class="form-control" style="color:#dc3545;background-color:#f8f9fa;font-weight:bold;"></div>
                        </div>

                        <div id="euroPaymentFields" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label">Montant Nouveau Paiement (EUR)</label>
                                <input type="number" step="0.01" id="modalNewPaymentAmountEur" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Équivalent FCFA</label>
                                <input type="text" id="modalConvertedAmountCfa" class="form-control" readonly>
                            </div>
                        </div>

                        <div id="fcfaPaymentFields" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label">Montant Nouveau Paiement (FCFA)</label>
                                <input type="number" step="1" id="modalNewPaymentAmountCfa" class="form-control">
                            </div>
                        </div>

                        <input type="hidden" id="modalNewPaymentAmount" name="montant_a_payer">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary" id="submitPaymentBtn">Enregistrer Paiement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
    #productTable th, #productTable td { vertical-align: middle; }
    .action-buttons-container { display: flex; justify-content:center; gap:5px; }
    .is-invalid { border-color: #dc3545; }
    .invalid-feedback { color:#dc3545; font-size:.875em; display:none; }
    .is-invalid ~ .invalid-feedback { display:block; }
</style>

<script>
$(document).ready(function () {
    const EUR_TO_FCFA_RATE = parseFloat("{{ App\Services\CurrencyConverterService::FCFA_TO_EUR_RATE }}") || 655.957;
    function formatCfa(val){return Math.round(val).toLocaleString('fr-FR')+' FCFA';}
    function formatEur(val){return Number(val).toFixed(2).replace('.',',')+' €';}

    $.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});

    var table = $("#productTable").DataTable({
        processing:true,
        serverSide:true,
        responsive:true,
        scrollX: true,
        language:{url:"{{ asset('js/fr-FR.json') }}"},
        ajax:'{{ route("ipms_colis.get.colis.dump") }}',
        columns:[
            { data:'statut_paiement', className:'text-center', orderable:false, searchable:false },
            { data:'reference_colis' },
            { data:'nom_produit' },
            { data:'nombre_de_colis', className:'text-center' },
            { data:'montant_total', render: function(d){return formatCfa(d);} },
            { data:'montant_paye', render: function(d){return formatCfa(d);} },
            { data:'reste_a_payer', render: function(d){return formatCfa(d);} },
            { data: null, render: (d, t, r) => (r.expediteur_nom || '') + ' ' + (r.expediteur_prenom || '') },
            { data:'expediteur_tel' },
            { data: null, render: (d, t, r) => (r.destinataire_nom || '') + ' ' + (r.destinataire_prenom || '') },
            { data:'destinataire_tel' },
            { data:'etat' },
            { data:'created_at' },
            { data:'action', orderable:false, searchable:false, className:'text-center' }
        ],
        dom:'Bfrtip',
        buttons:[
            'excel',
                
            {
                extend: 'print',
                title: 'MANIFESTE DES COLIS ARRIVÉS',
                exportOptions: {
                    columns: ':not(:first-child):not(:last-child)'
                }
            }
        ],
        order:[[1,'desc']]
    });

    // Gestion paiement
    $('#productTable tbody').on('click','.pay-btn',function(){
        var button=$(this);
        var creatorAgenceId=parseInt(button.data('creator-agence-id'))||0;
        var reference=button.data('reference');
        var colisId=button.data('colis-id');
        var colisIds=button.data('colis-ids');

        $('#modalDisplayReference').val(reference);
        $('#modalColisId').val(colisId);
        $('#modalColisIds').val(JSON.stringify(colisIds));

        var fcfaInput=$('#modalNewPaymentAmountCfa');
        var eurInput=$('#modalNewPaymentAmountEur');
        let total=parseFloat(button.data('total'))||0;
        let paid=parseFloat(button.data('paid'))||0;
        let remaining=total-paid;

        if(remaining<=0){ Swal.fire('Info','Colis déjà payé','info'); return;}

        if(creatorAgenceId===7){
            $('#euroPaymentFields').hide(); eurInput.prop('required',false);
            $('#fcfaPaymentFields').show(); fcfaInput.prop('required',true);
            $('#modalTotalAmount').val(formatCfa(total));
            $('#modalAmountAlreadyPaid').val(formatCfa(paid));
            $('#modalRemainingAmountDisplay').html(`<strong style="color:#dc3545;">${formatCfa(remaining)}</strong>`);
            fcfaInput.val(Math.round(remaining)).trigger('input');
        }else{
            $('#fcfaPaymentFields').hide(); fcfaInput.prop('required',false);
            $('#euroPaymentFields').show(); eurInput.prop('required',true);
            let totalCfa=total*EUR_TO_FCFA_RATE;
            let paidCfa=paid*EUR_TO_FCFA_RATE;
            let remainingCfa=remaining*EUR_TO_FCFA_RATE;
            $('#modalTotalAmount').val(`${formatEur(total)} soit ${formatCfa(totalCfa)}`);
            $('#modalAmountAlreadyPaid').val(`${formatEur(paid)} soit ${formatCfa(paidCfa)}`);
            $('#modalRemainingAmountDisplay').html(`<strong style="color:#dc3545;">${formatEur(remaining)}</strong> soit ${formatCfa(remainingCfa)}`);
            eurInput.val(remaining.toFixed(2)).trigger('input');
        }
        $('#paymentModal').modal('show');
    });

    $('#modalNewPaymentAmountEur').on('input',function(){
        let amountEur=parseFloat($(this).val())||0;
        let amountCfa=amountEur*EUR_TO_FCFA_RATE;
        $('#modalConvertedAmountCfa').val(formatCfa(amountCfa));
        $('#modalNewPaymentAmount').val(Math.round(amountCfa));
    });

    $('#modalNewPaymentAmountCfa').on('input',function(){
        let amountCfa=parseFloat($(this).val())||0;
        $('#modalNewPaymentAmount').val(Math.round(amountCfa));
    });

    $('#paymentForm').on('submit',function(e){
        e.preventDefault();
        var submitButton=$('#submitPaymentBtn');
        var originalText=submitButton.html();
        submitButton.prop('disabled',true).html('<span class="spinner-border spinner-border-sm"></span> Enregistrement...');
        $.ajax({
            url:'{{ route("ipms_colis.valide.payer") }}',
            type:'POST',
            data:$(this).serialize(),
            dataType:'json',
            success:function(resp){
                $('#paymentModal').modal('hide');
                Swal.fire({icon:'success',title:'Succès!',text:resp.success,timer:2500,showConfirmButton:false});
                table.ajax.reload(null,false);
            },
            error:function(xhr){
                let msg='Erreur lors de l\'enregistrement.';
                if(xhr.responseJSON && xhr.responseJSON.error){ msg=xhr.responseJSON.error; }
                Swal.fire({icon:'error',title:'Erreur!',text:msg});
            },
            complete:function(){ submitButton.prop('disabled',false).html(originalText);}
        });
    });
});
</script>
@endsection
